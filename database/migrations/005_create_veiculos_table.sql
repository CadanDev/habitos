-- Migração 005: Criar tabela de veículos

CREATE TABLE IF NOT EXISTS veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    marca VARCHAR(100) NOT NULL,
    cor VARCHAR(50),
    ano INT NOT NULL,
    apelido VARCHAR(100),
    quilometragem INT DEFAULT 0,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_marca_modelo (marca, modelo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
