-- Migração 007: Adicionar seleção de provider TTS e voz OpenAI

ALTER TABLE usuarios 
    ADD COLUMN tts_provider VARCHAR(20) DEFAULT 'chrome' AFTER tts_pitch,
    ADD COLUMN tts_voice_openai VARCHAR(50) DEFAULT 'nova' AFTER tts_provider;
