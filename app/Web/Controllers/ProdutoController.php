<?php

namespace App\Web\Controllers;

use App\Components\Session;
use App\Components\Template;
use App\Database\Models\Estoque;
use App\Database\Models\Produto;

class ProdutoController
{
    public function index()
    {
        Template::title('Pagina Produto');
        Template::app('produto');

        $data = (new Produto)->query_join(['order' => 'DESC'])->joins(['estoque' => 'produto_id']);

        $produtos = [];

        if ($data) {
            foreach ($data as $i => $item):
                $variacoes = json_decode($item['produtos']['variacoes'], true);

                $produtos[$i] = [
                    'id' => $item['produtos']['id'],
                    'nome' => $item['produtos']['nome'],
                    'preco' => $item['produtos']['preco'],
                    'variacao_1' => $variacoes['variacao_1'],
                    'variacao_2' => $variacoes['variacao_2'],
                    'estoque_id' => $item['estoque']['id'] ?? 0,
                    'estoque_quantidade' => $item['estoque']['quantidade'] ?? 0,
                ];
            endforeach;
        }

        return Template::view([
            'produtos' => $produtos,
        ]);
    }

    public function create()
    {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $preco = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);
        $variacao_1 = filter_input(INPUT_POST, 'variacao_1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $variacao_2 = filter_input(INPUT_POST, 'variacao_2', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $estoqueQuantidade = filter_input(INPUT_POST, 'estoque_quantidade', FILTER_VALIDATE_INT);

        if (!$nome or !$preco or !$variacao_1 or !$variacao_2 or !$estoqueQuantidade) {
            die('error');
        }

        $produto = new Produto;
        $produto->field('nome', $nome);
        $produto->field('preco', $preco);
        $produto->field('variacoes', json_encode([
            'variacao_1' => $variacao_1,
            'variacao_2' => $variacao_2,
        ]));

        $produto = $produto->save();
        $produto_id = $produto['id'];

        if ($produto_id) {
            $estoque = new Estoque;
            $estoque->field('produto_id', $produto_id);
            $estoque->field('quantidade', $estoqueQuantidade);
            $estoque->save();
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function update($id)
    {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $preco = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);
        $variacao_1 = filter_input(INPUT_POST, 'variacao_1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $variacao_2 = filter_input(INPUT_POST, 'variacao_2', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $estoqueID = filter_input(INPUT_POST, 'estoque_id', FILTER_VALIDATE_INT);
        $estoqueQuantidade = filter_input(INPUT_POST, 'estoque_quantidade', FILTER_VALIDATE_INT);

        if (isset($_POST['adicionar_carrinho'])) {
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

                            if ($item['qtd'] < $estoqueQuantidade) {
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
                    ]
                ];

                Session::set('carrinhoProduto', $carrinhoProdutoAdd);
            }

            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        if (!$nome or !$preco or !$variacao_1 or !$variacao_2 or !$estoqueID or !$estoqueQuantidade) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $produto = new Produto;
        $produto->field('id', $id);
        $produto->field('nome', $nome);
        $produto->field('preco', $preco);
        $produto->field('variacoes', json_encode([
            'variacao_1' => $variacao_1,
            'variacao_2' => $variacao_2,
        ]));

        $produto->update($id);

        if ($estoqueID) {
            $estoque = new Estoque;
            $estoque->field('id', $estoqueID);
            $estoque->field('produto_id', $id);
            $estoque->field('quantidade', $estoqueQuantidade);
            $estoque->update($estoqueID);
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
