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
define('PEDIDO', URL . '/pedido');

// Caminho para salvar a sessão
define('SESSION', __DIR__ . '/../storage/temp/session');

// Conexão com DB
define('DB_HOST', 'mysql');
define('DB_PORT', '3306');
define('DB_NAME', 'erp');
define('DB_USER', 'root');
define('DB_PASSWD', 'root');

// Configuração do PHPMailer
define('MAILER_HOST', 'sandbox.smtp.mailtrap.io');
define('MAILER_SMTP_AUTH', true);
define('MAILER_PORT', 2525);
define('MAILER_USERNAME', '8e64353629550d');
define('MAILER_PASSWD', '74ac090f22bfe5');
define('MAILER_SENDER_EMAIL', 'from@example.com');
define('MAILER_SENDER_NAME', 'Mailer');

// Opções de configuração do banco de dados
define('DB_OPTIONS', [
  PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_CASE => PDO::CASE_NATURAL
]);