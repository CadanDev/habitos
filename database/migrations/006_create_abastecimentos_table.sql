-- Migração 006: Criar tabela de abastecimentos

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
