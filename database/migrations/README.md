# Sistema de Migrações

Sistema simples de migrações de banco de dados usando arquivos SQL.

## Como usar

### Executar migrações pendentes
```bash
php database/migrate.php
# ou
php database/migrate.php run
```

### Ver status das migrações
```bash
php database/migrate.php status
```

## Criar nova migração

1. Crie um arquivo SQL na pasta `database/migrations`
2. Use o formato: `###_descricao.sql` (ex: `002_add_column_avatar.sql`)
3. Os arquivos são executados em ordem alfabética
4. Use sempre números sequenciais com 3 dígitos

### Exemplo de migração

**Arquivo:** `002_add_avatar_to_users.sql`
```sql
-- Migração 002: Adicionar campo avatar na tabela usuarios

ALTER TABLE usuarios 
ADD COLUMN avatar VARCHAR(255) NULL AFTER email;

-- Criar índice se necessário
-- CREATE INDEX idx_avatar ON usuarios(avatar);
```

## Boas práticas

- ✅ Use nomes descritivos para as migrações
- ✅ Numere sequencialmente (001, 002, 003...)
- ✅ Inclua comentários explicando o que a migração faz
- ✅ Teste a migração localmente antes de aplicar em produção
- ✅ Use `IF NOT EXISTS` quando apropriado para evitar erros
- ✅ Sempre faça backup antes de executar migrações em produção
- ❌ Nunca edite migrações já executadas
- ❌ Não delete migrações já executadas

## Estrutura

```
database/
├── migrations/
│   ├── 001_create_initial_tables.sql
│   ├── 002_add_new_feature.sql
│   └── 003_modify_something.sql
├── migrate.php
└── schema.sql (legado - usar apenas para referência)
```

## Controle de migrações

O sistema cria automaticamente uma tabela `migrations` que registra:
- Nome do arquivo da migração
- Data/hora de execução

Isso garante que cada migração seja executada apenas uma vez.
