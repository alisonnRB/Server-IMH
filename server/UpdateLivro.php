
<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";
include "./token/decode_token.php";

date_default_timezone_set('America/Sao_Paulo');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: *');

$token = decode_token($_POST['id']);
if(!$token || $token == "erro"){
    resposta(200, false, "não autorizado");
}else{
    oqueAlterar($token->id);
}

//! Verificar entrada string, filtrar e etc. assim como fazer apenas a variavel jafiltrada passar pelas funções
function oqueAlterar($id){
    $nome = false;
    $foto =  false;
    $selecao = false;

    //TODO verifica se o id veio

        
        //TODO verfica se há nome para alterar
        if(isset($_POST['nome']) && !empty($_POST['nome'])){
            $nome = true;
        }
        if (!empty($_FILES['image']['name']) && isset($_FILES['image']['name']) && $_FILES['image']['name'] != ''){
            $foto = true;
        }
        if (!empty($_POST['selecao']) && isset($_POST['selecao'])){
            $selecao = true;
        }
        controla($nome, $foto, $selecao, $id);  
}
function controla($nome, $foto, $selecao, $id){
    $okFoto = false;
    if($foto == true){
        if(verificaFoto()){
            $okFoto = true;
        }
    }
    //? cria a conexão
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {

    if(!empty($_POST['classificacao'])){
        salvaClasse($conexao);
    }
    if(!empty($_POST['color'])){
        salvaTema($conexao);
    }
    if($foto == true && $okFoto == true){

        $extensao = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        $arquivoTemporario = $_FILES['image']['tmp_name'];

        $nomeUnico = $id . '_' . time() . '.' . $extensao;

        salvaFoto($conexao, $nomeUnico, $id);
    }
    if($nome == true){
        salvaNome($conexao, $id);
    }
    if($selecao == true){
        salvaGen($conexao);
    }
    if(!empty($_POST['tags'])){
        salvaTags($conexao);
    }
    salvaFim($conexao);
    salvaPubliFin($conexao);

    resposta(200, true, "Dados atualizados com sucesso.");
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
        resposta(200, false, "Tipo de arquivo não permitido.");
    }else{
        return true;
    }
}
function salvaFoto($conexao, $nomeUnico, $id){ 
    $destino = '../livros/' . $id . "/" . $_POST['nome'] . '_' . $_POST['idLivro'] . '/';

    if (!is_dir($destino)) {
        mkdir($destino, 0777, true);
    }
    
    $consulta = $conexao->prepare('SELECT imagem FROM livro_publi WHERE id = :id');
    $consulta->execute([':id' => $_POST['idLivro']]);
    $consulta = $consulta->fetchColumn();

    $caminhoAntigo = $destino . $consulta;

    if (file_exists($caminhoAntigo) && is_file($caminhoAntigo)) {
        unlink($caminhoAntigo);
    }

    $arquivoTemporario = $_FILES['image']['tmp_name'];

    if (move_uploaded_file($arquivoTemporario, $destino . $nomeUnico)){
        $stm = $conexao->prepare('UPDATE livro_publi SET imagem = :imagem WHERE id = :id');
        $stm->bindParam(':imagem', $nomeUnico);
        $stm->bindParam(':id', $_POST['idLivro']);
        $stm->execute();
    } else {
        resposta(200, false, "Algo deu errado com o arquivo.");
    }
}
function salvaNome($conexao, $id){
    $consulta = $conexao->prepare('SELECT nome FROM livro_publi WHERE id = :id');
    $consulta->execute([':id' => $_POST['idLivro']]);
    $consulta = $consulta->fetchColumn();

    $caminhoAntigo = '../livros/' . $id . "/" . $consulta . '_' . $_POST['idLivro'];
    $caminhoNovo = '../livros/' . $id . "/" . $_POST['nome'] . '_' . $_POST['idLivro'];

    if (rename($caminhoAntigo, $caminhoNovo)) {
        $stm = $conexao->prepare('UPDATE livro_publi SET nome = :nome WHERE id = :id');
        $stm->bindParam(':nome', $_POST['nome']);
        $stm->bindParam(':id', $_POST['idLivro']);
        $stm->execute();
    }
}
function salvaClasse($conexao){
    $stm = $conexao->prepare('UPDATE livro_publi SET classificacao = :classificacao WHERE id = :id');
    $stm->bindParam(':classificacao', $_POST['classificacao']);
    $stm->bindParam(':id', $_POST['idLivro']);
    $stm->execute();
}
function salvaGen($conexao){
    $lista = array();

    $selecao = json_decode($_POST['selecao']);
        
    foreach ($selecao as $chave => $valor) {
        if ($valor == true) {
            $lista[] = $chave;
        }
        }
        
    $lista = json_encode($lista);
        
    $stmt = $conexao->prepare('UPDATE livro_publi SET genero = ? WHERE id = ?');
    $stmt->execute([$lista, $_POST['idLivro']]);

}
function salvaFim($conexao){
    $data = date('Y-m-d H:i:s');
    $stm = $conexao->prepare('UPDATE livro_publi SET tempo = :tempo WHERE id = :id');
    $stm->bindParam(':tempo', $data);
    $stm->bindParam(':id', $_POST['idLivro']);
    $stm->execute();
}
function salvaPubliFin($conexao){
    $publico = $_POST['publico'] == 'true' ? 1 : 0;
    $finalizado = $_POST['finalizado'] ==  'true' ? 1 : 0;

    $stm = $conexao->prepare('UPDATE livro_publi SET publico = :publico, finalizado = :finalizado WHERE id = :id');
    $stm->bindParam(':publico', $publico, PDO::PARAM_INT);
    $stm->bindParam(':finalizado', $finalizado, PDO::PARAM_INT);
    $stm->bindParam(':id', $_POST['idLivro']);
    $stm->execute();
}
function salvaTema($conexao){
    $stm = $conexao->prepare('UPDATE livro_publi SET tema = :tema WHERE id = :id');
    $stm->bindParam(':tema', $_POST['color']);
    $stm->bindParam(':id', $_POST['idLivro']);
    $stm->execute();
}
function salvaTags($conexao){
    $stm = $conexao->prepare('UPDATE livro_publi SET tags = :tags WHERE id = :id');
    $stm->bindParam(':tags', $_POST['tags']);
    $stm->bindParam(':id', $_POST['idLivro']);
    $stm->execute();
}

?>