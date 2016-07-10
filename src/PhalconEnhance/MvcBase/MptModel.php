<?php
/**
 * Created by PhpStorm.
 * User: qin
 * Date: 7/10/16
 * Time: 1:19 PM
 */

namespace OK\PhalconEnhance\MvcBase;


use OK\PhalconEnhance\DomainObject\ModelQueryDO;

/**
 * This is a special table to store hierarchical data (in db)
 * Using "Modified Preorder Tree Traversal" algorithm
 * See https://www.sitepoint.com/hierarchical-data-database-2/ for more detail
 * 
 * Class MptModel
 * @package OK\PhalconEnhance\MvcBase
 */
class MptModel extends ModelBase
{
    /**
     * @var string
     */
    protected $fieldNameOfDepth = "depth";

    /**
     * @var string
     */
    protected $fieldNameOfLeftValue = "left_value";

    /**
     * @var string
     */
    protected $fieldNameOfRightValue = "right_value";

    /**
     * @var string
     */
    protected $fieldNameOfRootId;

    /**
     * @param string $fieldNameOfDepth
     * @return static
     */
    public function setFieldNameOfDepth($fieldNameOfDepth)
    {
        $this->fieldNameOfDepth = $fieldNameOfDepth;
        return $this;
    }

    /**
     * @param string $fieldNameOfLeftValue
     * @return static
     */
    public function setFieldNameOfLeftValue($fieldNameOfLeftValue)
    {
        $this->fieldNameOfLeftValue = $fieldNameOfLeftValue;
        return $this;
    }

    /**
     * @param string $fieldNameOfRightValue
     * @return static
     */
    public function setFieldNameOfRightValue($fieldNameOfRightValue)
    {
        $this->fieldNameOfRightValue = $fieldNameOfRightValue;
        return $this;
    }

    /**
     * @param string $fieldNameOfRootId
     * @return static
     */
    public function setFieldNameOfRootId($fieldNameOfRootId)
    {
        $this->fieldNameOfRootId = $fieldNameOfRootId;
        return $this;
    }

    /**
     * Please call this method without parameter
     * The parameter is only used for interface declaration compatible
     * @param array $data
     * @param array $whiteList
     *
     * @return bool
     */
    public function create($data = [], $whiteList = [])
    {
        unset($data, $whiteList);

        $fieldNameOfDepth = $this->fieldNameOfDepth;
        $fieldNameOfLeftValue = $this->fieldNameOfLeftValue;
        $fieldNameOfRightValue = $this->fieldNameOfRightValue;
        $fieldNameOfRootId = $this->fieldNameOfRootId;

        $do = new ModelQueryDO();
        $do->setConditions("$fieldNameOfRootId = :$fieldNameOfRootId: and $fieldNameOfLeftValue >= :$fieldNameOfLeftValue:");
        $do->setBind([
            $fieldNameOfRootId => $fieldNameOfRootId,
            $fieldNameOfLeftValue => $fieldNameOfLeftValue
        ]);
        $do->setOrderBy("$fieldNameOfLeftValue DESC");
        foreach (static::findUseDO($do) as $node) {
            $node->$fieldNameOfLeftValue += 2;
            if (!$node->update()) {
                return false;
            }
        }

        $do = new ModelQueryDO();
        $do->setConditions("$fieldNameOfRootId = :$fieldNameOfRootId: and $fieldNameOfRightValue >= :$fieldNameOfRightValue:");
        $do->setBind([
            $fieldNameOfRootId => $fieldNameOfRootId,
            $fieldNameOfRightValue => $fieldNameOfLeftValue
        ]);
        $do->setOrderBy("$fieldNameOfRightValue DESC");
        foreach (static::findUseDO($do) as $node) {
            $node->$fieldNameOfRightValue += 2;
            if (!$node->update()) {
                return false;
            }
        }

        $do = new ModelQueryDO();
        $do->setConditions("$fieldNameOfRootId = :$fieldNameOfRootId: and $fieldNameOfLeftValue < :$fieldNameOfLeftValue:
        and $fieldNameOfRightValue > :$fieldNameOfRightValue:");
        $do->setBind([
            $fieldNameOfRootId => $fieldNameOfRootId,
            $fieldNameOfLeftValue => $fieldNameOfLeftValue,
            $fieldNameOfRightValue => $this->$fieldNameOfRightValue
        ]);
        $this->$fieldNameOfDepth = parent::countUseDO($do) + 1;

        return parent::create();
    }

    /**
     * Node with child can not be deleted
     * Only leaf node can be deleted
     *
     * @return bool
     */
    public function delete()
    {
        if (!$this->isLeafNode()) {
            return false;
        }
        if (!parent::delete()) {
            return false;
        }

        $fieldNameOfLeftValue = $this->fieldNameOfLeftValue;
        $fieldNameOfRightValue = $this->fieldNameOfRightValue;
        $fieldNameOfRootId = $this->fieldNameOfRootId;

        $do = new ModelQueryDO();
        $do->setForUpdate(true);
        $do->setConditions("$fieldNameOfRootId = :$fieldNameOfRootId: and $fieldNameOfLeftValue > :$fieldNameOfLeftValue:");
        $do->setBind([
            $fieldNameOfRootId => $this->$fieldNameOfRootId,
            $fieldNameOfLeftValue => $this->$fieldNameOfLeftValue
        ]);
        $do->setOrderBy($fieldNameOfLeftValue);
        foreach (static::findUseDO($do) as $node) {
            $node->$fieldNameOfLeftValue -= 2;
            if (!$node->update()) {
                return false;
            }
        }

        $do = new ModelQueryDO();
        $do->setForUpdate(true);
        $do->setConditions("$fieldNameOfRootId = :$fieldNameOfRootId: and $fieldNameOfRightValue > :$fieldNameOfRightValue:");
        $do->setBind([
            $fieldNameOfRootId => $this->$fieldNameOfRootId,
            $fieldNameOfRightValue => $this->$fieldNameOfRightValue
        ]);
        $do->setOrderBy($fieldNameOfRightValue);
        foreach (static::findUseDO($do) as $node) {
            $node->$fieldNameOfRightValue -= 2;
            if (!$node->update()) {
                return false;
            }
        }
        return true;
    }

    /**
     * This method does not update depth, left value, right value, root id, to avoid potential mistakes
     * Please call this method instead of update()
     *
     * @return bool
     */
    public function updateContent()
    {
        $this->skipAttributesOnUpdate([
            $this->fieldNameOfDepth,
            $this->fieldNameOfLeftValue,
            $this->fieldNameOfRightValue,
            $this->fieldNameOfRootId
        ]);
        return $this->update();
    }

    /**
     * @return bool
     */
    public function isLeafNode()
    {
        $fieldNameOfLeftValue = $this->fieldNameOfLeftValue;
        $fieldNameOfRightValue = $this->fieldNameOfRightValue;
        return (int)$this->$fieldNameOfRightValue - (int)$this->$fieldNameOfLeftValue === 1;
    }
}