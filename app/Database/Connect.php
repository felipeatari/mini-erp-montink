<?php

namespace App\Database;

use App\Components\Singleton;
use PDO;
use PDOException;

class Connect extends Singleton
{
  private static ?object $pdo = null;

  private static ?string $db_host = null;
  private static ?string $db_port = null;
  private static ?string $db_name = null;
  private static ?string $db_user = null;
  private static ?string $db_passwd = null;
  private static ?array $db_options = null;

  private static bool $db_error = false;
  private static int $db_cod_error = 0;
  private static string $db_msg_error = '';

  /**
   * Realiza a conexão
   */
  public static function on(): ?object
  {
    self::$db_host = DB_HOST;
    self::$db_port = DB_PORT;
    self::$db_name = DB_NAME;
    self::$db_user = DB_USER;
    self::$db_passwd = DB_PASSWD;
    self::$db_options = DB_OPTIONS;

    try {
      self::$pdo = parent::new(new PDO(
        'mysql:host=' . self::$db_host .';'.
        'port=' . self::$db_port . ';' .
        'dbname=' . self::$db_name . ';',
        self::$db_user,
        self::$db_passwd,
        self::$db_options
      ));

      // Zera os dados da conexão
      self::$db_host = null;
      self::$db_port = null;
      self::$db_name = null;
      self::$db_user = null;
      self::$db_passwd = null;
      self::$db_options = [];
    }
    catch (PDOException $exception) {
      self::$pdo = null;
      self::$db_error = true;
      self::$db_cod_error = $exception->getCode();
      self::$db_msg_error = $exception->getMessage();
    }

    return self::$pdo;
  }

  public static function off()
  {
    self::$pdo = null;
  }

  public static function db_error()
  {
    return self::$db_error;
  }

  public static function db_cod_error()
  {
    return self::$db_cod_error;
  }

  public static function db_msg_error()
  {
    return self::$db_msg_error;
  }

  public static function db_error_data(): object
  {
    return (object) [
      'code' => self::$db_cod_error,
      'message' => self::$db_msg_error,
    ];
  }
}