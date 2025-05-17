<?php

namespace App\Components;

class Singleton
{
  /**
   * Guardar todas as instâncias
   *
   * @var array
   */
  private static $instance = [];

  /**
   * Guarda tanto os parâmetros do construct como um objeto
   *
   * @var array
   */
  private static $params = null;

  /**
   * Método responsável por retornar a instância única de um objeto
   * Exemplo: Ao invés de new Class() será Class::new()
   *
   * @param object|mixed $object Pode receber uma instância ou parâmteros mistos
   *
   * @return object
   */
  public static function new(): object
  {
    if (func_num_args() == 1) {
      $object = func_get_arg(0);
    }

    // Verifica se o parâmetro existe e se é um objeto
    if (isset($object) && (is_object($object))) {
      // Recupera o nome da instância (objeto)
      $name_object = get_class($object);

      // Se essa instância não existir então será atribuída
      if (! array_key_exists($name_object, self::$instance)) {
        self::$instance[$name_object] = $object;
      }

      return self::$instance[$name_object];
    }

    // Recupera o nome da classe que será instanciada
    $class = static::class;

    // Se essa instância não existir então será criada
    if (! array_key_exists($class, self::$instance)) {
      self::$instance[$class] = new $class();
    }

    // Todas as classes que herdam Singleton, terão que ter esse método declarado como construtor
    if (method_exists($class, 'construct')) {
      call_user_func_array([self::$instance[$class], 'construct'],func_get_args());
    }

    return self::$instance[$class];
  }

  /**
   * Retorna uma ou mais instância(s) ou um array vazio
   *
   * @return array
   */
  public static function getInstance(): array
  {
    return self::$instance;
  }

  /**
   * Retorna todos os parâmetros possíveis ou um array vazio
   *
   * @return array
   */
  public static function getParams()
  {
    return self::$params;
  }

  final private function __construct()
  {
  }

  final private function __clone()
  {
  }
}