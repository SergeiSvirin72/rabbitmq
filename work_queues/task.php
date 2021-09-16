<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/../vendor/autoload.php';

$conn = new AMQPStreamConnection('rabbitmq', '5672', 'root', 'root');
$channel = $conn->channel();

$channel->queue_declare('hello', false, true, false, false);

$timeToSleep = isset($argv[1]) ? intval($argv[1]) : 0;
$msg = new AMQPMessage(
    $timeToSleep,
    ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
);

$channel->basic_publish($msg, '', 'hello');

$channel->close();
$conn->close();
