<?php

namespace RabbitMQ;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Publisher
{
    protected AMQPStreamConnection $connection;
    protected AMQPChannel $channel;
    protected string $exchange;

    public function __construct(string $exchange, string $type)
    {
        $this->exchange = $exchange;

        $this->connection = new AMQPStreamConnection('rabbitmq', '5672', 'root', 'root');
        $this->channel = $this->connection->channel();

        $this->channel->exchange_declare($this->exchange, $type, false, false, true);
    }

    public function publish(string $data = '', string $routingKey = '')
    {
        $msg = new AMQPMessage($data);
        $this->channel->basic_publish($msg, $this->exchange, $routingKey);
    }
}
