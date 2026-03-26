<!-- public/views/orcamentos.php -->
<section id="aba-orcamentos" x-show="currentTab === 'orcamentos'" x-transition x-cloak>
    <div style="margin-bottom: 24px;">
        <h2 class="font-outfit" style="font-size: 2rem; font-weight: 600; color: #000; margin-bottom: 24px;">Orçamentos</h2>
        
        <!-- Cards de Status -->
        <div style="display: flex; gap: 24px; margin-bottom: 32px;">
            <!-- Pendentes -->
            <div style="flex: 1; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); background: white;">
                <div style="background-color: #fbbc3e; padding: 24px; color: white; position: relative; height: 120px; display: flex; flex-direction: column; justify-content: flex-end;">
                    <div style="position: absolute; top: 16px; left: 16px; width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center;">
                        <i class="fa-regular fa-clock" style="font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-size: 3.5rem; font-weight: 600; line-height: 1; text-align: left;" x-text="orcamentos.filter(o => (o.status || '').toUpperCase() === 'PENDENTE').length">0</div>
                </div>
                <div style="padding: 16px 24px; font-size: 1.35rem; font-weight: 500; color: #000;">Pendentes</div>
            </div>

            <!-- Aprovados -->
            <div style="flex: 1; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); background: white;">
                <div style="background-color: #7acc56; padding: 24px; color: white; position: relative; height: 120px; display: flex; flex-direction: column; justify-content: flex-end;">
                    <div style="position: absolute; top: 16px; left: 16px; width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-dollar-sign" style="font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-size: 3.5rem; font-weight: 600; line-height: 1; text-align: left;" x-text="orcamentos.filter(o => (o.status || '').toUpperCase() === 'APROVADO').length">0</div>
                </div>
                <div style="padding: 16px 24px; font-size: 1.35rem; font-weight: 500; color: #000;">Aprovados</div>
            </div>

            <!-- Cancelados -->
            <div style="flex: 1; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); background: white;">
                <div style="background-color: #ee436b; padding: 24px; color: white; position: relative; height: 120px; display: flex; flex-direction: column; justify-content: flex-end;">
                    <div style="position: absolute; top: 16px; left: 16px; width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-ban" style="font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-size: 3.5rem; font-weight: 600; line-height: 1; text-align: left;" x-text="orcamentos.filter(o => (o.status || '').toUpperCase() === 'CANCELADO').length">0</div>
                </div>
                <div style="padding: 16px 24px; font-size: 1.35rem; font-weight: 500; color: #000;">Cancelados</div>
            </div>
        </div>

        <!-- Lista e Novo -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 16px;">
            <p style="color: #4b5563; font-size: 1rem; font-weight: 500;">Lista de orçamento</p>
            <button @click="openModal('novo-orcamento')" style="background-color: #fb5153; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-plus"></i> Novo Orçamento
            </button>
        </div>

        <!-- Filtros e Busca -->
        <div style="background: white; border-radius: 8px; margin-bottom: 24px; border: 1px solid var(--line-color); padding: 16px 24px; display: flex; align-items: center;">
            <i class="fa-solid fa-magnifying-glass" style="color: #6b7280; font-size: 1.3rem;"></i>
            <input type="text" placeholder="Buscar por Cliente" 
                   style="width: 100%; border: none; outline: none; font-size: 1rem; color: #000; margin-left: 12px; background: transparent;"
                   x-model="searchOrcamento">
        </div>

        <style>
            .orcamento-filter-tab {
                height: 50px;
                border-radius: 8px;
                font-size: 1.05rem;
                cursor: pointer;
                transition: 0.2s;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        </style>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
            <div class="orcamento-filter-tab"
                 :style="filterOrcamentoStatus === 'Pendentes' ? 'background-color: #fb5153; color: white;' : 'color: #6b7280; background: transparent;'"
                 @click="filterOrcamentoStatus = 'Pendentes'">
                Pendentes
            </div>
            <div class="orcamento-filter-tab"
                 :style="filterOrcamentoStatus === 'Aprovados' ? 'background-color: #fb5153; color: white;' : 'color: #6b7280; background: transparent;'"
                 @click="filterOrcamentoStatus = 'Aprovados'">
                Aprovados
            </div>
            <div class="orcamento-filter-tab"
                 :style="filterOrcamentoStatus === 'Cancelados' ? 'background-color: #fb5153; color: white;' : 'color: #6b7280; background: transparent;'"
                 @click="filterOrcamentoStatus = 'Cancelados'">
                Cancelados
            </div>
        </div>

        <!-- Tabela -->
        <div class="card" style="padding: 16px; overflow: hidden; border-radius: 8px; border: 1px solid var(--line-color); box-shadow: none;">
            <div class="table-container">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0 8px;">
                    <thead>
                        <tr style="background-color: #e5e7eb;">
                            <th style="padding: 16px 24px; text-align: left; color: #374151; font-weight: 500; border-radius: 8px 0 0 8px;">AÇÃO</th>
                            <th style="padding: 16px 8px; text-align: left; color: #374151; font-weight: 500;">Nº</th>
                            <th style="padding: 16px 8px; text-align: left; color: #374151; font-weight: 500;">DATA</th>
                            <th style="padding: 16px 8px; text-align: center; color: #374151; font-weight: 500;">CLIENTE</th>
                            <th style="padding: 16px 8px; text-align: center; color: #374151; font-weight: 500;">PARCEIRO</th>
                            <th style="padding: 16px 24px; text-align: right; color: #374151; font-weight: 500; border-radius: 0 8px 8px 0;">VALOR TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="o in filteredOrcamentos()" :key="o.id_orcamento">
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 16px 24px; display: flex; gap: 8px; font-size: 1.3rem; align-items: center;">
                                    <i class="fa-solid fa-pen" style="color: #26b1cb; cursor: pointer; font-size: calc(1.3rem + 5px);" @click="editOrcamento(o)"></i>
                                    <i class="fa-solid fa-trash-can" style="color: #ee436b; cursor: pointer; font-size: calc(1.3rem + 5px);" @click="deleteOrcamento(o)"></i>
                                    <i class="fa-solid fa-square-check" style="color: #1e3a8a; cursor: pointer; font-size: calc(1.3rem + 5px);" @click="approveOrcamento(o)" title="Aprovar"></i>
                                </td>
                                <td style="padding: 16px 8px; color: #111;" x-text="'#' + (o.numero_sequencial || '1')"></td>
                                <td style="padding: 16px 8px; color: #111;" x-text="formatDate(o.dt_criado) || '1/03/2026'"></td>
                                <td style="padding: 16px 8px; color: #111; text-align: center;" x-text="getClientName(o.cliente_nome)"></td>
                                <td style="padding: 16px 8px; color: #111; text-align: center;" x-text="o.parceiro_nome || 'Sistema'"></td>
                                <td style="padding: 16px 24px; color: #111; text-align: right;" x-text="formatMoney(o.valor_total)"></td>
                            </tr>
                        </template>
                        
                        <!-- Estado Vazio -->
                        <tr x-show="filteredOrcamentos().length === 0">
                            <td colspan="6" style="padding: 32px; text-align: center; color: #6b7280;">Nenhum orçamento encontrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
