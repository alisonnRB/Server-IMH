<?php

function resposta($codigo, $ok, $msg){
    
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

http_response_code($codigo);
echo(json_encode([
    'ok'=> $ok,
    'msg'=> $msg
]));
die;

}
if($_SERVER['REQUEST_METHOD']=="OPTIONS"){
    resposta(200, true, '');
}

if($_SERVER['REQUEST_METHOD']!="POST")
    resposta(400, false, "metodo Invalido");


$conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

$body = file_get_contents('php://input');

if(!$body)
    resposta(200, false, "corpo não encontrado");

$body = json_decode($body);

if (empty($body->nome) && empty($body->email) && empty($body->senha)){
    resposta(200, false, "Você deve preencher os campos");
}

if (empty($body->nome)){
    resposta(200, false, "Preencha o campo do Nome");
}

if (empty($body->email)){
    resposta(200, false, "Preencha o campo do E-mail");
}

if (empty($body->senha)){
    resposta(200, false, "Preencha o campo da Senha");
}

if (!preg_match('/^[a-zA-Z0-9]/', $body->nome)) {
    resposta(200, false, "nome com caracteres inválidos");
}

$consulta = $conexao->prepare('SELECT email FROM usuarios WHERE email = :email');

$consulta->execute([':email' => $body->email]);
$consulta = $consulta->fetchColumn();

if ($body->email == $consulta){
    resposta(200, false, "Email já cadastrado");
}



$body->nome = filter_var($body->nome,FILTER_SANITIZE_STRING);
$body->email = filter_var($body->email,FILTER_VALIDATE_EMAIL);
$body->senha = filter_var($body->senha,FILTER_SANITIZE_STRING);

if(!$body->nome ||!$body->email || !$body->senha)
    resposta(400, false, "Dados Invalidos");


$stm = $conexao->prepare('INSERT INTO usuarios(nome, email, senha) VALUES (:nome, :email, :senha)');
$stm->bindParam('nome', $body->nome);
$stm->bindParam('email', $body->email);
$stm->bindParam('senha', $body->senha);
$stm->execute();

resposta(200,true,'msg salvA');

?>