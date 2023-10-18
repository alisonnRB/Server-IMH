<?php

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

busca_curtidas($body);

function busca_curtidas($body) {
    $conexao = conecta_bd();

    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
        
    $consulta = $conexao->prepare("SELECT id, id_user, id_ref, tipo, coment FROM curtidas WHERE id_ref = :id_ref AND tipo = :tipo");
    $consulta->bindParam(':id_ref', $body->id_ref);
    $consulta->bindParam(':tipo', $body->tipo);
    $consulta->execute();
    $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

    resposta(200, true, $resultado);
    }
}

?>