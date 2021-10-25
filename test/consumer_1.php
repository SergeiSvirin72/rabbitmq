<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

$exchange = 'router';
$queue = 'msgs';
$consumerTag = 'consumer';

$connection = new AMQPStreamConnection('rabbitmq', '5672', 'root', 'root');
$channel = $connection->channel();

$channel->queue_declare($queue, false, true, false, false);
$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);
$channel->queue_bind($queue, $exchange);

function process_message(AMQPMessage $message) {
    echo "Received: ";
    if ($message->body == 'good') {
        echo "ack.\n";
        $message->ack();
    } else {
        echo "nack.\n";
        $message->nack();
    }
}

$channel->basic_consume($queue, $consumerTag, false, false, false, false, 'process_message');

while ($channel->is_consuming()) {
    $channel->wait();
}
