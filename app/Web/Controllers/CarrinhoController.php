<?php

namespace App\Web\Controllers;

use App\Components\Template;

class CarrinhoController
{
  public function index()
  {
    Template::title('Carrinho de Compras');
    Template::app('carrinho');

    return Template::view();
  }

  public function create()
  {
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
    die;
  }
}