<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/11/20
 * Time: 下午3:34
 */

namespace OK\PhpEnhance\DomainObject;


class SearchResultDO extends ServiceReturnDataDO
{
    /**
     * @var int
     */
    protected $total;

    /**
     * @var bool
     */
    protected $hasMore = false;

    /**
     * @var array
     */
    protected $aggregation;

    /**
     * @var array
     */
    protected $itemList;

    /**
     * @var array
     */
    protected $highlight;

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return boolean
     */
    public function isHasMore()
    {
        return $this->hasMore;
    }

    /**
     * @param boolean $hasMore
     */
    public function setHasMore($hasMore)
    {
        $this->hasMore = $hasMore;
    }

    /**
     * @return array
     */
    public function getAggregation()
    {
        return $this->aggregation;
    }

    /**
     * @param array $aggregation
     */
    public function setAggregation($aggregation)
    {
        $this->aggregation = $aggregation;
    }

    /**
     * @return array
     */
    public function getItemList()
    {
        return $this->itemList;
    }

    /**
     * @param array $itemList
     */
    public function setItemList($itemList)
    {
        $this->itemList = $itemList;
    }

    /**
     * @return array
     */
    public function getHighlight()
    {
        return $this->highlight;
    }

    /**
     * @param array $highlight
     */
    public function setHighlight($highlight)
    {
        $this->highlight = $highlight;
    }
}