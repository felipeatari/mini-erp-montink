<?php

namespace App\Web\Controllers;

use App\Components\View;

class ErroController
{
    public static function error($code, $message)
    {
        View::title('Erro ' . $code);
        View::error('erro');

        http_response_code($code);

        return View::view([
            'code' => $code,
            'message' => $message
        ]);
    }
}
