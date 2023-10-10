<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

//TODO função que encerra as operações e enciar umas resposta para a api trabalhar
function resposta($codigo, $ok, $msg, $livros) {
    http_response_code($codigo);
    header('Content-Type: application/json');

    $response = [
        'ok' => $ok,
        'msg' => $msg,
        'livros' => $livros,
    ];

    echo(json_encode($response));
    die;
}

//TODO recebe os inputs da api
$body = file_get_contents('php://input');
$body = json_decode($body);

function quaisLivros($body){
    try{
    if (isset($body->id) || !empty($body->id)){
        $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

        $stmt = $conexao->prepare("SELECT id, user_id, nome, imagem, genero, sinopse, classificacao, curtidas FROM livro_publi WHERE user_id = :id");
        $stmt->execute([':id' => $body->id]);
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);


        resposta(200, true, "deu certo", $stmt);

    }}catch (Exception $e) {
        resposta(500, false, null, null);
    }
}




quaisLivros($body);
?>