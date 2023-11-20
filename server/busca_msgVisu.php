<?php

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/decode_token.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');


$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->id);
if(!$token || $token == "erro"){
    resposta(200, false, "não autorizado");
}else{
    busca_visus($token->id, $body->id_ref);
}


//! verificações e validações
function busca_visus($id, $ref){

    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    }else{

    $visus = $conexao->prepare('SELECT count(*) AS New FROM chats WHERE visu = 0 AND id_user2 = :id_user2 AND id_user1 = :id_user1');
    $visus->bindParam(':id_user2', $id);
    $visus->bindParam(':id_user1', $ref);
    $id_user = validar_int($ref);
    if ($id_user[0] == true) {
        $id_user = $id_user[1];
    } else {
        resposta(200, false, $id_user[1]);
    }
    $visus->execute();
    $visus = $visus->fetch(PDO::FETCH_ASSOC);

  

    resposta(200, true, $visus);

    }
}




?>