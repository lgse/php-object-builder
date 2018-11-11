<?php
namespace PHPOB;

use InvalidArgumentException;
use ReflectionException;

/**
 * Class Model
 * @package PHPOB
 * @author Pierre Bérubé <pierre@lgse.com>
 */
abstract class Model
{
    /**
     * @param $arguments
     * @return mixed
     * @throws ReflectionException
     */
    public static function getInstance($arguments)
    {
        $modelClass = get_called_class();

        if (!is_array($arguments) && !is_object($arguments)) {
            throw new InvalidArgumentException(
                "{$modelClass} Instantiation Error: " . PHP_EOL .
                "`getInstance` method expects argument to be an array or an object."
            );
        }

        $objectClass = get_called_class();
        $builder = new ObjectBuilder($objectClass);

        return $builder->getObject((array) $arguments);
    }
}
