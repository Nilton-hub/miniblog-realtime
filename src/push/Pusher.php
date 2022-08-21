<?php

namespace src\push;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface {
	protected array $subscribedTopics;
	
	public function __construct()
	{
		$this->subscribedTopics = [];
	}
	
	/**
	 *
	 */
    public function onSubscribe(ConnectionInterface $conn, $topic)
	{
		$this->subscribedTopics[$topic->getId()] = $topic;
    }

	/**
	 *
	 */
    public function onUnSubscribe(ConnectionInterface $conn, $topic)
	{
    }

	/**
	 *
	 */
    public function onOpen(ConnectionInterface $conn)
	{
    }
	
	/**
	 *
	 */
    public function onClose(ConnectionInterface $conn)
	{
    }

	/**
	 *
	 */
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
	{
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }

	/**
	 * @var ConnectionInterface $conn
	 * @var $topic Canal ws em da onde veio a requisição (obj com o método toString())
	 * @var array $event contém o corpo da mensagem
	 * @var array $exclude
	 * @var array $eligible
	 */
    public function onPublish(ConnectionInterface $conn, string $topic, array $event, array $exclude, array $eligible)
	{
        // In this application if clients send data it's because the user hacked around in console
		$topic->broadcast(json_encode($event)); # envia a mensagem para o cliente
        $conn->close();
    }

	/**
	 *
	 */
    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}
