<?php
/**
 * Created by IntelliJ IDEA.
 * User: qinjx
 * Date: 15/4/3
 * Time: 16:31
 */

namespace OK\PhalconEnhance\Constant;


class BuiltinKey
{
    /** ##### Cache ##### */
    const CACHE_LIFETIME        = "lifetime";

    /**
     * Memcache / Libmemcached Backend
     */
    const CACHE_HOST            = "host";
    const CACHE_PORT            = "port";
    const CACHE_PERSISTENT      = "persistent";
    /**
     * @default _PHCM
     * statsKey means: phalcon will store all your keys into an item (named by statsKey)
     * I strongly recommend you set it to null to prevent phalcon doing this, except you really need it.
     * @see https://github.com/phalcon/cphalcon/blob/master/phalcon/cache/backend/libmemcached.zep, __construct()
     */
    const CACHE_STATS_KEY       = "statsKey";

    /**
     * File Backend
     */
    const CACHE_DIR             = "cacheDir";
    const CACHE_KEY_PREFIX      = "prefix";
    /**
     * @default false
     * safekey means: auto hashing (md5) for key, checking unsafe filesystem characters in the prefix
     * @see https://github.com/phalcon/cphalcon/blob/master/phalcon/cache/backend/file.zep __construct()
     */
    const CACHE_SAFE_KEY        = "safekey";

    /**
     * CLI
     */
    const CLI_TASK              = "task";
    const CLI_ACTION            = "action";
    const CLI_PARAMS            = "params";

    /** ##### DB ##### */
    const DB_ADAPTER            = "adapter";
    const DB_HOST               = "host";
    const DB_USERNAME           = "username";
    const DB_PASSWORD           = "password";
    const DB_NAME               = "dbname";
    const DB_CHARSET            = "charset";

    /** ##### Meta Data ##### */
    const META_DATA_DIR         = "metaDataDir";

    /** ##### Model Query ##### */
    const MODEL_COLUMN          = "column";
    const MODEL_COLUMNS         = "columns";
    const MODEL_DISTINCT_COLUMN = "distinct";
    const MODEL_CONDITIONS      = "conditions";
    const MODEL_BIND            = "bind";
    const MODEL_ORDER           = "order";
    const MODEL_GROUP           = "group";
    const MODEL_LIMIT           = "limit";
    const MODEL_LIMIT_NUMBER    = "number";
    const MODEL_LIMIT_OFFSET    = "offset";
    const MODEL_CACHE           = "cache";
    const MODEL_CACHE_KEY       = "key";
    const MODEL_CACHE_SERVICE   = "service";
    const MODEL_FOR_UPDATE      = "for_update";
    const MODEL_SHARED_LOCK     = "shared_lock";

    /** ##### Queue ##### */
    //Config
    const QUEUE_HOST            = "host";
    const QUEUE_PORT            = "port";
    //Option
    const QUEUE_PRIORITY        = "priority";
    const QUEUE_DELAY           = "delay";
    const QUEUE_TTR             = "ttr";

    /** ##### Validator ##### */
    const VALIDATOR_MIN_VALUE   = "minimum";
    const VALIDATOR_MAX_VALUE   = "maximum";
    const VALIDATOR_MIN_LENGTH  = "min";
    const VALIDATOR_MAX_LENGTH  = "max";
    const VALIDATOR_RE_PATTERN  = "pattern";
    const VALIDATOR_SET_DOMAIN  = "domain";
    const VALIDATOR_CANCEL      = "cancelOnFail";
    const VALIDATOR_MESSAGE     = "message";
}