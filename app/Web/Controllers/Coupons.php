<?php

namespace App\Web\Controllers;

use App\Components\Template;

class Coupons
{
  public function index()
  {
    Template::title('Cupons de Desconto');
    Template::app('coupon');

    return Template::view();
  }
}