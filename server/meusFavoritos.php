<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

//TODO função que encerra as operações e enciar umas resposta para a api trabalhar
function resposta($codigo, $ok, $msg, $livros) {
    http_response_code($codigo);
    header('Content-Type: application/json');

    $response = [
        'ok' => $ok,
        'msg' => $msg,
        'livros' => $livros,
    ];

    echo(json_encode($response));
    die;
}

//TODO recebe os inputs da api
$body = file_get_contents('php://input');
$body = json_decode($body);

function quaisLivros($body){
    //! verificar se é publico quando o livro não for seu
    try{
        if (isset($body->id) || !empty($body->id)){

            $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

            $consulta = $conexao->prepare("SELECT id_livro FROM favoritos WHERE user_id = :user_id");
            $consulta->bindParam(':user_id', $body->id, PDO::PARAM_INT);
            $consulta->execute();
            $livros = $consulta->fetchAll(PDO::FETCH_ASSOC);

            $livrosObj = new stdClass();
            $numeroChave = 0;

            foreach ($livros as $livro) {
                $stmt = $conexao->prepare("SELECT id, user_id, nome, imagem, genero, sinopse, classificacao, curtidas, favoritos FROM livro_publi WHERE id = :id");
                $stmt->execute([':id' => $livro['id_livro']]);
                $livroDados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($livroDados as $dados) {
                    $livrosObj->{$numeroChave} = $dados;
                    $numeroChave++;
                }
            }
            resposta(200, true, "deu certo", $livrosObj);
        }
    }catch (Exception $e) {
        resposta(500, false, null, null);
    }
}




quaisLivros($body);
?>