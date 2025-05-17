<?php

namespace App\Web;

use App\Components\Template;

abstract class Admin
{
  public function __construct()
  {
    if (! isset($_SESSION['admin'])) {
      header('Location: ' . URL . '/login');
    }
  }

  protected function title(string $title)
  {
    Template::title($title);
  }

  protected function admin(string $admin)
  {
    Template::admin($admin);
  }

  protected function content(array $vars_dynamic = [])
  {
    $content = Template::view($vars_dynamic);
    Template::layout('admin', $content);
  }
}