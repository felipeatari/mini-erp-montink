<?php

$web->get('/', 'ProdutoController->index');
$web->get('/produto', 'ProdutoController->index');
$web->post('/produto', 'ProdutoController->create');
$web->post('/produto/{id}', 'ProdutoController->update');
$web->get('/carrinho', 'CarrinhoController->index');
// $web->post('/carrinho', 'CarrinhoController->create');
$web->get('/cupons', 'Coupons->index');