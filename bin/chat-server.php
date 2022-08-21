<?php
require dirname(__DIR__, 1) . '/vendor/autoload.php';
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use src\WS\Products;

$serverPdt = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Products()
        )
    ),
    3000
);

/*
$serverChat = IoServer::factory(
    new HttpServer(
        new WsServer(
            new src\WS\Chat()
        )
    ),
    8080
);

$serverChat->run();
*/
$serverPdt->run();
