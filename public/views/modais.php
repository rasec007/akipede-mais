<!-- public/views/modais.php -->
<div x-show="modal" class="modal-overlay" x-transition>
    
    <!-- Modal Cadastrar/Editar Produto (Imagem 06) -->
    <div x-show="modal === 'novo-produto' || modal === 'editar-produto'" class="modal-content" style="max-width: 650px; border-radius: 8px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 class="font-outfit" style="font-size: 1.5rem; font-weight: 700;" x-text="modal === 'novo-produto' ? 'Cadastrar Produto' : 'Atualizar Produto'"></h2>
            <button @click="modal = null" style="background: none; border: none; font-size: 1.75rem; cursor: pointer; color: var(--secondary-text);">&times;</button>
        </div>

        <div style="display: flex; gap: 32px;">
            <!-- Foto Upload Area -->
            <div @click="$refs.fileProduct.click()" style="width: 160px; height: 160px; border: 2px dashed var(--line-color); border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; background: #f4f6fc; overflow: hidden; position: relative;">
                <template x-if="!formProduct.foto">
                    <div style="text-align: center;">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2.5rem; color: #95a1ac; margin-bottom: 8px;"></i>
                        <span style="font-size: 0.75rem; color: #95a1ac; text-align: center; font-weight: 500;">Upload da Foto</span>
                    </div>
                </template>
                <template x-if="formProduct.foto">
                    <img :src="formProduct.foto.startsWith('http') ? formProduct.foto : (formProduct.foto.startsWith('storage-akipede') ? formProduct.foto : 'storage-akipede/produtos/' + formProduct.foto.split('/').pop())" style="width: 100%; height: 100%; object-fit: cover;">
                </template>
                <input type="file" x-ref="fileProduct" style="display: none;" @change="uploadFile($event, 'produtos', 'formProduct')">
            </div>

            <!-- Fields -->
            <div style="flex: 1;">
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <input type="text" id="prod-nome" class="form-control" placeholder="Nome Produto" x-model="formProduct.nome" required>
                        <label for="prod-nome" class="form-label">Nome Produto</label>
                    </div>
                    <div class="form-group">
                        <input type="text" id="prod-cod" class="form-control" placeholder="Cód. Produto" x-model="formProduct.cod_produto">
                        <label for="prod-cod" class="form-label">Cód. Produto</label>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <input type="text" id="prod-venda" class="form-control" placeholder="Valor de Venda" x-model="formProduct.valor_venda" required>
                        <label for="prod-venda" class="form-label">Valor de Venda</label>
                    </div>
                    <div class="form-group">
                        <input type="text" id="prod-promo" class="form-control" placeholder="Valor Promo" x-model="formProduct.valor_promocional">
                        <label for="prod-promo" class="form-label">Valor Promo</label>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <input type="text" id="prod-custo" class="form-control" placeholder="Valor de Custo" x-model="formProduct.valor_custo">
                        <label for="prod-custo" class="form-label">Valor de Custo</label>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <select id="prod-cat" x-model="formProduct.id_categoria" class="form-control" required style="padding-top: 24px; padding-right: 50px; appearance: none; -webkit-appearance: none;">
                            <option value=""></option>
                            <template x-for="cat in categorias" :key="cat.id_categoria">
                                <option :value="cat.id_categoria" x-text="cat.nome"></option>
                            </template>
                        </select>
                        <label for="prod-cat" class="form-label" style="top: -10px; left: 12px; font-size: 0.75rem; font-weight: 700; color: var(--primary); background: white; padding: 0 6px; border-radius: 4px;">Categoria</label>
                        <button @click="openModal('novo-categoria')" type="button" 
                                style="position: absolute; right: 10px; top: 12px; background: var(--primary); color: white; border: none; width: 32px; height: 32px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 5; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <i class="fa-solid fa-plus" style="font-size: 0.85rem;"></i>
                        </button>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <span style="font-size: 0.85rem; font-weight: 600; color: var(--secondary-text);">Ativo?</span>
                    <div class="switch" :class="{ 'active': formProduct.ativo }" @click="formProduct.ativo = !formProduct.ativo">
                        <div class="handle"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group" style="margin-top: 8px;">
            <textarea id="prod-desc" x-model="formProduct.descricao" rows="3" class="form-control" placeholder="Descrição" style="resize: none;"></textarea>
            <label for="prod-desc" class="form-label">Descrição</label>
        </div>

        <div style="display: flex; gap: 16px; margin-top: 32px;">
            <button @click="modal = null" class="btn" style="flex: 1; background: #f1f4f8; color: var(--secondary-text);">Cancelar</button>
            <button @click="saveProduct()" class="btn btn-primary" style="flex: 1;" x-text="modal === 'novo-produto' ? 'Cadastrar Novo Produto' : 'Salvar Alterações'"></button>
        </div>
    </div>

    <!-- Modal Nova Categoria (Pop-up inspirado imagem 06.1) -->
    <div x-show="modal === 'novo-categoria'" class="modal-content" style="max-width: 400px; border-radius: 12px; padding: 32px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 class="font-outfit" style="font-size: 1.25rem; font-weight: 700; color: var(--primary-text);">Nova Categoria</h2>
            <button @click="modal = 'novo-produto'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--secondary-text);">&times;</button>
        </div>

        <div class="form-group">
            <input type="text" id="cat-nome" class="form-control" placeholder="Nome da Categoria" x-model="formCategory.nome">
            <label for="cat-nome" class="form-label">Nome da Categoria</label>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button @click="modal = 'novo-produto'" class="btn" style="flex: 1; background: #f1f4f8; color: var(--secondary-text);">Voltar</button>
            <button @click="saveCategory()" class="btn btn-primary" style="flex: 1;">Cadastrar</button>
        </div>
    </div>

    <!-- [Resto dos modais permanecem iguais...] -->
    <!-- Modal Agendamento (Imagem 08) -->
    <div x-show="modal === 'agendamento'" class="modal-content" style="max-width: 400px; border-radius: 12px; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
            <div>
                <h2 class="font-outfit" style="font-size: 1.25rem; font-weight: 600; color: var(--primary-text); margin-bottom: 4px;">Configurar Agendamento</h2>
                <p style="font-size: 0.85rem; color: var(--secondary-text);">Defina como este produto pode ser agendado.</p>
            </div>
            <button @click="modal = null" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--secondary-text);">&times;</button>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--line-color);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 48px; height: 48px; background: rgba(4, 219, 126, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.25rem;">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <div>
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: var(--primary-text);">Permitir Agendamento</h3>
                    <p style="font-size: 0.75rem; color: var(--secondary-text);">Ativar se o produto exigir tempo preparo extra</p>
                </div>
            </div>
            <div class="switch" :class="{ 'active': formAgendamento.ativo }" @click="formAgendamento.ativo = !formAgendamento.ativo">
                <div class="handle"></div>
            </div>
        </div>

        <div class="form-group" x-show="formAgendamento.ativo" x-transition>
            <input type="number" id="aged-tempo" class="form-control" placeholder="Tempo (em minutos)" x-model="formAgendamento.tempo">
            <label for="aged-tempo" class="form-label">Tempo Extra (minutos)</label>
        </div>

        <button @click="saveAgendamento()" class="btn btn-primary" style="width: 100%; border-radius: 8px; font-weight: 600;">Salvar Configurações</button>
    </div>

    <!-- Modal Cadastrar/Editar Cliente/Usuário (Imagem 10) -->
    <div x-show="modal === 'novo-cliente' || modal === 'editar-cliente'" class="modal-content" style="max-width: 500px; border-radius: 8px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 class="font-outfit" style="font-size: 1.5rem; font-weight: 700;" x-text="modal === 'novo-cliente' ? 'Cadastrar Usuário' : 'Atualizar Usuário'"></h2>
            <button @click="modal = null" style="background: none; border: none; font-size: 1.75rem; cursor: pointer; color: var(--secondary-text);">&times;</button>
        </div>

        <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 24px;">
            <div @click="$refs.fileCliente.click()" style="width: 84px; height: 84px; border-radius: 8px; overflow: hidden; border: 3px solid var(--primary); margin-bottom: 8px; cursor: pointer; position: relative;">
                <img :src="formCliente.foto ? (formCliente.foto.startsWith('http') ? formCliente.foto : 'storage-akipede/usuarios/' + formCliente.foto.split('/').pop()) : 'https://via.placeholder.com/84'" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                <input type="file" x-ref="fileCliente" style="display: none;" @change="uploadFile($event, 'usuarios', 'formCliente')">
            </div>
            <button @click="$refs.fileCliente.click()" class="btn" style="background: transparent; color: var(--primary); font-size: 0.85rem; font-weight: 600;">Alterar Foto</button>
        </div>

        <div class="form-group">
            <input type="text" id="cli-nome" class="form-control" placeholder="Nome Completo" x-model="formCliente.nome" required>
            <label for="cli-nome" class="form-label">Nome Completo</label>
        </div>

        <div class="form-group">
            <input type="email" id="cli-email" class="form-control" placeholder="E-mail" x-model="formCliente.email" required>
            <label for="cli-email" class="form-label">E-mail</label>
        </div>

        <div class="form-group">
            <input type="password" id="cli-senha" class="form-control" placeholder="Senha" required>
            <label for="cli-senha" class="form-label">Senha</label>
        </div>

        <div class="form-group">
            <select id="cli-perfil" x-model="formCliente.perfil" class="form-control" style="padding-top: 24px;">
                <option value="Usuário">Usuário</option>
                <option value="Admin">Administrador</option>
            </select>
            <label for="cli-perfil" class="form-label" style="top: -10px; left: 12px; font-size: 0.75rem; font-weight: 700; color: var(--primary); background: white; padding: 0 6px; border-radius: 4px;">Perfil</label>
        </div>

        <div style="display: flex; gap: 16px;">
            <button @click="modal = null" class="btn" style="flex: 1; background: #f1f4f8; color: var(--secondary-text);">Cancelar</button>
            <button @click="saveCliente()" class="btn btn-primary" style="flex: 1;">Salvar</button>
        </div>
    </div>
    <!-- Modal Excluir Produto (NOVO) -->
    <div class="modal-overlay" x-show="modal === 'excluir-produto'" x-transition style="z-index: 10001;">
        <div class="modal-content" @click.away="modal = null" style="max-width: 450px; text-align: center;">
            <div style="margin-bottom: 24px;">
                <div style="width: 80px; height: 80px; background: rgba(226, 28, 61, 0.1); color: var(--error); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 2rem;">
                    <i class="fa-solid fa-trash-can"></i>
                </div>
                <h2 class="font-outfit" style="font-size: 1.5rem; margin-bottom: 8px;">Excluir Produto?</h2>
                <p style="color: var(--secondary-text); font-size: 0.95rem;">Você está prestes a remover este produto definitivamente do seu catálogo.</p>
            </div>

            <div style="background: #f1f4f8; border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; text-align: left;">
                <img :src="productToDelete.foto || 'https://via.placeholder.com/60'" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover; margin-right: 16px;">
                <div style="flex: 1; overflow: hidden;">
                    <p style="font-weight: 700; color: var(--primary-text); white-space: nowrap; text-overflow: ellipsis; overflow: hidden;" x-text="productToDelete.nome"></p>
                    <p style="font-size: 0.85rem; color: var(--secondary-text);" x-text="formatMoney(productToDelete.valor_venda)"></p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <button @click="modal = null" class="btn" style="background: #e0e3e7; color: var(--primary-text);">Cancelar</button>
                <button @click="confirmDeleteProduct()" class="btn btn-error">Excluir Agora</button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-overlay { 
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(16, 18, 19, 0.45);
        backdrop-filter: blur(4px); 
        display: flex; align-items: center; justify-content: center; z-index: 2000; 
    }
    .modal-content { 
        background: white; padding: 32px; width: 95%; max-height: 90vh; overflow-y: auto;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
    }
</style>
