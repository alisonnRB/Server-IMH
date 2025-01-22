<?php
function conecta_bd()
{
    try {
        $conexao = new PDO("mysql:host=dpg-cu8kbdaj1k6c73a002og-a;dbname=ihm_database", "ihm_database_user", "6VH0P3ugUBq22ReQZMGWv1Fxk8pMufEj");
        return $conexao;
    } catch (Exception $e) {
        return false;
    }
    ;
}
;

?>