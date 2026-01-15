-- Migração 008: Criar tabela de preferências de usuário

CREATE TABLE IF NOT EXISTS user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    notificacoes_ativas BOOLEAN DEFAULT TRUE,
    som_ativo BOOLEAN DEFAULT TRUE,
    vibracao_ativa BOOLEAN DEFAULT FALSE,
    tema_escuro BOOLEAN DEFAULT FALSE,
    idioma VARCHAR(10) DEFAULT 'pt-BR',
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
