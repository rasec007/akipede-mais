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

    <div class="auth-bg">
        <div class="auth-card" style="padding-top: 4px;">
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
                    Não tem uma conta? <a href="#" style="color: var(--primary-text); font-weight: 700; text-decoration: none;">Criar uma conta</a>
                </p>
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
    </script>
</body>
</html>
