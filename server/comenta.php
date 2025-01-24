<?php
date_default_timezone_set('America/Sao_Paulo');

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
if (!$token || $token == "erro") {
    resposta(200, true, "não autorizado");
} else {
    comentar($token->id, $body);
}

function comentar($id_user, $body)
{
    //! Verificar entrada string, filtrar e etc
    $conexao = conecta_bd();

    if (!$conexao) {
        resposta(200, false, "Houve um problema ao conectar ao servidor");
    } else {
        $data = date('Y-m-d H:i:s');

        $resposta = $body->resposta ? 1 : 0;

        //tipo (string), id_ref (int), texto (string), id_resposta (int), conversa (int)
        //?validações
        //*tipo (livro, publi, etc)
        $tipo = validar_string($body->tipo);
        if ($tipo[0] == true) {
            $tipo = $tipo[1];
        } else if ($tipo[0] == false) {
            $tipo = $tipo[1];
        }

        //*id ref referente ao livro ou publicação
        $idref = validar_int($body->id_ref);
        if ($idref[0] == true) {
            $idref = $idref[1];
        } else if ($idref[0] == false) {
            $idref = $idref[1];
        }

        //*texto do comentário
        $texto = strip_tags($body->texto);
        if (empty($texto)) {
            resposta(200, false, "campo vazio");
        }

        //*respostas 
        if ($resposta == 1) {
            //*id resposta é pra qual comentário é dirigido a resposta
            $idresp = validar_int($body->idResposta);
            if ($idresp[0] == true) {
                $idresp = $idresp[1];
            } else if ($idresp[0] == false) {
                $idresp = $idresp[1];
            }

            //*conversa é o id do comentário ao qual as respostas estão
            $conversa = validar_int($body->conversa);
            if ($conversa[0] == true) {
                $conversa = $conversa[1];
            } else if ($conversa[0] == false) {
                $conversa = $conversa[1];
            }
        } else {
            $idresp = 0;
            $conversa = 0;
        }



        //resposta(200, true, $conversa);

        $stm = $conexao->prepare('INSERT INTO comentarios(user_id, tipo, id_ref, texto, resposta, id_resposta, tempo, conversa) VALUES (:user, :tipo, :id_ref, :texto, :resposta, :id_resposta, :tempo, :conversa)');
        $stm->bindParam(':user', $id_user);
        $stm->bindParam(':tipo', $tipo);
        $stm->bindParam(':id_ref', $idref);
        $stm->bindParam(':texto', $texto);
        $stm->bindParam(':resposta', $resposta);
        $stm->bindParam(':id_resposta', $idresp);
        $stm->bindParam(':tempo', $data);
        $stm->bindParam(':conversa', $conversa);

        if ($stm->execute()) {
            resposta(200, true, "Deu certo");
        } else {
            $errorInfo = $stm->errorInfo();
            resposta(200, false, "Erro no banco de dados: " . $errorInfo[2]);
        }

    }

}
?>