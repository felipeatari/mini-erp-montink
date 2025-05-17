<?php

if (!function_exists('pr')) {
    function pr(mixed $debug = null, bool $var_dump = false, bool $gettype = false): void {
        ob_start();
        echo '<pre>';
        if ($var_dump) {
            var_dump($debug);
        } elseif ($gettype) {
            echo gettype($debug);
        } else {
            print_r($debug);
        }
        echo '</pre>';
        ob_end_flush();
        exit;
    }
}

if (!function_exists('redirectBackAlert')) {
    function redirectBackAlert(string $message = ''): void {
        if (! $message) $message = 'Algo deu errado! :(';

        echo "<script>
                alert('$message');
                history.back();
            </script>";
        die;
    }
}

if (!function_exists('redirectBack')) {
    function redirectBack(): void {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        die;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $route = ''): void
    {
        header('Location: ' . URL . $route);
        die;
    }
}