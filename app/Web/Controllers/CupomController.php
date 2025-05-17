<?php

namespace App\Web\Controllers;

use App\Components\Session;
use App\Components\View;
use App\Database\Models\Cupom;
use DateTime;

class CupomController
{
    public function index()
    {
        View::title('Cupons de Desconto');
        View::app('cupom');

        $cupons = (new Cupom)->find()->fetch(true);

        return View::view([
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

    public function add()
    {
        $codigo = filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!$codigo) {
            Session::set('codigo_cupom_erro', 'Código inválido');
            if (Session::has('cupom')) {
                Session::unset('cupom');
            }

            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $cupom = (new Cupom)->find()->condition(['codigo' => $codigo])->fetch();

        $messagemErro = '';
        if (!$cupom) {
            $messagemErro = 'Cupom não encontrado';
        }

        $data = new DateTime($cupom['validade']);
        $agora = new DateTime();

        if ($data < $agora) {
            $messagemErro = 'Esse cupom já venceu';
        }

        if ($messagemErro) {
            Session::set('codigo_cupom_erro', $messagemErro);
            if (Session::has('cupom')) {
                Session::unset('cupom');
            }

            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        Session::set('cupom', $cupom);

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
