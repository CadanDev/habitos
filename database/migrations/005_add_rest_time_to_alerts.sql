-- Migração 005: Adicionar tempo de descanso aos alertas

ALTER TABLE alertas 
    ADD COLUMN descanso_segundos INT NULL AFTER intervalo_minutos;
