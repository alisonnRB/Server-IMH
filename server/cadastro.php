<?php
//* faz o cadastro dos usuarios

//TODO função que encerra as operações e enciar umas resposta para a api trabalhar
function resposta($codigo, $ok, $msg){
    
header('Access-Control-Allow-Origin: *');
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


//TODO faz a conexão com BD
$conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

//TODO recebe os inputs da api
$body = file_get_contents('php://input');

//?verifica a existencia
if(!$body)
    resposta(200, false, "corpo não encontrado");

//? decodifica o json para um objeto php
$body = json_decode($body);

//? verifica se os campus estão preenchidos
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

if (empty($body->confSenha)){
    resposta(200, false, "Preencha o campo de Confirmação de Senha");
}

//? Verifica se a senha e a confirmação de senha coincidem
if ($body->senha !== $body->confSenha) {
    resposta(200, false, "A senha e a confirmação de senha não coincidem");
}

//? verifica a existencia de caracteres não permitidos no nome
if (!preg_match('/^[a-zA-Z0-9]/', $body->nome)) {
    resposta(200, false, "nome com caracteres inválidos");
}

//TODO estabelece a consulta o bd e recebe a resposta
$consulta = $conexao->prepare('SELECT email FROM usuarios WHERE email = :email');
$consulta->execute([':email' => $body->email]);
$consulta = $consulta->fetchColumn();

//? verifica a existencia do email no bd
if ($body->email == $consulta){
    resposta(200, false, "Email já cadastrado");
}


//? verificaçao de tipo

$body->nome = filter_var($body->nome,FILTER_SANITIZE_STRING);
$body->email = filter_var($body->email,FILTER_VALIDATE_EMAIL);
$body->senha = filter_var($body->senha,FILTER_SANITIZE_STRING);

//? verifica existencia de cada dado
if(!$body->nome ||!$body->email || !$body->senha)
    resposta(400, false, "Dados Invalidos");

//? faz o cadastro
$stm = $conexao->prepare('INSERT INTO usuarios(nome, email, senha) VALUES (:nome, :email, :senha)');
$stm->bindParam('nome', $body->nome);
$stm->bindParam('email', $body->email);
$stm->bindParam('senha', $body->senha);
$stm->execute();

resposta(200,true,'msg salvA');

?>