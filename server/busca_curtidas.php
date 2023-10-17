<?php

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./valicações/validacoes.php";

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

function busca_curtidas($body) {
    $conexao = conecta_bd();

    $id_ref = validar_number($body->id_ref);
    $tipo = validar_string($body->tipo);

    if (!$id_ref[0]) {
        resposta(400, false, $id_ref[1]);
    }
    if (!$tipo[0]) {
        resposta(400, false, $tipo[1]);
    }
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
        
    $consulta = $conexao->prepare("SELECT id, id_user, id_ref, tipo, coment FROM curtidas WHERE id_ref = :id_ref AND tipo = :tipo");
    $consulta->bindParam(':id_ref', $id_ref);
    $consulta->bindParam(':tipo', $tipo);
    $consulta->execute();
    $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

    resposta(200, true, $resultado);
    }
}
busca_curtidas($body);
?>