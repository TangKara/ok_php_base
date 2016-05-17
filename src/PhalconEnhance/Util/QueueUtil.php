<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/11/1
 * Time: 下午11:21
 */

namespace OK\PhalconEnhance\Util;


use OK\PhalconEnhance\DomainObject\QueueWatcherConfigDO;
use OK\PhpEnhance\Constant\Enum\CommonErrorEnum;
use OK\PhpEnhance\DomainObject\ServiceResultDO;

class QueueUtil
{
    /**
     * @var array
     */
    static protected $tubePoolMap = [];

    /**
     * @param string $serviceName
     * @param string $tube
     * @param mixed $data
     * @param array $options
     * @return int
     */
    static public function fireEventQueue($serviceName, $tube, $data, $options = null)
    {
        $cacheKey = $serviceName . "-" . $tube;
        if (array_key_exists($cacheKey, self::$tubePoolMap)) {
            $queue = self::$tubePoolMap[$cacheKey];
        } else {
            $queue = DiUtil::getQueueService($serviceName);
            $queue->choose($tube);
            self::$tubePoolMap[$cacheKey] = $queue;
        }
        return $queue->put($data, $options);
    }

    /**
     * @param QueueWatcherConfigDO $do
     * @return bool
     * @throws \UnexpectedValueException
     */
    static public function watch(QueueWatcherConfigDO $do)
    {
        echo "\n", "Processing jobs in ", $do->getQueue() . "." . $do->getTube(), "\n";
        $queue = DiUtil::getQueueService($do->getQueue());
        $queue->watch($do->getTube());
        $callback = $do->getCallback();
        $delay = 30;
        if ($do->getDelay()) {
            $delay = $do->getDelay();
        }
        for (; ;) {
            $job = $queue->reserve();
            $body = $job->getBody();
            /** @var ServiceResultDO $resultDO */
            try {
                $resultDO = $callback($body);
                if (!($resultDO instanceof ServiceResultDO)) {
                    throw new \UnexpectedValueException("return type of callback must be an instance of ServiceResultDO");
                }
                if ($resultDO->isSuccess()) {
                    $job->delete();
                    echo $body, "\t";
                } else {
                    switch ($resultDO->getErrorCode()) {
                        case CommonErrorEnum::PRECONDITION_FAILED:
                        case CommonErrorEnum::GATEWAY_TIMEOUT:
                            $job->release(100, $delay);
                            break;
                        case CommonErrorEnum::ITEM_NOT_EXISTS:
                            $job->delete();
                            break;
                        default:
                            $job->bury();
                            echo $body, "\t", $resultDO->getErrorMessage(), "\n";
                    }
                }
            } catch (\Exception $e) {
                $job->release(100, 3600);
                echo $e->getMessage();
            }
        }
        return true;
    }
}