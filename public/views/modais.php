<!-- public/views/modais.php -->
<div x-show="modal" class="modal-overlay" x-transition style="display: flex;">
    
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
                    <img :src="'storage-akipede/produtos/' + formProduct.foto.split('/').pop()" style="width: 100%; height: 100%; object-fit: cover;">
                </template>
                <input type="file" x-ref="fileProduct" style="display: none;" @change="uploadFile($event, 'produtos', 'formProduct')">
            </div>

            <!-- Fields -->
            <div style="flex: 1;">
                <div class="form-group">
                    <input type="text" id="prod-nome" class="form-control" placeholder="Nome Produto" x-model="formProduct.nome" required>
                    <label for="prod-nome" class="form-label">Nome Produto</label>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <input type="text" id="prod-venda" class="form-control" placeholder="Valor de Venda" x-model="formProduct.valor_venda" required>
                        <label for="prod-venda" class="form-label">Valor de Venda</label>
                    </div>
                    <div class="form-group">
                        <input type="text" id="prod-custo" class="form-control" placeholder="Valor de Custo" x-model="formProduct.valor_custo" required>
                        <label for="prod-custo" class="form-label">Valor de Custo</label>
                    </div>
                </div>

                <div class="form-group">
                    <select id="prod-cat" x-model="formProduct.id_categoria" class="form-control" required style="padding-top: 24px;">
                        <option value=""></option>
                        <template x-for="cat in categorias" :key="cat.id_categoria">
                            <option :value="cat.id_categoria" x-text="cat.nome"></option>
                        </template>
                    </select>
                    <label for="prod-cat" class="form-label" style="top: -10px; left: 12px; font-size: 0.75rem; font-weight: 700; color: var(--primary); background: white; padding: 0 6px; border-radius: 4px;">Categoria</label>
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
            <textarea id="prod-desc" x-model="formProduct.descricao" rows="4" class="form-control" placeholder="Descrição" style="resize: none;"></textarea>
            <label for="prod-desc" class="form-label">Descrição</label>
        </div>

        <div style="display: flex; gap: 16px; margin-top: 32px;">
            <button @click="modal = null" class="btn" style="flex: 1; background: #f1f4f8; color: var(--secondary-text);">Cancelar</button>
            <button @click="saveProduct()" class="btn btn-primary" style="flex: 1;">Salvar Alterações</button>
        </div>
    </div>

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
                <img :src="formCliente.foto ? 'storage-akipede/usuarios/' + formCliente.foto.split('/').pop() : 'https://via.placeholder.com/84'" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
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

</div>

<style>
    .modal-overlay { 
        position: fixed; top: 0; left: 0; width: 100%; height: 100vh; 
        background: rgba(16, 18, 19, 0.45); /* overlay mais escuro conforme FF */
        backdrop-filter: blur(4px); 
        align-items: center; justify-content: center; z-index: 2000; 
    }
    .modal-content { 
        background: white; padding: 32px; width: 95%; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
    }
</style>
