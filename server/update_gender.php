<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/decode_token.php";


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->id);
if(!$token || $token == "erro"){
    resposta(200, false, "não autorizado");
}else{
   salva($token->id, $body->selecionados); 
}


function salva($id, $selecao){
    try {
        $conexao = conecta_bd();
        if (!$conexao) {
            resposta(200, false, "Houve um problema ao conectar ao servidor");
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
        resposta(200, false, 'Algo deu errado :(');
    }
}


?>