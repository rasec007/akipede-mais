<!-- public/views/loja.php -->
<section id="aba-loja" x-show="currentTab === 'loja'" x-transition x-cloak>
    <div class="card" style="max-width: 800px; border-radius: 8px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
            <h2 class="font-outfit" style="font-size: 1.5rem; font-weight: 700;">Informações da Loja</h2>
            <button class="btn btn-primary">Salvar Alterações</button>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <div style="grid-column: span 2; display: flex; align-items: center; gap: 24px; margin-bottom: 16px;">
                <div style="width: 100px; height: 100px; border-radius: 8px; overflow: hidden; border: 2px solid var(--line-color);">
                    <img src="img/1__.png" onerror="this.src='img/logo1__.png'" style="width: 100%; height: 100%; object-fit: contain; background: white;">
                </div>
                <button class="btn" style="background: #f1f4f8; color: var(--primary);">Alterar Logo</button>
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--secondary-text); margin-bottom: 8px;">Nome da Loja</label>
                <input type="text" value="Rasec Sushi" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--line-color); background: #f4f6fc; outline: none; font-size: 0.95rem;">
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--secondary-text); margin-bottom: 8px;">CNPJ</label>
                <input type="text" value="00.000.000/0001-00" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--line-color); background: #f4f6fc; outline: none; font-size: 0.95rem;">
            </div>
            <div style="grid-column: span 2;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--secondary-text); margin-bottom: 8px;">Endereço</label>
                <input type="text" value="Av. Principal, 1000 - Centro" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--line-color); background: #f4f6fc; outline: none; font-size: 0.95rem;">
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--secondary-text); margin-bottom: 8px;">WhatsApp para Pedidos</label>
                <input type="text" value="(11) 99999-9999" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--line-color); background: #f4f6fc; outline: none; font-size: 0.95rem;">
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--secondary-text); margin-bottom: 8px;">Link do Catálogo</label>
                <input type="text" value="catalogo.php" readonly style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--line-color); background: #f4f6fc; outline: none; font-size: 0.95rem; color: var(--primary);">
            </div>
        </div>
    </div>
</section>
