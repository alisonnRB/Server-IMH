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

        $noti = [];

        $noti["seguidores"] = novo_seguidor($conexao, $id); //*certo
        $noti["livros-coment"] = novos_comentarios($conexao, $id); //*certo
        $noti["coment-coment"] = novos_comentarios_coment($conexao, $id); //*certo
        $noti["publi"] = novos_comentarios_publi($conexao, $id); //*certo
        $noti["favoritos"] = novidade($conexao, $id); //*certo
        $noti["curtidas-livro"] = nova_curtida($conexao, $id); //*certo
        $noti["curtidas-coment"] = nova_curtida_coment($conexao, $id); //*certo
        $noti["curtidas-publi"] = nova_Pcurtida($conexao, $id);
        $noti["curtidas-Pcoment"] = nova_Pcurtida_coment($conexao, $id);

        resposta(200, true, $noti);
    }
}

function novo_seguidor($conexao, $id)
{
    $segui = $conexao->prepare("SELECT id, user_id FROM seguidores WHERE id_ref = :id_ref AND visu = 0");
    $segui->bindParam(":id_ref", $id);
    $segui->execute();
    $segui = $segui->fetchAll(PDO::FETCH_ASSOC);

    return $segui;
}
function nova_curtida($conexao, $id)
{
    $curtis = [];

    $curti = $conexao->prepare("SELECT id, id_user, id_ref, tipo, coment FROM curtidas WHERE visu = 0 AND tipo = 'livro' AND coment = 0");
    $curti->execute();
    $curti = $curti->fetchAll(PDO::FETCH_ASSOC);

    foreach ($curti as $key => $value) {
        $stmt = $conexao->prepare("SELECT id FROM livro_publi WHERE id = :id AND user_id = :user_id");
        $stmt->bindParam(":id", $curti[$key]["id_ref"]);
        $stmt->bindParam(":user_id", $id);
        $stmt->execute();
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($stmt) {
            $curtis[] = $curti[$key];
        }
    }

    return $curtis;
}

function nova_curtida_coment($conexao, $id)
{
    $curtis = [];

    $curti = $conexao->prepare("SELECT id, id_user, id_ref, tipo, coment FROM curtidas WHERE visu = 0 AND tipo = 'livro' AND coment != 0");
    $curti->execute();
    $curti = $curti->fetchAll(PDO::FETCH_ASSOC);

    foreach ($curti as $key => $value) {
        $stmt = $conexao->prepare("SELECT id FROM comentarios WHERE id = :id AND user = :user ");
        $stmt->bindParam(":id", $curti[$key]["coment"]);
        $stmt->bindParam(":user", $id);
        $stmt->execute();
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($stmt) {
            $curtis[] = $curti[$key];
        }
    }

    return $curtis;
}

function nova_Pcurtida($conexao, $id)
{
    $curtis = [];

    $curti = $conexao->prepare("SELECT id, id_user, id_ref, tipo, coment FROM curtidas WHERE visu = 0 AND tipo = 'publi' AND coment = 0");
    $curti->execute();
    $curti = $curti->fetchAll(PDO::FETCH_ASSOC);

    foreach ($curti as $key => $value) {
        $stmt = $conexao->prepare("SELECT id FROM feed_publi WHERE id = :id AND user_id = :user_id");
        $stmt->bindParam(":id", $curti[$key]["id_ref"]);
        $stmt->bindParam(":user_id", $id);
        $stmt->execute();
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($stmt) {
            $curtis[] = $curti[$key];
        }
    }

    return $curtis;
}

function nova_Pcurtida_coment($conexao, $id)
{
    $curtis = [];

    $curti = $conexao->prepare("SELECT id, id_user, id_ref, tipo, coment FROM curtidas WHERE visu = 0 AND tipo = 'publi' AND coment != 0");
    $curti->execute();
    $curti = $curti->fetchAll(PDO::FETCH_ASSOC);

    foreach ($curti as $key => $value) {
        $stmt = $conexao->prepare("SELECT id FROM comentarios WHERE id = :id AND user = :user ");
        $stmt->bindParam(":id", $curti[$key]["coment"]);
        $stmt->bindParam(":user", $id);
        $stmt->execute();
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($stmt) {
            $curtis[] = $curti[$key];
        }
    }



    return $curtis;
}
function novos_comentarios($conexao, $id)
{
    $comenta = [];

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
            $comenta[] = $coment[$key];
        }
    }

    return $comenta;
}

function novos_comentarios_coment($conexao, $id)
{
    $coments = [];

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
            $coments[] = $coment[$key];
        }
    }

    return $coments;
}

function novos_comentarios_publi($conexao, $id)
{
    $comenta = [];

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
            $comenta[] = $coment[$key];
        }
    }

    return $comenta;
}

function novidade($conexao, $id)
{
    $fav = $conexao->prepare("SELECT id, id_livro FROM favoritos WHERE visu = 0 AND user_id = :user_id");
    $fav->bindParam(":user_id", $id);
    $fav->execute();
    $fav = $fav->fetchAll(PDO::FETCH_ASSOC);

    return $fav;
}