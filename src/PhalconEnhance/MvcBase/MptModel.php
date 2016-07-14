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
     * Field: depth
     * The depth of current node, starts from 1, if a node does't have any parent, its depth is 1.
     * Depth is redundant, for simplification and performance reason.
     *
     * @return string
     */
    static protected function getFieldNameOfDepth()
    {
        return "depth";
    }

    /**
     * Field: left value
     * A unique integer in a tree
     *
     * @return string
     */
    static protected function getFieldNameOfLeftValue()
    {
        return "left_value";
    }

    /**
     * Field: right value
     * A unique integer in a tree
     *
     * @return string
     */
    static protected function getFieldNameOfRightValue()
    {
        return "right_value";
    }

    /**
     * Field: root id
     * A tree is made of nodes with the same "root id" value
     * With this field, we can store multi trees in the same sql table
     *
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
     * $a is a node already exists in db
     * If you want insert a new node (called $b) as the first child node of $a
     * Call this method like this: $b->createUnder($a->leftValue)
     *
     * Please note that, $a must be a leaf node (node without child)
     * If $a already has children nodes, please use createAtLeftOf() and createAtRightOf()
     *
     * @param int $leftValue
     * @return bool
     */
    public function createAsChildOf($leftValue)
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
        return $this->create();
    }

    /**
     * $a is a node already exists in db
     * If you want insert a new node (called $b) on the left hand of $a
     * Call this method like this: $b->createAtLeftOf($a->leftValue)
     *
     * @param int $leftValue
     * @return bool
     */
    public function createAtLeftOf($leftValue)
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
        return $this->create();
    }

    /**
     * $a is a node already exists in db
     * If you want insert a new node (called $b) on the right hand of $a
     * Call this method like this: $b->createAtRightOf($a->leftValue)
     *
     * @param int $leftValue
     * @return bool
     */
    public function createAtRightOf($leftValue)
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
        return $this->create();
    }

    /**
     * Use this method to create the first node of a tree
     * Then use createAtRightOf()/createAtLeftOf()/createAsChildOf() to create other nodes
     *
     * @param array $data don't use this parameter, it is declared for interface compatible
     * @param array $whiteList don't use this parameter, it is declared for interface compatible
     * @return bool
     */
    public function create($data = [], $whiteList = [])
    {
        unset($data, $whiteList);

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
        $fieldNameOfLeftValue = static::getFieldNameOfLeftValue();
        $fieldNameOfRightValue = static::getFieldNameOfRightValue();
        $fieldNameOfRootId = static::getFieldNameOfRootId();

        if (!$this->isLeafNode()) {
            return false;
        }
        if (!parent::delete()) {
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
     * Please call this method instead of update()
     * This method does not update depth, left value, right value, root id, to avoid potential bug
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
     * leaf node means node has no child
     * 
     * @return bool
     */
    public function isLeafNode()
    {
        $fieldNameOfLeftValue = static::getFieldNameOfLeftValue();
        $fieldNameOfRightValue = static::getFieldNameOfRightValue();
        return (int)$this->$fieldNameOfRightValue - (int)$this->$fieldNameOfLeftValue === 1;
    }
}