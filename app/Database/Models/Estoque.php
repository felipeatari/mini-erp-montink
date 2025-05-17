<?php

namespace App\Database\Models;

use App\Database\Model;

class Estoque extends Model
{
    public function __construct()
    {
        parent::__construct('estoque');
    }
}
