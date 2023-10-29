<?php
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
    resposta(401, false, "não autorizado");
}else{
    busca_amigos($token->id);
}

function busca_amigos($id_user){
    $conexao = conecta_bd();

    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
        $consulta = $conexao->prepare("SELECT id, user_id, id_ref FROM seguidores WHERE user_id = :user_id");
        $consulta->bindParam(':user_id', $id_user);
        $consulta->execute();
        $seguidores = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $list = new stdClass();

        $i = 0;
        foreach ($seguidores as $seguidor) {
            if($seguidor['user_id'] == $id_user){
                $stm = $conexao->prepare("SELECT id, user_id, id_ref FROM seguidores WHERE id_ref = :id_ref AND user_id = :user_id");
                $stm->bindParam(':user_id', $seguidor['id_ref']);
                $stm->bindParam(':id_ref', $seguidor['user_id'] );
                $stm->execute();
                $amigos = $stm->fetchAll(PDO::FETCH_ASSOC); 

                $list->{$i} = $amigos;
                $i++;
            }
        } 
        resposta(200, true, $list);  
    }
    resposta(200, true, false); 
}



?>