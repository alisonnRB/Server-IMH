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
            $conn->for = $queryData['for'];

            $visualizar = $this->conexao->prepare('UPDATE chats SET visu = 1 WHERE id_user1 = ? AND id_user2 = ?');
            $visualizar->execute([$queryData['for'], $queryData['id']]);
        }else{
            $conn->close();
        }
        
        $this->clientes->attach($conn);

        echo "Nova conexão: {$conn->resourceId} e {$conn->id} e {$conn->for}\n";
        
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $on = 0;
        $messageJSON = json_decode($msg);

        foreach ($this->clientes as $cliente) {
            if($cliente->id == $messageJSON->for && $cliente->resourceId != $from){
                if($cliente->for == $from->id){
                    echo "esse: {$cliente->from} recebeu de {$from->id}\n"; 
                    $on = 1;
                    
                    $cliente->send($msg); 
                    echo "code: {$messageJSON->code}";
                }else{
                    $messageJSON->code = 500;
                    $msg = json_encode($messageJSON);
                    $cliente->send($msg);

                    echo "code: {$messageJSON->code}";
                }
                

                echo "esse: {$cliente->id}\n";
            }
        }

        
        $data = date('Y-m-d H:i:s');
        $stmt = $this->conexao->prepare('INSERT INTO chats (id_user1, id_user2, texto, tempo, visu) VALUES (:id_user1, :id_user2, :texto, :tempo, :visu)');
        $stmt->bindParam(':id_user1', $from->id);
        $stmt->bindParam(':id_user2', $messageJSON->for);
        $stmt->bindParam(':texto', $messageJSON->message);
        $stmt->bindParam(':tempo', $data); 
        $stmt->bindParam(':visu', $on); 
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
