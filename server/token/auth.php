<?php
include "../resposta/resposta.php";
include "../../vendor/autoload.php";

echo "oi trouxa";

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__, 3));
$dotenv->load();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, x-xsrf-token, x_csrftoken, Cache-Control, X-Requested-With');

$auth = $_SERVER['HTTP_AUTHORIZATION'];


$token = str_replace('Bearer ', '', $auth);

echo $token;

try {
    $decoded = JWT::decode($token, new Key($_SERVER['KEY'], 'HS256'));
    resposta(200, true, "autenticado");
} catch (ExpiredException $e) {
    resposta(200, false, 'Token expirado');
} catch (BeforeValidException $e) {
    resposta(200, false, 'Token ainda não é válido');
} catch (SignatureInvalidException $e) {
    resposta(200, false, 'Token inválido - Assinatura inválida');
} catch (Throwable $e) {
    if ($e->getMessage() == 'Expired token') {
        resposta(200, false, 'token Expirado');
    }
}


?>