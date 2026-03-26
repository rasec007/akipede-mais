// public/js/app.js
function app() {
    return {
        user: {},
        produtos: [],
        topProducts: [],
        topOrders: [],
        topClients: [],
        topPartners: [],
        orcamentos: [],
        orders: [],
        clientes: [],
        searchCliente: '',
        searchProduct: '',
        searchOrcamento: '',
        filterOrcamentoStatus: 'Pendentes',
        currentTab: 'home',
        stats: { 
            produtos: 0, 
            orcamentos: 0, 
            parceiros: 0,
            clientes: 0,
            pedidos: 0,
            valor_vendas: 0
        },
        toasts: [],
        lastNotification: '',
        modal: null,
        loadingUpload: false,
        _pollingInterval: null,
        formProduct: { nome: '', valor_venda: '', valor_promocional: '', cod_produto: '', valor_custo: '', id_categoria: '', ativo: true, descricao: '', foto: '' },
        formCategory: { id_categoria: null, nome: '', icone: '', ativo: true },
        formAgendamento: { ativo: false, tempo: 0, pId: null, nome: '', currentYear: new Date().getFullYear(), currentMonth: new Date().getMonth(), days: [], agendamentosGerais: [], selectedDay: null, selectedDayAgendamentos: [] },
        formCliente: { id_cliente: null, nome: '', apelido: '', email: '', fone: '', cpf: '', obs: '', logradouro: '', num: '', complemento: '', bairro: '', cidade: '', estado: '', cep: '', perfil: 'Cliente', foto: '', senha: '' },
        formOrcamento: { numero_sequencial: '', cliente_id: '', dt_criado: new Date().toISOString().split('T')[0], validade_dias: 30, dt_inicio: '', dt_fim: '', observacoes: '', subtotal: 0, descontos: 0, valor_total: 0 },
        novoItemOrcamento: { produto_id: '', quantidade: 1, valor_unitario: 0, total: 0, qtd_prevista: '-' },
        orcamentoItens: [],
        productToDelete: null,
        clienteToDelete: null,
        orcamentoToDelete: null,
        orcamentoStatusApprove: null,
        novoStatusOrcamento: 'PENDENTE',
        categorias: [],
        formLoja: {
            id_loja: '', nome: '', cnpj: '', whatsapp: '', cor_tema: '#37c6da',
            descricao: '', instagram: '', facebook: '',
            cep: '', endereco: '', numero: '', complemento: '', bairro: '', cidade: '', estado: '',
            logo: '', url: ''
        },
        isSavingLoja: false,
        isSavingCliente: false,

        init() {
            console.log('Akipede Mais Premium (Fidelity Pass) inicializado');
            this.loadUserData();
            this.setupRealtime();
        },

        mountGooglePlaces(inputEl) {
            const tentarIniciar = () => {
                if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                    setTimeout(tentarIniciar, 500);
                    return;
                }
                try {
                    const autocomplete = new google.maps.places.Autocomplete(inputEl, {
                        types: ['address'],
                        componentRestrictions: { country: 'br' }
                    });
                    autocomplete.addListener('place_changed', () => {
                        const place = autocomplete.getPlace();
                        if (place && place.formatted_address) {
                            this.formCliente.logradouro = place.formatted_address;
                            inputEl.blur(); // Força o fechamento do dropdown do Google Maps
                            for (const component of place.address_components) {
                                const type = component.types[0];
                                if (type === 'administrative_area_level_2') this.formCliente.cidade = component.long_name;
                                if (type === 'administrative_area_level_1') this.formCliente.estado = component.short_name;
                                if (type === 'sublocality_level_1' || type === 'sublocality') this.formCliente.bairro = component.long_name;
                                if (type === 'postal_code') this.formCliente.cep = component.long_name;
                            }
                        }
                    });
                } catch (e) {
                    console.error("Erro Google Maps no input:", e);
                }
            };
            tentarIniciar();
        },

        mountGooglePlacesLoja(inputEl) {
            const tentarIniciar = () => {
                if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                    setTimeout(tentarIniciar, 500); return;
                }
                try {
                    const autocomplete = new google.maps.places.Autocomplete(inputEl, { types: ['address'], componentRestrictions: { country: 'br' } });
                    autocomplete.addListener('place_changed', () => {
                        const place = autocomplete.getPlace();
                        if (place && place.formatted_address) {
                            this.formLoja.endereco = place.formatted_address;
                            inputEl.blur();
                            for (const component of place.address_components) {
                                const type = component.types[0];
                                if (type === 'administrative_area_level_2') this.formLoja.cidade = component.long_name;
                                if (type === 'administrative_area_level_1') this.formLoja.estado = component.short_name;
                                if (type === 'sublocality_level_1' || type === 'sublocality') this.formLoja.bairro = component.long_name;
                                if (type === 'postal_code') this.formLoja.cep = component.long_name;
                            }
                        }
                    });
                } catch (e) { console.error("Erro Google Maps Loja:", e); }
            };
            tentarIniciar();
        },

        loadUserData() {
            // Obter dados do usuário injetados pelo PHP no window se necessário, 
            // ou buscar via API que lê a sessão
            fetch('api/auth/get_user.php')
                .then(res => res.json())
                .then(data => {
                    if (data.logged) {
                        this.user = data.user;
                        this.loadInitialData(); // Carrega dados após saber quem é o usuário
                        this.loadLoja();
                    } else {
                        window.location.href = 'login.php';
                    }
                });
        },

        loadLoja() {
            if (!this.user || !this.user.id) return;
            fetch('api/index.php/loja?user_id=' + this.user.id)
                .then(res => res.json())
                .then(data => {
                    if (data && data.id_loja) {
                        this.formLoja = { ...this.formLoja, ...data };
                        if (!this.formLoja.cor_tema) this.formLoja.cor_tema = '#37c6da';
                    }
                })
                .catch(err => console.error("Erro ao carregar loja", err));
        },

        saveLoja() {
            if (!this.formLoja.id_loja) return;
            this.isSavingLoja = true;
            fetch('api/index.php/loja?id=' + this.formLoja.id_loja, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.formLoja)
            })
            .then(res => res.json())
            .then(data => {
                this.showToast('Informações da loja atualizadas com sucesso!', 'success');
            })
            .catch(err => this.showToast('Erro ao atualizar a loja', 'error'))
            .finally(() => this.isSavingLoja = false);
        },

        getPageTitle() {
            const titles = {
                'home': 'Dashboard',
                'produtos': 'Gestão de Produtos',
                'usuarios': 'Gestão de Clientes',
                'orcamentos': 'Gestão de Orçamentos',
                'loja': 'Minha Loja',
                'perfil': 'Meu Perfil'
            };
            return titles[this.currentTab] || 'Akipede Mais';
        },

        formatMoney(value) {
            if (value === null || value === undefined) return 'R$ 0,00';
            const number = typeof value === 'string' ? parseFloat(value) : value;
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(number);
        },

        maskPhone(val) {
            if (!val) return '';
            let v = val.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            if (v.length > 10) {
                return v.replace(/^(\d{2})(\d)(\d{4})(\d{4}).*/, '($1) $2 $3-$4');
            } else if (v.length > 6) {
                return v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            } else if (v.length > 2) {
                return v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            } else if (v.length > 0) {
                return v.replace(/^(\d*)/, '($1');
            }
            return v;
        },

        showToast(message, type = 'success') {
            const id = Date.now();
            this.toasts.push({ id, message, type });
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 4000);
        },

        loadInitialData() {
            // Carregar Dados Consolidados do Dashboard
            fetch('api/index.php/dashboard-stats')
                .then(res => res.json())
                .then(data => {
                    this.topProducts = data.top_products || [];
                    this.topOrders = data.top_orders || [];
                    this.topClients = data.top_clients || [];
                    this.topPartners = data.top_partners || [];
                    this.stats = {
                        produtos: data.summary.produtos || 0,
                        orcamentos: data.summary.pedidos || 0,
                        parceiros: data.summary.parceiros || 0,
                        clientes: data.summary.clientes || 0,
                        pedidos: data.summary.pedidos || 0,
                        valor_vendas: data.summary.valor_vendas || 0
                    };
                });

            // Carregar Produtos (para as outras abas)
            fetch('api/index.php/produtos')
                .then(res => res.json())
                .then(data => {
                    this.produtos = data;
                });

            // Carregar Orçamentos
            fetch('api/index.php/orcamentos')
                .then(res => res.json())
                .then(data => {
                    this.orcamentos = data;
                });

            // Carregar Clientes/Usuários
            fetch('api/index.php/clientes')
                .then(res => res.json())
                .then(data => {
                    this.clientes = data;
                    this.stats.clientes = data.length;
                });

            // Carregar Categorias da loja logada
            const catUrl = 'api/index.php/categorias' + (this.user.loja_id ? '?loja_id=' + this.user.loja_id : '');
            fetch(catUrl)
                .then(res => res.json())
                .then(data => {
                    this.categorias = data;
                });
        },

        loadClientes() {
            fetch('api/index.php/clientes')
                .then(res => res.json())
                .then(data => {
                    this.clientes = data;
                    if (this.stats) this.stats.clientes = data.length;
                });
        },

        setupRealtime() {
            // Polling inteligente: busca novos orçamentos a cada 15s.
            // Isso evita o deadlock no PHP single-thread (php -S) que o SSE/EventSource causaria
            // por manter uma conexão permanente bloqueando a única thread disponível.
            // O stream.php continua disponível para produção com Apache (multi-thread).
            this.connected = true;
            this._pollingInterval = setInterval(() => {
                fetch('api/index.php/orcamentos')
                    .then(res => {
                        if (!res.ok) { this.connected = false; return null; }
                        this.connected = true;
                        return res.json();
                    })
                    .then(data => {
                        if (!data) return;
                        // Só atualiza se houver mudança no total de registros
                        if (data.length !== this.orcamentos.length) {
                            const novos = data.filter(d => !this.orcamentos.find(o => o.id_orcamento === d.id_orcamento));
                            if (novos.length > 0) {
                                novos.forEach(n => this.orcamentos.unshift(n));
                                this.stats.orcamentos = this.orcamentos.length;
                                this.lastNotification = `${novos.length} novo(s) orçamento(s) recebido(s)`;
                                console.log('[Realtime Poll]', this.lastNotification);
                            }
                        }
                    })
                    .catch(() => { this.connected = false; });
            }, 15000); // 15 segundos
        },

        openModal(name) {
            this.modal = name;
            if (name === 'novo-produto') {
                this.formProduct = { nome: '', valor_venda: '', valor_promocional: '', cod_produto: '', valor_custo: '', id_categoria: '', ativo: true, descricao: '', foto: '' };
            } else if (name === 'novo-categoria') {
                this.formCategory = { nome: '' };
            } else if (name === 'novo-cliente') {
                this.formCliente = { nome: '', email: '', perfil: 'Usuário', foto: '' };
            } else if (name === 'novo-orcamento') {
                // Calcular próximo número sequencial
                const nums = this.orcamentos.map(o => parseInt(o.numero_sequencial) || 0);
                const maxNum = nums.length > 0 ? Math.max(...nums) : 0;
                
                // Datas locais para o datetime-local
                const now = new Date();
                const offset = now.getTimezoneOffset() * 60000;
                const dt_inicio = (new Date(now.getTime() - offset)).toISOString().slice(0, 16);
                const dt_fim = (new Date(now.getTime() - offset + 3600000)).toISOString().slice(0, 16); // +1h

                this.formOrcamento = {
                    numero_sequencial: (maxNum + 1).toString(),
                    cliente_id: '',
                    parceiro_id: this.user.id || '',
                    parceiro_nome: this.user.nome || '',
                    dt_criado: new Date().toISOString().split('T')[0],
                    validade_dias: 30,
                    validade: '',
                    dt_inicio: dt_inicio,
                    dt_fim: dt_fim,
                    observacoes: '',
                    subtotal: 0,
                    descontos: 0,
                    descontos_display: 'R$ 0,00',
                    valor_total: 0
                };
                this.orcamentoItens = [];
                this.calculateValidade();
            }
        },

        calculateValidade() {
            if (!this.formOrcamento.dt_criado || !this.formOrcamento.validade_dias) return;
            const date = new Date(this.formOrcamento.dt_criado + 'T12:00:00'); // Evitar timezone shift
            date.setDate(date.getDate() + parseInt(this.formOrcamento.validade_dias));
            const iso = date.toISOString().split('T')[0];
            this.formOrcamento.validade = iso;
            this.formOrcamento.dt_validade = iso;
        },

        editProduct(p) { 
            const formatted = { ...p };
            // Formatar números para string PT-BR para que o input mostre com vírgula
            if (p.valor_venda !== null) formatted.valor_venda = parseFloat(p.valor_venda).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
            if (p.valor_promocional !== null) formatted.valor_promocional = parseFloat(p.valor_promocional).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
            if (p.valor_custo !== null) formatted.valor_custo = parseFloat(p.valor_custo).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
            
            // Garantir que a categoria esteja mapeada para o select
            formatted.id_categoria = p.categoria;

            this.formProduct = formatted; 
            this.modal = 'editar-produto'; 
        },
        editCliente(c) { this.formCliente = { ...c }; this.modal = 'editar-cliente'; },

        openAgendamento(p) {
            const now = new Date();
            this.formAgendamento = {
                ativo: parseFloat(p.agendamento) > 0,
                tempo: parseFloat(p.agendamento) > 0 ? parseFloat(p.agendamento) : 0,
                pId: p.id_produto,
                nome: p.nome,
                currentYear: now.getFullYear(),
                currentMonth: now.getMonth(),
                days: [],
                agendamentosGerais: [],
                selectedDay: null,
                selectedDayAgendamentos: []
            };
            this.generateCalendar(); // Renderiza vazio sem bug do reativo imediatamente
            this.loadAgendaProduto(p.id_produto);
            this.modal = 'agendamento';
        },

        loadAgendaProduto(produto_id) {
            fetch(`api/index.php/agenda?produto_id=${produto_id}`)
                .then(res => res.json())
                .then(data => {
                    this.formAgendamento.agendamentosGerais = data;
                    this.generateCalendar(); // Atualiza re-adicionando seções preenchidas
                })
                .catch(err => console.error("Erro ao carregar agendamentos:", err));
        },

        generateCalendar() {
            const year = this.formAgendamento.currentYear;
            const month = this.formAgendamento.currentMonth;
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            
            let days = [];
            // Preencher dias vazios do início (base zero - domingo a sábado)
            for (let i = 0; i < firstDay; i++) {
                days.push({ day: null, dateStr: null, hasAgendamento: false });
            }
            
            // Preencher os dias do mês
            for (let i = 1; i <= daysInMonth; i++) {
                // Monta string YYYY-MM-DD segura para o fuso local
                const currStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                
                // Checa se tem algum agendamento pro dia e soma as quantidades
                let hasAg = false;
                let totalQtd = 0;
                
                this.formAgendamento.agendamentosGerais.forEach(ag => {
                    if (ag.data_inicio && ag.data_inicio.substring(0, 10) === currStr) {
                        hasAg = true;
                        totalQtd += parseInt(ag.quantidade || 1);
                    }
                });

                days.push({ day: i, dateStr: currStr, hasAgendamento: hasAg, totalQtd: totalQtd });
            }
            
            // Completar última semana se precisar
            const remainder = days.length % 7;
            if (remainder !== 0) {
                const addObj = 7 - remainder;
                for (let i = 0; i < addObj; i++) {
                    days.push({ day: null, dateStr: null, hasAgendamento: false });
                }
            }
            
            this.formAgendamento.days = days;
            this.formAgendamento.selectedDay = null;
            this.formAgendamento.selectedDayAgendamentos = [];
        },

        prevMonthCalendar() {
            let m = this.formAgendamento.currentMonth - 1;
            let y = this.formAgendamento.currentYear;
            if (m < 0) { m = 11; y--; }
            this.formAgendamento.currentMonth = m;
            this.formAgendamento.currentYear = y;
            this.generateCalendar();
        },

        nextMonthCalendar() {
            let m = this.formAgendamento.currentMonth + 1;
            let y = this.formAgendamento.currentYear;
            if (m > 11) { m = 0; y++; }
            this.formAgendamento.currentMonth = m;
            this.formAgendamento.currentYear = y;
            this.generateCalendar();
        },

        selectCalendarDay(dayObj) {
            if(!dayObj.day) return;
            this.formAgendamento.selectedDay = dayObj.dateStr;
            this.formAgendamento.selectedDayAgendamentos = this.formAgendamento.agendamentosGerais.filter(ag => {
                if(!ag.data_inicio) return false;
                return ag.data_inicio.substring(0, 10) === dayObj.dateStr;
            });
        },

        getCalendarMonthName() {
            const m = this.formAgendamento.currentMonth;
            const y = this.formAgendamento.currentYear;
            const months = ['janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'];
            return `${months[m]} de ${y}`;
        },

        formatDateTimeRange(dt) {
            if(!dt) return '';
            const dateObj = new Date(dt);
            // Corrige se timezone for problemático mostrando hora certa do banco
            // Como postgres pode retornar "YYYY-MM-DD HH:MM:SS"
            const strPart = dt.split(' ');
            if (strPart.length === 2) {
                const ds = strPart[0].split('-');
                const ts = strPart[1].split(':');
                return `${parseInt(ds[2])}/${parseInt(ds[1])}/${ds[0]} ${ts[0]}:${ts[1]}`;
            }
            return dateObj.toLocaleString('pt-BR').substring(0, 16);
        },

        saveAgendamento() {
            // Este método de "save configuração de tempo" no produto foi substituído pelo novo modal. 
            // O design novo foca na agenda do produto e não de configurar o switch. Se for o caso, pode ser adaptado.
            this.modal = null;
        },

        toggleProductStatus(p) {
            p.ativo = !p.ativo; // Inverte localmente
            
            fetch('api/index.php/produtos?id=' + p.id_produto, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(p)
            })
            .then(res => res.json())
            .then(data => {
                const acao = p.ativo ? 'ativado' : 'desativado';
                this.showToast(`Produto "${p.nome}" ${acao} com sucesso!`);
            })
            .catch(err => {
                p.ativo = !p.ativo; // Reverte em caso de erro
                this.showToast('Erro ao atualizar status', 'error');
            });
        },

        saveProduct() {
            const method = this.modal === 'editar-produto' ? 'PUT' : 'POST';
            const endpoint = 'api/index.php/produtos' + (method === 'PUT' ? '?id=' + this.formProduct.id_produto : '');
            
            // Mapear campos e limpar dados para o banco
            const payload = { ...this.formProduct };
            payload.categoria = this.formProduct.id_categoria || null;
            payload.loja = this.user.loja_id || this.user.loja;

            // Limpar valores monetários (remover R$, pontos e trocar vírgula por ponto)
            const cleanPrice = (val) => {
                if (!val) return null;
                if (typeof val === 'number') return val;
                return parseFloat(val.toString().replace(/[R$\.\s]/g, '').replace(',', '.'));
            };

            payload.valor_venda = cleanPrice(payload.valor_venda) || 0;
            payload.valor_promocional = cleanPrice(payload.valor_promocional);
            payload.valor_custo = cleanPrice(payload.valor_custo);

            fetch(endpoint, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    this.showToast('Erro ao salvar produto: ' + data.error, 'error');
                } else {
                    const msg = method === 'POST' ? `Produto "${payload.nome}" cadastrado com sucesso!` : `Produto "${payload.nome}" atualizado com sucesso!`;
                    this.showToast(msg);
                    this.modal = null;
                    this.loadInitialData();
                }
            })
            .catch(err => this.showToast('Erro na conexão com o servidor', 'error'));
        },

        saveCategory() {
            if (!this.formCategory.nome) return alert('Nome da categoria é obrigatório');
            
            const payload = {
                nome: this.formCategory.nome,
                loja: this.user.loja_id || this.user.loja || '35f29d20-0097-4d7a-b286-9a25b3952f9c'
            };

            fetch('api/index.php/categorias', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(data => {
                this.showToast(`Categoria "${payload.nome}" cadastrada com sucesso!`);
                this.modal = 'novo-produto'; // Volta para o modal de produto
                this.loadInitialData(); // Recarrega categorias
            })
            .catch(err => this.showToast('Erro ao salvar categoria', 'error'));
        },

        saveCliente() {
            const u = this.formCliente;
            if (!u.nome || !u.cpf || !u.email || !u.fone || !u.perfil || (this.modal === 'novo-cliente' && !u.senha)) {
                this.showToast('Por favor, preencha todos os campos obrigatórios (Nome, CPF, E-mail, Celular, Senha e Perfil).', 'error');
                return;
            }

            if (this.formCliente.senha && this.formCliente.senha !== this.formCliente.senha_confirma) {
                this.showToast('As senhas não coincidem!', 'error');
                return;
            }

            this.isSavingCliente = true;

            const method = this.modal === 'editar-cliente' ? 'PUT' : 'POST';
            const endpoint = 'api/index.php/clientes' + (method === 'PUT' ? '?id=' + this.formCliente.id_cliente : '');
            
            const payload = { ...this.formCliente };
            payload.loja = this.user.loja_id || this.user.loja;
            payload.cpf_cnpj = this.formCliente.cpf; // Mapeia para o nome esperado no DB

            // Lógica de Apelido Automático (Primeiro + Último Nome)
            if (payload.nome) {
                const names = payload.nome.trim().split(/\s+/);
                if (names.length > 1) {
                    payload.apelido = `${names[0]} ${names[names.length - 1]}`;
                } else {
                    payload.apelido = names[0];
                }
            }

            fetch(endpoint, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    this.showToast('Erro ao salvar usuário: ' + data.error, 'error');
                } else {
                    const msg = method === 'POST' ? 'Tudo realizado com sucesso! Dados gravados e mensagens enviadas.' : `Usuário "${this.formCliente.nome}" atualizado com sucesso!`;
                    this.showToast(msg, 'success');
                    this.modal = null;
                    this.loadClientes();
                }
            })
            .catch(err => this.showToast('Erro ao salvar usuário', 'error'))
            .finally(() => {
                this.isSavingCliente = false;
            });
        },

        deleteCliente(c) {
            this.clienteToDelete = { ...c };
            this.modal = 'excluir-cliente';
        },

        confirmDeleteCliente() {
            const c = this.clienteToDelete;
            fetch('api/index.php/clientes?id=' + c.id_cliente, { method: 'DELETE' })
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        this.showToast('Erro ao excluir cliente: ' + data.error, 'error');
                    } else {
                        this.showToast(`Cliente "${c.nome}" excluído com sucesso!`);
                        this.modal = null;
                        this.loadClientes();
                    }
                })
                .catch(err => this.showToast(`Erro ao excluir cliente "${c.nome}"`, 'error'));
        },

        deleteProduct(p) {
            this.productToDelete = { ...p };
            this.modal = 'excluir-produto';
        },

        confirmDeleteProduct() {
            const p = this.productToDelete;
            fetch('api/index.php/produtos?id=' + p.id_produto, { method: 'DELETE' })
                .then(res => res.json())
                .then(data => {
                    if (data.type === 'deactivated') {
                        this.showToast(`Produto "${p.nome}" vinculado a orçamentos. Foi desativado em vez de excluído.`, 'warning');
                    } else {
                        this.showToast(`Produto "${p.nome}" excluído definitivamente!`);
                    }
                    this.modal = null;
                    this.loadInitialData();
                })
                .catch(err => {
                    console.error(err);
                    this.showToast('Erro ao excluir produto', 'error');
                });
        },

        deleteOrcamento(o) {
            this.orcamentoToDelete = o;
            this.modal = 'excluir-orcamento';
        },

        confirmDeleteOrcamento() {
            const o = this.orcamentoToDelete;
            if (!o) return;
            fetch('api/index.php/orcamentos?id=' + o.id_orcamento, { method: 'DELETE' })
                .then(res => res.json())
                .then(data => {
                    this.showToast(`Orçamento #${o.numero_sequencial || o.id_orcamento} excluído com sucesso!`);
                    this.modal = null;
                    this.orcamentoToDelete = null;
                    this.loadOrcamentos();
                })
                .catch(err => this.showToast(`Erro ao excluir orçamento #${o.numero_sequencial || o.id_orcamento}`, 'error'));
        },

        approveOrcamento(o) {
            this.orcamentoStatusApprove = o;
            this.novoStatusOrcamento = o.status || 'PENDENTE';
            this.modal = 'atualizar-status-orcamento';
        },

        confirmUpdateStatusOrcamento() {
            if (!this.orcamentoStatusApprove) return;
            fetch(`api/index.php/orcamentos?id=${this.orcamentoStatusApprove.id_orcamento}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: this.novoStatusOrcamento })
            })
            .then(res => res.json())
            .then(data => {
                this.showToast(`Status do orçamento #${this.orcamentoStatusApprove.numero_sequencial || this.orcamentoStatusApprove.id_orcamento} atualizado para ${this.novoStatusOrcamento}!`, 'success');
                this.modal = null;
                this.loadOrcamentos();
            })
            .catch(err => this.showToast(`Erro ao atualizar status`, 'error'));
        },

        editOrcamento(o) {
            this.formOrcamento = {
                id_orcamento: o.id_orcamento,
                numero_sequencial: o.numero_sequencial,
                cliente_id: o.cliente_id,
                parceiro_id: o.parceiro,
                parceiro_nome: o.parceiro_nome,
                dt_criado: o.dt_criado ? o.dt_criado.split(' ')[0] : '',
                validade_dias: 30, // Defaul, will calculate date
                dt_inicio: o.data_inicio ? o.data_inicio.slice(0, 16) : '',
                dt_fim: o.data_fim ? o.data_fim.slice(0, 16) : '',
                observacoes: o.observacoes || '',
                subtotal: o.valor_total || 0,
                descontos: o.desconto || 0,
                valor_total: o.valor_total || 0
            };
            this.calculateValidade();
            
            fetch(`api/index.php/orcamentos_itens?orcamento_id=${o.id_orcamento}`)
            .then(res => res.json())
            .then(data => {
                this.orcamentoItens = data.map(i => ({
                    produto_id: i.produto,
                    nome_produto: i.nome_produto || 'Produto ' + i.produto,
                    quantidade: i.quantidade,
                    valor_unitario: i.valor_unitario,
                    total: i.valor_total,
                    qtd_prevista: '-'
                }));
                this.modal = 'novo-orcamento';
            });
        },

        async uploadFile(event, type, formTarget) {
            const file = event.target.files[0];
            if (!file) return;

            this.loadingUpload = true;
            
            try {
                // Otimizar imagem: 500px é a medida ideal para web (performance vs qualidade)
                const optimizedFile = await this.resizeImage(file, 500, 500, 0.7);
                
                const formData = new FormData();
                formData.append('file', optimizedFile, file.name.replace(/\.[^/.]+$/, "") + ".jpg");
                formData.append('type', type);

                const res = await fetch('api/utils/upload.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    this[formTarget].foto = data.path;
                    this.showToast('Imagem enviada com sucesso!');
                } else {
                    this.showToast('Erro no upload: ' + (data.error || 'Erro desconhecido'), 'error');
                }
            } catch (err) {
                console.error('Erro no processamento da imagem:', err);
                this.showToast('Erro ao processar imagem para upload', 'error');
            } finally {
                this.loadingUpload = false;
            }
        },

        resizeImage(file, maxWidth, maxHeight, quality) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > maxWidth) {
                                height *= maxWidth / width;
                                width = maxWidth;
                            }
                        } else {
                            if (height > maxHeight) {
                                width *= maxHeight / height;
                                height = maxHeight;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            resolve(blob);
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = reject;
                    img.src = e.target.result;
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        },

        filteredClientes() {
            if (!this.searchCliente) return this.clientes;
            const s = this.searchCliente.toLowerCase();
            return this.clientes.filter(c => c.nome.toLowerCase().includes(s));
        },

        filteredProducts() {
            if (!this.searchProduct) return this.produtos;
            const s = this.searchProduct.toLowerCase();
            return this.produtos.filter(p => p.nome.toLowerCase().includes(s));
        },

        filteredOrcamentos() {
            let res = this.orcamentos;
            
            // Mapa de abas (plural) para status do banco (uppercase)
            const statusMap = {
                'Pendentes': 'PENDENTE',
                'Aprovados': 'APROVADO',
                'Cancelados': 'CANCELADO'
            };
            
            // Filtro por status (Abas)
            if (this.filterOrcamentoStatus && statusMap[this.filterOrcamentoStatus]) {
                const target = statusMap[this.filterOrcamentoStatus];
                res = res.filter(o => (o.status || '').toUpperCase() === target);
            }
            
            // Filtro por texto
            if (this.searchOrcamento) {
                const s = this.searchOrcamento.toLowerCase();
                res = res.filter(o => (o.cliente_nome || '').toLowerCase().includes(s) || (o.numero_sequencial && String(o.numero_sequencial).includes(s)));
            }
            
            return res;
        },

        updateNovoItemValor() {
            // Use == para comparar IDs UUID vs string de forma flexível
            const p = this.produtos.find(prod => prod.id_produto == this.novoItemOrcamento.produto_id);
            if (p) {
                this.novoItemOrcamento.valor_unitario = parseFloat(p.valor_venda) || 0;
                // O limite agora é baseado na QTD PREVISTA (asynchronous)
                this.fetchDisponibilidade();
            } else {
                this.novoItemOrcamento.valor_unitario = 0;
                this.novoItemOrcamento.qtd_prevista = '-';
            }
        },

        async fetchDisponibilidade() {
            const produto_id = this.novoItemOrcamento.produto_id;
            const dt_inicio = this.formOrcamento.dt_inicio;
            const dt_fim = this.formOrcamento.dt_fim;
            
            // Só busca se todos os dados estiverem preenchidos
            if (!produto_id || !dt_inicio || !dt_fim) {
                this.novoItemOrcamento.qtd_prevista = '-';
                return;
            }

            try {
                // Usando o endpoint registrado no api/index.php
                const res = await fetch(`api/index.php/disponibilidade?produto_id=${produto_id}&inicio=${dt_inicio}&fim=${dt_fim}`);
                const data = await res.json();
                
                if (data.status === 'success') {
                    // Garante que o valor seja convertido para string para exibição correta e sempre positivo conforme solicitado
                    this.novoItemOrcamento.qtd_prevista = Math.abs(data.qtd_prevista).toString();
                } else {
                    this.novoItemOrcamento.qtd_prevista = '0';
                }
            } catch (err) {
                console.error('Erro ao buscar disponibilidade:', err);
                this.novoItemOrcamento.qtd_prevista = 'Erro';
            }
        },

        applyDiscountMask(value) {
            let cleanValue = value.replace(/\D/g, '');
            let numberValue = parseFloat(cleanValue) / 100 || 0;
            this.formOrcamento.descontos = numberValue;
            this.formOrcamento.descontos_display = this.formatMoney(numberValue);
            this.calculateOrcamentoTotal();
        },

        addItemOrcamento() {
            if (!this.novoItemOrcamento.produto_id || this.novoItemOrcamento.quantidade < 1) {
                alert('Selecione um produto e a quantidade.');
                return;
            }
            const p = this.produtos.find(prod => prod.id_produto == this.novoItemOrcamento.produto_id);
            if (!p) return;

            const limit = parseInt(this.novoItemOrcamento.qtd_prevista);
            if (!isNaN(limit) && this.novoItemOrcamento.quantidade > limit) {
                alert('Quantidade excede a disponibilidade prevista (' + limit + ').');
                return;
            }
            
            const existingItem = this.orcamentoItens.find(item => item.produto_id == p.id_produto);
            if (existingItem) {
                existingItem.quantidade += this.novoItemOrcamento.quantidade;
            } else {
                this.orcamentoItens.push({
                    produto_id: p.id_produto,
                    nome_produto: p.nome,
                    quantidade: this.novoItemOrcamento.quantidade,
                    valor_unitario: this.novoItemOrcamento.valor_unitario
                });
            }
            
            this.novoItemOrcamento = { produto_id: '', quantidade: 1, valor_unitario: 0, total: 0, qtd_prevista: '-' };
            this.calculateOrcamentoTotal();
        },

        removeItemOrcamento(index) {
            this.orcamentoItens.splice(index, 1);
            this.calculateOrcamentoTotal();
        },

        calculateOrcamentoTotal() {
            this.formOrcamento.subtotal = this.orcamentoItens.reduce((acc, item) => acc + (item.quantidade * item.valor_unitario), 0);
            const desc = parseFloat(this.formOrcamento.descontos) || 0;
            this.formOrcamento.valor_total = this.formOrcamento.subtotal - desc;
        },

        async saveOrcamento() {
            if (!this.formOrcamento.cliente_id) {
                alert('Selecione o cliente.');
                return;
            }
            if (this.orcamentoItens.length === 0) {
                alert('Adicione pelo menos um item.');
                return;
            }
            
            const selectedClient = this.clientes.find(c => c.id_cliente === this.formOrcamento.cliente_id);
            const payload = {
                numero_sequencial: parseInt(this.formOrcamento.numero_sequencial),
                parceiro: this.formOrcamento.parceiro_id,
                cliente_nome: this.formOrcamento.cliente_id,
                cliente_cpf_cnpj: selectedClient ? selectedClient.cpf_cnpj : '',
                cliente_fone: selectedClient ? selectedClient.fone : '',
                loja: this.user.loja_id,
                status: 'PENDENTE',
                validade: this.formOrcamento.dt_validade,
                valor_total: this.formOrcamento.valor_total,
                observacoes: this.formOrcamento.observacoes,
                desconto: parseFloat(this.formOrcamento.descontos) || 0,
                mes: this.formOrcamento.dt_criado.split('-')[1],
                ano: this.formOrcamento.dt_criado.split('-')[0],
                data_orcamento: this.formOrcamento.dt_criado,
                data_inicio: this.formOrcamento.dt_inicio,
                data_fim: this.formOrcamento.dt_fim,
                itens: this.orcamentoItens.map(i => ({ 
                    produto_id: i.produto_id, 
                    quantidade: i.quantidade, 
                    valor_unitario: i.valor_unitario 
                }))
            };
            
            const isEditing = !!this.formOrcamento.id_orcamento;
            const method = isEditing ? 'PUT' : 'POST';
            const endpoint = isEditing ? `api/index.php/orcamentos?id=${this.formOrcamento.id_orcamento}` : 'api/index.php/orcamentos';
            
            try {
                const res = await fetch(endpoint, {
                    method: method,
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.id || data.message === "Orçamento atualizado") {
                    this.showToast(isEditing ? 'Orçamento atualizado com sucesso!' : 'Orçamento criado com sucesso!');
                    this.modal = null;
                    await this.loadInitialData(); // Agora usando await
                    // Forçar atualização do array orcamentos para garantir UI
                    const resO = await fetch('api/index.php/orcamentos');
                    this.orcamentos = await resO.json();
                    this.orcamentoItens = [];
                    this.formOrcamento = { numero_sequencial: '', cliente_id: '', dt_criado: new Date().toISOString().split('T')[0], validade_dias: 30, dt_inicio: '', dt_fim: '', observacoes: '', subtotal: 0, descontos: 0, valor_total: 0 };
                } else {
                    this.showToast('Erro ao criar orçamento: ' + (data.message || ''), 'error');
                }
            } catch (err) {
                console.error(err);
                alert('Erro na requisição');
            }
        },

        formatDate(dateStr) {
            if (!dateStr) return null;
            return new Date(dateStr).toLocaleDateString('pt-BR');
        },

        getClientName(idOrName) {
            if (!idOrName) return '-';
            // Se parecer com um UUID (tem hifens ou mais de 30 chars), tenta buscar
            if (idOrName.length > 30 || idOrName.includes('-')) {
                const c = this.clientes.find(cli => cli.id_cliente === idOrName);
                if (c) return c.nome;
            }
            return idOrName;
        },

        logout() {
            if (this._pollingInterval) clearInterval(this._pollingInterval);
            window.location.href = 'api/auth/logout.php';
        }
    }
}
