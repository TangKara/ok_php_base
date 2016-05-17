<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 16/1/16
 * Time: 下午8:20
 */

namespace OK\PhalconEnhance\DomainObject;


class FullScanUseIntIdConfigDO
{
    /**
     * @var string
     */
    protected $idField;

    /**
     * @var int
     */
    protected $idStart = 0;

    /**
     * @var \Closure
     */
    protected $callback;

    /**
     * @var int
     */
    protected $pageSize = 1000;

    /**
     * @return string
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * ID filed must be an integer field, and must be a unique key
     * @param string $idField
     */
    public function setIdField($idField)
    {
        $this->idField = $idField;
    }

    /**
     * @return int
     */
    public function getIdStart()
    {
        return $this->idStart;
    }

    /**
     * $idStart is including in full table scan
     * For example, $idStart = 101, means id=101 will be scanned
     * @param int $idStart
     */
    public function setIdStart($idStart)
    {
        $this->idStart = $idStart;
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
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }
}