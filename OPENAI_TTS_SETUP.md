# 🎤 Configuração do TTS OpenAI

## Pré-requisitos

1. Conta na OpenAI: https://platform.openai.com/
2. API Key gerada
3. Créditos na conta (pago por uso)

## Passo 1: Obter API Key

1. Acesse https://platform.openai.com/api-keys
2. Clique em "Create new secret key"
3. Copie a chave (ela começa com `sk-proj-...`)
4. **IMPORTANTE:** Guarde essa chave em local seguro, ela não será mostrada novamente!

## Passo 2: Configurar no Projeto

### Opção A: Variável de Ambiente do Sistema (Recomendado)

**Windows:**
```powershell
# Temporário (apenas sessão atual)
$env:OPENAI_API_KEY = "sk-proj-sua-chave-aqui"

# Permanente (para o usuário)
[System.Environment]::SetEnvironmentVariable('OPENAI_API_KEY', 'sk-proj-sua-chave-aqui', 'User')

# Reinicie o servidor web após configurar
```

**Linux/Mac:**
```bash
# Adicionar no ~/.bashrc ou ~/.zshrc
export OPENAI_API_KEY="sk-proj-sua-chave-aqui"

# Ou temporariamente
export OPENAI_API_KEY="sk-proj-sua-chave-aqui"
```

### Opção B: Arquivo .env (Alternativa)

1. Crie um arquivo `.env` na pasta `config/`:
```bash
OPENAI_API_KEY=sk-proj-sua-chave-aqui
```

2. Adicione ao `.gitignore`:
```
.env
```

3. Modifique `config/env.php` para carregar o arquivo:
```php
<?php
// Carregar arquivo .env se existir
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}
```

## Passo 3: Testar

1. Abra o dashboard
2. Clique no ⚙️ do bonequinho
3. Marque "Fala ativada"
4. Selecione "OpenAI (Premium)" no Tipo de Voz
5. Escolha uma voz (recomendo "Nova" ou "Shimmer")
6. Teste marcando/desmarcando um hábito

## Passo 4: Verificar Erros

Abra o Console do navegador (F12) e veja se há erros:

- **"OpenAI API não configurada"** → API key não foi definida
- **"401 Unauthorized"** → API key inválida
- **"429 Too Many Requests"** → Limite de requisições excedido
- **"insufficient_quota"** → Sem créditos na conta OpenAI

## Custos

- Modelo `tts-1`: $0.015 por 1000 caracteres
- Modelo `tts-1-hd`: $0.030 por 1000 caracteres (melhor qualidade)

**Estimativa de uso:**
- Mensagem média: 50 caracteres
- 100 celebrações/dia = 5.000 caracteres
- Custo diário: ~$0.075 (tts-1) ou ~$0.15 (tts-1-hd)
- Custo mensal: ~$2.25 (tts-1) ou ~$4.50 (tts-1-hd)

## Segurança

⚠️ **NUNCA** exponha sua API key no código frontend!
⚠️ **SEMPRE** use o arquivo PHP como proxy (api/tts.php)
⚠️ Adicione `.env` ao `.gitignore` se usar essa opção

## Vozes Disponíveis

- **Nova** 👩 - Feminina energética (recomendado)
- **Shimmer** 👩 - Feminina suave
- **Alloy** 🧑 - Neutra equilibrada
- **Echo** 👨 - Masculina clara
- **Fable** 👨 - Britânica masculina
- **Onyx** 👨 - Masculina profunda

## Fallback

Se o TTS da OpenAI falhar por qualquer motivo, o sistema automaticamente usa o TTS do navegador como backup.

## Desativar OpenAI TTS

Se quiser voltar para o TTS gratuito do navegador:
1. Clique no ⚙️
2. Selecione "Navegador (Grátis)" no Tipo de Voz

---

**Pronto!** 🎉 Agora você tem acesso a vozes ultra-realistas da OpenAI!
