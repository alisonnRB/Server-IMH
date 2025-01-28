<?php

use Api\WebSocket\SistemaChat;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\WebSocket\SecureWebSocketServer; // Importar a classe SecureWebSocketServer

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/server/conexão/conexao.php';

$conexaoBD = conecta_bd();

// Certifique-se de que $conexaoBD seja uma instância do PDO
if ($conexaoBD instanceof PDO) {
    // Criação do servidor WebSocket com suporte a SSL/TLS
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new SistemaChat($conexaoBD)
            )
        ),
        8080,
        '0.0.0.0'  // Porta 8080, modifique conforme necessário
    );

    // Configuração de SSL/TLS com os arquivos de certificado
    $secureServer = new SecureWebSocketServer(
        $server,
        '/path/to/your/cert.pem',   // Caminho para o seu certificado SSL
        '/path/to/your/key.pem'     // Caminho para a chave privada do certificado
    );

    $secureServer->run();  // Executa o servidor WebSocket seguro
} else {
    echo "A conexão PDO não está correta.";
}
