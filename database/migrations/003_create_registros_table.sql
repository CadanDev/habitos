-- Migração 003: Criar tabela de registros de hábitos

CREATE TABLE IF NOT EXISTS registros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    habito_id INT NOT NULL,
    data DATE NOT NULL,
    concluido BOOLEAN DEFAULT FALSE,
    notas TEXT,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (habito_id) REFERENCES habitos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_habito_data (habito_id, data),
    INDEX idx_data (data),
    INDEX idx_habito_data (habito_id, data)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
