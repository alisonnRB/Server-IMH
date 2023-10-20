<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/decode_token.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');


//TODO função que encerra as operações e enciar umas resposta para a api trabalhar

$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->id);
if($token == "erro"){
    resposta(401, false, "não autorizado");
}else{
    if($body->idUser == "i"){
        resposta(200, true, objectInfo($token->id));    
    }else{
        resposta(200, true, objectInfo($body->idUser));    
    }
}

//TODO função que constroi a lista que armazena as informaçoes do usuario
function objectInfo($id) {
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
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

?>
