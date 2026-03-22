// public/js/app.js
function app() {
    return {
        user: {},
        products: [],
        topProducts: [],
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
            orcamentos: 3, 
            parceiros: 0,
            clientes: 0,
            pedidos: 0,
            valor_vendas: '0,00'
        },
        toasts: [],
        lastNotification: '',
        productToDelete: {},
        modal: null,
        loadingUpload: false,
        _pollingInterval: null,
        formProduct: { nome: '', valor_venda: '', valor_promocional: '', cod_produto: '', valor_custo: '', id_categoria: '', ativo: true, descricao: '', foto: '' },
        formCategory: { id_categoria: null, nome: '', icone: '', ativo: true },
        formAgendamento: { ativo: false, tempo: 0, pId: null, nome: '', currentYear: new Date().getFullYear(), currentMonth: new Date().getMonth(), days: [], agendamentosGerais: [], selectedDay: null, selectedDayAgendamentos: [] },
        formCliente: { id_cliente: null, nome: '', apelido: '', email: '', fone: '', cpf: '', perfil: 'Usuário', foto: '' },
        formOrcamento: { numero_sequencial: '', cliente_id: '', dt_criado: new Date().toISOString().split('T')[0], validade_dias: 30, dt_inicio: '', dt_fim: '', observacoes: '', subtotal: 0, descontos: 0, valor_total: 0 },
        novoItemOrcamento: { produto_id: '', quantidade: 1, valor_unit: 0, total: 0 },
        orcamentoItens: [],
        productToDelete: null,
        clienteToDelete: null,
        orcamentoToDelete: null,
        categorias: [],
        
        init() {
            console.log('Akipede Mais Premium (Fidelity Pass) inicializado');
            this.loadUserData();
            this.setupRealtime();
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
                    } else {
                        window.location.href = 'login.php';
                    }
                });
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

        showToast(message, type = 'success') {
            const id = Date.now();
            this.toasts.push({ id, message, type });
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 4000);
        },

        loadInitialData() {
            // Carregar Produtos
            fetch('api/index.php/produtos')
                .then(res => res.json())
                .then(data => {
                    this.products = data;
                    this.stats.produtos = data.length;
                    
                    // Dashboard: Popular Top 10 com produtos reais
                    // Como não temos tabela de vendas consolidada ainda, mostramos os últimos cadastrados
                    this.topProducts = [...data]
                        .sort((a, b) => b.dt_criado ? b.dt_criado.localeCompare(a.dt_criado) : 0)
                        .slice(0, 10)
                        .map(p => ({
                            ...p,
                            id: p.id_produto,
                            total_pedidos: Math.floor(Math.random() * 5), // Dummy para visual
                            total_vendidos: Math.floor(Math.random() * 20), // Dummy para visual
                            valor_total: p.valor_venda // Dummy para visual
                        }));
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
            }
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
            const method = this.modal === 'editar-cliente' ? 'PUT' : 'POST';
            const endpoint = 'api/index.php/clientes' + (method === 'PUT' ? '?id=' + this.formCliente.id_cliente : '');
            
            fetch(endpoint, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.formCliente)
            })
            .then(res => res.json())
            .then(data => {
                const msg = method === 'POST' ? `Cliente "${this.formCliente.nome}" cadastrado com sucesso!` : `Cliente "${this.formCliente.nome}" atualizado com sucesso!`;
                this.showToast(msg);
                this.modal = null;
                this.loadInitialData();
            })
            .catch(err => this.showToast('Erro ao salvar cliente', 'error'));
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

        deleteCliente(c) {
            if (!confirm(`Deseja realmente excluir o usuário/cliente "${c.nome}"?`)) return;
            fetch('api/index.php/clientes?id=' + c.id_cliente, { method: 'DELETE' })
                .then(res => res.json())
                .then(data => {
                    this.showToast(`Cliente "${c.nome}" excluído com sucesso!`);
                    this.loadInitialData();
                })
                .catch(err => this.showToast(`Erro ao excluir cliente "${c.nome}"`, 'error'));
        },

        deleteOrcamento(o) {
            if (!confirm(`Deseja realmente excluir o orçamento #${o.numero_sequencial}?`)) return;
            // Endpoint fictício pois orçamentos são sensíveis, mas seguindo o padrão
            fetch('api/index.php/orcamentos?id=' + o.id_orcamento, { method: 'DELETE' })
                .then(res => res.json())
                .then(data => {
                    this.showToast(`Orçamento #${o.numero_sequencial} excluído com sucesso!`);
                    this.loadInitialData();
                })
                .catch(err => this.showToast(`Erro ao excluir orçamento #${o.numero_sequencial}`, 'error'));
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
            if (!this.searchProduct) return this.products;
            const s = this.searchProduct.toLowerCase();
            return this.products.filter(p => p.nome.toLowerCase().includes(s));
        },

        filteredOrcamentos() {
            let res = this.orcamentos;
            
            // Filtro por status (Abas)
            if (this.filterOrcamentoStatus) {
                res = res.filter(o => o.status === this.filterOrcamentoStatus || (this.filterOrcamentoStatus === 'Pendentes' && o.status === 'Pendente'));
            }
            
            // Filtro por texto
            if (this.searchOrcamento) {
                const s = this.searchOrcamento.toLowerCase();
                res = res.filter(o => o.cliente_nome.toLowerCase().includes(s) || (o.numero_sequencial && String(o.numero_sequencial).includes(s)));
            }
            
            return res;
        },

        updateNovoItemValor() {
            if (this.novoItemOrcamento.produto_id) {
                const p = this.produtos.find(prod => prod.id_produto == this.novoItemOrcamento.produto_id);
                if (p) {
                    this.novoItemOrcamento.valor_unit = parseFloat(p.valor_venda) || 0;
                }
            } else {
                this.novoItemOrcamento.valor_unit = 0;
            }
        },

        addItemOrcamento() {
            if (!this.novoItemOrcamento.produto_id || this.novoItemOrcamento.quantidade < 1) {
                alert('Selecione um produto e a quantidade.');
                return;
            }
            const p = this.produtos.find(prod => prod.id_produto == this.novoItemOrcamento.produto_id);
            if (!p) return;
            
            this.orcamentoItens.push({
                produto_id: p.id_produto,
                nome_produto: p.nome,
                quantidade: this.novoItemOrcamento.quantidade,
                valor_unitario: this.novoItemOrcamento.valor_unit
            });
            
            this.novoItemOrcamento = { produto_id: '', quantidade: 1, valor_unit: 0, total: 0 };
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
            
            const payload = {
                action: 'create',
                numero_sequencial: this.formOrcamento.numero_sequencial || '1',
                cliente_id: this.formOrcamento.cliente_id,
                dt_criado: this.formOrcamento.dt_criado,
                validade_dias: this.formOrcamento.validade_dias,
                dt_inicio: this.formOrcamento.dt_inicio || null,
                dt_fim: this.formOrcamento.dt_fim || null,
                observacoes: this.formOrcamento.observacoes,
                subtotal: this.formOrcamento.subtotal,
                descontos: this.formOrcamento.descontos || 0,
                valor_total: this.formOrcamento.valor_total,
                itens: this.orcamentoItens.map(i => ({ produto_id: i.produto_id, quantidade: i.quantidade, valor_unitario: i.valor_unitario }))
            };
            
            try {
                const res = await fetch('api/index.php/orcamentos', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.status === 'success') {
                    this.modal = null;
                    await this.loadOrcamentos();
                    this.orcamentoItens = [];
                    this.formOrcamento = { numero_sequencial: '', cliente_id: '', dt_criado: new Date().toISOString().split('T')[0], validade_dias: 30, dt_inicio: '', dt_fim: '', observacoes: '', subtotal: 0, descontos: 0, valor_total: 0 };
                } else {
                    alert('Erro ao criar orçamento: ' + (data.message || ''));
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

        logout() {
            if (this._pollingInterval) clearInterval(this._pollingInterval);
            window.location.href = 'api/auth/logout.php';
        }
    }
}
