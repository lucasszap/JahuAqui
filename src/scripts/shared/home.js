/* ================================================================
   JahuAqui — home.js
   Lógica da seção de Notícias e inicialização da home.
   A lógica de Transporte está em transporte.js (carregado após este).
================================================================ */

const container  = document.getElementById("noticias");
const btnMais    = document.getElementById("btnMais");

const API_KEY    = "a1b49a3dfac3a71581dce5ce861ba617";
const POR_PAG    = 3;
let   pagina     = 1;
let   carregando = false;

/* ─── Helpers ─── */

function criarSkeleton() {
  return `
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <div style="height:200px;background:linear-gradient(90deg,#e8e8e8 25%,#f5f5f5 50%,#e8e8e8 75%);
             background-size:200% 100%;animation:shimmer 1.4s infinite;border-radius:16px 16px 0 0;"></div>
        <div class="card-body d-flex flex-column gap-2" style="padding:16px;">
          <div style="height:14px;background:#e8e8e8;border-radius:6px;width:80%;"></div>
          <div style="height:12px;background:#f0f0f0;border-radius:6px;width:100%;"></div>
          <div style="height:12px;background:#f0f0f0;border-radius:6px;width:65%;"></div>
          <div style="height:36px;background:#e8e8e8;border-radius:10px;width:40%;margin-top:auto;"></div>
        </div>
      </div>
    </div>`;
}

function mostrarSkeletons() {
  const id = "skeletons-tmp";
  if (document.getElementById(id)) return;
  const wrap = document.createElement("div");
  wrap.id = id;
  wrap.classList.add("row", "g-4");
  wrap.style.maxWidth = "1200px";
  wrap.style.margin   = "16px auto 0";
  wrap.innerHTML = criarSkeleton().repeat(POR_PAG);
  container.parentElement.insertBefore(wrap, container.nextSibling);
}

function removerSkeletons() {
  const el = document.getElementById("skeletons-tmp");
  if (el) el.remove();
}

function mostrarErro(msg) {
  const div = document.createElement("div");
  div.className = "col-12";
  div.innerHTML = `
    <div style="text-align:center;padding:40px 20px;color:#ff0000;">
      <span style="font-size:40px;"></span>
      <p style="margin:12px 0 0;font-size:14px;">${msg}</p>
    </div>`;
  container.appendChild(div);
}

/* ─── Card de notícia ─── */

function criarCardNoticia(noticia) {
  const imagem = noticia.image ||
    "https://placehold.co/400x250/f1f2f6/555?text=Sem+Imagem";

  const data = noticia.publishedAt
    ? new Date(noticia.publishedAt).toLocaleDateString("pt-BR", { day:"2-digit", month:"short", year:"numeric" })
    : "";

  const descricao = noticia.description
    ? noticia.description.slice(0, 110) + (noticia.description.length > 110 ? "…" : "")
    : "";

  return `
    <div class="col-md-4 fade-in-up">
      <div class="card shadow-sm h-100">
        <img src="${imagem}" class="card-img-top" alt="${noticia.title}" loading="lazy"
             onerror="this.src='https://placehold.co/400x250/f1f2f6/555?text=Sem+Imagem'">
        <div class="card-body d-flex flex-column">
          ${data ? `<span style="font-size:11px;color:#999;font-weight:600;letter-spacing:.5px;text-transform:uppercase;margin-bottom:8px;">${data}</span>` : ""}
          <h6 class="card-title">${noticia.title}</h6>
          <p class="card-text small text-muted">${descricao}</p>
          <a href="${noticia.url}" target="_blank" rel="noopener noreferrer"
             class="mt-auto btn btn-noticia btn-sm">Ler mais</a>
        </div>
      </div>
    </div>`;
}

/* ─── Fetch principal ─── */

async function carregarNoticias() {
  if (carregando) return;
  carregando = true;

  if (btnMais) btnMais.classList.add("loading");
  mostrarSkeletons();

  try {
    const url =
      `https://gnews.io/api/v4/search?q=(jaú OR são paulo)&lang=pt&country=br` +
      `&max=${POR_PAG}&page=${pagina}&token=${API_KEY}`;

    const res  = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    const data = await res.json();
    removerSkeletons();

    if (!data.articles || data.articles.length === 0) {
      if (pagina === 1) mostrarErro("Nenhuma notícia encontrada no momento.");
      if (btnMais) btnMais.style.display = "none";
      return;
    }

    const fragmento = document.createDocumentFragment();
    data.articles.forEach(noticia => {
      const tmp = document.createElement("div");
      tmp.innerHTML = criarCardNoticia(noticia);
      fragmento.appendChild(tmp.firstElementChild);
    });
    container.appendChild(fragmento);

    requestAnimationFrame(() => {
      container.querySelectorAll(".fade-in-up:not(.visible)").forEach((el, i) => {
        setTimeout(() => el.classList.add("visible"), i * 80);
      });
    });

    pagina++;

    if (data.articles.length < POR_PAG) {
      if (btnMais) btnMais.style.display = "none";
    }

  } catch (err) {
    removerSkeletons();
    console.error("Erro ao carregar notícias:", err);
    if (pagina === 1) mostrarErro("Não foi possível carregar as notícias. Tente novamente mais tarde.");
  } finally {
    carregando = false;
    if (btnMais) btnMais.classList.remove("loading");
  }
}

/* ─── Animação fade-in via IntersectionObserver ─── */

function iniciarObserver() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("visible");
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });

  document.querySelectorAll(".fade-in-up").forEach(el => observer.observe(el));

  const mutationObs = new MutationObserver(() => {
    document.querySelectorAll(".fade-in-up:not(.visible)").forEach(el => observer.observe(el));
  });
  mutationObs.observe(container, { childList: true });
}

/* ─── Shimmer keyframe ─── */
(function injetarShimmer() {
  const style = document.createElement("style");
  style.textContent = `
    @keyframes shimmer {
      0%   { background-position: 200% 0; }
      100% { background-position: -200% 0; }
    }`;
  document.head.appendChild(style);
})();

/* ─── Inicialização ─── */

document.addEventListener("DOMContentLoaded", () => {
  carregarNoticias();
  iniciarObserver();
  iniciarTransporte(); // definida em transporte.js (carregado após home.js)

  if (btnMais) btnMais.addEventListener("click", carregarNoticias);
});
