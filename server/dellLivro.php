<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

function resposta($codigo, $ok) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
    ];

    echo(json_encode($response));
    die;
}

function verifica($body){
    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

    $consulta = $conexao->prepare('SELECT nome FROM livro_publi WHERE id = :id');
    $consulta->execute([':id' => $body->idLivro]);
    $consulta = $consulta->fetchColumn();

    if ($consulta !== false) {
        $pasta = '../livros/' . $body->id . '/' . $consulta . '_' . $body->idLivro . '/';
        
        // Exclui todos os arquivos dentro da pasta
        $files = glob($pasta . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        // Exclui a pasta vazia
        if (is_dir($pasta)) {
            rmdir($pasta);
        }

    $stmt = $conexao->prepare('DELETE FROM livro_publi WHERE id = :id');
    $stmt->execute([':id' => $body->idLivro]);

    resposta(200, true);
    }
}


$body = file_get_contents('php://input');
$body = json_decode($body);

verifica($body);
?>