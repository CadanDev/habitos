// Configuração da API - definida no dashboard.php via env
// const API_BASE_URL já está disponível globalmente

// Estado da aplicação
const AppState = {
    user: null,
    habitos: [],
    registros: {},
    estatisticas: null
};

// Utilitários
const utils = {
    // Fazer requisição à API
    async api(endpoint, options = {}) {
        const config = {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        };
        
        try {
            const response = await fetch(`${API_BASE_URL}/${endpoint}`, config);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.error || 'Erro na requisição');
            }
            
            return data;
        } catch (error) {
            console.error('Erro na API:', error);
            throw error;
        }
    },
    
    // Mostrar alerta
    showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        
        const container = document.querySelector('.container') || document.body;
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => alertDiv.remove(), 5000);
    },
    
    // Formatar data
    formatDate(date) {
        return new Date(date).toLocaleDateString('pt-BR');
    },
    
    // Data de hoje
    today() {
        return new Date().toISOString().split('T')[0];
    },
    
    // Obter nome do dia da semana
    getDayName(date) {
        const days = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        return days[new Date(date + 'T00:00:00').getDay()];
    }
};

// Autenticação
const auth = {
    async login(email, senha) {
        try {
            const data = await utils.api('login.php', {
                method: 'POST',
                body: JSON.stringify({ email, senha })
            });
            
            if (data.success) {
                AppState.user = data.user;
                window.location.href = 'dashboard.php';
            }
        } catch (error) {
            utils.showAlert(error.message, 'error');
        }
    },
    
    async register(nome, email, senha) {
        try {
            const data = await utils.api('registro.php', {
                method: 'POST',
                body: JSON.stringify({ nome, email, senha })
            });
            
            if (data.success) {
                AppState.user = data.user;
                window.location.href = 'dashboard.php';
            }
        } catch (error) {
            utils.showAlert(error.message, 'error');
        }
    },
    
    async logout() {
        try {
            await utils.api('logout.php');
            window.location.href = 'login.php';
        } catch (error) {
            utils.showAlert(error.message, 'error');
        }
    }
};

// Gerenciamento de hábitos
const habitos = {
    async carregar() {
        try {
            const data = await utils.api('habitos.php');
            AppState.habitos = data.habitos;
            return data.habitos;
        } catch (error) {
            utils.showAlert('Erro ao carregar hábitos', 'error');
            return [];
        }
    },
    
    async criar(habito) {
        try {
            const data = await utils.api('habitos.php', {
                method: 'POST',
                body: JSON.stringify(habito)
            });
            
            if (data.success) {
                utils.showAlert('Hábito criado com sucesso!', 'success');
                return data;
            }
        } catch (error) {
            utils.showAlert(error.message, 'error');
            throw error;
        }
    },
    
    async atualizar(habito) {
        try {
            const data = await utils.api('habitos.php', {
                method: 'PUT',
                body: JSON.stringify(habito)
            });
            
            if (data.success) {
                utils.showAlert('Hábito atualizado com sucesso!', 'success');
                return data;
            }
        } catch (error) {
            utils.showAlert(error.message, 'error');
            throw error;
        }
    },
    
    async deletar(id) {
        if (!confirm('Tem certeza que deseja remover este hábito?')) {
            return;
        }
        
        try {
            const data = await utils.api(`habitos.php?id=${id}`, {
                method: 'DELETE'
            });
            
            if (data.success) {
                utils.showAlert('Hábito removido com sucesso!', 'success');
                return data;
            }
        } catch (error) {
            utils.showAlert(error.message, 'error');
            throw error;
        }
    }
};

// Gerenciamento de registros
const registros = {
    async carregar(habitoId = null, dataInicio = null, dataFim = null) {
        try {
            let url = 'registros.php?';
            if (habitoId) url += `habito_id=${habitoId}&`;
            if (dataInicio) url += `data_inicio=${dataInicio}&`;
            if (dataFim) url += `data_fim=${dataFim}`;
            
            const data = await utils.api(url);
            return data.registros;
        } catch (error) {
            utils.showAlert('Erro ao carregar registros', 'error');
            return [];
        }
    },
    
    async marcar(habitoId, data = null, concluido = true, notas = '') {
        try {
            const response = await utils.api('registros.php', {
                method: 'POST',
                body: JSON.stringify({
                    habito_id: habitoId,
                    data: data || utils.today(),
                    concluido: concluido,
                    notas: notas
                })
            });
            
            if (window.character) {
                const habito = AppState.habitos.find(h => h.id === habitoId);
                
                if (habito) {
                    if (concluido) {
                        window.character.celebrateHabit(habito.nome);
                        
                        // Verificar sequência
                        if (response.sequencia && response.sequencia >= 3) {
                            setTimeout(() => {
                                window.character.celebrateStreak(response.sequencia);
                            }, 3000);
                        }
                    } else {
                        // Desmarcar - descelebrar
                        window.character.uncelebrateHabit(habito.nome);
                    }
                }
            }
            
            return response;
        } catch (error) {
            utils.showAlert(error.message, 'error');
            throw error;
        }
    },
    
    async verificarConcluido(habitoId, data) {
        const key = `${habitoId}_${data}`;
        if (AppState.registros[key] !== undefined) {
            return AppState.registros[key];
        }
        
        const registrosHabito = await this.carregar(habitoId, data, data);
        const concluido = registrosHabito.length > 0 && registrosHabito[0].concluido;
        AppState.registros[key] = concluido;
        return concluido;
    }
};

// Estatísticas
const estatisticas = {
    async carregar() {
        try {
            const data = await utils.api('estatisticas.php');
            AppState.estatisticas = data.estatisticas;
            return data.estatisticas;
        } catch (error) {
            utils.showAlert('Erro ao carregar estatísticas', 'error');
            return null;
        }
    }
};

// Modal
class Modal {
    constructor(modalId) {
        this.modal = document.getElementById(modalId);
        this.closeBtn = this.modal?.querySelector('.modal-close');
        
        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.close());
        }
        
        this.modal?.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });
    }
    
    open() {
        this.modal?.classList.add('active');
    }
    
    close() {
        this.modal?.classList.remove('active');
    }
}

// Exportar para uso global
window.AppState = AppState;
window.utils = utils;
window.auth = auth;
window.habitos = habitos;
window.registros = registros;
window.estatisticas = estatisticas;
window.Modal = Modal;

// Alertas (notificações no navegador)
const alerts = {
    timers: {},
    initialized: false,
    settings: {
        voice: null,
        volume: 1.0,
        rate: 1.0,
        pitch: 1.0
    },
    progressEls: {},
    progressLoop: null,
    async requestPermission() {
        try {
            if (!('Notification' in window)) return;
            if (Notification.permission === 'default') {
                await Notification.requestPermission();
            }
        } catch (e) { /* ignore */ }
    },
    initVoices(callback) {
        if (!('speechSynthesis' in window)) return;
        const loadVoices = () => {
            const voices = window.speechSynthesis.getVoices() || [];
            // tenta default pt-BR
            const ptVoice = voices.find(v => (v.lang || '').toLowerCase().startsWith('pt-br'))
                || voices.find(v => (v.lang || '').toLowerCase().startsWith('pt'))
                || voices[0] || null;
            this.settings.voice = this.settings.voice || ptVoice;
            if (typeof callback === 'function') callback(voices);
        };
        loadVoices();
        window.speechSynthesis.onvoiceschanged = loadVoices;
    },
    populateVoiceSelect(selectEl) {
        if (!selectEl || !('speechSynthesis' in window)) return;
        const voices = window.speechSynthesis.getVoices() || [];
        selectEl.innerHTML = '';
        voices.forEach((v, idx) => {
            const opt = document.createElement('option');
            opt.value = idx.toString();
            opt.textContent = `${v.name} (${v.lang})`;
            selectEl.appendChild(opt);
        });
        // selecionar padrão
        let defaultIndex = voices.findIndex(v => (v.lang || '').toLowerCase().startsWith('pt-br'));
        if (defaultIndex < 0) defaultIndex = voices.findIndex(v => (v.lang || '').toLowerCase().startsWith('pt'));
        if (defaultIndex < 0) defaultIndex = 0;
        if (voices[defaultIndex]) {
            selectEl.value = defaultIndex.toString();
            this.settings.voice = voices[defaultIndex];
        }
    },
    clearTimers() {
        Object.values(this.timers).forEach(t => {
            try {
                if (t && t.type === 'interval') clearInterval(t.id);
                else if (t && t.type === 'timeout') clearTimeout(t.id);
            } catch (e) {}
        });
        this.timers = {};
        this.progressEls = {};
        if (this.progressLoop) {
            clearInterval(this.progressLoop);
            this.progressLoop = null;
        }
    },
    saveTimerToStorage(habitoId, startedAt, totalMs, phase, originalTotalMs) {
        try {
            const key = `alert_timer_${habitoId}`;
            localStorage.setItem(key, JSON.stringify({ startedAt, totalMs, phase, originalTotalMs }));
        } catch (e) {}
    },
    loadTimerFromStorage(habitoId) {
        try {
            const key = `alert_timer_${habitoId}`;
            const data = localStorage.getItem(key);
            if (data) return JSON.parse(data);
        } catch (e) {}
        return null;
    },
    removeTimerFromStorage(habitoId) {
        try {
            const key = `alert_timer_${habitoId}`;
            localStorage.removeItem(key);
        } catch (e) {}
    },
    initialize() {
        this.clearTimers();
        this.clearProgress();
        this.requestPermission();
        this.initVoices();
        (AppState.habitos || []).forEach(h => {
            if (h.alerta_ativo) {
                if (h.alerta_tipo === 'dia') this.scheduleByDay(h);
                else if (h.alerta_tipo === 'hora') this.scheduleDaily(h);
                else if (h.alerta_tipo === 'minuto') this.scheduleInterval(h);
            }
        });
        this.initialized = true;
    },
    registerProgress(habitoId, el) {
        if (!el) return;
        this.progressEls[habitoId] = el;
        this.startProgressLoop();
    },
    registerAllProgress() {
        // Registra todos os elementos de progresso após os timers serem criados
        this.progressEls = {};
        (AppState.habitos || []).forEach(h => {
            if (h.alerta_ativo) {
                const el = document.getElementById(`progress-${h.id}`);
                if (el) {
                    this.progressEls[h.id] = el;
                }
            }
        });
        this.startProgressLoop();
        this.refreshProgress(); // Atualiza imediatamente
    },
    clearProgress() {
        this.progressEls = {};
        if (this.progressLoop) {
            clearInterval(this.progressLoop);
            this.progressLoop = null;
        }
    },
    startProgressLoop() {
        if (this.progressLoop) return;
        this.progressLoop = setInterval(() => this.refreshProgress(), 1000);
    },
    refreshProgress() {
        const now = Date.now();
        Object.entries(this.progressEls).forEach(([id, el]) => {
            const t = this.timers[id];
            if (!t || !t.nextAt || !t.totalMs) {
                el.style.width = '0%';
                return;
            }
            const remaining = t.nextAt - now;
            const pct = Math.max(0, Math.min(100, ((t.totalMs - remaining) / t.totalMs) * 100));
            el.style.width = `${pct}%`;
        });
    },
    setTimerMeta(h, id, totalMs, phase, originalTotalMs = null) {
        const now = Date.now();
        const origTotal = originalTotalMs !== null ? originalTotalMs : totalMs;
        this.timers[h.id] = {
            id,
            type: 'timeout',
            nextAt: now + totalMs,
            totalMs: origTotal,
            phase,
            startedAt: now
        };
        this.saveTimerToStorage(h.id, now, totalMs, phase, origTotal);
        this.refreshProgress();
    },
    notify(h, extraMsg = '') {
        const title = `Alerta: ${h.nome}`;
        const body = h.alerta_mensagem || extraMsg || 'Hora do seu hábito!';
        // Notificação visual
        if ('Notification' in window && Notification.permission === 'granted') {
            try {
                new Notification(title, { body });
            } catch (e) {
                utils.showAlert(`${title} - ${body}`, 'info');
            }
        } else {
            utils.showAlert(`${title} - ${body}`, 'info');
        }
        // TTS com configurações
        this.speakMessage(body);
    },
    speakMessage(text) {
        try {
            if (!('speechSynthesis' in window)) return;
            const utter = new SpeechSynthesisUtterance(text);
            // aplicar configurações
            const s = this.settings;
            utter.lang = (s.voice && s.voice.lang) ? s.voice.lang : 'pt-BR';
            utter.rate = s.rate;
            utter.pitch = s.pitch;
            utter.volume = s.volume;
            if (s.voice) utter.voice = s.voice;
            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(utter);
        } catch (e) {}
    },
    scheduleByDay(h) {
        // dias: "1,3,5" (Seg=1 ... Dom=0), hora: "HH:MM"
        const dias = (h.alerta_dias || '').split(',').map(d => parseInt(d, 10)).filter(n => !isNaN(n));
        const hora = h.alerta_hora || '09:00';
        if (dias.length === 0) return;
        
        // Tentar restaurar do localStorage
        const saved = this.loadTimerFromStorage(h.id);
        let nextMs = this.nextOccurrenceMsByDays(dias, hora);
        let originalTotal = nextMs;
        
        if (saved && saved.phase === 'interval') {
            const elapsed = Date.now() - saved.startedAt;
            const remaining = saved.totalMs - elapsed;
            if (remaining > 0) {
                nextMs = remaining;
                originalTotal = saved.originalTotalMs || saved.totalMs;
            }
        }
        
        const id = setTimeout(() => {
            this.removeTimerFromStorage(h.id);
            this.notify(h);
            // Reagendar próxima ocorrência
            this.scheduleByDay(h);
        }, nextMs);
        this.setTimerMeta(h, id, nextMs, 'interval', originalTotal);
    },
    nextOccurrenceMsByDays(dias, hora) {
        const now = new Date();
        const [hh, mm] = (hora || '09:00').split(':').map(Number);
        let minDiff = Infinity;
        for (let i = 0; i < 7; i++) {
            const target = new Date(now);
            target.setHours(hh, mm, 0, 0);
            // Map JS getDay (0=Dom ... 6=Sáb) to our dias list
            const alvoDia = ((now.getDay() + i) % 7);
            if (!dias.includes(alvoDia)) continue;
            if (i > 0 || target <= now) {
                target.setDate(target.getDate() + (i || 0));
                if (i === 0 && target <= now) target.setDate(target.getDate() + 7);
            }
            const diff = target.getTime() - now.getTime();
            if (diff > 0 && diff < minDiff) minDiff = diff;
        }
        return isFinite(minDiff) ? minDiff : 0;
    },
    scheduleDaily(h) {
        const hora = h.alerta_hora || '09:00';
        const now = new Date();
        const [hh, mm] = hora.split(':').map(Number);
        const target = new Date(now);
        target.setHours(hh, mm, 0, 0);
        if (target <= now) target.setDate(target.getDate() + 1);
        let diff = target.getTime() - now.getTime();
        let originalTotal = diff;
        
        // Tentar restaurar do localStorage
        const saved = this.loadTimerFromStorage(h.id);
        if (saved && saved.phase === 'interval') {
            const elapsed = Date.now() - saved.startedAt;
            const remaining = saved.totalMs - elapsed;
            if (remaining > 0) {
                diff = remaining;
                originalTotal = saved.originalTotalMs || saved.totalMs;
            }
        }
        
        const id = setTimeout(() => {
            this.removeTimerFromStorage(h.id);
            this.notify(h);
            // Reagendar para o próximo dia
            this.scheduleDaily(h);
        }, diff);
        this.setTimerMeta(h, id, diff, 'interval', originalTotal);
    },
    scheduleInterval(h) {
        const mins = parseInt(h.alerta_intervalo_minutos || 60, 10);
        const rest = parseInt(h.alerta_descanso_segundos || 0, 10);
        if (!mins || mins <= 0) return;
        const intervalMs = mins * 60 * 1000;
        const restMs = (rest && rest > 0) ? rest * 1000 : 0;
        
        const cycle = (isRestore = false) => {
            let waitTime = intervalMs;
            let currentPhase = 'interval';
            let originalTotal = intervalMs;
            
            // Tentar restaurar do localStorage
            if (isRestore) {
                const saved = this.loadTimerFromStorage(h.id);
                if (saved) {
                    const elapsed = Date.now() - saved.startedAt;
                    const remaining = saved.totalMs - elapsed;
                    if (remaining > 0) {
                        waitTime = remaining;
                        currentPhase = saved.phase;
                        originalTotal = saved.originalTotalMs || saved.totalMs;
                    }
                }
            }
            
            if (currentPhase === 'rest') {
                // Estava em descanso, continua
                const restId = setTimeout(() => {
                    this.removeTimerFromStorage(h.id);
                    this.notifyRestEnd(h);
                    cycle(false);
                }, waitTime);
                this.setTimerMeta(h, restId, waitTime, 'rest', originalTotal);
            } else {
                // Fase de intervalo normal
                const intervalId = setTimeout(() => {
                    this.removeTimerFromStorage(h.id);
                    this.notify(h);
                    if (restMs > 0) {
                        const restId = setTimeout(() => {
                            this.removeTimerFromStorage(h.id);
                            this.notifyRestEnd(h);
                            cycle(false);
                        }, restMs);
                        this.setTimerMeta(h, restId, restMs, 'rest', restMs);
                    } else {
                        cycle(false);
                    }
                }, waitTime);
                this.setTimerMeta(h, intervalId, waitTime, 'interval', originalTotal);
            }
        };
        
        cycle(true); // Primeira vez tenta restaurar
    },
    notifyRestEnd(h) {
        const message = `Descanso encerrado para: ${h.nome}. Retome o hábito.`;
        this.notify(h, message);
    }
};

window.alerts = alerts;

// Preferências do usuário (inclui TTS)
const userPrefs = {
    async load() {
        try {
            const data = await utils.api('usuarios.php');
            if (data && data.user) {
                const u = data.user;
                if (window.alerts) {
                    // aplicar TTS
                    alerts.settings.rate = parseFloat(u.tts_rate ?? alerts.settings.rate);
                    alerts.settings.pitch = parseFloat(u.tts_pitch ?? alerts.settings.pitch);
                    alerts.settings.volume = parseFloat(u.tts_volume ?? alerts.settings.volume);
                    // voz será definida após carregar voices; armazenamos o nome
                    alerts.settings.voiceName = u.tts_voice || null;
                    alerts.initVoices((voices) => {
                        const select = document.getElementById('habitoTTSVoz');
                        alerts.populateVoiceSelect(select);
                        if (alerts.settings.voiceName) {
                            const matchIndex = voices.findIndex(v => v.name === alerts.settings.voiceName);
                            if (matchIndex >= 0) {
                                alerts.settings.voice = voices[matchIndex];
                                if (select) select.value = matchIndex.toString();
                            }
                        }
                        // aplicar sliders
                        const volEl = document.getElementById('habitoTTSVolume');
                        const rateEl = document.getElementById('habitoTTSRate');
                        const pitchEl = document.getElementById('habitoTTSPitch');
                        if (volEl) volEl.value = alerts.settings.volume;
                        if (rateEl) rateEl.value = alerts.settings.rate;
                        if (pitchEl) pitchEl.value = alerts.settings.pitch;
                    });
                }
            }
        } catch (e) {
            // ignore
        }
    },
    async save(partial) {
        try {
            const payload = {
                tts_voice: partial.tts_voice ?? (alerts.settings.voice ? alerts.settings.voice.name : null),
                tts_volume: partial.tts_volume ?? alerts.settings.volume,
                tts_rate: partial.tts_rate ?? alerts.settings.rate,
                tts_pitch: partial.tts_pitch ?? alerts.settings.pitch
            };
            await utils.api('usuarios.php', { method: 'PUT', body: JSON.stringify(payload) });
        } catch (e) {
            // ignore
        }
    }
};

window.userPrefs = userPrefs;
