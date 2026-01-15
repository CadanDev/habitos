-- Migração 001: Criar tabela de usuários

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NULL,
    tema VARCHAR(20) DEFAULT 'light',
    tts_voice VARCHAR(100) NULL,
    tts_volume FLOAT DEFAULT 1.0,
    tts_rate FLOAT DEFAULT 1.0,
    tts_pitch FLOAT DEFAULT 1.0,
    tts_provider VARCHAR(20) DEFAULT 'chrome',
    tts_voice_openai VARCHAR(50) DEFAULT 'nova',
    ultimo_acesso TIMESTAMP NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_tema (tema),
    INDEX idx_ultimo_acesso (ultimo_acesso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
