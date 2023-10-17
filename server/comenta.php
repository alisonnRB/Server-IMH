<?php
date_default_timezone_set('America/Sao_Paulo');

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./valicações/validacoes.php";


header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

comentar($body);

function comentar($body){
    $conexao = conecta_bd();
    
    $id = validar_number($body->id_user);
    $tipo = validar_string($body->tipo);
    $id_ref = validar_number($body->id_ref);
    $texto = validar_string($body->texto);
    $id_resposta = validar_number($body->idResposta);
    $conversa = validar_number($body->conversa);

    if (!$id[0]) {
        resposta(400, false, $id[1]);
    }
    if (!$tipo[0]) {
        resposta(400, false, $tipo[1]);
    }
    if (!$idref[0]) {
        resposta(400, false, $idref[1]);
    }
    if (!$texto[0]) {
        resposta(400, false, $texto[1]);
    }
    if (!$idresposta[0]) {
        resposta(400, false, $idresposta[1]);
    }
    if (!$conversa[0]) {
        resposta(400, false, $conversa[1]);
    }

    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
        $data = date('Y-m-d H:i:s');

        $resposta = $body->resposta? 1 : 0;
        
    
        $stm = $conexao->prepare('INSERT INTO comentarios(user, tipo, id_ref, texto, resposta, id_resposta, tempo, conversa ) VALUES (:user, :tipo, :id_ref, :texto, :resposta, :id_resposta, :tempo, :conversa)');
        $stm->bindParam(':user', $id_user);
        $stm->bindParam(':tipo', $tipo);
        $stm->bindParam(':id_ref', $id_ref);
        $stm->bindParam(':texto', $texto);
        $stm->bindParam(':resposta', $resposta);
        $stm->bindParam(':id_resposta', $idResposta);
        $stm->bindParam(':tempo', $data);
        $stm->bindParam(':conversa', $conversa);
    
        $stm->execute();
    
        resposta(200, true, "Deu certo");
    }

}
?>