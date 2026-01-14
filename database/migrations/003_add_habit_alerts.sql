-- ==================================================
-- MIGRAÇÃO: Tabela de alertas por hábito
-- ==================================================

CREATE TABLE IF NOT EXISTS alertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    habito_id INT NOT NULL UNIQUE,
    ativo BOOLEAN DEFAULT TRUE,
    tipo ENUM('dia','hora','minuto') NOT NULL,
    hora TIME NULL,
    dias VARCHAR(20) NULL, -- Ex: "1,3,5" (Seg=1 ... Dom=0)
    intervalo_minutos INT NULL,
    mensagem VARCHAR(255) NULL,
    ultima_execucao DATETIME NULL,
    FOREIGN KEY (habito_id) REFERENCES habitos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
