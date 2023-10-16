<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');


function busca($body) {
    $conexao = conecta_bd();

    $consulta = $conexao->prepare("SELECT id, user_id, id_ref FROM seguidores WHERE id_ref = :id_ref AND user_id = :user_id");
    $consulta->bindParam(':user_id', $body->id);
    $consulta->bindParam(':id_ref', $body->id_ref);
    $consulta->execute();
    $seguidores = $consulta->fetchAll(PDO::FETCH_ASSOC);

    resposta(200, true, $seguidores);
}

$body = file_get_contents('php://input');
$body = json_decode($body);

busca($body);
?>