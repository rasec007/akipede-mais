<?php
require_once __DIR__ . '/api/auth/check_session.php';
checkAuth();
$loggedUser = getLoggedUser();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akipede Mais - Dashboard</title>
    <link rel="icon" href="img/logo1__.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Prevenir Flash of Unstyled Content (FOUC) do AlpineJS */
        [x-cloak] { display: none !important; }

        /* Status Badges */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
            text-transform: uppercase;
        }
        .status-badge.aprovado, .status-badge.ativo { background-color: rgba(80, 205, 137, 0.1); color: #50cd89; }
        .status-badge.pendente { background-color: rgba(249, 190, 75, 0.1); color: #f9be4b; }
        .status-badge.cancelado, .status-badge.inativo { background-color: rgba(241, 65, 108, 0.1); color: #f1416c; }

        /* Google Places Autocomplete - Estilizações e Correções */
        .pac-container {
            z-index: 999999 !important;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
            border: 1px solid var(--line-color) !important;
        }
        /* Remove o "powered by Google" */
        .pac-container::after { display: none !important; }
        .pac-logo::after { display: none !important; }
        .hdpi.pac-logo::after { display: none !important; }
        .pac-target-input { background-image: none !important; }
        
        /* Personalização dos itens da lista de endereços */
        .pac-item {
            padding: 10px 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: var(--secondary-text);
            cursor: pointer;
            border-top: 1px solid #f1f4f8;
        }
        .pac-item:hover { background-color: #f4f6fc; }
        .pac-item-query { font-weight: 600; color: var(--primary-text); }
        .pac-icon { display: none; }
    </style>
</head>
<body x-data="app()">
    
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="logo-container">
            <img src="img/1__.png" alt="Logo" style="height: 80px; object-fit: contain;">
      <!-- <span style="font-weight: 800; font-size: 1.2rem;">Akipede Orçamento</span> -->
        </div>

        <div class="user-profile-summary" style="border-radius: 8px;">
            <img :src="user.foto || 'img/placeholder.svg'" onerror="this.src='img/placeholder.svg'" alt="User" style="width: 44px; height: 44px; border-radius: 8px; margin-right: 12px; object-fit: cover;" loading="lazy">
            <div style="overflow: hidden;">
                <p style="font-weight: 700; font-size: 0.95rem; color: var(--primary-text); white-space: nowrap; text-overflow: ellipsis;" x-text="user.nome || 'Carregando...'"></p>
                <p style="font-size: 0.8rem; color: var(--secondary-text);" x-text="user.email || '...'"></p>
            </div>
        </div>

        <nav style="flex: 1;">
            <a href="#" @click.prevent="currentTab = 'home'" :class="currentTab === 'home' ? 'active' : ''" class="nav-link">
                <i class="fa-solid fa-house-chimney"></i> Dashboard
            </a>
            <a href="#" @click.prevent="currentTab = 'produtos'" :class="currentTab === 'produtos' ? 'active' : ''" class="nav-link">
                <i class="fa-solid fa-box"></i> Produtos
            </a>
            <a href="#" @click.prevent="currentTab = 'usuarios'" :class="currentTab === 'usuarios' ? 'active' : ''" class="nav-link">
                <i class="fa-solid fa-users"></i> Usuário
            </a>
            <a href="#" @click.prevent="currentTab = 'orcamentos'" :class="currentTab === 'orcamentos' ? 'active' : ''" class="nav-link">
                <i class="fa-solid fa-file-invoice-dollar"></i> Orçamento
            </a>
            <a href="#" @click.prevent="currentTab = 'loja'" :class="currentTab === 'loja' ? 'active' : ''" class="nav-link">
                <i class="fa-solid fa-shop"></i> Minha Loja
            </a>
            <a href="#" @click.prevent="currentTab = 'perfil'" :class="currentTab === 'perfil' ? 'active' : ''" class="nav-link">
                <i class="fa-solid fa-user-gear"></i> Perfil
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="#" @click.prevent="modal = 'confirmar-logout'" class="nav-link">
                <i class="fa-solid fa-right-from-bracket"></i> Sair
            </a>
            
            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--line-color); display: flex; justify-content: center;">
                <div class="switch">
                    <div class="handle"></div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        
        <!-- Header Section (Image 02) -->
        <header style="margin-bottom: 32px;">
            <h1 class="font-outfit" style="font-size: 2rem; font-weight: 700; margin-bottom: 4px;" x-text="getPageTitle()"></h1>
            <p style="color: var(--secondary-text); font-size: 0.95rem;">Abaixo está um resumo da sua atividade.</p>
        </header>

        <!-- Home / Dashboard (Image 02) -->
        <div x-show="currentTab === 'home'" x-transition>
            <div class="stats-grid">
                <!-- Welcome Card -->
                <div class="card welcome-card">
                    <p style="font-size: 0.95rem; font-weight: 600; color: var(--primary-text); margin-bottom: 4px;">Olá, <span x-text="user.nome"></span></p>
                    <p style="font-size: 0.85rem; color: var(--secondary-text); margin-bottom: 12px;">Total de vendas concluídas</p>
                    <p class="price" x-text="formatMoney(stats.valor_vendas)">R$ 0,00</p>
                </div>
                
                <!-- Partners Stat -->
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(255, 89, 99, 0.1); color: var(--alternate);">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <p style="font-size: 0.75rem; color: var(--secondary-text); font-weight: 500;">Parceiros</p>
                        <p style="font-size: 1.5rem; font-weight: 700;" x-text="stats.parceiros">1</p>
                    </div>
                </div>

                <!-- Clients Stat -->
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(255, 89, 99, 0.1); color: var(--alternate);">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                    <div class="stat-info">
                        <p style="font-size: 0.75rem; color: var(--secondary-text); font-weight: 500;">Clientes</p>
                        <p style="font-size: 1.5rem; font-weight: 700;" x-text="stats.clientes">5</p>
                    </div>
                </div>

                <!-- Sales Stat -->
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(255, 89, 99, 0.1); color: var(--alternate);">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    <div class="stat-info">
                        <p style="font-size: 0.75rem; color: var(--secondary-text); font-weight: 500;">Total de Vendas</p>
                        <p style="font-size: 1.5rem; font-weight: 700;" x-text="stats.pedidos">2</p>
                    </div>
                </div>
            </div>

            <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 32px;">
                <div style="padding: 24px;">
                    <h3 class="font-outfit" style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Top 10 Produtos mais vendidos</h3>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th style="padding-left: 24px;">Produto</th>
                                <th>Categoria</th>
                                <th>Total de Pedidos</th>
                                <th>Qtd. Vendidos</th>
                                <th style="text-align: right; padding-right: 24px;">Valor Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="prod in topProducts" :key="prod.id_produto">
                                <tr class="product-row">
                                    <td style="padding-left: 24px; display: flex; align-items: center;">
                                        <img :src="prod.foto || 'img/placeholder.svg'" onerror="this.src='img/placeholder.svg'" alt="Prod" style="width: 48px; height: 48px; border-radius: 8px; margin-right: 12px; object-fit: cover;" loading="lazy">
                                        <span style="font-weight: 600;" x-text="prod.nome"></span>
                                    </td>
                                    <td x-text="prod.categoria_nome || 'Sem Categoria'"></td>
                                    <td x-text="prod.total_pedidos"></td>
                                    <td x-text="prod.total_vendidos"></td>
                                    <td style="text-align: right; padding-right: 24px; font-weight: 700; color: var(--alternate);" x-text="formatMoney(prod.valor_total)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div x-show="topProducts.length === 0" style="text-align: center; padding: 48px; color: var(--secondary-text);">
                        No momento não há produtos vendidos
                    </div>
                </div>
            </div>

            <!-- Top 10 Pedidos Recentes -->
            <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 32px;">
                <div style="padding: 24px;">
                    <h3 class="font-outfit" style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Top 10 Pedidos recentes</h3>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th style="padding-left: 24px;">Cliente</th>
                                <th>Data</th>
                                <th>Contato</th>
                                <th>Valor Total</th>
                                <th style="text-align: right; padding-right: 24px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="order in topOrders" :key="order.id_orcamento">
                                <tr class="product-row">
                                    <td style="padding-left: 24px;">
                                        <div style="font-weight: 600;" x-text="order.cliente_nome"></div>
                                        <div style="font-size: 0.75rem; color: var(--secondary-text);">Nº: <span x-text="order.numero_sequencial"></span></div>
                                    </td>
                                    <td x-text="formatDate(order.dt_criado)"></td>
                                    <td>
                                        <div x-text="order.cliente_fone"></div>
                                        <div style="font-size: 0.75rem; color: var(--secondary-text);" x-text="order.cliente_cpf_cnpj"></div>
                                    </td>
                                    <td x-text="formatMoney(order.valor_total)"></td>
                                    <td style="text-align: right; padding-right: 24px;">
                                        <span class="status-badge" :class="order.status.toLowerCase()" x-text="order.status"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div x-show="topOrders.length === 0" style="text-align: center; padding: 48px; color: var(--secondary-text);">
                        Sem pedidos recentes
                    </div>
                </div>
            </div>

            <!-- Top 10 Clientes -->
            <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 32px;">
                <div style="padding: 24px;">
                    <h3 class="font-outfit" style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Top 10 Clientes</h3>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th style="padding-left: 24px;">Cliente</th>
                                <th>Qtd. de pedidos</th>
                                <th>Total de valor comprado</th>
                                <th style="text-align: right; padding-right: 24px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="client in topClients" :key="client.nome">
                                <tr class="product-row">
                                    <td style="padding-left: 24px; display: flex; align-items: center;">
                                        <img :src="client.foto || 'img/placeholder.svg'" alt="Client" style="width: 48px; height: 48px; border-radius: 8px; margin-right: 12px; object-fit: cover;" loading="lazy">
                                        <div>
                                            <div style="font-weight: 600;" x-text="client.nome"></div>
                                            <div style="font-size: 0.75rem; color: var(--secondary-text);" x-text="client.fone"></div>
                                        </div>
                                    </td>
                                    <td x-text="client.total_pedidos"></td>
                                    <td x-text="formatMoney(client.valor_total)"></td>
                                    <td style="text-align: right; padding-right: 24px;">
                                        <span class="status-badge" :class="client.status.toLowerCase()" x-text="client.status"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top 10 Parceiros -->
            <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 32px;">
                <div style="padding: 24px;">
                    <h3 class="font-outfit" style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Top 10 Parceiros</h3>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th style="padding-left: 24px;">Parceiro</th>
                                <th>Qtd. de vendas realizadas</th>
                                <th>Total de vendas realizadas</th>
                                <th style="text-align: right; padding-right: 24px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="partner in topPartners" :key="partner.nome">
                                <tr class="product-row">
                                    <td style="padding-left: 24px; display: flex; align-items: center;">
                                        <img :src="partner.foto || 'img/placeholder.svg'" alt="Partner" style="width: 48px; height: 48px; border-radius: 8px; margin-right: 12px; object-fit: cover;" loading="lazy">
                                        <div>
                                            <div style="font-weight: 600;" x-text="partner.nome"></div>
                                            <div style="font-size: 0.75rem; color: var(--secondary-text);" x-text="partner.email"></div>
                                        </div>
                                    </td>
                                    <td x-text="partner.total_pedidos"></td>
                                    <td x-text="formatMoney(partner.valor_total)"></td>
                                    <td style="text-align: right; padding-right: 24px;">
                                        <span class="status-badge" :class="partner.status.toLowerCase()" x-text="partner.status"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Dynamic Content from Views -->
        <?php include 'views/produtos.php'; ?>
        <?php include 'views/orcamentos.php'; ?>
        <?php include 'views/clientes.php'; ?>
        <?php include 'views/loja.php'; ?>

        <!-- Popups / Modais -->
        <?php include 'views/modais.php'; ?>
    </main>

    <!-- Toast Notifications Container -->
    <div class="toast-container">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="toast show shadow-lg" :class="'toast-' + toast.type">
                <i :class="toast.type === 'success' ? 'fa-solid fa-circle-check text-success' : (toast.type === 'error' ? 'fa-solid fa-circle-xmark text-error' : 'fa-solid fa-circle-exclamation text-warning')"></i>
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>

    <!-- Google Maps Places API -->
    <?php $gmaps_key = getenv('GMAPS_KEY') ?: 'AIzaSyDgiuDZTfivdsfbBCL1A0k0MP4nihQv4nk'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= $gmaps_key ?>&libraries=places&loading=async" async defer></script>
    <!-- Alpine.js Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine.js Core (DEVE vir após os plugins) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- App Logic -->
    <script src="js/app.js?v=<?= time() ?>"></script>
</body>
</html>
