
<?php

include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";
include "./token/decode_token.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');

// TODO função que encerra as operações e envia uma resposta para a API trabalhar
$token = isset($_POST["id"]) ? decode_token($_POST["id"]) : null;

if (!$token || $token == "erro") {
    resposta(200, false, "Não autorizado");
} else {
    oque_alterar($token->id);
}



function oque_alterar($id){
    //! Verificar as entradas string, filtrar e etc
    $nome = false;
    $foto =  false;

    
    //TODO verifica se o id veio
    if (isset($id) || !empty($id)){
        
        //TODO verfica se há nome para alterar
        if(isset($_POST['nome']) && !empty($_POST['nome'])){
            $nome = true;
        }
        if (!empty($_FILES['image']['name']) && isset($_FILES['image']['name'])){
            $foto = true;
        }

        controla($nome, $foto, $id, $_POST['idioma']);  
    }else{
        if ($_POST['idioma'] == "PT"){
            resposta(200, false, "há algo errado, tente novamente mais tarde :(");
        }
        else if ($_POST['idioma'] == "ES"){
            resposta(200, false, "Hay algo mal, inténtalo de nuevo más tarde :(");
        }
        else if ($_POST['idioma'] == "EN"){
            resposta(200, false, "there is something wrong, try again later :(");
        }
    }
}

function controla($nome, $foto, $id, $idioma){

    $okFoto = false;
    $okNome = false;

    if($nome){
        $Nome = validar_nome($_POST['nome']);
        if($Nome[0]){
            $okNome = true;
        }else{
            resposta(200, false, $Nome[1]);
        }
    }

    if($foto == true){
        $Img = validar_img($_FILES);
        if($Img[0]){
            $okFoto = true;
        }else{
            resposta(200, false, $Img[1]);
        }
    }

    if($nome == false && $foto == false){
        if ($_POST['idioma'] == "PT"){
            resposta(200, false, "não quer mudar nada :/");
        }
        else if ($_POST['idioma'] == "ES"){
            resposta(200, false, "No quieres cambiar nada :/");
        }
        else if ($_POST['idioma'] == "EN"){
            resposta(200, false, "Don't want to change anything :/");
        }
    }

    $conexao = conecta_bd();
    if(!$conexao){
        resposta(200, false, "algo errado no server");
    }else{
        if($foto == true && $okFoto == true){

        $extensao = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $arquivoTemporario = $_FILES['image']['tmp_name'];
        $nomeUnico = $id . '_' . time() . '.' . $extensao;

        salvaFoto($conexao, $nomeUnico, $id);
        }

    if($nome == true && $okNome == true){
        
        salvaNome($conexao, $Nome[0], $id);
    }
    }
        resposta(200, true, "Dados atualizados com sucesso.");  
}

function salvaFoto($conexao, $nomeUnico, $id){

    $destino = '../imagens/';

    //? busca o caminho da foto antiga
    $fotoPerfil = $conexao->prepare("SELECT fotoPerfil FROM usuarios WHERE id = :id");
    $fotoPerfil->execute([':id' => $id]);
    $fotoPerfil = $fotoPerfil->fetchColumn();

    $caminhoAntigo = $destino . $fotoPerfil;

    $arquivoTemporario = $_FILES['image']['tmp_name'];


    if (file_exists($caminhoAntigo) && is_file($caminhoAntigo)) {
        unlink($caminhoAntigo);
    }

    if (move_uploaded_file($arquivoTemporario, $destino . $nomeUnico)){
        //? Arquivo antigo foi apagado com sucesso
        $stmt = $conexao->prepare('UPDATE usuarios SET fotoPerfil = ? WHERE id = ?');
        $stmt->execute([$nomeUnico, $id]);
    }else{
        if ($_POST['idioma'] == "PT"){
            resposta(200, false, "Algo deu errado com o arquivo.");
        }
        else if ($_POST['idioma'] == "ES"){
            resposta(200, false, "Algo salió mal con el archivo.");
        }
        else if ($_POST['idioma'] == "EN"){
            resposta(200, false, "Something went wrong with the file.");
        }
    }
}
function salvaNome($conexao, $Nome, $id){
    $stmt = $conexao->prepare('UPDATE usuarios SET nome = ? WHERE id = ?');
    $stmt->execute([$_POST['nome'], $id]);
}


?>