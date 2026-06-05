<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JahuAqui - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="src/styles/layouts/header.css">
  <link rel="stylesheet" href="src/styles/layouts/footer.css">
  <link rel="stylesheet" href="src/styles/pages/home.css">
  <style>
    .nav-user { display: none; align-items: center; gap: 10px; }
    .nav-user:hover { opacity: 1 !important; }
    .services-cards img{
      width: 100% !important;
    }
    .service-icon{
      background-color: #fff !important;
    }
    .user-avatar {
      width: 36px !important; height: 36px !important;
      border-radius: 50% !important;
      border: 1px solid #f0f0f0 !important;
      background: transparent !important;
      color: #fff !important;
      font-size: 15px !important; font-weight: 600 !important;
      display: flex !important; align-items: center !important; justify-content: center !important;
      flex-shrink: 0; user-select: none;
      letter-spacing: 0 !important; transform: none !important;
      box-shadow: none !important; transition: none !important; opacity: 1 !important;
    }
    .user-name {
      color: #fff !important; font-size: 14px !important;
      font-weight: 400 !important; letter-spacing: 0 !important;
      white-space: nowrap; text-decoration: none !important;
      opacity: 1 !important; transform: none !important; transition: none !important;
    }
    .btn-sair {
      border: 1px solid #f0f0f0 !important; background: transparent !important;
      color: #fff !important; padding: 10px 20px !important;
      border-radius: 60px !important; font-size: inherit !important;
      font-family: inherit !important; font-weight: 400 !important;
      cursor: pointer; transition: background 0.3s, color 0.3s !important;
      letter-spacing: 0 !important; text-decoration: none !important;
    }
    .btn-sair:hover { background-color: #fff !important; color: #211b15 !important; }
  </style>
</head>

<body>

<header>
  <nav>
    <img src="src/assets/images/home/logo.png" alt="" class="logo" href="#hero">

    <div class="mobile-menu" aria-label="Abrir menu">
      <div class="line1"></div>
      <div class="line2"></div>
      <div class="line3"></div>
    </div>

    <ul class="nav-list">
      <li><a href="#news">Notícias</a></li>
      <li><a href="#tourism">Lazer</a></li>
      <li><a href="#transport">Transporte Público</a></li>
      <li><a href="#services">Serviços</a></li>
      <li class="btn-mobile"><a href="/src/pages/login.html">Cadastrar</a></li>
      <li class="btn-mobile"><a href="/src/pages/login.html">Entrar</a></li>
    </ul>

    <!-- Botões para usuário NÃO logado -->
    <div class="nav-buttons" id="nav-guest">
      <a href="src/pages/login.html" class="btn-cadastrar">Cadastrar</a>
      <a href="src/pages/login.html" class="btn-login">Entrar</a>
    </div>

    <!-- Área para usuário LOGADO (começa oculta) -->
    <div class="nav-user" id="nav-user" style="display: none;">
      <div class="user-avatar" id="user-avatar"></div>
      <span class="user-name" id="user-name"></span>
      <button class="btn-sair" id="btn-sair">Sair</button>
    </div>
  </nav>
</header>

<section class="hero" id="topo">
  <div class="hero-content">
    <h1>Tudo sobre Jaú<br>em um só lugar</h1>
    <p>Encontre serviços, transporte público, notícias e pontos turísticos de forma simples e rápida.</p>
  </div>
</section>

<section class="news" id="news">
  <div class="section-header">
    <h2>Últimas Notícias</h2>
    <p>Confira as últimas notícias da região.</p>
  </div>

  <div class="card-noticia">
    <div id="noticias" class="row g-4"></div>
    <div class="news-footer">
      <button id="btnMais" class="btn-ver-mais">
        <span>Ver mais notícias</span>
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
      </button>
    </div>
  </div>
</section>

<section class="tourism" id="tourism">
  <div class="section-header">
    <h2>Pontos Turísticos de <span class="brand-name">Jaú</span></h2>
    <p>Explore os principais destinos da cidade e clique para abrir no Google Maps.</p>
  </div>

  <div class="tourism-grid">
    <div class="map-card">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d59453.3!2d-48.559!3d-22.295!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94bf26e13e5b3d45%3A0x9f1a0c07be3c5a0!2sJa%C3%BA%2C%20SP!5e0!3m2!1spt-BR!2sbr!4v1699000000000"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        title="Mapa turístico de Jaú, SP">
      </iframe>
    </div>

    <div class="tourism-cards">

      <a class="tourism-card"
         href="https://www.google.com/maps/search/Museu+Histórico+Voluntários+da+Pátria+Jaú+SP"
         target="_blank" rel="noopener noreferrer">
        <div class="tc-image">
          <img src="src/assets/images/home/museu.jpg" alt="Museu Histórico de Jaú"
               onerror="this.parentElement.innerHTML='<div class=\'tc-image-fallback\' style=\'background:#ede9fe;\'></div>'" />
        </div>
        <div class="tc-body">
          <h4>Museu Histórico de Jaú</h4>
          <p>Acervo sobre a história da cidade desde o século XIX.</p>
        </div>
        <div class="tc-arrow">→</div>
      </a>

      <a class="tourism-card"
         href="https://www.google.com/maps/search/Parque+do+Rio+Jaú+Jaú+SP"
         target="_blank" rel="noopener noreferrer">
        <div class="tc-image">
          <img src="src/assets/images/home/parque.jpg" alt="Parque do Rio Jaú"
               onerror="this.parentElement.innerHTML='<div class=\'tc-image-fallback\' style=\'background:#dcfce7;\'></div>'" />
        </div>
        <div class="tc-body">
          <h4>Parque do Rio Jaú</h4>
          <p>Área de lazer às margens do Rio Jaú, ideal para passeios.</p>
        </div>
        <div class="tc-arrow">→</div>
      </a>

      <a class="tourism-card"
         href="https://www.google.com/maps/search/Catedral+Nossa+Senhora+Patrocínio+Jaú+SP"
         target="_blank" rel="noopener noreferrer">
        <div class="tc-image">
          <img src="src/assets/images/home/catedral.jpeg" alt="Catedral de Jaú"
               onerror="this.parentElement.innerHTML='<div class=\'tc-image-fallback\' style=\'background:#fef9c3;\'></div>'" />
        </div>
        <div class="tc-body">
          <h4>Catedral de Jaú</h4>
          <p>Marco arquitetônico no coração do centro histórico.</p>
        </div>
        <div class="tc-arrow">→</div>
      </a>

    </div>
  </div>
</section>

<section class="transport" id="transport">
  <div class="section-header">
    <h2>Transporte Público de <span class="brand-name">Jaú</span></h2>
    <p>Selecione uma linha e consulte os horários de saída. Dados da Viação Paraty.</p>
  </div>

  <div class="transport-wrapper">

    <div class="transport-filters">
      <div class="tf-group">
        <label for="selectLinha">Selecionar linha</label>
        <div class="tf-select-wrap">
          <select id="selectLinha">
            <option value="">-- Escolha uma linha --</option>
          </select>
          <span class="tf-chevron">▾</span>
        </div>
      </div>

      <div class="tf-group">
        <label for="selectDia">Tipo de dia</label>
        <div class="tf-select-wrap">
          <select id="selectDia">
            <option value="uteis">Dias úteis</option>
            <option value="sabados">Sábados</option>
            <option value="domingos">Domingos / Feriados</option>
          </select>
          <span class="tf-chevron">▾</span>
        </div>
      </div>
    </div>

    <div class="transport-result" id="transportResult">
      <div class="tr-empty">
        <p>Selecione uma linha para ver os horários.</p>
      </div>
    </div>
  </div>
</section>

<section class="services" id="services">
  <div class="section-header">
    <h2>Serviços de <span class="brand-name">Jaú</span></h2>
    <p>Confira os serviços disponíveis na região.</p>
  </div>

<div class="services-cards">

  <a href="php/services/index.php" class="service-horizontal-card">
    <div class="service-icon">
      <img src="src/assets/images/home/mecanico.png" alt="">
    </div>

    <div class="service-content">
      <h3>Mecânico</h3>
      <p>
        Busque por mecânicos de confiança para cuidar do seu veículo com qualidade e rapidez.
      </p>
    </div>

    <div class="service-arrow">
      →
    </div>
  </a>

  <a href="php/services/index.php" class="service-horizontal-card">
    <div class="service-icon">
      <img src="src/assets/images/home/limpeza.png" alt="">
    </div>

    <div class="service-content">
      <h3>Limpeza</h3>
      <p>
        Busque por serviços de limpeza residencial, comercial ou industrial para manter seu ambiente sempre impecável.
      </p>
    </div>

    <div class="service-arrow">
      →
    </div>
  </a>

  <a href="php/services/index.php" class="service-horizontal-card">
    <div class="service-icon">
      <img src="src/assets/images/home/logistica.png" alt="">
    </div>

    <div class="service-content">
      <h3>Logística</h3>
      <p>
        Busque por serviços de logística para transporte de mercadorias, 
        entregas rápidas e soluções eficientes para suas necessidades de transporte.
      </p>
    </div>

    <div class="service-arrow">
      →
    </div>
  </a>

</div>

  <div class="news-footer mt-4">
    <a href="php/services/index.php" class="btn-ver-mais">
      <span>Ver todos os serviços</span>
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
      </svg>
    </a>
  </div>
</section>

<footer>
  <span class="footer-logo">JAHUAQUI</span>
  <ul class="footer-links">
    <li><a href="#">Sobre</a></li>
    <li><a href="#">Contato</a></li>
    <li><a href="#">Termos de uso</a></li>
    <li><a href="#">Privacidade</a></li>
  </ul>
  <span class="footer-copy">© 2025 JahuAqui</span>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="src/scripts/vendor/navbar.js"></script>
<script src="src/scripts/shared/home.js"></script>
<script src="src/scripts/shared/transporte.js"></script>

<script>
(async () => {
  try {
    const resp = await fetch('php/auth/session.php');
    const json = await resp.json();

    if (json.logado) {
      const primeiroNome = json.nome.split(' ')[0];
      const inicial      = primeiroNome.charAt(0).toUpperCase();

      document.getElementById('user-avatar').textContent = inicial;
      document.getElementById('user-name').textContent   = primeiroNome;

      document.getElementById('nav-guest').style.display = 'none';
      document.getElementById('nav-user').style.display  = 'flex';
    }
  } catch (err) {
    // Sessão indisponível — mantém botões de guest
  }

  document.getElementById('btn-sair').addEventListener('click', async () => {
    await fetch('php/auth/logout.php');
    window.location.reload();
  });
})();
</script>

</body>
</html>