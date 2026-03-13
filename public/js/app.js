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
        formCategory: { nome: '' },
        formAgendamento: { ativo: false, tempo: 0, pId: null },
        formCliente: { nome: '', email: '', perfil: 'Usuário', foto: '' },
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
                .then(data => {
                    this.showToast(`Agendamento de "${p.nome}" atualizado!`);
                })
                .catch(err => this.showToast(`Erro ao atualizar agendamento de "${p.nome}"`, 'error'));
            }
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
