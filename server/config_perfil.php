<?php
    //! é necessario ais verificações e lidar com o envio de apenas uma dos inputs
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


//TODO verifica a existencia dos conteudos da pasta temporaria e a salva
if (isset($_FILES['image']) && isset($_POST['id']) && isset($_POST['nome'])) {
    $body = $_POST;

    //? caminho para a pasta imagens do server
    $pastaDestino = '../imagens/';

    //? arazena o tipo de imagem enviada
    //! VERIFIQUE PARA QUE SEJA POSSIVEL O ENVIO DE TIPOS ESPECIFICOS DE IMAGENS PARA EVITAR INJECTIONS
    $extensao = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    //? constroi e guarda um novo nome para a imagem
    $nomeUnico = $body['id'] . '_' . time() . '.' . $extensao;

    //? controi e guarda o caminho especifico para o arquivo
    $caminhoDestino = $pastaDestino . $nomeUnico;

    //TODO move o arquivo para a pasta e lida com o erro
    if (move_uploaded_file($_FILES['image']['tmp_name'], $caminhoDestino)) {

        //? salva o caminho no bd
        $stmt = $conexao->prepare('UPDATE usuarios SET nome = ?, fotoPerfil = ? WHERE id = ?');
        $stmt->execute([$body['nome'], $nomeUnico, $body['id']]);

        resposta(200, true, "Dados atualizados com sucesso.");
    } else {
        resposta(500, false, "Erro ao fazer upload do arquivo.");
    }
} else {
    resposta(400, false, "Requisição inválida.");
}
?>
