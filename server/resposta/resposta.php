<?php
    function resposta($codigo, $ok, $informacoes) {
        http_response_code($codigo);

        $response = [
            'ok' => $ok,
            'informacoes' => $informacoes,
        ];

        echo(json_encode($response));
        die;
    }

?>