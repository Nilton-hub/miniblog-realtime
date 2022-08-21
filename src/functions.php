<?php

/**
 * @var array $data
 * @var string $socketServer
 * @var array $filter
 * @return \WebSocket\ConnectionException|string
 */
function wsClient(array $data, string $socketServer = "ws://localhost:3000/", array $filter = ['filter' => ['text']])
{
	$client = new WebSocket\Client($socketServer, ['filter' => ['text']]);
	try {
		$client->text(json_encode($data));
        $message = $client->receive();
		return $message . PHP_EOL;
	} catch (\WebSocket\ConnectionException $e) {
		$client->close();
		return $e->getMessage();
	}
}
