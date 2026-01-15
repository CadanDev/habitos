-- Migração 004: Criar tabela de alertas de hábitos

CREATE TABLE IF NOT EXISTS alertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    habito_id INT NOT NULL UNIQUE,
    ativo BOOLEAN DEFAULT TRUE,
    tipo ENUM('dia','hora','minuto') NOT NULL,
    hora TIME NULL,
    dias VARCHAR(20) NULL,
    intervalo_minutos INT NULL,
    descanso_segundos INT NULL,
    mensagem_alerta VARCHAR(255) NULL,
    mensagem_descanso VARCHAR(255) NULL,
    mensagem_fim_descanso VARCHAR(255) NULL,
    descanso_requer_trigger BOOLEAN DEFAULT TRUE,
    estado_atual ENUM('aguardando_alerta', 'alerta_disparado', 'em_descanso') DEFAULT 'aguardando_alerta',
    ultima_execucao DATETIME NULL,
    FOREIGN KEY (habito_id) REFERENCES habitos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
