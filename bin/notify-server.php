<?php

require __DIR__ . "/../vendor/autoload.php";

$loop = React\EventLoop\Loop::get();
$notify = new src\WS\Notification();

// $webSock = new React\Socket\Server('0.0.0.0:8080', $loop);
$webSock = new React\Socket\SocketServer('127.0.0.1:8080');

// Configure nosso servidor WebSocket para clientes que desejam atualizações em tempo real
$webServer = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            new Ratchet\Wamp\WampServer(
                $notify
            )
        )
    ),
    $webSock
);

$loop->run();
