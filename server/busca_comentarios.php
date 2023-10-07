<?php

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

function resposta($codigo, $ok, $comentarios) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'comentarios' => $comentarios,
    ];

    echo(json_encode($response));
    die;
}

function busca($body){
    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

    
    $stmt = $conexao->prepare("SELECT id, id_ref, user, texto, resposta, id_resposta, tempo, conversa FROM comentarios WHERE id_ref = :id_ref AND tipo = :tipo");
    $stmt->execute([':id_ref' => $body->id, ':tipo' => $body->tipo]);
    $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);


    resposta(200, true, $stmt);
}

$body = file_get_contents('php://input');
$body = json_decode($body);

busca($body);
?>