<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;

require_once __DIR__ . '/../vendor/autoload.php';

$conn = new AMQPStreamConnection('rabbitmq', '5672', 'root', 'root');
$channel = $conn->channel();

$channel->queue_declare('hello', false, true, false, false);

$callback = function ($msg) {
    echo "[x] Received message. Sleep for : ", $msg->body, " seconds.\n";
    sleep($msg->body);
    echo "[x] Done.\n";
    $msg->ack();
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('hello', '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$conn->close();
