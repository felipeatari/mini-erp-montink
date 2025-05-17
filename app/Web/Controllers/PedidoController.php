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

    public function webhook()
    {
        $pedidoId = $_POST['pedido_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$pedidoId or !$status) {
            jsonResponse(400, 'Veririque os parâmetros "pedido_id" e "status".');
        }

        if (! in_array($status, ['pendente', 'aprovado', 'cancelado'])) {
            jsonResponse(400, 'O status informado é inválido. Aceitos: "pendente", "aprovado" e "cancelado".');
        }

        $model = new Pedido;

        $model->field('status', $status);
        $model->update($pedidoId);

        if ($model->error) {
            $code = httpStatusCodeError($model->code_error);
            jsonResponse($code, $model->message_error);
        }

        $pedido = $model->find_id($pedidoId);

        if ($model->error) {
            $code = httpStatusCodeError($model->code_error);
            jsonResponse($code, $model->message_error);
        }

        jsonResponse(data: ['status' => $pedido['status']]);
    }
}


