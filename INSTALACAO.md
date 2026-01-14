# INSTRUÇÕES DE INSTALAÇÃO

## 🏠 Instalação Local (XAMPP)

### Passo 1: Preparar Ambiente
1. Instale o **XAMPP** (https://www.apachefriends.org)
2. Inicie o Apache e MySQL no painel do XAMPP

### Passo 2: Copiar Arquivos
1. Copie a pasta do projeto para `C:\xampp\htdocs\habitos`
2. Mantenha a estrutura de pastas intacta

### Passo 3: Criar Banco de Dados
1. Acesse http://localhost/phpmyadmin
2. Clique em **Novo** na barra lateral
3. Nome do banco: `habitos_db`
4. Cotejamento: `utf8mb4_general_ci`
5. Clique em **Criar**

### Passo 4: Importar Schema
1. Selecione o banco `habitos_db` criado
2. Clique na aba **SQL**
3. Abra o arquivo `database/schema.sql`
4. Copie todo o conteúdo e cole na área de texto
5. Clique em **Executar**

### Passo 5: Configurar .env
1. Renomeie `.env.example` para `.env` (se necessário)
2. Edite o arquivo `.env`:
```env
DB_HOST=localhost
DB_NAME=habitos_db
DB_USER=root
DB_PASS=
BASE_URL=http://localhost/habitos
```
> **Nota**: No XAMPP padrão, o usuário é `root` e a senha fica vazia

### Passo 6: Testar
1. Acesse http://localhost/habitos
2. Crie sua conta e comece a usar!

---

## ☁️ Instalação no Hostinger

### Passo 1: Preparar Arquivos
- Baixe todos os arquivos do projeto
- Mantenha a estrutura de pastas intacta
- **IMPORTANTE**: NÃO envie o arquivo `.env` para produção!

### Passo 2: Upload para Hostinger

#### Opção A - Via Gerenciador de Arquivos (Recomendado)
1. Entre no **hPanel** da Hostinger
2. Navegue até **Arquivos** → **Gerenciador de Arquivos**
3. Abra a pasta `public_html` (ou pasta do seu domínio)
4. Clique em **Upload** e envie todos os arquivos
5. Mantenha a estrutura de pastas

#### Opção B - Via FTP
1. Use um cliente FTP (FileZilla, WinSCP, etc)
2. Conecte usando as credenciais FTP do hPanel
3. Navegue até `public_html`
4. Arraste todos os arquivos do projeto

### Passo 3: Criar Banco de Dados MySQL

1. No hPanel, vá em **Bancos de Dados** → **MySQL Databases**
2. Clique em **Create New Database**
3. Configure:
   - **Database Name**: `habitos_db`
   - **Database User**: crie um usuário
   - **Password**: crie uma senha forte
4. **IMPORTANTE**: Anote essas credenciais!
5. Associe o usuário ao banco com todas as permissões

### Passo 4: Importar Schema do Banco

1. Clique em **Manage** no banco criado (abre phpMyAdmin)
2. Selecione o banco `habitos_db` na barra lateral esquerda
3. Clique na aba **SQL** no topo
4. Abra o arquivo `database/schema.sql` do projeto
5. Copie todo o conteúdo
6. Cole na área de texto do phpMyAdmin
7. Clique em **Go/Executar**
8. Aguarde confirmação de sucesso

### Passo 5: Configurar Conexão do Banco

#### Opção 1: Usar .env (Recomendado)
1. Renomeie `.env.example` para `.env`
2. Edite o arquivo `.env`:
```env
DB_HOST=localhost
DB_NAME=habitos_db
DB_USER=seu_usuario_mysql
DB_PASS=sua_senha_mysql
BASE_URL=https://seudominio.com
```

#### Opção 2: Editar diretamente
Edite o arquivo **config/database.php**:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'habitos_db');
define('DB_USER', 'seu_usuario_mysql');    // ← Cole o usuário que você criou
define('DB_PASS', 'sua_senha_mysql');      // ← Cole a senha que você criou
```

### Passo 6: Configurar URLs

#### 6.1 - Edite config/config.php:
```php
define('BASE_URL', 'https://seudominio.com');  // ← Seu domínio real
```

#### 6.2 - Edite assets/js/app.js (linha 2):
```javascript
const API_BASE_URL = 'https://seudominio.com/api';  // ← Seu domínio real
```

### Passo 7: Verificar Permissões

No Gerenciador de Arquivos:
1. Selecione todos os **arquivos**
2. Clique com botão direito → **Permissions**
3. Configure para **644**

4. Selecione todas as **pastas**
5. Clique com botão direito → **Permissions**
6. Configure para **755**

### Passo 8: Testar Instalação

1. Acesse seu domínio: `https://seudominio.com`
2. Você deve ser redirecionado para o dashboard
3. Clique em **"Criar conta"**
4. Registre-se com seu email
5. Comece a usar!

## 🔧 Configurações do Hostinger

### Se você está usando um subdomínio:

Se instalou em `https://seudominio.com/habitos/`:

**config/config.php**:
```php
define('BASE_URL', 'https://seudominio.com/habitos');
```

**assets/js/app.js**:
```javascript
const API_BASE_URL = 'https://seudominio.com/habitos/api';
```

### Se o .htaccess não funcionar:

1. Verifique se o Apache está ativo (Hostinger usa Apache por padrão)
2. Se necessário, ative o mod_rewrite no hPanel em **Configurações Avançadas**

## 🎯 Dados de Teste (Opcional)

Se você importou o schema.sql completo (com dados de exemplo):

- **Email**: teste@exemplo.com
- **Senha**: 123456

**IMPORTANTE**: Delete esse usuário de teste após criar sua conta!

No phpMyAdmin:
```sql
DELETE FROM usuarios WHERE email = 'teste@exemplo.com';
```

## ✅ Checklist de Instalação

- [ ] Arquivos enviados para public_html
- [ ] Banco de dados `habitos_db` criado
- [ ] Usuário MySQL criado e associado
- [ ] Schema SQL importado com sucesso
- [ ] Arquivo `config/database.php` atualizado com credenciais
- [ ] Arquivo `config/config.php` atualizado com URL
- [ ] Arquivo `assets/js/app.js` atualizado com URL
- [ ] Permissões configuradas (644 para arquivos, 755 para pastas)
- [ ] Testado acesso ao site
- [ ] Conta de usuário criada
- [ ] Hábito de teste criado

## 🐛 Problemas Comuns

### 1. "Erro de conexão com banco de dados"
**Solução**:
- Verifique as credenciais em `config/database.php`
- Confirme que o banco existe no phpMyAdmin
- Verifique se o usuário está associado ao banco

### 2. "Cannot modify header information"
**Solução**:
- Certifique-se que não há espaços antes de `<?php` nos arquivos
- Salve arquivos em UTF-8 sem BOM

### 3. "404 Not Found" nas rotas
**Solução**:
- Verifique se o arquivo `.htaccess` foi enviado
- Confirme que está na raiz do projeto
- Se necessário, contate o suporte Hostinger para ativar mod_rewrite

### 4. Página em branco
**Solução**:
Adicione temporariamente no início do `index.php`:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```
Depois verifique os erros exibidos.

### 5. "Access denied for user"
**Solução**:
- Verifique se o usuário MySQL tem permissões no banco
- No phpMyAdmin, vá em **Privileges** e garanta que o usuário tem ALL PRIVILEGES

## 📞 Suporte Hostinger

Se precisar de ajuda específica do Hostinger:
- Chat ao vivo 24/7 no hPanel
- Base de conhecimento: https://support.hostinger.com
- Email: support@hostinger.com

## 🎉 Pronto!

Após seguir todos os passos, seu sistema de hábitos estará funcionando!

Acesse `https://seudominio.com` e comece a rastrear seus hábitos! 🚀
