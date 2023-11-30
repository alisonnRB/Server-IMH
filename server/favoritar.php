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
    Favoritar($token->id, $body->id_ref);
}

function Favoritar($id_user, $id_ref){
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {

        $consulta = $conexao->prepare('SELECT * FROM favoritos WHERE user_id = :id_user AND id_livro = :id_ref');
        $consulta->bindParam(':id_user', $id_user);
        $consulta->bindParam(':id_ref', $id_ref);
        $consulta->execute();
        $consulta = $consulta->fetchColumn();

        if($consulta){        
            $stmt = $conexao->prepare('DELETE FROM favoritos WHERE user_id = :id_user AND id_livro = :id_ref');
            $stmt->execute([':id_user' => $id_user, ':id_ref' => $id_ref]);
        }else{
            $stm = $conexao->prepare('INSERT INTO favoritos(user_id, id_livro, visu) VALUES (:id_user, :id_ref, 1)');
            $stm->bindParam(':id_user', $id_user);
            $stm->bindParam(':id_ref', $id_ref);
            $stm->execute();
        }
        resposta(200, true, "certo");

    }
}


?>