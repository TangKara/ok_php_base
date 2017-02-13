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
     * @param string $ip
     * @return int
     */
    static public function castToLong($ip)
    {
        $longIp = ip2long($ip);
        if (PHP_INT_SIZE === 4 && $longIp < 0) {//negative $longIp under 32bit OS, add 2^32
            $longIp += 4294967296;
        }
        return $longIp;
    }

    /**
     * If remote_addr is private ip, check the x-forwarded-for
     * @return string
     */
    static public function getIp()
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && self::isPrivateIp($ip)) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        return $ip;
    }

    /**
     * client -> haproxy -> php
     * Haproxy will set HTTP_X_FORWARDED_FOR header by default
     *
     * If your haproxy access php server over public ip, please use this method.
     *
     * @return string
     */
    static public function getIpBehindLb()
    {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    }

    /**
     * @return int
     */
    static public function getIpAsUnsignedLong()
    {
        return self::castToLong(self::getIp());
    }

    static public function isPrivateIp($ip)
    {
        $long = self::castToLong($ip);
        return $long > 167772160 && $long < 184549375 // 10.0.0.0 - 10.255.255.255
            || $long > 2130706432 && $long < 2147483647 // 127.0.0.0 - 127.255.255.255
            || $long > 2886729728 && $long < 2887778303 // 172.16.0.0 - 172.31.255.255
            || $long > 3232235520 && $long < 3232301055; // 192.168.0.0 - 192.168.255.255
    }
}