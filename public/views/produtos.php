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
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Agendamento</th>
                        <th>Status</th>
                        <th style="text-align: right; padding-right: 24px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="p in filteredProducts()" :key="p.id_produto">
                        <tr class="product-row">
                            <td style="padding-left: 24px; display: flex; align-items: center;">
                                <img :src="p.foto || 'https://via.placeholder.com/48'" alt="Prod" style="width: 48px; height: 48px; border-radius: 8px; margin-right: 12px; object-fit: cover;">
                                <span style="font-weight: 600;" x-text="p.nome"></span>
                            </td>
                            <td x-text="p.categoria_nome || '-'"></td>
                            <td style="font-weight: 700; color: var(--alternate);" x-text="'R$ ' + p.valor_venda"></td>
                            <td>
                                <button class="btn" style="background: rgba(4, 219, 126, 0.1); color: var(--primary); padding: 8px; border-radius: 8px;" @click="openAgendamento(p)">
                                    <i class="fa-regular fa-calendar-check"></i>
                                </button>
                            </td>
                            <td>
                                <div class="switch" :class="{ 'active': p.ativo }" @click="p.ativo = !p.ativo">
                                    <div class="handle"></div>
                                </div>
                            </td>
                            <td style="text-align: right; padding-right: 24px;">
                                <button class="btn" style="background: transparent; color: var(--secondary-text); padding: 8px;" @click="editProduct(p)">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn" style="background: transparent; color: var(--error); padding: 8px;" @click="deleteProduct(p)">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</section>
