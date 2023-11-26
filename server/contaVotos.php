<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";
include "./token/decode_token.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

$body = file_get_contents('php://input');
$body = json_decode($body);

conta_votos($body);

function conta_votos($body){
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {
    
    
    $consulta = $conexao->prepare('SELECT chave FROM votacao WHERE id_ref = :id_ref');
    $consulta->bindParam(':id_ref', $body->id_ref);
    $consulta->execute();
    $votos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $list = new stdClass();

    $list->{0} = 0;
    $list->{1} = 0;
    $list->{2} = 0;
    $list->{3} = 0;
 

    foreach ($votos as $voto) {
        if($voto['chave'] == 0){
            $list->{0} += 1;
        }
        else if($voto['chave'] == 1){
            $list->{1} += 1;
        }
        else if($voto['chave'] == 2){
            $list->{2} += 1;
        }
        else if($voto['chave'] == 3){
            $list->{3} += 1;
        }
    }

    $list = json_encode($list);
            
    $stmt = $conexao->prepare("UPDATE enquete SET votos = :votos WHERE id = :id");
    $stmt->bindParam(':votos', $list);
    $stmt->bindParam(':id', $body->id_ref, PDO::PARAM_INT);
    $stmt->execute();
    }
}


?>