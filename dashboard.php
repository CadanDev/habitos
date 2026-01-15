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
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='0.9em' font-size='90'>✓</text></svg>">
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

        <!-- Seção de Veículos -->
        <div style="margin-top: 40px;">
			<div class="w-100 d-flex flex-row justify-between">
				<h2 style="margin-bottom: 20px; color: var(--text-primary); font-size: 24px; font-weight: 600;">🚗 Meus Veículos</h2>
				<div class="w-20">
					<input type="date" id="filter-date-veiculos" name="filter-date-veiculos" class="form-input" />
				</div>
			</div>
            
            <!-- Estatísticas de Veículos -->
            <div class="grid grid-4 mb-20" id="veiculosStatsContainer">
                <div class="stat-card">
                    <div class="stat-label">🚗 Total de Veículos</div>
                    <div class="stat-value" id="totalVeiculos">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">⛽ Total Gasto (Combustível)</div>
                    <div class="stat-value" id="totalGastoCombustivel">R$ 0,00</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">🔧 Total Gasto (Manutenção)</div>
                    <div class="stat-value" id="totalGastoManutencao">R$ 0,00</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">💰 Total Investido</div>
                    <div class="stat-value" id="totalInvestido">R$ 0,00</div>
                </div>
            </div>

            <!-- Lista de Veículos -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Meus Veículos</h3>
                    <button onclick="abrirModalNovoVeiculo()" class="btn btn-primary">
                        + Novo Veículo
                    </button>
                </div>
                <div id="veiculosLista" class="col">
                    <!-- Veículos serão carregados aqui -->
                </div>
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
                                <label class="form-label" for="habitoTTSProvider">Provider de áudio</label>
                                <select id="habitoTTSProvider" class="form-input">
                                    <option value="chrome">Chrome (Web Speech)</option>
                                    <option value="gpt">OpenAI (GPT)</option>
                                </select>
                            </div>
                            <div id="voiceOpenAIContainer" style="display: none;">
                                <label class="form-label" for="habitoTTSVozOpenAI">Voz OpenAI</label>
                                <select id="habitoTTSVozOpenAI" class="form-input">
                                    <option value="alloy">Alloy</option>
                                    <option value="echo">Echo</option>
                                    <option value="fable">Fable</option>
                                    <option value="onyx">Onyx</option>
                                    <option value="nova" selected>Nova</option>
                                    <option value="shimmer">Shimmer</option>
                                </select>
                            </div>
                            <div id="voiceChromeContainer">
                                <label class="form-label" for="habitoTTSVoz">Voz do navegador</label>
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
                            <div id="pitchContainer">
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
            // Carregar dados de veículos
            await carregarVeiculos();
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
            // Provider selector
            const providerSelect = document.getElementById('habitoTTSProvider');
            if (providerSelect) {
                function updateVoiceFields() {
                    const isOpenAI = providerSelect.value === 'gpt';
                    document.getElementById('voiceOpenAIContainer').style.display = isOpenAI ? 'block' : 'none';
                    document.getElementById('voiceChromeContainer').style.display = isOpenAI ? 'none' : 'block';
                    document.getElementById('pitchContainer').style.display = isOpenAI ? 'none' : 'block';
                }
                providerSelect.addEventListener('change', (e) => {
                    alerts.settings.tts_provider = e.target.value;
                    updateVoiceFields();
                    userPrefs.save({ tts_provider: alerts.settings.tts_provider });
                });
                updateVoiceFields();
            }
            const select = document.getElementById('habitoTTSVoz');
            select.addEventListener('change', () => {
                const voices = window.speechSynthesis.getVoices() || [];
                const idx = parseInt(select.value || '0', 10);
                alerts.settings.voice = voices[idx] || null;
                userPrefs.save({ tts_voice: alerts.settings.voice ? alerts.settings.voice.name : null });
            });
            const voiceOpenAISelect = document.getElementById('habitoTTSVozOpenAI');
            if (voiceOpenAISelect) {
                voiceOpenAISelect.addEventListener('change', (e) => {
                    alerts.settings.tts_voice = e.target.value;
                    userPrefs.save({ tts_voice_openai: alerts.settings.tts_voice });
                });
            }
            document.getElementById('habitoTTSVolume').addEventListener('input', (e) => {
                alerts.settings.volume = parseFloat(e.target.value);
                userPrefs.save({ tts_volume: alerts.settings.volume });
            });
            document.getElementById('habitoTTSRate').addEventListener('input', (e) => {
                alerts.settings.rate = parseFloat(e.target.value);
                userPrefs.save({ tts_rate: alerts.settings.rate });
            });
            const pitchEl = document.getElementById('habitoTTSPitch');
            if (pitchEl) {
                pitchEl.addEventListener('input', (e) => {
                    alerts.settings.pitch = parseFloat(e.target.value);
                    userPrefs.save({ tts_pitch: alerts.settings.pitch });
                });
            }
            document.getElementById('btnTestarAlerta').addEventListener('click', () => {
                const msg = document.getElementById('habitoAlertaMensagem').value || 'Hora do seu hábito!';
                alerts.speakMessage(msg);
            });
        }

        // ========== VEÍCULOS ==========
        let modalNovoVeiculo;
        let veiculoEmEdicao = null;

        // Carregar veículos e suas estatísticas
        async function carregarVeiculos() {
            try {
                const response = await fetch(`${API_BASE_URL}/veiculos.php?action=listar`);
                const result = await response.json();
                
                if (result.sucesso) {
                    exibirVeiculos(result.dados);
                    await carregarEstatisticasVeiculos(result.dados);
                }
            } catch (error) {
                console.error('Erro ao carregar veículos:', error);
            }
        }

        // Carregar estatísticas de veículos
        async function carregarEstatisticasVeiculos(veiculos) {
            let totalGastoCombustivel = 0;
            let totalGastoManutencao = 0;

            for (const veiculo of veiculos) {
                try {
                    const response = await fetch(`${API_BASE_URL}/veiculos.php?action=detalhes&id=${veiculo.id}`);
                    const result = await response.json();
                    
                    if (result.sucesso) {
                        totalGastoCombustivel += parseFloat(result.abastecimentos.stats.total_gasto_abastecimentos || 0);
                        totalGastoManutencao += parseFloat(result.manutencoes.stats.total_gasto_manutencoes || 0);
                    }
                } catch (error) {
                    console.error('Erro ao carregar detalhes do veículo:', error);
                }
            }

            const totalInvestido = totalGastoCombustivel + totalGastoManutencao;

            document.getElementById('totalVeiculos').textContent = veiculos.length;
            document.getElementById('totalGastoCombustivel').textContent = `R$ ${totalGastoCombustivel.toFixed(2).replace('.', ',')}`;
            document.getElementById('totalGastoManutencao').textContent = `R$ ${totalGastoManutencao.toFixed(2).replace('.', ',')}`;
            document.getElementById('totalInvestido').textContent = `R$ ${totalInvestido.toFixed(2).replace('.', ',')}`;
        }

        // Exibir lista de veículos
        async function exibirVeiculos(veiculos) {
            const container = document.getElementById('veiculosLista');
            
            if (veiculos.length === 0) {
                container.innerHTML = '<p class="row" style="text-align: center; color: var(--gray-500);">Nenhum veículo cadastrado. Cadastre seu primeiro veículo!</p>';
                return;
            }

            container.innerHTML = '';

            for (const veiculo of veiculos) {
                const veiculoEl = document.createElement('div');
                veiculoEl.className = 'row';
                veiculoEl.style.cssText = 'padding: 15px; border: 1px solid var(--gray-200); border-radius: 8px; margin-bottom: 10px; background: var(--bg-secondary); align-items: center; gap: 15px;';
                
                let stats = { abastecimentos: { stats: {} }, manutencoes: { stats: {} } };
                try {
                    const response = await fetch(`${API_BASE_URL}/veiculos.php?action=detalhes&id=${veiculo.id}`);
                    const result = await response.json();
                    if (result.sucesso) {
                        stats = result;
                    }
                } catch (error) {
                    console.error('Erro ao carregar detalhes:', error);
                }

                const totalAbastecimentos = stats.abastecimentos.stats.total_abastecimentos || 0;
                const totalManutencoes = stats.manutencoes.stats.total_manutencoes || 0;
                const gastoAbastecimentos = parseFloat(stats.abastecimentos.stats.total_gasto_abastecimentos || 0);
                const gastoManutencoes = parseFloat(stats.manutencoes.stats.total_gasto_manutencoes || 0);
                const custoTotal = gastoAbastecimentos + gastoManutencoes;

                veiculoEl.innerHTML = `
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 16px; color: var(--text-primary); margin-bottom: 8px;">
                            🚗 ${veiculo.modelo} ${veiculo.marca} ${veiculo.ano}
                            ${veiculo.apelido ? `<span style="color: var(--gray-500); font-size: 14px;">(${veiculo.apelido})</span>` : ''}
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; font-size: 13px; color: var(--gray-600);">
                            <div>📍 <strong>${veiculo.quilometragem.toLocaleString('pt-BR')}</strong> km</div>
                            <div>⛽ <strong>${totalAbastecimentos}</strong> abastecimentos</div>
                            <div>🔧 <strong>${totalManutencoes}</strong> manutenções</div>
                            <div>💰 <strong>R$ ${custoTotal.toFixed(2).replace('.', ',')}</strong> investido</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button 
                            class="btn btn-primary" 
                            onclick="abrirVeiculoDetalhes(${veiculo.id})"
                            title="Ver detalhes"
                        >
                            👁️ Ver
                        </button>
                        <button 
                            class="btn btn-outline" 
                            onclick="abrirModalAbastecimento(${veiculo.id})"
                            title="Abastecer"
                        >
                            ⛽ Abastecer
                        </button>
                        <button 
                            class="btn btn-outline" 
                            onclick="abrirModalManutencao(${veiculo.id})"
                            title="Registrar manutenção"
                        >
                            🔧 Manutenção
                        </button>
                        <button 
                            class="btn btn-outline" 
                            onclick="editarVeiculo(${veiculo.id})"
                            title="Editar"
                        >
                            ✏️
                        </button>
                        <button 
                            class="btn btn-danger" 
                            onclick="deletarVeiculo(${veiculo.id})"
                            title="Excluir"
                        >
                            🗑️
                        </button>
                    </div>
                `;

                container.appendChild(veiculoEl);
            }
        }

        // Abrir detalhes do veículo
        function abrirVeiculoDetalhes(veiculoId) {
            const veiculo = AppState.veiculos?.find(v => v.id == veiculoId);
            if (window.character && veiculo) {
                window.character.speak(`Vamos ver os detalhes do ${veiculo.apelido || veiculo.modelo}! 📋`, 'happy', 3000);
            }
            alert(`Funcionalidade de detalhes em desenvolvimento. ID: ${veiculoId}`);
        }

        // Abrir modal novo veículo
        function abrirModalNovoVeiculo() {
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.id = 'modalNovoVeiculo';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Novo Veículo</h3>
                        <button class="modal-close">&times;</button>
                    </div>
                    <form id="formNovoVeiculo">
                        <div class="form-group">
                            <label class="form-label" for="veiMarca">Marca *</label>
                            <input type="text" id="veiMarca" class="form-input" required placeholder="Ex: Toyota">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="veiModelo">Modelo *</label>
                            <input type="text" id="veiModelo" class="form-input" required placeholder="Ex: Corolla">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="veiAno">Ano *</label>
                            <input type="number" id="veiAno" class="form-input" required placeholder="Ex: 2022" min="1900" max="2099">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="veiCor">Cor</label>
                            <input type="text" id="veiCor" class="form-input" placeholder="Ex: Branco">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="veiApelido">Apelido</label>
                            <input type="text" id="veiApelido" class="form-input" placeholder="Ex: Meu Carro">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="veiQuilometragem">Quilometragem Atual</label>
                            <input type="number" id="veiQuilometragem" class="form-input" placeholder="Ex: 50000" min="0">
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Salvar</button>
                            <button type="button" onclick="fecharModalVeiculo()" class="btn btn-outline">Cancelar</button>
                        </div>
                    </form>
                </div>
            `;

            document.body.appendChild(modal);
            modalNovoVeiculo = new Modal('modalNovoVeiculo');
            modalNovoVeiculo.open();

            document.getElementById('formNovoVeiculo').addEventListener('submit', async (e) => {
                e.preventDefault();
                await salvarVeiculo();
            });
        }

        // Salvar novo veículo
        async function salvarVeiculo() {
            const dados = {
                marca: document.getElementById('veiMarca').value,
                modelo: document.getElementById('veiModelo').value,
                ano: parseInt(document.getElementById('veiAno').value),
                cor: document.getElementById('veiCor').value || null,
                apelido: document.getElementById('veiApelido').value || null,
                quilometragem: parseInt(document.getElementById('veiQuilometragem').value) || 0,
            };

            try {
                const response = await fetch(`${API_BASE_URL}/veiculos.php?action=criar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dados)
                });

                const result = await response.json();
                if (result.sucesso) {
                    if (window.character) {
                        window.character.speak(`Novo veículo ${dados.modelo} cadastrado! 🚗✨`, 'excited', 3500);
                    }
                    fecharModalVeiculo();
                    await carregarVeiculos();
                } else {
                    if (window.character) {
                        window.character.speak('Ops! Algo deu errado ao cadastrar. 😟', 'thinking', 2500);
                    }
                    alert('Erro: ' + result.erro);
                }
            } catch (error) {
                console.error('Erro ao salvar veículo:', error);
                if (window.character) {
                    window.character.speak('Erro ao salvar o veículo. 😞', 'tired', 2500);
                }
                alert('Erro ao salvar veículo');
            }
        }

        // Editar veículo
        function editarVeiculo(veiculoId) {
            const veiculo = AppState.veiculos?.find(v => v.id == veiculoId);
            if (window.character && veiculo) {
                window.character.speak(`Vamos editar as informações do ${veiculo.apelido || veiculo.modelo}! ✏️`, 'happy', 3000);
            }
            alert(`Funcionalidade de edição em desenvolvimento. ID: ${veiculoId}`);
        }

        // Deletar veículo
        async function deletarVeiculo(veiculoId) {
            const veiculo = AppState.veiculos?.find(v => v.id == veiculoId);
            const nomVeiculo = veiculo ? veiculo.apelido || veiculo.modelo : 'veículo';
            
            if (!confirm('Tem certeza que deseja deletar este veículo? Todos os registros de abastecimento e manutenção serão perdidos.')) {
                return;
            }

            try {
                const response = await fetch(`${API_BASE_URL}/veiculos.php?action=deletar&id=${veiculoId}`, {
                    method: 'DELETE'
                });

                const result = await response.json();
                if (result.sucesso) {
                    if (window.character) {
                        window.character.speak(`${nomVeiculo} foi removido da garagem. 😢`, 'tired', 3500);
                    }
                    await carregarVeiculos();
                } else {
                    if (window.character) {
                        window.character.speak('Não consegui deletar o veículo. 😟', 'thinking', 2500);
                    }
                    alert('Erro: ' + result.erro);
                }
            } catch (error) {
                console.error('Erro ao deletar veículo:', error);
                if (window.character) {
                    window.character.speak('Erro ao deletar o veículo! 😞', 'tired', 2500);
                }
                alert('Erro ao deletar veículo');
            }
        }

        // Abrir modal abastecimento
        function abrirModalAbastecimento(veiculoId) {
            const veiculo = AppState.veiculos?.find(v => v.id == veiculoId);
            if (window.character && veiculo) {
                window.character.speak(`Vamos registrar um abastecimento para o ${veiculo.apelido || veiculo.modelo}! ⛽`, 'happy', 3000);
            }
            alert(`Funcionalidade de abastecimento em desenvolvimento. ID: ${veiculoId}`);
        }

        // Abrir modal manutenção
        function abrirModalManutencao(veiculoId) {
            const veiculo = AppState.veiculos?.find(v => v.id == veiculoId);
            if (window.character && veiculo) {
                window.character.speak(`Vamos registrar uma manutenção para o ${veiculo.apelido || veiculo.modelo}! 🔧`, 'thinking', 3000);
            }
            alert(`Funcionalidade de manutenção em desenvolvimento. ID: ${veiculoId}`);
        }

        // Fechar modal veículo
        function fecharModalVeiculo() {
            if (modalNovoVeiculo) {
                modalNovoVeiculo.close();
                const modal = document.getElementById('modalNovoVeiculo');
                if (modal) modal.remove();
            }
        }
    </script>
</body>
</html>
