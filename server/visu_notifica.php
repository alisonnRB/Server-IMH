<?php

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";
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
    busca_noti($token->id);
}

function busca_noti($id)
{
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {
    
    //!! verificar
    novo_seguidor($conexao, $id);
    nova_curtida($conexao, $id);
    novos_comentarios($conexao, $id);
    novos_comentarios_coment($conexao, $id);
    novos_comentarios_publi($conexao, $id);
    novidade($conexao, $id);

    resposta(200, true, 'certo');
}
}


function novo_seguidor($conexao, $id)
{
    $segui = $conexao->prepare("UPDATE seguidores SET visu = 1 WHERE id_ref = ?");
    $segui->execute([$id]);
}
function nova_curtida($conexao, $id)
{
    $curti = $conexao->prepare("UPDATE curtidas SET visu = 1");
    $curti->execute();

}
function novos_comentarios($conexao, $id)
{
    $coment = $conexao->prepare("SELECT id, user, id_ref FROM comentarios WHERE visu = 0 AND resposta = 0 AND tipo = 'livro'");
    $coment->execute();
    $coment = $coment->fetchAll(PDO::FETCH_ASSOC);

    foreach ($coment as $key => $value) {
        $stmt = $conexao->prepare("SELECT id FROM livro_publi WHERE id = :id AND user_id = :user_id");
        $stmt->bindParam(":id", $coment[$key]["id_ref"]);
        $stmt->bindParam(":user_id", $id);
        $stmt->execute();
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($stmt) {
            $smt = $conexao->prepare("UPDATE comentarios SET visu = 1 WHERE id = ?");
            $smt->execute([$coment[$key]['id']]);
        }
    }

}

function novos_comentarios_coment($conexao, $id)
{
    $coment = $conexao->prepare("SELECT id, user, id_ref, tipo, id_resposta, conversa FROM comentarios WHERE visu = 0 AND resposta != 0");
    $coment->execute();
    $coment = $coment->fetchAll(PDO::FETCH_ASSOC);

    

    foreach ($coment as $key => $value) {
        $stmt = $conexao->prepare("SELECT id FROM comentarios WHERE (id = :id OR id = :id_r) AND user = :user_id");
        $stmt->bindParam(":id", $coment[$key]["conversa"]);
        $stmt->bindParam(":id_r", $coment[$key]["id_resposta"]);
        $stmt->bindParam(":user_id", $id);
        $stmt->execute();
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($stmt) {
            $smt = $conexao->prepare("UPDATE comentarios SET visu = 1 WHERE id = ?");
            $smt->execute([$coment[$key]['id']]);
        }
    }

}

function novos_comentarios_publi($conexao, $id)
{
    $coment = $conexao->prepare("SELECT id, user, id_ref FROM comentarios WHERE visu = 0 AND resposta = 0 AND tipo = 'publi'");
    $coment->execute();
    $coment = $coment->fetchAll(PDO::FETCH_ASSOC);

    foreach ($coment as $key => $value) {
        $stmt = $conexao->prepare("SELECT id FROM feed_publi WHERE id = :id AND user_id = :user_id");
        $stmt->bindParam(":id", $coment[$key]["id_ref"]);
        $stmt->bindParam(":user_id", $id);
        $stmt->execute();
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);


        if ($stmt) {
            $smt = $conexao->prepare("UPDATE comentarios SET visu = 1 WHERE id = ?");
            $smt->execute([$coment[$key]['id']]);
        }

    }
}

function novidade($conexao, $id)
{
    $fav = $conexao->prepare("UPDATE favoritos SET visu = 1 WHERE user_id = ?");
    $fav->execute([$id]);
}