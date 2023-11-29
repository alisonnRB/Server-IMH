<?php
date_default_timezone_set('America/Sao_Paulo');
include "./conexão/conexao.php";
include "./resposta/resposta.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

// Função que encerra as operações e envia uma resposta para a API trabalhar


// Recebe os inputs da API
$body = file_get_contents('php://input');
$body = json_decode($body);

//! Verificar as entradas string, filtrar e etc

function criaPesquisa($body) {
    $search = 'publico = 1';
    $params = array(); // Para armazenar os parâmetros seguros

    if (!empty($body->nome) && $body->nome != '') {
        $search .= ' AND (nome LIKE :nome';
        $params[':nome'] = '%' . $body->nome . '%';
        $search .= ' OR tags LIKE :tags)';
        $params[':tags'] = '%' . $body->nome . '%';
    }

    if (!empty($body->Finalizado)) {
        $search .= ' AND finalizado = :finalizado';
        $params[':finalizado'] = $body->Finalizado ? 1 : 0;
    }
    if (!empty($body->selecao)) {
        $generoSelecionado = null;
    
        foreach ($body->selecao as $index => $valor) {
            if ($valor == true) {
                $generoSelecionado = $index;
                break;
            }
        }
    
        if (!is_null($generoSelecionado)) {
            // Verifica se o índice selecionado está presente na lista de gêneros
            $search .= " AND JSON_CONTAINS(genero, :generoSelecionado)";
            $params[':generoSelecionado'] = json_encode($generoSelecionado);
        }
    }
    if (!empty($body->Novo)) {
        $search .= " AND DATEDIFF(NOW(), tempo) <= 7";
    }

    if(!empty($body->classificacao)){
        $search .= " AND classificacao = :classificacao";
        $params[':classificacao'] = $body->classificacao;
    }

    quaisLivros($search, $params, $body->indice);
}

function quaisLivros($search, $params, $indice){
    try {
        $conexao = conecta_bd();
        if (!$conexao) {
            resposta(200, false, "Houve um problema ao conectar ao servidor");
        } else {

        $sql = "SELECT id, user_id, nome, imagem, genero, sinopse, classificacao, curtidas, favoritos, visus FROM livro_publi WHERE $search ORDER BY curtidas ASC, visus DESC LIMIT 18 OFFSET $indice";
        $stmt = $conexao->prepare($sql);
        $stmt->execute($params);
        $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);sleep(5);

        if($indice == 0 && !$livros){
            resposta(200, true, "nao");
        }else if($indice != 0 && !$livros){
            resposta(200, true, "naoM");
        }
        resposta(200, true, $livros);
    }} catch (Exception $e) {
        resposta(200, false, null);
    }
}

criaPesquisa($body);
?>