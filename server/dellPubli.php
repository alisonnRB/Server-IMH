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
if (!$token || $token == "erro") {
    resposta(200, false, "não autorizado");
} else {
    verifica($token->id, $body);
}

function verifica($id, $body)
{
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    }

    $consulta = $conexao->prepare('SELECT user_id FROM feed_publi WHERE id = :id');
    $consulta->execute([':id' => $body->idPubli]);
    $consulta = $consulta->fetchAll(PDO::FETCH_ASSOC);

    if(!$consulta || $consulta[0]['user_id'] !== $id){
        resposta(200, false, 'erro');
    }

    $stmt = $conexao->prepare("DELETE FROM comentarios WHERE id_ref = :id AND tipo = 'publi' ");
    $stmt->execute([':id' => $body->idPubli]);

    $stmt = $conexao->prepare("DELETE FROM curtidas WHERE id_ref = :id AND tipo = 'publi' ");
    $stmt->execute([':id' => $body->idPubli]);

    $stmt = $conexao->prepare('DELETE FROM feed_publi WHERE id = :id');
    $stmt->execute([':id' => $body->idPubli]);

    resposta(200, true, "certo");

}



?>