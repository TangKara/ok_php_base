<?php
/**
 * User: qinjx
 * Date: 15/2/15
 */

namespace OK\Util;


use Phalcon\Text;

class ObjectUtil
{
    /** @noinspection GenericObjectTypeUsageInspection */
    /**
     * @param object $obj
     * @param string $propName
     * @param bool $reference
     * @return mixed|null
     */
    static public function getObjPropDyn($obj, $propName, $reference = false)
    {
        if ($reference) {
            if (isset($obj->$propName)) {
                /** @noinspection OneTimeUseVariablesInspection */
                $value = &$obj->$propName;
                return $value;
            }
            return null;
        } else {
            $getterName = "get" . Text::camelize($propName);
            if (method_exists($obj, $getterName)) {
                return $obj->$getterName();
            }

            if (isset($obj->$propName)) {
                return $obj->$propName;
            }
            return null;
        }
    }

    /** @noinspection GenericObjectTypeUsageInspection */
    /**
     * @param object $obj
     * @param string $propName
     * @param mixed|null $value
     */
    static public function setObjPropDyn($obj, $propName, $value)
    {
        $setterName = "set" . Text::camelize($propName);
        if (method_exists($obj, $setterName)) {
            $obj->$setterName($value);
            return;
        }
        /**
         * @todo check propName iss protected/private
         */
        $obj->$propName = $value;
    }

    /** @noinspection GenericObjectTypeUsageInspection */
    /**
     * @param object $destination
     * @param object $sourceObject
     * @return mixed
     */
    static public function cast($destination, $sourceObject)
    {
        $sourceReflection = new \ReflectionObject($sourceObject);
        $destinationReflection = new \ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination, $value);
            } else {
                $destination->$name = $value;
            }
        }
        return $destination;
    }
}