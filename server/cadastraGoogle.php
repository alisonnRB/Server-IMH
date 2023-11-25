<?php
//* faz o cadastro dos usuarios
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";
include "./token/google_token.php";
//TODO função que encerra as operações e enciar umas resposta para a api trabalhar

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');
//TODO recebe os inputs da api

$body = file_get_contents('php://input');
$body = json_decode($body);

verificacao_de_dados($body);

function verificacao_de_dados($body)
{
    $decode = decode_token($body->token);
    //! se for nulo deve retornar erro de cadastro
    if ($decode) {
        $conexao = conecta_bd();
        if (!$conexao) {
            resposta(200, false, "Houve um problema ao conectar ao servidor");
        } else {
            $consulta = $conexao->prepare("SELECT email FROM usuarios WHERE email = :email AND tipo = 'google'");
            $consulta->execute([':email' => $decode->email]);
            $consulta = $consulta->fetchColumn();
            if ($decode->email == $consulta) {
                resposta(200, false, "Email já cadastrado");
            }
        }
    }

    cadastrar($conexao, $decode->given_name, $decode->email);


    resposta(200, false, $decode);
}

function cadastrar($conexao, $nome, $email)
{
    try {
        $stm = $conexao->prepare("INSERT INTO usuarios(nome, email, tipo) VALUES (:nome, :email, 'google')");
        $stm->bindParam('nome', $nome);
        $stm->bindParam('email', $email);
        $stm->execute();

        $id = $conexao->lastInsertId();

        $destina = '../livros/';
        $nomeDaPasta = $id;
        if (!is_dir($destina . $nomeDaPasta)) {
            mkdir($destina . $nomeDaPasta);
        }
        resposta(200, true, 'msg salvA');
    } catch (Exception $e) {
        resposta(200, false, "algo deu errado");
    }
}
?>