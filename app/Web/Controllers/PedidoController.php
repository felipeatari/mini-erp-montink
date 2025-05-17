<?php

namespace App\Web\Controllers;

use App\Components\View;
use App\Database\Models\Estoque;
use App\Database\Models\Pedido;
use App\Database\Models\Produto;

class PedidoController
{
    public function index()
    {
        View::title('Meus Pedidos');
        View::app('pedido');

        $pedidos = (new Pedido)->find()->order()->fetch(true);
        $pedidos = jsonDecodeDB($pedidos, ['produtos']);

        return View::view([
            'pedidos' => $pedidos ?? [],
        ]);
    }

    public function create(array $data = [])
    {
        $pedido = new Pedido;
        $pedido->fields($data);
        $pedidoId = $pedido->save();

        if ($pedido->error) {
            return [
                'error' => $pedido->message_error
            ];
        }

        return $pedidoId;
    }
}
