const btn = document.getElementById("btnProcurar");
const select = document.getElementById("linhaSelect");
const diaSelect = document.getElementById("diaSelect");
const resultado = document.getElementById("resultado");

btn.addEventListener("click", async () => {
    const linhaEscolhida = select.value;
    const diaEscolhido = diaSelect.value;

    // Validação de ambos os campos
    if (!linhaEscolhida || !diaEscolhido) {
        resultado.innerHTML = `
            <div class="alert alert-warning text-center">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                Por favor, selecione a <strong>Linha</strong> e o <strong>Dia</strong>.
            </div>`;
        return;
    }

    resultado.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>';

    try {
        const response = await fetch("dados.json");
        if (!response.ok) throw new Error("Erro ao carregar banco de dados.");
        
        const dados = await response.json();
        const linha = dados[linhaEscolhida];

        if (!linha || !linha.horarios[diaEscolhido]) {
            resultado.innerHTML = '<div class="alert alert-danger">Horários não encontrados para esta seleção.</div>';
            return;
        }

        const horarios = linha.horarios[diaEscolhido];

// ... (parte inicial do seu fetch)

const criarCards = (lista) => {
    return `
        <div class="horarios-grid">
            ${lista.map(h => `<div class="horario-card shadow-sm">${h}</div>`).join("")}
        </div>`;
};


          resultado.innerHTML = `
              <div class="resultado-card slide-down" style="background-color: #ffffff; border-radius: 12px; padding: 25px;">
                  <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                      <h2 style="color: #0D1B2A; margin: 0;">${linha.nome}</h2>
                      <span class="badge" style="background-color: #778DA9; padding: 10px;">Linha: ${linha.numeros.join(" / ")}</span>
                  </div>

                  <div class="row">
                      <div class="col-md-6 mb-4">
                          <h5 style="color: #415A77; font-weight: bold;"><i class="fa-solid fa-arrow-right me-2"></i>Ida (Bairro/Centro)</h5>
                          ${criarCards(horarios.ida)}
                      </div>
                      <div class="col-md-6 mb-4">
                          <h5 style="color: #415A77; font-weight: bold;"><i class="fa-solid fa-arrow-left me-2"></i>Volta (Centro/Bairro)</h5>
                          ${criarCards(horarios.volta)}
                      </div>
                  </div>
              </div>
          `;

    } catch (error) {
        resultado.innerHTML = `<div class="alert alert-danger">Erro: ${error.message}</div>`;
    }
});