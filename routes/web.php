<?php

$web->get('/', 'ProdutoController->index');
$web->get('/produto', 'ProdutoController->index');
$web->post('/produto/criar', 'ProdutoController->create');
$web->post('/produto/editar/{id}', 'ProdutoController->update');

$web->get('/carrinho', 'CarrinhoController->index');

$web->get('/cupom', 'CupomController->index');
$web->post('/cupom/criar', 'CupomController->create');
$web->post('/cupom/apagar/{id}', 'CupomController->destroy');
$web->post('/cupom/adicionar', 'CupomController->add');