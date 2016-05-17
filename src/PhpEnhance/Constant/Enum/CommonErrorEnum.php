<?php

/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/12/23
 * Time: 上午10:25
 */
namespace OK\PhpEnhance\Constant\Enum;

use OK\PhpEnhance\DataStructure\Enum;

class CommonErrorEnum extends Enum {
    const FORBIDDEN = 403;
    const ITEM_NOT_EXISTS = 404;
    const PRECONDITION_FAILED = 412;
    const INTERNAL_SERVER_ERROR = 500;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
}