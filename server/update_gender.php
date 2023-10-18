<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

$body = file_get_contents('php://input');
$body = json_decode($body);

salva($body->id, $body->selecionados);

//! verificar se o id é valido.
function salva($id, $selecao){
    try {
        $conexao = conecta_bd();
        if (!$conexao) {
            resposta(500, false, "Houve um problema ao conectar ao servidor");
        } else {

        $lista = array();
        
        foreach ($selecao as $chave => $valor) {
            if ($valor == true) {
                $lista[] = $chave;
            }
        }
        
        $lista = json_encode($lista);
        
        $stmt = $conexao->prepare('UPDATE usuarios SET genero = ? WHERE id = ?');
        $stmt->execute([$lista, $id]);
        
        resposta(200, true, 'Salvo com sucesso');
    }} catch (Exception $e) {
        resposta(500, false, 'Algo deu errado :(');
    }
}


?>