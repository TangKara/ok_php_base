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
    public static function render($templatePath, $params)
    {
        $view = new View();
        $volt = new Volt($view);
        $volt->render($templatePath, $params);
    }
}