<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/8/6
 * Time: 下午6:26
 */

namespace OK\Util;


class CommonUtil
{
    /**
     * @param $file
     * @param bool $returnEmptyArray
     * @return mixed
     */
    static public function includeIfExists($file, $returnEmptyArray = true)
    {
        $includedData = null;
        if (file_exists($file)) {
            /** @noinspection PhpIncludeInspection */
            $includedData = include($file);
        }

        if (!$includedData && $returnEmptyArray) {
            return [];
        } else {
            return $includedData;
        }
    }
}