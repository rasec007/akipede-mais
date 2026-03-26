<!-- public/views/loja.php -->
<section id="aba-loja" x-show="currentTab === 'loja'" x-transition x-cloak>
    <div class="card" style="max-width: 800px; border-radius: 8px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
            <h2 class="font-outfit" style="font-size: 1.5rem; font-weight: 700;">Informações da Loja</h2>
            <button @click="saveLoja()" class="btn btn-primary" :disabled="isSavingLoja">
                <span x-show="!isSavingLoja">Salvar Alterações</span>
                <span x-show="isSavingLoja"><i class="fa-solid fa-spinner fa-spin"></i> Salvando...</span>
            </button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 32px;">

            <!-- Link do Catálogo -->
            <div style="border-bottom: 1px solid var(--line-color); padding-bottom: 24px;">
                <label style="display: block; font-size: 0.95rem; font-weight: 600; margin-bottom: 8px;">Publicação do Catálogo Virtual</label>
                <div style="display: flex; gap: 16px; align-items: center;">
                    <div style="position: relative; flex: 1;">
                        <input type="text" x-model="formLoja.url" class="form-control" style="background: #f8fafc; padding-left: 80px;" placeholder="nome-da-sua-loja">
                        <span style="position: absolute; left: 16px; top: 12px; color: var(--secondary-text); font-size: 0.85rem;">URL: /</span>
                    </div>
                    <a :href="'catalogo.php?url=' + formLoja.url" target="_blank" class="btn btn-primary" style="white-space: nowrap;">
                        <i class="fa-solid fa-external-link-alt" style="margin-right: 8px;"></i> Ver minha loja
                    </a>
                </div>
            </div>

            <!-- Paleta de Cores -->
            <div style="border-bottom: 1px solid var(--line-color); padding-bottom: 24px;">
                <label style="display: block; font-size: 0.95rem; font-weight: 600; margin-bottom: 12px;">Selecione a cor do tema da loja</label>
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <template x-for="cor in ['#fdca40', '#ff8552', '#ff4e4e', '#37c6da', '#ce7da5', '#89bbfe', '#0d3b66', '#444e5e', '#2d2d2a', '#82d173']" :key="cor">
                        <div @click="formLoja.cor_tema = cor" 
                             style="width: 40px; height: 40px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; position: relative; border: 2px solid transparent;"
                             :style="(formLoja.cor_tema === cor ? 'border-color: #111; z-index: 2; transform: scale(1.1); box-shadow: 0 4px 12px rgba(0,0,0,0.15); background: ' : 'background: ') + cor">
                            <i class="fa-solid fa-check" x-show="formLoja.cor_tema === cor" style="color: white; font-size: 1rem; filter: drop-shadow(0px 1px 2px rgba(0,0,0,0.5));"></i>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Upload Logo -->
            <div style="border-bottom: 1px solid var(--line-color); padding-bottom: 24px;">
                <label style="display: block; font-size: 0.95rem; font-weight: 600; margin-bottom: 12px;">Logo da loja</label>
                <div style="display: flex; align-items: center; gap: 24px;">
                    <div style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 2px dashed var(--line-color); display: flex; align-items: center; justify-content: center; position: relative; background: #fafafa;">
                        <img :src="formLoja.logo ? (formLoja.logo.startsWith('http') ? formLoja.logo : 'storage-akipede/lojas/' + formLoja.logo.split('/').pop()) : 'img/placeholder.svg'" 
                             style="width: 100%; height: 100%; object-fit: contain; background: white;" x-show="formLoja.logo">
                        <i class="fa-solid fa-image" style="font-size: 2rem; color: #ccc;" x-show="!formLoja.logo"></i>
                    </div>
                    <div>
                        <label class="btn" style="background: #f1f4f8; color: var(--primary); cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-upload"></i> Alterar Logo
                            <input type="file" style="display: none;" accept="image/*" @change="uploadFile($event, 'logo', 'formLoja')">
                        </label>
                        <p style="font-size: 0.8rem; color: var(--secondary-text); margin-top: 8px;">Recomendado: 500x500px, PNG com fundo transparente.</p>
                    </div>
                </div>
            </div>

            <!-- Dados Básicos -->
            <div style="border-bottom: 1px solid var(--line-color); padding-bottom: 24px;">
                <label style="display: block; font-size: 0.95rem; font-weight: 600; margin-bottom: 12px;">Sobre a loja</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div style="position: relative;">
                        <label class="form-label" style="top: -10px; left: 12px; background: white; padding: 0 4px; font-size: 0.8rem; color: var(--primary);">CNPJ</label>
                        <input type="text" x-model="formLoja.cnpj" class="form-control" x-mask="99.999.999/9999-99" placeholder="00.000.000/0000-00">
                    </div>
                    <div style="position: relative;">
                        <label class="form-label" style="top: -10px; left: 12px; background: white; padding: 0 4px; font-size: 0.8rem; color: var(--primary);">Nome da loja</label>
                        <input type="text" x-model="formLoja.nome" class="form-control" placeholder="Razão Social ou Nome Fantasia">
                    </div>
                    <div style="grid-column: span 2; position: relative;">
                        <label class="form-label" style="top: -10px; left: 12px; background: white; padding: 0 4px; font-size: 0.8rem; color: var(--primary);">Descrição</label>
                        <textarea x-model="formLoja.descricao" class="form-control" rows="3" placeholder="Fale um pouco sobre o que a sua loja oferece..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Contatos e Redes Sociais -->
            <div style="border-bottom: 1px solid var(--line-color); padding-bottom: 24px;">
                <label style="display: block; font-size: 0.95rem; font-weight: 600; margin-bottom: 12px;">Contatos</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div style="position: relative;">
                        <label class="form-label" style="top: -10px; left: 12px; background: white; padding: 0 4px; font-size: 0.8rem; color: var(--primary);">WhatsApp (com DDD)</label>
                        <input type="text" x-model="formLoja.whatsapp" class="form-control" x-mask="(99) 99999-9999" placeholder="(00) 00000-0000">
                    </div>
                    <div></div> <!-- Placeholder visual -->
                    <div style="position: relative;">
                        <label class="form-label" style="top: -10px; left: 12px; background: white; padding: 0 4px; font-size: 0.8rem; color: var(--primary);">Link do Instagram</label>
                        <div style="display: flex; align-items: center; background: white; border: 1px solid var(--line-color); border-radius: 8px; overflow: hidden;">
                            <div style="background: #f1f4f8; padding: 12px; border-right: 1px solid var(--line-color); color: var(--secondary-text);"><i class="fa-brands fa-instagram"></i></div>
                            <input type="url" x-model="formLoja.instagram" style="border: none; flex: 1; padding: 12px; outline: none; background: transparent;" placeholder="https://instagram.com/sua_loja">
                        </div>
                    </div>
                    <div style="position: relative;">
                        <label class="form-label" style="top: -10px; left: 12px; background: white; padding: 0 4px; font-size: 0.8rem; color: var(--primary);">Link do Facebook</label>
                        <div style="display: flex; align-items: center; background: white; border: 1px solid var(--line-color); border-radius: 8px; overflow: hidden;">
                            <div style="background: #f1f4f8; padding: 12px; border-right: 1px solid var(--line-color); color: var(--secondary-text);"><i class="fa-brands fa-facebook"></i></div>
                            <input type="url" x-model="formLoja.facebook" style="border: none; flex: 1; padding: 12px; outline: none; background: transparent;" placeholder="https://facebook.com/sua_loja">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Endereço Completo -->
            <div>
                <label style="display: block; font-size: 0.95rem; font-weight: 600; margin-bottom: 12px;">Endereço</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div style="position: relative;">
                         <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 15px; color: var(--secondary-text);"></i>
                         <label class="form-label" style="top: -10px; left: 12px; background: white; padding: 0 4px; font-size: 0.8rem; color: var(--primary);">CEP</label>
                         <input type="text" x-model="formLoja.cep" id="loja-cep" class="form-control" style="padding-left: 36px;" x-mask="99999-999" placeholder="00000-000">
                    </div>
                    <div style="position: relative;">
                         <label class="form-label" style="top: -10px; left: 12px; background: white; padding: 0 4px; font-size: 0.8rem; color: var(--primary);">Logradouro</label>
                         <input type="text" x-model="formLoja.endereco" id="loja-endereco" x-init="mountGooglePlacesLoja($el)" class="form-control" placeholder="Rua, Avenida, etc">
                    </div>
                    <div style="position: relative;">
                         <label class="form-label" style="top: -10px; left: 12px; background: white; padding: 0 4px; font-size: 0.8rem; color: var(--primary);">Nº</label>
                         <input type="text" x-model="formLoja.numero" class="form-control" placeholder="">
                    </div>
                    <div style="position: relative;">
                         <label class="form-label" style="top: -10px; left: 12px; background: white; padding: 0 4px; font-size: 0.8rem; color: var(--primary);">Bairro</label>
                         <input type="text" x-model="formLoja.bairro" id="loja-bairro" class="form-control" placeholder="">
                    </div>
                    <div style="position: relative;">
                         <label class="form-label" style="top: -10px; left: 12px; background: white; padding: 0 4px; font-size: 0.8rem; color: var(--primary);">Complemento</label>
                         <input type="text" x-model="formLoja.complemento" class="form-control" placeholder="Apto, Bloco, etc">
                    </div>
                    <div style="position: relative;">
                         <label class="form-label" style="top: -10px; left: 12px; background: white; padding: 0 4px; font-size: 0.8rem; color: var(--primary);">Cidade</label>
                         <input type="text" x-model="formLoja.cidade" id="loja-cidade" class="form-control" placeholder="">
                    </div>
                    <div style="position: relative;">
                         <label class="form-label" style="top: -10px; left: 12px; background: white; padding: 0 4px; font-size: 0.8rem; color: var(--primary);">Estado</label>
                         <select x-model="formLoja.estado" id="loja-estado" class="form-control">
                            <option value="">Selecione</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                         </select>
                    </div>
                </div>
            </div>

            <button @click="saveLoja()" class="btn btn-primary" style="margin-top: 16px;" :disabled="isSavingLoja">
                <span x-show="!isSavingLoja">Salvar Alterações</span>
                <span x-show="isSavingLoja"><i class="fa-solid fa-spinner fa-spin"></i> Salvando...</span>
            </button>
        </div>
    </div>
</section>
