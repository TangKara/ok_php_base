<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/12/4
 * Time: 上午9:50
 */

namespace OK\Util;


class ShellUtil
{
    /**
     * @param string $remoteUrl
     * @param string $localFile
     * @todo axel supporting
     * @return string
     */
    static public function download($remoteUrl, $localFile = null)
    {
        $cmd = "wget -q -N $remoteUrl";
        if ($localFile) {
            $cmd .= " -O $localFile";
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            static::prepareDir($localFile);
        }
        return exec($cmd);
    }

    /**
     * @param $source
     * @param $destination
     * @return bool
     */
    static public function copy($source, $destination)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        static::prepareDir($destination);
        return copy($source, $destination);
    }

    /**
     * @param $source
     * @param $destination
     * @return bool
     */
    static public function move($source, $destination)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        static::prepareDir($destination);
        copy($source, $destination);
        return unlink($source);
    }

    /**
     * @param $file
     * @param $link
     * @return bool
     */
    static public function symlink($file, $link)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        static::prepareDir($link);
        return symlink($file, $link);
    }

    /**
     * @param $file
     * @return bool
     * @throws \RuntimeException
     */
    static protected function prepareDir($file)
    {
        $dir = dirname($file);
        if (!@mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException("Failed to create dir: $dir");
        }
        return true;
    }
}