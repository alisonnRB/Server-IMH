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
    if ($body->id_ref == "i") {
        Busca_publi($token->id, $body->indice);
    } else {
        Busca_publi_other($body->id_ref, $body->indice);
    }

}

function Busca_publi($id, $indice)
{
    // Verificação da conexão
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {

        $indice = intval($indice);
        $stmt = $conexao->prepare('SELECT id, user_id, texto, ref_livro, enquete, tempo FROM feed_publi WHERE user_id = :id ORDER BY tempo DESC, id DESC LIMIT 20 OFFSET :indice');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':indice', $indice, PDO::PARAM_INT);
        $stmt->execute();
        $Busca = $stmt->fetchAll(PDO::FETCH_ASSOC);

        for ($i = 0; $i < count($Busca); $i++) {
            $stmt = $conexao->prepare('SELECT id, nome, fotoPerfil FROM usuarios WHERE id = :id');
            $stmt->bindParam(':id', $Busca[$i]['user_id']);
            $stmt->execute();
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $Busca[$i]['infos_user'] = $user;
        }


        if (!$Busca) {
            resposta(200, true, 'nao');
        }

        resposta(200, true, $Busca);
    }
}

function Busca_publi_other($id, $indice)
{
    // Verificação da conexão
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {

        $indice = intval($indice);
        $stmt = $conexao->prepare('SELECT id, user_id, texto, ref_livro, enquete, tempo FROM feed_publi WHERE user_id = :id ORDER BY tempo DESC, id DESC LIMIT 20 OFFSET :indice');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':indice', $indice, PDO::PARAM_INT);
        $stmt->execute();
        $Busca = $stmt->fetchAll(PDO::FETCH_ASSOC);

        for ($i = 0; $i < count($Busca); $i++) {
            $stmt = $conexao->prepare('SELECT id, nome, fotoPerfil FROM usuarios WHERE id = :id');
            $stmt->bindParam(':id', $Busca[$i]['user_id']);
            $stmt->execute();
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $Busca[$i]['infos_user'] = $user;
        }


        if (!$Busca) {
            resposta(200, true, 'nao');
        }

        resposta(200, true, $Busca);
    }
}