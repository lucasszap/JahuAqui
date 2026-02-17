const btn = document.getElementById("btnProcurar");
const selectLinha = document.getElementById("linhaSelect");
const selectDia = document.getElementById("diaSelect");
const resultado = document.getElementById("resultado");

btn.addEventListener("click", async () => {

  const linhaEscolhida = selectLinha.value;
  const diaEscolhido = selectDia.value;

  if (!linhaEscolhida) {
    resultado.innerHTML = "Selecione uma linha primeiro!";
    return;
  }

  try {
    const response = await fetch("dados.json");
    const dados = await response.json();

    const linha = dados[linhaEscolhida];

    if (!linha) {
      resultado.innerHTML = "Linha não encontrada.";
      return;
    }

    const horariosIda = linha.horarios[diaEscolhido].ida;
    const horariosVolta = linha.horarios[diaEscolhido].volta;

    resultado.innerHTML = `
      <h3 class="mb-3">${linha.nome}</h3>
      <p><strong>Linhas:</strong> ${linha.numeros.join(", ")}</p>

      <h5 class="mt-4">Ida</h5>
      <div class="row row-cols-3 row-cols-md-6 row-cols-lg-8 g-2">
        ${horariosIda.map(h => `
          <div class="col">
            <div class="horario-box">${h}</div>
          </div>
        `).join("")}
      </div>

      <h5 class="mt-4">Volta</h5>
      <div class="row row-cols-3 row-cols-md-6 row-cols-lg-8 g-2">
        ${horariosVolta.map(h => `
          <div class="col">
            <div class="horario-box">${h}</div>
          </div>
        `).join("")}
      </div>
    `;

  } catch (erro) {
    resultado.innerHTML = "Erro ao carregar os dados.";
    console.error(erro);
  }

});
