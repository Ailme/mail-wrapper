<?php

namespace app\modules\mail\services;

use app\components\ElasticSearch;
use app\modules\mail\models\Mail;
use PhpMimeMailParser\Parser;
use Yii;

/**
 * Class ElasticMailService
 *
 * @package app\modules\mail\services
 */
class ElasticMailService
{
    /**
     * @var ElasticSearch
     */
    private $elasticsearch;

    /**
     * ElasticMailService constructor.
     *
     * @param ElasticSearch $elasticsearch
     */
    public function __construct(ElasticSearch $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    /**
     * Сохраняем данные в SD, если письмо относится к интеграции
     *
     * @param Parser $Parser
     * @param Mail $mail
     *
     * @return array
     */
    public function insert(Parser $Parser, Mail $mail)
    {
        $attachments = $Parser->getAttachments(true);

        $files = [];
        $xml = '';

        foreach ($attachments as $attachment) {
            $files[] = $attachment->getFilename();

            if (preg_match('#\.xml$#ui', $attachment->getFilename())) {
                $xml .= $attachment->getContent();
            };
        }

        $toList = array_column($Parser->getAddresses('to'), 'address');
        $toList = array_map('trim', $toList);

        $fromList = array_column($Parser->getAddresses('from'), 'address');
        $fromList = array_map('trim', $fromList);

        $data = [
            'id' => $mail->id,
            'type' => $mail->type,
            'created_at' => $mail->created_at,
            'from' => implode(', ', $fromList),
            'to' => implode(', ', $toList),
            'subject' => (string)$Parser->getHeader('subject'),
            'files' => implode(', ', $files),
            'html' => (string)$Parser->getMessageBody('html'),
            'text' => (string)$Parser->getMessageBody('text'),
            'xml' => $xml,
        ];

        /** @var \Elasticsearch\Client $client */
        $client = $this->elasticsearch->client;

        return $client->index([
            'index' => 'mail',
            'type' => 'mails',
            'id' => $data['id'],
            'body' => $data,
        ]);
    }

    /**
     * @return array
     */
    public function createIndex()
    {
        $params = [
            'index' => 'mail',
            'body' => [
                'mappings' => [
                    'mails' => [
                        'properties' => [
                            'id' => [
                                'type' => 'long',
                            ],
                            'files' => [
                                'type' => 'text',
                            ],
                            'type' => [
                                'type' => 'keyword',
                            ],
                            'from' => [
                                'type' => 'keyword',
                            ],
                            'to' => [
                                'type' => 'keyword',
                            ],
                            'subject' => [
                                'type' => 'text',
                            ],
                            'text' => [
                                'type' => 'text',
                            ],
                            'html' => [
                                'type' => 'text',
                            ],
                            'xml' => [
                                'type' => 'text',
                            ],
                            'created_at' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd HH:mm:ss',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        /** @var \Elasticsearch\Client $client */
        $client = $this->elasticsearch->client;

        return $client->indices()->create($params);
    }

    /**
     * @return array
     */
    public function deleteIndex()
    {
        /** @var \Elasticsearch\Client $client */
        $client = $this->elasticsearch->client;

        return $client->indices()->delete([
            'index' => 'mail',
        ]);
    }

    /**
     * @param int $days
     *
     * @return array
     */
    public function deleteOld($days = 14)
    {
        /** @var \Elasticsearch\Client $client */
        $client = $this->elasticsearch->client;

        return $client->deleteByQuery([
            'index' => 'mail',
            'type' => 'mails',
            'body' => [
                'query' => [
                    'range' => [
                        'created_at' => [
                            'to' => date(DATE_ATOM_1, strtotime("-$days days")),
                        ],
                    ],
                ],
            ],
        ]);
    }
}