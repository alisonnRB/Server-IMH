<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/decode_token.php";
include "./validações/validacoes.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->id);
if($token == "erro"){
    resposta(200, false, "não autorizado");
}else{
    Dell_verify($token->id, $body);
}

function Dell_verify($id, $body) {
    //!validar entrada senha

    $senhaUser = validar_senha($body->senha);
    if ($senhaUser[0] == true){
        $senhaUser = $senhaUser[1];
    } else {
        resposta (200, false, $senhaUser[1]);
    } 
    
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    }

    $consulta = $conexao->prepare('SELECT senha FROM usuarios WHERE id = :id');
    $consulta->bindParam(':id', $id);
    $consulta->execute();
    $Senha = $consulta->fetchColumn();

    //!adaptar para criptografia

    if(password_verify($senhaUser, $Senha)){
        dell($id, $conexao);
    }else{
        resposta(200, false, 'essa não é sua senha');
    }

}

function dell($id, $conexao){
    apaga_coments($id, $conexao);
    apaga_curtidas($id, $conexao);
    apaga_favoritos($id, $conexao);
    apaga_publi($id, $conexao);
    apaga_livros($id, $conexao);
    apaga_seguidores($id, $conexao);
    apaga_votos($id, $conexao);
    apaga_contents($id, $conexao);
    apaga_foto($id, $conexao);
    apaga_conta($id, $conexao);

    resposta(200, true, 'adeus');
}

function apaga_coments($id, $conexao){
    try{
    $stmt = $conexao->prepare('DELETE FROM comentarios WHERE user = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        resposta(200, false, 'não foi possivel apagar os comentarios');
    }
}
function apaga_curtidas($id, $conexao){
    try{
    $stmt = $conexao->prepare('DELETE FROM curtidas WHERE id_user = :id');
    $stmt->execute([':id' => $id]);
    } catch(PDOExeception $e){
        resposta(200, false, 'não foi possivel apagar os curtidas');
    }
}
function apaga_favoritos($id, $conexao){
    try{
    $stmt = $conexao->prepare('DELETE FROM favoritos WHERE user_id = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        resposta(200, false, 'não foi possivel apagar os favoritos');
    }
}
function apaga_publi($id, $conexao){
    try{
    $stmt = $conexao->prepare('DELETE FROM feed_publi WHERE user_id = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        resposta(200, false, 'não foi possivel apagar os publicacoes');
    }
}
function apaga_livros($id, $conexao){
    try{
    $stmt = $conexao->prepare('DELETE FROM livro_publi WHERE user_id = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        resposta(200, false, 'não foi possivel apagar os livros');
    }
}
function apaga_seguidores($id, $conexao){
    try{
    $stmt = $conexao->prepare('DELETE FROM seguidores WHERE user_id = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        resposta(200, false, 'não foi possivel apagar os seguidores');
    }
}
function apaga_votos($id, $conexao){
    try{
    $stmt = $conexao->prepare('DELETE FROM votacao WHERE user_id = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        resposta(200, false, 'não foi possivel apagar os votos');
    }
}
function apaga_contents($id, $conexao){
    try{
    $caminho = '../livros/' . $id;
    if (is_dir($caminho)) {
        // Abre o diretório
        $diretorio = opendir($caminho);

        // Loop através dos arquivos
        while (($arquivo = readdir($diretorio)) !== false) {
            // Ignora os diretórios pai e atual
            if ($arquivo != "." && $arquivo != "..") {
                $caminhoCompleto = $caminho . '/' . $arquivo;

                // Verifica se é um diretório e apaga recursivamente
                if (is_dir($caminhoCompleto)) {
                    apagarPasta($caminhoCompleto);
                } else {
                    // Se for um arquivo, o exclui
                    unlink($caminhoCompleto);
                }
            }
        }

        // Fecha o diretório
        closedir($diretorio);

        // Remove o diretório em si
        rmdir($caminho);
    }
    }catch(Exeception $e){
        resposta(200, false, 'não foi possivel apagar os arquivos');
    }
}
function apaga_foto($id, $conexao){
    try{
    $fotoPerfil = $conexao->prepare("SELECT fotoPerfil FROM usuarios WHERE id = :id");
    $fotoPerfil->execute([':id' => $id]);
    $fotoPerfil = $fotoPerfil->fetchColumn();

    $caminho = '../imagens/' . $fotoPerfil;
    if (file_exists($caminho) && is_file($caminho)) {
        unlink($caminho);
    }
    }catch(Exeception $e){
        resposta(200, false, 'não foi possivel apagar a foto');
    }
}

function apaga_conta($id, $conexao){
    try{
    $stmt = $conexao->prepare('DELETE FROM usuarios WHERE id = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        resposta(200, false, 'não foi possivel apagar a conta');
    }
}





?>