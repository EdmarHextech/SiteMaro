-- Schema do banco de dados do site marocamargo.com.br
-- Importe este arquivo no phpMyAdmin (cPanel/HostGator) ou via linha de comando:
--   mysql -u SEU_USUARIO -p SEU_BANCO < schema.sql

CREATE TABLE IF NOT EXISTS eventos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    tipo ENUM('palestra', 'consultoria', 'workshop', 'outro') NOT NULL DEFAULT 'palestra',
    local VARCHAR(255) NOT NULL,
    link_inscricao VARCHAR(500) DEFAULT NULL,
    data_evento DATE NOT NULL,
    hora_evento TIME NOT NULL,
    vagas SMALLINT UNSIGNED DEFAULT NULL,
    descricao TEXT DEFAULT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_data_evento (data_evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Alguns eventos de exemplo (pode apagar pelo painel admin depois)
INSERT INTO eventos (titulo, tipo, local, link_inscricao, data_evento, hora_evento, vagas, descricao) VALUES
('Palestra: Diálogo e World Café em organizações', 'palestra', 'São Paulo, SP', 'https://exemplo.com/inscricao', DATE_ADD(CURDATE(), INTERVAL 20 DAY), '19:00:00', 40, 'Uma conversa sobre metodologias participativas para times e comunidades.'),
('Consultoria de Cultura Organizacional', 'consultoria', 'Online', 'https://exemplo.com/consultoria', DATE_ADD(CURDATE(), INTERVAL 35 DAY), '10:00:00', NULL, 'Sessão de consultoria para líderes e educadores.');
