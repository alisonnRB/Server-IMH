<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');


//TODO função que encerra as operações e enciar umas resposta para a api trabalhar


$conexao = conecta_bd();

$body = file_get_contents('php://input');
$body = json_decode($body);


//TODO função que constroi a lista que armazena as informaçoes do usuario
function objectInfo($id) {
    $conexao = conecta_bd();
    
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
        'nome' => $nome,
        'fotoPerfil' => $fotoPerfil,
        'genero' => $genero,
        'seguidores' => $seguidores,
    ];
}

//TODO tenta responder
try {
    resposta(200, true, objectInfo($body->id));
} catch (Exception $e) {
    resposta(500, false, null);
}
?>
