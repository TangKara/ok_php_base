<?php
/**
 * User: qinjx
 * Date: 15/2/18
 */

namespace OK\PhpEnhance\DomainObject;


abstract class ServiceReturnDataDO implements \JsonSerializable
{
    /**
     * Process internal (protected in subclass) object members when json_encode, filtered null out
     * @return array
     */
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this), function ($v) {
            return $v !== null;
        });
    }
}