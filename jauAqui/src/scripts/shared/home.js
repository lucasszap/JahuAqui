
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




const LINHAS = [
  {
    id: "america",
    numero: "01",
    nome: "América (Via Hosp. A. Carvalho)",
    mapsQuery: "América+Via+Hospital+Carvalho+Jaú+SP",
    horarios: {
      uteis:    { ida: ["05:30","06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00"], volta: ["06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00"] },
      sabados:  { ida: ["06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00"], volta: ["07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00"] },
      domingos: { ida: ["07:00","09:00","11:00","13:00","15:00","17:00","19:00","21:00"], volta: ["08:00","10:00","12:00","14:00","16:00","18:00","20:00","22:00"] }
    }
  },
  {
    id: "bela-vista",
    numero: "02",
    nome: "Bela Vista",
    mapsQuery: "Bela+Vista+Jaú+SP",
    horarios: {
      uteis:    { ida: ["05:40","06:40","07:40","08:40","09:40","10:40","11:40","12:40","13:40","14:40","15:40","16:40","17:40","18:40","19:40","20:40"], volta: ["06:20","07:20","08:20","09:20","10:20","11:20","12:20","13:20","14:20","15:20","16:20","17:20","18:20","19:20","20:20","21:20"] },
      sabados:  { ida: ["06:40","08:40","10:40","12:40","14:40","16:40","18:40","20:40"], volta: ["07:20","09:20","11:20","13:20","15:20","17:20","19:20","21:20"] },
      domingos: { ida: ["08:00","11:00","14:00","17:00","20:00"], volta: ["09:00","12:00","15:00","18:00","21:00"] }
    }
  },
  {
    id: "cemiterio",
    numero: "03",
    nome: "Cemitério (Via Hosp. São Judas)",
    mapsQuery: "Cemitério+Hospital+São+Judas+Jaú+SP",
    horarios: {
      uteis:    { ida: ["05:45","06:45","07:45","08:45","09:45","10:45","11:45","12:45","13:45","14:45","15:45","16:45","17:45","18:45","19:45","20:45"], volta: ["06:15","07:15","08:15","09:15","10:15","11:15","12:15","13:15","14:15","15:15","16:15","17:15","18:15","19:15","20:15","21:15"] },
      sabados:  { ida: ["06:45","08:45","10:45","12:45","14:45","16:45","18:45","20:45"], volta: ["07:15","09:15","11:15","13:15","15:15","17:15","19:15","21:15"] },
      domingos: { ida: ["08:00","11:30","15:00","18:00"], volta: ["09:00","12:30","16:00","19:00"] }
    }
  },
  {
    id: "jau-shopping",
    numero: "08",
    nome: "Jaú Shopping",
    mapsQuery: "Shopping+Território+do+Calçado+Jaú+SP",
    horarios: {
      uteis:    { ida: ["06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00"], volta: ["06:30","07:30","08:30","09:30","10:30","11:30","12:30","13:30","14:30","15:30","16:30","17:30","18:30","19:30","20:30","21:30"] },
      sabados:  { ida: ["07:00","09:00","11:00","13:00","15:00","17:00","19:00","21:00"], volta: ["07:30","09:30","11:30","13:30","15:30","17:30","19:30","21:30"] },
      domingos: { ida: ["09:00","12:00","15:00","18:00","21:00"], volta: ["09:30","12:30","15:30","18:30","21:30"] }
    }
  },
  {
    id: "joao-balan",
    numero: "09",
    nome: "João Balan I-II",
    mapsQuery: "João+Balan+Jaú+SP",
    horarios: {
      uteis:    { ida: ["05:30","06:30","07:30","08:30","09:30","10:30","11:30","12:30","13:30","14:30","15:30","16:30","17:30","18:30","19:30","20:30","21:30"], volta: ["06:10","07:10","08:10","09:10","10:10","11:10","12:10","13:10","14:10","15:10","16:10","17:10","18:10","19:10","20:10","21:10","22:10"] },
      sabados:  { ida: ["06:30","08:30","10:30","12:30","14:30","16:30","18:30","20:30"], volta: ["07:10","09:10","11:10","13:10","15:10","17:10","19:10","21:10"] },
      domingos: { ida: ["08:00","11:00","14:00","17:00","20:00"], volta: ["08:40","11:40","14:40","17:40","20:40"] }
    }
  },
  {
    id: "maria-cibele",
    numero: "11",
    nome: "Maria Cibele (Via Jd. Carolina / Santa Casa)",
    mapsQuery: "Jardim+Carolina+Santa+Casa+Jaú+SP",
    horarios: {
      uteis:    { ida: ["05:30","06:30","07:30","08:30","09:30","10:30","11:30","12:30","13:30","14:30","15:30","16:30","17:30","18:30","19:30","20:30"], volta: ["06:15","07:15","08:15","09:15","10:15","11:15","12:15","13:15","14:15","15:15","16:15","17:15","18:15","19:15","20:15","21:15"] },
      sabados:  { ida: ["06:30","08:30","10:30","12:30","14:30","16:30","18:30","20:30"], volta: ["07:15","09:15","11:15","13:15","15:15","17:15","19:15","21:15"] },
      domingos: { ida: ["08:00","11:00","14:00","18:00"], volta: ["09:00","12:00","15:00","19:00"] }
    }
  },
  {
    id: "novo-horizonte",
    numero: "13",
    nome: "Novo Horizonte (Via Maria Izabel / Santa Rosa)",
    mapsQuery: "Novo+Horizonte+Santa+Rosa+Jaú+SP",
    horarios: {
      uteis:    { ida: ["05:45","06:45","07:45","08:45","09:45","10:45","11:45","12:45","13:45","14:45","15:45","16:45","17:45","18:45","19:45","20:45"], volta: ["06:25","07:25","08:25","09:25","10:25","11:25","12:25","13:25","14:25","15:25","16:25","17:25","18:25","19:25","20:25","21:25"] },
      sabados:  { ida: ["06:45","08:45","10:45","12:45","14:45","16:45","18:45","20:45"], volta: ["07:25","09:25","11:25","13:25","15:25","17:25","19:25","21:25"] },
      domingos: { ida: ["08:00","11:00","14:00","17:00","20:00"], volta: ["09:00","12:00","15:00","18:00","21:00"] }
    }
  },
  {
    id: "paraty",
    numero: "21",
    nome: "Paraty (Via Fundação)",
    mapsQuery: "Residencial+Paraty+Terminal+Jaú+SP",
    horarios: {
      uteis:    { ida: ["05:30","06:30","07:30","08:30","09:30","10:30","11:30","12:30","13:30","14:30","15:30","16:30","17:30","18:30","19:30","20:30","21:30"], volta: ["06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","23:00"] },
      sabados:  { ida: ["05:30","06:30","07:30","08:30","09:30","10:30","11:30","12:30","13:30","14:30","15:30","16:30","17:30","18:30","19:30","20:30","21:30"], volta: ["06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","22:45"] },
      domingos: { ida: ["05:30","06:30","07:30","08:30","09:30","10:30","11:30","12:30","13:30","14:30","15:30","16:30","17:30","18:30","19:30","20:30","21:30"], volta: ["06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","22:45"] }
    }
  },
  {
    id: "pe-augusto",
    numero: "17",
    nome: "Pe. Augusto Sani (Via Nova Jaú)",
    mapsQuery: "Nova+Jaú+Terminal+Jaú+SP",
    horarios: {
      uteis:    { ida: ["05:30","06:30","07:30","08:30","09:30","10:30","11:30","12:30","13:30","14:30","15:30","16:30","17:30","18:30","19:30","20:30"], volta: ["06:10","07:10","08:10","09:10","10:10","11:10","12:10","13:10","14:10","15:10","16:10","17:10","18:10","19:10","20:10","21:10"] },
      sabados:  { ida: ["06:30","08:30","10:30","12:30","14:30","16:30","18:30","20:30"], volta: ["07:10","09:10","11:10","13:10","15:10","17:10","19:10","21:10"] },
      domingos: { ida: ["08:00","11:00","14:00","17:00","20:00"], volta: ["09:00","12:00","15:00","18:00","21:00"] }
    }
  },
  {
    id: "santa-casa",
    numero: "20",
    nome: "Santa Casa",
    mapsQuery: "Santa+Casa+Jaú+SP",
    horarios: {
      uteis:    { ida: ["05:30","06:00","06:30","07:00","07:30","08:00","08:30","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","17:30","18:00","18:30","19:00","20:00","21:00"], volta: ["06:00","06:30","07:00","07:30","08:00","08:30","09:00","09:30","10:30","11:30","12:30","13:30","14:30","15:30","16:30","17:30","18:00","18:30","19:00","19:30","20:30","21:30"] },
      sabados:  { ida: ["06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00"], volta: ["06:30","07:30","08:30","09:30","10:30","11:30","12:30","13:30","14:30","15:30","16:30","17:30","18:30","19:30","20:30","21:30"] },
      domingos: { ida: ["07:00","09:00","11:00","13:00","15:00","17:00","19:00","21:00"], volta: ["07:30","09:30","11:30","13:30","15:30","17:30","19:30","21:30"] }
    }
  },
  {
    id: "rodrigues-branco",
    numero: "19",
    nome: "Rodrigues Branco (Via Fundação)",
    mapsQuery: "Rodrigues+Branco+Jaú+SP",
    horarios: {
      uteis:    { ida: ["05:30","06:30","07:30","08:30","09:30","10:30","11:30","12:30","13:30","14:30","15:30","16:30","17:30","18:30","19:30","20:30","21:30"], volta: ["06:10","07:10","08:10","09:10","10:10","11:10","12:10","13:10","14:10","15:10","16:10","17:10","18:10","19:10","20:10","21:10","22:10"] },
      sabados:  { ida: ["06:30","08:30","10:30","12:30","14:30","16:30","18:30","20:30"], volta: ["07:10","09:10","11:10","13:10","15:10","17:10","19:10","21:10"] },
      domingos: { ida: ["08:00","11:00","14:00","17:00","20:00"], volta: ["09:00","12:00","15:00","18:00","21:00"] }
    }
  },
  {
    id: "unoeste",
    numero: "25",
    nome: "Unoeste Via Território do Calçado",
    mapsQuery: "Unoeste+Território+Calçado+Jaú+SP",
    horarios: {
      uteis:    { ida: ["06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00"], volta: ["06:50","07:50","08:50","09:50","10:50","11:50","12:50","13:50","14:50","15:50","16:50","17:50","18:50","19:50","20:50"] },
      sabados:  { ida: ["07:00","09:00","11:00","13:00","15:00","17:00","19:00"], volta: ["07:50","09:50","11:50","13:50","15:50","17:50","19:50"] },
      domingos: { ida: ["09:00","13:00","17:00"], volta: ["09:50","13:50","17:50"] }
    }
  }
];


function iniciarTransporte() {
  const sel = document.getElementById("selectLinha");
  const selDia = document.getElementById("selectDia");
  if (!sel || !selDia) return;

  LINHAS.forEach(l => {
    const opt = document.createElement("option");
    opt.value = l.id;
    opt.textContent = `${l.numero} – ${l.nome}`;
    sel.appendChild(opt);
  });

  sel.addEventListener("change", renderResultado);
  selDia.addEventListener("change", renderResultado);
}

function renderResultado() {
  const linhaId  = document.getElementById("selectLinha").value;
  const tipoDia  = document.getElementById("selectDia").value;
  const result   = document.getElementById("transportResult");
  if (!result) return;

  if (!linhaId) {
    result.innerHTML = `
      <div class="tr-empty">
        <span class="tr-empty-icon">🚌</span>
        <p>Selecione uma linha para ver os horários.</p>
      </div>`;
    return;
  }

  const linha = LINHAS.find(l => l.id === linhaId);
  const { ida, volta } = linha.horarios[tipoDia];
  const agora = new Date();

  function classHora(t) {
    const [h, m] = t.split(":").map(Number);
    const d = new Date(); d.setHours(h, m, 0, 0);
    const diff = d - agora;
    if (diff < 0) return "passado";
    if (diff < 30 * 60 * 1000) return "proximo";
    return "";
  }

  function buildHoras(arr) {
    return arr.map(t => `<span class="tr-hora ${classHora(t)}">${t}</span>`).join("");
  }

  const nomeDir  = linha.nome.split("(")[0].trim();
  const mapsUrl  = `https://www.google.com/maps/search/${linha.mapsQuery}`;

  result.innerHTML = `
    <div class="tr-header">
      <div class="tr-header-left">
        <span class="tr-linha-num">Linha ${linha.numero}</span>
        <p class="tr-linha-nome">${linha.nome}</p>
      </div>
      <a class="tr-maps-link" href="${mapsUrl}" target="_blank" rel="noopener">
        📍 Ver no Maps
      </a>
    </div>

    <div class="tr-body">
      <div class="tr-directions">
        <div>
          <p class="tr-direction-title">⬆ ${nomeDir} → Terminal</p>
          <div class="tr-horarios">${buildHoras(ida)}</div>
        </div>
        <div>
          <p class="tr-direction-title">⬇ Terminal → ${nomeDir}</p>
          <div class="tr-horarios">${buildHoras(volta)}</div>
        </div>
      </div>
    </div>

    <div class="tr-legend">
      <span class="tr-legend-item">
        <span class="tr-legend-dot proximo"></span> Próximos 30 min
      </span>
      <span class="tr-legend-item">
        <span class="tr-legend-dot passado"></span> Já passou
      </span>
      <span class="tr-legend-item">
        <span class="tr-legend-dot normal"></span> Próximos horários
      </span>
    </div>`;
}


document.addEventListener("DOMContentLoaded", () => {
  carregarNoticias();
  iniciarObserver();
  iniciarTransporte();

  if (btnMais) btnMais.addEventListener("click", carregarNoticias);
});