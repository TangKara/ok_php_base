<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 16/2/12
 * Time: 上午1:10
 */

namespace OK\PhpEnhance\Constant\Enum;


use OK\PhpEnhance\DataStructure\Enum;

class ChecksumTypeEnum extends Enum
{
    const SHA = "sha";
    const SHA256 = "sha256";
    const MD5 = "md5";
}