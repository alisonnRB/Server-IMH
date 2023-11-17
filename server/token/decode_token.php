<?php
    require_once __DIR__ . "/../../vendor/autoload.php";
    
    use \Firebase\JWT\JWT;
    use \Firebase\JWT\Key;

    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__, 3));
    $dotenv->load();
    
    function decode_token($token){
        try{
            $decoded = JWT::decode($token, new Key($_SERVER['KEY'], 'HS256'));
            return $decoded;
        } catch (BeforeValidException $e) {
            return "erro";
        }catch(Throwable $e){
            if($e->getMessage() == 'Expired token'){
                return "erro";     
            }   
        }catch(Exception $e){
            return "erro";
        }
    }

?>