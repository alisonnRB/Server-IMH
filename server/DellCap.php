<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";
include "./token/decode_token.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->idUser);
if($token == "erro"){
    resposta(401, true, "não autorizado");
}else{
    verifica($token->id,$body);
}


function verifica($idUser, $body){
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {
    $consulta = $conexao->prepare('SELECT texto, nome, pronto FROM livro_publi WHERE id = :id');
    $consulta->execute([':id' => $body->id]);

    $linha = $consulta->fetch(PDO::FETCH_ASSOC);

    $caminhoPasta = '../livros/' . $idUser . '/' . $linha['nome'] . '_' . $body->id . '/';
    $nomeArquivo = $caminhoPasta . $body->id . '_' . $idUser . '_' . $body->cap . '.html';

    $texto= json_decode($linha['texto'], true);
    $public= json_decode($linha['pronto'], true);

    if(file_exists($nomeArquivo)){
        unlink($nomeArquivo);
        
    }
    if (reorderList($texto, $public, $body, $conexao)) {
        if (!empty($texto)) {
            Renomeando($caminhoPasta, $texto, $body, $idUser);
        }
        resposta(200, true, "certo");
    }
}
}
function reorderList($texto, $public, $body, $conexao) {
    if (isset($texto[$body->cap])) {
        unset($texto[$body->cap]);
    }
    if (isset($public[$body->cap])) {
        unset($public[$body->cap]);
    }

    $texto = array_values($texto);
    $public = array_values($public);

    if(count($texto) <= 0){
        $texto = array();
    }else{
        $texto = array_combine(range(1, count($texto)), $texto);
    }

    if(count($public) <= 0){
        $public = array();
    }else{
        $public = array_combine(range(1, count($public)), $public);
    }

    $texto = json_encode($texto);
    $public = json_encode($public);

    $stmt = $conexao->prepare("UPDATE livro_publi SET texto = ?, pronto = ? WHERE id = ?");
    $stmt->execute([$texto, $public, $body->id]);
    return true;
}

function Renomeando($caminhoPasta, $texto, $body, $idUser){
    $keys = count($texto);
    $contador = 0;
    for ($i = 0; $i <= $keys; $i++) {
        $nomeArquivoAntigo = $caminhoPasta . $body->id . '_' . $idUser . '_' . $i . '.html';
        if (file_exists($nomeArquivoAntigo)) {
            $contador++;        
            $novoNomeArquivo = $caminhoPasta . $body->id . '_' . $idUser . '_' . $contador . '.html';
            rename($nomeArquivoAntigo, $novoNomeArquivo);
        }
    }
}

?>