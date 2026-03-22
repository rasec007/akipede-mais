<!-- public/views/produtos.php -->
<section id="aba-produtos" x-show="currentTab === 'produtos'" x-transition>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div style="position: relative; width: 400px;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 14px; color: var(--secondary-text);"></i>
            <input type="text" placeholder="O que você procura?" 
                   style="width: 100%; padding: 12px 16px 12px 48px; border-radius: 8px; border: 1px solid var(--line-color); background: white; outline: none; font-size: 0.95rem; color: var(--primary-text);"
                   x-model="searchProduct">
        </div>
        <button @click="openModal('novo-produto')" class="btn btn-primary">
            <i class="fa-solid fa-plus" style="margin-right: 8px;"></i> Novo Produto
        </button>
    </div>

    <div class="card" style="padding: 0; overflow: hidden; border-radius: 8px;">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">Nome</th>
                        <th style="text-align: center;">Categoria</th>
                        <th style="text-align: center;">Preço</th>
                        <th style="text-align: center;">Agendamento</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center; padding-right: 24px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="p in filteredProducts()" :key="p.id_produto">
                        <tr class="product-row">
                            <td style="padding-left: 24px; display: flex; align-items: center;">
                                <img :src="p.foto || 'https://via.placeholder.com/48'" alt="Prod" style="width: 48px; height: 48px; border-radius: 8px; margin-right: 12px; object-fit: cover;" loading="lazy">
                                <span style="font-weight: 600;" x-text="p.nome"></span>
                            </td>
                            <td style="text-align: center;" x-text="p.categoria_nome || '-'"></td>
                            <td style="font-weight: 700; color: var(--alternate); text-align: center;" x-text="formatMoney(p.valor_venda)"></td>
                            <td style="text-align: center;">
                                <button class="btn" style="background: rgba(4, 219, 126, 0.1); color: var(--primary); padding: 8px; border-radius: 8px;" @click="openAgendamento(p)">
                                    <i class="fa-regular fa-calendar-check" style="font-size: calc(1.3rem + 5px);"></i>
                                </button>
                            </td>
                            <td style="text-align: center;">
                                <div class="switch" :class="{ 'active': p.ativo }" @click="toggleProductStatus(p)" style="margin: 0 auto;">
                                    <div class="handle"></div>
                                </div>
                            </td>
                            <td style="text-align: center; padding-right: 24px;">
                                <button class="btn" style="background: transparent; color: var(--secondary-text); padding: 8px;" @click="editProduct(p)">
                                    <i class="fa-solid fa-pen-to-square" style="font-size: calc(1.3rem + 5px);"></i>
                                </button>
                                <button class="btn" style="background: transparent; color: var(--error); padding: 8px;" @click="deleteProduct(p)">
                                    <i class="fa-solid fa-trash-can" style="font-size: calc(1.3rem + 5px);"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</section>
