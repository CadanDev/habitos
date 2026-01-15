# 🔐 Autenticação de Admin - Arquivos Sensíveis

## Visão Geral

Para proteger arquivos sensíveis (migrations, logs, testes de conexão), implementamos um sistema de autenticação via chave de administração armazenada no `.env`.

## ⚙️ Configuração

### 1. Defina a chave no `.env`

```env
ADMIN_KEY=sua_chave_super_secreta_aqui_123
```

**⚠️ IMPORTANTE:**
- Use uma chave forte e aleatória
- Gere usando: `openssl rand -hex 32`
- Ou use: https://www.random.org/strings/
- **NUNCA** commite o arquivo `.env` no repositório

### 2. Em Produção

No seu `.env` de produção (Hostinger):

```env
APP_ENV=production
ADMIN_KEY=SuaChaveForteAleatoria2026
```

## 🔒 Arquivos Protegidos

Os seguintes arquivos requerem a chave de admin em produção:

1. **run-migrations.php** - Executor de migrations
2. **test-connection.php** - Teste de conexão BD
3. **logs-viewer.php** - Visualizador de logs

## 🌐 Como Usar

### Em Desenvolvimento (APP_ENV=development)

Acesso livre sem chave:
```
http://localhost/habitos/run-migrations.php
http://localhost/habitos/test-connection.php
http://localhost/habitos/logs-viewer.php
```

### Em Produção (APP_ENV=production)

Adicione `?key=SUA_CHAVE` na URL:
```
https://lucascadan.com/habitos/run-migrations.php?key=SuaChaveForteAleatoria2026
https://lucascadan.com/habitos/test-connection.php?key=SuaChaveForteAleatoria2026
https://lucascadan.com/habitos/logs-viewer.php?key=SuaChaveForteAleatoria2026
```

## 🔐 Segurança

### Como Funciona

1. A chave é armazenada APENAS no `.env` (nunca no código)
2. A comparação usa `hash_equals()` com SHA-256 (timing-safe)
3. Se estiver logado no sistema, a chave não é necessária
4. Em desenvolvimento, a autenticação é desabilitada

### Exemplo de Verificação

```php
function verifyAdminKey($providedKey) {
    $adminKey = env('ADMIN_KEY', '');
    
    if (empty($adminKey)) {
        return false;
    }
    
    // Comparação timing-safe com hash
    return hash_equals(
        hash('sha256', $adminKey), 
        hash('sha256', $providedKey)
    );
}
```

## ✅ Boas Práticas

1. **Gere chave forte:**
   ```bash
   # Linux/Mac
   openssl rand -hex 32
   
   # PowerShell (Windows)
   -join ((65..90) + (97..122) + (48..57) | Get-Random -Count 32 | % {[char]$_})
   ```

2. **Troque regularmente:** Mude a chave a cada 3-6 meses

3. **Não compartilhe:** Cada ambiente deve ter sua própria chave

4. **Remova após uso:** Após rodar migrations em produção, considere remover os arquivos do servidor

5. **Use HTTPS:** Sempre use HTTPS em produção para proteger a chave na URL

## 🚨 Em Caso de Vazamento

Se a chave vazar:

1. Gere uma nova chave imediatamente
2. Atualize o `.env` em todos os ambientes
3. Revogue acesso aos arquivos temporariamente
4. Considere remover os arquivos sensíveis do servidor

## 📝 Exemplo Completo

### Local (.env)
```env
APP_ENV=development
ADMIN_KEY=chave_local_123
```

### Produção (.env no Hostinger)
```env
APP_ENV=production
ADMIN_KEY=a9f8e7d6c5b4a3210fedcba9876543210abcdef1234567890
```

### Uso
```bash
# Local (sem chave)
http://localhost/habitos/run-migrations.php?action=run

# Produção (com chave)
https://lucascadan.com/habitos/run-migrations.php?action=run&key=a9f8e7d6c5b4a3210fedcba9876543210abcdef1234567890
```

## 🔄 Alternativas

Se preferir não usar chave na URL, você pode:

1. **Proteger por IP** - Adicionar whitelist de IPs no código
2. **Usar autenticação básica** - HTTP Basic Auth no `.htaccess`
3. **SSH/CLI** - Executar migrations via terminal SSH
4. **Remover arquivos** - Deletar após usar e re-upload quando necessário
