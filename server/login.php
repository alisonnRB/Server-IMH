<?php

function resposta($codigo, $ok, $msg, $userInfo, $token) {
    header('Access-Control-Allow-Origin: http://localhost:3000');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Headers: *');

    http_response_code($codigo);
    echo(json_encode([
        'ok' => $ok,
        'msg' => $msg,
        'userInfo' => $userInfo,
        'authorization' => $token,
    ]));
    die;
}

$conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

$body = file_get_contents('php://input');
$token = 'deslogado';
if (!$body){
    resposta(200, false, "corpo nao encontrado", [], $token);}


$body = json_decode($body);

if (!$body->email || !$body->senha)
    resposta(400, false, "Dados Invalidos", []);

$body->email = filter_var($body->email, FILTER_VALIDATE_EMAIL);
$body->senha = filter_var($body->senha, FILTER_SANITIZE_STRING);

function objectInfo($conexão, $email) {
    $idUser = $conexão->prepare("SELECT id FROM usuarios WHERE email = :email");
    $idUser->execute([':email' => $email]);

    $id = $idUser->fetchColumn();

    $nome = $conexão->prepare("SELECT nome FROM usuarios WHERE id = :id");
    $nome->execute([':id' => $id]);
    $nome = $nome->fetchColumn();

    $fotoPerfil = $conexão->prepare("SELECT fotoPerfil FROM usuarios WHERE id = :id");
    $fotoPerfil->execute([':id' => $id]);
    $fotoPerfil = $fotoPerfil->fetchColumn();

    return(
    $userInfo = [
        'nome' => $nome,
        'fotoPerfil' => $fotoPerfil,
        'id' => $id
    ]);
}

try {
    $consulta = $conexao->prepare("SELECT * FROM usuarios WHERE email = :email");
    $consulta->execute([':email' => $body->email]);

    if ($consulta->rowCount() === 1) {
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($body->senha === $usuario['senha']) {
            $userInfo = objectInfo($conexao, $body->email);
            $token = "logado";
            resposta(200, true, "login bem sucedido", $userInfo, $token);
        } else {
            resposta(400, false, "Senha incorreta!", [], $token);
        }
    } else {
        resposta(400, false, "Email não registrado!", [], $token);
    }
} catch (PDOException $e) {
    die("Erro na consulta: " . $e->getMessage());
}
