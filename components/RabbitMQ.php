<?php

namespace app\components;

use yii\base\Component;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class RabbitMQ
 *
 * @package app\components
 */
class RabbitMQ extends Component
{
    public $host;
    public $port;
    public $user;
    public $password;

    /**
     * @param $queue
     * @param array $data
     */
    public function sendMail($queue, array $data)
    {
        $connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password);
        $channel = $connection->channel();

        $channel->queue_declare($queue, false, true, false, false);

        $msg = new AMQPMessage();
        $msg->setBody(json_encode($data, JSON_UNESCAPED_UNICODE));
        $msg->set('delivery_mode', 2);

        $channel->basic_publish($msg, '', $queue);

        $channel->close();
        $connection->close();
    }
}