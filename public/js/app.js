// public/js/app.js
function app() {
    return {
        user: {},
        products: [],
        topProducts: [
            { id: 1, nome: 'Temaki', categoria_nome: 'Entrada', total_pedidos: 2, total_vendidos: 8, valor_total: '40,00', foto: 'https://img.freepik.com/fotos-premium/temaki-sushi-em-forma-de-cone-recheado-com-salmao-e-arroz_158001-2481.jpg' },
            { id: 2, nome: 'Niguiri', categoria_nome: 'Nova', total_pedidos: 1, total_vendidos: 6, valor_total: '180,00', foto: 'https://img.itdg.com.br/tdg/images/recipes/000/011/327/324102/324102_original.jpg' },
            { id: 3, nome: 'Hot', categoria_nome: 'Entrada', total_pedidos: 1, total_vendidos: 3, valor_total: '38,97', foto: 'https://p2.trrsf.com/image/fget/cf/1200/675/middle/images.terra.com/2023/04/18/163820986-istock-1205168449.jpg' }
        ],
        orcamentos: [],
        orders: [],
        clientes: [],
        searchCliente: '',
        searchProduct: '',
        searchOrcamento: '',
        currentTab: 'home',
        stats: { 
            produtos: 0, 
            orcamentos: 3, 
            pedidos: 2, 
            parceiros: 1, 
            clientes: 5,
            valor_vendas: '20.165,00' 
        },
        connected: false,
        lastNotification: '',
        modal: null,
        loadingUpload: false,
        _pollingInterval: null,
        formProduct: { nome: '', valor_venda: '', valor_custo: '', id_categoria: '', ativo: true, descricao: '', foto: '' },
        formAgendamento: { ativo: false, tempo: 0, pId: null },
        formCliente: { nome: '', email: '', perfil: 'Usuário', foto: '' },
        categorias: [],
        
        init() {
            console.log('Akipede Mais Premium (Fidelity Pass) inicializado');
            this.loadUserData();
            this.loadInitialData();
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
                    } else {
                        window.location.href = 'login.php';
                    }
                });
        },

        getPageTitle() {
            const titles = {
                'home': 'Dashboard',
                'produtos': 'Produtos',
                'orcamentos': 'Orçamento',
                'usuarios': 'Usuário',
                'clientes': 'Clientes',
                'loja': 'Minha Loja',
                'perfil': 'Perfil'
            };
            return titles[this.currentTab] || 'Akipede Mais';
        },

        loadInitialData() {
            // Carregar Produtos
            fetch('api/index.php/produtos')
                .then(res => res.json())
                .then(data => {
                    this.products = data;
                    this.stats.produtos = data.length;
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

            // Carregar Categorias
            fetch('api/index.php/categorias')
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
                this.formProduct = { nome: '', valor_venda: '', valor_custo: '', id_categoria: '', ativo: true, descricao: '' };
            } else if (name === 'novo-cliente') {
                this.formCliente = { nome: '', email: '', perfil: 'Usuário', foto: '' };
            }
        },

        editProduct(p) { this.formProduct = { ...p }; this.modal = 'editar-produto'; },
        editCliente(c) { this.formCliente = { ...c }; this.modal = 'editar-cliente'; },

        openAgendamento(p) {
            this.formAgendamento = {
                ativo: parseFloat(p.agendamento) > 0,
                tempo: parseFloat(p.agendamento) > 0 ? parseFloat(p.agendamento) : 0,
                pId: p.id_produto
            };
            this.modal = 'agendamento';
        },

        saveAgendamento() {
            const p = this.products.find(prod => prod.id_produto === this.formAgendamento.pId);
            if (p) {
                p.agendamento = this.formAgendamento.ativo ? this.formAgendamento.tempo : 0;
                
                // Salvando no banco de dados
                fetch('api/index.php/produtos?id=' + p.id_produto, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(p)
                })
                .then(res => res.json())
                .then(data => console.log('Agendamento Atualizado', data))
                .catch(err => alert('Erro ao atualizar agendamento no servidor'));
            }
            this.modal = null;
        },

        saveProduct() {
            const method = this.modal === 'editar-produto' ? 'PUT' : 'POST';
            const endpoint = 'api/index.php/produtos' + (method === 'PUT' ? '?id=' + this.formProduct.id_produto : '');
            
            // Garantir que a loja esteja setada (exemplo fixo por enquanto)
            if (!this.formProduct.loja) this.formProduct.loja = '35f29d20-0097-4d7a-b286-9a25b3952f9c';

            fetch(endpoint, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.formProduct)
            })
            .then(res => res.json())
            .then(data => {
                console.log('Produto salvo:', data);
                this.modal = null;
                this.loadInitialData(); // Recarregar lista
            })
            .catch(err => alert('Erro ao salvar produto'));
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
                console.log('Cliente salvo:', data);
                this.modal = null;
                this.loadInitialData();
            })
            .catch(err => alert('Erro ao salvar cliente'));
        },

        uploadFile(event, type, formTarget) {
            const file = event.target.files[0];
            if (!file) return;

            this.loadingUpload = true;
            const formData = new FormData();
            formData.append('file', file);
            formData.append('type', type);

            fetch('api/utils/upload.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this[formTarget].foto = data.path;
                    console.log('Upload concluído:', data.path);
                } else {
                    alert('Erro no upload: ' + (data.error || 'Erro desconhecido'));
                }
            })
            .catch(err => alert('Erro na conexão com o servidor'))
            .finally(() => {
                this.loadingUpload = false;
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
            if (!this.searchOrcamento) return this.orcamentos;
            const s = this.searchOrcamento.toLowerCase();
            return this.orcamentos.filter(o => o.cliente_nome.toLowerCase().includes(s));
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
