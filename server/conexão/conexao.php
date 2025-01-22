<?php
require_once 'vendor/autoload.php'; // Certifique-se de incluir o autoload do Composer

use Dotenv\Dotenv;

// Carregar as variáveis de ambiente do arquivo .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

function conecta_bd()
{
    echo "host" . getenv('DB_HOST');
    // Obtendo os dados de conexão das variáveis de ambiente
    $host = getenv('DB_HOST') ?: 'dpg-cu8kbdaj1k6c73a002og-a';
    $port = getenv('DB_PORT') ?: '5432';
    $dbname = getenv('DB_NAME') ?: 'ihm_database';
    $user = getenv('DB_USER') ?: 'ihm_database_user';
    $password = getenv('DB_PASSWORD') ?: '6VH0P3ugUBq22ReQZMGWv1Fxk8pMufEj';

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