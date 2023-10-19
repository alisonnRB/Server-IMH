<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./token/decode_token.php";


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');

//TODO função que encerra as operações e enviar umas resposta para a api trabalhar


//TODO recebe os inputs da api
$body = file_get_contents('php://input');
$body = json_decode($body);

$token = decode_token($body->id);
if($token == "erro"){
    resposta(401, true, "não autorizado");
}else{
    quaisLivros($token->id);   
}

function quaisLivros($id){
    try{
        $conexao = conecta_bd();
        if (!$conexao) {
            resposta(500, false, "Houve um problema ao conectar ao servidor");
        } else {

        $consulta = $conexao->prepare("SELECT id_livro FROM favoritos WHERE user_id = :user_id");
        $consulta->bindParam(':user_id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $livros = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $livrosObj = new stdClass();
        $numeroChave = 0;

        foreach ($livros as $livro) {
            $stmt = $conexao->prepare("SELECT id, user_id, nome, imagem, genero, sinopse, classificacao, curtidas, favoritos, visus FROM livro_publi WHERE id = :id");
            $stmt->execute([':id' => $livro['id_livro']]);
            $livroDados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($livroDados as $dados) {
                $livrosObj->{$numeroChave} = $dados;
                $numeroChave++;
            }
        }
        resposta(200, true, $livrosObj);
            
    }
    }catch (Exception $e) {
        resposta(500, false, null);
    }
}

?>