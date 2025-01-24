<?php

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/server/conexão/conexao.php';

$conexaoBD = conecta_bd();

// Certifique-se de que $conexaoBD seja uma instância do PDO
if ($conexaoBD instanceof PDO) {
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new SistemaChat($conexaoBD)
            )
        ),
        8080
    );

    // Definir cabeçalhos de CORS para aceitar conexões externas
    $server->on('beforeRequest', function ($request) {
        $request->headers->set('Access-Control-Allow-Origin', '*');
        $request->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
        $request->headers->set('Access-Control-Allow-Headers', 'Content-Type');
    });

    $server->run();
} else {
    echo "A conexão PDO não está correta.";
}
