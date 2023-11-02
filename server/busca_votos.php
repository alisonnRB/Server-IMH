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
$token = decode_token($body->id_user);

if($token == "erro"){
    resposta(401, false, "não autorizado");
}else{
    busca_voos($token->id, $body->id_ref);
}

function busca_voos($id_user, $id_ref){
    $conexao = conecta_bd();

    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
        $consulta = $conexao->prepare("SELECT user_id FROM votacao WHERE id_ref = :id_ref AND user_id = :user_id");
        $consulta->bindParam(':user_id', $id_user);
        $consulta->bindParam(':id_ref', $id_ref);
        $consulta->execute();
        $votos = $consulta->fetchAll(PDO::FETCH_ASSOC);

        foreach ($votos as $voto) {
            if($voto['user_id'] == $id_user){
             resposta(200, true, true);   
            }
        }

        resposta(200, true, false); 
    }
}



?>