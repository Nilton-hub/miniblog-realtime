<?php
require dirname(__DIR__, 1) . '/vendor/autoload.php';

// $loop   = React\EventLoop\Factory::create();
$loop = React\EventLoop\Loop::get();
$pusher = new src\push\Pusher();

// Listen for the web server to make a ZeroMQ push after an ajax request
/*
$context = new React\ZMQ\Context($loop);
$pull = $context->getSocket(ZMQ::SOCKET_PULL); // Um socket para recepção de fluxo
$pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
$pull->on('message', array($pusher, 'onBlogEntry'));
*/

/*
 * $socket = new React\Socket\SocketServer('127.0.0.1:0');
 * $socket = new React\Socket\SocketServer('127.0.0.1:8000');
 * $socket = new React\Socket\SocketServer('127.0.0.1:8000', $context);
 * $socket = new React\Socket\SocketServer('127.0.0.1:8000', $context, $loop);
 */
// $webSock = new React\Socket\Server('0.0.0.0:8080', $loop);
// Set up our WebSocket server for clients wanting real-time updates
$options = [
	"bindto" => "tcp://127.0.0.1:5555", // Usado para especificar o endereço IP (IPv4 ou IPv6) e/ou o número da porta que o PHP usará para acessar a rede. A sintaxe é ip:portpara endereços IPv4 e [ip]:portpara endereços IPv6. Definir o IP ou a porta 0permitirá que o sistema escolha o IP e/ou a porta.
 // "backlog" => 200, // Usado para limitar o número de conexões pendentes na fila de escuta do soquete.
 // "ipv6_v6only" => false, // Substitui o padrão do SO em relação ao mapeamento de IPv4 para IPv6.
 // "so_reuseport" => true, // Permite várias ligações para um mesmo par ip:port, mesmo de processos separados.,
 // "so_broadcast" => true,// Permite enviar e receber dados de/para endereços de broadcast.
 // tcp_nodelay => false // Definir esta opção para truedefinirá SOL_TCP,NO_DELAY=1 adequadamente, desativando assim o algoritmo TCP Nagle.
];
$webSock = new React\Socket\SocketServer('127.0.0.1:8080', ['tcp' => $options], $loop);  // Binding to 0.0.0.0 means remotes can connect
// $webSock->on('connection', function (React\Socket\ConnectionInterface $connection) {
//     echo 'Plaintext connection from ' . $connection->getRemoteAddress() . PHP_EOL;

//     $connection->write('hello there!' . PHP_EOL);
// });

$webServer = new Ratchet\Server\IoServer(
	new Ratchet\Http\HttpServer(
		new Ratchet\WebSocket\WsServer(
			new Ratchet\Wamp\WampServer(
				$pusher
			)
		)
	),
	$webSock
);

$loop->run();
