<?php
    require_once "../vendor/autoload.php";
    
    use \Firebase\JWT\JWT;
    use \Firebase\JWT\Key;

    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__, 3));
    $dotenv->load();
    
    function decode_token($token){
        try{
            $decoded = JWT::decode($token, new Key($_SERVER['KEY'], 'HS256'));
            return $decoded;
        }catch(Throwable $e){
            if($e->getMessage() == 'Expired token'){
                return "erro";     
            }   
        }
    }

?>