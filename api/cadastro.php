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