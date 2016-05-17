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

class QueueUtilPh
{
    /**
     * @var array
     */
    static protected $tubePoolMap = [];

    /**
     * @param string $serviceName
     * @param string $tube
     * @param mixed $data
     * @return int
     */
    static public function fireEventQueue($serviceName, $tube, $data)
    {
        $cacheKey = $serviceName . "-" . $tube;
        if (array_key_exists($cacheKey, self::$tubePoolMap)) {
            $queue = self::$tubePoolMap[$cacheKey];
        } else {
            $queue = DiUtil::getPheanstalkQueueService($serviceName);
            $queue->useTube($tube);
            self::$tubePoolMap[$cacheKey] = $queue;
        }
        return $queue->put($data);
    }

    /**
     * @param QueueWatcherConfigDO $do
     * @return bool
     * @throws \UnexpectedValueException
     */
    static public function watch(QueueWatcherConfigDO $do)
    {
        echo "\n", "Processing jobs in ", $do->getQueue() . "." . $do->getTube(), "\n";
        $queue = DiUtil::getPheanstalkQueueService($do->getQueue());
        $queue->watch($do->getTube());
        $callback = $do->getCallback();
        $delay = 30;
        if ($do->getDelay()) {
            $delay = $do->getDelay();
        }
        for (; ;) {
            $job = $queue->reserve();
            $body = $job->getData();
            /** @var ServiceResultDO $resultDO */
            try {
                $resultDO = $callback($body);
                if (!($resultDO instanceof ServiceResultDO)) {
                    throw new \UnexpectedValueException("return type of callback must be an instance of ServiceResultDO");
                }
                if ($resultDO->isSuccess()) {
                    $queue->delete($job);
                    echo $body, "\t";
                } else {
                    switch ($resultDO->getErrorCode()) {
                        case CommonErrorEnum::PRECONDITION_FAILED:
                        case CommonErrorEnum::GATEWAY_TIMEOUT:
                            $queue->release($job, 100, $delay);
                            break;
                        case CommonErrorEnum::ITEM_NOT_EXISTS:
                            $queue->delete($job);
                            break;
                        default:
                            $queue->bury($job);
                            echo $body, "\t", $resultDO->getErrorMessage(), "\n";
                    }
                }
            } catch (\Exception $e) {
                $queue->release($job, 3600, $delay);
                echo $e->getMessage();
            }
        }
        return true;
    }
}