<?php

namespace app\modules\mail\forms;

use app\components\ElasticDataProvider;
use app\modules\mail\models\Mail;
use app\modules\mail\Module;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MailSearchForm represents the model behind the search form about `app\modules\mail\models\Mail`.
 */
class MailSearchForm extends Model
{
    public $id;
    public $from;
    public $to;
    public $subject;
    public $type;
    public $text;
    public $files;
    public $date_from;
    public $date_to;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            [['from', 'to', 'subject', 'text', 'files'], 'safe'],
            [['date_from', 'date_to'], 'date', 'format' => 'php:Y-m-d'],

            ['type', 'string', 'max' => 3],
            ['type', 'in', 'range' => array_keys(Mail::getTypesArray())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date_from' => Module::t('module', 'DATE_FROM'),
            'date_to' => Module::t('module', 'DATE_TO'),
            'from' => Module::t('module', 'FROM'),
            'to' => Module::t('module', 'TO'),
            'subject' => Module::t('module', 'SUBJECT'),
            'text' => Module::t('module', 'CONTENT'),
            'files' => Module::t('module', 'FILES'),
            'type' => Module::t('module', 'TYPE'),
        ];
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Mail::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');

            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
        ]);

        $query
            ->andFilterWhere(['>=', 'DATE(created_at)', $this->date_from ?: null])
            ->andFilterWhere(['<=', 'DATE(created_at)', $this->date_to ?: null]);

        $query->andFilterWhere([
            'OR',
            ['like', 'text', $this->text],
            ['like', 'html', $this->text],
            ['like', 'xml', $this->text],
        ])
            ->andFilterWhere(['like', 'from', $this->from])
            ->andFilterWhere(['like', 'to', $this->to])
            ->andFilterWhere(['like', 'subject', $this->subject]);

        switch (strtoupper($this->files)) {
            case 'YES':
                $query->andWhere(['<>', 'files', '']);
                break;
            case 'NO':
                $query->andWhere(['=', 'files', '']);
                break;
        }

        return $dataProvider;
    }

    /**
     * @param array $params
     *
     * @return ElasticDataProvider
     */
    public function searchElastic(array $params)
    {
        $this->load($params);

        /** @var \Elasticsearch\Client $client */
        $client = Yii::$app->elasticsearch->client;

        $page = $params['page'] ?? 1;
        $size = 20;

        $body = [
            'sort' => ['id' => 'desc'],
            'from' => ($page - 1) * $size,
            'size' => $size,
        ];

        if (!empty($this->id)) {
            $body['query']['bool']['must'][]['match']['id'] = $this->id;
        }

        if (!empty($this->type)) {
            $body['query']['bool']['must'][]['match']['type'] = $this->type;
        }

        if (!empty($this->from)) {
            $body['query']['bool']['filter'][]['match']['from'] = $this->from;
        }

        if (!empty($this->to)) {
            $body['query']['bool']['filter'][]['match']['to'] = $this->to;
        }

        if (!empty($this->subject)) {
            $body['query']['bool']['filter'][]['match']['subject'] = $this->subject;
        }

        if (!empty($this->text)) {
            $queryString = [
                'query_string' => [
                    'query' => $this->text,
                    'fields' => ['text', 'html', 'xml'],
                    'default_operator' => 'or'
                ]
            ];
            $body['query']['bool']['filter'][] = $queryString;
        }

        if (!empty($this->date_from)) {
            $body['query']['bool']['filter'][]['range']['created_at'] = ['gte' => $this->date_from . ' 00:00:00'];
        }

        if (!empty($this->date_to)) {
            $body['query']['bool']['filter'][]['range']['created_at'] = ['lte' => $this->date_to . ' 23:59:59'];
        }

        try {
            $result = $client->search([
                'index' => 'mail',
                'type' => 'mails',
                'body' => $body,
            ]);
        } catch (\Exception $e) {
            $result = [
                'hits' => [
                    'total' => 0,
                    'hits' => [],
                ],
            ];
        }

        $provider = new ElasticDataProvider([
            'key' => 'id',
            'hits' => $result['hits'],
            'modelClass' => 'app\modules\mail\models\Mail',
        ]);

        return $provider;
    }
}
