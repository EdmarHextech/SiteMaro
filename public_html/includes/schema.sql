-- Schema do banco de dados do site marocamargo.com.br
-- Importe este arquivo no phpMyAdmin (cPanel/HostGator) ou via linha de comando:
--   mysql -u SEU_USUARIO -p SEU_BANCO < schema.sql

CREATE TABLE IF NOT EXISTS eventos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    tipo ENUM('palestra', 'consultoria', 'workshop', 'outro') NOT NULL DEFAULT 'palestra',
    local VARCHAR(255) NOT NULL,
    link_inscricao VARCHAR(500) DEFAULT NULL,
    video_youtube_url VARCHAR(500) DEFAULT NULL,
    data_evento DATE NOT NULL,
    hora_evento TIME NOT NULL,
    vagas SMALLINT UNSIGNED DEFAULT NULL,
    descricao TEXT DEFAULT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_data_evento (data_evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Instalação já existente (banco criado antes desta coluna existir)? Rode manualmente:
--   ALTER TABLE eventos ADD COLUMN video_youtube_url VARCHAR(500) DEFAULT NULL AFTER link_inscricao;
-- (verifique antes com `SHOW COLUMNS FROM eventos LIKE 'video_youtube_url';` para não duplicar)

-- Alguns eventos de exemplo (pode apagar pelo painel admin depois)
INSERT INTO eventos (titulo, tipo, local, link_inscricao, data_evento, hora_evento, vagas, descricao) VALUES
('Palestra: Diálogo e World Café em organizações', 'palestra', 'São Paulo, SP', 'https://exemplo.com/inscricao', DATE_ADD(CURDATE(), INTERVAL 20 DAY), '19:00:00', 40, 'Uma conversa sobre metodologias participativas para times e comunidades.'),
('Consultoria de Cultura Organizacional', 'consultoria', 'Online', 'https://exemplo.com/consultoria', DATE_ADD(CURDATE(), INTERVAL 35 DAY), '10:00:00', NULL, 'Sessão de consultoria para líderes e educadores.');

-- ---------- Blog ----------
CREATE TABLE IF NOT EXISTS posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    resumo VARCHAR(500) DEFAULT NULL,
    conteudo_html LONGTEXT NOT NULL,
    imagem_capa VARCHAR(255) DEFAULT NULL,
    status ENUM('rascunho', 'publicado') NOT NULL DEFAULT 'rascunho',
    publicado_em DATETIME DEFAULT NULL,
    autor VARCHAR(100) NOT NULL DEFAULT 'Maro Camargo',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status_publicado (status, publicado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Galeria de fotos ----------
CREATE TABLE IF NOT EXISTS galeria_fotos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    evento_id INT UNSIGNED DEFAULT NULL,
    arquivo VARCHAR(255) NOT NULL,
    legenda VARCHAR(255) DEFAULT NULL,
    pessoas_mencionadas VARCHAR(500) DEFAULT NULL,
    ordem SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_evento_id (evento_id),
    CONSTRAINT fk_galeria_evento FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Loja ----------
CREATE TABLE IF NOT EXISTS produtos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('fisico', 'sessao') NOT NULL DEFAULT 'fisico',
    nome VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    descricao TEXT DEFAULT NULL,
    preco_centavos INT UNSIGNED NOT NULL,
    imagem VARCHAR(255) DEFAULT NULL,
    permite_dedicatoria TINYINT(1) NOT NULL DEFAULT 0,
    -- Campos usados só por produtos físicos (cálculo de frete via Melhor Envio, fase futura)
    peso_gramas SMALLINT UNSIGNED DEFAULT NULL,
    altura_cm DECIMAL(6,2) DEFAULT NULL,
    largura_cm DECIMAL(6,2) DEFAULT NULL,
    comprimento_cm DECIMAL(6,2) DEFAULT NULL,
    -- Campos usados só por produtos de sessão (agendamento via Cal.com, fase futura)
    duracao_minutos SMALLINT UNSIGNED DEFAULT NULL,
    calcom_link VARCHAR(500) DEFAULT NULL,
    estoque SMALLINT UNSIGNED DEFAULT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipo_ativo (tipo, ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pedidos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    cliente_nome VARCHAR(255) NOT NULL,
    cliente_email VARCHAR(255) NOT NULL,
    cliente_telefone VARCHAR(30) DEFAULT NULL,
    cliente_cpf VARCHAR(20) DEFAULT NULL,
    endereco_cep VARCHAR(9) DEFAULT NULL,
    endereco_logradouro VARCHAR(255) DEFAULT NULL,
    endereco_numero VARCHAR(20) DEFAULT NULL,
    endereco_complemento VARCHAR(100) DEFAULT NULL,
    endereco_bairro VARCHAR(100) DEFAULT NULL,
    endereco_cidade VARCHAR(100) DEFAULT NULL,
    endereco_uf CHAR(2) DEFAULT NULL,
    subtotal_centavos INT UNSIGNED NOT NULL,
    frete_centavos INT UNSIGNED NOT NULL DEFAULT 0,
    total_centavos INT UNSIGNED NOT NULL,
    metodo_pagamento ENUM('credit_card', 'debit_card', 'pix') DEFAULT NULL,
    status ENUM('pendente', 'pago', 'recusado', 'cancelado', 'reembolsado') NOT NULL DEFAULT 'pendente',
    mp_payment_id VARCHAR(64) DEFAULT NULL,
    mp_status_detail VARCHAR(100) DEFAULT NULL,
    ip_criacao VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_mp_payment_id (mp_payment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pedido_itens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNSIGNED NOT NULL,
    produto_id INT UNSIGNED NOT NULL,
    produto_nome_snapshot VARCHAR(255) NOT NULL,
    preco_unitario_centavos INT UNSIGNED NOT NULL,
    quantidade SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    dedicatoria_texto VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedido_item_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_pedido_item_produto FOREIGN KEY (produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Registra qual serviço de frete (PAC/SEDEX/etc.) foi escolhido no pedido, útil pro admin
-- e para uma futura geração de etiqueta de envio.
-- (MySQL não suporta ADD COLUMN IF NOT EXISTS; confira antes com SHOW COLUMNS FROM pedidos
-- LIKE 'frete_servico' se estiver rodando num banco que já existia antes desta linha.)
ALTER TABLE pedidos ADD COLUMN frete_servico VARCHAR(50) DEFAULT NULL AFTER frete_centavos;
