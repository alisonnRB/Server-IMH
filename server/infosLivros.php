<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/decode_token.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

$token = decode_token($_GET['id']);
if (!$token || $token == "erro") {
    resposta(200, false, "não autorizado");
} else {
    info_livro($_GET['idLivro']);
}

function info_livro($id)
{
    try {
        $conexao = conecta_bd();
        if (!$conexao) {
            resposta(200, false, "Houve um problema ao conectar ao servidor");
        } else {

            $stmt = $conexao->prepare("SELECT id, user_id, nome, imagem, genero, texto, sinopse, classificacao, pronto, publico, finalizado, tema, tags, curtidas, favoritos, visus FROM livro_publi WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $stmt = $stmt->fetch(PDO::FETCH_ASSOC);

            resposta(200, true, $stmt);
        }
    } catch (Exception $e) {
        resposta(200, false, ['...']);
    }
}


?>