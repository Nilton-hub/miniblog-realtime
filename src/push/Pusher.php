<?php

namespace src\push;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use \SplObjectStorage;

class Pusher implements WampServerInterface {
	protected array $subscribedTopics;
	protected SplObjectStorage $subscribers;
	
	public function __construct()
	{
		$this->subscribedTopics = [];
		$this->subscribers = new SplObjectStorage();
	}

	/**
	 *
	 */
    public function onSubscribe(ConnectionInterface $conn, $topic)
	{
		//$this->changeOutput(__METHOD__, "Nova inscrição estabelecida #{$topic->getId()}", [$conn, $topic]);
		$this->subscribedTopics[$topic->getId()] = $topic;
		$this->subscribers->attach($conn);
    }

	public function onBlogEntry($entry) {
        $entryData = json_decode($entry, true);

        // Se o objeto do tópico de pesquisa não estiver definido, não haverá ninguém para quem publicar
        if (!array_key_exists($entryData['category'], $this->subscribedTopics)) {
			$this->changeOutput(__METHOD__, "Error ao assinar o canal", [$entry]);
            return;
        }
		$this->changeOutput(__METHOD__, "Tópico assinado com sucesso", [$entry]);
        $topic = $this->subscribedTopics[$entryData['category']];

        // reenviar os dados para todos os clientes inscritos nessa categoria
        $topic->broadcast($entryData);
    }

	/**
	 *
	 */
    public function onUnSubscribe(ConnectionInterface $conn, $topic)
	{
		//$this->changeOutput(__METHOD__, "Assinatura do tópico cancelada #{$topic->getId()}", [$conn, $topic]);
    }

	/**
	 *
	 */
    public function onOpen(ConnectionInterface $conn)
	{
		//$this->changeOutput(__METHOD__, "Nova conexão aberta", [$conn]);
    }

	/**
	 *
	 */
    public function onClose(ConnectionInterface $conn)
	{
		//$this->changeOutput(__METHOD__, "Conexão encerrada", [$conn]);
    }

	/**
	 *
	 */
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
	{
		$this->changeOutput(__METHOD__, "Nova chamada", [$conn, $id, $topic, $params]);
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
	//function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible);
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
	{

		$this->changeOutput(__METHOD__, "Nova publicação", [$conn, $topic, $event, $exclude, $eligible]);
		// var_dump($topic->getSubscribers());
        // In this application if clients send data it's because the user hacked around in console
		$topic->broadcast(json_encode($event)); # envia a mensagem para o cliente
        //$conn->close();
    }

	/**
	 *
	 */
    public function onError(ConnectionInterface $conn, \Exception $e) {
    }

	public function changeOutput(string $method, string $message, ?array $params = null): void
	{
		echo "[ {$method} ] {$message}" . PHP_EOL;
		var_dump($params);
	}
}
