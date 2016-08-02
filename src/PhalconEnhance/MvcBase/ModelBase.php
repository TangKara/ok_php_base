<?php
/**
 * User: qinjx
 * Date: 15/1/29
 */

namespace OK\PhalconEnhance\MvcBase;

use OK\PhalconEnhance\Constant\BuiltinKey;
use OK\PhalconEnhance\Constant\BuiltinServiceName;
use OK\PhalconEnhance\DomainObject\FullScanUseIntIdConfigDO;
use OK\PhalconEnhance\DomainObject\ModelQueryDO;
use OK\Util\StringUtil;
use Phalcon\Cache\BackendInterface;
use Phalcon\Di;
use Phalcon\Mvc\Model;
use Phalcon\Text;

class ModelBase extends Model implements \JsonSerializable
{
    /**
     * The cache key encode way
     */
    const CACHE_KEY_ENCODE_NONE = 0;
    const CACHE_KEY_ENCODE_BASE64 = 1;
    const CACHE_KEY_ENCODE_CRC32 = 2;
    const CACHE_KEY_ENCODE_MD5 = 3;

    /**
     * Special field list of empty
     */
    const CACHE_KEY_FIELD_LIST_EMPTY = "";

    /**
     * Min max id cache key
     */
    const CACHE_KEY_RULE_MIN_MAX_PK_ID = "min_max_pk_id";

    /** ##### Methods for subclass overriding ##### */
    /**
     * @return string
     */
    static protected function getCacheKeyNamespace()
    {
        return str_replace("\\", "_", get_called_class()) . ".";
    }

    /**
     * @return string
     * <code>
     * return ServiceName::REAL_TIME_CACHE;
     * </code>
     */
    static protected function getDefaultCacheService()
    {
    }

    /**
     * @return string
     */
    static protected function getFieldNameOfPK()
    {
        return "id";
    }

    /**
     * @return int
     * <code>
     * return parent::CACHE_KEY_ENCODE_BASE64;
     * </code>
     */
    static protected function getUniqueKeyEncodeWay()
    {
    }

    /**
     * @return array
     * <code>
     * return [
     *  "list_all" => [parent::CACHE_KEY_FIELD_LIST_EMPTY],
     *  "api_param_list" => ["api_id"],
     *  "method_arg_list" => ["api_id", parent::CACHE_KEY_ENCODE_NONE],
     *  "top_seller" => ["seller_id,cat_id,month", parent::CACHE_KEY_ENCODE_BASE64],
     *   key template => [field list, cache key encode way]
     * ];
     * </code>
     *
     */
    static protected function getNonUniqueCacheKeyRules()
    {
    }

    /**
     * static::$pkFieldName will be added automatically
     * @return array
     * <code>
     * return [
     *  "name,version",
     *  field list
     * ];
     * </code>
     *
     */
    static protected function getUniqueKeys()
    {
    }

    /**
     * @return bool
     */
    static protected function isCacheDisabled()
    {
    }

    public function initialize()
    {
        $this->keepSnapshots(true);
    }

    /**
     * delete cache automatically after creating
     */
    public function afterCreate()
    {
        $this->processCache();
    }

    /**
     * delete cache automatically after deleting
     */
    public function afterDelete()
    {
        $this->processCache();
    }

    /**
     * delete cache automatically after updating
     */
    public function afterUpdate()
    {
        $this->processCache();
    }

    /**
     * Enable dynamic updating
     */
    public function beforeUpdate()
    {
        $this->useDynamicUpdate(true);
    }

    /**
     * @param null $filter
     * @return string
     */
    public function getMessages($filter = null)
    {
        $messages = parent::getMessages($filter);
        return json_encode($messages);
    }
    /** ##### Methods for subclass overriding ##### */

    /** ##### Utilities for DB SELECT ##### */
    /**
     * @param ModelQueryDO $do
     * @return int
     */
    final static public function countUseDO(ModelQueryDO $do)
    {
        return parent::count(self::buildParam($do));
    }

    /**
     * @param int|string $id
     * @return static
     */
    final static public function findUniqueByPKId($id)
    {
        if (!self::validatePkIdRange($id)) {
            return null;
        }
        $row = parent::findFirst(self::buildParamForUK([static::getFieldNameOfPK() => $id], false));
        if ($row !== false) {
            return $row;
        } else {
            return null;
        }
    }

    /**
     * @param int|string $id
     * @return static
     */
    final static public function findUniqueByPKIdForUpdate($id)
    {
        if (!self::validatePkIdRange($id)) {
            return null;
        }
        $row = parent::findFirst(self::buildParamForUK([static::getFieldNameOfPK() => $id], true));
        if ($row !== false) {
            return $row;
        } else {
            return null;
        }
    }

    /**
     * @param array $bind
     * @return static
     */
    final static public function findUniqueByUK(array $bind)
    {
        $row = parent::findFirst(self::buildParamForUK($bind, false));
        if ($row !== false) {
            return $row;
        } else {
            return null;
        }
    }

    /**
     * @param array $bind
     * @return static
     */
    final static public function findUniqueByUKForUpdate(array $bind)
    {
        $row = parent::findFirst(self::buildParamForUK($bind, true));
        if ($row !== false) {
            return $row;
        } else {
            return null;
        }
    }

    /**
     * @param ModelQueryDO $do
     * @return static
     */
    final static public function findFirstUseDO(ModelQueryDO $do)
    {
        $row = parent::findFirst(self::buildParam($do));
        if ($row !== false) {
            return $row;
        } else {
            return null;
        }
    }

    /**
     * The return value of Model::find() is an iterator object, not an array
     * @param ModelQueryDO $do
     * @return static[] this is for IDE auto completion
     */
    final static public function findUseDO(ModelQueryDO $do)
    {
        $rows = [];
        /** @var \Iterator $resultSet */
        $resultSet = parent::find(self::buildParam($do));
        foreach ($resultSet as $row) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * @param FullScanUseIntIdConfigDO $configDO
     */
    final static public function fullScanUseIntId(FullScanUseIntIdConfigDO $configDO)
    {
        $idField = $configDO->getIdField();
        if ($idField === null) {
            $idField = static::getFieldNameOfPK();
        }
        $callback = $configDO->getCallback();
        $idStart = (int)$configDO->getIdStart();
        if ($idStart === 0) {
            $idStart = parent::minimum([BuiltinKey::MODEL_COLUMN => $idField]);
        }
        $maxId = parent::maximum([BuiltinKey::MODEL_COLUMN => $idField]);

        $modelQueryDO = new ModelQueryDO();
        $modelQueryDO->setConditions("$idField BETWEEN :id_start: AND :id_end:");
        while ($idStart <= $maxId) {
            $modelQueryDO->setBind([
                "id_start" => $idStart,
                "id_end" => $idStart + $configDO->getPageSize()
            ]);
            foreach (self::findUseDO($modelQueryDO) as $row) {
                $callback($row);
            }
            $idStart += $configDO->getPageSize() + 1;
        }
    }
    /** ##### Utilities for DB SELECT ##### */

    /**
     * @param ModelBase $other
     * @return bool
     */
    final public function copyPropFrom(ModelBase $other)
    {
        $modelClassName = get_class($this);
        if (get_class($other) !== $modelClassName) {
            return false;
        }

        $ref = new \ReflectionClass($modelClassName);
        foreach ($ref->getProperties() as $refProp) {
            $propName = $refProp->name;
            if ($refProp->class === $modelClassName && $other->$propName !== null
                && $this->$propName !== $other->$propName
            ) {
                $this->$propName = $other->$propName;
            }
        }
        return true;
    }

    /**
     * Process internal (protected in subclass) object members when json_encode
     *  - filtered null out
     *  - filtered parent member (in Model and ModelBase) out
     *  - convert field name into camel style
     * @return array
     */
    final public function JsonSerialize()
    {
        $modelClassName = get_class($this);
        $hashSet = [];
        $ref = new \ReflectionClass($modelClassName);
        foreach ($ref->getProperties() as $refProp) {
            if ($refProp->class === $modelClassName) {
                $hashSet[$refProp->name] = true;
            }
        }

        $return = [];
        foreach (get_object_vars($this) as $k => $v) {
            if ($v !== null && array_key_exists($k, $hashSet)) {
                $return[lcfirst(Text::camelize($k))] = $v;
            }
        }
        return $return;
    }

    /** ##### Private methods ##### */
    /**
     * @param ModelQueryDO $do
     * @return array
     */
    final static private function buildParam(ModelQueryDO $do)
    {
        $param = [];

        if ($do->getColumns()) {
            $param[BuiltinKey::MODEL_COLUMNS] = $do->getColumns();
        }

        if ($do->getDistinctColumn()) {
            $param[BuiltinKey::MODEL_DISTINCT_COLUMN] = $do->getDistinctColumn();
        }

        if ($do->getConditions()) {
            $param[BuiltinKey::MODEL_CONDITIONS] = $do->getConditions();
        }

        if (count($do->getBind())) {
            $param[BuiltinKey::MODEL_BIND] = $do->getBind();
        }

        if ($do->getOrderBy()) {
            $param[BuiltinKey::MODEL_ORDER] = $do->getOrderBy();
        }

        if ($do->getGroupBy()) {
            $param[BuiltinKey::MODEL_GROUP] = $do->getGroupBy();
        }

        if ($do->getLimit()) {
            if ($do->getOffset()) {
                $param[BuiltinKey::MODEL_LIMIT] = [
                    BuiltinKey::MODEL_LIMIT_NUMBER => $do->getLimit(),
                    BuiltinKey::MODEL_LIMIT_OFFSET => $do->getOffset()
                ];
            } else {
                $param[BuiltinKey::MODEL_LIMIT] = $do->getLimit();
            }
        }

        if ($do->isForUpdate()) {
            $param[BuiltinKey::MODEL_FOR_UPDATE] = true;
        } else {
            $cacheServiceName = self::chooseCacheService($do->getCacheService());
            if ($do->getCacheKeyRule()) {
                /**
                 * We don't check if $do->getBind() returns empty array
                 * Because empty array is allowed, for example: empty search form can lead to empty bind array
                 * So, take care of it.
                 */
                $cacheKey = self::generateCacheKeyByKVAndRule($do->getBind(), $do->getCacheKeyRule());
            } else {
                $cacheKey = $do->getCacheKey();
            }

            if ($cacheServiceName !== null && $cacheKey) {
                $param[BuiltinKey::MODEL_CACHE][BuiltinKey::MODEL_CACHE_SERVICE] = $cacheServiceName;
                $param[BuiltinKey::MODEL_CACHE][BuiltinKey::MODEL_CACHE_KEY] = static::getCacheKeyNamespace() . $cacheKey;
                if ($do->getCacheLifeTime()) {
                    $param[BuiltinKey::MODEL_CACHE][BuiltinKey::CACHE_LIFETIME] = $do->getCacheLifeTime();
                }
            }
        }

        if ($do->isSharedLock()) {
            $param[BuiltinKey::MODEL_SHARED_LOCK] = true;
        }

        return $param;
    }

    /**
     * @param array $bind
     * @param bool $selectForUpdate
     * @return array
     */
    final static private function buildParamForUK(array $bind, $selectForUpdate)
    {
        $param = [];
        $i = 0;
        $conditions = "";
        foreach ($bind as $field => $v) {
            if ($i === 0) {
                $conditions .= "$field = :$field: ";
            } else {
                $conditions .= " and $field = :$field:";
            }
            $i++;
        }
        $param[BuiltinKey::MODEL_CONDITIONS] = $conditions;
        $param[BuiltinKey::MODEL_BIND] = $bind;
        if ($selectForUpdate) {
            $param[BuiltinKey::MODEL_FOR_UPDATE] = true;
        } else {
            $cacheServiceName = self::chooseCacheService();
            if ($cacheServiceName !== null) {
                $param[BuiltinKey::MODEL_CACHE][BuiltinKey::MODEL_CACHE_SERVICE] = $cacheServiceName;
                $param[BuiltinKey::MODEL_CACHE][BuiltinKey::MODEL_CACHE_KEY] = static::getCacheKeyNamespace() .
                    self::generateCacheKeyByKV($bind, static::getUniqueKeyEncodeWay());
            }
        }
        return $param;
    }

    /**
     * @return array
     */
    static private function getNonUniqueCacheKeyRulesWithDefault()
    {
        $defaultNonUniqueCacheKeyRules = [
            self::CACHE_KEY_RULE_MIN_MAX_PK_ID => [self::CACHE_KEY_FIELD_LIST_EMPTY]
        ];
        if (!is_array(static::getNonUniqueCacheKeyRules())) {
            return $defaultNonUniqueCacheKeyRules;
        }
        return array_merge($defaultNonUniqueCacheKeyRules, static::getNonUniqueCacheKeyRules());
    }

    /**
     * @param int|string $id
     * @return bool
     */
    final static private function validatePkIdRange($id)
    {
        $cacheServiceName = self::chooseCacheService();
        if ($cacheServiceName === null) {
            return true;
        } else {
            $pkField = static::getFieldNameOfPK();
            $do = new ModelQueryDO();
            $do->setColumns("max($pkField) as max, min($pkField) as min");
            $do->setCacheKeyRule(self::CACHE_KEY_RULE_MIN_MAX_PK_ID);
            $minAndMaxId = self::findFirstUseDO($do);
            if ($id > $minAndMaxId["max"] || $id < $minAndMaxId["min"]) {
                return false;
            } else {
                return true;
            }
        }
    }
    /** ##### Private methods ##### */

    /** ##### Cache auto processing ##### */
    /**
     * Choose cache service, follow this order:
     * 1. passed by the parameter
     * 2. returned via Model::getDefaultCacheService()
     * 3. injected via DI
     * @param string $serviceName
     * @return string
     */
    final static private function chooseCacheService($serviceName = null)
    {
        if (static::isCacheDisabled()) {
            return null;
        }
        $nameList = [$serviceName, static::getDefaultCacheService(), BuiltinServiceName::DEFAULT_MODELS_CACHE];
        foreach ($nameList as $name) {
            if ($name && Di::getDefault()->has($name)
                && Di::getDefault()->get($name) instanceof BackendInterface
            ) {
                return $name;
            }
        }

        return null;
    }

    /**
     * @param string $serviceName
     * @param string $cacheKeyWithoutNamespace
     * @return bool
     */
    final static private function deleteCache($serviceName, $cacheKeyWithoutNamespace)
    {
        if ($serviceName === null) {
            return true;
        }

        $cacheKey = static::getCacheKeyNamespace() . $cacheKeyWithoutNamespace;
        /** @var BackendInterface $cacheInterface */
        $cacheInterface = Di::getDefault()->get($serviceName);
        if ($cacheInterface->get($cacheKey) !== null) {
            $cacheInterface->delete($cacheKey);
        }
        return true;
    }

    /**
     * @param array $kvArray
     * @param int $encodeWay
     * @return string
     */
    static private function encodeSortedValue($kvArray, $encodeWay)
    {
        $valueString = implode("-", array_values($kvArray));
        switch ($encodeWay) {
            case self::CACHE_KEY_ENCODE_BASE64:
                $cacheKey = StringUtil::base64EncodeWithoutSlash($valueString);
                break;
            case self::CACHE_KEY_ENCODE_CRC32:
                $cacheKey = crc32($valueString);
                break;
            case self::CACHE_KEY_ENCODE_MD5:
                $cacheKey = md5($valueString);
                break;
            case self::CACHE_KEY_ENCODE_NONE:
            default:
                $cacheKey = $valueString;
                break;
        }
        return $cacheKey;
    }

    /**
     * @param array $uniqueKeysAndValues
     * @param int $encodeWay
     * @return string
     */
    static private function generateCacheKeyByKV(array $uniqueKeysAndValues, $encodeWay)
    {
        ksort($uniqueKeysAndValues);
        return implode("-", array_keys($uniqueKeysAndValues)) . "." .
        self::encodeSortedValue($uniqueKeysAndValues, $encodeWay);
    }

    /**
     * @param array $kvArray
     * @param string $keyRule
     * @return string
     */
    static private function generateCacheKeyByKVAndRule(array $kvArray, $keyRule)
    {
        $cacheKeyRules = self::getNonUniqueCacheKeyRulesWithDefault();
        if ($cacheKeyRules[$keyRule][0] === self::CACHE_KEY_FIELD_LIST_EMPTY) {
            return $keyRule;
        } else {
            $encodeWay = self::CACHE_KEY_ENCODE_NONE;
            if (isset($cacheKeyRules[$keyRule][1])) {
                $encodeWay = $cacheKeyRules[$keyRule][1];
            }
            ksort($kvArray);
            return $keyRule . "." . self::encodeSortedValue($kvArray, $encodeWay);
        }
    }

    /**
     * process cache
     * @return bool
     */
    final private function processCache()
    {
        $cacheServiceName = self::chooseCacheService();
        if ($cacheServiceName === null) {
            return true;
        }

        $currentKvArray = $this->toArray();
        $snapshot = [];
        if ($this->getOperationMade() === parent::OP_UPDATE) {
            $snapshot = $this->getSnapshotData();
        }

        self::processCacheByUK($cacheServiceName, $currentKvArray, $snapshot);
        self::processCacheByNonUK($cacheServiceName, $currentKvArray, $snapshot);
        return true;
    }

    /**
     * process cache by unique key(s)
     * @param string $serviceName
     * @param array $currentKvArray
     * @param array $snapshot
     * @return bool
     */
    final private static function processCacheByUK($serviceName, $currentKvArray, $snapshot)
    {
        $uniqueKeys = static::getUniqueKeys();
        if (static::getFieldNameOfPK() !== null) {
            $uniqueKeys[] = static::getFieldNameOfPK();
        }

        if (!is_array($uniqueKeys)) {
            return true;
        }

        foreach ($uniqueKeys as $ukString) {
            $kv = [];
            $kvOld = [];
            foreach (explode(",", $ukString) as $field) {
                $field = trim($field);
                if (array_key_exists($field, $currentKvArray)) {
                    $kv[$field] = $currentKvArray[$field];
                }
                if (is_array($snapshot) && array_key_exists($field, $snapshot)) {
                    $kvOld[$field] = $snapshot[$field];
                }
            }
            if (count($kv)) {
                $cacheKey = self::generateCacheKeyByKV($kv, static::getUniqueKeyEncodeWay());
                self::deleteCache($serviceName, $cacheKey);
            }
            if (count($kvOld)) {
                $cacheKey = self::generateCacheKeyByKV($kvOld, static::getUniqueKeyEncodeWay());
                self::deleteCache($serviceName, $cacheKey);
            }
        }
        return true;
    }

    /**
     * process cache by unique key(s)
     * @param string $serviceName
     * @param array $currentKvArray
     * @param array $snapshot
     * @return bool
     */
    final private static function processCacheByNonUK($serviceName, $currentKvArray, $snapshot)
    {
        $keyRules = self::getNonUniqueCacheKeyRulesWithDefault();
        foreach ($keyRules as $ruleName => $keyRule) {
            $kv = [];
            $kvOld = [];
            if ($keyRule[0] === self::CACHE_KEY_FIELD_LIST_EMPTY) {
                $kv = $currentKvArray;
                $kvOld = $snapshot;
            } else {
                foreach (explode(",", $keyRule[0]) as $field) {
                    $field = trim($field);
                    if (array_key_exists($field, $currentKvArray)) {
                        $kv[$field] = $currentKvArray[$field];
                    }

                    if (is_array($snapshot) && array_key_exists($field, $snapshot)) {
                        $kvOld[$field] = $snapshot[$field];
                    }
                }
            }
            if (count($kv)) {
                $cacheKey = self::generateCacheKeyByKVAndRule($kv, $ruleName);
                self::deleteCache($serviceName, $cacheKey);
            }
            if (count($kvOld)) {
                $cacheKey = self::generateCacheKeyByKVAndRule($kvOld, $ruleName);
                self::deleteCache($serviceName, $cacheKey);
            }
        }
        return true;
    }
    /** ##### Cache auto processing ##### */
}