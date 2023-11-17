<?php
date_default_timezone_set('America/Sao_Paulo');
//! mexer validação
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";
include "./token/decode_token.php";


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->id_user);
if(!$token || $token == "erro"){
    resposta(200, true, "não autorizado");
}else{
    comentar($token->id,$body);
}

function comentar($id_user, $body){
    //! Verificar entrada string, filtrar e etc
    $conexao = conecta_bd();

    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {
        $data = date('Y-m-d H:i:s');

        $resposta = $body->resposta? 1 : 0;
        
    
        $stm = $conexao->prepare('INSERT INTO comentarios(user, tipo, id_ref, texto, resposta, id_resposta, tempo, conversa ) VALUES (:user, :tipo, :id_ref, :texto, :resposta, :id_resposta, :tempo, :conversa)');
        $stm->bindParam(':user', $id_user);
        $stm->bindParam(':tipo', $body->tipo);
        $stm->bindParam(':id_ref', $body->id_ref);
        $stm->bindParam(':texto', $body->texto);
        $stm->bindParam(':resposta', $resposta);
        $stm->bindParam(':id_resposta', $body->idResposta);
        $stm->bindParam(':tempo', $data);
        $stm->bindParam(':conversa', $body->conversa);
    
        $stm->execute();
    
        resposta(200, true, "Deu certo");
    }
    
    //! validações prontas
    /*//validar body->id
        $id_user = validar_int($body->id_user);
        if ($id_user[0] == true) {
            $id_user = $id_user[1];
        } else {
            resposta(200, false, $id_user[1]);
        }

        //validar body->tipo 
        $tipo = validar_string($body->tipo);
        if ($tipo[0] == true) {
            $tipo = $tipo[1];
        } else {
            resposta(200, false, $tipo[1]);
        }

        //validar id ref
        $id_ref = validar_int($body->id_ref);
        if ($id_ref[0] == true) {
            $id_ref = $id_ref[1];
        } else {
            resposta(200, false, $id_ref[1]);
        }
        
        //validar texto
        $texto = validar_string($body->texto);
        if ($texto[0] == true) {
            $texto = $texto[1];
        } else {
            resposta(200, false, $texto[1]);
        }

        //validar resposta
        $resposta = validar_string($body->resposta);
        if ($resposta[0] == true) {
            $resposta = $resposta[1];
        } else {
            resposta(200, false, $resposta[1]);
        }

        //validar id resposta 
        $id_resposta = validar_int($body->id_resposta);
        if ($id_resposta[0] == true) {
            $id_resposta = $id_resposta[1];
        } else {
            resposta(200, false, $id_resposta[1]);
        }

        $conversa = validar_string($body->conversa);
        if ($conversa[0] == true) {
            $conversa = $conversa[1];
        } else {
                resposta(200, false, $conversa[1]);
        }*/
}
?>
