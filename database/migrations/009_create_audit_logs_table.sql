-- Migração 009: Criar tabela de logs de auditoria

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(100) NOT NULL,
    tabela VARCHAR(50) NOT NULL,
    registro_id INT,
    dados_anteriores JSON,
    dados_novos JSON,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_tabela (tabela),
    INDEX idx_data (data_criacao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
