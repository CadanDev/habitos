-- Migração 004: Adicionar preferências de TTS ao usuário

ALTER TABLE usuarios 
    ADD COLUMN tts_voice VARCHAR(100) NULL AFTER tema,
    ADD COLUMN tts_volume FLOAT DEFAULT 1.0 AFTER tts_voice,
    ADD COLUMN tts_rate FLOAT DEFAULT 1.0 AFTER tts_volume,
    ADD COLUMN tts_pitch FLOAT DEFAULT 1.0 AFTER tts_rate;
