<?php

use PhpAmqpLib\Exchange\AMQPExchangeType;
use RabbitMQ\Publisher;

require_once __DIR__ . '/vendor/autoload.php';

$publisher = null;
while (!$publisher) {
    try {
        $publisher = new Publisher('test.exchange', AMQPExchangeType::FANOUT);
    } catch (Exception $e) {
        echo "[x] RabbitMQ is composing up\n";
        sleep(3);
    }
}

while (true) {
    $msg = 'Hello!';
    $publisher->publish($msg);
    echo "[x] Published: ", $msg, "\n";
    sleep(3);
}
