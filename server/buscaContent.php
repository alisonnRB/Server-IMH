<?php

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

function resposta($codigo, $ok, $conteudo) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'conteudo' => $conteudo,
    ];

    echo(json_encode($response));
    die;
}

function busca($body) {
    // Construa o caminho para o arquivo HTML com base no ID do livro
    $caminho = '../livros/' . $body->id . '/' . $body->nome .'/'. $body->idLivro . '_'.$body->id .'_'. $body->cap .'.html';


    // Verifique se o arquivo existe
    if (file_exists($caminho)) {
        // Leia o conteúdo do arquivo HTML
        $conteudo = file_get_contents($caminho);

        // Envie o conteúdo como resposta
        resposta(200, true, $conteudo);
    } else {
        resposta(200, false, 'Arquivo não encontrado');
    }
}

$body = file_get_contents('php://input');
$body = json_decode($body);

busca($body);
?>
