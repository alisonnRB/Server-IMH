<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/geraToken.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

//TODO função que encerra as operações e enciar umas resposta para a api trabalhar


function verifica($body){
    //! Verificar entrada string, filtrar e etc
    //? verifica se estão vazios
    if (empty($body->email) && empty($body->senha)){
        resposta(200, false, "Você deve preencher os campos");
    }

    if (empty($body->email)){
        resposta(200, false, "Preencha o campo do e-mail");
    }

    if (empty($body->senha)){
        resposta(200, false, "Preencha o campo da senha");
    }

    consulta($body);
}

function consulta($body){


    $conexao = conecta_bd();

    //? acessa o email do input
    $consulta = $conexao->prepare("SELECT * FROM usuarios WHERE email = :email");
    $consulta->execute([':email' => $body->email]);

    if ($consulta->rowCount() === 1) {

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        //? verifica se senha e email coicidem
        if ($body->senha === $usuario['senha']) {
            $idUser = $conexao->prepare("SELECT id, email FROM usuarios WHERE email = :email");
            $idUser->execute([':email' => $body->email]);
            $info = $idUser->fetch(PDO::FETCH_ASSOC);

            $token = geraToken($info['id'], $info['email']);

            resposta(200, true, $token);
        }else{
            resposta(400, false, "Senha incorreta");
        }
    }else{
        resposta(400, false, "Email não registrado!");
    }
}


$body = file_get_contents('php://input');
$body = json_decode($body);

verifica($body);
?>