<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/11/19
 * Time: 下午5:47
 */

namespace OK\ElasticEnhance\Constant;


class SdkArrayKey
{
    //param key
    const PARAM_INDEX           	= "index";
    const PARAM_TYPE            	= "type";
    const PARAM_ID              	= "id";
    const PARAM_CONSISTENCY     	= "consistency";
    const PARAM_PARENT          	= "parent";
    const PARAM_PERCOLATE       	= "percolate";
    const PARAM_REFRESH         	= "refresh";
    const PARAM_REPLICATION     	= "replication";
    const PARAM_ROUTING         	= "routing";
    const PARAM_SIZE                = "size";
    const PARAM_TIMEOUT         	= "timeout";
    const PARAM_TIMESTAMP       	= "timestamp";
    const PARAM_TTL             	= "ttl";
    const PARAM_VERSION         	= "version";
    const PARAM_VERSION_TYPE    	= "version_type";
    const PARAM_BODY            	= "body";
    const PARAM_BODY_DOC        	= "doc";
    const PARAM_BODY_SCRIPT     	= "script";
    const PARAM_BODY_PARAMS     	= "params";
    const PARAM_BODY_UPSERT     	= "upsert";

    //response key
    const RESPONSE_ACKNOWLEDGED 	= "acknowledged";
    const RESPONSE_INDEX        	= "_index";
    const RESPONSE_TYPE         	= "_type";
    const RESPONSE_ID           	= "_id";
    const RESPONSE_VERSION      	= "_version";
    const RESPONSE_FOUND        	= "found";
    const RESPONSE_FIELDS       	= "fields";
    const RESPONSE_SOURCE       	= "_source";
    const RESPONSE_COUNT       	    = "count";

    //search result key, HITS2 =[hits][hits]
    const SR_AGGREGATIONS           = "aggregations";
    const SR_AGGREGATIONS_PREFIX    = "agg_";
    const SR_AGGREGATIONS_BUCKETS   = "buckets";
    const SR_AGGREGATIONS_KEY       = "key";
    const SR_HITS               	= "hits";
    const SR_HITS_TOTAL         	= "total";
    const SR_HITS_MAX_SCORE     	= "max_score";
    const SR_HITS2_ID           	= "_id";
    const SR_HITS2_SOURCE       	= "_source";
    const SR_HITS2_HIGHLIGHT    	= "highlight";

    //index setting
    const IS_MAX_RESULT_WINDOW      = "max_result_window";
    const IS_QUERY                  = "query";
    const IS_BOOL                   = "bool";
    const IS_MAX_CLAUSE_COUNT       = "max_clause_count";
    
    //mapping key
    const MAPPING               	= "mappings";
    const MAPPING_PROPERTIES    	= "properties";
    //mapping property key
    const M_PROP_K_TYPE         	= "type";
    const M_PROP_K_INDEX        	= "index";
    const M_PROP_K_ANALYZER         = "analyzer";
    const M_PROP_K_SEARCH_ANALYZER  = "search_analyzer";
    const M_PROP_K_PAYLOADS         = "payloads";
    //mapping property value
    const M_PROP_V_STRING       	= "string";
    const M_PROP_V_COMPLETION   	= "completion";
    const M_PROP_V_SIMPLE   	    = "simple";
    const M_PROP_V_NO_ANALYZE   	= "not_analyzed";
    const M_PROP_V_NO_NESTED        = "nested";

    //suggester key
    const SG                        = "suggest";
    const SG_PARAM_TEXT             = "text";
    const SG_PARAM_COMPLETION       = "completion";
    const SG_PARAM_COMPLETION_FIELD = "field";
    const SG_PARAM_COMPLETION_SIZE  = "size";
    const SG_RESULT_OPTIONS         = "options";

    //special doc key
    const COMPLETION_INPUT          = "input";//for suggest field indexing
}