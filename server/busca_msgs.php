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
    busca_visus($token->id);
}


//! verificações e validações
function busca_visus($id){

    $conexao = conecta_bd();

    $visus = $conexao->prepare('SELECT count(*) AS Num FROM chats WHERE visu = 0 AND id_user2 = :id_user2');
    $visus->bindParam(':id_user2', $id);
    $visus->execute();
    $visus = $visus->fetch(PDO::FETCH_ASSOC);

    resposta(200, true, $visus);

}




?>