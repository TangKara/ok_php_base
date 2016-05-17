<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/11/4
 * Time: 下午8:49
 */

namespace OK\PhalconEnhance\Util;


use OK\PhpEnhance\Constant\BuiltinKey;

class LoggerUtil
{
    /**
     * @param string $service
     * @param int $level
     * @param string $content
     */
    static public function log($service, $level, $content)
    {
        $caller = debug_backtrace()[0];
        $file = $caller[BuiltinKey::TRACE_FILE];
        $line = $caller[BuiltinKey::TRACE_LINE];
        $logger = DiUtil::getLoggerService($service);
        $logger->log("$content  [printed by $file line $line]", $level);
    }
}