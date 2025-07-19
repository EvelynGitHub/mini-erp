CREATE DATABASE IF NOT EXISTS mini_erp;
USE mini_erp;
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    variacao VARCHAR(255) NULL,
    quantidade INT DEFAULT 0,
    FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE
);
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subtotal DECIMAL(10, 2),
    frete DECIMAL(10, 2),
    total DECIMAL(10, 2),
    status ENUM ('pendente', 'pago', 'enviado', 'cancelado') DEFAULT 'pendente',
    cliente_email VARCHAR(255),
    cliente_endereco TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE,
    desconto_percentual DECIMAL(5, 2),
    valor_minimo DECIMAL(10, 2),
    validade DATE,
    ativo TINYINT (1) DEFAULT 1
);
CREATE TABLE pedido_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos (id),
    FOREIGN KEY (produto_id) REFERENCES produtos (id)
);
-- SEEDS
-- Produtos e Estoque
INSERT INTO produtos (nome, preco)
VALUES ('Camiseta Básica', 49.90),
    ('Calça Jeans', 159.90),
    ('Tênis Esportivo', 249.90),
    ('Boné Trucker', 39.90);
INSERT INTO estoque (produto_id, variacao, quantidade)
VALUES (1, 'P', 10),
    (1, 'M', 8),
    (1, 'G', 5),
    (2, '38', 6),
    (2, '40', 4),
    (3, '41', 7),
    (3, '42', 6),
    (4, NULL, 15);
-- Cupons
INSERT INTO cupons (
        codigo,
        desconto_percentual,
        valor_minimo,
        validade,
        ativo
    )
VALUES (
        'DESCONTO10',
        10,
        100.00,
        DATE_ADD(CURDATE(), INTERVAL 30 DAY),
        1
    ),
    (
        'FRETEGRATIS',
        100,
        200.00,
        DATE_ADD(CURDATE(), INTERVAL 15 DAY),
        1
    ),
    (
        'PROMO5',
        5,
        50.00,
        DATE_ADD(CURDATE(), INTERVAL 60 DAY),
        1
    );
-- Pedidos falsos para testar Webhook
INSERT INTO pedidos (
        subtotal,
        frete,
        total,
        status,
        cliente_email,
        cliente_endereco
    )
VALUES (
        120.00,
        15.00,
        135.00,
        'pendente',
        'cliente1@teste.com',
        'Rua Alpha, 123 - SP'
    ),
    (
        300.00,
        0.00,
        300.00,
        'pago',
        'cliente2@teste.com',
        'Rua Beta, 456 - RJ'
    ),
    (
        80.00,
        20.00,
        100.00,
        'enviado',
        'cliente3@teste.com',
        'Rua Gamma, 789 - MG'
    );