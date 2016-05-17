<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/12/23
 * Time: 上午11:12
 */

namespace OK\PhalconEnhance\DomainObject;

class QueueWatcherConfigDO
{
    /**
     * @var string
     */
    protected $queue;

    /**
     * @var string
     */
    protected $tube;

    /**
     * @var \Closure
     */
    protected $callback;

    /**
     * @var int
     */
    protected $delay;

    /**
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param string $queue
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;
    }

    /**
     * @return string
     */
    public function getTube()
    {
        return $this->tube;
    }

    /**
     * @param string $tube
     */
    public function setTube($tube)
    {
        $this->tube = $tube;
    }

    /**
     * @return \Closure
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param \Closure $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * in seconds
     * @param int $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }
}