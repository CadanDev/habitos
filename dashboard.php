<?php
require_once 'config/config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Hábitos</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="dashboard.php" class="logo">🎯 Meus Hábitos</a>
            <div class="user-info">
                <span class="user-name">Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                <button onclick="auth.logout()" class="btn btn-outline">Sair</button>
            </div>
        </div>
    </header>
    
    <div class="container">
        <!-- Estatísticas -->
        <div class="grid grid-4 mb-20" id="statsContainer">
            <div class="stat-card">
                <div class="stat-label">➕ Total de Hábitos</div>
                <div class="stat-value" id="totalHabitos">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">✅ Concluídos Hoje</div>
                <div class="stat-value" id="habitosHoje">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">🔥 Melhor Sequência</div>
                <div class="stat-value" id="melhorSequencia">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">📈 Taxa Semanal</div>
                <div class="stat-value" id="taxaSemanal">0%</div>
            </div>
        </div>
        
        <!-- Lista de Hábitos -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Meus Hábitos</h2>
                <button onclick="abrirModalNovoHabito()" class="btn btn-primary">
                    + Novo Hábito
                </button>
            </div>
            <div id="habitosLista" class="col">
                <!-- Hábitos serão carregados aqui -->
            </div>
        </div>
    </div>
    
    <!-- Modal Novo/Editar Hábito -->
    <div id="modalHabito" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitulo">Novo Hábito</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="formHabito">
                <input type="hidden" id="habitoId">
                
                <div class="form-group">
                    <label class="form-label" for="habitoNome">Nome do Hábito</label>
                    <input 
                        type="text" 
                        id="habitoNome" 
                        class="form-input" 
                        required 
                        placeholder="Ex: Exercícios físicos"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="habitoDescricao">Descrição</label>
                    <textarea 
                        id="habitoDescricao" 
                        class="form-textarea" 
                        placeholder="Detalhes sobre o hábito..."
                    ></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="habitoIcone">Ícone (Emoji)</label>
                    <input 
                        type="text" 
                        id="habitoIcone" 
                        class="form-input" 
                        placeholder="💪"
                        maxlength="2"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="habitoCor">Cor</label>
                    <input 
                        type="color" 
                        id="habitoCor" 
                        class="form-input" 
                        value="#3b82f6"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="habitoMeta">Meta Semanal (dias)</label>
                    <input 
                        type="number" 
                        id="habitoMeta" 
                        class="form-input" 
                        min="1" 
                        max="7" 
                        value="7"
                    >
                </div>

                <!-- Configuração de Alerta -->
                <div class="form-group">
                    <label class="form-label" style="display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" id="habitoAlertaAtivo"> Hábito com alerta
                    </label>
                </div>
                <div id="alertaConfig" style="display:none; border:1px solid var(--gray-200); border-radius:8px; padding:12px;">
                    <div class="form-group">
                        <label class="form-label" for="habitoAlertaTipo">Tipo de alerta</label>
                        <select id="habitoAlertaTipo" class="form-input">
                            <option value="dia">Por dia (dias da semana + horário)</option>
                            <option value="hora">Por hora (todo dia, horário)</option>
                            <option value="minuto">Por minuto (intervalo)</option>
                        </select>
                    </div>
                    <div id="alertaDiaFields" style="display:none;">
                        <div class="form-group">
                            <label class="form-label">Dias da semana</label>
                            <div style="display:grid; grid-template-columns: repeat(7, 1fr); gap:6px;">
                                <label><input type="checkbox" class="alerta-dia" value="1"> Seg</label>
                                <label><input type="checkbox" class="alerta-dia" value="2"> Ter</label>
                                <label><input type="checkbox" class="alerta-dia" value="3"> Qua</label>
                                <label><input type="checkbox" class="alerta-dia" value="4"> Qui</label>
                                <label><input type="checkbox" class="alerta-dia" value="5"> Sex</label>
                                <label><input type="checkbox" class="alerta-dia" value="6"> Sáb</label>
                                <label><input type="checkbox" class="alerta-dia" value="0"> Dom</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="habitoAlertaHora">Horário</label>
                            <input type="time" id="habitoAlertaHora" class="form-input">
                        </div>
                    </div>
                    <div id="alertaHoraFields" style="display:none;">
                        <div class="form-group">
                            <label class="form-label" for="habitoAlertaHoraDiaria">Horário diário</label>
                            <input type="time" id="habitoAlertaHoraDiaria" class="form-input">
                        </div>
                    </div>
                    <div id="alertaMinutoFields" style="display:none;">
                        <div class="form-group">
                            <label class="form-label" for="habitoAlertaIntervalo">Intervalo (minutos)</label>
                            <input type="number" id="habitoAlertaIntervalo" class="form-input" min="1" value="60">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="habitoAlertaDescanso">Tempo de descanso (segundos, opcional)</label>
                            <input type="number" id="habitoAlertaDescanso" class="form-input" min="0" placeholder="Ex: 20">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="habitoAlertaMensagem">Mensagem do alerta (opcional)</label>
                        <input type="text" id="habitoAlertaMensagem" class="form-input" placeholder="Ex: Hora de praticar seu hábito!">
                    </div>
                    <div id="alertaMensagensDescanso" style="display:none;">
                        <div class="form-group">
                            <label class="form-label" for="habitoAlertaMensagemDescanso">Mensagem ao iniciar descanso (opcional)</label>
                            <input type="text" id="habitoAlertaMensagemDescanso" class="form-input" placeholder="Ex: Iniciando descanso de 20 segundos">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="habitoAlertaMensagemFimDescanso">Mensagem ao fim do descanso (opcional)</label>
                            <input type="text" id="habitoAlertaMensagemFimDescanso" class="form-input" placeholder="Ex: Descanso finalizado, retome o hábito!">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Configurações de áudio (opcional)</label>
                        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <label class="form-label" for="habitoTTSVoz">Voz</label>
                                <select id="habitoTTSVoz" class="form-input"></select>
                            </div>
                            <div>
                                <label class="form-label" for="habitoTTSVolume">Volume</label>
                                <input type="range" id="habitoTTSVolume" class="form-input" min="0" max="1" step="0.1" value="1">
                            </div>
                            <div>
                                <label class="form-label" for="habitoTTSRate">Velocidade</label>
                                <input type="range" id="habitoTTSRate" class="form-input" min="0.5" max="1.5" step="0.1" value="1">
                            </div>
                            <div>
                                <label class="form-label" for="habitoTTSPitch">Tom</label>
                                <input type="range" id="habitoTTSPitch" class="form-input" min="0" max="2" step="0.1" value="1">
                            </div>
                        </div>
                        <div style="margin-top: 8px;">
                            <button type="button" id="btnTestarAlerta" class="btn btn-outline">🔊 Testar alerta</button>
                        </div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        Salvar
                    </button>
                    <button type="button" onclick="modalHabito.close()" class="btn btn-outline">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <link rel="stylesheet" href="assets/css/character.css">
    <script>
        // Configuração da API baseada no ambiente
        const API_BASE_URL = '<?php echo env('BASE_URL', 'http://localhost'); ?>/api';
		window.API_BASE_URL = API_BASE_URL;
    </script>
    <script src="assets/js/character.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        let modalHabito;
        
        // Inicializar
        document.addEventListener('DOMContentLoaded', async () => {
            modalHabito = new Modal('modalHabito');
            await carregarDados();
            // Carregar preferências do usuário (TTS)
            if (window.userPrefs) {
                await userPrefs.load();
            }
        });
        
        // Carregar dados
        async function carregarDados() {
            await Promise.all([
                carregarEstatisticas(),
                carregarHabitos()
            ]);
            // Inicializar alertas PRIMEIRO (cria timers)
            if (window.alerts && typeof window.alerts.initialize === 'function') {
                window.alerts.initialize();
            }
            // DEPOIS registrar elementos de progresso
            if (window.alerts && typeof window.alerts.registerAllProgress === 'function') {
                window.alerts.registerAllProgress();
            }
        }
        
        // Carregar estatísticas
        async function carregarEstatisticas() {
            const stats = await estatisticas.carregar();
            if (stats) {
                document.getElementById('totalHabitos').textContent = stats.total_habitos;
                document.getElementById('habitosHoje').textContent = stats.habitos_hoje;
                document.getElementById('melhorSequencia').textContent = stats.melhor_sequencia.dias;
                document.getElementById('taxaSemanal').textContent = stats.taxa_semanal + '%';
            }
        }
        
        // Carregar hábitos
        async function carregarHabitos() {
            const habitosData = await habitos.carregar();
            const container = document.getElementById('habitosLista');
            
            if (habitosData.length === 0) {
                container.innerHTML = '<p class="row" style="text-align: center; color: var(--gray-500);">Nenhum hábito cadastrado. Crie seu primeiro hábito!</p>';
                return;
            }
            
            container.innerHTML = '';
            
            for (const habito of habitosData) {
                const concluido = await registros.verificarConcluido(habito.id, utils.today());
                
                const habitoEl = document.createElement('div');
                habitoEl.className = 'habit-item row';
                habitoEl.style.borderLeftColor = habito.cor;
                const alertaTexto = habito.alerta_ativo ? '🔔 alerta ativo' : `📊 ${habito.registros_semana}/${habito.meta_semanal} esta semana`;
                const progressId = `progress-${habito.id}`;
                const restBtnId = `rest-btn-${habito.id}`;
                const progressBar = habito.alerta_ativo ? `
                    <div class="alert-progress" style="position:relative;height:6px;border-radius:999px;background: var(--gray-200);overflow:hidden;margin-top:10px;">
                        <div class="alert-progress-bar" id="${progressId}" style="height:100%;width:0%;background:${habito.cor};transition:width 0.4s ease;"></div>
                    </div>
                ` : '';
                habitoEl.innerHTML = `
                    <div class="habit-icon" style="background: ${habito.cor}20;">
                        ${habito.icone}
                    </div>
                    <div class="habit-content">
                        <div class="habit-name">${habito.nome}</div>
                        <div class="habit-description">${habito.descricao || ''}</div>
                        <div class="habit-stats">
                            <span class="habit-stat">${alertaTexto}</span>
                            <span class="habit-stat">✅ ${habito.total_registros} total</span>
                        </div>
                        ${progressBar}
                    </div>
                    <div class="habit-actions">
                        <button 
                            id="${restBtnId}"
                            class="btn btn-primary" 
                            onclick="iniciarDescanso(${habito.id})"
                            title="Iniciar descanso"
                            style="display:none;"
                        >
                            ⏸️ Descansar
                        </button>
                        <button 
                            class="checkbox-btn ${concluido ? 'checked' : ''}" 
                            onclick="marcarHabito(${habito.id}, this, '${habito.nome}')"
                            title="${concluido ? 'Marcar como não concluído' : 'Marcar como concluído'}"
                        >
                            ${concluido ? '✓' : ''}
                        </button>
                        <button 
                            class="btn btn-outline" 
                            onclick="abrirConfigAlerta(${habito.id})"
                            title="Configurar alerta"
                        >
                            🔔
                        </button>
                        <button 
                            class="btn btn-outline" 
                            onclick="editarHabito(${habito.id})"
                            title="Editar"
                        >
                            ✏️
                        </button>
                        <button 
                            class="btn btn-danger" 
                            onclick="deletarHabito(${habito.id})"
                            title="Excluir"
                        >
                            🗑️
                        </button>
                    </div>
                `;
                
                container.appendChild(habitoEl);
            }
        }
        
        // Marcar/desmarcar hábito
        async function marcarHabito(habitoId, btn, habitName) {
            const estavaConcluido = btn.classList.contains('checked');
            
            try {
                await registros.marcar(habitoId, utils.today(), !estavaConcluido);
                btn.classList.toggle('checked');
                btn.innerHTML = btn.classList.contains('checked') ? '✓' : '';
                
                // Atualizar estatísticas
                await carregarEstatisticas();
                
                // Atualizar cache
                AppState.registros[`${habitoId}_${utils.today()}`] = !estavaConcluido;
            } catch (error) {
                console.error('Erro ao marcar hábito:', error);
            }
        }
        
        // Iniciar descanso de um hábito
        function iniciarDescanso(habitoId) {
            if (window.alerts && typeof window.alerts.startRest === 'function') {
                window.alerts.startRest(habitoId);
                // Esconder o botão
                const btn = document.getElementById(`rest-btn-${habitoId}`);
                if (btn) btn.style.display = 'none';
            }
        }
        
        // Atualizar visibilidade dos botões de descanso
        function atualizarBotoesDescanso() {
            if (!window.alerts || !window.alerts.pendingRestStarts) return;
            
            Object.keys(window.alerts.pendingRestStarts).forEach(habitoId => {
                const btn = document.getElementById(`rest-btn-${habitoId}`);
                if (btn) btn.style.display = 'inline-block';
            });
            
            // Esconder botões que não estão mais pendentes
            AppState.habitos.forEach(h => {
                if (!window.alerts.pendingRestStarts[h.id]) {
                    const btn = document.getElementById(`rest-btn-${h.id}`);
                    if (btn) btn.style.display = 'none';
                }
            });
        }
        
        // Verificar botões periodicamente
        setInterval(atualizarBotoesDescanso, 1000);
        
        // Abrir modal novo hábito
        function abrirModalNovoHabito() {
            document.getElementById('modalTitulo').textContent = 'Novo Hábito';
            document.getElementById('formHabito').reset();
            document.getElementById('habitoId').value = '';
            document.getElementById('habitoCor').value = '#3b82f6';
            document.getElementById('habitoMeta').value = '7';
            document.getElementById('habitoAlertaAtivo').checked = false;
            document.getElementById('alertaConfig').style.display = 'none';
            modalHabito.open();
        }
        
        // Editar hábito
        function editarHabito(id) {
            const habito = AppState.habitos.find(h => h.id == id);
            if (!habito) return;
            
            document.getElementById('modalTitulo').textContent = 'Editar Hábito';
            document.getElementById('habitoId').value = habito.id;
            document.getElementById('habitoNome').value = habito.nome;
            document.getElementById('habitoDescricao').value = habito.descricao || '';
            document.getElementById('habitoIcone').value = habito.icone;
            document.getElementById('habitoCor').value = habito.cor;
            document.getElementById('habitoMeta').value = habito.meta_semanal;
            document.getElementById('habitoAlertaAtivo').checked = !!habito.alerta_ativo;
            document.getElementById('alertaConfig').style.display = habito.alerta_ativo ? 'block' : 'none';
            const tipo = habito.alerta_tipo || 'dia';
            document.getElementById('habitoAlertaTipo').value = tipo;
            mostrarCamposAlerta(tipo);
            // Dias
            document.querySelectorAll('.alerta-dia').forEach(cb => { cb.checked = false; });
            if (habito.alerta_dias) {
                habito.alerta_dias.split(',').forEach(d => {
                    const el = document.querySelector(`.alerta-dia[value="${d}"]`);
                    if (el) el.checked = true;
                });
            }
            // Horas
            document.getElementById('habitoAlertaHora').value = habito.alerta_hora || '';
            document.getElementById('habitoAlertaHoraDiaria').value = habito.alerta_hora || '';
            // Intervalo
            document.getElementById('habitoAlertaIntervalo').value = habito.alerta_intervalo_minutos || 60;
            document.getElementById('habitoAlertaDescanso').value = habito.alerta_descanso_segundos || '';
            // Mensagens
            document.getElementById('habitoAlertaMensagem').value = habito.alerta_mensagem || '';
            document.getElementById('habitoAlertaMensagemDescanso').value = habito.alerta_mensagem_descanso || '';
            document.getElementById('habitoAlertaMensagemFimDescanso').value = habito.alerta_mensagem_fim_descanso || '';
            // Mostrar campos de descanso se houver tempo de descanso
            const descanso = habito.alerta_descanso_segundos;
            document.getElementById('alertaMensagensDescanso').style.display = (descanso && parseInt(descanso) > 0) ? 'block' : 'none';
            
            modalHabito.open();
        }

        // Abrir direto a configuração de alerta
        function abrirConfigAlerta(id) {
            editarHabito(id);
            document.getElementById('habitoAlertaAtivo').checked = true;
            document.getElementById('alertaConfig').style.display = 'block';
        }
        
        // Deletar hábito
        async function deletarHabito(id) {
            try {
                await habitos.deletar(id);
                await carregarDados();
            } catch (error) {
                console.error('Erro ao deletar hábito:', error);
            }
        }
        
        // Salvar hábito
        document.getElementById('formHabito').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const habitoData = {
                nome: document.getElementById('habitoNome').value,
                descricao: document.getElementById('habitoDescricao').value,
                icone: document.getElementById('habitoIcone').value || '✓',
                cor: document.getElementById('habitoCor').value,
                meta_semanal: document.getElementById('habitoMeta').value
            };

            // Alerta
            const alertaAtivo = document.getElementById('habitoAlertaAtivo').checked;
            if (alertaAtivo) {
                const tipo = document.getElementById('habitoAlertaTipo').value;
                habitoData.alerta_ativo = true;
                habitoData.alerta_tipo = tipo;
                habitoData.alerta_mensagem = document.getElementById('habitoAlertaMensagem').value || null;
                habitoData.alerta_mensagem_descanso = document.getElementById('habitoAlertaMensagemDescanso').value || null;
                habitoData.alerta_mensagem_fim_descanso = document.getElementById('habitoAlertaMensagemFimDescanso').value || null;
                if (tipo === 'dia') {
                    const diasSelecionados = Array.from(document.querySelectorAll('.alerta-dia:checked')).map(cb => cb.value).join(',');
                    habitoData.alerta_dias = diasSelecionados || null;
                    habitoData.alerta_hora = document.getElementById('habitoAlertaHora').value || null;
                } else if (tipo === 'hora') {
                    habitoData.alerta_hora = document.getElementById('habitoAlertaHoraDiaria').value || null;
                } else if (tipo === 'minuto') {
                    habitoData.alerta_intervalo_minutos = parseInt(document.getElementById('habitoAlertaIntervalo').value || '60', 10);
                    const descanso = document.getElementById('habitoAlertaDescanso').value;
                    if (descanso !== '') {
                        habitoData.alerta_descanso_segundos = parseInt(descanso, 10);
                    }
                }
            } else {
                habitoData.alerta_ativo = false;
            }
            
            const habitoId = document.getElementById('habitoId').value;
            
            try {
                if (habitoId) {
                    habitoData.id = habitoId;
                    await habitos.atualizar(habitoData);
                } else {
                    await habitos.criar(habitoData);
                }
                
                modalHabito.close();
                await carregarDados();
                // Recarregar agendamentos
                if (window.alerts && typeof window.alerts.initialize === 'function') {
                    window.alerts.initialize();
                }
            } catch (error) {
                console.error('Erro ao salvar hábito:', error);
            }
        });

        // UI dinâmica para alerta
        document.getElementById('habitoAlertaAtivo').addEventListener('change', (e) => {
            document.getElementById('alertaConfig').style.display = e.target.checked ? 'block' : 'none';
        });
        document.getElementById('habitoAlertaTipo').addEventListener('change', (e) => {
            mostrarCamposAlerta(e.target.value);
        });
        document.getElementById('habitoAlertaDescanso').addEventListener('input', (e) => {
            const descanso = e.target.value;
            document.getElementById('alertaMensagensDescanso').style.display = (descanso && parseInt(descanso) > 0) ? 'block' : 'none';
        });
        function mostrarCamposAlerta(tipo) {
            document.getElementById('alertaDiaFields').style.display = tipo === 'dia' ? 'block' : 'none';
            document.getElementById('alertaHoraFields').style.display = tipo === 'hora' ? 'block' : 'none';
            document.getElementById('alertaMinutoFields').style.display = tipo === 'minuto' ? 'block' : 'none';
        }

        // Configurações de TTS
        if (window.alerts) {
            alerts.initVoices((voices) => {
                const select = document.getElementById('habitoTTSVoz');
                alerts.populateVoiceSelect(select);
            });
            const select = document.getElementById('habitoTTSVoz');
            select.addEventListener('change', () => {
                const voices = window.speechSynthesis.getVoices() || [];
                const idx = parseInt(select.value || '0', 10);
                alerts.settings.voice = voices[idx] || null;
                userPrefs.save({ tts_voice: alerts.settings.voice ? alerts.settings.voice.name : null });
            });
            document.getElementById('habitoTTSVolume').addEventListener('input', (e) => {
                alerts.settings.volume = parseFloat(e.target.value);
                userPrefs.save({ tts_volume: alerts.settings.volume });
            });
            document.getElementById('habitoTTSRate').addEventListener('input', (e) => {
                alerts.settings.rate = parseFloat(e.target.value);
                userPrefs.save({ tts_rate: alerts.settings.rate });
            });
            document.getElementById('habitoTTSPitch').addEventListener('input', (e) => {
                alerts.settings.pitch = parseFloat(e.target.value);
                userPrefs.save({ tts_pitch: alerts.settings.pitch });
            });
            document.getElementById('btnTestarAlerta').addEventListener('click', () => {
                const msg = document.getElementById('habitoAlertaMensagem').value || 'Hora do seu hábito!';
                alerts.speakMessage(msg);
            });
        }
    </script>
</body>
</html>
