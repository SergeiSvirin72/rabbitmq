<?php

use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use RabbitMQ\Consumer;

require_once __DIR__ . '/vendor/autoload.php';

$callback = function (AMQPMessage $msg) {
    echo "[x] Consumed: ", $msg->body, "\n";
};

$consumer = null;
while (!$consumer) {
    try {
        $consumer = new Consumer('test.exchange', AMQPExchangeType::FANOUT, '', $callback);
    } catch (Exception $e) {
        echo "[x] RabbitMQ is composing up\n";
        sleep(3);
    }
}

echo "[x] Start consuming.\n";
$consumer->consume();
