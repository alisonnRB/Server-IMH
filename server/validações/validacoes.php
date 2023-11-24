<?php
//função validando nome
function validar_nome ($string){
    if (isset($string) && !empty($string) && filter_var($string, FILTER_SANITIZE_STRING)){
        if (!preg_match('/^[a-zA-Z0-9\s]+$/', $string)) {
            return [false, "possui um caracter inválido"];
        } else {
            $string = strip_tags($string);
            return [true, $string];
        }
    } 
}

//função validando string
function validar_string($string){
    $padrao_string = "/^[a-zA-Z0-9ç\s!@#$%^&*_+\[\]:;,.?~\\-]+$/u";
    if (isset($string) && !empty($string) && filter_var($string,FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => $padrao_string)))){
        $string = strip_tags($string);
        return [true, $string];
    } else {
        return [false, "caracter inválido"];
    }
}

//função validando int
function validar_int($int){
    if (isset($int) && !empty($int) && filter_var($int,FILTER_VALIDATE_INT)){
        $int = strip_tags($int);
        return [true, $int];
    } else {
        return [false, "dado indisponivel"];
    }
}

//função validando email
function validar_email ($string){
    if (isset($string) && !empty($string) && filter_var($string,FILTER_VALIDATE_EMAIL)){
            $string = strip_tags($string);
            return [true, $string];
    } else {
        return [false, "Email inválido"];
    }
}

//função validando senha
function validar_senha ($senha){
    $padrao_senha = "/^[a-zA-Z0-9ç!@#$%^&*_+\[\]:;,.?~\\-]+$/u";
    if (isset($senha) && !empty($senha) && filter_var($senha,FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => $padrao_senha)))){
        $senha = strip_tags($senha);
        return [true, $senha];
    } else {
        return [false, "senha inválida"];
    }
}

//função que criptografa uma senha
function cripto_senha ($senha){
            $hash = password_hash($senha, PASSWORD_BCRYPT);
            return $hash;
}     

//função que verifica a senha com o hash
function verifica_senha($senha, $banco){
    return password_verify($senha, $banco);
}
    




//função validando imagem
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
            return [false, "Tipo de arquivo não permitido."];
        }else{
            return [true];
        }
    }
    else {
        return [false, "Imagem não existe"];
    }
}
    

?>