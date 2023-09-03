
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
    if (isset($_POST['id']) || !empty($_POST['id'])){
        
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
    }else{
        resposta(400, false, "há algo errado, tente movamente mais tarde :(");
    }
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

    $stm = $conexao->prepare('INSERT INTO livro_publi(user_id) VALUES (:user_id)');
    $stm->bindParam(':user_id', $_POST['id']);
    $stm->execute();

    $consulta = $conexao->prepare("SELECT MAX(id) FROM livro_publi WHERE user_id = :user_id");
    $consulta->execute([':user_id' => $_POST['id']]);
    $consulta = $consulta->fetchColumn();


    if($foto == true && $okFoto == true){

        $extensao = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        $arquivoTemporario = $_FILES['image']['tmp_name'];

        $nomeUnico = $_POST['id'] . '_' . time() . '.' . $extensao;

        salvaFoto($conexao, $nomeUnico, $consulta);
    }
    if($nome == true){
        salvaNome($conexao, $consulta);
    }
    if($selecao == true){
        salaGen($conexao, $consulta);
    }
    salvaFim($conexao, $consulta);

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
function salvaFoto($conexao, $nomeUnico, $consulta){
    $destino = '../livros/' . $_POST['id'] . "/";
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
    $data = date('Y-m-d H:i:s');
    $stm = $conexao->prepare('UPDATE livro_publi SET tempo = :tempo WHERE id = :id');
    $stm->bindParam(':tempo', $data);
    $stm->bindParam(':id', $consulta);
    $stm->execute();
}
oqueAlterar();

?>