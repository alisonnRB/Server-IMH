<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/decode_token.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

//TODO função que encerra as operações e enciar umas resposta para a api trabalhar


//TODO recebe os inputs da api
$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->id);

if (!$token || $token == "erro") {
    resposta(200, false, "não autorizado");
} else {
    if ($body->idUser == "i") {
        meus_livros($token->id, $body->indice);
    } else {
        other_livros($body->idUser, $body->indice);
    }
}

function meus_livros($id, $indice)
{
    try {
        $conexao = conecta_bd();
        if (!$conexao) {
            resposta(200, false, "Houve um problema ao conectar ao servidor");
        } else {
            $stmt = $conexao->prepare("SELECT id, user_id, nome, imagem, genero, sinopse, classificacao, curtidas, favoritos, visus FROM livro_publi WHERE user_id = :id ORDER BY curtidas ASC, visus DESC LIMIT 18 OFFSET :indice");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":indice", $indice, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$stmt) {
                resposta(200, true, 'nao');
            }

            resposta(200, true, $stmt);
        }
    } catch (Exception $e) {
        resposta(200, false, null);
    }
}

function other_livros($id, $indice)
{
    try {
        $conexao = conecta_bd();
        if (!$conexao) {
            resposta(200, false, "Houve um problema ao conectar ao servidor");
        } else {
            $stmt = $conexao->prepare("SELECT id, user_id, nome, imagem, genero, sinopse, classificacao, curtidas, favoritos, visus FROM livro_publi WHERE user_id = :id AND publico = 1 ORDER BY curtidas ASC, visus DESC LIMIT 18 OFFSET :indice");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":indice", $indice, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$stmt) {
                resposta(200, true, 'nao');
            }

            resposta(200, true, $stmt);
        }
    } catch (Exception $e) {
        resposta(200, false, null);
    }
}

?>