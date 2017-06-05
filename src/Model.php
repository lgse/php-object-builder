<?php
namespace PHPOB;

use InvalidArgumentException;

/**
 * Class Model
 * @package PHPOB
 * @author Pierre Bérubé <pierre@lgse.com>
 */
abstract class Model
{
    /**
     * @param array|object $arguments
     * @return mixed
     * @throws InvalidArgumentException
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
        $builder = new ObjectBuilder($objectClass, $arguments);

        return $builder->getObject((array) $arguments);
    }
}