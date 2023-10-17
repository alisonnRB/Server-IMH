<?php

include "./resposta/resposta.php";
include "./valicações/validacoes.php";

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

function busca_conteudos($body){

    $id = validar_number($body->id);
    $nome = validar_string($body->nome);
    $idLivro = validar_number($body->idLivro);
    $cap = validar_number($body->cap);
   
    if (!$id[0]) {
        resposta(400, false, $id[1]);
    }
    if (!$nome[0]) {
        resposta(400, false, $nome[1]);
    }
    if (!$idLivro[0]) {
        resposta(400, false, $idLivro[1]);
    }
    if (!$cap[0]) {
        resposta(400, false, $cap[1]);
    }
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
        // Construa o caminho para o arquivo HTML com base no ID do livro
        $caminho = '../livros/' . $id . '/' . $nome . '_' . $idLivro .'/'. $idLivro . '_'. $id .'_'. $cap .'.html';

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
}
busca_conteudos($body);
?>
