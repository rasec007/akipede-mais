<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Virtual</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { background: #FFF; }
        .cat-pill { border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.95rem; }
        .product-card { cursor: pointer; overflow: hidden; border-radius: 12px; border: none; background: #F1F4F8; box-shadow: none; transition: transform 0.2s, box-shadow 0.2s; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
        .modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); }
        @media (max-width: 768px) {
            .modal-inner { flex-direction: column !important; height: auto !important; }
            .modal-img-side { width: 100% !important; height: 260px !important; }
            .modal-content-side { width: 100% !important; }
        }
    </style>
</head>
<body x-data="catalogo()" x-init="init()" style="display: block;">

    <!-- Header -->
    <header style="padding: 16px 48px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #EEE;"
            :style="loja ? 'border-bottom-color: ' + loja.cor_tema + '40' : ''">
        <div style="display: flex; align-items: center; gap: 16px;">
            <img :src="loja?.logo ? ('storage-akipede/lojas/' + loja.logo.split('/').pop()) : 'img/placeholder.svg'"
                 alt="Logo" style="height: 80px; object-fit: contain; border-radius: 8px;">
            <div>
                <h1 class="font-outfit" style="font-size: 1.5rem; font-weight: 700; margin: 0;" x-text="loja?.nome || 'Catálogo'"></h1>
                <p style="font-size: 0.85rem; color: #777; margin: 0;" x-text="loja?.descricao || ''"></p>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 24px;">
            <a href="login.php" style="text-decoration: none; color: var(--primary-text); font-weight: 600;">Entrar</a>
            <i class="fa-solid fa-circle-user" style="font-size: 1.75rem; color: #555;"></i>
        </div>
    </header>

    <!-- Loading State -->
    <div x-show="loading" style="display: flex; justify-content: center; padding: 80px;">
        <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; color: #ccc;"></i>
    </div>

    <!-- Not Found -->
    <div x-show="!loading && !loja" style="text-align: center; padding: 80px 24px;">
        <i class="fa-solid fa-store-slash" style="font-size: 4rem; color: #ccc; margin-bottom: 24px; display: block;"></i>
        <h2 style="color: #444; margin-bottom: 8px;">Loja não encontrada</h2>
        <p style="color: #888;">O endereço que você acessou não corresponde a nenhuma loja cadastrada.</p>
    </div>

    <main x-show="!loading && loja" style="max-width: 1200px; margin: 0 auto; padding: 32px 24px;">
        <!-- Search -->
        <div style="display: flex; justify-content: center; margin-bottom: 48px;">
            <div style="position: relative; width: 100%; max-width: 550px;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 16px; color: #AAA;"></i>
                <input type="text" x-model="search" placeholder="O que você procura?"
                       style="width: 100%; padding: 14px 16px 14px 48px; border-radius: 8px; border: 1px solid #DDD; outline: none; font-size: 1rem; background: #F8FAFC; box-sizing: border-box;">
            </div>
        </div>

        <h2 class="font-outfit" style="text-align: center; font-size: 2.25rem; font-weight: 700; margin-bottom: 40px; color: #101213;">Catálogo de Produtos</h2>

        <!-- Categories -->
        <div style="display: flex; gap: 12px; justify-content: center; margin-bottom: 48px; flex-wrap: wrap;">
            <template x-for="cat in categories" :key="cat">
                <button class="cat-pill" @click="activeCategory = cat"
                        :style="activeCategory === cat ? 'background: ' + (loja?.cor_tema || '#37c6da') + '; color: white;' : 'background: #f1f4f8; color: #57636c;'"
                        x-text="cat"></button>
            </template>
        </div>

        <!-- Product Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
            <template x-for="p in filteredProducts()" :key="p.id_produto">
                <div class="product-card" @click="openProduct(p)">
                    <img :src="p.foto ? (p.foto.startsWith('http') ? p.foto : 'storage-akipede/produtos/' + p.foto.split('/').pop()) : 'img/placeholder.svg'"
                         style="width: 100%; height: 220px; object-fit: cover;">
                    <div style="padding: 20px;">
                        <h3 class="font-outfit" style="font-size: 1.25rem; font-weight: 700; color: #101213; margin: 0 0 8px;" x-text="p.nome"></h3>
                        <template x-if="p.valor_promocional && parseFloat(p.valor_promocional) > 0">
                            <div style="font-size: 0.9rem; color: #999; text-decoration: line-through;" x-text="formatMoney(p.valor_venda)"></div>
                        </template>
                        <div style="font-size: 1.75rem; font-weight: 800;" :style="'color: ' + (loja?.cor_tema || '#ff4e4e')"
                             x-text="formatMoney(p.valor_promocional && parseFloat(p.valor_promocional) > 0 ? p.valor_promocional : p.valor_venda)"></div>
                        <template x-if="p.valor_promocional && parseFloat(p.valor_promocional) > 0">
                            <div style="font-size: 0.85rem; font-weight: 600;" :style="'color: ' + (loja?.cor_tema || '#ff4e4e')"
                                 x-text="'Economia de: ' + formatMoney(parseFloat(p.valor_venda) - parseFloat(p.valor_promocional))"></div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="filteredProducts().length === 0 && !loading" style="text-align: center; padding: 48px; color: #aaa;">
            <i class="fa-solid fa-box-open" style="font-size: 2.5rem; margin-bottom: 16px; display: block;"></i>
            <p>Nenhum produto encontrado.</p>
        </div>
    </main>

    <!-- Floating Scroll Up -->
    <div style="position: fixed; bottom: 30px; right: 30px;">
        <button @click="window.scrollTo({top:0, behavior:'smooth'})"
                :style="'background: ' + (loja?.cor_tema || '#37c6da')"
                style="width: 60px; height: 60px; border-radius: 50%; color: white; border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.2); cursor: pointer; font-size: 1.5rem;">
            <i class="fa-solid fa-arrow-up"></i>
        </button>
    </div>

    <!-- Product Detail Modal -->
    <div x-show="selectedProduct" class="modal-overlay" @click.self="selectedProduct = null">
        <div class="modal-inner" style="display: flex; background: white; border-radius: 12px; overflow: hidden; max-width: 980px; width: 95%; height: 600px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <div class="modal-img-side" style="width: 45%; position: relative;">
                <button @click="selectedProduct = null" style="position: absolute; left: 16px; top: 16px; width: 40px; height: 40px; border-radius: 8px; background: rgba(0,0,0,0.5); color: white; border: none; cursor: pointer; z-index: 2;">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <img :src="selectedProduct?.foto ? (selectedProduct.foto.startsWith('http') ? selectedProduct.foto : 'storage-akipede/produtos/' + selectedProduct.foto.split('/').pop()) : 'img/placeholder.svg'"
                     style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div class="modal-content-side" style="width: 55%; padding: 40px; display: flex; flex-direction: column; overflow-y: auto;">
                <div style="flex: 1;">
                    <h2 class="font-outfit" style="font-size: 2rem; font-weight: 800; margin-bottom: 8px;" x-text="selectedProduct?.nome"></h2>
                    <p style="color: #aaa; font-size: 0.9rem; margin-bottom: 16px;" x-text="selectedProduct?.categoria_nome || selectedProduct?.id_categoria || ''"></p>
                    <p style="color: #57636c; font-size: 1rem; line-height: 1.6;" x-text="selectedProduct?.descricao || 'Sem descrição disponível.'"></p>
                    <div style="margin-top: 24px;">
                        <div style="font-size: 2rem; font-weight: 800;" :style="'color: ' + (loja?.cor_tema || '#ff4e4e')"
                             x-text="formatMoney(selectedProduct?.valor_promocional && parseFloat(selectedProduct?.valor_promocional) > 0 ? selectedProduct?.valor_promocional : selectedProduct?.valor_venda)"></div>
                        <template x-if="selectedProduct?.valor_promocional && parseFloat(selectedProduct?.valor_promocional) > 0">
                            <div style="text-decoration: line-through; color: #aaa; font-size: 1rem;" x-text="formatMoney(selectedProduct?.valor_venda)"></div>
                        </template>
                    </div>
                </div>
                <div style="border-top: 1px solid #eee; padding-top: 24px; margin-top: 24px;">
                    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 16px;">
                        <button @click="if(qty > 1) qty--" style="width:36px; height:36px; border-radius:50%; border:2px solid #eee; background:none; font-size:1.25rem; cursor:pointer; font-weight:800;">-</button>
                        <span style="font-size: 1.5rem; font-weight: 800; min-width: 32px; text-align: center;" x-text="qty"></span>
                        <button @click="qty++" style="width:36px; height:36px; border-radius:50%; border:2px solid #eee; background:none; font-size:1.25rem; cursor:pointer; font-weight:800;">+</button>
                    </div>
                    <a :href="'https://wa.me/' + (loja?.whatsapp || '').replace(/\D/g,'') + '?text=Olá! Gostaria de comprar ' + qty + 'x ' + selectedProduct?.nome"
                       target="_blank"
                       style="display: flex; align-items: center; justify-content: center; gap: 12px; background: #04a24c; color: white; padding: 16px 24px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 1rem;">
                        <i class="fa-brands fa-whatsapp" style="font-size: 1.4rem;"></i> Pedir por WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function catalogo() {
            return {
                loja: null,
                products: [],
                categories: ['Todos'],
                activeCategory: 'Todos',
                selectedProduct: null,
                search: '',
                qty: 1,
                loading: true,

                init() {
                    // Always use query string for compatibility with PHP built-in server
                    const params = new URLSearchParams(window.location.search);
                    const q = params.get('url') || params.get('slug');
                    if (q) this.loadData(q);
                    else this.loading = false;
                },

                loadData(slug) {
                    fetch('api/index.php/catalogo_publico?url=' + encodeURIComponent(slug))
                        .then(res => res.json())
                        .then(data => {
                            if (data.loja) {
                                this.loja = data.loja;
                                this.products = (data.produtos || []).filter(p => p.ativo);
                                const cats = [...new Set(this.products.map(p => p.id_categoria || 'Geral'))];
                                this.categories = ['Todos', ...cats];
                                document.title = 'Catálogo - ' + data.loja.nome;
                            }
                        })
                        .catch(err => console.error(err))
                        .finally(() => this.loading = false);
                },

                filteredProducts() {
                    let list = this.products;
                    if (this.activeCategory !== 'Todos') {
                        list = list.filter(p => (p.id_categoria || 'Geral') === this.activeCategory);
                    }
                    if (this.search.trim()) {
                        const q = this.search.toLowerCase();
                        list = list.filter(p => p.nome.toLowerCase().includes(q) || (p.descricao || '').toLowerCase().includes(q));
                    }
                    return list;
                },

                openProduct(p) {
                    this.selectedProduct = p;
                    this.qty = 1;
                },

                formatMoney(val) {
                    if (!val) return 'R$ 0,00';
                    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(parseFloat(val));
                }
            }
        }
    </script>
</body>
</html>
