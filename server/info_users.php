<?php

    //!

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Headers: *');


    //TODO função que encerra as operações e enciar umas resposta para a api trabalhar
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


//TODO função que constroi a lista que armazena as informaçoes do usuario
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

//TODO tenta responder
try {
    resposta(200, true, objectInfo($conexao, $body->id));
} catch (Exception $e) {
    resposta(500, false, null);
}
?>
