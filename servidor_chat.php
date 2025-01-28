<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/server/conexao/conexao.php';
use Api\WebSocket\SistemaChat;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

$conexaoBD = conecta_bd();

if ($conexaoBD instanceof PDO) {
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new SistemaChat($conexaoBD)
            )
        ),
        getenv('PORT') // Porta definida pela variável de ambiente no Render
    );

    echo "Servidor WebSocket iniciado na porta " . getenv('PORT') . "\n";
    $server->run();
} else {
    echo "Falha na conexão com o banco de dados.\n";
}
