-- Cria o banco de dados com UTF-8 completo
CREATE DATABASE IF NOT EXISTS `erp`
    CHARACTER SET `utf8mb4`
    COLLATE `utf8mb4_unicode_ci`;

USE `erp`;

-- Tabela de Produtos
CREATE TABLE IF NOT EXISTS `produtos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(255) NOT NULL,
    `preco` INT NOT NULL,
    `variacoes` JSON,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Estoque
CREATE TABLE IF NOT EXISTS `estoque` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `produto_id` INT NOT NULL,
    `quantidade` INT NOT NULL DEFAULT 0,
    `atualizado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE
);

-- Tabela de Cupons
CREATE TABLE IF NOT EXISTS `cupons` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(50) NOT NULL,
    `desconto` INT NOT NULL DEFAULT 0,
    `validade` DATE NOT NULL
);

-- Tabela de Pedidos
CREATE TABLE IF NOT EXISTS `pedidos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `total` INT NOT NULL DEFAULT 0,
    `frete` INT DEFAULT 0,
    `desconto` INT DEFAULT 0,
    `qtd_produtos` INT NOT NULL DEFAULT 0,
    `produtos` JSON NOT NULL,
    `cupom` VARCHAR(50),
    `cidade` VARCHAR(255) NOT NULL,
    `endereco` VARCHAR(255) NOT NULL,
    `estado` VARCHAR(255) NOT NULL,
    `bairro` VARCHAR(255) NOT NULL,
    `cep` VARCHAR(255) NOT NULL,
    `status` ENUM(
      'pendente', 'aprovado', 'cancelado'
    ) NOT NULL DEFAULT 'pendente',
    `nome` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL
);
