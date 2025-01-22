<?php
function conecta_bd()
{
    // Dados de conexão
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
        return $conexao;  // Retorna a conexão PDO
    } catch (PDOException $e) {
        // Exibe o erro detalhado no log para debug
        error_log("Erro ao conectar ao banco de dados: " . $e->getMessage());
        echo "erro" . $e->getMessage();
        return [
            "ok" => false,
            "informacoes" => "Erro ao conectar ao banco de dados: " . $e->getMessage()
        ];
    }
}


?>