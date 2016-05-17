<?php
/**
 * User: qinjx
 * Date: 15/1/22
 */

namespace OK\Util;


class StringUtil
{
    /**
     * Generate a random hex string, with fixed length
     * @param int $len
     * @return string
     */
    static public function getRandomHex($len)
    {
        return substr(bin2hex(self::getRandomBytes($len)), 0, $len);
    }

    /**
     * Generate a random base64 string, with fixed length
     * @param int $len
     * @return string
     */
    static public function getRandomBase64($len)
    {
        return substr(base64_encode(self::getRandomBytes($len)), 0, $len);
    }

    /**
     * Generate a random "word character" string (alphanumeric characters plus underscore)
     * @param int $len
     * @return string
     */
    static public function getRandomWordCharacters($len)
    {
        $charPool = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
        $charPoolSize = strlen($charPool);

        $string = "";
        while ($len) {
            $string .= $charPool[mt_rand(0, $charPoolSize - 1)];
            --$len;
        }

        return $string;
    }

    /**
     * @param int $len
     * @return string
     */
    static protected function getRandomBytes($len)
    {
        return openssl_random_pseudo_bytes($len);
    }

    /**
     * @param string $content
     * @param string $salt
     * @return string
     */
    static public function calculateHashWithTwoSalt($content, $salt)
    {
        return md5($salt . $content . $salt);
    }

    /**
     * @param string $content
     * @param string $salt
     * @return string
     */
    static public function calculateHashWithLeftSalt($content, $salt)
    {
        return md5($salt . $content);
    }

    /**
     * @param $headerKey
     * @return string
     */
    static public function convertHttpHeaderToServerKey($headerKey)
    {
        return "HTTP_" . strtoupper(str_replace("-", "_", $headerKey));
    }

    /**
     * @param string $raw
     * @return string
     */
    static public function base64EncodeWithoutSlash($raw)
    {
        return str_replace("/", "-", base64_encode($raw));
    }

    /**
     * @param string $encoded
     * @return string
     */
    static public function base64DecodeWithoutSlash($encoded)
    {
        return base64_encode(str_replace("-", "/", $encoded));
    }
}