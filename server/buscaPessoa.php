<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

// Função que encerra as operações e envia uma resposta para a API trabalhar


// Recebe os inputs da API
$body = file_get_contents('php://input');
$body = json_decode($body);


function Pesquisa($body){
    try {
        $conexao = conecta_bd();

        $search = '';
        $params = array();
    
        if ($body->nome != '') {
            $search .= 'nome LIKE :nome';
            $params[':nome'] = '%' . $body->nome . '%';
        } else {
            // Se $body->nome for vazio, não aplicar filtro
            $search .= '1'; // Isso é verdadeiro para todos os registros
        }

        $sql = "SELECT id, nome, fotoPerfil, seguidores FROM usuarios WHERE $search";
        $stmt = $conexao->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        resposta(200, true, $users);
    } catch (Exception $e) {
        resposta(500, false, null);
    }
}

Pesquisa($body);
?>