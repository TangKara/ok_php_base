<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/11/19
 * Time: 下午2:50
 */
namespace OK\ElasticEnhance;


use Elasticsearch\Client;
use OK\ElasticEnhance\Constant\SdkArrayKey;
use OK\PhpEnhance\DomainObject\SearchResultDO;
use ONGR\ElasticsearchDSL\Highlight\Highlight;
use ONGR\ElasticsearchDSL\Search;
use Phalcon\Di;

abstract class SearchIndexBase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $indexName;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var array
     */
    protected $body;

    /** ##### Auto generated methods ##### */
    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @param string $indexName
     */
    public function setIndexName($indexName)
    {
        $this->indexName = $indexName;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param array $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
    /** ##### Auto generated methods ##### */

    /** ##### Document manipulating ##### */
    /**
     * @return bool
     */
    public function index()
    {
        if (!$this->body) {
            return false;
        }
        $param = $this->buildCommonParam();
        if ($this->id) {
            $param[SdkArrayKey::PARAM_ID] = $this->id;
        }
        $param[SdkArrayKey::PARAM_BODY] = $this->body;
        /** @noinspection BadExceptionsProcessingInspection */
        try {
            $result = $this->client->index($param);
            $this->setId($result[SdkArrayKey::RESPONSE_ID]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param int|string $id
     * @return array
     */
    public function get($id)
    {
        $param = $this->buildCommonParam();
        $param[SdkArrayKey::PARAM_ID] = $id;
        /** @noinspection BadExceptionsProcessingInspection */
        try {
            $response = $this->client->get($param);
            if ($response[SdkArrayKey::RESPONSE_FOUND]) {
                return $response[SdkArrayKey::RESPONSE_SOURCE];
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    /**
     * @todo scripted updated
     */
    public function update()
    {
        if (!$this->id) {
            return false;
        }
        if (!$this->body) {
            return false;
        }
        $param = $this->buildCommonParam();
        $param[SdkArrayKey::PARAM_ID] = $this->id;
        $param[SdkArrayKey::PARAM_BODY][SdkArrayKey::PARAM_BODY_DOC] = $this->body;
        /** @noinspection BadExceptionsProcessingInspection */
        try {
            $this->client->update($param);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if (!$this->id) {
            return false;
        }
        $param = $this->buildCommonParam();
        $param[SdkArrayKey::PARAM_ID] = $this->id;
        /** @noinspection BadExceptionsProcessingInspection */
        try {
            $this->client->delete($param);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Exact filtering, no keyword highlighting
     * @param Search $search
     * @return int
     */
    public function count(Search $search = null)
    {
        $param = $this->buildCommonParam();
        if ($search !== null) {
            $searchBody = $search->toArray();
            $param[SdkArrayKey::PARAM_BODY] = $searchBody;
        }
        $result = $this->client->count($param);
        return $result[SdkArrayKey::RESPONSE_COUNT];
    }

    /**
     * Exact filtering, no keyword highlighting
     * @param Search $search
     * @return SearchResultDO
     */
    public function filter(Search $search)
    {
        return $this->executeSearch($search);
    }

    /**
     * @param Search $search
     * @return SearchResultDO
     */
    public function search(Search $search)
    {
        if ($search->getHighlight() === null) {
            $highlight = new Highlight();
            $highlight->addField("*");
            $highlight->setTags(["<strong>"], ["</strong>"]);
            $search->addHighlight($highlight);
        }
        return $this->executeSearch($search);
    }
    /** ##### Document manipulating ##### */

    /** ##### Type mapping manipulating ##### */
    /**
     * @param string $fieldName
     * @param array $config
     * @return bool
     */
    public function putMapping($fieldName, array $config)
    {
        $param = $this->buildCommonParam();
        $param[SdkArrayKey::PARAM_BODY][SdkArrayKey::MAPPING_PROPERTIES][$fieldName] = $config;
        /** @noinspection BadExceptionsProcessingInspection */
        try {
            $result = $this->client->indices()->putMapping($param);
            return $result[SdkArrayKey::RESPONSE_ACKNOWLEDGED];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        $param = $this->buildCommonParam();
        /** @noinspection BadExceptionsProcessingInspection */
        try {
            $mappings = $this->client->indices()->getMapping($param);
            return $mappings[$this->indexName][SdkArrayKey::MAPPING][$this->type][SdkArrayKey::MAPPING_PROPERTIES];
        } catch (\Exception $e) {
            return [];
        }
    }
    /** ##### Type mapping manipulating ##### */

    /** ##### Index(database) manipulating ##### */
    /**
     * @return bool
     */
    public function createEmptyIndex()
    {
        $param[SdkArrayKey::PARAM_INDEX] = $this->indexName;
        /** @noinspection BadExceptionsProcessingInspection */
        try {
            $result = $this->client->indices()->create($param);
            return $result[SdkArrayKey::RESPONSE_ACKNOWLEDGED];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param array $config
     * @return bool
     */
    public function putIndexSetting(array $config)
    {
        $param[SdkArrayKey::PARAM_INDEX] = $this->indexName;
        $param[SdkArrayKey::PARAM_BODY] = $config;
        /** @noinspection BadExceptionsProcessingInspection */
        try {
            $result = $this->client->indices()->putSettings($param);
            return $result[SdkArrayKey::RESPONSE_ACKNOWLEDGED];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param string $text
     * @param string $field
     * @param int $size
     * @return array
     */
    public function autoComplete($text, $field, $size = null)
    {
        $size = (int)$size;
        $suggestName = $this->type . "_" . SdkArrayKey::SG;
        $param[SdkArrayKey::PARAM_INDEX] = $this->indexName;
        $param[SdkArrayKey::PARAM_BODY][$suggestName] = [
            SdkArrayKey::SG_PARAM_TEXT => $text,
            SdkArrayKey::SG_PARAM_COMPLETION => [
                SdkArrayKey::SG_PARAM_COMPLETION_FIELD => $field
            ]
        ];
        if ($size > 0) {
            $param[SdkArrayKey::PARAM_BODY][$suggestName][SdkArrayKey::SG_PARAM_COMPLETION]
            [SdkArrayKey::SG_PARAM_COMPLETION_SIZE] = $size;
        }
        /** @noinspection BadExceptionsProcessingInspection */
        try {
            $result = $this->client->suggest($param);
            return $result[$suggestName][0][SdkArrayKey::SG_RESULT_OPTIONS];
        } catch (\Exception $e) {
            return [];
        }
    }
    /** ##### Index(database) manipulating ##### */

    /**
     * the constructor
     */
    public function __construct()
    {
        $this->initialize();
    }

    protected function initialize()
    {
        $className = get_called_class();
        $this->type = substr($className, strrpos($className, "\\") + 1);
    }

    /**
     * @param string $clientServiceName
     */
    final protected function setClientServiceName($clientServiceName)
    {
        $this->client = Di::getDefault()->get($clientServiceName);
    }

    /**
     * @return array
     */
    final protected function buildCommonParam()
    {
        $param = [
            SdkArrayKey::PARAM_INDEX => $this->indexName,
            SdkArrayKey::PARAM_TYPE => $this->type
        ];
        return $param;
    }

    /**
     * @param Search $search
     * @return SearchResultDO
     */
    final protected function executeSearch(Search $search)
    {
        $searchBody = $search->toArray();
        $param = $this->buildCommonParam();
        $param[SdkArrayKey::PARAM_BODY] = $searchBody;
        $result = $this->client->search($param);

        $itemList = [];
        $highlight = [];
        foreach ($result[SdkArrayKey::SR_HITS][SdkArrayKey::SR_HITS] as $item) {
            $itemList[] = $item[SdkArrayKey::SR_HITS2_SOURCE];
            if ($search->getHighlight() !== null) {
                $highlight[$item[SdkArrayKey::SR_HITS2_ID]] = $item[SdkArrayKey::SR_HITS2_HIGHLIGHT];
            }
        }

        //return search result
        $searchResultDO = new SearchResultDO();
        $searchResultDO->setTotal($result[SdkArrayKey::SR_HITS][SdkArrayKey::SR_HITS_TOTAL]);
        $searchResultDO->setHasMore($searchResultDO->getTotal() > $search->getFrom() + count($itemList));
        $searchResultDO->setItemList($itemList);
        if (array_key_exists(SdkArrayKey::SR_AGGREGATIONS, $result)) {
            $searchResultDO->setAggregation($result[SdkArrayKey::SR_AGGREGATIONS]);
        }
        if ($search->getHighlight() !== null) {
            $searchResultDO->setHighlight($highlight);
        }
        return $searchResultDO;
    }
}