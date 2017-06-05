<?php
namespace PHPOB;

use InvalidArgumentException;

/**
 * Class Model
 * @package PHPOB
 */
abstract class Model
{
    /**
     * @param array|object $arguments
     * @return mixed
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

        if (is_object($arguments)) {
            $arguments = (array) $arguments;
        }

        $objectClass = get_called_class();
        $builder = new ObjectBuilder($objectClass, $arguments);

        return $builder->getObject($arguments);
    }
}