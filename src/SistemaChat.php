<?php



namespace Api\WebSocket;

use Exception;
use PDO;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\MessageComponentInterface;

date_default_timezone_set('America/Sao_Paulo');

class SistemaChat implements MessageComponentInterface {
    protected $clientes;

    public function __construct(PDO $conexao) {
        $this->clientes = new \SplObjectStorage;
        $this->conexao = $conexao;
    }

    public function onOpen(ConnectionInterface $conn) {
        $queryParams = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryParams, $queryData);
        if (isset($queryData['id'])) {
            $conn->id = $queryData['id'];
        }else{
            $conn->close();
        }
        
        $this->clientes->attach($conn);

        echo "Nova conexão: {$conn->resourceId} e {$conn->id}\n";
        
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $messageJSON = json_decode($msg);
        foreach ($this->clientes as $cliente) {
            if($cliente->id == $messageJSON->for && $cliente->resourceId != $from){
                $cliente->send($msg); 

                echo "esse: {$cliente->id}\n";
                break;
            }
        }

        $data = date('Y-m-d H:i:s');
        $stmt = $this->conexao->prepare('INSERT INTO chats (id_user1, id_user2, texto, tempo) VALUES (:id_user1, :id_user2, :texto, :tempo)');
        $stmt->bindParam(':id_user1', $from->id);
        $stmt->bindParam(':id_user2', $messageJSON->for);
        $stmt->bindParam(':texto', $messageJSON->message);
        $stmt->bindParam(':tempo', $data); 
        $stmt->execute();
        
        echo "Usuário: {$from->resourceId} mandou: {$msg} para: {$messageJSON->for}\n\n";
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clientes->detach($conn);

        echo "Usuário {$conn->resourceId} desconectou\n\n";
    }

    public function onError(ConnectionInterface $conn, Exception $e) {
        $conn->close();

        echo "Ocorreu um erro: {$e->getMessage()}\n\n";
    }
}
