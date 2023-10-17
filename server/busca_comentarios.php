<?php

include "./resposta/resposta.php";
include "./conexão/conexao.php";
include "./valicações/validacoes.php";

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

function busca_comentarios($body){
    $conexao = conecta_bd();

    $id = validar_number($body->id);
    $tipo = validar_string($body->tipo);

    if (!$id[0]) {
        resposta(400, false, $id[1]);
    }
    if (!$tipo[0]) {
        resposta(400, false, $tipo[1]);
    }
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
        $stmt = $conexao->prepare("SELECT id, id_ref, user, texto, resposta, id_resposta, tempo, conversa, curtidas FROM comentarios WHERE id_ref = :id_ref AND tipo = :tipo");
        $stmt->execute([':id_ref' => $id, ':tipo' => $tipo]);
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

        resposta(200, true, $stmt);
    }
}
busca_comentarios($body);
?>