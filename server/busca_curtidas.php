<?php

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";
include "./token/decode_token.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

$id_ref = validar_int($body->id_ref);
if ($id_ref[0] == true) {
    $id_ref = $id_ref[1];
} else {
    resposta(200, false, $id_ref[1]);
}

//validar body->tipo 
$tipo = validar_string($body->tipo);
if ($tipo[0] == true) {
    $tipo = $tipo[1];
} else {
    resposta(200, false, $tipo[1]);
}

$token = decode_token($body->id);
if (!$token || $token == "erro") {
    resposta(200, false, "não autorizado");
} else {
    busca_curtidas($id_ref, $tipo);
}

function busca_curtidas($id, $tipo)
{
    $conexao = conecta_bd();

    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {

        $consulta = $conexao->prepare("SELECT id, id_user, id_ref, tipo, coment FROM curtidas WHERE id_ref = :id_ref AND tipo = :tipo");
        $consulta->bindParam(':id_ref', $id_ref);
        $consulta->bindParam(':tipo', $tipo);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

        resposta(200, true, $resultado);
    }
}

?>