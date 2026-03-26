<!-- public/views/modais.php -->
<div x-show="modal" class="modal-overlay" x-transition x-cloak>
    
    <!-- Modal Cadastrar/Editar Produto (Imagem 06) -->
    <div x-show="modal === 'novo-produto' || modal === 'editar-produto'" class="modal-content" style="max-width: 650px; border-radius: 8px; padding: 16px;">
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
    <div x-show="modal === 'novo-categoria'" class="modal-content" style="max-width: 400px; border-radius: 12px; padding: 16px;">
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

    <!-- Modal Confirmar Logout -->
    <div class="modal-overlay" x-show="modal === 'confirmar-logout'" x-transition style="z-index: 10001;">
        <div class="modal-content" @click.away="modal = null" style="max-width: 400px; border-radius: 12px; padding: 16px; text-align: center;">
            <div style="margin-bottom: 24px;">
                <div style="width: 80px; height: 80px; background: rgba(226, 28, 61, 0.1); color: var(--error); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 2rem;">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </div>
                <h2 class="font-outfit" style="font-size: 1.5rem; margin-bottom: 8px;">Deseja realmente sair?</h2>
                <p style="color: var(--secondary-text); font-size: 0.95rem;">Sua sessão será encerrada e você precisará fazer login novamente.</p>
            </div>

            <div style="display: flex; gap: 12px;">
                <button @click="modal = null" class="btn" style="flex: 1; background: #f1f4f8; color: var(--secondary-text);">Fechar</button>
                <button @click="logout()" class="btn btn-primary" style="flex: 1;">Sair</button>
            </div>
        </div>
    </div>

    <!-- [Resto dos modais permanecem iguais...] -->
    <!-- Modal Agendamento (Imagem 08) -->
    <div x-show="modal === 'agendamento'" class="modal-content" style="max-width: 800px; border-radius: 12px; padding: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
            <div>
                <h2 class="font-outfit" style="font-size: 1.5rem; font-weight: 400; color: #000;">Agenda do produto: <span x-text="formAgendamento.nome || 'Hot'"></span></h2>
                <p style="font-size: 0.8rem; color: #000; margin-top: 4px;">selecione para ver o detalhes do agendamento desse produto</p>
            </div>
            <button @click="modal = null" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #555;">&times;</button>
        </div>

        <div style="margin-top: 24px;">
            <!-- Header Calendar -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h3 style="font-size: 1.25rem; font-weight: 500; color: #000;" x-text="getCalendarMonthName()"></h3>
                <div style="display: flex; gap: 16px; color: #555; font-size: 1.1rem;">
                    <i class="fa-regular fa-calendar" style="cursor: pointer;" @click="const d=new Date(); formAgendamento.currentYear=d.getFullYear(); formAgendamento.currentMonth=d.getMonth(); generateCalendar()"></i>
                    <i class="fa-solid fa-chevron-left" style="cursor: pointer;" @click="prevMonthCalendar()"></i>
                    <i class="fa-solid fa-chevron-right" style="cursor: pointer;" @click="nextMonthCalendar()"></i>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; gap: 16px;">
                <!-- Weekdays -->
                <div style="font-size: 0.9rem; color: #000; margin-bottom: 16px;">dom.</div>
                <div style="font-size: 0.9rem; color: #000; margin-bottom: 16px;">seg.</div>
                <div style="font-size: 0.9rem; color: #000; margin-bottom: 16px;">ter.</div>
                <div style="font-size: 0.9rem; color: #000; margin-bottom: 16px;">qua.</div>
                <div style="font-size: 0.9rem; color: #000; margin-bottom: 16px;">qui.</div>
                <div style="font-size: 0.9rem; color: #000; margin-bottom: 16px;">sex.</div>
                <div style="font-size: 0.9rem; color: #000; margin-bottom: 16px;">sáb.</div>

                <template x-for="(dObj, index) in formAgendamento.days" :key="index">
                    <div style="padding: 12px 0; font-size: 0.9rem; cursor: pointer; transition: 0.2s; position: relative;"
                         @click="selectCalendarDay(dObj)"
                         :style="dObj.hasAgendamento 
                            ? (formAgendamento.selectedDay === dObj.dateStr 
                                ? 'color: #fff; background-color: #26b1cb; border-radius: 50%; width: 40px; height: 40px; margin: 0 auto; display: flex; align-items: center; justify-content: center; position: relative;' 
                                : 'color: #333; background-color: #8ed6e5; border-radius: 50%; width: 40px; height: 40px; margin: 0 auto; display: flex; align-items: center; justify-content: center; position: relative;') 
                            : (formAgendamento.selectedDay === dObj.dateStr 
                                ? 'color: #fff; background-color: #555; border-radius: 50%; width: 40px; height: 40px; margin: 0 auto; display: flex; align-items: center; justify-content: center; position: relative;' 
                                : 'color: #333; width: 40px; height: 40px; margin: 0 auto; display: flex; align-items: center; justify-content: center; position: relative;')"
                    >
                        <span x-text="dObj.day || ''"></span>
                        <div x-show="dObj.totalQtd > 0" 
                             style="position: absolute; top: -4px; right: -4px; background-color: var(--primary); color: #000; font-size: 0.75rem; font-weight: 900; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #fff;"
                             x-text="dObj.totalQtd"></div>
                    </div>
                </template>
            </div>

            <!-- Detail Box -->
            <div style="margin-top: 16px;">
                <!-- Lista de Agendamentos do dia selecionado -->
                <template x-for="(ag, index) in formAgendamento.selectedDayAgendamentos" :key="index">
                    <div style="border: 1px solid #c4c4c4; border-radius: 8px; padding: 16px; background: #fff; margin-bottom: 8px;">
                        <p style="font-size: 1rem; color: #222; margin-bottom: 6px; font-weight: 600;">Nº orçamento: <span style="font-weight: 400;">#<span x-text="ag.numero_sequencial || 'N/A'"></span></span></p>
                        <p style="font-size: 1rem; color: #222; margin-bottom: 6px;">Parceiro: <span x-text="ag.parceiro || 'Não informado'"></span></p>
                        <p style="font-size: 1rem; color: #222; margin-bottom: 6px;">Cliente: <span x-text="ag.cliente_nome || 'Desconhecido'"></span></p>
                        <div style="display: flex; gap: 32px; margin-top: 6px; margin-bottom: 6px;">
                            <p style="font-size: 1rem; color: #222;">Qtd: <span style="font-weight: 600;" x-text="ag.quantidade || '1'"></span></p>
                            <p style="font-size: 1rem; color: #222;">Total: <span style="font-weight: 600; color: #26b1cb;" x-text="formatMoney(ag.valor_total || 0)"></span></p>
                        </div>
                        <div style="display: flex; gap: 32px; margin-top: 6px;">
                            <p style="font-size: 1rem; color: #222;">Data Início: <span x-text="formatDateTimeRange(ag.data_inicio)"></span></p>
                            <p style="font-size: 1rem; color: #222;">Data Fim: <span x-text="formatDateTimeRange(ag.data_fim)"></span></p>
                        </div>
                    </div>
                </template>
                <div x-show="formAgendamento.selectedDay && formAgendamento.selectedDayAgendamentos.length === 0" style="text-align: center; padding: 16px; color: var(--secondary-text);">
                    Nenhum agendamento para este dia.
                </div>
                <div x-show="!formAgendamento.selectedDay" style="text-align: center; padding: 16px; color: var(--secondary-text);">
                    Selecione um dia no calendário para ver os detalhes.
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cadastrar/Editar Cliente/Usuário (Imagem 10) -->
    <div x-show="modal === 'novo-cliente' || modal === 'editar-cliente'" class="modal-content" style="max-width: 500px; border-radius: 8px; padding: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 class="font-outfit" style="font-size: 1.5rem; font-weight: 700;" x-text="modal === 'novo-cliente' ? 'Cadastrar Usuário' : 'Atualizar Usuário'"></h2>
            <button @click="modal = null" :disabled="isSavingCliente" style="background: none; border: none; font-size: 1.75rem; cursor: pointer; color: var(--secondary-text);">&times;</button>
        </div>

        <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 24px;">
            <div @click="$refs.fileCliente.click()" style="width: 120px; height: 120px; border: 2px dashed var(--line-color); border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; background: #f4f6fc; overflow: hidden; position: relative;">
                <template x-if="!formCliente.foto">
                    <div style="text-align: center;">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2rem; color: #95a1ac; margin-bottom: 8px;"></i>
                        <span style="font-size: 0.75rem; color: #95a1ac; text-align: center; font-weight: 500; display: block;">Upload da Foto</span>
                    </div>
                </template>
                <template x-if="formCliente.foto">
                    <img :src="formCliente.foto.startsWith('http') ? formCliente.foto : (formCliente.foto.startsWith('storage-akipede') ? formCliente.foto : 'storage-akipede/usuarios/' + formCliente.foto.split('/').pop())" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                </template>
                <input type="file" x-ref="fileCliente" style="display: none;" @change="uploadFile($event, 'usuarios', 'formCliente')">
            </div>
        </div>

        <div class="form-group">
            <input type="text" id="cli-nome" class="form-control" placeholder="Nome Completo" x-model="formCliente.nome" required>
            <label for="cli-nome" class="form-label">Nome Completo</label>
        </div>

        <div class="form-group">
            <input type="text" id="cli-cpf" class="form-control" placeholder="CPF" x-model="formCliente.cpf" x-mask="999.999.999-99" required>
            <label for="cli-cpf" class="form-label">CPF</label>
        </div>

        <div class="form-group">
            <input type="email" id="cli-email" class="form-control" placeholder="E-mail" x-model="formCliente.email" required>
            <label for="cli-email" class="form-label">E-mail</label>
        </div>

        <div class="form-group">
            <input type="text" id="cli-fone" class="form-control" placeholder="Celular (Apenas números)" x-model="formCliente.fone" @input="formCliente.fone = maskPhone($event.target.value)" maxlength="15" required>
            <label for="cli-fone" class="form-label">Celular</label>
        </div>

        <div class="form-group">
            <input type="text" id="cli-logradouro" class="form-control" placeholder="Pesquise o endereço..." x-model="formCliente.logradouro" x-init="mountGooglePlaces($el)">
            <label for="cli-logradouro" class="form-label">Endereço</label>
        </div>

        <div style="display: flex; gap: 16px;">
            <div class="form-group" style="flex: 1;">
                <input type="text" id="cli-num" class="form-control" placeholder="Nº" x-model="formCliente.num">
                <label for="cli-num" class="form-label">Número</label>
            </div>
            <div class="form-group" style="flex: 2;">
                <input type="text" id="cli-complemento" class="form-control" placeholder="Apto, Bloco..." x-model="formCliente.complemento">
                <label for="cli-complemento" class="form-label">Complemento</label>
            </div>
        </div>

        <div class="form-group">
            <input type="password" id="cli-senha" class="form-control" placeholder="Senha" x-model="formCliente.senha" :required="modal === 'novo-cliente'">
            <label for="cli-senha" class="form-label">Senha</label>
        </div>

        <div class="form-group">
            <input type="password" id="cli-senha-confirma" class="form-control" placeholder="Confirmar Senha" x-model="formCliente.senha_confirma" :required="modal === 'novo-cliente'">
            <label for="cli-senha-confirma" class="form-label">Confirmar Senha</label>
        </div>

        <div class="form-group">
            <textarea id="cli-obs" class="form-control" placeholder="Observações extras (Opcional)" x-model="formCliente.obs" rows="2" style="resize: none;"></textarea>
            <label for="cli-obs" class="form-label">Observação</label>
        </div>

        <div class="form-group">
            <select id="cli-perfil" x-model="formCliente.perfil" class="form-control" style="padding-top: 24px;" required>
                <option value="Cliente">Cliente</option>
                <option value="Admin">Administrador</option>
                <option value="Parceiro">Parceiro</option>
            </select>
            <label for="cli-perfil" class="form-label" style="top: -10px; left: 12px; font-size: 0.75rem; font-weight: 700; color: var(--primary); background: white; padding: 0 6px; border-radius: 4px;">Perfil</label>
        </div>

        <div style="display: flex; gap: 16px;">
            <button @click="modal = null" class="btn" style="flex: 1; background: #f1f4f8; color: var(--secondary-text);" :disabled="isSavingCliente">Cancelar</button>
            <button @click="saveCliente()" class="btn btn-primary" style="flex: 1;" :disabled="isSavingCliente">
                <span x-show="!isSavingCliente">Salvar</span>
                <span x-show="isSavingCliente"><i class="fa-solid fa-spinner fa-spin"></i> Salvando...</span>
            </button>
        </div>
    </div>
    <!-- Modal Excluir Produto (NOVO) -->
    <div x-show="modal === 'excluir-produto'" class="modal-content" style="max-width: 450px; text-align: center; border-radius: 8px; padding: 16px;">
        <div style="margin-bottom: 24px;">
            <div style="width: 80px; height: 80px; background: rgba(226, 28, 61, 0.1); color: var(--error); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 2rem;">
                <i class="fa-solid fa-trash-can"></i>
            </div>
            <h2 class="font-outfit" style="font-size: 1.5rem; margin-bottom: 8px;">Excluir Produto?</h2>
            <p style="color: var(--secondary-text); font-size: 0.95rem;">Você está prestes a remover este produto definitivamente do seu catálogo.</p>
        </div>

        <div style="background: #f1f4f8; border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; text-align: left;">
            <img :src="(productToDelete?.foto) || 'img/placeholder.svg'" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover; margin-right: 16px;">
            <div style="flex: 1; overflow: hidden;">
                <p style="font-weight: 700; color: var(--primary-text); white-space: nowrap; text-overflow: ellipsis; overflow: hidden;" x-text="productToDelete?.nome || ''"></p>
                <p style="font-size: 0.85rem; color: var(--secondary-text);" x-text="formatMoney(productToDelete?.valor_venda)"></p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <button @click="modal = null" class="btn" style="background: #e0e3e7; color: var(--primary-text);">Cancelar</button>
            <button @click="confirmDeleteProduct()" class="btn btn-error">Excluir Agora</button>
        </div>
    </div>

    <!-- Modal Excluir Cliente (NOVO) -->
    <div x-show="modal === 'excluir-cliente'" class="modal-content" style="max-width: 450px; text-align: center; border-radius: 8px; padding: 16px;">
        <div style="margin-bottom: 24px;">
            <div style="width: 80px; height: 80px; background: rgba(226, 28, 61, 0.1); color: var(--error); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 2rem;">
                <i class="fa-solid fa-user-xmark"></i>
            </div>
            <h2 class="font-outfit" style="font-size: 1.5rem; margin-bottom: 8px;">Excluir Usuário?</h2>
            <p style="color: var(--secondary-text); font-size: 0.95rem;">Você está prestes a remover este usuário definitivamente.</p>
        </div>

        <div style="background: #f1f4f8; border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; text-align: left;">
            <img :src="(clienteToDelete?.foto) ? (clienteToDelete.foto.startsWith('http') ? clienteToDelete.foto : 'storage-akipede/usuarios/' + clienteToDelete.foto.split('/').pop()) : 'img/placeholder.svg'" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover; margin-right: 16px;">
            <div style="flex: 1; overflow: hidden;">
                <p style="font-weight: 700; color: var(--primary-text); white-space: nowrap; text-overflow: ellipsis; overflow: hidden;" x-text="clienteToDelete?.nome || ''"></p>
                <p style="font-size: 0.85rem; color: var(--secondary-text);" x-text="clienteToDelete?.email || ''"></p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <button @click="modal = null" class="btn" style="background: #e0e3e7; color: var(--primary-text);">Cancelar</button>
            <button @click="confirmDeleteCliente()" class="btn btn-error">Excluir Agora</button>
        </div>
    </div>

    <!-- Modal Excluir Orçamento (NOVO) -->
    <div x-show="modal === 'excluir-orcamento'" class="modal-content" style="max-width: 450px; text-align: center; border-radius: 8px; padding: 16px;">
        <div style="margin-bottom: 24px;">
            <div style="width: 80px; height: 80px; background: rgba(226, 28, 61, 0.1); color: var(--error); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 2rem;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <h2 class="font-outfit" style="font-size: 1.5rem; margin-bottom: 8px;">Excluir Orçamento?</h2>
            <p style="color: var(--secondary-text); font-size: 0.95rem;">Você está prestes a remover este orçamento definitivamente.</p>
        </div>

        <div style="background: #f1f4f8; border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; text-align: left;">
            <div style="width: 60px; height: 60px; background: #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 16px; font-size: 1.5rem; color: #9ca3af;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div style="flex: 1; overflow: hidden;">
                <p style="font-weight: 700; color: var(--primary-text); white-space: nowrap; text-overflow: ellipsis; overflow: hidden;" x-text="'Orçamento #' + (orcamentoToDelete?.numero_sequencial || '')"></p>
                <p style="font-size: 0.85rem; color: var(--secondary-text);" x-text="getClientName(orcamentoToDelete?.cliente_nome) || ''"></p>
                <p style="font-size: 0.85rem; color: var(--alternate); font-weight: 700; margin-top: 4px;" x-text="formatMoney(orcamentoToDelete?.valor_total)"></p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <button @click="modal = null" class="btn" style="background: #e0e3e7; color: var(--primary-text);">Cancelar</button>
            <button @click="confirmDeleteOrcamento()" class="btn btn-error">Excluir Agora</button>
        </div>
    </div>
</div>

<!-- Modal Novo Orçamento -->
<div class="modal-overlay" x-show="modal === 'novo-orcamento'" x-transition style="z-index: 10001;" x-cloak>
    <div class="modal-content" @click.away="modal = null" style="max-width: 850px; border-radius: 12px; padding: 16px; border: none; overflow: hidden;">
        <div style="background-color: #f9fafb; padding: 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h2 style="font-size: 1.5rem; color: #111; margin-bottom: 4px; font-weight: 600;">Cadastrar novo orçamento</h2>
                <p style="color: #6b7280; font-size: 0.95rem;">Preencha os dados abaixo para gerar o orçamento.</p>
            </div>
            <button @click="modal = null" style="background: #e5e7eb; border: none; cursor: pointer; color: #374151; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div style="padding: 32px;">
            <!-- Primeira Linha: Nº e Parceiro -->
            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group-custom">
                    <label>Nº Orçamento</label>
                    <input type="text" x-model="formOrcamento.numero_sequencial" class="form-control" style="background: #f3f4f6; color: #6b7280;" readonly>
                </div>
                <div class="form-group-custom">
                    <label>Parceiro / Vendedor</label>
                    <input type="text" :value="formOrcamento.parceiro_nome" class="form-control" style="background: #f3f4f6; color: #6b7280;" readonly>
                </div>
            </div>

            <!-- Segunda Linha: Cliente -->
            <div style="display: flex; gap: 16px; align-items: flex-end; margin-bottom: 20px;">
                <div class="form-group-custom" style="flex: 1;">
                    <label>Cliente</label>
                    <select x-model="formOrcamento.cliente_id" class="form-control" style="background: white;">
                        <option value="">Selecione o Cliente</option>
                        <template x-for="c in clientes" :key="c.id_cliente">
                            <option :value="c.id_cliente" x-text="c.nome"></option>
                        </template>
                    </select>
                </div>
                <button @click="openModal('novo-cliente')" class="btn" style="background: #f3f4f6; border: 1px solid #d1d5db; height: 45px; width: 45px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                    <i class="fa-solid fa-user-plus" style="color: #374151;"></i>
                </button>
            </div>

            <!-- Terceira Linha: Data Criação e Validade -->
            <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group-custom">
                    <label>Data de Criação</label>
                    <div style="position: relative;">
                        <input type="date" x-model="formOrcamento.dt_criado" class="form-control" @input="calculateValidade()" style="padding-right: 40px;">
                        <i class="fa-solid fa-calendar-day" style="position: absolute; right: 12px; top: 14px; color: #26b1cb;"></i>
                    </div>
                </div>
                <div class="form-group-custom">
                    <label>Validade do orçamento (dias)</label>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <input type="number" x-model="formOrcamento.validade_dias" class="form-control" style="width: 100px;" @input="calculateValidade()">
                        <span style="color: #374151; font-weight: 600; font-size: 0.95rem;">
                            Expira em: <span x-text="formOrcamento.validade ? formOrcamento.validade.split('-').reverse().join('/') : '-'" style="color: #fb5153;"></span>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Quarta Linha: Início e Fim -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 32px;">
                <div class="form-group-custom">
                    <label>Data Início</label>
                    <input type="datetime-local" x-model="formOrcamento.dt_inicio" class="form-control" @input.debounce.500ms="fetchDisponibilidade()">
                </div>
                <div class="form-group-custom">
                    <label>Data Fim</label>
                    <input type="datetime-local" x-model="formOrcamento.dt_fim" class="form-control" @input.debounce.500ms="fetchDisponibilidade()">
                </div>
            </div>

        <h3 style="text-align: center; font-size: 1.3rem; margin-bottom: 16px; color: #111; font-weight: 500;">Itens do Orçamento</h3>
        
        <div style="display: flex; gap: 16px; align-items: flex-end; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--line-color);">
            <div class="form-group-custom" style="flex: 2;">
                <label>Produto</label>
                <select x-model="novoItemOrcamento.produto_id" class="form-control" style="height: 45px; background: white; padding: 0 12px;" @change="updateNovoItemValor()">
                    <option value="">Selecione Produto</option>
                    <template x-for="p in produtos" :key="p.id_produto">
                        <option :value="p.id_produto" x-text="p.nome"></option>
                    </template>
                </select>
            </div>

            <i class="fa-solid fa-calendar-days" style="font-size: 1.6rem; cursor: pointer; color: #26b1cb; margin-bottom: 10px;" @click="fetchDisponibilidade()"></i>
            
            <div style="text-align: center; font-size: 0.85rem; line-height: 1.2; margin-bottom: 10px;">
                <span style="color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 0.75rem;">Qtd. Prevista</span><br>
                <span style="font-weight: 600; font-size: 1rem; color: #111;" x-text="novoItemOrcamento.qtd_prevista"></span>
            </div>
            
            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: #4b5563; text-transform: uppercase;">Qtd.</label>
                <div style="display: flex; align-items: center; gap: 12px; font-size: 1.1rem; border: 1px solid #dbe2e7; padding: 8px 12px; border-radius: 8px;">
                    <i class="fa-solid fa-minus" style="color: #ef4444; cursor: pointer;" @click="if(novoItemOrcamento.quantidade > 1) novoItemOrcamento.quantidade--"></i>
                    <span style="font-weight: 600; min-width: 20px; text-align: center;" x-text="novoItemOrcamento.quantidade"></span>
                    <i class="fa-solid fa-plus" style="color: #26b1cb; cursor: pointer;" @click="const limit = parseInt(novoItemOrcamento.qtd_prevista); if(isNaN(limit) || novoItemOrcamento.quantidade < limit) novoItemOrcamento.quantidade++;"></i>
                </div>
            </div>
            
            <div style="text-align: right; font-size: 0.85rem; line-height: 1.2; min-width: 100px; margin-bottom: 10px;">
                <span style="color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 0.75rem;">Valor Unit.</span><br>
                <span style="font-weight: 600; font-size: 1rem; color: #111;" x-text="formatMoney(novoItemOrcamento.valor_unitario)"></span>
            </div>

            <div style="text-align: right; font-size: 0.85rem; line-height: 1.2; min-width: 100px; margin-bottom: 10px;">
                <span style="color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 0.75rem;">Valor Total</span><br>
                <span style="font-weight: 600; font-size: 1rem; color: #111;" x-text="formatMoney(novoItemOrcamento.quantidade * novoItemOrcamento.valor_unitario)"></span>
            </div>
            
            <i class="fa-solid fa-circle-plus" style="color: #26b1cb; font-size: 2.2rem; cursor: pointer; margin-left: 12px; margin-bottom: 5px;" @click="addItemOrcamento()"></i>
        </div>

        <div style="border: 1px solid var(--line-color); border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #e5e7eb; color: #374151; font-size: 0.9rem;">
                    <tr>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; width: 60px;">AÇÃO</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 500;">PRODUTO</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 500; width: 60px;">QTD.</th>
                        <th style="padding: 12px 16px; text-align: right; font-weight: 500; width: 120px;">V. UNIT.</th>
                        <th style="padding: 12px 16px; text-align: right; font-weight: 500; width: 120px;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in orcamentoItens" :key="index">
                        <tr style="border-bottom: 1px solid var(--line-color);">
                            <td style="padding: 12px 16px; font-size: 1.2rem; text-align: center;">
                                <i class="fa-solid fa-trash-can" style="color: #fb5153; cursor: pointer; background: rgba(251, 81, 83, 0.1); padding: 8px; border-radius: 4px;" @click="removeItemOrcamento(index)"></i>
                            </td>
                            <td style="padding: 12px 16px; color: #111;" x-text="item.nome_produto"></td>
                            <td style="padding: 12px 16px; text-align: center; color: #111;" x-text="item.quantidade"></td>
                            <td style="padding: 12px 16px; text-align: right; color: #111;" x-text="formatMoney(item.valor_unitario)"></td>
                            <td style="padding: 12px 16px; text-align: right; color: #111;" x-text="formatMoney(item.quantidade * item.valor_unitario)"></td>
                        </tr>
                    </template>
                    <tr x-show="orcamentoItens.length === 0">
                        <td colspan="5" style="padding: 24px; text-align: center; color: #6b7280;">Nenhum item adicionado ao orçamento.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 300px; gap: 24px; margin-bottom: 24px;">
            <textarea x-model="formOrcamento.observacoes" placeholder="Observações" style="width: 100%; border: 1px solid var(--line-color); border-radius: 8px; padding: 12px; outline: none; resize: none; min-height: 100px; font-family: inherit; font-size: 0.95rem;"></textarea>
            
            <div style="background: white; border: 1px solid var(--line-color); border-radius: 8px; padding: 16px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-weight: 500; font-size: 0.95rem; color: #111;">
                    <span>Subtotal:</span>
                    <span x-text="formatMoney(formOrcamento.subtotal)"></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 16px; font-weight: 500; font-size: 0.95rem; color: #111;">
                    <span>Desconto:</span>
                    <input type="text" x-model="formOrcamento.descontos_display" @input="applyDiscountMask($event.target.value)" style="width: 120px; text-align: right; border: none; border-bottom: 1px solid var(--line-color); outline: none; font-weight: 500; font-size: 1.1rem; color: #ef4444;" placeholder="R$ 0,00">
                </div>
                <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.25rem; color: #111;">
                    <span>Total Geral:</span>
                    <span x-text="formatMoney(formOrcamento.valor_total)"></span>
                </div>
            </div>
        </div>

        <div style="text-align: center;">
            <button @click="saveOrcamento()" class="btn" style="background-color: #26b1cb; color: white; border: none; padding: 14px 48px; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer;">
                Finalizar orçamento
            </button>
        </div>
    </div>
    </div>
</div>

<!-- Modal Atualizar Status Orçamento (Inspirado no mockup 18) -->
<div x-show="modal === 'atualizar-status-orcamento'" class="modal-overlay" style="z-index: 2500;">
    <div class="modal-content" @click.stop style="max-width: 400px; border-radius: 8px; padding: 32px; background: white; margin: auto; align-self: center;">
        <h2 style="font-size: 1.5rem; color: #111; margin-bottom: 24px; font-weight: 500;">Status</h2>
        
        <div class="form-group-custom" style="margin-bottom: 32px;">
            <label style="color: #6b7280; font-size: 0.90rem; margin-bottom: 8px; display: block; text-transform: none; font-weight: 400;">Selecione o status</label>
            <div style="position: relative;">
                <select x-model="novoStatusOrcamento" class="form-control" style="background: white; width: 100%; border: 1px solid #e5e7eb; padding: 12px 16px; border-radius: 8px; font-size: 1rem; color: #374151; appearance: none; -webkit-appearance: none;">
                    <option value="PENDENTE">Pendente</option>
                    <option value="APROVADO">Aprovado</option>
                    <option value="CANCELADO">Cancelado</option>
                </select>
                <i class="fa-solid fa-chevron-down" style="position: absolute; right: 16px; top: 16px; color: #6b7280; pointer-events: none;"></i>
            </div>
        </div>

        <div style="display: flex; gap: 16px;">
            <button @click="modal = null" class="btn" style="flex: 1; background: #f9fafb; color: #4b5563; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; font-weight: 500; font-size: 1.05rem;">Fechar</button>
            <button @click="confirmUpdateStatusOrcamento()" class="btn" style="flex: 1; background: #fb5153; color: white; border: none; border-radius: 8px; padding: 14px; font-weight: 500; font-size: 1.05rem; box-shadow: 0 4px 10px rgba(251, 81, 83, 0.3);">Confirmar</button>
        </div>
    </div>
</div>

<style>
    .modal-overlay { 
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(16, 18, 19, 0.45);
        backdrop-filter: blur(4px); 
        display: flex; align-items: flex-start; justify-content: center; z-index: 2000; 
        overflow-y: auto;
        padding: 30px 0;
    }
    .modal-content { 
        background: white; padding: 16px; width: 95%; max-height: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
        margin-bottom: 30px;
    }
    .form-group-custom {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .form-group-custom label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #4b5563;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
</style>
