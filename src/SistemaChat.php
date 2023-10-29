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
        $this->clientes->attach($conn);

        echo "Nova conexão: {$conn->resourceId}\n\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        foreach ($this->clientes as $cliente) {
            if ($from !== $cliente) {
                $cliente->send($msg);
            }
        }

        echo "Usuário: {$from->resourceId}\n\n";
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
