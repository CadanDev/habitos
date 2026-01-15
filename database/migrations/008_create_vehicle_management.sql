-- ==================================================
-- SISTEMA DE GERENCIAMENTO DE VEÍCULOS
-- ==================================================

-- Tabela de Veículos
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

-- Tabela de Abastecimentos
CREATE TABLE IF NOT EXISTS abastecimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    veiculo_id INT NOT NULL,
    valor_pago DECIMAL(10, 2) NOT NULL,
    valor_litro DECIMAL(10, 2) NOT NULL,
    litros DECIMAL(10, 2) NOT NULL,
    combustivel VARCHAR(50) NOT NULL DEFAULT 'gasolina',
    quilometragem INT,
    data_abastecimento DATE NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (veiculo_id) REFERENCES veiculos(id) ON DELETE CASCADE,
    INDEX idx_veiculo (veiculo_id),
    INDEX idx_data (data_abastecimento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Manutenções
CREATE TABLE IF NOT EXISTS manutencoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    veiculo_id INT NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    valor_pago DECIMAL(10, 2) NOT NULL,
    descricao TEXT,
    gravidade VARCHAR(50) DEFAULT 'normal',
    eu_que_fiz BOOLEAN DEFAULT FALSE,
    quilometragem INT,
    data_manutencao DATE NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (veiculo_id) REFERENCES veiculos(id) ON DELETE CASCADE,
    INDEX idx_veiculo (veiculo_id),
    INDEX idx_tipo (tipo),
    INDEX idx_data (data_manutencao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
