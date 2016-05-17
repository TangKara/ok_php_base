<?php
/**
 * User: qinjx
 * Date: 15/1/29
 */

namespace OK\PhpEnhance\DomainObject;


abstract class ServiceResultDO implements \JsonSerializable
{
    /**
     * @var boolean if is success
     */
    protected $success;

    /**
     * @var string
     */
    protected $errorCode;

    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @var mixed
     * Allowed data type as follow:
     *  null
     *  scalar type
     *  object decodes from json_decode(), unserialize()
     *  object implements \JsonSerializable
     */
    protected $data;

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * The actual return type depends the subclass
     * use it like this:
     *
     *
     *   /** @var ServiceReturnDataDO $data * /
     *   $data = $resultDO->getData();
     *   $value = $data->key;
     *
     * @return int
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Process internal (protected in subclass) object members when json_encode, filtered null out
     * @return array
     */
    public function JsonSerialize()
    {
        return array_filter(get_object_vars($this), function ($v) {
            return $v !== null;
        });
    }
}
