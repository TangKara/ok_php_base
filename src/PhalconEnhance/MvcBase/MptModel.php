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
    /** ##### MPT builtin field name declaration ##### */
    /**
     * @return string
     */
    static protected function getFieldNameOfDepth()
    {
        return "depth";
    }

    /**
     * @return string
     */
    static protected function getFieldNameOfLeftValue()
    {
        return "left_value";
    }

    /**
     * @return string
     */
    static protected function getFieldNameOfRightValue()
    {
        return "right_value";
    }

    /**
     * @return string
     */
    static protected function getFieldNameOfRootId()
    {
        return "root_id";
    }
    /** ##### MPT builtin field name declaration ##### */

    /**
     * Please call this method instead of create()
     *
     * @return bool
     */
    public function createNode()
    {
        $fieldNameOfDepth = static::getFieldNameOfDepth();
        $fieldNameOfLeftValue = static::getFieldNameOfLeftValue();
        $fieldNameOfRightValue = static::getFieldNameOfRightValue();
        $fieldNameOfRootId = static::getFieldNameOfRootId();

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
    public function deleteNode()
    {
        $fieldNameOfLeftValue = static::getFieldNameOfLeftValue();
        $fieldNameOfRightValue = static::getFieldNameOfRightValue();
        $fieldNameOfRootId = static::getFieldNameOfRootId();

        if (!$this->isLeafNode()) {
            return false;
        }
        if (!$this->delete()) {
            return false;
        }

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
    public function updateNode()
    {
        $this->skipAttributesOnUpdate([
            static::getFieldNameOfDepth(),
            static::getFieldNameOfLeftValue(),
            static::getFieldNameOfRightValue(),
            static::getFieldNameOfRootId()
        ]);
        return $this->update();
    }

    /**
     * @return bool
     */
    public function isLeafNode()
    {
        $fieldNameOfLeftValue = static::getFieldNameOfLeftValue();
        $fieldNameOfRightValue = static::getFieldNameOfRightValue();
        return (int)$this->$fieldNameOfRightValue - (int)$this->$fieldNameOfLeftValue === 1;
    }
}