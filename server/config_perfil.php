<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: *');

function resposta($codigo, $ok, $msg) {
    header('Content-Type: application/json');
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'msg' => $msg,
    ];

    echo(json_encode($response));
    die;
}

$conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

if (isset($_FILES['image']) && isset($_POST['id']) && isset($_POST['nome'])) {
    $body = $_POST;
    $pastaDestino = '../imagens/';

    $extensao = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    $nomeUnico = $body['id'] . '_' . time() . '.' . $extensao;

    $caminhoDestino = $pastaDestino . $nomeUnico;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $caminhoDestino)) {
        $stmt = $conexao->prepare('UPDATE usuarios SET nome = ?, fotoPerfil = ? WHERE id = ?');
        $stmt->execute([$body['nome'], $nomeUnico, $body['id']]);
        resposta(200, true, "Dados atualizados com sucesso.");
    } else {
        resposta(500, false, "Erro ao fazer upload do arquivo.");
    }
} else {
    resposta(400, false, "Requisição inválida.");
}
?>
