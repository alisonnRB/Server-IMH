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
$token = decode_token($body->id_user);

if(!$token || $token == "erro"){
    resposta(200, false, "não autorizado");
}else{
    busca_amigos($token->id);
}

function busca_amigos($id_user){
    $conexao = conecta_bd();

    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {
        $consulta = $conexao->prepare("SELECT id, user_id, id_ref FROM seguidores WHERE user_id = :user_id");
        $consulta->bindParam(':user_id', $id_user);
        $consulta->execute();
        $seguidores = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $list = array();

        foreach ($seguidores as $seguidor) {
            if($seguidor['user_id'] == $id_user){
                $stm = $conexao->prepare("SELECT id, user_id, id_ref FROM seguidores WHERE id_ref = :id_ref AND user_id = :user_id");
                $stm->bindParam(':user_id', $seguidor['id_ref']);
                $stm->bindParam(':id_ref', $seguidor['user_id'] );
                $stm->execute();
                $amigos = $stm->fetchAll(PDO::FETCH_ASSOC); 
                if ($amigos){
                    $list[] = $amigos[0];
                }
            }
        } 

        foreach ($list as &$amigo) {
            // Contar mensagens não lidas
            $msgsNaoLidas = $conexao->prepare("SELECT COUNT(*) AS nao_lidas FROM chats WHERE (id_user1 = :id OR id_user2 = :id) AND (id_user1 = :id2 OR id_user2 = :id2) AND visu = 0");
            $msgsNaoLidas->bindParam(":id", $amigo['user_id']);
            $msgsNaoLidas->bindParam(":id2", $amigo['id_ref']);
            $msgsNaoLidas->execute();
            $resultNaoLidas = $msgsNaoLidas->fetch(PDO::FETCH_ASSOC);

            $amigo['msgs_nao_lidas'] = $resultNaoLidas['nao_lidas'];

            // Contar total de mensagens
            $msgsTotal = $conexao->prepare("SELECT COUNT(*) AS total_msgs FROM chats WHERE (id_user1 = :id OR id_user2 = :id) AND (id_user1 = :id2 OR id_user2 = :id2)");
            $msgsTotal->bindParam(":id", $amigo['user_id']);
            $msgsTotal->bindParam(":id2", $amigo['id_ref']);
            $msgsTotal->execute();
            $resultTotal = $msgsTotal->fetch(PDO::FETCH_ASSOC);

            $amigo['total_msgs'] = $resultTotal['total_msgs'];
        }

        // Ordenar o array com base em mensagens não lidas e, em seguida, no total de mensagens
        usort($list, function ($a, $b) {
            if ($a['msgs_nao_lidas'] != $b['msgs_nao_lidas']) {
                return $b['msgs_nao_lidas'] - $a['msgs_nao_lidas'];
            } else {
                return $b['total_msgs'] - $a['total_msgs'];
            }
        });

        resposta(200, true, $list);  
    }
    resposta(200, true, false); 
}
?>
