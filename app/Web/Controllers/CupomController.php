<?php

namespace App\Web\Controllers;

use App\Components\Template;
use App\Database\Models\Cupom;

class CupomController
{
    public function index()
    {
        Template::title('Cupons de Desconto');
        Template::app('cupom');

        $cupons = (new Cupom)->find()->fetch(true);

        return Template::view([
            'cupons' => $cupons
        ]);
    }

    public function create()
    {
        $codigo = filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $desconto = filter_input(INPUT_POST, 'desconto', FILTER_VALIDATE_FLOAT);
        $validade = filter_input(INPUT_POST, 'validade', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!$codigo or !$desconto or !$validade) {
            die('error');
        }

        $cupom = new Cupom;
        $cupom->field('codigo', $codigo);
        $cupom->field('desconto', $desconto);
        $cupom->field('validade', $validade);
        $cupom->save();

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function destroy($id)
    {
        (new Cupom)->delete($id);

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
