<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/decode_token.php";

// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

// Recebe o corpo da requisição e decodifica o JSON
$body = file_get_contents('php://input');
$body = json_decode($body);

// Verifica se o JSON foi corretamente decodificado
if (json_last_error() !== JSON_ERROR_NONE) {
    resposta(400, false, "Erro ao processar JSON: " . json_last_error_msg());
    exit();
}

// Verifica a presença do 'id' e 'idioma' no corpo da requisição
if (empty($body->id) || empty($body->idioma)) {
    resposta(400, false, "Parâmetros 'id' ou 'idioma' faltando.");
    exit();
}

// Tenta decodificar o token
$token = decode_token($body->id);

// Verifica se o token é inválido
if ($token == "erro") {
    resposta(200, false, "Não autorizado");
    exit();
} else {
    quaisGeneros($body->idioma);
}

function quaisGeneros($nomeColuna)
{
    // Valida a coluna para garantir que não seja uma injeção de SQL
    $colunasPermitidas = ['idioma1', 'idioma2']; // Substitua com suas colunas reais
    if (!in_array($nomeColuna, $colunasPermitidas)) {
        resposta(400, false, "Coluna inválida.");
        exit();
    }

    try {
        $conexao = conecta_bd();
        if (!$conexao) {
            resposta(200, false, "Houve um problema ao conectar ao servidor");
            exit();
        }

        // Use uma declaração preparada para a coluna
        $stmt = $conexao->prepare("SELECT id, {$nomeColuna} FROM genero");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $list = array();
        foreach ($result as $row) {
            $list[$row['id']] = $row[$nomeColuna];
        }

        resposta(200, true, $list);
    } catch (PDOException $e) {
        resposta(200, false, "Erro no servidor: " . $e->getMessage());
    }
}
?>