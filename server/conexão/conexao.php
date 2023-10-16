<?php
    function conecta_bd (){
        try {
            $conexao = new PDO("mysql:host=localhost;dbname=ihm", "root", "");
            return $conexao;
        } catch(Exception $e){
            return false;
        };
    };

?>