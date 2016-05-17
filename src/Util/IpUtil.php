<?php
/**
 * User: qinjx
 * Date: 15/3/1
 */

namespace OK\Util;


class IpUtil
{
    /**
     * ip2long("1.1.1.1")
     */
    const IPV4_MIN = 16843009;

    /**
     * ip2long("254.254.254.254")
     */
    const IPV4_MAX = 4278124286;

    /**
     * @return string
     */
    static public function getIp()
    {
        return $_SERVER["REMOTE_ADDR"];
    }

    /**
     * @return int
     */
    static public function getIpAsUnsignedLong()
    {
        $longIp = ip2long(static::getIp());
        if (PHP_INT_SIZE === 4 && $longIp < 0) {//negative $longIp under 32bit OS, add 2^32
            $longIp += 4294967296;
        }
        return $longIp;
    }
}