<?php

namespace App\Web\Controllers;

use App\Components\Mail;
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
            return [ 'error' => $pedido->message_error ];
        }

        $pedido = $pedido->find_id($pedidoId['id']);

        $sendMail = new Mail(
            $pedido['nome'],
            $pedido['email'],
            'Pedido confirmado!',
            $this->html($pedido),
            $this->text($pedido)
        );

        $sendMail->send();

        if ($sendMail->error) {
            return [ 'error' => $sendMail->messageError ];
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

        $pedido = new Pedido;

        $pedido->find_id($pedidoId);

        if ($pedido->error) {
            $code = httpStatusCodeError($pedido->code_error);
            jsonResponse($code, $pedido->message_error);
        }

        $pedido->field('status', $status);
        $pedido->update($pedidoId);

        if ($pedido->error) {
            $code = httpStatusCodeError($pedido->code_error);
            jsonResponse($code, $pedido->message_error);
        }

        jsonResponse(data: ['status' => $status]);
    }

    private function html(array $pedido)
    {
        $pedido['produtos'] = json_decode($pedido['produtos'], true);

        $html = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border: 1px solid #ddd;">
                <h2 style="color: #2c3e50; margin-bottom: 20px;">Pedido confirmado!</h2>
                <p>Olá <strong>' . $pedido['nome'] . '</strong>,</p>
                <p>Recebemos seu pedido <strong>#' . $pedido['id'] . '</strong> e ele está em processamento.</p>

                <h3 style="margin-top: 30px;">Resumo do Pedido</h3>

                <table cellpadding="8" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 14px; margin-top: 10px;">
                    <thead>
                        <tr style="background-color: #f2f2f2;">
                            <th align="left" style="border: 1px solid #ccc;">Produto</th>
                            <th align="center" style="border: 1px solid #ccc;">Qtde</th>
                            <th align="right" style="border: 1px solid #ccc;">Preço Un</th>
                            <th align="right" style="border: 1px solid #ccc;">Preço Total</th>
                        </tr>
                    </thead>
                    <tbody>';

                foreach ($pedido['produtos'] as $produto):
                    $html .= '
                        <tr>
                            <td style="border: 1px solid #ccc;">' . $produto['nome'] . '</td>
                            <td align="center" style="border: 1px solid #ccc;">' . $produto['qtd'] . '</td>
                            <td align="right" style="border: 1px solid #ccc;">R$ ' . number_format($produto['preco_un'], 2, ',', '.') . '</td>
                            <td align="right" style="border: 1px solid #ccc;">R$ ' . number_format($produto['preco_total'], 2, ',', '.') . '</td>
                        </tr>';
                endforeach;

                $html .= '
                    </tbody>
                </table>

                <p style="font-size: 16px; margin-top: 20px;"><strong>Total:</strong> R$ ' . number_format($pedido['total'], 2, ',', '.') . '</p>

                <p style="margin-top: 30px;">Obrigado por comprar com a gente!</p>
            </div>';

        return $html;
    }

    private function text(array $pedido)
    {
        $text = "Pedido confirmado!\n\n";
        $text .= "Olá " . $pedido['nome'] . ",\n";
        $text .= "Recebemos seu pedido #" . $pedido['id'] . " e ele está em processamento.\n\n";
        $text .= "Resumo do Pedido:\n";
        $text .= str_repeat('-', 40) . "\n";

        $pedido['produtos'] = json_decode($pedido['produtos'], true);

        foreach ($pedido['produtos'] as $produto) {
            $text .= "Produto: " . $produto['nome'] . "\n";
            $text .= "Quantidade: " . $produto['qtd'] . "\n";
            $text .= "Preço Un.: R$ " . number_format($produto['preco_un'], 2, ',', '.') . "\n";
            $text .= "Total Item: R$ " . number_format($produto['preco_total'], 2, ',', '.') . "\n";
            $text .= str_repeat('-', 40) . "\n";
        }

        $text .= "Total do Pedido: R$ " . number_format($pedido['total'], 2, ',', '.') . "\n\n";
        $text .= "Obrigado por comprar com a gente!";

        return $text;
    }
}


