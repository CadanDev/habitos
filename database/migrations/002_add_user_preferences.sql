-- Migração 002: Adicionar campo de avatar e preferências de usuário

-- Adicionar campo avatar
ALTER TABLE usuarios 
ADD COLUMN avatar VARCHAR(255) NULL AFTER email,
ADD COLUMN tema VARCHAR(20) DEFAULT 'light' AFTER avatar;

-- Criar índice para busca por tema
CREATE INDEX idx_tema ON usuarios(tema);
