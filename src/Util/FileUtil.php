<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 16/2/7
 * Time: 上午10:56
 */

namespace OK\Util;


use OK\PhpEnhance\Constant\Enum\ChecksumTypeEnum;

class FileUtil
{
    /**
     * @param string $hash
     * @return string
     */
    static public function getHashedFilePath($hash)
    {
        return substr($hash, 0, 2) . DIRECTORY_SEPARATOR . substr($hash, 2, 2) . DIRECTORY_SEPARATOR . $hash;
    }

    /**
     * @param int $size
     * @param ChecksumTypeEnum $checksumType
     * @param string $checksum
     * @param string $filePath
     * @return bool
     */
    static public function verifyFile(ChecksumTypeEnum $checksumType, $checksum, $filePath, $size = 0)
    {
        if ($size !== 0 && filesize($filePath) !== (int) $size) {
            return false;
        }

        switch ($checksumType->getValue()) {
            case ChecksumTypeEnum::SHA:
                return $checksum === sha1_file($filePath);
            case ChecksumTypeEnum::SHA256:
                return $checksum === hash_file(ChecksumTypeEnum::SHA256, $filePath);
            case ChecksumTypeEnum::MD5:
                return $checksum === md5_file($filePath);
        }
        return false;
    }
}