<?php

namespace App\Database\Models;

use App\Database\Model;

class Pedido extends Model
{
    public function __construct()
    {
        parent::__construct('pedidos');
    }
}
