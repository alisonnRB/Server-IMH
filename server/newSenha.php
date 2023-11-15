<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/decode_token.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->id);
if($token == "erro"){
    resposta(401, false, "não autorizado");
}else{
    change_senha($token->id, $body);
}

function change_senha($id, $body) {
    //! validar se as entradas existem e etc.

    $conexao = conecta_bd();
    //! validar conexao

    $consulta = $conexao->prepare('SELECT senha FROM usuarios WHERE id = :id');
    $consulta->bindParam(':id', $id);
    $consulta->execute();
    $Senha = $consulta->fetchColumn();

    //! verificar com hash

    if($Senha == $body->senhaAntiga){
        //!converter a senha para hash
        $stmt = $conexao->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
        $stmt->execute([$body->NovaSenha, $id]);

        resposta(200, true, 'senha alterada');
    }else{
        resposta(401, false, 'essa não é sua senha');
    }

}