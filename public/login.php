<?php
// public/login.php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Akipede Mais</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body style="display: block;">
    <!-- Toast Container -->
    <div class="toast-container" x-data="{ errorMsg: '' }" @show-toast.window="errorMsg = $event.detail; setTimeout(() => errorMsg = '', 5000)" x-show="errorMsg" x-cloak>
        <div class="toast toast-error" :class="{ 'show': errorMsg }">
            <i class="fa-solid fa-circle-exclamation" style="color: var(--error);"></i>
            <span x-text="errorMsg"></span>
        </div>
    </div>

    <div class="auth-bg" x-data="{ view: 'login' }">
        
        <!-- =================== LOGIN =================== -->
        <div class="auth-card" style="padding-top: 4px;" x-show="view === 'login'" x-transition x-cloak>
            <div class="logo-container" style="margin-bottom: 4px; padding: 0;">
                <img src="img/logo.png" alt="Logo" style="height: 300px; object-fit: contain; margin-bottom: 0;">
            </div>
        
            <h2 class="font-outfit" style="margin-bottom: 8px; font-size: 2rem; font-weight: 500;">Logar</h2> 
            <p style="color: var(--secondary-text); margin-bottom: 32px; font-size: 0.95rem;">Use o formulário abaixo para acessar sua conta.</p>

            <form @submit.prevent="fazerLogin($data)" x-data="{ email: '', senha: '', showSenha: false, loading: false }">
                <div class="form-group">
                    <input type="email" id="email" class="form-control" placeholder="Seu e-mail" x-model="email" required>
                    <label for="email" class="form-label">Seu e-mail</label>
                </div>

                <div class="form-group">
                    <input :type="showSenha ? 'text' : 'password'" id="senha" class="form-control" placeholder="Sua senha" x-model="senha" required>
                    <label for="senha" class="form-label">Sua senha</label>
                    <i :class="showSenha ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash'" 
                       @click="showSenha = !showSenha" 
                       style="position: absolute; right: 20px; top: 16px; color: var(--secondary-text); cursor: pointer; z-index: 10;"></i>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
                    <a href="#" style="font-size: 0.9rem; color: var(--secondary-text); text-decoration: none; font-weight: 500;">Esqueceu a senha?</a>
                    <button type="submit" class="btn btn-primary" 
                            style="width: 130px !important; min-width: 130px !important; max-width: 130px !important; height: 50px !important; flex: none !important; padding: 0 !important; font-size: 1rem; border-radius: 8px; transition: none !important;" 
                            :disabled="loading" :style="loading ? 'opacity: 0.7' : ''">
                        <span x-show="!loading">Entrar</span>
                        <span x-show="loading"><i class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                </div>

                <p style="font-size: 1rem; color: var(--secondary-text);">
                    Não tem uma conta? <a href="#" @click.prevent="view = 'register'" style="color: var(--primary-text); font-weight: 700; text-decoration: none;">Criar uma conta</a>
                </p>
            </form>
        </div>

        <!-- =================== CADASTRO =================== -->
        <div class="auth-card" style="padding-top: 4px;" x-show="view === 'register'" x-transition x-cloak>
            <div class="logo-container" style="margin-bottom: 4px; padding: 0;">
                <img src="img/logo.png" alt="Logo" style="height: 180px; object-fit: contain; margin-bottom: 0;">
            </div>
        
            <div style="text-align: center;">
                <h2 class="font-outfit" style="margin-bottom: 8px; font-size: 2rem; font-weight: 500;">Logista</h2>
                <p style="color: var(--secondary-text); margin-bottom: 24px; font-size: 0.95rem;">Use o formulário abaixo para começar.</p>
            </div>

            <form @submit.prevent="fazerCadastro($data, $dispatch)" x-data="{ form: {nome: '', nome_loja: '', email: '', cpf: '', celular: '', apelido: '', senha: '', senha_confirma: ''}, showSenha: false, showSenhaC: false, loading: false }">
                <div class="form-group">
                    <input type="text" id="reg_nome" class="form-control" placeholder="Seu Nome" x-model="form.nome" required>
                    <label for="reg_nome" class="form-label">Seu Nome</label>
                </div>

                <div class="form-group">
                    <input type="text" id="reg_nome_loja" class="form-control" placeholder="Nome da Loja" x-model="form.nome_loja" required>
                    <label for="reg_nome_loja" class="form-label">Nome da Loja</label>
                </div>

                <div class="form-group">
                    <input type="email" id="reg_email" class="form-control" placeholder="Email" x-model="form.email" required>
                    <label for="reg_email" class="form-label">Email</label>
                </div>

                <div class="form-group">
                    <input type="text" id="reg_cpf" class="form-control" placeholder="CPF" x-model="form.cpf" x-mask="999.999.999-99" required>
                    <label for="reg_cpf" class="form-label">CPF</label>
                </div>

                <div class="form-group">
                    <input type="text" id="reg_celular" class="form-control" placeholder="Celular" x-model="form.celular" x-mask="(99) 99999-9999" required>
                    <label for="reg_celular" class="form-label">Celular</label>
                </div>

                <div class="form-group">
                    <input type="text" id="reg_apelido" class="form-control" placeholder="Como gostaria de ser chamado?" x-model="form.apelido">
                    <label for="reg_apelido" class="form-label">Como gostaria de ser chamado?</label>
                </div>

                <div class="form-group">
                    <input :type="showSenha ? 'text' : 'password'" id="reg_senha" class="form-control" placeholder="Sua melhor senha" x-model="form.senha" required>
                    <label for="reg_senha" class="form-label">Sua melhor senha</label>
                    <i :class="showSenha ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash'" 
                       @click="showSenha = !showSenha" 
                       style="position: absolute; right: 20px; top: 16px; color: var(--secondary-text); cursor: pointer; z-index: 10;"></i>
                </div>

                <div class="form-group">
                    <input :type="showSenhaC ? 'text' : 'password'" id="reg_senha_confirma" class="form-control" placeholder="Confirme sua senha" x-model="form.senha_confirma" required>
                    <label for="reg_senha_confirma" class="form-label">Confirme sua senha</label>
                    <i :class="showSenhaC ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash'" 
                       @click="showSenhaC = !showSenhaC" 
                       style="position: absolute; right: 20px; top: 16px; color: var(--secondary-text); cursor: pointer; z-index: 10;"></i>
                </div>

                <div style="display: flex; justify-content: center; margin-bottom: 24px; margin-top: 10px;">
                    <button type="submit" class="btn btn-primary" 
                            style="width: 100% !important; height: 50px !important; font-size: 1rem; border-radius: 8px;" 
                            :disabled="loading" :style="loading ? 'opacity: 0.7' : ''">
                        <span x-show="!loading">Criar conta</span>
                        <span x-show="loading"><i class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                </div>

                <div style="text-align: center;">
                    <p style="font-size: 1rem; color: var(--secondary-text);">
                        já tem uma conta? <a href="#" @click.prevent="view = 'login'" style="color: var(--primary-text); font-weight: 700; text-decoration: none;">Entre aqui</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        function fazerLogin(data) {
            data.loading = true;
            data.errorMsg = '';
            
            fetch('api/auth/login_process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    email: String(data.email), 
                    senha: String(data.senha) 
                })
            })
            .then(res => {
                return res.json().then(dataResp => {
                    if (!res.ok) throw new Error(dataResp.error || 'Erro inesperado no servidor');
                    return dataResp;
                });
            })
            .then(dataResp => {
                window.location.href = 'index.php';
            })
            .catch(err => {
                let msg = err.message;
                if (msg === 'Senha incorreta') msg = 'Ops! A senha informada está incorreta.';
                if (msg === 'Usuário não encontrado') msg = 'E-mail não cadastrado.';
                if (msg === 'Email e senha são obrigatórios') msg = 'Preencha todos os campos.';
                
                window.dispatchEvent(new CustomEvent('show-toast', { detail: msg }));
            })
            .finally(() => {
                data.loading = false;
            });
        }

        function fazerCadastro(data, dispatch) {
            if (data.form.senha !== data.form.senha_confirma) {
                dispatch('show-toast', 'As senhas não coincidem!');
                return;
            }

            data.loading = true;
            
            fetch('api/auth/register_process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data.form)
            })
            .then(res => {
                return res.json().then(dataResp => {
                    if (!res.ok) throw new Error(dataResp.error || 'Erro inesperado no servidor');
                    return dataResp;
                });
            })
            .then(dataResp => {
                // Sucesso: mostrar toast com cor diferente se possível, mas usa a info de momento
                dispatch('show-toast', dataResp.message || 'Conta criada com sucesso!');
                // Reset form and back to login
                data.form = {nome: '', nome_loja: '', email: '', cpf: '', celular: '', apelido: '', senha: '', senha_confirma: ''};
                
                setTimeout(() => {
                    // Alpine's way of going back to the parent component
                    document.querySelector('[x-data=\"{ view: \\\'login\\\' }\"]').__x.$data.view = 'login';
                }, 1500);

            })
            .catch(err => {
                dispatch('show-toast', err.message);
            })
            .finally(() => {
                data.loading = false;
            });
        }
    </script>
</body>
</html>
