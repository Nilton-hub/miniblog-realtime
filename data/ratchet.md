# Enviar para um site existente

## Aprimore seu trabalho existente sem alterações generalizadas.

## O problema

Os tutoriais anteriores eram legais e tudo mais, mas o foco deles era criar um aplicativo de longa duração onde os usuários interagissem uns com os outros inteiramente por meio de WebSockets sem persistência. Isso é muito trabalho para incorporar em um site já existente. O código precisaria ser portado do seu repositório para um novo aplicativo Ratchet. Uma fase de teste totalmente nova precisaria acontecer para garantir que as páginas anteriormente funcionais ainda funcionem.

## Meta¶
Quando um usuário, seja você mesmo em seu administrador ou um usuário postando um comentário em seu blog, faz um POST por meio de um envio de formulário ou AJAX, queremos que essa alteração seja imediatamente enviada a todos os outros visitantes dessa página. Adicionaremos atualizações em tempo real ao nosso site sem interromper sua base de código ou afetar sua estabilidade atual.

Para este tutorial, vamos fingir (leia: código clichê ausente) que você está publicando um artigo de blog em seu site e os visitantes verão a história aparecer assim que você publicá-la.

## Arquitetura de rede

<http://socketo.me/assets/img/push-1.png>

- Passo 1

Um cliente faz uma solicitação e recebe uma resposta do servidor web e renderiza a página. Em seguida, ele estabelece uma conexão WebSocket aberta (os clientes 2 e 3 fazem a mesma coisa).

- Passo 2

O cliente 1 faz um POST back, por meio de envio de formulário ou AJAX, para o servidor web. (Observe a conexão WebSocket ainda aberta)

- Passo 3

Enquanto o servidor está processando a solicitação POST (salvando no banco de dados, etc), ele envia uma mensagem diretamente para a pilha WebSocket com um transporte ZeroMQ.

- Passo 4

A pilha WebSocket manipula a mensagem ZeroMQ e a envia para os clientes apropriados por meio de conexões WebSocket abertas. Os navegadores da Web lidam com a mensagem recebida e atualizam a página da Web com Javascript de acordo.

----------------------------------------------------------------------

Esse fluxo de trabalho é discreto e fácil de introduzir em sites existentes. As únicas alterações no site são adicionar um pouco de ZeroMQ ao servidor e um arquivo Javascript no cliente para manipular as mensagens recebidas do servidor WebSocket.

# Requisitos

## ZeroMQ
<https://www.php.net/manual/pt_BR/class.zmqcontext.php>

Para se comunicar com um script em execução, ele precisa estar escutando em um soquete aberto. Nosso aplicativo estará escutando a porta 8080 para conexões WebSocket de entrada... mas como ele também obterá atualizações de outro script PHP? Digite ZeroMQ . Poderíamos usar soquetes brutos, como os que o Ratchet é construído, mas o ZeroMQ é uma biblioteca que apenas facilita os soquetes.

ZeroMQ é uma biblioteca (libzmq) que você precisará instalar, bem como uma extensão PECL para ligações PHP. A instalação é fácil e é fornecida para muitos sistemas operacionais em seu site .

## React/ZMQ
Ratchet é uma biblioteca WebSocket construída sobre uma biblioteca de sockets chamada React . React lida com conexões e E/S bruta para Ratchet. Além do React, que vem com o Ratchet, precisamos de outra biblioteca que faz parte da suíte React: React/ZMQ. Essa biblioteca vinculará os soquetes ZeroMQ ao núcleo do Reactor, permitindo lidar com os soquetes WebSockets e ZeroMQ. Para instalar, seu arquivo composer.json deve ficar assim:

```json
{
    "autoload": {
        "psr-4": {
            "MyApp\\": "src"
        }
    },
    "require": {
        "cboden/ratchet": "0.4.*",
        "react/zmq": "0.2.*|0.3.*"
    }
}
```

# Comece sua codificação ¶

Let's get to some code! We'll start by stubbing out our class application. We're going to useWAMP for its ease of use with the Pub/Sub pattern. This will allow clients to subscribe to updates on a specific page and we'll only push updates to those who have subscribed.

```php
<?php
namespace MyApp;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface {
    public function onSubscribe(ConnectionInterface $conn, $topic) {
    }
    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
    }
    public function onOpen(ConnectionInterface $conn) {
    }
    public function onClose(ConnectionInterface $conn) {
    }
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }
    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}
```

## Editando o envio do seu blog¶
Em seguida, adicionaremos um pouco de mágica do ZeroMQ ao código do seu site existente, onde você lida com uma nova postagem no blog. O código aqui pode ser um pouco básico e arcaico em comparação com a arquitetura avançada do seu blog atual, sentado no Drupal ou WordPress, mas estamos focando nos fundamentos.



```php
Salve isso em /src/MyApp/Pusher.php. Acabamos de criar os métodos necessários para o WAMP e garantimos que ninguém tente enviar dados, fechando a conexão se o fizerem. Estamos fazendo um aplicativo push e não aceitamos nenhuma mensagem de entrada de WebSockets, todas elas virão de AJAX.

<?php
    // post.php ???
    // Isso tudo estava aqui antes ;)
    $entryData = array(
        'category' => $_POST['category']
      , 'title'    => $_POST['title']
      , 'article'  => $_POST['article']
      , 'when'     => time()
    );

    $pdo->prepare("INSERT INTO blogs (title, article, category, published) VALUES (?, ?, ?, ?)")
        ->execute($entryData['title'], $entryData['article'], $entryData['category'], $entryData['when']);

    // Este é o nosso novo material
    $context = new ZMQContext(); // Cria um novo objeto ZMQContext
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher'); // Define o tipo (push/pull) e o id do socket (my pusher)
    $socket->connect("tcp://localhost:5555"); // Conecta-se ao dns de destino. No caso, um servidor na própria máquina na porta 5555

    $socket->send(json_encode($entryData)); // Envia a mensagem ao servidor de ws
```

Depois que registramos sua entrada de blog no banco de dados, abrimos uma conexão ZeroMQ com nosso servidor de soquete e entregamos uma mensagem serializada com as mesmas informações. (nota: por favor, faça a higienização adequada, este é apenas um exemplo rápido e sujo)

## Manipulando mensagens no ZeroMQ
Vamos voltar para nossa classe de stub do aplicativo. Como deixamos, ele estava apenas lidando com conexões WebSocket. Como você viu em nosso último trecho de código, abrimos uma conexão com localhost na porta 5555 para a qual enviamos dados. Vamos adicionar manipulação para essa mensagem ZeroMQ, bem como reenviá-la para nossos clientes WebSocket.

```php
<?php
namespace MyApp;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface {
    /**
     * Uma pesquisa de todos os tópicos em que os clientes se inscreveram
     */
    protected $subscribedTopics = array();

    public function onSubscribe(ConnectionInterface $conn, $topic) {
        $this->subscribedTopics[$topic->getId()] = $topic;
    }

    /**
     * @param string String JSON'ified que receberemos do ZeroMQ
     */
    public function onBlogEntry($entry) {
        $entryData = json_decode($entry, true);

        // Se o objeto do tópico de pesquisa não estiver definido, não haverá ninguém para quem publicar
        if (!array_key_exists($entryData['category'], $this->subscribedTopics)) {
            return;
        }

        $topic = $this->subscribedTopics[$entryData['category']];

        // reenviar os dados para todos os clientes inscritos nessa categoria
        $topic->broadcast($entryData);
    }

    /* O resto dos nossos métodos estavam como estavam, omitidos dos documentos para economizar espaço */
}
```

## Amarrando tudo junto - Criando nosso executável ¶
Até agora, abordamos toda a lógica de envio, recebimento e tratamento de mensagens. Agora, vamos juntar tudo e criar nosso script executável que gerencia tudo. Vamos construir nosso aplicativo Ratchet com componentes de E/S, WebSockets, Wamp e ZeroMQ e executar o loop de eventos.

```php
<?php
    require dirname(__DIR__) . '/vendor/autoload.php';

    $loop   = React\EventLoop\Factory::create();
    $pusher = new MyApp\Pusher;

    // Escute o servidor web para fazer um push do ZeroMQ após uma solicitação ajax
    $context = new React\ZMQ\Context($loop);
    $pull = $context->getSocket(ZMQ::SOCKET_PULL);
    $pull->bind('tcp://127.0.0.1:5555'); // Vincular a 127.0.0.1 significa que o único cliente que pode se conectar é ele mesmo
    $pull->on('message', array($pusher, 'onBlogEntry'));

    // Configure nosso servidor WebSocket para clientes que desejam atualizações em tempo real
    $webSock = new React\Socket\Server('0.0.0.0:8080', $loop); // Vincular a 0.0.0.0 significa que os controles remotos podem se conectar
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
```

Salve o código como  /bin/push-server.php e execute-o:

```
$ php bin/push-server.php
```

## Lado do cliente - Obtendo atualizações em tempo real ¶

Agora que nosso código do lado do servidor está completo e funcionando, é hora de obter essas postagens em tempo real! O que você faz com essas atualizações especificamente está além do escopo deste documento, vamos apenas colocar essas mensagens no console de depuração.

```js
<script src="https://gist.githubusercontent.com/cboden/fcae978cfc016d506639c5241f94e772/raw/e974ce895df527c83b8e010124a034cfcf6c9f4b/autobahn.js"></script>
<script>
    var conn = new ab.Session('ws://localhost:8080',
        function() {
            conn.subscribe('categoriaGatinhos', function(topic, data) {
                // É aqui que você adicionaria o novo artigo ao DOM (além do escopo deste tutorial)
                console.log('Novo artigo publicado na categoria "' + topic + '" : ' + data.title);
            });
        },
        function() {
            console.warn('Conexão WebSocket fechada');
        },
        {'skipSubprotocolCheck': true} // pularVerificacaodeSubprotocolo
    );
</script>
```

Finalmente, abra a página em que você colocou este Javascript em uma janela do navegador e de outro navegador poste uma entrada de blog para "kittensCategory" e observe o log do seu console desde o primeiro. Uma vez que está funcionando, seus próximos passos são incorporar os dados recebidos em alguma manipulação do DOM.

Quando isso está funcionando localmente (assumindo que localhost foi seu ambiente de desenvolvimento), você pode alterar as referências localhost e possivelmente as ligações para seus nomes de host/endereços IP do servidor apropriados.
