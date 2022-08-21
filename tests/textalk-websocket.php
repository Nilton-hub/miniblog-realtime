<?php

require __DIR__ . "/../vendor/autoload.php";

$client = new WebSocket\Client("ws://127.0.0.1:8080", ['filter' => ['text']]);
//while (true) {
    try {
		$client->text('Olá');
        $message = $client->receive();
		echo $message . PHP_EOL;
        // Act on received message
        // Break while loop to stop listening
    } catch (\WebSocket\ConnectionException $e) {
        // Possibly log errors
        var_dump($e);
    }
//}
$client->close();
