// Bonequinho Animado
class Character {
    constructor() {
        this.element = null;
        this.face = null;
        this.bubble = null;
        this.currentMood = 'neutral';
        this.isAnimating = false;
        this.hasInteracted = false;
        this.pendingGreeting = true;
        this.init();
    }

    init() {
        // Criar container do personagem
        this.element = document.createElement('div');
        this.element.className = 'character-container';
        this.element.innerHTML = `
            <div class="speech-bubble hidden"></div>
            <div class="character-settings">
                <button class="settings-btn" title="Configurações do assistente">⚙️</button>
                <div class="settings-menu hidden">
                    <label class="settings-option">
                        <input type="checkbox" id="character-tts-toggle" ${this.shouldSpeak() ? 'checked' : ''}>
                        <span>Fala ativada 🔊</span>
                    </label>
                    
                    <div class="settings-divider"></div>
                    
                    <div class="settings-group">
                        <label class="settings-label">Volume:</label>
                        <input type="range" id="character-volume" class="settings-slider" min="0" max="2" step="0.1" value="1">
                        <span id="volume-value" class="settings-value">100%</span>
                    </div>
                    
                    <div class="settings-divider"></div>
                    
                    <div class="settings-group">
                        <label class="settings-label">Tipo de Voz:</label>
                        <select id="character-tts-type" class="settings-select">
                            <option value="browser">Navegador (Grátis)</option>
                            <option value="openai">OpenAI (Premium)</option>
                        </select>
                    </div>
                    
                    <div id="openai-settings" class="settings-group" style="display:none;">
                        <label class="settings-label">Voz OpenAI:</label>
                        <select id="character-openai-voice" class="settings-select">
                            <option value="nova">Nova (Feminina Energética)</option>
                            <option value="shimmer">Shimmer (Feminina Suave)</option>
                            <option value="alloy">Alloy (Neutra)</option>
                            <option value="echo">Echo (Masculina)</option>
                            <option value="fable">Fable (Britânica)</option>
                            <option value="onyx">Onyx (Profunda)</option>
                        </select>
                        
                        <label class="settings-label">Velocidade:</label>
                        <input type="range" id="character-openai-speed" class="settings-slider" min="0.25" max="2.0" step="0.25" value="1.0">
                        <span id="speed-value" class="settings-value">1.0x</span>
                    </div>
                    
                    <button class="settings-close">✕</button>
                </div>
            </div>
            <div class="character">
                <div class="character-face">😊</div>
                <div class="character-body">
                    <div class="character-hand left">👋</div>
                    <div class="character-hand right">👋</div>
                </div>
            </div>
        `;
        
        document.body.appendChild(this.element);
        
        this.face = this.element.querySelector('.character-face');
        this.bubble = this.element.querySelector('.speech-bubble');
        
        // Configurar botão de settings
        this.setupSettings();
        
        // Animação de entrada
        setTimeout(() => {
            this.element.classList.add('show');
            this.greet();
        }, 500);
        
        // Verificar alertas periodicamente
        this.startAlertChecker();
    }

    // Configurar menu de settings
    setupSettings() {
        const settingsBtn = this.element.querySelector('.settings-btn');
        const settingsMenu = this.element.querySelector('.settings-menu');
        const settingsClose = this.element.querySelector('.settings-close');
        const ttsToggle = this.element.querySelector('#character-tts-toggle');
        const volumeSlider = this.element.querySelector('#character-volume');
        const volumeValue = this.element.querySelector('#volume-value');
        const ttsType = this.element.querySelector('#character-tts-type');
        const openaiSettings = this.element.querySelector('#openai-settings');
        const openaiVoice = this.element.querySelector('#character-openai-voice');
        const openaiSpeed = this.element.querySelector('#character-openai-speed');
        const speedValue = this.element.querySelector('#speed-value');
        
        // Carregar configurações salvas
        const savedVolume = localStorage.getItem('character_volume') || '1';
        volumeSlider.value = savedVolume;
        volumeValue.textContent = Math.round(parseFloat(savedVolume) * 100) + '%';
        
        const savedType = localStorage.getItem('character_tts_type') || 'browser';
        ttsType.value = savedType;
        openaiSettings.style.display = savedType === 'openai' ? 'block' : 'none';
        
        const savedVoice = localStorage.getItem('character_openai_voice') || 'nova';
        openaiVoice.value = savedVoice;
        
        const savedSpeed = localStorage.getItem('character_openai_speed') || '1.0';
        openaiSpeed.value = savedSpeed;
        speedValue.textContent = savedSpeed + 'x';
        
        settingsBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            settingsMenu.classList.toggle('hidden');
            settingsMenu.classList.toggle('show');
        });
        
        settingsClose.addEventListener('click', (e) => {
            e.stopPropagation();
            settingsMenu.classList.remove('show');
            setTimeout(() => settingsMenu.classList.add('hidden'), 300);
        });
        
        ttsToggle.addEventListener('change', (e) => {
            localStorage.setItem('character_tts_enabled', e.target.checked);
            const message = e.target.checked 
                ? 'Voz ativada! Agora vou falar com você! 🎤'
                : 'Voz desativada. Modo silencioso ativado. 🤫';
            this.speak(message, 'happy', 3000);
        });
        
        volumeSlider.addEventListener('input', (e) => {
            const volume = parseFloat(e.target.value);
            localStorage.setItem('character_volume', volume);
            volumeValue.textContent = Math.round(volume * 100) + '%';
        });
        
        ttsType.addEventListener('change', (e) => {
            const type = e.target.value;
            localStorage.setItem('character_tts_type', type);
            openaiSettings.style.display = type === 'openai' ? 'block' : 'none';
            
            const message = type === 'openai' 
                ? 'Modo Premium ativado! Voz da OpenAI selecionada. 🎙️'
                : 'Modo padrão do navegador selecionado. 🔊';
            this.speak(message, 'happy', 3000);
        });
        
        openaiVoice.addEventListener('change', (e) => {
            localStorage.setItem('character_openai_voice', e.target.value);
        });
        
        openaiSpeed.addEventListener('input', (e) => {
            const speed = e.target.value;
            localStorage.setItem('character_openai_speed', speed);
            speedValue.textContent = speed + 'x';
        });
        
        // Fechar menu ao clicar fora
        document.addEventListener('click', (e) => {
            if (!this.element.contains(e.target)) {
                settingsMenu.classList.remove('show');
                setTimeout(() => settingsMenu.classList.add('hidden'), 300);
            }
        });
    }

    // Expressões do personagem
    moods = {
        neutral: '😊',
        happy: '😄',
        excited: '🤩',
        celebration: '🎉',
        sleeping: '😴',
        alert: '⏰',
        thinking: '🤔',
        proud: '😎',
        love: '😍',
        tired: '😮‍💨'
    };

    // Mensagens aleatórias
    messages = {
        greet: [
            'Olá! Vamos construir ótimos hábitos juntos! 💪',
            'Que bom te ver! Pronto para mais um dia? 🌟',
            'Oi! Estou aqui para te motivar! 🚀'
        ],
        habitComplete: [
            'Parabéns! Você completou {habit}! Você é incrível! 🎉',
            'Mais um concluído! {habit} feito! Continue assim! ⭐',
            'Arrasou! Você completou {habit}! Está no caminho certo! 🏆',
            'Uau! Que dedicação com {habit}! 💎',
            'Excelente trabalho! {habit} completado! Orgulho de você! 🌟',
            'Maravilha! Você fez {habit}! Cada dia melhor! 💪',
            'Incrível! {habit} concluído! Você está voando! 🚀',
            'Sucesso! Mais um {habit} na conta! Você é demais! ⭐',
            'Fantástico! {habit} feito! Continue brilhando! ✨',
            'Perfeito! Você completou {habit}! Imparável! 🔥'
        ],
        reminder: [
            'Opa! Não esquece do seu hábito! ⏰',
            'Hora de cuidar de você! 🔔',
            'Ei! Tem um hábito te esperando! 📢',
            'Lembrete: hora do seu hábito! ⏱️'
        ],
        streak: [
            'Sequência mantida! Você está em chamas! 🔥',
            'Dia após dia, você está construindo algo incrível! 💪',
            'Consistência é tudo! Continue! 📈'
        ],
        habitUncomplete: [
            'Ops! {habit} desmarcado. Sem problemas, você pode refazer! 😊',
            'Tudo bem! {habit} foi desmarcado. O importante é tentar de novo! 💪',
            '{habit} removido da lista de hoje. Ainda dá tempo! ⏰',
            'Desmarcou {habit}? Tranquilo! Você decide seu ritmo! 🌟',
            '{habit} desmarcado. Lembre-se: progresso, não perfeição! 😌'
        ]
    };

    // Saudar usuário
    greet() {
        const hour = new Date().getHours();
        let greeting;
        
        if (hour < 12) {
            greeting = 'Bom dia! ☀️ Vamos começar bem o dia!';
        } else if (hour < 18) {
            greeting = 'Boa tarde! 🌤️ Como vão seus hábitos hoje?';
        } else {
            greeting = 'Boa noite! 🌙 Ainda dá tempo de fazer algo hoje!';
        }
        
        this.speak(greeting, 'happy');
    }

    // Mudar expressão
    changeMood(mood, duration = 3000) {
        if (this.moods[mood]) {
            this.currentMood = mood;
            this.face.textContent = this.moods[mood];
            this.element.classList.add('bounce');
            
            // Esconder mão esquerda para emojis que já têm mão no rosto
            if (mood === 'thinking') {
                this.element.classList.add('hide-left-hand');
            } else {
                this.element.classList.remove('hide-left-hand');
            }
            
            setTimeout(() => {
                this.element.classList.remove('bounce');
                if (duration > 0) {
                    setTimeout(() => this.changeMood('neutral', 0), duration);
                }
            }, 600);
        }
    }

    // Falar (mostrar balão de fala)
    speak(message, mood = null, duration = 4000) {
        if (mood) this.changeMood(mood, duration);
        
        this.bubble.textContent = message;
        this.bubble.classList.remove('hidden');
        this.bubble.classList.add('show');
        
        // Animar mãos
        this.element.classList.add('talking');
        
        // TTS - Text to Speech (opcional)
        if (window.speechSynthesis && this.shouldSpeak()) {
            this.trySpeech(message);
        }
        
        setTimeout(() => {
            this.bubble.classList.remove('show');
            this.element.classList.remove('talking');
            setTimeout(() => this.bubble.classList.add('hidden'), 300);
        }, duration);
    }

    // Tentar falar com TTS
    async trySpeech(message) {
        const ttsType = localStorage.getItem('character_tts_type') || 'browser';
        
        if (ttsType === 'openai') {
            await this.trySpeechOpenAI(message);
        } else {
            this.trySpeechBrowser(message);
        }
    }
    
    // TTS do navegador
    trySpeechBrowser(message) {
        try {
            const utterance = new SpeechSynthesisUtterance(message.replace(/[🎉😄💪⭐🏆💎🌟⏰🔔📢⏱️🔥📈☀️🌤️🌙😊😌]/g, ''));
            utterance.lang = 'pt-BR';
            utterance.rate = 1.1;
            utterance.pitch = 1.2;
            
            // Aplicar volume configurado
            const volume = parseFloat(localStorage.getItem('character_volume') || '1');
            utterance.volume = volume;
            
            // Marcar que tentou falar após interação
            utterance.onstart = () => {
                this.hasInteracted = true;
                this.pendingGreeting = false;
            };
            
            utterance.onerror = (event) => {
                // Se falhar e ainda não houve interação, aguardar próximo clique
                if (!this.hasInteracted && event.error === 'not-allowed') {
                    console.log('TTS bloqueado: aguardando interação do usuário');
                }
            };
            
            window.speechSynthesis.speak(utterance);
        } catch (error) {
            console.log('Erro ao tentar falar:', error);
        }
    }
    
    // TTS da OpenAI
    async trySpeechOpenAI(message) {
        try {
            // Remover emojis
            const cleanText = message.replace(/[🎉😄💪⭐🏆💎🌟⏰🔔📢⏱️🔥📈☀️🌤️🌙😊😌]/g, '').trim();
            
            // Validar se há texto
            if (!cleanText || cleanText.length === 0) {
                console.log('Texto vazio após limpar emojis, usando TTS do navegador');
                this.trySpeechBrowser(message);
                return;
            }
            
            // Buscar configurações
            const voice = localStorage.getItem('character_openai_voice') || 'nova';
            const speed = parseFloat(localStorage.getItem('character_openai_speed') || '1.0');
            
            // Fazer requisição para o backend
            const response = await fetch(window.API_BASE_URL + '/api/tts.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    text: cleanText,
                    voice: voice,
                    speed: speed
                })
            });
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                console.log('Erro na API OpenAI:', errorData.error || 'Erro desconhecido');
                throw new Error(errorData.error || 'Erro ao gerar áudio OpenAI');
            }
            
            // Obter o blob de áudio
            const audioBlob = await response.blob();
            const audioUrl = URL.createObjectURL(audioBlob);
            
            // Criar e tocar o áudio
            const audio = new Audio(audioUrl);
            
            // Aplicar volume configurado
            const volume = parseFloat(localStorage.getItem('character_volume') || '1');
            audio.volume = volume;
            
            audio.onended = () => {
                URL.revokeObjectURL(audioUrl); // Limpar memória
            };
            
            audio.onplay = () => {
                this.hasInteracted = true;
                this.pendingGreeting = false;
            };
            
            await audio.play();
            
        } catch (error) {
            console.log('Erro no TTS OpenAI:', error.message);
            console.log('Usando TTS do navegador como fallback');
            // Fallback para TTS do navegador
            this.trySpeechBrowser(message);
        }
    }

    // Verificar se deve falar (respeitar preferências do usuário)
    shouldSpeak() {
        // Por padrão está desabilitado, pode ser habilitado pelo usuário
        const enabled = localStorage.getItem('character_tts_enabled');
        return enabled === 'true';
    }

    // Animação ao completar hábito
    celebrateHabit(habitName) {
        this.isAnimating = true;
        this.element.classList.add('celebrate');
        
        const message = this.getRandomMessage('habitComplete').replace('{habit}', habitName);
        this.speak(message, 'celebration', 5000);
        
        // Confetes
        this.showConfetti();
        
        setTimeout(() => {
            this.element.classList.remove('celebrate');
            this.isAnimating = false;
        }, 5000);
    }
    // Rea��o ao desmarcar h�bito
    uncelebrateHabit(habitName) {
        const message = this.getRandomMessage('habitUncomplete').replace('{habit}', habitName);
        this.speak(message, 'neutral', 4000);
    }

    // Mostrar confetes
    showConfetti() {
        const confettiEmojis = ['🎉', '⭐', '✨', '🎊', '💫', '🌟'];
        
        for (let i = 0; i < 15; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.textContent = confettiEmojis[Math.floor(Math.random() * confettiEmojis.length)];
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                confetti.style.animationDelay = (Math.random() * 0.5) + 's';
                
                document.body.appendChild(confetti);
                
                setTimeout(() => confetti.remove(), 4000);
            }, i * 100);
        }
    }

    // Lembrete de hábito
    remindHabit(habitName) {
        if (this.isAnimating) return;
        
        this.element.classList.add('alert-animation');
        const message = this.getRandomMessage('reminder');
        this.speak(`${message}\n"${habitName}"`, 'alert', 5000);
        
        // Som de alerta (beep)
        this.playBeep();
        
        setTimeout(() => {
            this.element.classList.remove('alert-animation');
        }, 2000);
    }

    // Tocar som de alerta
    playBeep() {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.5);
    }

    // Celebrar sequência
    celebrateStreak(days) {
        const message = `${days} dias de sequência! ${this.getRandomMessage('streak')}`;
        this.speak(message, 'proud', 5000);
    }

    // Obter mensagem aleatória
    getRandomMessage(type) {
        const messages = this.messages[type] || [];
        return messages[Math.floor(Math.random() * messages.length)];
    }

    // Verificar alertas de hábitos
    async startAlertChecker() {
        // Verificar a cada 30 segundos
        setInterval(async () => {
            try {
                const response = await fetch(`${window.API_BASE_URL}/api/habitos.php`);
                const data = await response.json();
                
                if (data.success && data.habitos) {
                    const now = new Date();
                    const currentTime = now.getHours() * 60 + now.getMinutes();
                    
                    data.habitos.forEach(habito => {
                        if (habito.alerta_ativo && habito.horario_alerta) {
                            const [hours, minutes] = habito.horario_alerta.split(':');
                            const alertTime = parseInt(hours) * 60 + parseInt(minutes);
                            
                            // Se está na hora do alerta (com margem de 1 minuto)
                            if (Math.abs(currentTime - alertTime) <= 1) {
                                const lastAlert = localStorage.getItem(`last_alert_${habito.id}`);
                                const today = now.toDateString();
                                
                                // Verificar se já não alertou hoje
                                if (lastAlert !== today) {
                                    this.remindHabit(habito.nome);
                                    localStorage.setItem(`last_alert_${habito.id}`, today);
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Erro ao verificar alertas:', error);
            }
        }, 30000); // 30 segundos
    }

    // Interagir com o personagem
    interact() {
        if (this.isAnimating) return;
        
        // Se é a primeira interação e a saudação estava pendente, repetir com voz
        if (!this.hasInteracted && this.pendingGreeting && this.shouldSpeak()) {
            this.hasInteracted = true;
            this.pendingGreeting = false;
            this.greet();
            return;
        }
        
        const interactions = [
            { message: 'Posso te ajudar em algo? 😊', mood: 'happy' },
            { message: 'Estou aqui torcendo por você! 💪', mood: 'excited' },
            { message: 'Você está indo muito bem! 🌟', mood: 'proud' },
            { message: 'Lembre-se: pequenos passos todos os dias! 🚶', mood: 'thinking' }
        ];
        
        const interaction = interactions[Math.floor(Math.random() * interactions.length)];
        this.speak(interaction.message, interaction.mood);
    }
}

// Exportar a classe
window.Character = Character;

// Instanciar personagem quando a página carregar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCharacter);
} else {
    initCharacter();
}

function initCharacter() {
    // Só criar o personagem no dashboard
    if (window.location.pathname.includes('dashboard') || window.location.pathname.endsWith('/')) {
        window.character = new Character();
        
        // Adicionar evento de clique no personagem
        setTimeout(() => {
            const charElement = document.querySelector('.character');
            if (charElement) {
                charElement.addEventListener('click', () => window.character.interact());
            }
        }, 1000);
    }
}
