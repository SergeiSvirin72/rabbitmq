<?php

require_once 'FibonacciRpcClient.php';

$fibonacciRpcClient = new FibonacciRpcClient();
$n = isset($argv[1]) ? intval($argv[1]) : 0;
$response = $fibonacciRpcClient->call($n);
echo '[.] Got ', $response, "\n";
