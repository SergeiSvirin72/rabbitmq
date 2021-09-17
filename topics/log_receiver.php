<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;

require_once __DIR__ . '/../vendor/autoload.php';

$conn = new AMQPStreamConnection('rabbitmq', '5672', 'root', 'root');
$channel = $conn->channel();

$channel->exchange_declare('logs', 'topic', false, false, false);
list($queueName, ,) = $channel->queue_declare('', false, false, true, false);

$routingKeys = array_slice($argv, 1);
foreach ($routingKeys as $key) {
    $channel->queue_bind($queueName, 'logs', $key);
}

$callback = function ($msg) {
    echo "[x] ", $msg->delivery_info['routing_key'], ": ", $msg->body, "\n";
};

$channel->basic_consume($queueName, '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$conn->close();
