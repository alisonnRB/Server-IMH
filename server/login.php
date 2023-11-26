<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/geraToken.php";
include "./validações/validacoes.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

//TODO função que encerra as operações e enviar umas resposta para a api trabalhar


function verifica($body){
    //! Verificar entrada string, filtrar e etc
    //? verifica se estão vazios
    if (empty($body->email) && empty($body->senha)) {
        resposta(200, false, "Você deve preencher os campos");
    }

    if (empty($body->email)) {
        resposta(200, false, "Preencha o campo do e-mail");
    }

    if (empty($body->senha)) {
        resposta(200, false, "Preencha o campo da senha");
    }

    //validação do email
    $email = validar_email($body->email);
    if ($email[0] == true) {
        $email = $email[1];
    } else {
        resposta(200, false, $email[1]);
    }

    //validação da senha
    $senha = validar_senha($body->senha);
    if ($senha[0] == true) {
        $senha = $senha[1];
    } else {
        resposta(200, false, $senha[1]);
    }


    consulta($email, $senha);
}

function consulta($email, $senha){


    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {

        //? acessa o email do input
        $consulta = $conexao->prepare("SELECT * FROM usuarios WHERE email = :email AND tipo = 'ihm' ");
        $consulta->execute([':email' => $email]);


        if ($consulta->rowCount() === 1) {

            $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
            //? verifica se senha e email coicidem
            //?verifica se a senha passada no campo de login coincide com o hash do banco
            if (password_verify($senha, $usuario['senha'])) {
                $idUser = $conexao->prepare("SELECT id, email FROM usuarios WHERE email = :email AND tipo = 'ihm'");
                $idUser->execute([':email' => $email]);
                $info = $idUser->fetch(PDO::FETCH_ASSOC);

                $token = geraToken($info['id'], $info['email']);

                resposta(200, true, $token);
            } else {
                resposta(200, false, "Senha incorreta");
            }
            } else {
                resposta(200, false, "Email não registrado!");
            }
    }
}


$body = file_get_contents('php://input');
$body = json_decode($body);

verifica($body);
?>