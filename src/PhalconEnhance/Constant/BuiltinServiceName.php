<?php
/**
 * User: qinjx
 * Date: 15/3/15
 */

namespace OK\PhalconEnhance\Constant;
/**
 * Class BuiltinService
 * @see http://docs.phalconphp.com/en/latest/reference/di.html#service-name-conventions
 */
class BuiltinServiceName
{
    const DISPATCHER            = "dispatcher";
    const ROUTER                = "router";
    const URL                   = "url";
    const REQUEST               = "request";
    const RESPONSE              = "response";
    const COOKIES               = "cookies";
    const FILTER                = "filter";
    const FLASH                 = "flash";
    const FLASH_SESSION         = "flashSession";
    const SESSION               = "session";
    const EVENT_MANAGER         = "eventsManager";
    const DB                    = "db";
    const SECURITY              = "security";
    const CRYPT                 = "crypt";
    const TAG                   = "tag";
    const ESCAPER               = "escaper";
    const ANNOTATIONS           = "annotations";
    const MODELS_MANAGER        = "modelsManager";
    const MODELS_METADATA       = "modelsMetadata";
    const TRANSACTION_MANAGER   = "transactionManager";
    const MODELS_CACHE          = "modelsCache";
    const VIEW                  = "view";
    const VIEWS_CACHE           = "viewsCache";
    const MODEL                 = "model";

    /**
     * note: Non-builtin name as following:
     */
    const LOADER                = "loader";
    const DEFAULT_MODELS_CACHE  = "defaultModelsCache";
}