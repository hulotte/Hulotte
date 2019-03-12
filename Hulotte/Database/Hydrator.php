<?php

namespace Hulotte\Database;

use Psr\Container\ContainerInterface;

/**
 * Class Hydrator
 *
 * @package Hulotte\Database
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class Hydrator
{
    /**
     * Hydrate the entity
     * @param array $array
     * @param mixed $object
     * @param ContainerInterface $container
     * @return mixed
     */
    public static function hydrate(array $array, $object, ContainerInterface $container)
    {
        if (is_string($object)) {
            $instance = clone $container->get($object);
        } else {
            $instance = $object;
        }

        foreach ($array as $key => $value) {
            $method = self::getSetter($key);

            if (method_exists($instance, $method)) {
                $instance->$method($value);
            } else {
                $property = $key;
                $instance->$property = $value;
            }
        }

        return $instance;
    }

    /**
     * @param string $fieldName
     * @return string
     */
    private static function getSetter(string $fieldName): string
    {
        return 'set' . self::getProperty($fieldName);
    }

    /**
     * @param string $fieldName
     * @return string
     */
    private static function getProperty(string $fieldName): string
    {
        return join('', array_map('ucfirst', explode('_', $fieldName)));
    }
}
