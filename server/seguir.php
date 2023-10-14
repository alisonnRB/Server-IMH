<?php


header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

function resposta($codigo, $ok) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
    ];

    echo(json_encode($response));
    die;
}

function seguir($body){
    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

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


    resposta(200, true);

}

$body = file_get_contents('php://input');
$body = json_decode($body);

if($body->id_user != $body->id_ref){
    seguir($body);
}else{
    resposta(200, false);
}


?>