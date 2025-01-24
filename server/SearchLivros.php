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

function criaPesquisa($body)
{
    $search = 'publico = 1';
    $params = array(); // Para armazenar os parâmetros seguros

    if (!empty($body->nome) && $body->nome != '') {
        $search .= ' AND (nome LIKE :nome';
        $params[':nome'] = '%' . $body->nome . '%';
        $search .= ' OR tags LIKE :tags)';
        $params[':tags'] = '%' . $body->nome . '%';
    }

    if (!empty($body->Finalizado)) {
        // Aqui, agora estamos passando o valor booleano diretamente
        $search .= ' AND finalizado = :finalizado';
        $params[':finalizado'] = $body->Finalizado ? true : false;
    }

    if (!empty($body->selecao)) {
        // Verificando o índice de gênero selecionado
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
            $params[':generoSelecionado'] = json_encode([$generoSelecionado]); // Passa como um array dentro de um objeto JSON
        }
    }

    if (!empty($body->Novo)) {
        $search .= " AND DATEDIFF(NOW(), tempo) <= 7";
    }

    if (!empty($body->classificacao)) {
        $search .= " AND classificacao = :classificacao";
        $params[':classificacao'] = $body->classificacao;
    }

    quaisLivros($search, $params, $body->indice);
}


function quaisLivros($search, $params, $indice)
{
    try {
        $conexao = conecta_bd();
        if (!$conexao) {
            resposta(200, false, "Houve um problema ao conectar ao servidor");
        } else {
            // Ajustando a consulta para garantir que os parâmetros sejam passados corretamente
            $sql = "SELECT id, user_id, nome, imagem, genero, sinopse, classificacao, curtidas, favoritos, visus 
                    FROM livro_publi 
                    WHERE $search 
                    ORDER BY curtidas DESC, visus DESC 
                    LIMIT 18 OFFSET :indice"; // Adicionando o parâmetro de offset com segurança
            $stmt = $conexao->prepare($sql);
            $params[':indice'] = $indice; // Atribuindo o índice à consulta

            $stmt->execute($params);
            $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($livros)) {
                if ($indice == 0) {
                    resposta(200, true, "nao");
                } else {
                    resposta(200, true, "naoM");
                }
            } else {
                resposta(200, true, $livros);
            }
        }
    } catch (Exception $e) {
        resposta(200, false, "Erro: " . $e->getMessage());
    }
}


criaPesquisa($body);
?>