<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Virtual - Rasec Sushi</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="catalogo()" style="display: block; background: #FFF;">

    <!-- Public Header (Image 13) -->
    <header style="padding: 16px 48px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #EEE;">
        <div style="display: flex; align-items: center;">
            <img src="img/logo.png" alt="Logo" style="height: 120px; margin-right: 16px; object-fit: contain;">
            <div>
                <h1 class="font-outfit" style="font-size: 1.5rem; font-weight: 700; margin: 0;">Rasec Sushi</h1>
                <p style="font-size: 0.85rem; color: #777; margin: 0;">Akipede Mais</p>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 24px;">
            <a href="login.php" style="text-decoration: none; color: var(--primary-text); font-weight: 600; font-size: 1rem;">Entrar</a>
            <i class="fa-solid fa-circle-user" style="font-size: 1.75rem; color: #555;"></i>
        </div>
    </header>

    <main style="max-width: 1200px; margin: 0 auto; padding: 32px 24px;">
        
        <!-- Search Section -->
        <div style="display: flex; justify-content: center; margin-bottom: 48px;">
            <div style="position: relative; width: 100%; max-width: 550px;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 16px; color: #AAA;"></i>
                <input type="text" placeholder="O que você procura?" 
                       style="width: 100%; padding: 14px 16px 14px 48px; border-radius: 8px; border: 1px solid #DDD; outline: none; font-size: 1rem; background: #F8FAFC;">
            </div>
        </div>

        <h2 class="font-outfit" style="text-align: center; font-size: 2.25rem; font-weight: 700; margin-bottom: 40px; color: #101213;">Catálogo de Produtos</h2>

        <!-- Category Pills (Image 13) -->
        <div style="display: flex; gap: 12px; justify-content: center; margin-bottom: 48px; overflow-x: auto; padding-bottom: 8px;">
            <template x-for="cat in categories" :key="cat">
                <button @click="activeCategory = cat" 
                        :style="activeCategory === cat ? 'background: #FF5963; color: white;' : 'background: #f1f4f8; color: #57636c;'"
                        style="border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.95rem;"
                        x-text="cat">
                </button>
            </template>
        </div>

        <!-- Product Grid (Image 13) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 32px;">
            <template x-for="p in filteredProducts()" :key="p.id">
                <div @click="openProduct(p)" class="card" style="padding: 0; cursor: pointer; overflow: hidden; border-radius: 8px; border: none; background: #F1F4F8; box-shadow: none;">
                    <img :src="p.foto" style="width: 100%; height: 240px; object-fit: cover; border-radius: 8px 8px 0 0;">
                    <div style="padding: 24px; display: flex; flex-direction: column; gap: 4px;">
                        <h3 class="font-outfit" style="font-size: 1.5rem; font-weight: 700; color: #101213;" x-text="p.nome"></h3>
                        <div x-show="p.preco_antigo" style="font-size: 0.95rem; color: #FF5963; text-decoration: line-through;" x-text="'R$ ' + p.preco_antigo"></div>
                        <div style="font-size: 2rem; font-weight: 800; color: #FF5963;" x-text="'R$ ' + p.preco"></div>
                        <div x-show="p.economia" style="font-size: 0.85rem; color: #FF5963; font-weight: 600;" x-text="'Economia de: R$ ' + p.economia"></div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Floating Red Arrow -->
        <div style="position: fixed; bottom: 30px; right: 30px;">
            <button style="width: 60px; height: 60px; border-radius: 50%; background: #FF5963; color: white; border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.2); cursor: pointer; font-size: 1.5rem;">
                <i class="fa-solid fa-arrow-up"></i>
            </button>
        </div>
    </main>

    <!-- Product Detail Modal (Image 14) -->
    <div x-show="selectedProduct" class="modal-overlay" style="display: flex;">
        <div class="modal-content" style="max-width: 1100px; padding: 0; display: flex; overflow: hidden; border-radius: 8px; height: 650px;">
            <!-- Left: Image -->
            <div style="width: 50%; position: relative;">
                <button @click="selectedProduct = null" style="position: absolute; left: 24px; top: 24px; width: 44px; height: 44px; border-radius: 8px; background: rgba(0,0,0,0.6); color: white; border: none; cursor: pointer;">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <img :src="selectedProduct?.foto" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            
            <!-- Right: Content -->
            <div style="width: 50%; padding: 48px; display: flex; flex-direction: column; background: white;">
                <div style="flex: 1;">
                    <h2 class="font-outfit" style="font-size: 2.5rem; font-weight: 800; margin-bottom: 24px; color: #101213;" x-text="selectedProduct?.nome"></h2>
                    <p style="color: #57636c; font-size: 1rem; font-weight: 500; margin-bottom: 24px;" x-text="selectedProduct?.categoria"></p>
                    
                    <div style="margin-bottom: 24px;">
                        <p style="font-weight: 700; font-size: 1.1rem; margin-bottom: 8px;">Descrição</p>
                        <p style="color: #57636c; font-size: 1rem; line-height: 1.6;" x-text="selectedProduct?.descricao || '3333'"></p>
                    </div>

                    <div style="margin-top: 32px;">
                        <h3 class="font-outfit" style="font-size: 1.5rem; font-weight: 700; margin-bottom: 16px;">Verificar reservar</h3>
                        <div style="display: flex; gap: 16px; align-items: center;">
                            <span style="font-weight: 600; color: #101213;">Data/Hora Início</span>
                            <i class="fa-solid fa-calendar-days" style="color: #37c6da; font-size: 1.5rem;"></i>
                        </div>
                        <div style="display: flex; gap: 16px; align-items: center; margin-top: 12px;">
                            <span style="font-weight: 600; color: #101213;">Data/Hora Fim</span>
                            <i class="fa-solid fa-calendar-days" style="color: #37c6da; font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>

                <!-- Footer / Checkout Area -->
                <div style="border-top: 1px solid #EEE; padding-top: 32px;">
                    <div style="display: flex; justify-content: flex-end; align-items: baseline; gap: 20px; margin-bottom: 12px;">
                        <span style="font-size: 1.25rem; font-weight: 600;">Subtotal</span>
                        <span style="font-size: 1.75rem; color: #FF5963; font-weight: 800;" x-text="'R$ ' + (selectedProduct?.preco * qty).toFixed(2).replace('.', ',')"></span>
                    </div>
                    <div style="display: flex; justify-content: flex-end; align-items: baseline; gap: 20px; margin-bottom: 32px;">
                        <span style="font-size: 1.25rem; font-weight: 600;">Total</span>
                        <span style="font-size: 1.75rem; color: #FF5963; font-weight: 800;" x-text="'R$ ' + (selectedProduct?.preco * qty).toFixed(2).replace('.', ',')"></span>
                    </div>

                    <div style="display: flex; gap: 24px; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <button @click="if(qty > 1) qty--" style="border: none; background: none; font-size: 1.5rem; cursor: pointer; color: #FF5963; font-weight: 800;">-</button>
                            <span style="font-size: 1.5rem; font-weight: 800; width: 40px; text-align: center; color: #101213;" x-text="qty"></span>
                            <button @click="qty++" style="border: none; background: none; font-size: 1.5rem; cursor: pointer; color: #37c6da; font-weight: 800;">+</button>
                        </div>

                        <button class="btn" style="flex: 1; background: #04a24c; color: white; padding: 18px; border-radius: 8px; font-weight: 700; font-size: 1rem;">
                            <i class="fa-brands fa-whatsapp" style="margin-right: 12px; font-size: 1.25rem;"></i> Pedir por Whatsapp
                        </button>
                        
                        <button class="btn" style="background: #FF5963; color: white; padding: 18px 32px; border-radius: 8px; font-weight: 700; font-size: 1rem;">
                            <i class="fa-solid fa-cart-shopping" style="margin-right: 12px;"></i> Adicionar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); }
    </style>

    <script>
        function catalogo() {
            return {
                categories: ['Todos', 'Doces', 'Entrada', 'Nova', 'Salgados', 'Sushi'],
                activeCategory: 'Todos',
                selectedProduct: null,
                qty: 1,
                products: [
                    { id: 1, nome: 'Hot', preco: 12.99, categoria: 'Entrada', foto: 'https://p2.trrsf.com/image/fget/cf/1200/675/middle/images.terra.com/2023/04/18/163820986-istock-1205168449.jpg', descricao: '3333' },
                    { id: 2, nome: 'Niguiri', preco: 30.00, preco_antigo: 33.33, economia: 3.33, categoria: 'Nova', foto: 'https://img.itdg.com.br/tdg/images/recipes/000/011/327/324102/324102_original.jpg', descricao: 'Niguiri tradicional com peixe fresco.' },
                    { id: 3, nome: 'Sushi show', preco: 10.00, preco_antigo: 20.89, economia: 10.89, categoria: 'Sushi', foto: 'https://img.freepik.com/fotos-premium/sushi-show-com-salmao-e-atum_158001-2485.jpg', descricao: 'Combo especial de sushi.' }
                ],
                filteredProducts() {
                    if (this.activeCategory === 'Todos') return this.products;
                    return this.products.filter(p => p.categoria === this.activeCategory);
                },
                openProduct(p) {
                    this.selectedProduct = p;
                    this.qty = 1;
                }
            }
        }
    </script>
</body>
</html>
