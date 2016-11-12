<?php
/**
 * Created by PhpStorm.
 * User: qin
 * Date: 11/11/2016
 * Time: 3:07 PM
 */

namespace OK\PhalconEnhance\Util;

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt;
class VoltUtil
{
    public static function getString($templatePath, $params)
    {
        $view = new View();
        $volt = new Volt($view);
        ob_start();
        $volt->render($templatePath, $params);
        $str = ob_get_contents();
        ob_clean();
        return $str;
    }
}