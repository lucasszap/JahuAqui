
const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});

function irParaPagina() {
  window.location.href = "/src/home/home.html";
}

const senha = document.getElementById("senha");
const confirmar = document.getElementById("confirmarSenha");

function validar(event) {
  event.preventDefault();
  
  var senha = document.getElementById("senha").value;
  var erro = document.getElementById("erro");

  if (senha.length < 6) {
    erro.textContent = "A senha precisa ter no mínimo 6 caracteres.";
  } else {
    erro.style.color = "green";
    erro.textContent = "Senha válida!";
  }
}