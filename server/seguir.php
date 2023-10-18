<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

seguir($body);

function seguir($body){
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {

    $consulta = $conexao->prepare('SELECT * FROM seguidores WHERE user_id = :id_user AND id_ref = :id_ref');
    $consulta->bindParam(':id_user', $body->id_user);
    $consulta->bindParam(':id_ref', $body->id_ref);
    $consulta->execute();
    $consulta = $consulta->fetchColumn();

    if($consulta){        
        $stmt = $conexao->prepare('DELETE FROM seguidores WHERE user_id = :id_user AND id_ref = :id_ref');
        $stmt->execute([':id_user' => $body->id_user, ':id_ref' => $body->id_ref]);

    }else{
        $stm = $conexao->prepare('INSERT INTO seguidores(user_id, id_ref) VALUES (:id_user, :id_ref)');
        $stm->bindParam(':id_user', $body->id_user);
        $stm->bindParam(':id_ref', $body->id_ref);
        $stm->execute();
    }

    resposta(200, true, "certo");

}
}



?>