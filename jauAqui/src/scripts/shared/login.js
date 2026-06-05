const container        = document.getElementById('container');
const btnLogin         = document.getElementById('login');
const btnRegister      = document.getElementById('register');
const mobileGoRegister = document.getElementById('mobile-go-register');
const mobileGoLogin    = document.getElementById('mobile-go-login');

if (btnRegister)      btnRegister.addEventListener('click',      () => container.classList.add('active'));
if (btnLogin)         btnLogin.addEventListener('click',         () => container.classList.remove('active'));
if (mobileGoRegister) mobileGoRegister.addEventListener('click', () => container.classList.add('active'));
if (mobileGoLogin)    mobileGoLogin.addEventListener('click',    () => container.classList.remove('active'));

/* ── Login ── */
const formLogin = document.getElementById('formLogin');

if (formLogin) {
    formLogin.addEventListener('submit', async (e) => {
        e.preventDefault();

        const dados = new FormData(formLogin);

        try {
            const resp = await fetch('../../php/auth/login.php', {
                method: 'POST',
                body: dados
            });

            const json = await resp.json();

            if (json.status === 'sucesso') {
                Swal.fire({
                    icon: 'success',
                    title: `Bem-vindo, ${json.nome}!`,
                    text: 'Login realizado com sucesso.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '../../home.html';
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

/* ── Cadastro ── */
const formCadastro = document.querySelector('.sign-up form');

if (formCadastro) {
    formCadastro.addEventListener('submit', async (e) => {
        e.preventDefault();

        const dados = new FormData(formCadastro);
        const erro  = document.getElementById('erro');
        erro.textContent = '';

        try {
            const resp  = await fetch('../../php/auth/cadastrar.php', {
                method: 'POST',
                body: dados
            });
            const texto = await resp.text();

            if (texto === 'sucesso') {
                Swal.fire({
                    icon: 'success',
                    title: 'Conta criada!',
                    text: 'Agora faça login para continuar.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    container.classList.remove('active');
                });
            } else {
                const msgs = {
                    campos_obrigatorios: 'Preencha todos os campos.',
                    email_invalido:      'E-mail inválido.',
                    senhas_diferentes:   'As senhas não coincidem.',
                    senha_curta:         'A senha deve ter pelo menos 6 caracteres.',
                    email_existente:     'Este e-mail já está cadastrado.',
                    erro_banco:          'Erro no servidor. Tente novamente.',
                };
                erro.textContent = msgs[texto] || 'Erro desconhecido.';
            }
        } catch (err) {
            erro.textContent = 'Erro de conexão.';
        }
    });
}