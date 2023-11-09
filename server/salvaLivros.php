<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/decode_token.php";


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');


$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->idUser);
if($token == "erro"){
    resposta(401, false, "não autorizado");
}else{
    qualSave($token->id,$body);
}


function qualSave($user_id, $body) {
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
    if($body->cap == 0){
        $consulta = $conexao->prepare('SELECT user_id FROM livro_publi WHERE id = :id');
        $consulta->execute([':id' => $body->id]);

        $linha = $consulta->fetch(PDO::FETCH_ASSOC);

        if($linha['user_id'] != $user_id){
            resposta(401, false, "não é seu livro");
        }else{
        SaveSinopse($body, $conexao, $user_id);}
    }
    elseif ($body->cap >= 1) {
        PrepareCap($body, $conexao, $user_id);
    }
}
}
function PrepareCap($body, $conexao, $user_id){

    $consulta = $conexao->prepare('SELECT user_id, texto, nome FROM livro_publi WHERE id = :id');
    $consulta->execute([':id' => $body->id]);

    $linha = $consulta->fetch(PDO::FETCH_ASSOC);

    if($linha['user_id'] != $user_id){
        resposta(401, false, "não é seu livro");
    }else{
        if ($linha) {
        $caminhoPasta = '../livros/' . $user_id . '/' . $linha['nome'] . '_' . $body->id . '/';

        $titulo = json_decode($linha['texto'], true); // Decodificar JSON existente para array associativo

        // Atualizar ou adicionar o título correspondente a body->cap
        $titulo[$body->cap] = $body->titulo;

        // Codificar de volta para JSON
        $tituloJSON = json_encode($titulo);


        $stmt = $conexao->prepare("UPDATE livro_publi SET texto = ? WHERE id = ?");
        $stmt->execute([$tituloJSON, $body->id]);

        $nomeArquivo = $caminhoPasta . $body->id . '_' . $user_id . '_' . $body->cap . '.html';

        // Verificar se o arquivo já existe antes de criar ou atualizar
        if (file_exists($nomeArquivo)) {
            unlink($nomeArquivo); // Remover o arquivo antigo do cap
        }

        file_put_contents($nomeArquivo, $body->text); // Criar ou atualizar o arquivo

        resposta(200, true, 'certo');
        } else {
        resposta(404, false, 'não foi encontrado');
        }
    } 
}


function SaveSinopse($body, $conexao, $user_id){
    $stmt = $conexao->prepare("UPDATE livro_publi SET sinopse = ? WHERE id = ?");
    $stmt = $stmt->execute([$body->text, $body->id]);

    resposta(200, true, 'certo');
}

?>