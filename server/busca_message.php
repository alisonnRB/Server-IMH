<?php

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";
include "./token/decode_token.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->id);
if(!$token || $token == "erro"){
    resposta(200, false, "não autorizado");
}else{
   busca_msgs($token->id, $body->id_ref); 
}

function busca_msgs($mine, $for) {
    $conexao = conecta_bd();

    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {
        
        $consulta = $conexao->prepare("SELECT id, id_user1, id_user2, texto FROM chats WHERE (id_user1 = :id_user1 AND id_user2 = :id_user2) OR (id_user1 = :id_user2 AND id_user2 = :id_user1) ORDER BY tempo ASC");
        $consulta->bindParam(':id_user1', $mine);
        $consulta->bindParam(':id_user2', $for);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

    resposta(200, true, $resultado);
    }
}

?>