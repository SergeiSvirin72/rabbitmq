<?php

namespace RabbitMQ;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Consumer
{
    protected AMQPStreamConnection $connection;
    protected AMQPChannel $channel;
    protected string $exchange;

    public function __construct(string $exchange, string $type, string $routingKey, callable $callback)
    {
        $this->exchange = $exchange;

        $this->connection = new AMQPStreamConnection('rabbitmq', '5672', 'root', 'root');
        $this->channel = $this->connection->channel();

        $this->channel->exchange_declare($this->exchange, $type, false, false, true);

        list($queueName, ,) = $this->channel->queue_declare('', false, false, false, true);
        $this->channel->queue_bind($queueName, $this->exchange, $routingKey);

        $this->channel->basic_consume($queueName, '', false, true, false, false, $callback);
    }

    public function consume()
    {
        while ($this->channel->is_open()) {
            $this->channel->wait();
        }
    }
}
