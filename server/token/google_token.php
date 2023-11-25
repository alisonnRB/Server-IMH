<?php

function decode_token($token)
{
    $googleVerificationEndpoint = 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=' . $token;
    $context = stream_context_create(['http' => ['ignore_errors' => true]]);
 
    $response = file_get_contents($googleVerificationEndpoint, false, $context);

    // Verifique se houve algum erro HTTP
    if ($response === false) {
        return null; // ou trate o erro de outra forma, dependendo dos requisitos
    }

    // Decodifique o JSON
    $tokenInfo = json_decode($response);

    // Verifique se houve um erro na resposta do Google
    if (isset($tokenInfo->error)) {
        return null; // ou trate o erro de outra forma, dependendo dos requisitos
    }

    return $tokenInfo;
}

?>
