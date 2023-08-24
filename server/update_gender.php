<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

function resposta($codigo, $ok, $msg) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'msg' => $msg,
    ];

    echo json_encode($response);
    die;
}
//! verificar se o id é valido.
function salva($id, $selecao){
    try {
        $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

        $lista = array();
        
        foreach ($selecao as $chave => $valor) {
            if ($valor == true) {
                $lista[] = $chave;
            }
        }
        
        $lista = json_encode($lista);
        
        $stmt = $conexao->prepare('UPDATE usuarios SET genero = ? WHERE id = ?');
        $stmt->execute([$lista, $id]);
        
        resposta(200, true, 'Salvo com sucesso');
    } catch (Exception $e) {
        resposta(500, false, 'Algo deu errado :(');
    }
}

$body = file_get_contents('php://input');
$body = json_decode($body);

salva($body->id, $body->selecionados);
?>