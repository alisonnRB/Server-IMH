<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

function salvaP($body) {
    $conexao = conecta_bd(); 

    $consulta = $conexao->prepare('SELECT user_id, pronto FROM livro_publi WHERE id = :id');
    $consulta->execute([':id' => $body->idLivro]);

    $linha = $consulta->fetch(PDO::FETCH_ASSOC);

    if($linha['user_id'] != $body->id){
        resposta(500, false);
    }else{

        $public = json_decode($linha['pronto'], true); // Decodificar JSON existente para array associativo

        // Atualizar ou adicionar o título correspondente a body->cap
        $public[$body->cap] = $body->pronto ? 0 : 1;

        // Codificar de volta para JSON
        $publicJSON = json_encode($public);


        $stmt = $conexao->prepare("UPDATE livro_publi SET pronto = ? WHERE id = ?");
        $stmt->execute([$publicJSON, $body->idLivro]);

        resposta(200, true);
        
    }
}

$body = file_get_contents('php://input');
$body = json_decode($body);

salvaP($body);
?>