<?php
require __DIR__ . "/../vendor/autoload.php";

// client
$connector = new React\Socket\Connector();

$connector->connect('tcp://127.0.0.1:8080')
    ->then(function (React\Socket\ConnectionInterface $connection) {
        // $connection->pipe(new React\Stream\WritableResourceStream(STDOUT));
        $connection->write("Hello World!\n");
        $connection->on('data', function ($chunk) {
            echo $chunk;
        });
}, function (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});

/*
// server
$connector->connect('127.0.0.1:8080')
    ->then(function (React\Socket\ConnectionInterface $connection) {
        $connection->pipe(new React\Stream\WritableResourceStream(STDOUT));
        // $connection->on('data', function ($chunk) {
        //     echo $chunk;
        // });
        echo "Linha " . __LINE__ . " nova conexão" . PHP_EOL;
}, function (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});
*/
