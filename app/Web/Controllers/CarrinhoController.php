<?php

namespace App\Web\Controllers;

use App\Components\Session;
use App\Components\View;
use App\Database\Models\Estoque;

class CarrinhoController
{
    public function index()
    {
        View::title('Carrinho de Compras');
        View::app('carrinho');

        $carrinho = Session::all();

        return View::view([
            'cupom' => $carrinho['cupom'] ?? [],
            'carrinhoProduto' => $carrinho['carrinhoProduto'] ?? [],
            'subTotal' => 0,
            'total' => 0,
            'frete' => 0,
            'desconto' => $carrinho['cupom']['desconto'] ?? 0,
        ]);
    }

    public function addCarrinho($id, $nome, $preco, $variacao_1, $variacao_2, $estoqueQtd, $estoqueId)
    {
        $estoque = (new Estoque)->find_id($estoqueId, ['quantidade']);
        $estoqueQtdDB = $estoque['quantidade'] ?? 0;

        if ($estoqueQtdDB <= 0) {
            redirectBackAlert('Esse produto não possuí estoque suficiente.');
        }

        if (Session::has('carrinhoQtd') and Session::has('carrinhoProduto')) {
            $carrinhoProduto = Session::one('carrinhoProduto');

            $ids = [];

            foreach ($carrinhoProduto as $item):
                $ids[] = $item['id'];
            endforeach;

            if (in_array($id, $ids)) {
                $carrinhoProdutoAdd = [];
                foreach ($carrinhoProduto as $i => $item):
                    if ($item['id'] == $id) {
                        $precoUn = $item['preco_un'];
                        $precoTotal = $item['preco_total'];

                        if ($item['qtd'] < $estoqueQtd) {
                            $item['qtd'] += 1;

                            $precoTotal += $precoUn;
                        }

                        $carrinhoProdutoAdd[] = [
                            'id' => $item['id'],
                            'nome' => $item['nome'],
                            'preco_un' => $precoUn,
                            'preco_total' => $precoTotal,
                            'variacoes' => $item['variacoes'],
                            'qtd' => $item['qtd'],
                            'estoque_id' => $estoqueId,
                        ];
                    } else {
                        $carrinhoProdutoAdd[] = $item;
                    }
                endforeach;
            } else {
                $carrinhoProdutoAdd = [
                    [
                        'id' => $id,
                        'nome' => $nome,
                        'preco_un' => $preco,
                        'preco_total' => $preco,
                        'variacoes' => $variacao_1 . ' ' . $variacao_2,
                        'qtd' => 1,
                        'estoque_id' => $estoqueId,
                    ]
                ];

                $carrinhoProdutoAdd = array_merge($carrinhoProduto, $carrinhoProdutoAdd);
            }

            $carrinhoQtd = 0;
            foreach ($carrinhoProdutoAdd as $item):
                $carrinhoQtd += $item['qtd'];
            endforeach;

            Session::set('carrinhoQtd', $carrinhoQtd);
            Session::set('carrinhoProduto', $carrinhoProdutoAdd);
        } else {
            Session::set('carrinhoQtd', 1);

            $carrinhoProdutoAdd = [
                [
                    'id' => $id,
                    'nome' => $nome,
                    'preco_un' => $preco,
                    'preco_total' => $preco,
                    'variacoes' => $variacao_1 . ' ' . $variacao_2,
                    'qtd' => 1,
                    'estoque_id' => $estoqueId,
                ]
            ];

            Session::set('carrinhoProduto', $carrinhoProdutoAdd);
        }

        redirectBack();
    }

    public function checkout()
    {
        $carrinho = Session::all();
        $inputs = $_POST;

        $produtos = $carrinho['carrinhoProduto'];

        $data = [
            'status' => 'pendente',
            'total' => $inputs['total'],
            'desconto' => $inputs['desconto'],
            'frete' => $inputs['frete'],
            'cep' => $inputs['cep'],
            'cidade' => $inputs['cidade'],
            'bairro' => $inputs['bairro'],
            'estado' => $inputs['estado'],
            'endereco' => $inputs['endereco'],
            'nome' => $inputs['nome'],
            'email' => $inputs['email'],
            'qtd_produtos' => $carrinho['carrinhoQtd'],
            'produtos' => json_encode($produtos),
            'cupom' => $carrinho['cupom']['codigo'] ?? null
        ];

        $pedido = (new PedidoController)->create($data);

        if (isset($pedido['error'])) {
            echo $pedido['error'];
            die;
        }

        $estoque = new Estoque;

        foreach ($produtos as $produto):
            if ($estoqueId = $produto['estoque_id'] ?? null) {
                $estoqueQtdDB = $estoque->find_id($estoqueId, ['quantidade']);

                if (! $estoque->error) {
                    $estoqueQtd = $estoqueQtdDB['quantidade'] - $produto['qtd'];

                    $estoque->field('quantidade', $estoqueQtd);
                    $estoque->update($estoqueId);
                }
            }
        endforeach;

        Session::unset('carrinhoProduto');
        Session::unset('carrinhoQtd');
        Session::unset('cupom');

        redirect('/');
    }
}
