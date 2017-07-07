<?php

namespace app\components;

use yii\base\Model;
use yii\data\BaseDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class ElasticDataProvider
 *
 * @package app\components
 */
class ElasticDataProvider extends BaseDataProvider
{
    /**
     * @var string|callable the column that is used as the key of the data models.
     * This can be either a column name, or a callable that returns the key value of a given data model.
     * If this is not set, the index of the [[models]] array will be used.
     * @see getKeys()
     */
    public $key;
    /**
     * @var string the name of the [[\yii\base\Model|Model]] class that will be represented.
     * This property is used to get columns' names.
     * @since 2.0.9
     */
    public $modelClass;

    /**
     * @var array
     */
    public $hits;

    /**
     * @return mixed
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * @param mixed $hits
     */
    public function setHits($hits)
    {
        $this->hits = $hits;
    }

    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {
        $models = [];

        for ($i = 0, $n = sizeof($this->hits['hits']); $i < $n; $i++) {
            /** @var Model $model */
            $model = new $this->modelClass($this->hits['hits'][$i]['_source']);
            $models[] = $model;
        }

        if (($sort = $this->getSort()) !== false) {
            $models = $this->sortModels($models, $sort);
        }

        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
        }

        return $models;
    }

    /**
     * @inheritdoc
     */
    protected function prepareKeys($models)
    {
        if ($this->key !== null) {
            $keys = [];
            foreach ($models as $model) {
                if (is_string($this->key)) {
                    $keys[] = $model[$this->key];
                } else {
                    $keys[] = call_user_func($this->key, $model);
                }
            }

            return $keys;
        } else {
            return array_keys($models);
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareTotalCount()
    {
        return $this->hits['total'];
    }

    /**
     * Sorts the data models according to the given sort definition
     *
     * @param array $models the models to be sorted
     * @param Sort $sort the sort definition
     *
     * @return array the sorted data models
     */
    protected function sortModels($models, $sort)
    {
        $orders = $sort->getOrders();
        if (!empty($orders)) {
            ArrayHelper::multisort($models, array_keys($orders), array_values($orders));
        }

        return $models;
    }
}