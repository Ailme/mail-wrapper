<?php

namespace app\components;

use Elasticsearch\ClientBuilder;
use yii\base\Component;

/**
 * Class ElasticSearch
 *
 * @package app\components
 */
class ElasticSearch extends Component
{
    public $hosts;

    public $client;

    /**
     * ElasticSearch constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->client = ClientBuilder::create()
            ->setHosts($this->hosts)
            ->build();
    }
}
