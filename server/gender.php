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

if ($token == "erro") {
    resposta(200, false, "Não autorizado");
} else {
    quaisGeneros($body->idioma);
}

function quaisGeneros($nomeColuna)
{
    try {
        $conexao = conecta_bd();
        if (!$conexao) {
            resposta(200, false, "Houve um problema ao conectar ao servidor");
        } else {

            // Use uma declaração preparada para a coluna
            $stmt = $conexao->prepare("SELECT id, {$nomeColuna} FROM genero");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $list = array();

            foreach ($result as $row) {
                $list[$row['id']] = $row[$nomeColuna];
            }

            resposta(200, true, $list);
        }
    } catch (PDOException $e) {
        resposta(200, false, "Erro no servidor: " . $e->getMessage());
    }
}
?>