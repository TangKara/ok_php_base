<?php
namespace OK\PhalconEnhance\Util;

use OK\PhalconEnhance\Constant\BuiltinServiceName;
use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Logger\Adapter;
use Phalcon\Queue\Beanstalk;
use Phalcon\Session\AdapterInterface as SessionAdapter;
use Phalcon\Db\AdapterInterface as DbAdapter;
use Pheanstalk\Pheanstalk;

/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/8/6
 * Time: 下午3:50
 */
class DiUtil
{
    /**
     * @param array $serviceDefList
     */
    static public function initServices($serviceDefList)
    {
        $di = Di::getDefault();
        foreach ($serviceDefList as $def) {
            if (count($def) < 2) {
                die("Di service must be at least 2 elements");
                continue;
            }
            if (!is_string($def[0])) {
                die("Di service name must be a string");
            }
            if (!is_callable($def[1])) {
                die("Di service definition must be a callable function");
            }

            $shared = true;
            if (isset($def[2]) && $def[2] === false) {
                $shared = false;
            }

            if ($di->has($def[0])) {
                $di->remove($def[0]);
            }

            $di->set($def[0], $def[1], $shared);
        }
    }

    /**
     * @param string $name
     * @return DbAdapter
     */
    static public function getDbService($name)
    {
        return Di::getDefault()->get($name);
    }

    /**
     * @return Loader
     */
    static public function getLoaderService()
    {
        return Di::getDefault()->get(BuiltinServiceName::LOADER);
    }

    /**
     * @param string $name
     * @return Adapter
     */
    static public function getLoggerService($name)
    {
        return Di::getDefault()->get($name);
    }

    /**
     * @param string $name
     * @return Beanstalk
     */
    static public function getQueueService($name)
    {
        $service = Di::getDefault()->getService($name);
        $service->setShared(false);
        return $service->resolve();
    }

    /**
     * @param string $name
     * @return Pheanstalk
     */
    static public function getPheanstalkQueueService($name)
    {
        $service = Di::getDefault()->getService($name);
        $service->setShared(false);
        return $service->resolve();
    }

    /**
     * @return SessionAdapter
     */
    static public function getSessionService()
    {
        return Di::getDefault()->get(BuiltinServiceName::SESSION);
    }
}