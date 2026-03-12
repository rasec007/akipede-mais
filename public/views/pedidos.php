<!-- public/views/pedidos.php -->
<section id="aba-pedidos" x-show="currentTab === 'pedidos'" class="glass-card">
    <div class="section-header">
        <h2>Meus Pedidos</h2>
        <div class="filters">
            <select x-model="filtroStatus">
                <option value="todos">Todos os Status</option>
                <option value="Pendente">Pendente</option>
                <option value="Confirmado">Confirmado</option>
                <option value="Entregue">Entregue</option>
            </select>
        </div>
    </div>
    
    <div class="grid-pedidos">
        <template x-for="ped in orders" :key="ped.id_pedido">
            <div class="glass-card pedido-card product-card">
                <div class="pedido-header">
                    <span class="pedido-id" x-text="'#' + ped.id_pedido.substring(0,8)"></span>
                    <span class="badge" :class="'badge-' + ped.status.toLowerCase()" x-text="ped.status"></span>
                </div>
                <div class="pedido-body">
                    <p>Total: <strong x-text="'R$ ' + ped.valor_total"></strong></p>
                    <p class="date" x-text="formatDate(ped.dt_criado)"></p>
                </div>
                <button @click="verDetalhesPedido(ped)" class="btn-primary w-full" style="margin-top: 15px;">Ver Detalhes</button>
            </div>
        </template>
    </div>
</section>
