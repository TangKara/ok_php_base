<?php
/**
 * User: qinjx
 * Date: 15/9/8
 */

namespace OK\PhpEnhance\DomainObject;

use OK\PhpEnhance\DataStructure\Enum;

class FailureResultDO extends ServiceResultDO
{
    /**
     * @param string|Enum $errorCode
     * @param string $errorMessage
     * @param mixed $data
     */
    public function __construct($errorCode, $errorMessage = null, $data = null)
    {
        $this->success = false;
        if ($errorCode instanceof Enum) {
            $this->errorCode = $errorCode->getValue();
            $this->errorMessage = $errorCode->getKey();
        } else {
            $this->errorCode = (string)$errorCode;
        }

        if ($errorMessage !== null) {
            $this->errorMessage = $errorMessage;
        }

        if ($data !== null) {
            $this->data = $data;
        }
    }
}