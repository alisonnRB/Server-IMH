<?php

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

// Função que encerra as operações e envia uma resposta para a API trabalhar


// Recebe os inputs da API
$body = file_get_contents('php://input');
$body = json_decode($body);

Busca_usuarios($body);


function Busca_usuarios ($body){
    $conexao = conecta_bd();

    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
        $search = '';
        $params = array();

        if ($body->nome != '') {
            $search .= 'nome LIKE :nome';
            $params[':nome'] = '%' . $body->nome . '%';
        }else{
            $search .= '1';
        }
        

        $sql = "SELECT id, nome, fotoPerfil, seguidores FROM usuarios WHERE $search";
        $stmt = $conexao->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        resposta(200, true, $users);
    }
}    
?>