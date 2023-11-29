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
    //! Verificar entrada string, filtrar e etc
    $conexao = conecta_bd();


    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {
        $search = '';
        $params = array();

        if ($body->nome != '') {
            $search .= 'nome LIKE :nome';
            $params[':nome'] = '%' . $body->nome . '%';
            
            //validação do body->nome
            $nome = validar_nome($body->nome);
            if ($nome[0] == true){
                $nome = $nome[1];
            } else {
                resposta(200, false, $nome[1]);
            }
            
        }else{
            $search .= '1';
        }

        $indice = $body->indice;

        $sql = "SELECT id, nome, fotoPerfil, seguidores FROM usuarios WHERE $search ORDER BY seguidores DESC LIMIT 20 OFFSET $indice";
        $stmt = $conexao->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($indice == 0 && !$users){
            resposta(200, true, "nao");
        }else if($indice != 0 && !$users){
            resposta(200, true, "naoM");
        }

        resposta(200, true, $users);
    }
}    
?>