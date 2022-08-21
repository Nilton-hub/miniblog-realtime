<?php

require __DIR__ . "/../vendor/autoload.php";

$client = new WebSocket\Client("ws://localhost:3000/", ['filter' => ['text']]);
//while (true) {
    try {
		$client->text(json_encode(['name' => 'Tênis Olimpicus', 'price' =>  '240,90']));
        $message = $client->receive();
		echo $message . PHP_EOL;
        // Act on received message
        // Break while loop to stop listening
    } catch (\WebSocket\ConnectionException $e) {
        // Possibly log errors
    }
//}
$client->close();
