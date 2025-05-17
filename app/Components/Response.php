<?php

namespace App\Components;

class Response
{
  private $headers = [];

  public function headers(string $key = '', string $value = '')
  {
    if (empty($key) or empty($value)) {
      return getallheaders();
    }

    $this->headers[][$key] = $value;
  }

  public function end(string $body = '', bool $buffer = false)
  {
    foreach ($this->headers as $header):
      header(key($header) . ': ' . $header[key($header)]);
    endforeach;

    if ($buffer) {
      ob_start();
      echo $body;
      $body = ob_get_contents();
      ob_clean();
    }

    echo $body;die;
  }

  public static function error(int|string $code, array|string $message)
  {
    if (is_int($code)) {
      http_response_code($code);
    }

    return [
      'status' => 'error',
      'code' => $code,
      'msg' => $message
    ];
  }

  public static function success(int $code, int $total, array $data = [])
  {
    if (is_int($code)) {
      http_response_code($code);
    }

    return [
      'status' => 'success',
      'code' => $code,
      'total' => $total,
      'data' => $data
    ];
  }
}