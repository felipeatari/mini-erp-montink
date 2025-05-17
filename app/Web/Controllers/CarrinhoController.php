<?php

namespace App\Web\Controllers;

use App\Components\View;

class CarrinhoController
{
    public function index()
    {
        View::title('Carrinho de Compras');
        View::app('carrinho');

        return View::view();
    }

    public function create()
    {
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        die;
    }
}
