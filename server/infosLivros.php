<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');


//! verificar id
function quaisGeneros($body) {
    try {
        $conexao = conecta_bd();

        $stmt = $conexao->prepare("SELECT id, user_id, nome, imagem, genero, texto, sinopse, classificacao, pronto, publico, finalizado, tema, tags, curtidas, favoritos, visus FROM livro_publi WHERE id = :id ");
        $stmt->execute([':id' => $body->idLivro]);
        $stmt = $stmt->fetch(PDO::FETCH_ASSOC);

        resposta(200, true, $stmt);
    } catch (Exception $e) {
        resposta(500, false, ['...']);
    }
}

$body = file_get_contents('php://input');
$body = json_decode($body);
    if(!isset($body->idLivro) || empty($body->idLivro)){
        resposta(200, false, ['...']);
    }
quaisGeneros($body);
?>