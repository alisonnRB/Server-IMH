<?php
function conecta_bd()
{
    // Obtendo os dados de conexão das variáveis de ambiente
    $host = 'dpg-cu8kbdaj1k6c73a002og-a';
    $port = '5432';
    $dbname = 'ihm_database';
    $user = 'ihm_database_user';
    $password = '6VH0P3ugUBq22ReQZMGWv1Fxk8pMufEj';

    try {
        // DSN para PostgreSQL
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $conexao = new PDO($dsn, $user, $password);
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Habilita exceções
        return $conexao;
    } catch (PDOException $e) {
        // Exibe o erro no log para debug (opcional)
        error_log("Erro ao conectar ao banco de dados: " . $e->getMessage());
        return false;
    }
}
?>