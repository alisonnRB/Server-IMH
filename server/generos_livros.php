<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

function resposta($codigo, $ok, $generos) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'generos' => $generos,
    ];

    echo(json_encode($response));
    die;
}
//! verificar id
function quaisGeneros($body) {

    try {
        $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

        $stmt = $conexao->prepare("SELECT genero FROM livro_publi WHERE nome = :nome");
        $stmt->execute([':nome' => $body->nome]);
        $generosIds = json_decode($stmt->fetchColumn()); // Decodifica a string JSON em um array de IDs

        // Busca os nomes dos gêneros no banco de dados a partir dos IDs
        $list = array();
        if($generosIds){
        foreach ($generosIds as $id) {
            $stmt = $conexao->prepare("SELECT nome FROM genero WHERE id = :id");
            $stmt->execute([':id' => $id + 1]);
            $generoNome = $stmt->fetchColumn();
            $list[$id] = $generoNome;
        }
        }

        resposta(200, true, $list);
    } catch (Exception $e) {
        resposta(500, false, ['...']);
    }
}

$body = file_get_contents('php://input');
$body = json_decode($body);
    if(!isset($body->nome) || empty($body->nome)){
        resposta(200, false, ['...']);
    }
quaisGeneros($body);
?>