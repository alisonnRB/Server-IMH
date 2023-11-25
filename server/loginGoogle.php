<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/geraToken.php";
include "./token/google_token.php";

include "./validações/validacoes.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

//TODO função que encerra as operações e enviar umas resposta para a api trabalhar

$body = file_get_contents('php://input');
$body = json_decode($body);

verifica($body->token);

function verifica($token)
{

    $decode = decode_token($token);
    if (!$decode) {
        resposta(200, false, 'seu login não foi autorizado');
    }

    $conexao = conecta_bd();

    //? acessa o email do input
    $consulta = $conexao->prepare("SELECT * FROM usuarios WHERE email = :email AND tipo = 'google' ");
    $consulta->execute([':email' => $decode->email]);


    if ($consulta->rowCount() === 1) {
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        $token = geraToken($usuario['id'], $usuario['email']);

        resposta(200, true, $token);

    } else {
        resposta(200, false, "Email não registrado!");
    }

} ?>