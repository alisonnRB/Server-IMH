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
    Busca_publi($token->id);
}

function Busca_publi($id) {
    //! Verificação da conexão
    $conexao = conecta_bd();

    $stm = $conexao->prepare('SELECT id_ref FROM seguidores WHERE user_id = :user_id');
    $stm->bindParam(':user_id', $id);
    $stm->execute();
    $segui = $stm->fetchAll(PDO::FETCH_ASSOC);

    

    for($i = 0; $i<count($segui); $i++){;
        $stmt = $conexao->prepare('SELECT id, user_id, texto, ref_livro, enquete, tempo  FROM feed_publi WHERE user_id = :user_id OR user_id = :id ORDER BY tempo DESC');
        $stmt->bindParam(':user_id', $segui[$i]['id_ref']);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $Busca = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $keys = count($Busca);
    
    for($i = 0; $i<$keys; $i++){;
        $stmt = $conexao->prepare('SELECT id, nome, fotoPerfil FROM usuarios WHERE id = :id');
        $stmt->bindParam(':id', $Busca[$i]['user_id']);
        $stmt->execute();
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $Busca[$i]['infos_user'] = $user;
    }

    for($i = 0; $i<$keys; $i++){;
        if($Busca[$i]['ref_livro'] != 0){
            $stmt = $conexao->prepare('SELECT id, imagem, user_id, nome FROM livro_publi WHERE id = :id');
            $stmt->bindParam(':id', $Busca[$i]['ref_livro']);
            $stmt->execute();
            $livro = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $Busca[$i]['infos_link'] = $livro;
        }
    }

    for($i = 0; $i<$keys; $i++){;
        if($Busca[$i]['enquete'] != 0){
            $stmt = $conexao->prepare('SELECT quest, titulo FROM enquete WHERE id = :id');
            $stmt->bindParam(':id', $Busca[$i]['enquete']);
            $stmt->execute();
            $enquete = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $Busca[$i]['enquete'] = $enquete;
        }
    }

    resposta(200, true, $Busca);
}

?>