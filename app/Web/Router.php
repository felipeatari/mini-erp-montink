<?php

namespace App\Web;

use App\Web\Controllers\ErroController;
use Closure;

class Router
{
    private array $controllers = [];
    private array $params = [];
    private array $routes = [];
    private bool $callback = false;
    private int $http_status_code = 200;
    private string $http_method;
    private string|Closure $controller;
    private string|Closure $method;
    private string $uri;

    public function __construct()
    {
        $this->uri = $_GET['url'] ?? '/';
        $this->http_method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public static function redirect(string $route): void
    {
        header('Location: ' . URL . $route);
        die;
    }

    private function convert_URL_str_to_URL_arr(string $url = null): array
    {
        $url = explode('/', $url);
        $url = array_values(array_filter($url));

        if (! $url) $url[] = '/';

        return $url;
    }

    private function add_route(string $http_method, string $route, string|Closure $action)
    {
        $this->routes[] = [
            'http_method' => $http_method,
            'route' => $route,
            'action' => $action
        ];
    }

    public function get(string $route, string|Closure $action)
    {
        $this->add_route('get', $route, $action);
    }

    public function post(string $route, string|Closure $action)
    {
        $this->add_route('post', $route, $action);
    }

    public function put(string $route, string|Closure $action)
    {
        $this->add_route('put', $route, $action);
    }

    public function patch(string $route, string|Closure $action)
    {
        $this->add_route('patch', $route, $action);
    }

    public function delete(string $route, string|Closure $action)
    {
        $this->add_route('delete', $route, $action);
    }

    public function head(string $route, string|Closure $action)
    {
        $this->add_route('head', $route, $action);
    }

    public function options(string $route, string|Closure $action)
    {
        $this->add_route('options', $route, $action);
    }

    private function make_router(string|Closure $action, array $params = []): void
    {
        // Verifica se é uma closure/callable ou um(a) classe/controller
        if (is_callable($action)) {
            $this->callback = true;
            $this->controllers[] = $action;
            $this->method = $action;
        } else {
            $action = explode('->', $action);

            $controller = ucfirst($action[0]);

            $namespace = 'App\\Web\\Controllers\\' . $controller;

            $this->controllers[] = $namespace;
            $this->method = $action[1];
        }

        // Verifica se há parâmetro(s)
        if (! empty($params)) $this->params = $params;
    }

    public function on(): Router
    {
        $uri = $this->convert_URL_str_to_URL_arr($this->uri);
        $http_method = strtolower($this->http_method);
        $http_method_route = [];
        $error_405 = false;

        foreach ($this->routes as $route):
            $uri_route = $this->convert_URL_str_to_URL_arr($route['route']);

            // Verifica se a rota é dinâmica
            if (preg_match('/(\{[\w]+\})|(\:[\w]+)/', $route['route'])) {
                // Recupera os campos estáticos da rota
                $route_static_fields = array_map(function ($item) {
                    if (! preg_match('/(\{[\w]+\})|(\:[\w]+)/', $item)) return $item;
                }, $uri_route);
                // Elimina os campos vazios da rota e ordena de forma numerada
                $route_static_fields = array_values(array_filter($route_static_fields));

                // Recupera os campos estáticos da URI
                $uri_static_fields = array_intersect($route_static_fields, $uri);

                // Verifica se a rota e a URI tem a mesma extensão e se os campos estáticos de ambas são iguais
                if ((count($uri_route) === count($uri)) and ($uri_static_fields === $route_static_fields)) {
                    // Verifica se método HTTP requisitado é o mesmo que foi definido para a rota
                    if ($route['http_method'] !== $http_method) {
                        if ($route['http_method'] !== $http_method) {
                            $error_405 = true;
                        } else {
                            $http_method_route[] = $http_method;
                        }
                    }

                    $this->make_router($route['action'], array_diff($uri, $uri_route));
                }
            }

            // Verifica se a rota é estática
            if (($uri_route === $uri)) {
                // Verifica se método HTTP requisitado é o mesmo que foi definido para a rota
                if ($route['http_method'] !== $http_method) {
                    $error_405 = true;
                } else {
                    $http_method_route[] = $http_method;
                }

                $this->make_router($route['action']);
            }
        endforeach;

        if (in_array($http_method, $http_method_route)) {
            $error_405 = false;
        }

        if ($error_405) {
            $this->http_status_code = 405;

            return $this;
        }

        if (empty($this->controllers)) {
            $this->http_status_code = 404;

            return $this;
        }

        $error_404 = [];

        foreach ($this->controllers as $controller):
            if (is_callable($controller) or class_exists($controller)) {

                $this->controller = $controller;

                continue;
            }

            $error_404[] = 1;
        endforeach;

        if (count($error_404) === count($this->controllers)) {
            $this->http_status_code = 404;

            return $this;
        }

        if (! is_callable($this->controller)) {
            if (! method_exists($this->controller, $this->method)) $this->http_status_code = 405;
        }

        return $this;
    }

    public function dispatcher()
    {
        http_response_code($this->http_status_code);

        if ($this->http_status_code !== 200) {
            if ($this->http_status_code === 405) $message_error = 'Método não implementado';
            if ($this->http_status_code === 404) $message_error = 'Pagina não encontrada';

            if ($this->callback) {
                $this->api = true;

                die(ErroController::error_api($this->http_status_code, $message_error));
            }

            return ErroController::error($this->http_status_code, $message_error);
        }

        if ($this->callback) {
            $this->api = true;

            return call_user_func_array($this->method, $this->params) ?? '';
        }

        return call_user_func_array([new $this->controller, $this->method], $this->params) ?? '';
    }
}
