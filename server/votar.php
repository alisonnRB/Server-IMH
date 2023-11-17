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
    votar($token->id, $body);
}

function votar($id_user, $body){
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {
        $consulta = $conexao->prepare('SELECT * FROM votacao WHERE user_id = :id_user AND id_ref = :id_ref');
        $consulta->bindParam(':id_user', $id_user);
        $consulta->bindParam(':id_ref', $body->id_ref);
        $consulta->execute();
        $consulta = $consulta->fetchColumn();

        if(!$consulta){        
            $stm = $conexao->prepare('INSERT INTO votacao(user_id, id_ref, chave) VALUES (:id_user, :id_ref, :chave)');
            $stm->bindParam(':id_user', $id_user);
            $stm->bindParam(':id_ref', $body->id_ref);
            $stm->bindParam(':chave', $body->chave);
            $stm->execute();
            
            resposta(200, true, "certo");
        }
        resposta(200, false, "ja votou");

    }
}


?>