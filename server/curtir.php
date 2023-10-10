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

function comentar($body){
    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

    $consulta = $conexao->prepare('SELECT * FROM curtidas WHERE id_user = :id_user AND id_ref = :id_ref AND tipo = :tipo AND coment = :coment');
    $consulta->bindParam(':id_user', $body->id_user);
    $consulta->bindParam(':id_ref', $body->id_ref);
    $consulta->bindParam(':tipo', $body->tipo);
    $consulta->bindParam(':coment', $body->coment);
    $consulta->execute();
    $consulta = $consulta->fetchColumn();

    if($consulta){        
        $stmt = $conexao->prepare('DELETE FROM curtidas WHERE id_user = :id_user AND id_ref = :id_ref AND tipo = :tipo AND coment = :coment');
        $stmt->execute([':id_user' => $body->id_user, ':id_ref' => $body->id_ref, ':tipo'=> $body->tipo, ':coment' => $body->coment]);
    }else{
        $stm = $conexao->prepare('INSERT INTO curtidas(id_user, id_ref, tipo, coment) VALUES (:id_user, :id_ref, :tipo, :coment)');
        $stm->bindParam(':id_user', $body->id_user);
        $stm->bindParam(':id_ref', $body->id_ref);
        $stm->bindParam(':tipo', $body->tipo);
        $stm->bindParam(':coment', $body->coment);
        $stm->execute();
    }


    resposta(200, true);

}

$body = file_get_contents('php://input');
$body = json_decode($body);

comentar($body);
?>