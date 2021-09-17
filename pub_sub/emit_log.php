<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/../vendor/autoload.php';

$conn = new AMQPStreamConnection('rabbitmq', '5672', 'root', 'root');
$channel = $conn->channel();

$channel->exchange_declare('logs', 'fanout', false, false, false);

$messageBody = (string)$argv[1] ?? '';
$msg = new AMQPMessage(
    $messageBody,
    ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
);

$channel->basic_publish($msg, 'logs');

echo "[x] Sent ", $messageBody, "\n";

$channel->close();
$conn->close();
