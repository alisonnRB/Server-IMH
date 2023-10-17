<?php

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./valicações/validacoes.php";


header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

busca_favoritos($body);

function busca_favoritos($body){

    $conexao = conecta_bd();

    $id_livro = validar_number($body->id);
    $id_ref = validar_number($body->id_ref);

    if (!$id_livro[0]) {
        resposta(400, false, $id_livro[1]);
    }
    if (!$id_ref[0]) {
        resposta(400, false, $id_ref[1]);
    }
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {

    $consulta = $conexao->prepare("SELECT id, user_id, id_livro FROM favoritos WHERE id_livro = :id_livro AND user_id = :user_id");
    $consulta->bindParam(':user_id', $id);
    $consulta->bindParam(':id_livro', $id_ref);
    $consulta->execute();
    $favoritos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    resposta(200, true, $favoritos);
    }
}

?>