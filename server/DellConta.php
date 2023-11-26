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
if(!$token || $token == "erro"){
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
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    }

    $consulta = $conexao->prepare('SELECT senha FROM usuarios WHERE id = :id');
    $consulta->bindParam(':id', $id);
    $consulta->execute();
    $Senha = $consulta->fetchColumn();

    //!adaptar para criptografia

    if(password_verify($senhaUser, $Senha)){
        dell($id, $conexao, $body);
    }else{
        resposta(200, false, 'essa não é sua senha');
    }

}

function dell($id, $conexao, $body){
    apaga_coments($id, $conexao, $body->idioma);
    apaga_curtidas($id, $conexao, $body->idioma);
    apaga_favoritos($id, $conexao, $body->idioma);
    apaga_publi($id, $conexao, $body->idioma);
    apaga_livros($id, $conexao, $body->idioma);
    apaga_seguidores($id, $conexao, $body->idioma);
    apaga_votos($id, $conexao, $body->idioma);
    apaga_contents($id, $conexao, $body->idioma);
    apaga_foto($id, $conexao, $body->idioma);
    apaga_conta($id, $conexao, $body->idioma);

    resposta(200, true, 'adeus');
}

function apaga_coments($id, $conexao, $idioma){
    try{
    $stmt = $conexao->prepare('DELETE FROM comentarios WHERE user = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        if ($idioma == "PT"){
        resposta(200, false, 'não foi possivel apagar os comentarios');
        }
        else if ($idioma == "ES"){
            resposta(200, false, 'No se pueden eliminar comentarios');
        }
        else if ($idioma == "EN"){
            resposta(200, false, 'Unable to delete comments');
        }
    }
}
function apaga_curtidas($id, $conexao, $idioma){
    try{
    $stmt = $conexao->prepare('DELETE FROM curtidas WHERE id_user = :id');
    $stmt->execute([':id' => $id]);
    } catch(PDOExeception $e){
        if ($idioma == "PT"){
        resposta(200, false, 'não foi possivel apagar os curtidas');
        }
        else if ($idioma == "ES"){
            resposta(200, false, 'No se pueden eliminar Me gusta');
        }
        else if ($idioma == "EN"){
            resposta(200, false, 'Unable to delete likes');
        }
    }
}
function apaga_favoritos($id, $conexao, $idioma){
    try{
    $stmt = $conexao->prepare('DELETE FROM favoritos WHERE user_id = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        if ($idioma == "PT"){
            resposta(200, false, 'não foi possivel apagar os favoritos');
        }
        else if ($idioma == "ES"){
            resposta(200, false, 'No se pueden eliminar favoritos');
        }
        else if ($idioma == "EN"){
            resposta(200, false, 'Unable to delete favorites');
        }
    }
}
function apaga_publi($id, $conexao, $idioma){
    try{
    $stmt = $conexao->prepare('DELETE FROM feed_publi WHERE user_id = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        if ($idioma == "PT"){
            resposta(200, false, 'não foi possivel apagar os publicacoes');
        }
        else if ($idioma == "ES"){
            resposta(200, false, 'No se pueden eliminar publicaciones');
        }
        else if ($idioma == "EN"){
            resposta(200, false, 'Unable to delete posts');
        }
    }
}
function apaga_livros($id, $conexao, $idioma){
    try{
    $stmt = $conexao->prepare('DELETE FROM livro_publi WHERE user_id = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        if ($idioma == "PT"){
        resposta(200, false, 'não foi possivel apagar os livros');
        }
        else if ($idioma == "ES"){
            resposta(200, false, 'No se pueden eliminar libros');
        }
        else if ($idioma == "EN"){
            resposta(200, false, 'Unable to delete books');
        }
    }
}
function apaga_seguidores($id, $conexao, $idioma){
    try{
    $stmt = $conexao->prepare('DELETE FROM seguidores WHERE user_id = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        if ($idioma == "PT"){
            resposta(200, false, 'não foi possivel apagar os seguidores');
        }
        else if ($idioma == "ES"){
            resposta(200, false, 'No se pueden eliminar seguidores');
        }
        else if ($idioma == "EN"){
            resposta(200, false, 'Unable to delete followers');
        }
    }
}
function apaga_votos($id, $conexao, $idioma){
    try{
    $stmt = $conexao->prepare('DELETE FROM votacao WHERE user_id = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        if ($idioma == "PT"){
            resposta(200, false, 'não foi possivel apagar os votos');
        }
        else if ($idioma == "ES"){
            resposta(200, false, 'no fue posible borrar los votos');
        }
        else if ($idioma == "EN"){
            resposta(200, false, 'unable to delete the votes');
        }
    }
}
function apaga_contents($id, $conexao, $idioma){
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
                    apaga_contents($caminhoCompleto, $conexao, $idioma);
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
        if ($idioma == "PT"){
            resposta(200, false, 'não foi possivel apagar os arquivos');
        }
        else if ($idioma == "ES"){
            resposta(200, false, 'No se pueden eliminar archivos');
        }
        else if ($idioma == "EN"){
            resposta(200, false, 'Unable to delete files');
        }
    }
}
function apaga_foto($id, $conexao, $idioma){
    try{
    $fotoPerfil = $conexao->prepare("SELECT fotoPerfil FROM usuarios WHERE id = :id");
    $fotoPerfil->execute([':id' => $id]);
    $fotoPerfil = $fotoPerfil->fetchColumn();

    $caminho = '../imagens/' . $fotoPerfil;
    if (file_exists($caminho) && is_file($caminho)) {
        unlink($caminho);
    }
    }catch(Exeception $e){
        if ($idioma == "PT"){
        resposta(200, false, 'não foi possivel apagar a foto');
        }
        else if ($idioma == "ES"){
            resposta(200, false, 'No se puede eliminar la foto');
        }
        else if ($idioma == "EN"){
            resposta(200, false, 'Unable to delete photo');
        }
    }
}

function apaga_conta($id, $conexao, $idioma){
    try{
    $stmt = $conexao->prepare('DELETE FROM usuarios WHERE id = :id');
    $stmt->execute([':id' => $id]);
    }catch(PDOExeception $e){
        if ($idioma == "PT"){
        resposta(200, false, 'não foi possivel apagar a conta');
        }
        else if ($idioma == "ES"){
            resposta(200, false, 'No se puede eliminar la cuenta');
        }
        else if ($idioma == "EN"){
            resposta(200, false, 'Unable to delete account');
        }
    }
}





?>