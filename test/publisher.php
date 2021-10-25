<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

$exchange = 'router';

$connection = new AMQPStreamConnection('rabbitmq', '5672', 'root', 'root');
$channel = $connection->channel();

$channel->set_ack_handler(
    function (AMQPMessage $message) {
        echo 'Message acked with content ' . $message->body . PHP_EOL;
    }
);

$channel->set_nack_handler(
    function (AMQPMessage $message) {
        echo 'Message nacked with content ' . $message->body . PHP_EOL;
    }
);

$channel->set_return_listener(
    function ($replyCode, $replyText, $exchange, $routingKey, AMQPMessage $message) {
        echo 'Message returned with content ' . $message->body . PHP_EOL;
    }
);

$channel->confirm_select();

$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);


$i = 1;

while ($i <= 10) {
    $message = new AMQPMessage($i++, array('content_type' => 'text/plain'));
    $channel->basic_publish($message, $exchange, null, true);
}

$channel->wait_for_pending_acks_returns();

$channel->close();
$connection->close();
