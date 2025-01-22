<?php
function conecta_bd()
{
    // Obtendo os dados de conexão das variáveis de ambiente
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '5432';
    $dbname = getenv('DB_NAME') ?: 'ihm';
    $user = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';

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