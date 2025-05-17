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

if (!function_exists('jsonDecodeDB')) {
    function jsonDecodeDB($collection, array $jsonFields): array
    {
        if (empty($collection) || !is_array($collection)) {
            return [];
        }

        return array_map(function ($item) use ($jsonFields) {
            foreach ($jsonFields as $field) {
                $parts = explode('.', $field);

                if (count($parts) === 2) {
                    [$level1, $level2] = $parts;

                    if (!empty($item[$level1][$level2]) && is_string($item[$level1][$level2])) {
                        $decoded = json_decode($item[$level1][$level2], true);
                        $item[$level1][$level2] = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
                    }
                } elseif (count($parts) === 1) {
                    if (!empty($item[$field]) && is_string($item[$field])) {
                        $decoded = json_decode($item[$field], true);
                        $item[$field] = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
                    }
                }
            }

            return $item;

        }, $collection);
    }
}