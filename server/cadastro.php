<?php
//* faz o cadastro dos usuarios
include "./conexão/conexao.php";
include "./resposta/resposta.php";
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

function existe($body){
    if(!$body){
    resposta(200, false, "corpo não encontrado");
    }

    $conexao = conecta_bd();


        if(verificacao($body, $conexao) == true){
            cadastra($body, $conexao);
        }


}

function verificacao($body, $conexao){
    //? verifica se os campus estão preenchidos
    if (empty($body->nome) && empty($body->email) && empty($body->senha)){
        resposta(200, false, "Você deve preencher os campos");
    }

    if (empty($body->nome)){
        resposta(200, false, "Preencha o campo do Nome");
    }
    
    //? verifica a existencia de caracteres não permitidos no nome
    if (!preg_match('/^[a-zA-Z0-9\s]*[a-zA-Z0-9\s]+[a-zA-Z0-9\s]*$/', $body->nome)) {
        resposta(200, false, "nome com caracteres inválidos");
    }

    if (empty($body->email)){
        resposta(200, false, "Preencha o campo do E-mail");
    }

    //TODO estabelece a consulta o bd e recebe a resposta
    $consulta = $conexao->prepare('SELECT email FROM usuarios WHERE email = :email');
    $consulta->execute([':email' => $body->email]);
    $consulta = $consulta->fetchColumn();
    
    if ($body->email == $consulta){
    resposta(200, false, "Email já cadastrado");
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

    return true;
}

function cadastra($body, $conexao){
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
    
    $id = $conexao->prepare("SELECT id FROM usuarios WHERE email = :email");
    $id->execute([':email' => $body->email]);
    $id = $id->fetchColumn();

    $destina = '../livros/';
    $nomeDaPasta = $id;


    // Verifica se a pasta já existe antes de criar
    if (!is_dir($destina . $nomeDaPasta)) {
        mkdir($destina . $nomeDaPasta);
    } 

    resposta(200,true,'msg salvA');
}

//TODO recebe os inputs da api
$body = file_get_contents('php://input');

//? decodifica o json para um objeto php
$body = json_decode($body);

existe($body);
?>