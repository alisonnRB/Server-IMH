<?php

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";


header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

busca_favoritos($body);

function busca_favoritos($body){

    $conexao = conecta_bd();

    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {

    $consulta = $conexao->prepare("SELECT id, user_id, id_livro FROM favoritos WHERE id_livro = :id_livro AND user_id = :user_id");
    $consulta->bindParam(':user_id', $body->id);
    $consulta->bindParam(':id_livro', $body->id_ref);
    $consulta->execute();
    $favoritos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    resposta(200, true, $favoritos);
    }
}

?>