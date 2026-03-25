const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');


registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});



const formCadastro = document.querySelector('.sign-up container form') || document.querySelector('form');


formCadastro.addEventListener('submit', function(e) {
    e.preventDefault(); 

    const formData = new FormData(this);

    fetch('cadastrar.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(resposta => {
        
        const msg = resposta.trim();

        if(msg === 'email_existente'){
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Este e-mail já está cadastrado!',
                confirmButtonColor: '#9D4EDD',
                confirmButtonText: 'Tentar Novamente'
            });
        } 
        else if(msg === 'sucesso'){
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: 'Usuário cadastrado com sucesso!',
                confirmButtonColor: '#9D4EDD'
            }).then(() => {
             
                window.location.href = "/241037/TCC/JahuAqui-main/JahuAqui/src/frontend/Home/index.html";
            });
        }
        else {
           
            Swal.fire('Ops!', msg, 'warning');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Erro', 'Não foi possível conectar ao servidor.', 'error');
    });
});