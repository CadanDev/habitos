-- Migração 006: Adicionar mensagens específicas para fases dos alertas

-- Renomear campo mensagem para mensagem_alerta
ALTER TABLE alertas 
    CHANGE COLUMN mensagem mensagem_alerta VARCHAR(255) NULL;

-- Adicionar novos campos de mensagem
ALTER TABLE alertas 
    ADD COLUMN mensagem_descanso VARCHAR(255) NULL AFTER mensagem_alerta,
    ADD COLUMN mensagem_fim_descanso VARCHAR(255) NULL AFTER mensagem_descanso;

-- Adicionar campo para controlar se descanso requer trigger manual
ALTER TABLE alertas 
    ADD COLUMN descanso_requer_trigger BOOLEAN DEFAULT TRUE AFTER descanso_segundos;

-- Adicionar campo para rastrear estado atual do alerta
ALTER TABLE alertas 
    ADD COLUMN estado_atual ENUM('aguardando_alerta', 'alerta_disparado', 'em_descanso') DEFAULT 'aguardando_alerta' AFTER descanso_requer_trigger;
