<?php

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/../vendor/autoload.php';

class FibonacciRpcClient
{
    protected AMQPStreamConnection $connection;
    protected AMQPChannel $channel;
    protected string $callbackQueue;
    protected ?string $response;
    protected string $correlationId;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection('rabbitmq', '5672', 'root', 'root');
        $this->channel = $this->connection->channel();
        list($this->callbackQueue, ,) = $this->channel->queue_declare('', false, false, true, false);
        $this->channel->basic_consume($this->callbackQueue, '', false, true, false, false, [$this, 'onResponse']);
    }

    public function onResponse(AMQPMessage $response)
    {
        if ($response->get('correlation_id') === $this->correlationId) {
            $this->response = $response->body;
        }
    }

    public function call(int $n): ?int
    {
        $this->response = null;
        $this->correlationId = uniqid();

        $message = new AMQPMessage($n, [
            'correlation_id' => $this->correlationId,
            'reply_to' => $this->callbackQueue,
        ]);

        $this->channel->basic_publish($message, '', 'rpc_queue');
        while (!$this->response) {
            $this->channel->wait();
        }

        return intval($this->response);
    }
}



