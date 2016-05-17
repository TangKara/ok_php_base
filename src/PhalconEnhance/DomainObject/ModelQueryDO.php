<?php
/**
 * User: qinjx
 * Date: 15/1/29
 */

namespace OK\PhalconEnhance\DomainObject;

class ModelQueryDO
{
    /**
     * @var string
     */
    protected $columns;

    /**
     * @var string
     */
    protected $distinctColumn;

    /**
     * @var string
     */
    protected $conditions;

    /**
     * @var array
     */
    protected $bind = [];

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var string
     */
    protected $orderBy;

    /**
     * @var string
     */
    protected $groupBy;

    /**
     * @var int
     */
    protected $cacheLifeTime;

    /**
     * @var string
     */
    protected $cacheKey;

    /**
     * @var string
     */
    protected $cacheService;

    /**
     * @var string
     */
    protected $cacheKeyRule;

    /**
     * @var boolean
     */
    protected $forUpdate;

    /**
     * @var boolean
     */
    protected $sharedLock;

    /**
     * @return array
     */
    public function getBind()
    {
        return $this->bind;
    }

    /**
     * @param array $bind
     */
    public function setBind($bind)
    {
        $this->bind = $bind;
    }

    /**
     * @return string
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param string $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return string
     */
    public function getDistinctColumn()
    {
        return $this->distinctColumn;
    }

    /**
     * @param string $distinctColumn
     */
    public function setDistinctColumn($distinctColumn)
    {
        $this->distinctColumn = $distinctColumn;
    }

    /**
     * @return string
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param string $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return string
     */
    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * @param string $groupBy
     */
    public function setGroupBy($groupBy)
    {
        $this->groupBy = $groupBy;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }

    /**
     * ModelBase will insert an prefix to cache key.
     * @param string $cacheKey
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    /**
     * @return int
     */
    public function getCacheLifeTime()
    {
        return $this->cacheLifeTime;
    }

    /**
     * @param int $cacheLifeTime
     */
    public function setCacheLifeTime($cacheLifeTime)
    {
        $this->cacheLifeTime = $cacheLifeTime;
    }

    /**
     * @return boolean
     */
    public function isForUpdate()
    {
        return $this->forUpdate;
    }

    /**
     * @param boolean $forUpdate
     */
    public function setForUpdate($forUpdate)
    {
        $this->forUpdate = $forUpdate;
    }

    /**
     * @return boolean
     */
    public function isSharedLock()
    {
        return $this->sharedLock;
    }

    /**
     * @param boolean $sharedLock
     */
    public function setSharedLock($sharedLock)
    {
        $this->sharedLock = $sharedLock;
    }

    /**
     * @return string
     */
    public function getCacheService()
    {
        return $this->cacheService;
    }

    /**
     * @param string $cacheService
     */
    public function setCacheService($cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * @return string
     */
    public function getCacheKeyRule()
    {
        return $this->cacheKeyRule;
    }

    /**
     * @param string $cacheKeyRule
     */
    public function setCacheKeyRule($cacheKeyRule)
    {
        $this->cacheKeyRule = $cacheKeyRule;
    }
} 