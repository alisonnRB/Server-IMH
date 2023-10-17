<?php

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./valicações/validacoes.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

$body = file_get_contents('php://input');
$body = json_decode($body);

$salva_cap_pronto = fn($body) => {
    $conexao = conecta_bd(); 

    $idLivro = verificar_number($body->idLivro);
    $cap = verificar_number($body->cap);

    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    }else{
        $consulta = $conexao->prepare('SELECT user_id, pronto FROM livro_publi WHERE id = :id');
        $consulta->execute([':id' => $idLivro]);
        $linha = $consulta->fetch(PDO::FETCH_ASSOC);
        if($linha['user_id'] != $body->id){
            resposta(500, false, "você não pode alterar livros que não são seus");
        }else{
            $public = json_decode($linha['pronto'], true);

            $public[$cap] = $body->pronto ? 0 : 1;

            $publicJSON = json_encode($public);

            $stmt = $conexao->prepare("UPDATE livro_publi SET pronto = ? WHERE id = ?");
            $stmt->execute([$publicJSON, $idLivro]);

            resposta(200, true);
        }
    }
}
?>