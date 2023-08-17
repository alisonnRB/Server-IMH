
<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: *');

// TODO função que encerra as operações e envia uma resposta para a API trabalhar
function resposta($codigo, $ok, $msg) {
    header('Content-Type: application/json');
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'msg' => $msg,
    ];

    echo(json_encode($response));
    die;
}
function oqueAlterar(){
    $nome = false;
    $foto =  false;

    //TODO verifica se o id veio
    if (isset($_POST['id']) || !empty($_POST['id'])){
        
        //TODO verfica se há nome para alterar
        if(isset($_POST['nome']) && !empty($_POST['nome'])){
            $nome = true;
        }
        if (!empty($_FILES['image']['name']) && isset($_FILES['image']['name'])){
            $foto = true;
        }

    controla($nome, $foto);  
    }else{
        resposta(400, false, "há algo errado, tente movamente mais tarde :(");
    }
}
function controla($nome, $foto){
    $okFoto = false;
    $okNome = false;
    if($nome == true){
        if(verificaNome()){
            $okNome = true;
        }
    }
    if($foto == true){
        if(verificaFoto()){
            $okFoto = true;
        }
    }

    if($nome == false && $foto == false){
        resposta(400, false, "não quer mudar nada :/");
    }

    //? cria a conexão
    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

    if($foto == true && $okFoto == true){

        $extensao = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        $arquivoTemporario = $_FILES['image']['tmp_name'];


        $nomeUnico = $_POST['id'] . '_' . time() . '.' . $extensao;

        salvaFoto($conexao, $nomeUnico);
    }
    if($nome == true && $okNome == true){
        salvaNome($conexao);
    }
    resposta(200, true, "Dados atualizados com sucesso.");
    
}
function verificaNome(){
    if (!preg_match('/^[a-zA-Z0-9]*[a-zA-Z0-9]+[a-zA-Z0-9]*$/', $_POST['nome'])) {
        resposta(200, false, "Nome com caracteres inválidos");
    }else{
        return true;
    }
}
function verificaFoto(){
    //? arazena o tipo de imagem enviada
    $extensao = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    $arquivoTemporario = $_FILES['image']['tmp_name'];

    //? Criar um objeto finfo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
        
    //? Obter o tipo MIME do arquivo
    $tipoMIME = finfo_file($finfo, $arquivoTemporario);
    
    //? Fechar o objeto finfo
    finfo_close($finfo);
    
    //? Array de tipos MIME permitidos
    $tiposMIMEPermitidos = array('image/jpeg', 'image/png');

    //? informa que não é possível a imagem, pois não é um formato compatível
    if (!in_array($tipoMIME, $tiposMIMEPermitidos)) {
        resposta(400, false, "Tipo de arquivo não permitido.");
    }else{
        return true;
    }
}
function salvaFoto($conexao, $nomeUnico){

    $destino = '../imagens/';

    //? busca o caminho da foto antiga
    $fotoPerfil = $conexao->prepare("SELECT fotoPerfil FROM usuarios WHERE id = :id");
    $fotoPerfil->execute([':id' => $_POST['id']]);
    $fotoPerfil = $fotoPerfil->fetchColumn();

    $caminhoAntigo = $destino . $fotoPerfil;

    $arquivoTemporario = $_FILES['image']['tmp_name'];


    if (file_exists($caminhoAntigo) && is_file($caminhoAntigo)) {
        unlink($caminhoAntigo);
    }

    if (move_uploaded_file($arquivoTemporario, $destino . $nomeUnico)){
        //? Arquivo antigo foi apagado com sucesso
        $stmt = $conexao->prepare('UPDATE usuarios SET fotoPerfil = ? WHERE id = ?');
        $stmt->execute([$nomeUnico, $_POST['id']]);
    }else{
        resposta(500, false, "Algo deu errado com o arquivo.");
    }
}
function salvaNome($conexao){
    $stmt = $conexao->prepare('UPDATE usuarios SET nome = ? WHERE id = ?');
    $stmt->execute([$_POST['nome'], $_POST['id']]);
}

oqueAlterar();

?>