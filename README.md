# 🎯 Sistema de Hábitos

Sistema completo de rastreamento de hábitos com autenticação de usuários, desenvolvido em PHP e MySQL, pronto para hospedar no Hostinger.

## 📋 Funcionalidades

- ✅ **Autenticação de Usuários** - Registro, login e logout seguro
- ✅ **Gerenciamento de Hábitos** - Criar, editar e excluir hábitos personalizados
- ✅ **Rastreamento Diário** - Marcar hábitos como concluídos a cada dia
- ✅ **Dashboard com Estatísticas** - Visualizar progresso e métricas
- ✅ **Interface Responsiva** - Funciona em desktop e mobile
- ✅ **Personalização** - Cores e ícones personalizados para cada hábito

## 🛠️ Tecnologias

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Arquitetura**: REST API

## 📁 Estrutura do Projeto

```
habitos/
├── api/                    # Endpoints da API REST
│   ├── login.php          # Autenticação de login
│   ├── registro.php       # Registro de novos usuários
│   ├── logout.php         # Encerrar sessão
│   ├── habitos.php        # CRUD de hábitos
│   ├── registros.php      # Marcação diária de hábitos
│   └── estatisticas.php   # Métricas e estatísticas
├── assets/
│   ├── css/
│   │   └── styles.css     # Estilos da aplicação
│   └── js/
│       └── app.js         # Lógica JavaScript
├── config/
│   ├── config.php         # Configurações gerais
│   ├── database.php       # Conexão com banco de dados
│   └── env.php            # Carregador de variáveis de ambiente
├── database/
│   ├── migrations/        # Migrações do banco de dados
│   │   ├── 001_create_initial_tables.sql
│   │   └── README.md
│   ├── migrate.php        # Sistema de migrações
│   └── schema.sql         # Script de criação do banco (legado)
├── .env                   # Variáveis de ambiente (não commitar!)
├── .env.example           # Template do .env
├── .gitignore             # Arquivos ignorados pelo Git
├── .htaccess              # Configurações Apache
├── index.php              # Redirecionamento
├── login.php              # Página de login
├── registro.php           # Página de registro
└── dashboard.php          # Dashboard principal
```

## 🚀 Instalação no Hostinger

### 1. Preparar os Arquivos

1. Faça o download de todos os arquivos do projeto
2. Compacte em um arquivo ZIP (opcional)

### 2. Upload via FTP/Gerenciador de Arquivos

1. Acesse o **hPanel** do Hostinger
2. Vá em **Arquivos** → **Gerenciador de Arquivos**
3. Navegue até a pasta `public_html` (ou a pasta do seu domínio)
4. Faça upload de todos os arquivos do projeto
5. Se usou ZIP, extraia os arquivos

### 3. Criar o Banco de Dados

1. No hPanel, vá em **Bancos de Dados** → **Gerenciamento**
2. Clique em **Novo Banco de Dados**
3. Crie um banco com o nome: `habitos_db`
4. Crie um usuário e senha (anote essas credenciais!)
5. Associe o usuário ao banco de dados com todas as permissões

### 4. Importar o Schema SQL

1. Clique em **Gerenciar** no banco criado (abre o phpMyAdmin)
2. Selecione o banco `habitos_db`
3. Clique na aba **SQL**
4. Copie e cole o conteúdo do arquivo `database/schema.sql`
5. Clique em **Executar**

### 5. Configurar a Conexão

Edite o arquivo `config/database.php` com suas credenciais:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'habitos_db');
define('DB_USER', 'seu_usuario_mysql');    // Altere aqui
define('DB_PASS', 'sua_senha_mysql');      // Altere aqui
```

### 6. Configurar a URL Base

Edite o arquivo `config/config.php`:

```php
define('BASE_URL', 'https://seudominio.com');  // Altere para seu domínio
```

Edite o arquivo `assets/js/app.js`:

```javascript
const API_BASE_URL = 'https://seudominio.com/api';  // Altere para seu domínio
```

### 7. Verificar Permissões

Certifique-se de que as permissões dos arquivos estejam corretas:
- Arquivos: 644
- Pastas: 755

## 🔐 Primeiro Acesso

Após a instalação:

1. Acesse: `https://seudominio.com`
2. Você será redirecionado para o dashboard
3. Clique em "Criar conta" para registrar
4. Preencha seus dados e comece a usar!

### Dados de Teste (Opcional)

Se você importou o schema.sql com os dados de exemplo:

- **Email**: teste@exemplo.com
- **Senha**: 123456

## 📱 Como Usar

### Criar um Hábito

1. No dashboard, clique em **"+ Novo Hábito"**
2. Preencha:
   - Nome do hábito
   - Descrição (opcional)
   - Escolha um emoji como ícone
   - Selecione uma cor
   - Defina a meta semanal (quantos dias por semana)
3. Clique em **Salvar**

### Marcar como Concluído

- Clique no botão de check (✓) ao lado do hábito
- O botão ficará verde quando concluído
- Clique novamente para desmarcar

### Acompanhar Progresso

No topo do dashboard você verá:
- **Total de Hábitos**: Quantos hábitos você tem
- **Concluídos Hoje**: Quantos você já fez hoje
- **Melhor Sequência**: Sua maior sequência de dias consecutivos
- **Taxa Semanal**: Percentual de conclusão nos últimos 7 dias

## 🔧 Requisitos do Servidor

- **PHP**: 7.4 ou superior
- **MySQL**: 5.7 ou superior
- **Extensões PHP**:
  - PDO
  - PDO_MySQL
  - JSON
  - Session
- **Apache**: mod_rewrite habilitado
- **HTTPS**: Recomendado para segurança

## 🎨 Personalização

### Alterar Cores

Edite as variáveis CSS em `assets/css/styles.css`:

```css
:root {
    --primary: #3b82f6;       /* Cor principal */
    --success: #10b981;       /* Cor de sucesso */
    --danger: #ef4444;        /* Cor de erro */
}
```

### Adicionar Novos Recursos

- **API**: Crie novos endpoints em `/api/`
- **Frontend**: Adicione novas páginas ou modifique o `dashboard.php`
- **Banco**: Execute novos scripts SQL no phpMyAdmin

## 🔒 Segurança

O sistema implementa:

- ✅ Senhas criptografadas com `password_hash()`
- ✅ Proteção contra SQL Injection (PDO prepared statements)
- ✅ Sanitização de inputs
- ✅ Proteção XSS
- ✅ Headers de segurança configurados
- ✅ Validação de sessão

### Recomendações Adicionais

1. **Use HTTPS** - Sempre que possível
2. **Senhas Fortes** - Exija senhas complexas dos usuários
3. **Backups** - Faça backups regulares do banco de dados
4. **Atualizações** - Mantenha PHP e MySQL atualizados

## 🐛 Solução de Problemas

### "Erro de conexão com banco de dados"
- Verifique as credenciais em `config/database.php`
- Confirme que o banco de dados existe
- Verifique se o usuário tem permissões

### "Página em branco"
- Ative a exibição de erros temporariamente:
  ```php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  ```
- Verifique os logs de erro do PHP no hPanel

### "Headers already sent"
- Verifique se não há espaços em branco antes de `<?php`
- Certifique-se de que os arquivos estão salvos em UTF-8 sem BOM

### "API não responde"
- Verifique a URL base em `assets/js/app.js`
- Confirme que o mod_rewrite está ativo
- Teste acessar diretamente: `https://seudominio.com/api/habitos.php`

## 📊 Estrutura do Banco de Dados

### Tabela: usuarios
- `id` - ID único do usuário
- `nome` - Nome completo
- `email` - Email (único)
- `senha` - Senha criptografada
- `data_cadastro` - Data de registro
- `ultimo_acesso` - Último login

### Tabela: habitos
- `id` - ID único do hábito
- `usuario_id` - ID do usuário (FK)
- `nome` - Nome do hábito
- `descricao` - Descrição detalhada
- `cor` - Cor em hexadecimal
- `icone` - Emoji do hábito
- `meta_semanal` - Dias por semana (1-7)
- `ativo` - Status (1=ativo, 0=inativo)

### Tabela: registros
- `id` - ID único do registro
- `habito_id` - ID do hábito (FK)
- `data` - Data do registro
- `concluido` - Se foi concluído (1/0)
- `notas` - Notas opcionais

## 🔧 Desenvolvimento Local

### Configuração com XAMPP

1. **Instalar XAMPP** - https://www.apachefriends.org
2. **Copiar projeto** - Coloque em `C:\xampp\htdocs\habitos`
3. **Configurar .env**:
   ```env
   DB_HOST=localhost:3306
   DB_NAME=habitos_db
   DB_USER=root
   DB_PASS=
   BASE_URL=http://localhost/habitos
   ```
4. **Criar banco** - Acesse http://localhost/phpmyadmin
5. **Executar migrações**:
   ```bash
   php database/migrate.php run
   ```
6. **Acessar** - http://localhost/habitos

### Sistema de Migrações

O projeto usa um sistema de migrações SQL similar ao Laravel:

```bash
# Ver status das migrações
php database/migrate.php status

# Executar migrações pendentes
php database/migrate.php run
```

**Criar nova migração:**

1. Crie um arquivo em `database/migrations/`
2. Use o formato: `###_descricao.sql`
3. Exemplo: `003_add_notifications.sql`

```sql
-- Migração 003: Adicionar sistema de notificações

CREATE TABLE IF NOT EXISTS notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    mensagem TEXT NOT NULL,
    lida BOOLEAN DEFAULT FALSE,
    criada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
```

Veja [database/migrations/README.md](database/migrations/README.md) para mais detalhes.

## 🤝 Suporte

Para problemas com:
- **Hospedagem**: Contate o suporte do Hostinger
- **Código**: Verifique os logs de erro e console do navegador
- **MySQL**: Use o phpMyAdmin para verificar as tabelas

## 📝 Licença

Este projeto é de código aberto e pode ser usado livremente.

## 🎉 Próximos Passos

Após ter o sistema funcionando, você pode:

1. ✨ Adicionar gráficos de progresso
2. 📅 Criar visualização de calendário
3. 🏆 Implementar sistema de conquistas
4. 📧 Adicionar notificações por email
5. 📱 Criar aplicativo mobile
6. 👥 Adicionar hábitos compartilhados
7. 🎨 Criar temas personalizados

---

**Desenvolvido com ❤️ para ajudar você a construir melhores hábitos!**
