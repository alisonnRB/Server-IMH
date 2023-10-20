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
    verifica($token->id, $body);
}

function verifica ($id, $body){
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
    $consulta = $conexao->prepare('SELECT nome FROM livro_publi WHERE id = :id');
    $consulta->execute([':id' => $body->idLivro]);
    $consulta = $consulta->fetchColumn();

    if ($consulta !== false) {
        $pasta = '../livros/' . $id . '/' . $consulta . '_' . $body->idLivro . '/';
        
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

    resposta(200, true, "certo");
    }
}
}


?>