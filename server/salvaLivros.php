<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');


//! verificar id
function qualSave($body) {
    $conexao = conecta_bd();
    if($body->cap == 0){
        $consulta = $conexao->prepare('SELECT user_id FROM livro_publi WHERE id = :id');
        $consulta->execute([':id' => $body->id]);

        $linha = $consulta->fetch(PDO::FETCH_ASSOC);

        if($linha['user_id'] != $body->idUser){
            resposta(500, false);
        }else{
        SaveSinopse($body, $conexao);}
    }
    elseif ($body->cap >= 1) {
        PrepareCap($body, $conexao);
    }
}
function PrepareCap($body, $conexao){

    $consulta = $conexao->prepare('SELECT user_id, texto, nome FROM livro_publi WHERE id = :id');
    $consulta->execute([':id' => $body->id]);

    $linha = $consulta->fetch(PDO::FETCH_ASSOC);

    if($linha['user_id'] != $body->idUser){
        resposta(500, false);
    }else{
        if ($linha) {
        $caminhoPasta = '../livros/' . $body->idUser . '/' . $linha['nome'] . '_' . $body->id . '/';

        $titulo = json_decode($linha['texto'], true); // Decodificar JSON existente para array associativo

        // Atualizar ou adicionar o título correspondente a body->cap
        $titulo[$body->cap] = $body->titulo;

        // Codificar de volta para JSON
        $tituloJSON = json_encode($titulo);


        $stmt = $conexao->prepare("UPDATE livro_publi SET texto = ? WHERE id = ?");
        $stmt->execute([$tituloJSON, $body->id]);

        $nomeArquivo = $caminhoPasta . $body->id . '_' . $body->idUser . '_' . $body->cap . '.html';

        // Verificar se o arquivo já existe antes de criar ou atualizar
        if (file_exists($nomeArquivo)) {
            unlink($nomeArquivo); // Remover o arquivo antigo do cap
        }

        file_put_contents($nomeArquivo, $body->text); // Criar ou atualizar o arquivo

        resposta(200, true);
    } else {
        resposta(404, false);
    }
    } 
}


function SaveSinopse($body, $conexao){
    $stmt = $conexao->prepare("UPDATE livro_publi SET sinopse = ? WHERE id = ?");
    $stmt = $stmt->execute([$body->text, $body->id]);

    resposta(200, true);
}

$body = file_get_contents('php://input');

$body = json_decode($body);
    if(!isset($body->id) || empty($body->id)){
        resposta(200, false);
    }
qualSave($body);
?>