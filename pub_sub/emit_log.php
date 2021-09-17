<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/../vendor/autoload.php';

$conn = new AMQPStreamConnection('rabbitmq', '5672', 'root', 'root');
$channel = $conn->channel();

$channel->exchange_declare('logs', 'direct', false, false, false);

$messageSeverity  = $argv[1] ? strtolower($argv[1]) : 'info';
$messageBody = (string)$argv[2] ?? '';

$msg = new AMQPMessage($messageBody);

$channel->basic_publish($msg, 'logs', $messageSeverity);

echo "[x] Sent ", $messageSeverity, ': ', $messageBody, "\n";

$channel->close();
$conn->close();
