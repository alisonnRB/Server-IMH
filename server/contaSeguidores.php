<?php
include "./conexão/conexao.php";
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

function userSegui() {
    $conexao = conecta_bd();
    $consulta = $conexao->prepare("SELECT id FROM usuarios");
    $consulta->execute();
    $livros = $consulta->fetchAll(PDO::FETCH_ASSOC);

    foreach ($livros as $livro) {
        salva($conexao, $livro['id']);
    }
}

function salva($conexao, $id) {

    $sql = "SELECT COUNT(*) AS total FROM seguidores WHERE id_ref = :id";   
    
    $consulta = $conexao->prepare($sql);
    $consulta->bindParam(':id', $id, PDO::PARAM_INT);
    $consulta->execute();
    $resultado = $consulta->fetchColumn();


    $stm = "UPDATE usuarios SET seguidores = :seguidores WHERE id = :id";
    $stmt = $conexao->prepare($stm);
    $stmt->bindParam(':seguidores', $resultado, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

function alterar(){
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
    userSegui($conexao);
}
}
alterar();
?>
