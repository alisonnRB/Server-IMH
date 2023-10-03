
<?php

date_default_timezone_set('America/Sao_Paulo');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
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
    $selecao = false;

    //TODO verifica se o id veio

        
        //TODO verfica se há nome para alterar
        if(isset($_POST['nome']) && !empty($_POST['nome'])){
            $nome = true;
        }
        if (!empty($_FILES['image']['name']) && isset($_FILES['image']['name'])){
            $foto = true;
        }
        if (!empty($_POST['selecao']) && isset($_POST['selecao'])){
            $selecao = true;
        }
        controla($nome, $foto, $selecao);  
}
function controla($nome, $foto, $selecao){
    $okFoto = false;
    if($foto == true){
        if(verificaFoto()){
            $okFoto = true;
        }
    }
    //? cria a conexão
    $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

    if(!empty($_POST['classificacao'])){
        salvaClasse($conexao);
    }
    if(!empty($_POST['color'])){
        salvaTema($conexao);
    }
    if($foto == true && $okFoto == true){

        $extensao = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        $arquivoTemporario = $_FILES['image']['tmp_name'];

        $nomeUnico = $_POST['id'] . '_' . time() . '.' . $extensao;

        salvaFoto($conexao, $nomeUnico);
    }
    if($nome == true){
        salvaNome($conexao);
    }
    if($selecao == true){
        salvaGen($conexao);
    }
    salvaFim($conexao);
    salvaPubliFin($conexao);

    resposta(200, true, "Dados atualizados com sucesso.");
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
    $destino = '../livros/' . $_POST['id'] . "/" . $_POST['nome'] . '_' . $_POST['idLivro'] . '/';

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
        resposta(500, false, "Algo deu errado com o arquivo.");
    }
}
function salvaNome($conexao){
    $consulta = $conexao->prepare('SELECT nome FROM livro_publi WHERE id = :id');
    $consulta->execute([':id' => $_POST['idLivro']]);
    $consulta = $consulta->fetchColumn();

    $caminhoAntigo = '../livros/' . $_POST['id'] . "/" . $consulta . '_' . $_POST['idLivro'];
    $caminhoNovo = '../livros/' . $_POST['id'] . "/" . $_POST['nome'] . '_' . $_POST['idLivro'];

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
    $stm = $conexao->prepare('UPDATE livro_publi SET tema = :tema, tema2 = :tema2 WHERE id = :id');
    $stm->bindParam(':tema', $_POST['color']);
    $stm->bindParam(':tema2', $_POST['color2']);
    $stm->bindParam(':id', $_POST['idLivro']);
    $stm->execute();
}

oqueAlterar();

?>