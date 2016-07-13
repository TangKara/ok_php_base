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

    /** ##### Utilities for node query ##### */
    const CACHE_KEY_RULE_LIST_BY_ROOT_ID = "list_by_root_id";
    const CACHE_KEY_RULE_LIST_BY_ROOT_ID_REVERSE = "list_by_root_id_reverse";

    /**
     * @return array
     */
    static protected function getNonUniqueCacheKeyRules()
    {
        return [
            self::CACHE_KEY_RULE_LIST_BY_ROOT_ID => [static::getFieldNameOfRootId()],
            self::CACHE_KEY_RULE_LIST_BY_ROOT_ID_REVERSE => [static::getFieldNameOfRootId()]
        ];
    }

    /**
     * @return array
     */
    static protected function getUniqueKeys()
    {
        return [
            static::getFieldNameOfRootId() . "," . static::getFieldNameOfLeftValue(),
            static::getFieldNameOfRootId() . "," . static::getFieldNameOfRightValue()
        ];
    }
    
    /**
     * Get all node list of a tree
     * Root is on the top
     * Used for UI displaying
     *
     * @param $rootId
     *
     * @return static[]
     */
    static public function getAllNode($rootId)
    {
        $fieldNameOfRootId = static::getFieldNameOfRootId();
        $fieldNameOfLeftValue = static::getFieldNameOfLeftValue();

        $do = new ModelQueryDO();
        $do->setConditions("$fieldNameOfRootId = :$fieldNameOfRootId:");
        $do->setBind([
            $fieldNameOfRootId => $rootId
        ]);
        $do->setOrderBy($fieldNameOfLeftValue);
        $do->setCacheKeyRule(self::CACHE_KEY_RULE_LIST_BY_ROOT_ID);
        return parent::findUseDO($do);
    }

    /**
     * Get all node list of a tree, in reverse order
     * Leaf is on the top
     * Used for object data preparing
     *
     * @param $rootId
     *
     * @return static[]
     */
    static public function getAllNodeReverse($rootId)
    {
        $fieldNameOfRootId = static::getFieldNameOfRootId();
        $fieldNameOfLeftValue = static::getFieldNameOfLeftValue();

        $do = new ModelQueryDO();
        $do->setConditions("$fieldNameOfRootId = :$fieldNameOfRootId:");
        $do->setBind([
            $fieldNameOfRootId => $rootId
        ]);
        $do->setOrderBy("$fieldNameOfLeftValue DESC");
        $do->setCacheKeyRule(self::CACHE_KEY_RULE_LIST_BY_ROOT_ID_REVERSE);
        return parent::findUseDO($do);
    }
    /** ##### Utilities for node query ##### */

    /**
     * @param int $leftValue
     * @return bool
     */
    public function createAfter($leftValue)
    {
        $fieldNameOfLeftValue = static::getFieldNameOfLeftValue();
        $fieldNameOfRightValue = static::getFieldNameOfRightValue();
        $fieldNameOfRootId = static::getFieldNameOfRootId();

        $baseNode = static::findUniqueByUK([
            $fieldNameOfRootId => $this->$fieldNameOfRootId,
            $fieldNameOfLeftValue => $leftValue
        ]);
        if ($baseNode === null) {
            return false;
        }

        $this->$fieldNameOfLeftValue = $baseNode->$fieldNameOfRightValue + 1;
        return $this->createNode();
    }

    /**
     * @param int $leftValue
     * @return bool
     */
    public function createBefore($leftValue)
    {
        $fieldNameOfLeftValue = static::getFieldNameOfLeftValue();
        $fieldNameOfRootId = static::getFieldNameOfRootId();

        $baseNode = static::findUniqueByUK([
            $fieldNameOfRootId => $this->$fieldNameOfRootId,
            $fieldNameOfLeftValue => $leftValue
        ]);
        if ($baseNode === null) {
            return false;
        }
        $this->$fieldNameOfLeftValue = $leftValue;
        return $this->createNode();
    }

    /**
     * You can only create a node under a leaf node.
     *
     * @param int $leftValue
     * @return bool
     */
    public function createUnder($leftValue)
    {
        $fieldNameOfLeftValue = static::getFieldNameOfLeftValue();
        $fieldNameOfRootId = static::getFieldNameOfRootId();

        $baseNode = static::findUniqueByUK([
            $fieldNameOfRootId => $this->$fieldNameOfRootId,
            $fieldNameOfLeftValue => $leftValue
        ]);
        if ($baseNode === null) {
            return false;
        }

        if (!$baseNode->isLeafNode()) {
            return false;
        }

        $this->$fieldNameOfLeftValue = $leftValue + 1;
        return $this->createNode();
    }

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
        //calc right value
        $this->$fieldNameOfRightValue = $this->$fieldNameOfLeftValue + 1;

        $do = new ModelQueryDO();
        $do->setConditions("$fieldNameOfRootId = :$fieldNameOfRootId: and $fieldNameOfLeftValue >= :$fieldNameOfLeftValue:");
        $do->setBind([
            $fieldNameOfRootId => $this->$fieldNameOfRootId,
            $fieldNameOfLeftValue => $this->$fieldNameOfLeftValue
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
            $fieldNameOfRootId => $this->$fieldNameOfRootId,
            $fieldNameOfRightValue => $this->$fieldNameOfLeftValue
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
            $fieldNameOfRootId => $this->$fieldNameOfRootId,
            $fieldNameOfLeftValue => $this->$fieldNameOfLeftValue,
            $fieldNameOfRightValue => $this->$fieldNameOfRightValue
        ]);
        $this->$fieldNameOfDepth = parent::countUseDO($do) + 1;

        return $this->create();
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