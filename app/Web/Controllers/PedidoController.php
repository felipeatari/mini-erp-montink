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

        $pedidos = (new Pedido)->find()->fetch(true);

        if ($pedidos) {
            $pedidos = array_map(function ($item) {
                if (isset($item['pedidos']['variacoes']) and $item['pedidos']['variacoes']) {
                    $json = $item['pedidos']['variacoes'];
                    $decoded = json_decode($json, true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        $item['pedidos']['variacoes'] = $decoded;
                    } else {
                        $item['pedidos']['variacoes'] = [];
                    }
                }

                return $item;
            }, $pedidos);
        }

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
