<?php
    
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

//TODO função que encerra as operações e enciar umas resposta para a api trabalhar
function resposta($codigo, $ok, $msg, $id, $token) {
    
    http_response_code($codigo);
    echo(json_encode([
        'ok' => $ok,
        'msg' => $msg,
        'id' => $id,
        'authorization' => $token,
    ]));
    die;
}

function verifica($body, $token){
    //? verifica se estão vazios
    if (empty($body->email) && empty($body->senha)){
        resposta(200, false, "Você deve preencher os campos", 'x', $token);
    }

    if (empty($body->email)){
        resposta(200, false, "Preencha o campo do e-mail", 'x', $token);
    }

    if (empty($body->senha)){
        resposta(200, false, "Preencha o campo da senha", 'x', $token);
    }

    consulta($body, $token);
}

function consulta($body, $token){

    $body->email = filter_var($body->email, FILTER_VALIDATE_EMAIL);
    $body->senha = filter_var($body->senha, FILTER_SANITIZE_STRING);

    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

    //? acessa o email do input
    $consulta = $conexao->prepare("SELECT * FROM usuarios WHERE email = :email");
    $consulta->execute([':email' => $body->email]);

    if ($consulta->rowCount() === 1) {

        //? guarda as informaçoes da consulta como lista
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        //? verifica se senha e email coicidem
        if ($body->senha === $usuario['senha']) {
            $idUser = $conexao->prepare("SELECT id FROM usuarios WHERE email = :email");
            $idUser->execute([':email' => $body->email]);
        
            $id = $idUser->fetchColumn();

            $token = geraToken();

            resposta(200, true, "login bem sucedido", $id, $token);
        }else{
            resposta(400, false, "Senha incorreta", 'x', $token);
        }
    }else{
        resposta(400, false, "Email não registrado!", 'x', $token);
    }
}
//!
function geraToken(){
    //! CRIAR SISTEMA DE TOKEN
    return 'logado';
}

$body = file_get_contents('php://input');
$token = 'deslogado';
$body = json_decode($body);

verifica($body, $token);
?>