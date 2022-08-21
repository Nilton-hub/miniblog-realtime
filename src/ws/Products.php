<?php

namespace src\WS;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use \SplObjectStorage;
use \Exception;

class Products implements MessageComponentInterface
{
    protected SplObjectStorage $clients;

    public function __construct()
	{
        $this->clients = new SplObjectStorage();
    }

    /**
     * Armazena a nova conexão para enviar mensagens posteriormente
     * @var ConnectionInterface
     */
    public function onOpen (ConnectionInterface $conn): void
    {
        $this->clients->attach($conn);
        //echo "Nova conexão aceita! ({$conn->resourceId})" . PHP_EOL;
    }

    /**
     * Envia as mensagens para a(s) conexão(ões) de destino
     * @var ConnectionInterface $from
     * @var mixed $msg
     */
    public function onMessage (ConnectionInterface $from, $msg): void
    {
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // O remetente não é o destinatário, envia para cada cliente conectado
                $client->send((string)$msg);
            }
        }
    }

    /**
     * A conexão está fechada, remova-a, pois não podemos mais enviar mensagens
     * @var ConnectionInterface $conn
     */
    public function onClose (ConnectionInterface $conn): void
    {
		//echo "Conexão encerrada: ({$conn->resourceId})" . PHP_EOL;
        $this->clients->detach($conn);
    }

    /**
     * Executado quando algum erro ocorre
     * @var ConnectionInterface $conn
     * @var Exception $e
     * @return
     */
    public function onError (ConnectionInterface $conn , Exception $e): void
    {
        $conn->close();
    }
}
