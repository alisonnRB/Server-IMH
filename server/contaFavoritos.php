<?php
include "./conexão/conexao.php";
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

function Livroslike() {
    $conexao = conecta_bd();
    $consulta = $conexao->prepare("SELECT id FROM livro_publi");
    $consulta->execute();
    $livros = $consulta->fetchAll(PDO::FETCH_ASSOC);

    foreach ($livros as $livro) {
        salva($conexao, $livro['id']);
    }
}

function salva($conexao, $id) {

    $sql = "SELECT COUNT(*) AS total FROM favoritos WHERE id_livro = :id";   
    
    $consulta = $conexao->prepare($sql);
    $consulta->bindParam(':id', $id, PDO::PARAM_INT);
    $consulta->execute();
    $resultado = $consulta->fetchColumn();


    $stm = "UPDATE livro_publi SET favoritos = :favoritos WHERE id = :id";
    $stmt = $conexao->prepare($stm);
    $stmt->bindParam(':favoritos', $resultado, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

function alterar(){
    Livroslike();
}

alterar();
?>
