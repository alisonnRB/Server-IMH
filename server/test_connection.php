<?php
require_once './conexão/conecta_bd.php';

$conexao = conecta_bd();
if (isset($conexao['ok']) && !$conexao['ok']) {
    echo json_encode($conexao); // Mostra o erro se não conseguiu conectar
} else {
    echo json_encode(["ok" => true, "informacoes" => "Conexão bem-sucedida ao banco de dados!"]);
}
?>