<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/server/conexao/conexao.php';

use Api\WebSocket\SistemaChat;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

$conexaoBD = conecta_bd();

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new SistemaChat($conexaoBD)
        )
    ),
    8080
);

$server->run();