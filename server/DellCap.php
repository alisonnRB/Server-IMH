<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

function resposta($codigo, $ok) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
    ];

    echo(json_encode($response));
    die;
}


function verifica($body){
    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

    $consulta = $conexao->prepare('SELECT texto, nome, pronto FROM livro_publi WHERE id = :id');
    $consulta->execute([':id' => $body->id]);

    $linha = $consulta->fetch(PDO::FETCH_ASSOC);

    $caminhoPasta = '../livros/' . $body->idUser . '/' . $linha['nome'] . '_' . $body->id . '/';
    $nomeArquivo = $caminhoPasta . $body->id . '_' . $body->idUser . '_' . $body->cap . '.html';

    $texto= json_decode($linha['texto'], true);
    $public= json_decode($linha['pronto'], true);

    if(file_exists($nomeArquivo)){
        unlink($nomeArquivo);
        
    }
    if (reorderList($texto, $public, $body, $conexao)) {
        if (!empty($texto)) {
            Renomeando($caminhoPasta, $texto, $body);
        }
        resposta(200, true);
    }
}

function reorderList($texto, $public, $body, $conexao) {
    if (isset($texto[$body->cap])) {
        unset($texto[$body->cap]);
    }
    if (isset($public[$body->cap])) {
        unset($public[$body->cap]);
    }

    $texto = array_values($texto);
    $public = array_values($public);

    if(count($texto) <= 0){
        $texto = array();
    }else{
        $texto = array_combine(range(1, count($texto)), $texto);
    }

    if(count($public) <= 0){
        $public = array();
    }else{
        $public = array_combine(range(1, count($public)), $public);
    }

    $texto = json_encode($texto);
    $public = json_encode($public);

    $stmt = $conexao->prepare("UPDATE livro_publi SET texto = ?, pronto = ? WHERE id = ?");
    $stmt->execute([$texto, $public, $body->id]);
    return true;
}

function Renomeando($caminhoPasta, $texto, $body){
    $keys = count($texto);
    $contador = 0;
    for ($i = 0; $i <= $keys; $i++) {
        $nomeArquivoAntigo = $caminhoPasta . $body->id . '_' . $body->idUser . '_' . $i . '.html';
        if (file_exists($nomeArquivoAntigo)) {
            $contador++;        
            $novoNomeArquivo = $caminhoPasta . $body->id . '_' . $body->idUser . '_' . $contador . '.html';
            rename($nomeArquivoAntigo, $novoNomeArquivo);
        }
    }
}

$body = file_get_contents('php://input');
$body = json_decode($body);

verifica($body);
?>