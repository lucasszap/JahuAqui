<?php
session_start();
include("../config/conexao.php");

$logado      = isset($_SESSION['usuario_id']);
$nomeUsuario = $_SESSION['nome'] ?? '';

$isAdmin = false;
if ($logado) {
    $stmtAdm = $conn->prepare("SELECT IS_ADMIN FROM USUARIOS WHERE ID = ?");
    $stmtAdm->bind_param("i", $_SESSION['usuario_id']);
    $stmtAdm->execute();
    $resAdm  = $stmtAdm->get_result()->fetch_assoc();
    $isAdmin = !empty($resAdm['IS_ADMIN']);
    $stmtAdm->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>JahuAqui - Serviços em Jaú</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <style>
      :root {
        --purple-dark:  #240046;
        --purple-mid:   #5A199B;
        --purple-main:  #6c3fc5;
        --purple-light: #7B2BBE;
        --bg-light:     #f1f2f6;
        --bg-white:     #ffffff;
        --text-dark:    #1a1a2e;
        --text-muted:   #555;
        --radius-sm:    8px;
        --radius-md:    12px;
        --radius-lg:    16px;
        --radius-pill:  30px;
        --shadow-card:  0 4px 16px rgba(0,0,0,.08);
        --shadow-hover: 0 10px 28px rgba(108,63,197,.18);
        --transition:   0.25s ease;
      }

      *, *::before, *::after { box-sizing: border-box; }

      body {
        background-color: var(--bg-light);
        font-family: 'Montserrat', Arial, sans-serif;
        color: var(--text-dark);
        margin: 0;
      }

      /* ── Navbar ── */
      nav a { color: #fff; text-decoration: none; transition: 0.3s; }
      nav a:hover { opacity: 0.7; }
      .logo { height: 40px; width: auto; }
      nav { display: flex; justify-content: space-around; align-items: center; font-family: 'Montserrat', sans-serif; background: #240046; height: 8vh; }
      .nav-list { list-style: none; display: flex; margin-top: 15px; margin-bottom: 0; }
      .nav-buttons { display: flex; gap: 10px; align-items: center; }
      .btn-cadastrar, .btn-login { border: 1px solid #f0f0f0; padding: 10px 20px; border-radius: 60px; color: #fff; font-size: 13px; }
      .btn-cadastrar:hover, .btn-login:hover { background-color: #fff; color: #211b15; opacity: 1; }
      .btn-mobile { display: none; }
      .nav-list a { display: inline-block; transition: all 0.3s ease; font-size: 13px; letter-spacing: 1px; }
      .nav-list a:hover { transform: translateY(-5px); opacity: 0.7; }
      .nav-list li { letter-spacing: 3px; margin-left: 32px; }
      .mobile-menu { display: none; cursor: pointer; }
      .mobile-menu div { width: 32px; height: 2px; background: #fff; margin: 8px; transition: 0.3s; }
      .nav-user { display: none; align-items: center; gap: 10px; }
      .nav-user:hover { opacity: 1 !important; }
      .user-avatar { width: 36px !important; height: 36px !important; border-radius: 50% !important; border: 1px solid #f0f0f0 !important; background: transparent !important; color: #fff !important; font-size: 15px !important; font-weight: 600 !important; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0; user-select: none; letter-spacing: 0 !important; transform: none !important; box-shadow: none !important; transition: none !important; opacity: 1 !important; }
      .user-name { color: #fff !important; font-size: 14px !important; font-weight: 400 !important; letter-spacing: 0 !important; white-space: nowrap; text-decoration: none !important; opacity: 1 !important; transform: none !important; transition: none !important; }
      .btn-sair { border: 1px solid #f0f0f0 !important; background: transparent !important; color: #fff !important; padding: 10px 20px !important; border-radius: 60px !important; font-size: inherit !important; font-family: inherit !important; font-weight: 400 !important; cursor: pointer; transition: background 0.3s, color 0.3s !important; letter-spacing: 0 !important; text-decoration: none !important; }
      .btn-sair:hover { background-color: #fff !important; color: #211b15 !important; }

      @media (max-width: 999px) {
        .nav-list { position: absolute; top: 6vh; right: 0; width: 60vw; height: 52.5vh; background: #240046; flex-direction: column; align-items: center; justify-content: space-around; transform: translateX(100%); transition: transform 0.3s ease-in; z-index: 3; }
        .nav-buttons { display: none; }
        .nav-user { display: none !important; }
        .nav-list li { margin-left: 0; opacity: 0; }
        .mobile-menu { display: block; }
        .btn-mobile { display: list-item; }
      }
      .nav-list.active { transform: translateX(0); }
      @keyframes navLinkFade { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
      .mobile-menu.active .line1 { transform: rotate(-45deg) translate(-8px, 8px); }
      .mobile-menu.active .line2 { opacity: 0; }
      .mobile-menu.active .line3 { transform: rotate(45deg) translate(-5px, -7px); }

      /* ── Hero ── */
      .hero-jahu {
        background: linear-gradient(rgba(36,0,70,0.75), rgba(36,0,70,0.75)),
          url("https://images.unsplash.com/photo-1599423423922-a7aab56c7d75?w=1200") center/cover;
        padding: 100px 0;
        color: #fff;
        border-radius: var(--radius-lg);
        margin-bottom: 2rem;
      }

      a{
        text-transform: uppercase;
      }

      .hero-jahu h1 { color: #fff; font-weight: 800; text-transform: uppercase; letter-spacing: -1px; }
      .hero-jahu p  { color: rgba(255,255,255,.85); }

      /* ── Filtros ── */
      .filtros .btn { border-radius: 999px; font-size: 0.82rem; font-weight: 600; letter-spacing: 0.5px; }

      /* ── Cards ── */
      #grade-servicos { transition: opacity 0.3s ease; }
      #grade-servicos.saindo { opacity: 0; }

      .card-servico {
        background: var(--bg-white);
        border: 1px solid rgba(0,0,0,.09) !important;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
        transition: var(--transition);
      }
      .card-servico:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-hover);
        border-color: var(--purple-light) !important;
      }
      .card-servico .card-img-top { height: 180px; object-fit: cover; }
      .card-foto-placeholder {
        height: 180px;
        background: var(--bg-light);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        border-radius: var(--radius-lg) var(--radius-lg) 0 0;
      }
      .card-servico .card-title { color: var(--text-dark); font-weight: 700; font-size: 15px; }
      .card-servico .card-text  { color: var(--text-muted); }

      /* ── Preview foto ── */
      #previewFoto { max-height: 160px; border-radius: 8px; object-fit: cover; display: none; margin-top: 8px; }

      /* ── Modal tema claro ── */
      .modal-content { background: var(--bg-white); border: 1px solid rgba(0,0,0,.1); border-radius: var(--radius-lg); }
      .modal-header { border-bottom: 1px solid rgba(0,0,0,.08); }
      .modal-header .modal-title { color: var(--text-dark); font-weight: 700; }
      .modal-footer { border-top: 1px solid rgba(0,0,0,.08); }
      .modal-content .form-label { color: var(--text-dark); font-weight: 600; font-size: 13px; }
      .modal-content .form-control,
      .modal-content .form-select {
        background: var(--bg-light);
        border: 1px solid rgba(0,0,0,.12);
        color: var(--text-dark);
        border-radius: var(--radius-md);
      }
      .modal-content .form-control:focus,
      .modal-content .form-select:focus {
        border-color: var(--purple-main);
        box-shadow: 0 0 0 3px rgba(108,63,197,.12);
      }

      /* ── Botão anunciar ── */
      #btnAbrirModal {
        background: var(--purple-dark);
        border: none;
        border-radius: var(--radius-pill);
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-size: 13px;
        padding: 14px 32px;
        color: #fff;
      }
      #btnAbrirModal:hover { background: var(--purple-mid); }

      /* ── Footer ── */
      footer { background: var(--bg-white); border-top: 1px solid rgba(0,0,0,.08); }
    </style>
  </head>

  <body>
    <header>
      <nav>
        <a href="/251256/jauAqui/home.html">
          <img src="/251256/jauAqui/src/assets/images/home/logo.png" alt="JauAqui" class="logo">
        </a>
        <div class="mobile-menu" aria-label="Abrir menu">
          <div class="line1"></div>
          <div class="line2"></div>
          <div class="line3"></div>
        </div>
        <ul class="nav-list">
          <li><a href="/251256/jauAqui/home.html#news">Notícias</a></li>
          <li><a href="/251256/jauAqui/home.html#tourism">Lazer</a></li>
          <li><a href="/251256/jauAqui/home.html#transport">Transporte Público</a></li>
          <li><a href="/251256/jauAqui/php/services/index.php">Serviços</a></li>
          <li class="btn-mobile"><a href="/251256/jauAqui/src/pages/login.html">Cadastrar</a></li>
          <li class="btn-mobile"><a href="/251256/jauAqui/src/pages/login.html">Entrar</a></li>
        </ul>
        <?php if ($logado): ?>
          <div class="nav-user" style="display:flex;">
            <div class="user-avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr($nomeUsuario, 0, 1))) ?></div>
            <span class="user-name"><?= htmlspecialchars(explode(' ', $nomeUsuario)[0]) ?></span>
            <a href="/251256/jauAqui/php/auth/logout.php" class="btn-sair">Sair</a>
          </div>
        <?php else: ?>
          <div class="nav-buttons">
            <a href="/251256/jauAqui/src/pages/login.html" class="btn-cadastrar">Cadastrar</a>
            <a href="/251256/jauAqui/src/pages/login.html" class="btn-login">Entrar</a>
          </div>
        <?php endif; ?>
      </nav>
    </header>

    <main class="container mt-4">
      <section class="hero-jahu text-center mb-5">
        <h1 class="display-4 fw-bold">Serviços em Jaú</h1>
        <p class="lead">Encontre os melhores profissionais da nossa cidade em um só lugar.</p>
        <button id="btnAbrirModal" class="btn btn-lg mt-3"
          data-bs-toggle="modal" data-bs-target="#modalServico" style="display:none">
          Anunciar serviço
        </button>
      </section>

      <div class="filtros d-flex flex-wrap gap-2 mb-4">
        <button class="btn btn-outline-secondary active" data-cat="Todos">Todos</button>
        <button class="btn btn-outline-primary"   data-cat="Manutenção">Manutenção</button>
        <button class="btn btn-outline-warning"   data-cat="Logística">Logística</button>
        <button class="btn btn-outline-info"      data-cat="Limpeza">Limpeza</button>
        <button class="btn btn-outline-success"   data-cat="Beleza">Beleza</button>
        <button class="btn btn-outline-danger"    data-cat="Saúde">Saúde</button>
        <button class="btn btn-outline-secondary" data-cat="Outros">Outros</button>
      </div>

      <div id="grade-servicos" class="row g-4">
        <div class="col-12 text-center py-5" style="color:var(--text-muted)">
          <div class="spinner-border" style="color:var(--purple-main)" role="status"></div>
          <p class="mt-2">Carregando serviços...</p>
        </div>
      </div>
    </main>

    <div class="modal fade" id="modalServico" tabindex="-1" aria-labelledby="tituloModal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="tituloModal">Cadastrar Serviço</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div id="feedbackModal" class="alert d-none" role="alert"></div>
            <form id="formServico" enctype="multipart/form-data" novalidate>
              <div class="mb-3">
                <label for="nomeServico" class="form-label">Nome do serviço <span class="text-danger">*</span></label>
                <input type="text" id="nomeServico" name="nome" class="form-control"
                       placeholder="Ex: Eletricista Residencial" required maxlength="100" />
              </div>
              <div class="mb-3">
                <label for="categoriaServico" class="form-label">Categoria <span class="text-danger">*</span></label>
                <select id="categoriaServico" name="categoria" class="form-select" required>
                  <option value="">Selecione...</option>
                  <option value="Manutenção">Manutenção</option>
                  <option value="Logística">Logística</option>
                  <option value="Limpeza">Limpeza</option>
                  <option value="Beleza">Beleza</option>
                  <option value="Saúde">Saúde</option>
                  <option value="Outros">Outros</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="descricaoServico" class="form-label">Descrição <span class="text-danger">*</span></label>
                <textarea id="descricaoServico" name="descricao" class="form-control"
                          rows="3" placeholder="Descreva seu serviço..." required maxlength="500"></textarea>
              </div>
              <div class="mb-3">
                <label for="telefoneServico" class="form-label">Telefone / WhatsApp <span class="text-danger">*</span></label>
                <input type="tel" id="telefoneServico" name="telefone" class="form-control"
                       placeholder="(14) 9 9999-9999" required maxlength="20" />
              </div>
              <div class="mb-3">
                <label for="fotoServico" class="form-label">Foto do serviço <span class="text-muted small">(opcional, máx 2 MB)</span></label>
                <input type="file" id="fotoServico" name="foto" class="form-control"
                       accept="image/jpeg,image/png,image/webp,image/gif" />
                <img id="previewFoto" src="" alt="Pré-visualização" class="w-100" />
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" id="btnEnviar" class="btn" style="background:var(--purple-dark);color:#fff;border-radius:var(--radius-pill);font-weight:700;border:none;">Publicar Serviço</button>
          </div>
        </div>
      </div>
    </div>

    <footer class="mt-5 py-4 text-center">
      <p class="mb-0" style="color:var(--text-muted)">&copy; 2025 JahuAqui - Conectando Jaú.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      class MobileNavbar {
        constructor(mobileMenu, navList, navLinks) {
          this.mobileMenu  = document.querySelector(mobileMenu);
          this.navList     = document.querySelector(navList);
          this.navLinks    = document.querySelectorAll(navLinks);
          this.activeClass = "active";
          this.handleClick     = this.handleClick.bind(this);
          this.handleLinkClick = this.handleLinkClick.bind(this);
        }
        animateLinks() {
          this.navLinks.forEach((link, index) => {
            link.style.animation
              ? (link.style.animation = "")
              : (link.style.animation = `navLinkFade 0.5s ease forwards ${index / 7 + 0.3}s`);
          });
        }
        handleClick() {
          this.navList.classList.toggle(this.activeClass);
          this.mobileMenu.classList.toggle(this.activeClass);
          this.animateLinks();
        }
        handleLinkClick() {
          if (window.innerWidth <= 768) {
            this.navList.classList.remove(this.activeClass);
            this.mobileMenu.classList.remove(this.activeClass);
            this.animateLinks();
          }
        }
        addClickEvent() {
          this.mobileMenu.addEventListener("click", this.handleClick);
          this.navLinks.forEach(link => link.addEventListener("click", this.handleLinkClick));
        }
        init() { if (this.mobileMenu) this.addClickEvent(); return this; }
      }
      const mobileNavbar = new MobileNavbar(".mobile-menu", ".nav-list", ".nav-list li");
      mobileNavbar.init();

      const usuarioLogado = <?= $logado ? 'true' : 'false' ?>;

      const CORES_CATEGORIA = {
        "Manutenção": "primary", "Logística": "warning", "Limpeza": "info",
        "Beleza": "success", "Saúde": "danger", "Outros": "secondary",
      };

      if (usuarioLogado) document.getElementById('btnAbrirModal').style.display = 'inline-block';

      document.getElementById('fotoServico').addEventListener('change', function () {
        const preview = document.getElementById('previewFoto');
        const file = this.files[0];
        if (file) { preview.src = URL.createObjectURL(file); preview.style.display = 'block'; }
        else preview.style.display = 'none';
      });

      document.getElementById('telefoneServico').addEventListener('input', function () {
        let v = this.value.replace(/\D/g, '').slice(0, 11);
        if (v.length > 10)     v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
        else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d*)$/,   '($1) $2-$3');
        else if (v.length > 2) v = v.replace(/^(\d{2})(\d*)$/,           '($1) $2');
        this.value = v;
      });

      const btnEnviar = document.getElementById('btnEnviar'); if (btnEnviar) btnEnviar.addEventListener('click', async () => {
        const form = document.getElementById('formServico');
        const feedback = document.getElementById('feedbackModal');
        const btn = document.getElementById('btnEnviar');
        if (!form.checkValidity()) { form.reportValidity(); return; }
        btn.disabled = true; btn.textContent = 'Publicando...';
        feedback.className = 'alert d-none';
        try {
          const dados = new FormData(form); const resp = await fetch('/251256/jauAqui/php/services/cadastrar_servico.php', { method: 'POST', body: dados });
          const json = await resp.json();
          if (json.status === 'sucesso') {
            feedback.className = 'alert alert-success';
            feedback.textContent = json.mensagem;
            form.reset();
            document.getElementById('previewFoto').style.display = 'none';
          } else {
            const msgs = {
              nao_logado: 'Você precisa estar logado.', campos_obrigatorios: 'Preencha todos os campos obrigatórios.',
              tipo_imagem_invalido: 'Tipo de imagem inválido.', imagem_grande_demais: 'Imagem muito grande (máx. 2 MB).', erro_banco: 'Erro no servidor.',
            };
            feedback.className = 'alert alert-danger';
            feedback.textContent = msgs[json.mensagem] || json.mensagem;
          }
        } catch (e) {
          feedback.className = 'alert alert-danger';
          feedback.textContent = 'Erro de conexão.';
        }
        btn.disabled = false; btn.textContent = 'Publicar Serviço';
      });

      function montarCard(s) {
        const cor = CORES_CATEGORIA[s.CATEGORIA] ?? 'secondary';
        const link = `https://wa.me/55${s.TELEFONE.replace(/\D/g, '')}`;
        const fotoHtml = s.FOTO
          ? `<img src="${s.FOTO}" class="card-img-top" alt="${s.NOME}" />`
          : `<div class="card-foto-placeholder">🔧</div>`;
        return `
          <div class="col-md-4">
            <div class="card card-servico h-100">
              ${fotoHtml}
              <div class="card-body">
                <span class="badge bg-${cor} mb-2">${s.CATEGORIA}</span>
                <h5 class="card-title">${s.NOME}</h5>
                <p class="card-text small mb-1">${s.DESCRICAO}</p>
                <p class="card-text small text-muted mb-3">👤 ${s.PRESTADOR}</p>
                <div class="d-flex justify-content-between align-items-center">
                  <a href="${link}" target="_blank" rel="noopener" class="btn btn-sm btn-success">📲 WhatsApp</a>
                  <span class="small text-muted">Jaú/SP</span>
                </div>
              </div>
            </div>
          </div>`;
      }

      let categoriaAtiva = 'Todos';

      async function carregarServicos() {
        const grade = document.getElementById('grade-servicos');
        grade.classList.add('saindo');
        await new Promise(r => setTimeout(r, 300));
        grade.innerHTML = `<div class="col-12 text-center py-5" style="color:#555">
          <div class="spinner-border" style="color:#6c3fc5" role="status"></div>
          <p class="mt-2">Carregando serviços...</p></div>`;
        grade.classList.remove('saindo');
        try {
            const lista = await (await fetch(`/251256/jauAqui/php/services/buscar_servicos.php?categoria=${encodeURIComponent(categoriaAtiva)}`)).json();          grade.classList.add('saindo');
          await new Promise(r => setTimeout(r, 150));
          grade.innerHTML = lista.length === 0
            ? `<div class="col-12 text-center py-5" style="color:#555"><p>Nenhum serviço encontrado nesta categoria.</p></div>`
            : lista.map(montarCard).join('');
          grade.classList.remove('saindo');
        } catch (e) {
          grade.innerHTML = `<div class="col-12 text-center py-5 text-danger"><p>Erro ao carregar serviços.</p></div>`;
          grade.classList.remove('saindo');
        }
      }

      document.querySelectorAll('.filtros .btn').forEach(btn => {
        btn.addEventListener('click', () => {
          document.querySelectorAll('.filtros .btn').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          categoriaAtiva = btn.dataset.cat;
          carregarServicos();
        });
      });

      carregarServicos();
    </script>
  </body>
</html>