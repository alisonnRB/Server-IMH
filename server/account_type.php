<?php

include "./resposta/resposta.php";
include "./conexão/conexao.php";
include "./validações/validacoes.php";
include "./token/decode_token.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->id);
if (!$token || $token == "erro") {
    resposta(200, false, "não autorizado");
} else {
    tipo($token->id);
}

function tipo($id)
{
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {
    $stmt = $conexao->prepare("SELECT tipo FROM usuarios WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $stmt = $stmt->fetch(PDO::FETCH_ASSOC);

    resposta(200, true, $stmt['tipo']);
    }
}




?>