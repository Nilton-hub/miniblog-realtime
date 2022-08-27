<?php

namespace src\WS;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Notification implements WampServerInterface
{
    private array $subscribedTopics;

    public function __construct()
    {
        $this->subscribedTopics = [];
    }

	/**
	 * Uma chamada RPC foi recebida
	 *
	 * @param ConnectionInterface $conn
	 * @param string $id O ID exclusivo do RPC, necessário para responder a conexão
	 * @param \Ratchet\Wamp\Topic|string $topic O tópico para executar a chamada contra
	 * @param array $params Parâmetros de chamada recebidos do cliente
	 *
	 * @return mixed
	 */
	function onCall(ConnectionInterface $conn, $id, $topic, array $params): void
    {
		echo "Nova chamada" . PHP_EOL;
	}

	/**
	 * A request to subscribe to a topic has been made
     * 
	 * @param ConnectionInterface $conn
	 * @param \Ratchet\Wamp\Topic|string $topic The topic to subscribe to
	 * @return mixed
	 */
	function onSubscribe(ConnectionInterface $conn, $topic): void
    {
        $this->subscribedTopics[$topic->getId()] = $topic;
	}

    /**
     * @param string $entry JSON'ified string we'll receive from ZeroMQ or client
     */
    public function onBlogEntry($entry): void
    {
        $entryData = json_decode($entry, true);
		echo $entry . ' - ' . __METHOD__ . ' - ' . __LINE__ . PHP_EOL;
        // Se o objeto do tópico de pesquisa não estiver definido, não haverá ninguém para quem publicar
        if (!array_key_exists($entryData['topic'], $this->subscribedTopics)) {
            return;
        }

        $topic = $this->subscribedTopics[$entryData['topic']];

        // reenviar os dados para todos os clientes inscritos nessa categoria
        $topic->broadcast($entryData);
    }

	/**
	 * Uma solicitação para cancelar a inscrição de um tópico foi feita
	 *
	 * @param ConnectionInterface $conn
	 * @param \Ratchet\Wamp\Topic|string $topic The topic to unsubscribe from
	 *
	 * @return mixed
	 */
	function onUnSubscribe(ConnectionInterface $conn, $topic): void
    {
	}

	/**
	 * Um cliente está tentando publicar conteúdo em conexões assinadas em um URI
	 *
	 * @param ConnectionInterface $conn
	 * @param \Ratchet\Wamp\Topic|string $topic O tópico no qual o usuário tentou publicar
	 * @param string $event Carga útil da publicação (O texto da mensagem)
	 * @param array $exclude Uma lista de IDs de sessão da qual a mensagem deve ser excluída (lista negra)
	 * @param array $eligible Uma lista de IDs de sessão para a qual a mensagem deve ser enviada (lista branca)
	 *
	 * @return mixed
	 */
	function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible): void
    {
		// public function broadcast($msg, array $exclude = array(), array $eligible = array())
		$topic->broadcast($event, $exclude, $eligible);
	}

	/**
	 * Quando uma nova conexão for aberta ela será passada para este método
	 *
	 * @param ConnectionInterface $conn O soquete/conexão que acabou de se conectar ao seu aplicativo
	 * @return mixed
	 */
	function onOpen(ConnectionInterface $conn): void
    {
	}

	/**
	 * Isso é chamado antes ou depois de um soquete ser fechado (depende de como ele é fechado). Enviar mensagem para $conn não resultará em erro se já tiver sido fechado.
	 *
	 * @param ConnectionInterface $conn A socket/conexão que está fechando/fechada
	 * @return mixed
	 */
	function onClose(ConnectionInterface $conn): void
    {
	}

	/**
	 * Se houver um erro com um dos soquetes ou em algum lugar no aplicativo onde uma exceção é lançada,
	 * a exceção é enviada de volta para a pilha, manipulada pelo servidor e retornada ao aplicativo por meio desse método
	 *
	 * @param ConnectionInterface $conn
	 * @param \Exception $e
	 * @return mixed
	 */
	function onError(ConnectionInterface $conn, \Exception $e): void
    {
	}
}
