<?php
/**
 * User: qinjx
 * Date: 15/9/8
 */

namespace OK\PhpEnhance\DomainObject;


class SuccessResultDO extends ServiceResultDO
{
    /**
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        $this->success = true;
        if ($data !== null) {
            $this->data = $data;
        }
    }
}