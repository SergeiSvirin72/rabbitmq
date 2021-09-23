<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/../vendor/autoload.php';

function fib(int $n): int {
    if ($n === 0) return 0;
    if ($n === 1) return 1;

    return fib($n - 1) + fib($n - 2);
}

$connection = new AMQPStreamConnection('rabbitmq', '5672', 'root', 'root');
$channel = $connection->channel();

$channel->queue_declare('rpc_queue', false, false, false);

echo "[x] Awaiting RPC requests\n";
$callback = function (AMQPMessage $request) {
    $n = intval($request->body);
    $result = fib($n);

    $msg = new AMQPMessage($result, [
        'correlation_id' => $request->get('correlation_id')
    ]);

    $channel = $request->getChannel();
    $queueName = $request->get('reply_to');
    $channel->basic_publish($msg, '', $queueName);

    echo '[.] fib(', $n, ")\n";

    $request->ack();
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
