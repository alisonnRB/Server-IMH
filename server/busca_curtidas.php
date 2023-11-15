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
if($token == "erro"){
    resposta(401, false, "não autorizado");
}else{
   busca_curtidas($body); 
}

//validar body->id
$id = validar_int($body['id_ref']);
if ($id[0] == true){
    $id = $id[1];
} else {
resposta (401, false, $id[1]);
}


//validar body->tipo 
$tipo = validar_string($body['tipo']);
if ($tipo[0] == true){
    $tipo = $tipo[1];
} else {
resposta (401, false, $tipo[1]);
}

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