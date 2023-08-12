<?php
    
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Headers: *');

    //TODO função que encerra as operações e enciar umas resposta para a api trabalhar
function resposta($codigo, $ok, $msg, $userInfo, $token) {
    
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



$body = json_decode($body);

//? verifica se estão vazios
if (empty($body->email) && empty($body->senha)){
    resposta(200, false, "Você deve preencher os campos", [], $token);
}

if (empty($body->email)){
    resposta(200, false, "Preencha o campo do e-mail", [], $token);
}

if (empty($body->senha)){
    resposta(200, false, "Preencha o campo da senha", [], $token);
}

//?verifica tipos
$body->email = filter_var($body->email, FILTER_VALIDATE_EMAIL);
$body->senha = filter_var($body->senha, FILTER_SANITIZE_STRING);

//TODO função que busca o id do usuario
function objectInfo($conexão, $email) {
    $idUser = $conexão->prepare("SELECT id FROM usuarios WHERE email = :email");
    $idUser->execute([':email' => $email]);

    $id = $idUser->fetchColumn();


    return(
    $userInfo = [
        'id' => $id
    ]);
}

//? tenta verificar e lida com o erro
try {
    //? acessa o email do input
    $consulta = $conexao->prepare("SELECT * FROM usuarios WHERE email = :email");
    $consulta->execute([':email' => $body->email]);

    //? pega a consulta e verifica se existe apenas um
    if ($consulta->rowCount() === 1) {

        //? guarda as informaçoes da consulta como lista
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        //? verifica se senha e email coicidem
        if ($body->senha === $usuario['senha']) {

            //? monta a resposta
            $userInfo = objectInfo($conexao, $body->email);

            //! NECESARIO CONTRUIR O SISTEMA DE TOKEN DE MANEIRA SEGURA
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
