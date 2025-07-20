-- Configuração inicial
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
CREATE DATABASE IF NOT EXISTS mini_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mini_erp;
-- Produtos (não dependem de variações diretamente)
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    ativo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Grupos de variações (ex.: "Cor", "Tamanho", "Preto+M")
CREATE TABLE grupos_variacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Variações (ex.: "Preto", "Branco", "P", "M")
CREATE TABLE variacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Relação Grupo ↔ Variações (muitos para muitos)
CREATE TABLE grupo_variacao_variacao (
    grupo_id INT NOT NULL,
    variacao_id INT NOT NULL,
    PRIMARY KEY (grupo_id, variacao_id),
    FOREIGN KEY (grupo_id) REFERENCES grupos_variacoes(id) ON DELETE CASCADE,
    FOREIGN KEY (variacao_id) REFERENCES variacoes(id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Estoque: vincula produto e (opcionalmente) um grupo de variação
CREATE TABLE estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    grupo_id INT NULL,
    quantidade INT NOT NULL DEFAULT 0,
    preco DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    UNIQUE (produto_id, grupo_id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (grupo_id) REFERENCES grupos_variacoes(id) ON DELETE
    SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Pedidos: dados de clientes podem ir para outra tabela posteriormente
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subtotal DECIMAL(10, 2),
    frete DECIMAL(10, 2),
    total DECIMAL(10, 2) NOT NULL,
    status ENUM ('pendente', 'pago', 'enviado', 'cancelado') DEFAULT 'pendente',
    cliente_email VARCHAR(255),
    cliente_endereco TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Itens dos pedidos
CREATE TABLE pedido_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    grupo_id INT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (grupo_id) REFERENCES grupos_variacoes(id) ON DELETE
    SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Cupons
CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    desconto_percentual DECIMAL(5, 2) NOT NULL,
    valor_minimo DECIMAL(10, 2) DEFAULT 0.00,
    validade DATE,
    ativo TINYINT (1) DEFAULT 1
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- SEEDS
-- Produtos e Estoque
-- Inserts iniciais (Produtos, Grupos e Variações)
INSERT INTO produtos (nome)
VALUES ('Camiseta Básica'),
    ('Tênis Esportivo');
INSERT INTO grupos_variacoes (nome)
VALUES ('Cor: Preto'),
    ('Tamanho: 38');
INSERT INTO variacoes (nome)
VALUES ('Preto'),
    ('Branco'),
    ('P'),
    ('M'),
    ('38');
-- Relacionando variações aos grupos
INSERT INTO grupo_variacao_variacao (grupo_id, variacao_id)
VALUES (1, 1),
    -- Cor: Preto
    (2, 5);
-- Tamanho: 38
-- Estoque com preço (Produto + Grupo de Variação)
INSERT INTO estoque (produto_id, grupo_id, quantidade, preco)
VALUES (1, 1, 10, 59.90),
    -- Camiseta Básica - Cor: Preto
    (2, NULL, 8, 199.00),
    -- Tênis Esportivo (sem variação)
    (2, 2, 8, 199.00);
-- Tênis Esportivo (com variação)
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