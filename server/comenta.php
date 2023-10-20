<?php
date_default_timezone_set('America/Sao_Paulo');

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";
include "./token/decode_token.php";


header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->id_user);
if($token == "erro"){
    resposta(401, true, "não autorizado");
}else{
    comentar($token->id,$body);
}

function comentar($id_user, $body){
    //! Verificar entrada string, filtrar e etc
    $conexao = conecta_bd();

    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
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

}
?>