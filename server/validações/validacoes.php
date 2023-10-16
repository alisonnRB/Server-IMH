<?php
function validar_string ($string, $type){
    if (isset($string) && !empty($string)){
        
        if ($type == "nome"){
            $string = filter_var($string, FILTER_SANITIZE_STRING);
            if (!preg_match('/^[a-zA-Z0-9\s]*[a-zA-Z0-9\s]+[a-zA-Z0-9\s]*$/', $string)) {
                return (true);
            }
            else {
                return (false, "Possui um caracter inválido");
            }
        
        } 
        else if ($type == "email") {
            $string = filter_var($string,FILTER_VALIDATE_EMAIL);
            return (true);
        }
        else{
            $string = filter_var($string, FILTER_SANITIZE_STRING);
            return (true);            
        }
    }
    else {
        return (false, "String não existe");
    }
    
};

function validar_number($numero){
    if (isset($numero) && !empty($numero)){
        if (is_int($numero)){
            $numero =  filter_var($numero, FILTER_VALIDATE_INT);
            return (true);
        }
        else if (is_float($numero)){
            $numero = filter_var($numero, FILTER_VALIDATE_FLOAT);
            return (true);
        }
        else {
            return (false, "Tipo indefinido");
        }
    }
    else {
        return (false, "Não existe");
    }
}

function validar_img($img){
    if (isset($img)){
        $extensao = pathinfo($img['image']['name'], PATHINFO_EXTENSION);
        $arquivoTemporario = $img['image']['tmp_name'];
        //? Criar um objeto finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        
        //? Obter o tipo MIME do arquivo
        $tipoMIME = finfo_file($finfo, $arquivoTemporario);
    
        //? Fechar o objeto finfo
        finfo_close($finfo);
    
        //? Array de tipos MIME permitidos
        $tiposMIMEPermitidos = array('image/jpeg', 'image/png');

        //? informa que não é possível a imagem, pois não é um formato compatível
        if (!in_array($tipoMIME, $tiposMIMEPermitidos)) {
            return(false, "Tipo de arquivo não permitido.");
        }else{
            return (true);
        }
    }
    else {
        return(false, "Imagem não existe")
    }
}
    //? arazena o tipo de imagem enviada
    

    

  


?>