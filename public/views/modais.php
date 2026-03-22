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

    <!-- Modal Confirmar Logout -->
    <div class="modal-overlay" x-show="modal === 'confirmar-logout'" x-transition style="z-index: 10001;">
        <div class="modal-content" @click.away="modal = null" style="max-width: 400px; border-radius: 12px; padding: 32px; text-align: center;">
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
    <div x-show="modal === 'agendamento'" class="modal-content" style="max-width: 800px; border-radius: 12px; padding: 32px;">
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
                             style="position: absolute; top: -4px; right: -4px; background-color: var(--primary); color: #fff; font-size: 0.65rem; font-weight: bold; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #fff;"
                             x-text="dObj.totalQtd"></div>
                    </div>
                </template>
            </div>

            <!-- Detail Box -->
            <div style="margin-top: 16px;">
                <!-- Lista de Agendamentos do dia selecionado -->
                <template x-for="ag in formAgendamento.selectedDayAgendamentos" :key="ag.id_agenda_produto">
                    <div style="border: 1px solid #c4c4c4; border-radius: 8px; padding: 16px; background: #fff; margin-bottom: 8px;">
                        <p style="font-size: 1rem; color: #222; margin-bottom: 6px;">Cliente: <span x-text="ag.cliente_nome || 'Desconhecido'"></span></p>
                        <p style="font-size: 1rem; color: #222; margin-bottom: 6px;">Qtd: <span x-text="ag.quantidade || '1'"></span></p>
                        <p style="font-size: 1rem; color: #222; margin-bottom: 6px;">Nº orçamento: #<span x-text="ag.orcamento ? ag.orcamento.split('-')[0] : 'N/A'"></span></p>
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

<!-- Modal Novo Orçamento -->
<div class="modal-overlay" x-show="modal === 'novo-orcamento'" x-transition style="z-index: 10001;">
    <div class="modal-content" @click.away="modal = null" style="max-width: 850px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
            <div>
                <h2 style="font-size: 1.5rem; color: #111; margin-bottom: 4px;">Cadastrar novo orçamento</h2>
                <p style="color: #6b7280; font-size: 0.95rem;">Insira as informações do seu orçamento nos campos abaixo.</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 80px 1fr; gap: 16px; margin-bottom: 16px;">
            <input type="text" x-model="formOrcamento.numero_sequencial" placeholder="Nº 3" class="form-control" style="background: white;">
            <input type="text" disabled value="Carlos Cesar Lima" class="form-control" style="background: white;">
        </div>

        <div style="display: flex; gap: 16px; margin-bottom: 16px;">
            <select x-model="formOrcamento.cliente_id" class="form-control" style="flex: 1; background: white;">
                <option value="">Selecione o Cliente</option>
                <template x-for="c in clientes" :key="c.id_cliente">
                    <option :value="c.id_cliente" x-text="c.nome"></option>
                </template>
            </select>
            <button class="btn" style="background: transparent; border: 1px solid var(--line-color); padding: 0 16px; border-radius: 8px;">
                <i class="fa-solid fa-plus" style="font-size: 1.4rem; color: #111;"></i>
            </button>
        </div>

        <div style="display: flex; gap: 32px; align-items: center; margin-bottom: 16px; font-size: 0.95rem; color: #374151;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span>Data de criação:</span>
                <span x-text="formOrcamento.dt_criado.split('-').reverse().join('/')"></span>
                <i class="fa-solid fa-calendar-days" style="color: #26b1cb; font-size: 1.4rem;"></i>
            </div>
        </div>
        
        <div style="display: flex; gap: 24px; align-items: center; margin-bottom: 24px; font-size: 0.95rem; color: #374151;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span>Validade do orçamento,</span>
                <input type="number" x-model="formOrcamento.validade_dias" style="width: 40px; border: none; border-bottom: 1px solid #ccc; text-align: center; outline: none;">
                <span>dias:</span>
                <span style="font-weight: 500;">10/04/2026</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calendar-days" style="color: #26b1cb; font-size: 1.2rem;"></i> Data Início: 
                <input type="datetime-local" x-model="formOrcamento.dt_inicio" style="border:none; outline:none; font-family:inherit;">
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calendar-days" style="color: #26b1cb; font-size: 1.2rem;"></i> Data Fim: 
                <input type="datetime-local" x-model="formOrcamento.dt_fim" style="border:none; outline:none; font-family:inherit;">
            </div>
        </div>

        <h3 style="text-align: center; font-size: 1.3rem; margin-bottom: 16px; color: #111; font-weight: 500;">Itens do Orçamento</h3>
        
        <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--line-color);">
            <select x-model="novoItemOrcamento.produto_id" class="form-control" style="flex: 2; height: 45px; background: white;" @change="updateNovoItemValor()">
                <option value="">Selecione Produto</option>
                <template x-for="p in produtos" :key="p.id_produto">
                    <option :value="p.id_produto" x-text="p.nome"></option>
                </template>
            </select>
            <i class="fa-solid fa-calendar-days" style="font-size: 1.6rem; cursor: pointer;"></i>
            
            <div style="text-align: center; font-size: 0.85rem; line-height: 1.2;">
                <span style="color: #6b7280;">Qtd. Prevista</span><br>
                <span style="font-weight: 600; font-size: 0.95rem;">-</span>
            </div>
            
            <div style="display: flex; align-items: center; gap: 12px; font-size: 1.1rem;">
                <i class="fa-solid fa-minus" style="color: #ef4444; cursor: pointer;" @click="if(novoItemOrcamento.quantidade > 1) novoItemOrcamento.quantidade--"></i>
                <span style="font-weight: 600;" x-text="novoItemOrcamento.quantidade"></span>
                <i class="fa-solid fa-plus" style="color: #26b1cb; cursor: pointer;" @click="novoItemOrcamento.quantidade++"></i>
            </div>
            
            <div style="text-align: right; font-size: 0.85rem; line-height: 1.2; min-width: 100px;">
                <span style="color: #6b7280;">Valor do Unit.</span><br>
                <span style="font-weight: 500; font-size: 0.95rem;" x-text="formatMoney(novoItemOrcamento.valor_unit) || 'R$ 0,00'"></span>
            </div>
            <div style="text-align: right; font-size: 0.85rem; line-height: 1.2; min-width: 100px;">
                <span style="color: #6b7280;">Valor Total</span><br>
                <span style="font-weight: 500; font-size: 0.95rem;" x-text="formatMoney(novoItemOrcamento.valor_unit * novoItemOrcamento.quantidade) || 'R$ 0,00'"></span>
            </div>
            
            <i class="fa-solid fa-circle-plus" style="color: #26b1cb; font-size: 1.8rem; cursor: pointer; margin-left: 12px;" @click="addItemOrcamento()"></i>
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
                    <span>Descontos:</span>
                    <input type="number" x-model="formOrcamento.descontos" style="width: 80px; text-align: right; border: none; border-bottom: 1px solid var(--line-color); outline: none; font-weight: 500;" @input="calculateOrcamentoTotal()">
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
