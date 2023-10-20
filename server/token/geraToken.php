<?php
    require_once "../vendor/autoload.php";
    use \Firebase\JWT\JWT;

    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__, 3));
    $dotenv->load();

    function geraToken($id, $email){

        $payload = array(
            "exp" => time() + 86400,
            "iat" => time(), // 
            "email" => $email,
            "id" => $id,

        );

        $token = JWT::encode($payload, $_ENV['KEY'], 'HS256');
        return $token;
    }
?>