<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

//! verificar id
function quaisGeneros(){
    try{
        $conexao = conecta_bd();

        $stmt = $conexao->prepare("SELECT id, nome FROM genero");
        $stmt->execute();
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $list = array();

        foreach ($stmt as $row) {
            $list[$row['id']] = $row['nome'];
        }

        resposta(200, true, $list);
    }catch(Exception $e){
        resposta(500, false, []);
    }
}

quaisGeneros();
?>