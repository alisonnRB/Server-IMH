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

function visu($body){
    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");


    $stmt = $conexao->prepare('UPDATE livro_publi SET visus = visus + 1 WHERE id = ?');
    $stmt->execute([$body->id]);


    resposta(200, true);

}

$body = file_get_contents('php://input');
$body = json_decode($body);


visu($body);



?>