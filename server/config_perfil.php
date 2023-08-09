<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

function resposta($codigo, $ok, $msg) {
    header('Content-Type: application/json');
    http_response_code($codigo);
    echo(json_encode([
        'ok' => $ok,
        'msg' => $msg,
    ]));
    die;
}


$conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

$body = file_get_contents('php://input');



    // Verifica se um arquivo foi enviado e se não houve erros no upload

        // Pasta onde a imagem será salva
        $pastaDestino = '../imagens/';

        // Obtem a extensão do arquivo
        $extensao = pathinfo($body->file['image']['name'], PATHINFO_EXTENSION);

        // Cria um nome único baseado no ID do usuário e um timestamp
        $nomeUnico = $body->id . '_' . time() . '.' . $extensao;

        // Caminho completo para o arquivo na pasta de destino
        $caminhoDestino = $pastaDestino . $nomeUnico;

        // Move o arquivo temporário para a pasta de destino com o nome único
        if (move_uploaded_file($body->file['image']['tmp_name'], $caminhoDestino)) {
            // Aqui você pode realizar a lógica para atualizar o nome do usuário no banco de dados.
            // Por exemplo, se você estiver usando uma conexão com banco de dados:

            // $stmt = $conexao->prepare('UPDATE usuarios SET nome = ? WHERE id = ?');
            // $stmt->execute([$nome, $id]);

            // Neste exemplo, vou apenas retornar uma resposta JSON simples para indicar sucesso.
            resposta(200, true, 'Dados atualizados com sucesso!');
        } else {
            resposta(500, false, 'Falha ao salvar a imagem.');
        }

?>