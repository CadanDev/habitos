# 🗄️ Migrações de Banco de Dados

## 📋 Estrutura

Cada migração cria ou modifica **UMA TABELA**. Isso torna mais fácil entender o que cada migration faz e facilita o rastreamento.

## 📜 Migrations Atuais

| # | Nome | Tabela | Descrição |
|---|------|--------|-----------|
| 001 | create_initial_tables | `usuarios` | Tabela principal de usuários com todas as colunas |
| 002 | add_user_preferences | `habitos` | Tabela de hábitos |
| 003 | add_habit_alerts | `registros` | Tabela de registros diários de hábitos |
| 004 | add_tts_preferences | `alertas` | Tabela de alertas para hábitos |
| 005 | add_rest_time_to_alerts | `veiculos` | Tabela de veículos |
| 006 | add_alert_messages | `abastecimentos` | Tabela de abastecimentos de veículos |
| 007 | add_tts_provider | `manutencoes` | Tabela de manutenções de veículos |
| 008 | create_vehicle_management | `user_preferences` | Tabela de preferências do usuário |
| 009 | add_ultimo_acesso | `audit_logs` | Tabela de logs de auditoria |

## 🚀 Como Usar

### Ver Status
```
run-migrations.php?action=status
```
Mostra quais migrações foram executadas e quais estão pendentes.

### Executar Migrações
```
run-migrations.php?action=run
```
Executa todas as migrações pendentes em ordem.

### Resetar Migrações (Fresh)
```
run-migrations.php?action=fresh
```
⚠️ **Limpa apenas o histórico** - não deleta os dados do banco!

Útil quando você quer reexecutar as migrações sem perder dados.

## 🔒 Segurança

Em produção, forneça a chave:
```
run-migrations.php?action=run&key=SUA_CHAVE_SECRETA
```

Configure a chave no `.env`:
```
ADMIN_KEY=sua_chave_muito_segura_aqui
```

## 📝 Notas

- Sem dados importantes? Use `fresh` para resetar e reexecutar
- Cada migration é independente e responsável por uma tabela
- O sistema rastrea migrações executadas na tabela `migrations`
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
