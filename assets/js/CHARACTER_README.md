# 🎭 Assistente Virtual - Bonequinho Animado

## Descrição

Um assistente virtual animado que aparece no canto inferior direito da tela do dashboard para motivar e lembrar você sobre seus hábitos!

## Características

### 😊 Expressões Emocionais
O bonequinho tem várias expressões que mudam conforme as situações:
- **Neutro** 😊 - Estado padrão
- **Feliz** 😄 - Quando você completa tarefas
- **Animado** 🤩 - Momentos especiais
- **Celebração** 🎉 - Ao completar um hábito
- **Alerta** ⏰ - Quando toca um alarme
- **Orgulhoso** 😎 - Ao manter sequências
- E mais!

### 🎉 Animações
- **Entrada suave** - Aparece com animação quando a página carrega
- **Flutuação** - Animação contínua de flutuação
- **Aceno** - As mãozinhas acenam para você
- **Saltos** - Pula de alegria ao completar hábitos
- **Confetes** - Chuva de confetes nas celebrações
- **Tremor** - Vibra quando toca um alarme

### 💬 Mensagens
O assistente fala com você através de balões de fala com mensagens motivacionais:
- Saudações baseadas na hora do dia
- Parabenizações ao completar hábitos
- Lembretes de hábitos
- Mensagens de incentivo para manter sequências

### 🔊 Text-to-Speech (Opcional)
- Pode falar as mensagens usando a voz do navegador
- Configurável através do menu de configurações (⚙️)
- Ativa/Desativa facilmente

### ⏰ Sistema de Alertas
- Verifica automaticamente os hábitos com alertas configurados
- Toca um beep sonoro quando é hora de fazer um hábito
- Mostra mensagem de lembrete
- Só alerta uma vez por dia para cada hábito

### 🎮 Interatividade
- **Clique no personagem** - Ele fala mensagens motivacionais aleatórias
- **Botão de configurações** ⚙️ - Configure se quer ativar a voz
- **Reage automaticamente** - Celebra quando você completa hábitos

## Como Usar

### Configurar a Voz (TTS)

1. Clique no botão de engrenagem ⚙️ no canto superior direito do personagem
2. Marque/desmarque a opção "Fala ativada 🔊"
3. Quando ativado, o personagem vai falar as mensagens em voz alta

### Interagir com o Personagem

Simplesmente clique no bonequinho e ele vai te dar uma mensagem motivacional!

### Celebrações Automáticas

Quando você marca um hábito como concluído:
- O personagem pula de alegria
- Confetes caem na tela
- Uma mensagem de parabéns aparece
- Se você tem uma sequência de 3+ dias, ele celebra isso também!

### Alertas de Hábitos

Configure alertas nos seus hábitos (ao criar/editar). O personagem vai:
1. Verificar a cada 30 segundos se é hora de algum hábito
2. Tocar um beep sonoro
3. Mostrar uma mensagem de lembrete
4. Tremer para chamar sua atenção

## Arquivos

- **character.js** - Lógica do personagem
- **character.css** - Estilos e animações
- Integrado automaticamente no **dashboard.php**

## Customização

### Adicionar Novas Mensagens

Edite o arquivo `character.js` e adicione mensagens nos objetos:
```javascript
messages = {
    greet: ['Nova mensagem de saudação'],
    habitComplete: ['Nova mensagem de conclusão'],
    reminder: ['Novo lembrete'],
    streak: ['Nova mensagem de sequência']
}
```

### Adicionar Novas Expressões

```javascript
moods = {
    meuHumor: '🤪'
}
```

### Mudar Tempo de Verificação de Alertas

No método `startAlertChecker()`, altere o intervalo (padrão: 30 segundos):
```javascript
}, 30000); // Altere este valor (em milissegundos)
```

## Compatibilidade

- ✅ Chrome/Edge - Completo
- ✅ Firefox - Completo
- ✅ Safari - Completo (TTS pode variar)
- ✅ Mobile - Funcional (escala reduzida)

## Tecnologias

- JavaScript ES6+
- CSS3 (Animations, Transforms)
- Web Speech API (TTS)
- Web Audio API (Beep sonoro)
- LocalStorage (Preferências)

## Divirta-se! 🚀

O bonequinho está aqui para te motivar e ajudar a manter seus hábitos! 💪
