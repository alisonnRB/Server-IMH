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

if(!$token || $token == "erro"){
    resposta(200, false, "não autorizado");
}else{
    busca_favoritos($token->id, $body->id_ref);
}


function busca_favoritos($id_user, $id_ref){
    $conexao = conecta_bd();

    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {

        $consulta = $conexao->prepare("SELECT id, user_id, id_livro FROM favoritos WHERE id_livro = :id_livro AND user_id = :user_id");
        $consulta->bindParam(':user_id', $id_user);
        $consulta->bindParam(':id_livro', $id_ref);
        $consulta->execute();
        $favoritos = $consulta->fetchAll(PDO::FETCH_ASSOC);

        foreach ($favoritos as $favorito) {
            if($favorito['user_id'] == $id_user){
                resposta(200, true, true);   
            }
        }

        resposta(200, true, false); 
    }
}

?>