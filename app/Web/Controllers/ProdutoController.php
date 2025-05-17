<?php

namespace App\Web\Controllers;

use App\Components\View;
use App\Database\Models\Estoque;
use App\Database\Models\Produto;

class ProdutoController
{
    public function index()
    {
        View::title('Pagina Produto');
        View::app('produto');

        $produtos = (new Produto)->query_join(['order' => 'DESC'])->joins(['estoque' => 'produto_id']);
        $produtos = jsonDecodeDB($produtos, ['produtos.variacoes']);

        if ($produtos) {
            $produtos = array_map(function ($item) {
                if (isset($item['produtos']['preco']) and $item['produtos']['preco']) {
                    $item['produtos']['preco'] /= 100;
                }

                return $item;
            }, $produtos);
        }

        return View::view([
            'produtos' => $produtos ?? [],
        ]);
    }

    public function create()
    {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $preco = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);
        $variacao_1 = filter_input(INPUT_POST, 'variacao_1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $variacao_2 = filter_input(INPUT_POST, 'variacao_2', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $estoqueQtd = filter_input(INPUT_POST, 'estoque_quantidade', FILTER_VALIDATE_INT);

        if (!$nome or !$preco or !$variacao_1 or !$variacao_2 or !$estoqueQtd) {
            redirectBackAlert('Verifique os campos e tente novamnete');
        }

        $preco *= 100;

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
            $estoque->field('quantidade', $estoqueQtd);
            $estoque->save();
        }

        redirectBack();
    }

    public function update($id)
    {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $preco = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);
        $variacao_1 = filter_input(INPUT_POST, 'variacao_1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $variacao_2 = filter_input(INPUT_POST, 'variacao_2', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $estoqueId = filter_input(INPUT_POST, 'estoque_id', FILTER_VALIDATE_INT);
        $estoqueQtd = filter_input(INPUT_POST, 'estoque_quantidade', FILTER_VALIDATE_INT);

        if (isset($_POST['adicionar_carrinho'])) {
            (new CarrinhoController)->addCarrinho($id, $nome, $preco, $variacao_1, $variacao_2, $estoqueQtd, $estoqueId);
        }

        if (!$nome or !$preco or !$variacao_1 or !$variacao_2 or !$estoqueId or !$estoqueQtd) {
            redirectBackAlert('Verifique os campos e tente novamente.');
        }

        $preco *= 100;

        $produto = new Produto;
        $produto->field('id', $id);
        $produto->field('nome', $nome);
        $produto->field('preco', $preco);
        $produto->field('variacoes', json_encode([
            'variacao_1' => $variacao_1,
            'variacao_2' => $variacao_2,
        ]));

        $produto->update($id);

        if ($estoqueId) {
            $estoque = new Estoque;
            $estoque->field('id', $estoqueId);
            $estoque->field('produto_id', $id);
            $estoque->field('quantidade', $estoqueQtd);
            $estoque->update($estoqueId);
        }

        redirectBack();
    }
}
