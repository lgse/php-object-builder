<?php
namespace PHPOB;

use InvalidArgumentException;
use ReflectionClass;

/**
 * Class ObjectBuilder
 * @package PHPOB
 * @author Pierre Bérubé <pierre@lgse.com>
 */
class ObjectBuilder
{
    /**
     * @var string
     */
    private $className;
    /**
     * @var array
     */
    private $constructorParameters = [];
    /**
     * @var array
     */
    private static $cache = [];

    /**
     * ObjectBuilder constructor.
     * @param $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
        $this->setConstructorParameters();
    }

    /**
     * @return void
     */
    private function setConstructorParameters()
    {
        if (isset(self::$cache[$this->className])) {
            $this->constructorParameters = self::$cache[$this->className];
        } else {
            $reflectionClass = new ReflectionClass($this->className);
            $constructor = $reflectionClass->getConstructor();

            if (is_null($constructor)) {
                throw new InvalidArgumentException(
                    "{$this->className} Instantiation Error:" . PHP_EOL .
                    "Object constructor missing."
                );
            }

            foreach ($constructor->getParameters() as $parameter) {
                $name = (string) $parameter->getName();
                $parameterType = (string) $parameter->getType();

                /**
                 * Fix PHP Scalar Type inequivalence
                 * (int) $string does not cast to type int, but integer natively
                 * (bool) $string does not cast to type bool, but boolean natively
                 * (float) $string does not cast to type float, but double natively
                 */
                if ($parameterType === 'int') {
                    $parameterType = 'integer';
                } elseif ($parameterType === 'bool') {
                    $parameterType = 'boolean';
                } elseif ($parameterType === 'float') {
                    $parameterType = 'double';
                }

                $details = [
                    'name' => $name,
                    'type' => $parameterType,
                    'class' => !is_null($parameter->getClass()) ? $parameter->getClass()->getName() : null,
                    'nullable' => $parameter->allowsNull(),
                    'optional' => $parameter->isOptional(),
                    'default' => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
                ];

                $this->constructorParameters[$name] = $details;
            }

            self::$cache[$this->className] = $this->constructorParameters;
        }
    }

    /**
     * @param $argument
     * @param $argumentExists
     * @param $parameter
     * @throws InvalidArgumentException
     */
    private function isArgumentValidForParameter($argument, $argumentExists, $parameter)
    {
        $argumentClass = is_object($argument) ? get_class($argument) : null;
        $argumentType = gettype($argument);

        if (
            $argumentExists
            && is_null($argument)
            && !$parameter['nullable']
            && !$parameter['optional']
        ) {
            throw new InvalidArgumentException(
                "{$this->className} Instantiation Error:" . PHP_EOL .
                "Missing parameter `{$parameter['name']}` in supplied arguments."
            );
        } elseif (
            !is_null($argument)
            && is_null($parameter['class'])
            && !empty($parameter['type'])
            && !$parameter['nullable']
            && !$parameter['optional']
            && $parameter['type'] !== $argumentType
        ) {
            throw new InvalidArgumentException(
                "{$this->className} Instantiation Error:" . PHP_EOL .
                "Parameter `{$parameter['name']}` expected to be `{$parameter['type']}` instead got `{$argumentType}`."
            );
        } elseif (
            !is_null($parameter['class'])
            && !is_null($argumentClass)
            && !($argument instanceof $parameter['class'])
            && !is_subclass_of($argument, $parameter['class'])
        ) {
            throw new InvalidArgumentException(
                "{$this->className} Instantiation Error:" . PHP_EOL .
                "Parameter `{$parameter['name']}` expected to be subclass of `{$parameter['class']}` instead got `{$argumentClass}`."
            );
        }
    }

    /**
     * @param $argument
     * @param $parameter
     * @return mixed
     */
    private function buildParameter($argument, $parameter)
    {
        $argumentClass = is_object($argument) ? get_class($argument) : null;
        $parameterClass = $parameter['class'];

        if (is_null($argument) && $parameter['nullable']) {
            return null;
        } elseif (is_null($parameterClass) || !is_null($parameterClass) && !is_null($argumentClass)) {
            return $argument;
        } else {
            $builder = new ObjectBuilder($parameterClass);
            return $builder->getObject($argument);
        }
    }

    /**
     * @param $arguments
     * @return mixed
     */
    public function getObject($arguments)
    {
        $constructorArguments = [];

        if (!count($this->constructorParameters)) {
            return new $this->className();
        }

        $firstParameter = current($this->constructorParameters);

        if (
            (
                is_array($arguments)
                && !array_key_exists($firstParameter['name'], $arguments)
                && $firstParameter['type'] === 'array'
            )
            || count($this->constructorParameters) === 1
        ) {
            $constructorArguments[] = $arguments;
        } elseif (!is_array($arguments) || is_null($arguments)) {
            $firstParameter = current($this->constructorParameters);
            $this->isArgumentValidForParameter($arguments, true, $firstParameter);
            $constructorArguments[] = $this->buildParameter($arguments, $firstParameter);
        } else {
            foreach ($this->constructorParameters as $parameterName => $parameter) {
                if (isset($arguments[$parameterName])) {
                    $argument = $arguments[$parameterName];
                } elseif (!is_null($parameter['default'])) {
                    $argument = $parameter['default'];
                } else {
                    $argument = null;
                }
                $argumentExists = array_key_exists($parameterName, $arguments);
                $this->isArgumentValidForParameter($argument, $argumentExists, $parameter);
                $constructorArguments[] = $this->buildParameter($argument, $parameter);
            }
        }

        return new $this->className(...$constructorArguments);
    }
}
