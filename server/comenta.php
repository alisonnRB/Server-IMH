<?php

date_default_timezone_set('America/Sao_Paulo');

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

    $data = date('Y-m-d H:i:s');

    $resposta = $body->resposta? 1 : 0;
    

    $stm = $conexao->prepare('INSERT INTO comentarios(user, tipo, id_ref, texto, resposta, id_resposta, tempo ) VALUES (:user, :tipo, :id_ref, :texto, :resposta, :id_resposta, :tempo)');
    $stm->bindParam(':user', $body->id_user);
    $stm->bindParam(':tipo', $body->tipo);
    $stm->bindParam(':id_ref', $body->id_ref);
    $stm->bindParam(':texto', $body->texto);
    $stm->bindParam(':resposta', $resposta);
    $stm->bindParam(':id_resposta', $body->idResposta);
    $stm->bindParam(':tempo', $data);

    $stm->execute();

    resposta(200, true);
}

$body = file_get_contents('php://input');
$body = json_decode($body);

comentar($body);
?>