<?php

namespace App\Components;

class View
{
    private static string $title = '';
    private static string $app = '';
    private static string $admin = '';
    private static string $error = '';

    /**
     * Método responsável por carregar o título da página atual
     *
     * @param string $title Título da página atual
     *
     * @return void
     */
    public static function title(string $title = null): void
    {
        self::$title = $title;
    }

    public static function app(string $app = ''): void
    {
        self::$app = $app;
    }

    public static function admin(string $admin = ''): void
    {
        self::$admin = $admin;
    }

    public static function error(string $error = ''): void
    {
        self::$error = $error;
    }

    /**
     * Carrega arquivos PHP
     *
     * @param string $path_file Caminho do arquivos
     * @param array $vars_dynamic Dados que serão carregados dinamicamente
     *
     * @return string
     */
    private static function load_file_php(string $path_file, array $vars_dynamic = []): string
    {
        if (! empty($vars_dynamic)) extract($vars_dynamic);

        ob_start();
        require_once __DIR__ . '/../../' . $path_file;
        $load_file = ob_get_contents();
        ob_clean();

        return $load_file;
    }

    /**
     * Método responsável por renderizar a view
     *
     * @param string $extension Extensão do arquivo da visão: .php ou .html
     * @param array $vars_dynamic Dados que serão carregados dinamicamente
     *
     * @return string Página com os campos substituídos
     */
    public static function view(array $vars_dynamic = []): ?string
    {
        if (self::$app) {
            $view = 'resources/views/' . self::$app . '.php';
        } elseif (self::$admin) {
            $view = 'resources/views/' . self::$admin . '.php';
        } elseif (self::$error) {
            $view = 'resources/views/' . self::$error . '.php';
        } else {
            return null;
        }

        if (! file_exists($view)) return null;

        return self::load_file_php($view, $vars_dynamic);
    }

    public static function file_exists_template(string $file = ''): string
    {
        return file_exists(ltrim($file, '/')) ? $file : '';
    }

    /**
     * Método responsável por carregar o layout
     *
     * @param string $content Recebe o conteúdo da página
     *
     * @return string
     */
    public static function layout(string $layout = 'app', ?string $content = '')
    {
        if ($layout === 'admin') {
            $view = self::$admin;
        } else {
            $view = self::$app;
        }

        $layout_css = '/resources/css/' . $layout . '.css';
        $layout_js = '/resources/js/' . $layout . '.js';
        $view_css = '/resources/css/' . $view . '.css';
        $view_js = '/resources/js/' . $view . '.js';

        $layout = 'resources/layouts/' . $layout . '.php';

        if (! file_exists($layout)) return null;

        $template = self::load_file_php($layout, [
            'layout_title' => self::$title,
            'layout_css' => self::file_exists_template($layout_css),
            'layout_js' => self::file_exists_template($layout_js),
            'view_css' => self::file_exists_template($view_css),
            'view_js' => self::file_exists_template($view_js),
            'content' => $content
        ]);

        // Renderiza em linha e sem espaço entre as tags
        $template = str_replace("\n", "", $template);
        $template = preg_replace('/\s+/', ' ', trim($template));
        $template = str_replace('> <', '><', $template);

        echo $template;
        die;
    }
}
