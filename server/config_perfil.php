
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

$conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");
$body = $_POST;

function armazena($id, $imagem, $nome, $nomeArq, $destino, $conexao){

    $fotoPerfil = $conexao->prepare("SELECT fotoPerfil FROM usuarios WHERE id = :id");
    $fotoPerfil->execute([':id' => $id]);
    $fotoPerfil = $fotoPerfil->fetchColumn();

    //? verifica se há imagem a ser salva
    if($imagem == false){

        //? verifica se ha nome a ser salvo
        if(empty($nome)){
            resposta(500, false, "Você não quer mudar nada? :)");
        } else {
            // TODO verifica se há caracteres inválidos
            if (!preg_match('/^[a-zA-Z0-9]/', $nome)) {
                resposta(200, false, "Nome com caracteres inválidos");
            } else {
                //? salva nome no banco
                $stmt = $conexao->prepare('UPDATE usuarios SET nome = ? WHERE id = ?');
                $stmt->execute([$nome, $id]);
                resposta(200, true, "Dados atualizados com sucesso.");  
            }
        }
    } else {
        //? tenta mover arquivo e lida com o erro
        if (move_uploaded_file($imagem, $destino . $nomeArq)) {

            //? verifica se existe algo no campo nome
            if(empty($nome)) {
                
                //? Se houver um arquivo de imagem antigo
                if (!empty($fotoPerfil)) {
                    $caminhoAntigo = $destino . $fotoPerfil;

                    //? Deleta o arquivo antigo
                    if (file_exists($caminhoAntigo) && unlink($caminhoAntigo)) {
                        //? Arquivo antigo foi apagado com sucesso
                        $stmt = $conexao->prepare('UPDATE usuarios SET fotoPerfil = ? WHERE id = ?');
                        $stmt->execute([$nomeArq, $id]);
                        resposta(200, true, "Dados atualizados com sucesso.");
                    } else {
                        resposta(200, true, "Algo deu errado ao excluir o arquivo antigo.");
                    }
                } else {
                    $stmt = $conexao->prepare('UPDATE usuarios SET fotoPerfil = ? WHERE id = ?');
                    $stmt->execute([$nomeArq, $id]);
                    resposta(200, true, "Dados atualizados com sucesso.");
                }
                
            } else {
                //TODO verifica se há caracteres inválidos no nome
                if (!preg_match('/^[a-zA-Z0-9]/', $nome)) {
                    resposta(200, false, "Nome com caracteres inválidos");
                } else {
                    //? Se houver um arquivo de imagem antigo
                    if (!empty($fotoPerfil)) {
                        $caminhoAntigo = $destino . $fotoPerfil;

                        //? Deleta o arquivo antigo
                        if (file_exists($caminhoAntigo) && unlink($caminhoAntigo)) {
                            //? Arquivo antigo foi apagado com sucesso
                            $stmt = $conexao->prepare('UPDATE usuarios SET nome = ?, fotoPerfil = ? WHERE id = ?');
                            $stmt->execute([$nome, $nomeArq, $id]);
                            resposta(200, true, "Dados atualizados com sucesso.");  
                        } else {
                            resposta(200, true, "Algo deu errado ao excluir o arquivo antigo.");
                        }
                    } else {
                        $stmt = $conexao->prepare('UPDATE usuarios SET nome = ?, fotoPerfil = ? WHERE id = ?');
                        $stmt->execute([$nome, $nomeArq, $id]);
                        resposta(200, true, "Dados atualizados com sucesso.");  
                    }
                }
            }
        } else {
            resposta(500, false, "Algo deu errado, tente mais tarde");
        }
    }
}

//TODO verifica a existência dos conteúdos da pasta temporária e os salva
if (isset($_POST['id']) && isset($_POST['nome'])){

    //? caminho para a pasta imagens do servidor
    $pastaDestino = '../imagens/';

    //? verifica se há algo
    if (!empty($_FILES['image']['name'])){
        
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

        } else {
            //? constroi e guarda um novo nome para a imagem
            $nomeUnico = $body['id'] . '_' . time() . '.' . $extensao;

            //? chama a função para armazenar
            armazena($body['id'], $arquivoTemporario, $body['nome'], $nomeUnico, $pastaDestino, $conexao);
        }
    } else {
        armazena($body['id'], false, $body['nome'], false, $pastaDestino, $conexao);
    }
    
} else {
    resposta(400, false, "Você não quer mudar nada? :)");
}
?>