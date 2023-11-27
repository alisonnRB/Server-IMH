<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');


function visu($body)
{
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {
        try {
            $stmt = $conexao->prepare('UPDATE livro_publi SET visus = visus + 1 WHERE id = ?');
            $stmt->execute([$body->id]);

            resposta(200, true, "certo");
        }catch(PDOException $e) {
            resposta(200, false, Err($e->getMessage()));
        }
    }
}

$body = file_get_contents('php://input');
$body = json_decode($body);


visu($body);



?>