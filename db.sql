-- Cria o banco de dados com UTF-8 completo
CREATE DATABASE IF NOT EXISTS `erp`
    CHARACTER SET `utf8mb4`
    COLLATE `utf8mb4_unicode_ci`;

USE `erp`;

-- Tabela de Produtos
CREATE TABLE `produtos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(255) NOT NULL,
    `preco` DECIMAL(10,2) NOT NULL,
    `variacoes` JSON,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Estoque
CREATE TABLE `estoque` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `produto_id` INT NOT NULL,
    `quantidade` INT NOT NULL DEFAULT 0,
    `atualizado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE
);
