<?php

// Define o meta charset
define('CHARSET', 'UTF-8');

// Define a URL principal
define('URL', 'http://localhost');

// Define as rotas do projeto
define('HOME', URL . '/');
define('PRODUTO', URL . '/produto');
define('CARRINHO', URL . '/carrinho');
define('CUPOM', URL . '/cupom');

// Caminho para salvar a sessão
define('SESSION', __DIR__ . '/storage/temp/session');

// Conexão com o banco de dados local
define('DB_HOST', 'mysql');
define('DB_PORT', '3306');
define('DB_NAME', 'erp');
define('DB_USER', 'root');
define('DB_PASSWD', 'root');

// Opções de configuração do banco de dados
define('DB_OPTIONS', [
  PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_CASE => PDO::CASE_NATURAL
]);