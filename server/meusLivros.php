<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/decode_token.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

//TODO função que encerra as operações e enciar umas resposta para a api trabalhar


//TODO recebe os inputs da api
$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->id);

if(!$token || $token == "erro"){
    resposta(200, false, "não autorizado");
}else{
    if($body->idUser == "i"){
        resposta(200, true, meus_livros($token->id));    
    }else{
        resposta(200, true, other_livros($body->idUser));    
    }
}

function meus_livros($id){
    try{
        $conexao = conecta_bd();
        if (!$conexao) {
            resposta(200, false, "Houve um problema ao conectar ao servidor");
        } else {
            $stmt = $conexao->prepare("SELECT id, user_id, nome, imagem, genero, sinopse, classificacao, curtidas, favoritos, visus FROM livro_publi WHERE user_id = :id");
            $stmt->execute([':id' => $id]);
            $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

            resposta(200, true, $stmt);
        }
    }catch (Exception $e) {
        resposta(200, false, null);
    }
}

function other_livros($id){
    try{
        $conexao = conecta_bd();
        if (!$conexao) {
            resposta(200, false, "Houve um problema ao conectar ao servidor");
        } else {
            $stmt = $conexao->prepare("SELECT id, user_id, nome, imagem, genero, sinopse, classificacao, curtidas, favoritos, visus FROM livro_publi WHERE user_id = :id AND publico = 1");
            $stmt->execute([':id' => $id]);
            $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

            resposta(200, true, $stmt);
        }
    }catch (Exception $e) {
        resposta(200, false, null);
    }
}

?>