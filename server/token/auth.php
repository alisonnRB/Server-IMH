<?php
    include "../resposta/resposta.php";
    include "../../vendor/autoload.php";
    
    use \Firebase\JWT\JWT;
    use \Firebase\JWT\Key;

    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__, 3));
    $dotenv->load();

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); 
    header('Access-Control-Allow-Headers: Content-Type, Authorization, x-xsrf-token, x_csrftoken, Cache-Control, X-Requested-With'); 

    $auth = $_SERVER['HTTP_AUTHORIZATION'];
    

    $token = str_replace('Bearer ','', $auth);
    
    try{
        $decoded = JWT::decode($token, new Key($_SERVER['KEY'], 'HS256'));
        resposta(200, true, "autenticado"); 
    }catch(Throwable $e){
        if($e->getMessage() == 'Expired token'){
            resposta(401, false, 'token Expirado');     
        }
    }

    
?>