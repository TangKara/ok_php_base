<?php
/**
 * Created by PhpStorm.
 * User: wangpeng
 * Date: 16/11/11
 * Time: 下午10:07
 */

namespace OK\Util;


class TimestampUtil
{
    /**
     * @return int
     */
    static public function getMicrotime()
    {
        return (int) (microtime(true) * 1000);
    }
}