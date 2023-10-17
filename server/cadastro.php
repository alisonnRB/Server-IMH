<?php
//* faz o cadastro dos usuarios
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./valicações/validacoes.php";
//TODO função que encerra as operações e enciar umas resposta para a api trabalhar

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');
//TODO recebe os inputs da api

$body = file_get_contents('php://input');
$body = json_decode($body);

verificacao_de_dados($body);

function verificacao_de_dados($body){ 
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
    if ($senha !== $confSenha) {
        resposta(200, false, "A senha e a confirmação de senha não coincidem");
    }

    $conexao = conecta_bd();

    $nome = validar_string($body->nome, "nome");
    $senha = validar_string($body->senha);
    $confSenha = validar_string($body->confSenha);
    $email = validar_string($body->email, "email");

    if (!$nome[0]) {
        resposta(400, false, $nome[1]);
    }
    if (!$senha[0]) {
        resposta(400, false, $senha[1]);
    }
    if (!$confSenha[0]) {
        resposta(400, false, $confSenha[1]);
    }
    if (!$email[0]) {
        resposta(400, false, $email[1]);
    }
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
        $consulta = $conexao->prepare('SELECT email FROM usuarios WHERE email = :email');
        $consulta->execute([':email' => $email]);
        $consulta = $consulta->fetchColumn();
        if ($email == $consulta){
        resposta(200, false, "Email já cadastrado");
        }
    }

    cadastrar($conexao, $nome, $email, $senha);
}

function cadastrar($conexao, $nome, $email, $senha){
    try{
        $stm = $conexao->prepare('INSERT INTO usuarios(nome, email, senha) VALUES (:nome, :email, :senha)');
        $stm->bindParam('nome', $nome);
        $stm->bindParam('email', $email);
        $stm->bindParam('senha', $senha);
        $stm->execute();

        $id = $conexao->prepare("SELECT id FROM usuarios WHERE email = :email");
        $id->execute([':email' => $email]);
        $id = $id->fetchColumn();

        $destina = '../livros/';
        $nomeDaPasta = $id;   
        if (!is_dir($destina . $nomeDaPasta)) {
            mkdir($destina . $nomeDaPasta);
        }
        resposta(200,true,'msg salvA');  
    }catch(Exception $e){
        resposta(500, false, "algo deu errado");
    }
}
?>