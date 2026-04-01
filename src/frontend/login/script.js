// ── Animação do painel de toggle (já existia no seu projeto) ────────────────
const container  = document.getElementById('container');
const btnLogin   = document.getElementById('login');
const btnRegister = document.getElementById('register');

if (btnRegister) {
    btnRegister.addEventListener('click', () => container.classList.add('active'));
}
if (btnLogin) {
    btnLogin.addEventListener('click', () => container.classList.remove('active'));
}

// ── Login via Fetch (sem recarregar a página) ────────────────────────────────
const formLogin = document.getElementById('formLogin');

if (formLogin) {
    formLogin.addEventListener('submit', async (e) => {
        e.preventDefault(); // impede o POST tradicional que causa tela branca

        const dados = new FormData(formLogin);

        try {
            const resp = await fetch('login.php', { method: 'POST', body: dados });
            const json = await resp.json();

            if (json.status === 'sucesso') {
                Swal.fire({
                    icon: 'success',
                    title: `Bem-vindo, ${json.nome}!`,
                    text: 'Login realizado com sucesso.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // Redireciona para a home após o alerta fechar
                    window.location.href = '../Home/index.html';
                });
            } else {
                const msgs = {
                    email_nao_cadastrado: 'E-mail não cadastrado.',
                    senha_incorreta:      'Senha incorreta.',
                    campos_obrigatorios:  'Preencha e-mail e senha.',
                };
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao entrar',
                    text: msgs[json.mensagem] || 'Erro desconhecido.',
                });
            }
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Erro de conexão',
                text: 'Verifique sua internet e tente novamente.',
            });
        }
    });
}

// ── Cadastro via Fetch ───────────────────────────────────────────────────────
const formCadastro = document.querySelector('.sign-up form');

if (formCadastro) {
    formCadastro.addEventListener('submit', async (e) => {
        e.preventDefault();

        const dados = new FormData(formCadastro);
        const erro  = document.getElementById('erro');
        erro.textContent = '';

        try {
            const resp = await fetch('cadastrar.php', { method: 'POST', body: dados });
            const texto = await resp.text();

            if (texto === 'sucesso') {
                Swal.fire({
                    icon: 'success',
                    title: 'Conta criada!',
                    text: 'Agora faça login para continuar.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Vai para o painel de login após cadastro
                    container.classList.remove('active');
                });
            } else {
                const msgs = {
                    senhas_diferentes: 'As senhas não coincidem.',
                    senha_curta:       'A senha deve ter pelo menos 6 caracteres.',
                    email_existente:   'Este e-mail já está cadastrado.',
                    erro_banco:        'Erro no servidor. Tente novamente.',
                };
                erro.textContent = msgs[texto] || 'Erro desconhecido.';
            }
        } catch (err) {
            erro.textContent = 'Erro de conexão.';
        }
    });
}