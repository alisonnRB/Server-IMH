<?php 

$conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");

$body = file_get_contents('php://input');
$body = json_decode($body);



?>