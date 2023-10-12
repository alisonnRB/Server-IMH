<?php

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

function resposta($codigo, $ok, $curtidas) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'curtidas' => $curtidas,
    ];

    echo(json_encode($response));
    die;
}

function busca($body) {
    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

    $consulta = $conexao->prepare("SELECT id, id_user, id_ref, tipo, coment FROM curtidas WHERE id_ref = :id_ref AND tipo = :tipo");
    $consulta->bindParam(':id_ref', $body->id_ref);
    $consulta->bindParam(':tipo', $body->tipo);
    $consulta->execute();
    $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

    resposta(200, true, $resultado);
}

$body = file_get_contents('php://input');
$body = json_decode($body);

busca($body);
?>