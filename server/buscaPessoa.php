<?php
date_default_timezone_set('America/Sao_Paulo');

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

// Função que encerra as operações e envia uma resposta para a API trabalhar
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

// Recebe os inputs da API
$body = file_get_contents('php://input');
$body = json_decode($body);


function BuscaPessoa($search, $params){
    try {
        $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

        $sql = "SELECT id, nome, fotoPerfil FROM usuarios WHERE $search";
        $stmt = $conexao->prepare($sql);
        $stmt->execute($params);
        $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        resposta(200, true, "deu certo", $livros);
    } catch (Exception $e) {
        resposta(500, false, null, null);
    }
}

BuscaPessoa($body);
?>