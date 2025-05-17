<?php

namespace App\Web\Controllers;

use App\Components\Template;

class Error
{
  public static function error($code, $message)
  {
    Template::title('Erro ' . $code);
    Template::error('error');

    http_response_code($code);

    return Template::view([
      'code' => $code,
      'message' => $message
    ]);
  }

  public static function error_api($code, $message)
  {
    header('Content-Type: application/json');
    http_response_code($code);

    die(json_encode([
      'code' => $code,
      'message' => $message
    ]));
  }
}