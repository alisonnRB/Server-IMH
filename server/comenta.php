<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
date_default_timezone_set('America/Sao_Paulo');

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');


function comentar($body){
    $conexao = conecta_bd();

    $data = date('Y-m-d H:i:s');

    $resposta = $body->resposta? 1 : 0;
    

    $stm = $conexao->prepare('INSERT INTO comentarios(user, tipo, id_ref, texto, resposta, id_resposta, tempo, conversa ) VALUES (:user, :tipo, :id_ref, :texto, :resposta, :id_resposta, :tempo, :conversa)');
    $stm->bindParam(':user', $body->id_user);
    $stm->bindParam(':tipo', $body->tipo);
    $stm->bindParam(':id_ref', $body->id_ref);
    $stm->bindParam(':texto', $body->texto);
    $stm->bindParam(':resposta', $resposta);
    $stm->bindParam(':id_resposta', $body->idResposta);
    $stm->bindParam(':tempo', $data);
    $stm->bindParam(':conversa', $body->conversa);

    $stm->execute();

    resposta(200, true);
}

$body = file_get_contents('php://input');
$body = json_decode($body);

comentar($body);
?>