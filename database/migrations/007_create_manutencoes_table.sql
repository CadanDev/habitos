-- Migração 007: Criar tabela de manutenções

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
