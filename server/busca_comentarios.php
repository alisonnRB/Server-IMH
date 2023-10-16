<?php
include "./resposta/resposta.php";
include "./conexão/conexao.php";
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');



function busca($body){
    $conexao = conecta_bd();

    
    $stmt = $conexao->prepare("SELECT id, id_ref, user, texto, resposta, id_resposta, tempo, conversa, curtidas FROM comentarios WHERE id_ref = :id_ref AND tipo = :tipo");
    $stmt->execute([':id_ref' => $body->id, ':tipo' => $body->tipo]);
    $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);


    resposta(200, true, $stmt);
}

$body = file_get_contents('php://input');
$body = json_decode($body);

busca($body);
?>