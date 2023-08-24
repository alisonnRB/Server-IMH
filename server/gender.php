<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

function resposta($codigo, $ok, $gender) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'gender' => $gender,
    ];

    echo(json_encode($response));
    die;
}
//! verificar id
function quaisGeneros(){
    try{
        $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

        $stmt = $conexao->prepare("SELECT id, nome FROM genero");
        $stmt->execute();
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $list = array();

        foreach ($stmt as $row) {
            $list[$row['id']] = $row['nome'];
        }

        resposta(200, true, $list);
    }catch(Exception $e){
        resposta(500, false, []);
    }
}

quaisGeneros();
?>