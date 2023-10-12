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
        salva($conexao, 'livro_publi', $livro['id']);
    }
}

function comentariolike() {
    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");
    $consulta = $conexao->prepare("SELECT id FROM comentarios");
    $consulta->execute();
    $coment = $consulta->fetchAll(PDO::FETCH_ASSOC);

    foreach ($coment as $comentario) {
        salva($conexao, 'comentarios', $comentario['id']);
    }
}

function salva($conexao, $tabela, $id) {

    if($tabela == 'livro_publi'){
        $sql = "SELECT COUNT(*) AS total FROM curtidas WHERE id_ref = :id AND coment = 0";   
    }
    else if ($tabela == 'comentarios') {
        $sql = "SELECT COUNT(*) AS total FROM curtidas WHERE coment = :id";   
    }
    
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
