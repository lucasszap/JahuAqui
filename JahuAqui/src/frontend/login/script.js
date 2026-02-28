const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');


registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});


async function validarCadastro(event) {
    event.preventDefault(); 

    const senha = document.getElementById("senha").value;
    const confirmar = document.getElementById("confirmarSenha").value;


    if (senha !== confirmar) {
        alert('As senhas não coincidem!');
        return; 
    }


    const form = event.target; 
    const dados = new FormData(form);

    try {

        const resposta = await fetch("cadastro.php", {
            method: "POST",
            body: dados
        });

        const resultado = await resposta.json(); 

        if (resultado.trim() === "sucesso") {
            alert(resultado.mensagem);
            setTimeout(() => {
                window.location.href = "/src/frontend/home/index";
            }, 1000);
        } else {
            alert('Erro no servidor: ' + resultado);
        }
    } catch (erro) {
        alert('Não foi possível conectar ao servidor.');
    }
}