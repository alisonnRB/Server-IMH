<?php

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

function Livroslike() {
    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");
    $consulta = $conexao->prepare("SELECT id FROM livro_publi");
    $consulta->execute();
    $livros = $consulta->fetchAll(PDO::FETCH_ASSOC);

    foreach ($livros as $livro) {
        salva($conexao, 'livro_publi', $livro['id'], 'id_ref = :id AND coment = 0');
    }
}

function comentariolike() {
    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");
    $consulta = $conexao->prepare("SELECT id FROM comentario");
    $consulta->execute();
    $coment = $consulta->fetchAll(PDO::FETCH_ASSOC);

    foreach ($coment as $comentario) {
        salva($conexao, 'comentario', $comentario['id'], 'coment = :id');
    }
}

function salva($conexao, $tabela, $id, $ref) {
    $sql = "SELECT COUNT(*) AS total FROM curtidas WHERE $ref";
    $consulta = $conexao->prepare($sql);
    $consulta->bindParam(':id', $id, PDO::PARAM_INT);
    $consulta->execute();
    $resultado = $consulta->fetchColumn();

    $stm = "UPDATE $tabela SET curtidas = :curtidas WHERE id = :id";
    $stmt = $conexao->prepare($stm);
    $stmt->bindParam(':curtidas', $resultado, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

function alterar(){
    Livroslike();
    comentariolike();
}

alterar();
?>
