<?php
include "./conexão/conexao.php";
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

$conexao = conecta_bd();

$body = file_get_contents('php://input');
$body = json_decode($body);


//TODO função que constroi a lista que armazena as informaçoes do usuario
function objectInfo($id) {
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
    $nome = $conexao->prepare("SELECT nome FROM usuarios WHERE id = :id");
    $nome->execute([':id' => $id]);
    $nome = $nome->fetchColumn();

    $fotoPerfil = $conexao->prepare("SELECT fotoPerfil FROM usuarios WHERE id = :id");
    $fotoPerfil->execute([':id' => $id]);
    $fotoPerfil = $fotoPerfil->fetchColumn();

    $genero = $conexao->prepare("SELECT genero FROM usuarios WHERE id = :id");
    $genero->execute([':id' => $id]);
    $genero = $genero->fetchColumn();

    $seguidores = $conexao->prepare("SELECT seguidores FROM usuarios WHERE id = :id");
    $seguidores->execute([':id' => $id]);
    $seguidores = $seguidores->fetchColumn();

    return [
        'id' => $id, 
        'nome' => $nome,
        'fotoPerfil' => $fotoPerfil,
        'genero' => $genero,
        'seguidores' => $seguidores,
    ];
    }
}

//TODO tenta responder
try {
    resposta(200, true, objectInfo($body->id));
} catch (Exception $e) {
    resposta(500, false, null);
}
?>
