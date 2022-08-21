<?php

require __DIR__ . "/../vendor/autoload.php";

$reactConnector = new \React\Socket\Connector([
    'dns' => '0.0.0.0', //'8.8.8.8',
    'timeout' => 10
]);
$loop = \React\EventLoop\Loop::get();
$connector = new \Ratchet\Client\Connector($loop, $reactConnector);

$connector('ws://127.0.0.1:8080', [], ['Origin' => 'http://localhost'])
->then(function(\Ratchet\Client\WebSocket $conn) {
    $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn) {
        echo "Received: {$msg}\n";
        $conn->close();
    });

    $conn->on('close', function($code = null, $reason = null) {
        echo "Connection closed ({$code} - {$reason})\n";
    });

    $conn->send('Hello World!');
}, function(\Exception $e) use ($loop) {
    echo "Não foi possível se conectar: {$e->getMessage()}\n";
    $loop->stop();
});

/*
\Ratchet\Client\connect('tcp://127.0.0.1:5555')->then(function($conn) {
    $conn->on('message', function($msg) use ($conn) {
        echo "Received: {$msg}\n";
        $conn->close();
    });

    $conn->send('Hello World!');
}, function ($e) {
    echo "Could not connect: {$e->getMessage()}\n";
});
*/