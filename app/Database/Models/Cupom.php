<?php

namespace App\Database\Models;

use App\Database\Model;

class Cupom extends Model
{
  public function __construct()
  {
    parent::__construct('cupons');
  }
}