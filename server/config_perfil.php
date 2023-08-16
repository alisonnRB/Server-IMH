<?php
    //! apagar a imagem antiga caso seja alterada

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: *');

//TODO função que encerra as operações e enciar umas resposta para a api trabalhar
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

$conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");


function armazena($id, $imagem, $nome, $nomeArq, $destino){
    //? verifica se há imagem a ser salva
    if($image == false){

        //? verifica se ha nome a ser salvo
        if(empty($nome)){
            resposta(500, false, "Você deve não quer mudar nada? :)")
        }else{
            //TODO verifica se há caracteres invalidos
            if (!preg_match('/^[a-zA-Z0-9]/', $nome)) {
                resposta(200, false, "nome com caracteres inválidos");
            }else{
                //?salva imagem e nome no banco
                $stmt = $conexao->prepare('UPDATE usuarios SET nome = ?, fotoPerfil = ? WHERE id = ?');
                $stmt->execute([$nome, $nomeArq, $id]);
                resposta(200, true, "Dados atualizados com sucesso.");  
            }
        }
    }else{
        //?tenta mover arquivo e lida com o erro
        if (move_uploaded_file($imagem, $destino)) {

            //? verifica se existe algo no campo nome
            if(empty($nome)){

                //? se não houver nome salva apenas o caminho da imagem
                $stmt = $conexao->prepare('UPDATE usuarios SET fotoPerfil = ? WHERE id = ?');
                $stmt->execute([$nomeArq, $id]);

                resposta(200, true, "Dados atualizados com sucesso.");
            }else{
                //TODO verifica se há caracteres invalidos
                if (!preg_match('/^[a-zA-Z0-9]/', $nome)) {
                    resposta(200, false, "nome com caracteres inválidos");
                }else{
                    //?salva imagem e nome no banco
                    $stmt = $conexao->prepare('UPDATE usuarios SET nome = ?, fotoPerfil = ? WHERE id = ?');
                    $stmt->execute([$nome, $nomeArq, $id]);
                    resposta(200, true, "Dados atualizados com sucesso.");  
                }
            }
        }else{
        resposta(500, false, "algo deu errado, tente mais tarde");
        }
    }
}


//TODO verifica a existencia dos conteudos da pasta temporaria e a salva
if (isset($_FILES['image']) && isset($_POST['id']) && isset($_POST['nome'])){
    
    
    $body = $_POST;

    //? caminho para a pasta imagens do server
    $pastaDestino = '../imagens/';
    //TODO verifica se há algo
    if(!empty($_FILES['image'])){
        
        //? arazena o tipo de imagem enviada
        $extensao = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        //? Criar um objeto finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        //? Obter o tipo MIME do arquivo
        $tipoMIME = finfo_file($finfo, $arquivoTemporario);

        //? Fechar o objeto finfo
        finfo_close($finfo);

        //? Array de tipos MIME permitidos
        $tiposMIMEPermitidos = array('image/jpeg', 'image/png');

        //TODO informa que não é possivel a imagem pois não é um formato compativel
        if (!in_array($tipoMIME, $tiposMIMEPermitidos)) {
            resposta(400, false, "Tipo de arquivo não permitido.");

        }else{
            $arquivoTemporario = $_FILES['image']['tmp_name'];    
                
            //? constroi e guarda um novo nome para a imagem
            $nomeUnico = $body['id'] . '_' . time() . '.' . $extensao;

            //?chama função
            armazena($body['id'],$_FILES['image']['tmp_name'], $body['nome'], $nomeUnico, $caminhoDestino);
        }
    }else{
        armazena($body['id'],false, $body['nome'], false, false);
    }
    
} else {
    resposta(400, false, "Você deve não quer mudar nada? :)");
}

?>
