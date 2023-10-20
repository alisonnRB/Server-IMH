
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
if($token == "erro"){
    resposta(401, false, "não autorizado");
}else{
    oqueAlterar($token->id);
}

function oqueAlterar($id){
    $nome = false;
    $foto =  false;
    $selecao = false;

    //TODO verifica se o id veio
    if (isset($id) || !empty($id)){
        
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
        controla($nome, $foto, $selecao, $id);  
    }else{
        resposta(400, false, "há algo errado, tente movamente mais tarde :(");
    }
}
function controla($nome, $foto, $selecao, $id){
    $okFoto = false;
 
    //? cria a conexão
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(500, false, "Houve um problema ao conectar ao servidor");
    } else {

        $stm = $conexao->prepare('INSERT INTO livro_publi(user_id) VALUES (:user_id)');
        $stm->bindParam(':user_id', $id);
        $stm->execute();

        $consulta = $conexao->prepare("SELECT MAX(id) FROM livro_publi WHERE user_id = :user_id");
        $consulta->execute([':user_id' => $id]);
        $consulta = $consulta->fetchColumn();
    
        $destino = '../livros/' . $id . "/" . $_POST['nome'] . '_' . $consulta . '/'; 
    }
    

    if(!empty($_POST['classificacao'])){
        salvaClasse($conexao, $consulta);
    }

    if($nome == true){
        salvaNome($conexao, $consulta);
        if (!is_dir($destino)) {
            mkdir($destino, 0777, true);
        }
    }  

    if($foto == true){
        $Img = validar_img($_FILES);
        if($Img[0]){
            $okFoto = true;
        }else{
            resposta(400, false, $Img[1]);
        }
    }

    if($foto == true && $okFoto == true){

        $extensao = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        $arquivoTemporario = $_FILES['image']['tmp_name'];

        $nomeUnico = $id . '_' . time() . '.' . $extensao;

        salvaFoto($conexao, $nomeUnico, $consulta, $destino);
    }
    if($selecao == true){
        salaGen($conexao, $consulta);
    }
    salvaFim($conexao, $consulta);

    resposta(200, true, "Dados atualizados com sucesso.");
}

function salvaFoto($conexao, $nomeUnico, $consulta, $destino){ 

    $arquivoTemporario = $_FILES['image']['tmp_name'];

    if (move_uploaded_file($arquivoTemporario, $destino . $nomeUnico)){
        $stm = $conexao->prepare('UPDATE livro_publi SET imagem = :imagem WHERE id = :id');
        $stm->bindParam(':imagem', $nomeUnico);
        $stm->bindParam(':id', $consulta);
        $stm->execute();
    } else {
        resposta(500, false, "Algo deu errado com o arquivo.");
    }
}

function salvaNome($conexao, $consulta){
    $stm = $conexao->prepare('UPDATE livro_publi SET nome = :nome WHERE id = :id');
    $stm->bindParam(':nome', $_POST['nome']);
    $stm->bindParam(':id', $consulta);
    $stm->execute();
}

function salvaClasse($conexao, $consulta){
    $stm = $conexao->prepare('UPDATE livro_publi SET classificacao = :classificacao WHERE id = :id');
    $stm->bindParam(':classificacao', $classe);
    $stm->bindParam(':id', $consulta);
    $stm->execute();
}

function salaGen($conexao, $consulta){
        $lista = array();

        $selecao = json_decode($_POST['selecao']);
        
        foreach ($selecao as $chave => $valor) {
            if ($valor == true) {
                $lista[] = $chave;
            }
        }
        
        $lista = json_encode($lista);
        
        $stmt = $conexao->prepare('UPDATE livro_publi SET genero = ? WHERE id = ?');
        $stmt->execute([$lista, $consulta]);

}

function salvaFim($conexao, $consulta){
    $a = array();
    $a = json_encode($a);
    $data = date('Y-m-d H:i:s');
    $stm = $conexao->prepare('UPDATE livro_publi SET tempo = :tempo, texto = :texto, pronto = :pronto WHERE id = :id');
    $stm->bindParam(':tempo', $data);
    $stm->bindParam(':id', $consulta);
    $stm->bindParam(':texto', $a);
    $stm->bindParam(':pronto', $a);
    $stm->execute();
}

?>