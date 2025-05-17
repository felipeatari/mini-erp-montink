<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Web\Router;
use App\Components\View;

$web = new App\Web\Router();

require_once __DIR__ . '/routes/web.php';

$end = $web?->on()?->dispatcher();

View::layout(content: $end);