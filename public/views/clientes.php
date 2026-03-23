<!-- public/views/clientes.php -->
<section id="aba-usuarios" x-show="currentTab === 'usuarios'" x-transition x-cloak>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div style="position: relative; width: 400px;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 14px; color: var(--secondary-text);"></i>
            <input type="text" placeholder="Buscar usuário..." 
                   style="width: 100%; padding: 12px 16px 12px 48px; border-radius: 8px; border: 1px solid var(--line-color); background: white; outline: none; font-size: 0.95rem;"
                   x-model="searchCliente">
        </div>
        <button @click="openModal('novo-cliente')" class="btn btn-primary">
            <i class="fa-solid fa-plus" style="margin-right: 8px;"></i> Novo Usuário
        </button>
    </div>

    <div class="card" style="padding: 0; overflow: hidden; border-radius: 8px;">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">Nome</th>
                        <th>Apelido</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Perfil</th>
                        <th style="text-align: right; padding-right: 24px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="c in filteredClientes()" :key="c.id_cliente">
                        <tr class="product-row">
                            <td style="padding-left: 24px; display: flex; align-items: center;">
                                <img :src="c.foto || 'img/placeholder.svg'" onerror="this.src='img/placeholder.svg'" alt="User" style="width: 44px; height: 44px; border-radius: 8px; margin-right: 12px; object-fit: cover;">
                                <span style="font-weight: 600;" x-text="c.nome"></span>
                            </td>
                            <td x-text="c.apelido || '-'"></td>
                            <td x-text="c.email || '-'"></td>
                            <td x-text="c.fone || '-'"></td>
                            <td>
                                <span style="background: #f1f4f8; padding: 4px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; color: var(--secondary-text);" x-text="c.perfil || 'Usuário'"></span>
                            </td>
                            <td style="text-align: right; padding-right: 24px;">
                                <button class="btn" style="background: transparent; color: var(--secondary-text); padding: 8px;" @click="editCliente(c)">
                                    <i class="fa-solid fa-pen-to-square" style="font-size: calc(1.3rem + 5px);"></i>
                                </button>
                                <button class="btn" style="background: transparent; color: var(--error); padding: 8px;" @click="deleteCliente(c)">
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
