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
    <title>Akipede Mais - Dashboard Premium</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="app()">
    
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="logo-container">
            <img src="img/logo.png" alt="Logo" style="height: 80px; object-fit: contain;">
            <span style="font-weight: 800; font-size: 1.2rem;">Akipede Orçamento</span>
        </div>

        <div class="user-profile-summary" style="border-radius: 8px;">
            <img :src="user.foto || 'https://via.placeholder.com/44'" alt="User" style="width: 44px; height: 44px; border-radius: 8px; margin-right: 12px; object-fit: cover;" loading="lazy">
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
            <a href="#" @click.prevent="logout()" class="nav-link">
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

            <!-- Top Products Table (Image 02) -->
            <div class="card" style="padding: 0; overflow: hidden;">
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
                            <template x-for="prod in topProducts" :key="prod.id">
                                <tr class="product-row">
                                    <td style="padding-left: 24px; display: flex; align-items: center;">
                                        <img :src="prod.foto || 'https://via.placeholder.com/45'" alt="Prod" style="width: 48px; height: 48px; border-radius: 8px; margin-right: 12px; object-fit: cover;" loading="lazy">
                                        <span style="font-weight: 600;" x-text="prod.nome"></span>
                                    </td>
                                    <td x-text="prod.categoria_nome || 'Sem Categoria'"></td>
                                    <td x-text="prod.total_pedidos">2</td>
                                    <td x-text="prod.total_vendidos">8</td>
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
        </div>

        <!-- Dynamic Content from Views -->
        <div x-show="currentTab === 'produtos'" x-transition><?php include 'views/produtos.php'; ?></div>
        <div x-show="currentTab === 'orcamentos'" x-transition><?php include 'views/orcamentos.php'; ?></div>
        <div x-show="currentTab === 'usuarios'" x-transition><?php include 'views/clientes.php'; ?></div>
        <div x-show="currentTab === 'loja'" x-transition><?php include 'views/loja.php'; ?></div>

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

    <script src="js/app.js"></script>
</body>
</html>
