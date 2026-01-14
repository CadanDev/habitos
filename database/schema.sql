-- ==================================================
-- SISTEMA DE HÁBITOS - SCHEMA DO BANCO DE DADOS
-- ==================================================

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS habitos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE habitos_db;

-- ==================================================
-- TABELA DE USUÁRIOS
-- ==================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acesso TIMESTAMP NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==================================================
-- TABELA DE HÁBITOS
-- ==================================================
CREATE TABLE IF NOT EXISTS habitos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome VARCHAR(200) NOT NULL,
    descricao TEXT,
    cor VARCHAR(7) DEFAULT '#3b82f6',
    icone VARCHAR(50) DEFAULT '✓',
    meta_semanal INT DEFAULT 7,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==================================================
-- TABELA DE REGISTROS DIÁRIOS
-- ==================================================
CREATE TABLE IF NOT EXISTS registros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    habito_id INT NOT NULL,
    data DATE NOT NULL,
    concluido BOOLEAN DEFAULT TRUE,
    notas TEXT,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (habito_id) REFERENCES habitos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_habito_data (habito_id, data),
    INDEX idx_data (data),
    INDEX idx_habito_data (habito_id, data)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==================================================
-- DADOS DE EXEMPLO (OPCIONAL)
-- ==================================================

-- Usuário de exemplo (senha: 123456)
INSERT INTO usuarios (nome, email, senha) VALUES 
('Usuário Teste', 'teste@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Hábitos de exemplo
INSERT INTO habitos (usuario_id, nome, descricao, cor, icone, meta_semanal) VALUES
(1, 'Exercícios Físicos', 'Fazer 30 minutos de exercícios', '#10b981', '💪', 5),
(1, 'Ler Livros', 'Ler pelo menos 20 páginas', '#8b5cf6', '📚', 7),
(1, 'Meditar', 'Meditar por 10 minutos', '#06b6d4', '🧘', 7),
(1, 'Beber Água', 'Beber 2 litros de água', '#3b82f6', '💧', 7);

-- Registros de exemplo (últimos 7 dias)
INSERT INTO registros (habito_id, data, concluido) VALUES
(1, DATE_SUB(CURDATE(), INTERVAL 6 DAY), TRUE),
(1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), TRUE),
(1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), TRUE),
(2, DATE_SUB(CURDATE(), INTERVAL 6 DAY), TRUE),
(2, DATE_SUB(CURDATE(), INTERVAL 5 DAY), TRUE),
(2, DATE_SUB(CURDATE(), INTERVAL 4 DAY), TRUE),
(2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), TRUE),
(2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), TRUE),
(2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), TRUE),
(3, DATE_SUB(CURDATE(), INTERVAL 6 DAY), TRUE),
(3, DATE_SUB(CURDATE(), INTERVAL 5 DAY), TRUE),
(3, DATE_SUB(CURDATE(), INTERVAL 3 DAY), TRUE),
(4, DATE_SUB(CURDATE(), INTERVAL 6 DAY), TRUE),
(4, DATE_SUB(CURDATE(), INTERVAL 5 DAY), TRUE),
(4, DATE_SUB(CURDATE(), INTERVAL 4 DAY), TRUE),
(4, DATE_SUB(CURDATE(), INTERVAL 3 DAY), TRUE),
(4, DATE_SUB(CURDATE(), INTERVAL 2 DAY), TRUE),
(4, DATE_SUB(CURDATE(), INTERVAL 1 DAY), TRUE),
(4, CURDATE(), TRUE);
