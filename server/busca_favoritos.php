<?php

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

function resposta($codigo, $ok, $favoritos) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'favoritos' => $favoritos,
    ];

    echo(json_encode($response));
    die;
}

function busca($body) {
    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

    $consulta = $conexao->prepare("SELECT id, user_id, id_livro FROM favoritos WHERE id_livro = :id_ref AND user_id = :user_id");
    $consulta->bindParam(':user_id', $body->id);
    $consulta->bindParam(':id_ref', $body->id_ref);
    $consulta->execute();
    $favoritos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    resposta(200, true, $favoritos);
}

$body = file_get_contents('php://input');
$body = json_decode($body);

busca($body);
?>