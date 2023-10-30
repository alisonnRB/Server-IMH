<?php



namespace Api\WebSocket;

use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\MessageComponentInterface;


class SistemaChat implements MessageComponentInterface {
    protected $clientes;

    public function __construct() {
        $this->clientes = new \SplObjectStorage;
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
        $a = json_decode($msg);
        foreach ($this->clientes as $cliente) {
            if($cliente->id == $a->for && $cliente->resourceId != $from){
                $cliente->send($msg); 

                echo "esse: {$cliente->id}\n";
                break;
            }
        }

        echo "Usuário: {$from->resourceId} mandou: {$msg} para: {$a->for}\n\n";
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
