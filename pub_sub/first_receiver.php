<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;

require_once __DIR__ . '/../vendor/autoload.php';

$conn = new AMQPStreamConnection('rabbitmq', '5672', 'root', 'root');
$channel = $conn->channel();

$channel->exchange_declare('logs', 'fanout', false, false, false);
list($queueName, ,) = $channel->queue_declare('', false, false, true, false);
$channel->queue_bind($queueName, 'logs');

$callback = function ($msg) {
    echo "[x] First receiver got message: ", $msg->body, "\n";
};

$channel->basic_consume($queueName, '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$conn->close();
