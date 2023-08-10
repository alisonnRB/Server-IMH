<?php
    header('Access-Control-Allow-Origin: http://localhost:3000');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Headers: *');

function resposta($codigo, $ok, $userInfo) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'userInfo' => $userInfo,
    ];

    echo(json_encode($response));
    die;
}

$conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

$body = file_get_contents('php://input');
$body = json_decode($body);

function objectInfo($conexao, $id) {
    $nome = $conexao->prepare("SELECT nome FROM usuarios WHERE id = :id");
    $nome->execute([':id' => $id]);
    $nome = $nome->fetchColumn();

    $fotoPerfil = $conexao->prepare("SELECT fotoPerfil FROM usuarios WHERE id = :id");
    $fotoPerfil->execute([':id' => $id]);
    $fotoPerfil = $fotoPerfil->fetchColumn();

    return [
        'nome' => $nome,
        'fotoPerfil' => $fotoPerfil,
    ];
}

try {
    resposta(200, true, objectInfo($conexao, $body->id));
} catch (Exception $e) {
    resposta(500, false, null);
}
?>
