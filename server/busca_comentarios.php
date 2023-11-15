<?php

include "./resposta/resposta.php";
include "./conexão/conexao.php";
include "./validações/validacoes.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);


busca_comentarios($body);

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

function busca_comentarios($body){
    $conexao = conecta_bd();

    
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
        $stmt = $conexao->prepare("SELECT id, id_ref, user, texto, resposta, id_resposta, tempo, conversa, curtidas FROM comentarios WHERE id_ref = :id_ref AND tipo = :tipo");
        $stmt->execute([':id_ref' => $body->id, ':tipo' => $body->tipo]);
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

        resposta(200, true, $stmt);
    }
}

?>