const container = document.getElementById("noticias");
const btnMais = document.getElementById("btnMais");

const API_KEY = "a1b49a3dfac3a71581dce5ce861ba617";

let pagina = 1;
const porPagina = 6;

// busca Jaú OU Bauru
async function carregarNoticias() {

  const url =
    `https://gnews.io/api/v4/search?q=(jaú OR bauru)&lang=pt&country=br&max=${porPagina}&page=${pagina}&token=${API_KEY}`;

  const res = await fetch(url);
  const data = await res.json();

  if (!data.articles) return;

  data.articles.forEach(noticia => {

    const imagem = noticia.image || "https://via.placeholder.com/400x250?text=Sem+Imagem";

    const card = `
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">

          <img src="${imagem}" class="card-img-top">

          <div class="card-body d-flex flex-column">

            <h6 class="card-title">${noticia.title}</h6>

            <p class="card-text small text-muted">
              ${noticia.description || ""}
            </p>

            <a href="${noticia.url}" target="_blank" class="mt-auto btn btn-noticia btn-sm">
              Ler mais
            </a>

          </div>
        </div>
      </div>
    `;

    container.innerHTML += card;
  });

  pagina++;
}

// primeira carga
carregarNoticias();

// botão ver mais
btnMais.addEventListener("click", carregarNoticias);
