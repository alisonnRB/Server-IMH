<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/server/conexao/conexao.php';

use Api\WebSocket\SistemaChat;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

$conexaoBD = conecta_bd();

$port = getenv('PORT') ?: 8080; // Usar a variável de ambiente ou fallback para 8080 se não estiver definida.

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new SistemaChat($conexaoBD)
        )
    ),
    $port
);

echo "Servidor WebSocket rodando na porta: " . $port . "\n";
$server->run();