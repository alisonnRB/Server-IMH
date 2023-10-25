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
    qual($token->id, $body);   
}
//! verificar id
function qual($id, $body){
    $conexao = conecta_bd();
    //! VERFICAR
    if(!empty($body->texto)){
        //! VERIFICAR
        saveText($id,$body, $conexao);
    }
}


function saveText($id, $body, $conexao) {
    $id_enquete = 0;

    $stmt = $conexao->prepare('INSERT INTO feed_publi(user_id, texto, ref_livro, enquete) VALUES (:user_id, :texto, :ref_livro, :enquete)');
    $stmt->bindParam(':texto', $body->texto);
    $stmt->bindParam(':user_id', $id);
    if($body->livro != "" && $body->livro->id != 0){
        $stmt->bindParam(':ref_livro', $body->livro->id);    
    }else{
        $l = 0;
        $stmt->bindParam(':ref_livro', $l); 
    }

    for ($i = 0; $i < 3; $i++) {
        $chave = strval($i);
        if ($body->enquete != '' && $body->enquete->{$chave} != '') {
            $id_enquete = salva_enquete($id, $body, $conexao);
            break;
        }
    }

    $stmt->bindParam(':enquete', $id_enquete); 
    $stmt->execute();

    resposta(200, true, 'certo');
}

function salva_enquete($id, $body, $conexao){
    $enquete = json_encode($body->enquete);
    $stm = $conexao->prepare('INSERT INTO enquete(titulo, quest) VALUES (:titulo, :quest)');
    $stm->bindParam(':titulo', $body->titleEnquete);
    $stm->bindParam(':quest', $enquete);
    $stm->execute();

    $lastInsertId = $conexao->lastInsertId();

    return $lastInsertId;
}

?>