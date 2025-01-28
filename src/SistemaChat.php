<?php

namespace Api\WebSocket;

use Exception;
use PDO;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\MessageComponentInterface;

date_default_timezone_set('America/Sao_Paulo');

class SistemaChat implements MessageComponentInterface
{
    protected $clientes;

    public function __construct(PDO $conexao)
    {
        $this->clientes = new \SplObjectStorage;
        $this->conexao = $conexao;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $queryParams = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryParams, $queryData);

        // Verificar se os parâmetros necessários estão presentes e são válidos
        if (isset($queryData['id'], $queryData['for']) && is_numeric($queryData['id']) && is_numeric($queryData['for'])) {
            $conn->id = (int) $queryData['id'];
            $conn->for = (int) $queryData['for'];

            try {
                $visualizar = $this->conexao->prepare('UPDATE chats SET visu = 1 WHERE id_user1 = ? AND id_user2 = ?');
                $visualizar->execute([$queryData['for'], $queryData['id']]);
            } catch (Exception $e) {
                echo "Erro ao atualizar visualização: {$e->getMessage()}\n";
            }
        } else {
            echo "Conexão recusada: parâmetros inválidos.\n";
            $conn->close();
            return;
        }

        $this->clientes->attach($conn);

        echo "Nova conexão: ResourceID={$conn->resourceId}, ID={$conn->id}, For={$conn->for}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $on = 0;
        $messageJSON = json_decode($msg);

        foreach ($this->clientes as $cliente) {
            if ($cliente->id == $messageJSON->for && $cliente->for == $from->id) {
                // Cliente correspondente encontrado
                echo "Mensagem de {$from->id} entregue para {$cliente->id}\n";
                $on = 1;
                $cliente->send($msg);
            }
        }

        if ($on === 0) {
            echo "Nenhum cliente conectado para receber a mensagem de {$from->id} para {$messageJSON->for}\n";
        }

        // Registrar mensagem no banco de dados
        $data = date('Y-m-d H:i:s');
        try {
            $stmt = $this->conexao->prepare('INSERT INTO chats (id_user1, id_user2, texto, tempo, visu) VALUES (:id_user1, :id_user2, :texto, :tempo, :visu)');
            $stmt->bindParam(':id_user1', $from->id);
            $stmt->bindParam(':id_user2', $messageJSON->for);
            $stmt->bindParam(':texto', $messageJSON->message);
            $stmt->bindParam(':tempo', $data);
            $stmt->bindParam(':visu', $on);
            $stmt->execute();
        } catch (Exception $e) {
            echo "Erro ao registrar mensagem no banco de dados: {$e->getMessage()}\n";
        }

        echo "Usuário: {$from->resourceId} enviou mensagem para: {$messageJSON->for}\n";
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clientes->detach($conn);
        echo "Usuário {$conn->resourceId} desconectou\n";
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "Ocorreu um erro: {$e->getMessage()}\n";
        $conn->close();
    }
}
