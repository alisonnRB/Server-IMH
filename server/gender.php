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

if($token == "erro"){
    resposta(401, false, "não autorizado");
}else{
    quaisGeneros();   
}
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

?>