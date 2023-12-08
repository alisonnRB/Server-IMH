<?php

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/decode_token.php";
include "./validações/validacoes.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

date_default_timezone_set('America/Sao_Paulo');

$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->id);

if (!$token || $token == "erro") {
    resposta(200, false, "não autorizado");
} else {
    qual($token->id, $body);
}

function qual($id, $body)
{
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {

        $texto = strip_tags($body->texto);
        if ($texto[0] == true) {
            saveText($id, $body, $conexao);
        } else {
            resposta(200, false, $texto[1]);
        }
    }
}


function saveText($id, $body, $conexao)
{
    //! verificar da existencia de enquete valida
    //! verificar da existencia link livro valido
    $id_enquete = 0;

    $stmt = $conexao->prepare('INSERT INTO feed_publi(user_id, texto, ref_livro, enquete, tempo) VALUES (:user_id, :texto, :ref_livro, :enquete, :tempo)');
    $stmt->bindParam(':texto', $body->texto);
    $stmt->bindParam(':user_id', $id);
    if ($body->livro != "" && $body->livro->id != 0) {
        $stmt->bindParam(':ref_livro', $body->livro->id);
    } else {
        $l = 0;
        $stmt->bindParam(':ref_livro', $l);
    }

    for ($i = 0; $i < 3; $i++) {
        $chave = strval($i);
        if ($body->enquete != '' && $body->enquete->{$chave} != '') {
            $id_enquete = salva_enquete($id, $body, $conexao);
            break;
        }
    }

    $stmt->bindParam(':enquete', $id_enquete);
    $data = date('Y-m-d H:i:s');
    $stmt->bindParam(':tempo', $data);
    $stmt->execute();

    resposta(200, true, 'certo');
}

function salva_enquete($id, $body, $conexao)
{
    //! verficar itens da enquete
    $enquete = json_encode($body->enquete);
    $stm = $conexao->prepare('INSERT INTO enquete(titulo, quest) VALUES (:titulo, :quest)');
    $stm->bindParam(':titulo', $body->titleEnquete);
    $stm->bindParam(':quest', $enquete);
    $stm->execute();

    $lastInsertId = $conexao->lastInsertId();

    return $lastInsertId;
}

?>