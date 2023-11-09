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
if($token == "erro"){
    resposta(401, false, "não autorizado");
}else{
    busca_enquete($body->id_ref);
}


function busca_enquete($id_ref) {
    // Verificação da conexão
    $conexao = conecta_bd();

    $stm = $conexao->prepare('SELECT votos FROM enquete WHERE id = :id');
    $stm->bindParam(':id', $id_ref);
    $stm->execute();
    $votes = $stm->fetchAll(PDO::FETCH_ASSOC);


    resposta(200, true, $votes[0]['votos']);
}




?>