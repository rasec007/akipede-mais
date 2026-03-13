<!-- public/views/orcamentos.php -->
<section id="aba-orcamentos" x-show="currentTab === 'orcamentos'" x-transition>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div style="position: relative; width: 400px;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 14px; color: var(--secondary-text);"></i>
            <input type="text" placeholder="Buscar orçamento..." 
                   style="width: 100%; padding: 12px 16px 12px 48px; border-radius: 8px; border: 1px solid var(--line-color); background: white; outline: none; font-size: 0.95rem;"
                   x-model="searchOrcamento">
        </div>
        <button @click="openModal('novo-orcamento')" class="btn btn-primary">
            <i class="fa-solid fa-plus" style="margin-right: 8px;"></i> Novo Orçamento
        </button>
    </div>

    <div class="card" style="padding: 0; overflow: hidden; border-radius: 8px;">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">Nº Orçamento</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Vencimento</th>
                        <th>Status</th>
                        <th style="text-align: right;">Total</th>
                        <th style="text-align: right; padding-right: 24px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="o in filteredOrcamentos()" :key="o.id_orcamento">
                        <tr class="product-row">
                            <td style="padding-left: 24px; font-weight: 700; color: var(--secondary-text);" x-text="'#' + (o.numero_sequencial || '0001')"></td>
                            <td>
                                <span style="font-weight: 600;" x-text="o.cliente_nome"></span>
                            </td>
                            <td x-text="formatDate(o.dt_criado)"></td>
                            <td x-text="formatDate(o.validade) || '-'"></td>
                            <td>
                                <span :style="{
                                    'background': o.status === 'Aprovado' ? 'rgba(4, 162, 76, 0.1)' : (o.status === 'Cancelado' ? 'rgba(226, 28, 61, 0.1)' : 'rgba(252, 220, 12, 0.1)'),
                                    'color': o.status === 'Aprovado' ? 'var(--success)' : (o.status === 'Cancelado' ? 'var(--error)' : '#a18d00')
                                }" style="padding: 6px 14px; border-radius: 8px; font-size: 0.8rem; font-weight: 700;" x-text="o.status"></span>
                            </td>
                            <td style="text-align: right; font-weight: 800; color: var(--alternate);" x-text="formatMoney(o.valor_total)"></td>
                            <td style="text-align: right; padding-right: 24px;">
                                <button class="btn" style="background: transparent; color: var(--secondary-text); padding: 8px;" @click="editOrcamento(o)">
                                    <i class="fa-solid fa-pen-to-square" style="font-size: 1.3rem;"></i>
                                </button>
                                <button class="btn" style="background: transparent; color: var(--error); padding: 8px;" @click="deleteOrcamento(o)">
                                    <i class="fa-solid fa-trash-can" style="font-size: 1.3rem;"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</section>
