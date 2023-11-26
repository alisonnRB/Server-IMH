<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/decode_token.php";
include "./validações/validacoes.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');


$body = file_get_contents('php://input');
$body = json_decode($body);



$token = decode_token($body->id);
if (!$token || $token == "erro") {
    resposta(200, false, "não autorizado");
} else {
    change_senha($token->id, $body);
}

function change_senha($id, $body)
{
    //! validar se as entradas existem e etc.
    if ($body->tipo != 'ihm') {
        if ($body->idioma == "PT") {
            resposta(200, false, 'Sua conta é do Google!');
        } else if ($body->idioma == "ES") {
            resposta(200, false, '¡Tu cuenta es Google!');
        } else if ($body->idioma == "EN") {
            resposta(200, false, 'Your account is Google!');
        }
    }

    $senhaOG = validar_senha($body->senhaAntiga);
    if ($senhaOG[0] == true) {
        $senhaOG = $senhaOG[1];
    } else {
        resposta(200, false, $senhaOG[1]);
    }

    $senhaNew = validar_senha($body->NovaSenha);
    if ($senhaNew[0] == true) {
        $senhaNew = $senhaNew[1];
    } else {
        resposta(200, false, $senhaNew[1]);
    }

    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {
        $consulta = $conexao->prepare('SELECT senha FROM usuarios WHERE id = :id');
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        $Senha = $consulta->fetchColumn();


        //! verificar com hash
        if (password_verify($senhaOG, $Senha)) {
            //!converter a senha para hash
            $cripto_senha = cripto_senha($senhaNew);
            $stmt = $conexao->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
            $stmt->execute([$cripto_senha, $id]);

            if ($body->idioma == "PT") {
                resposta(200, true, 'senha alterada');
            } else if ($body->idioma == "ES") {
                resposta(200, true, 'contraseña cambiada');
            } else if ($body->idioma == "EN") {
                resposta(200, true, 'password changed');
            }
        } else {
            if ($body->idioma == "PT") {
                resposta(200, false, 'essa não é sua senha');
            } else if ($body->idioma == "ES") {
                resposta(200, false, 'esta no es tu contraseña');
            } else if ($body->idioma == "EN") {
                resposta(200, false, 'this is not your password');
            }
        }
    }
}

?>