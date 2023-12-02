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
    reportar($token->id, $body->denuncia, $body->idLivro);
}

function reportar($id, $reporte, $livro)
{
    $conexao = conecta_bd();
    $stmt = $conexao->prepare("SELECT user FROM reporte WHERE user = :user AND livro = :livro");
    $stmt->bindParam(":user", $id);
    $stmt->bindParam(":livro", $livro);
    $stmt->execute();
    $stmt = $stmt->fetchColumn();

    if($stmt){
        resposta(200, true, "você não pode reportar denovo");
    }

    $denuncia = $conexao->prepare("INSERT INTO reporte(livro, user, denuncia) VALUES (:idLivro, :id, :denuncia)");
    $denuncia->bindParam(":idLivro", $livro, PDO::PARAM_INT);
    $denuncia->bindParam(":id", $id, PDO::PARAM_INT);
    $denuncia->bindParam(":denuncia", $reporte, PDO::PARAM_STR);
    $denuncia->execute();

    resposta(200, true, 'certo');
}


?>