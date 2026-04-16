<?php
session_start();
include("../../login/conexao.php");

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
<html lang="pt-br" data-bs-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>JahuAqui - Serviços em Jaú</title>
    <link href="../../../presets/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../../header/header.css" />
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700,900&display=swap" rel="stylesheet" />

    <style>
            #grade-servicos {
              transition: opacity 0.3s ease; 
            }

              #grade-servicos.saindo {
              opacity: 0;
            }

      .hero-jahu {
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
          url("https://images.unsplash.com/photo-1599423423922-a7aab56c7d75?w=1200") center/cover;
        padding: 100px 0;
        color: white;
        border-radius: 15px;
      }

      .card-servico {
        transition: 0.3s;
        border: 1px solid #333;
      }
      .card-servico:hover {
        transform: scale(1.02);
        border-color: #712cf9;
      }

      .card-servico .card-img-top {
        height: 180px;
        object-fit: cover;
      }

      .card-foto-placeholder {
        height: 180px;
        background: #1e1e2e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        border-radius: 0.375rem 0.375rem 0 0;
      }

      #previewFoto {
        max-height: 160px;
        border-radius: 8px;
        object-fit: cover;
        display: none;
        margin-top: 8px;
      }

      .filtros .btn {
        border-radius: 999px;
        font-size: 0.85rem;
      }
    </style>
  </head>

  <body>
    <header>
      <nav>
        <a class="logo" href="../../Home/index.html">JahuAqui</a>
        <div class="mobile-menu">
          <div class="line1"></div>
          <div class="line2"></div>
          <div class="line3"></div>
        </div>
        <ul class="nav-list">
          <li><a href="../../paginaNoticias/index.html">Notícias</a></li>
          <li><a href="../../transportePublico/index.html">Transporte Público</a></li>
          <li><a href="../../lazer/index.html">Lazer</a></li>
          <li><a href="../../servicos/index.php">Serviços</a></li>
        </ul>

        <div class="nav-buttons">
          <?php if ($logado): ?>
            <span>👤 <?= htmlspecialchars($nomeUsuario) ?></span>
            <a href="../../login/logout.php">| Sair</a>
          <?php else: ?>
            <a href="../../login/login.html">Cadastrar-se</a>
          <?php endif; ?>
        </div>
      </nav>
    </header>

    <main class="container mt-4">
      <section class="hero-jahu text-center mb-5">
        <h1 class="display-4 fw-bold">Serviços em Jaú</h1>
        <p class="lead">Encontre os melhores profissionais da nossa cidade em um só lugar.</p>
        <button
          id="btnAbrirModal"
          class="btn btn-lg btn-primary mt-3"
          data-bs-toggle="modal"
          data-bs-target="#modalServico"
          style="display:none"
        >
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
        <div class="col-12 text-center text-secondary py-5">
          <div class="spinner-border" role="status"></div>
          <p class="mt-2">Carregando serviços...</p>
        </div>
      </div>
    </main>

    <div class="modal fade" id="modalServico" tabindex="-1" aria-labelledby="tituloModal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border border-secondary">

          <div class="modal-header border-secondary">
            <h5 class="modal-title" id="tituloModal">Cadastrar Serviço</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div id="feedbackModal" class="alert d-none" role="alert"></div>

            <form id="formServico" enctype="multipart/form-data" novalidate>

              <div class="mb-3">
                <label for="nomeServico" class="form-label">Nome do serviço <span class="text-danger">*</span></label>
                <input type="text" id="nomeServico" name="nome" class="form-control bg-dark text-white border-secondary"
                       placeholder="Ex: Eletricista Residencial" required maxlength="100" />
              </div>

              <div class="mb-3">
                <label for="categoriaServico" class="form-label">Categoria <span class="text-danger">*</span></label>
                <select id="categoriaServico" name="categoria" class="form-select bg-dark text-white border-secondary" required>
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
                <textarea id="descricaoServico" name="descricao" class="form-control bg-dark text-white border-secondary"
                          rows="3" placeholder="Descreva seu serviço..." required maxlength="500"></textarea>
              </div>

              <div class="mb-3">
                <label for="telefoneServico" class="form-label">Telefone / WhatsApp <span class="text-danger">*</span></label>
                <input type="tel" id="telefoneServico" name="telefone" class="form-control bg-dark text-white border-secondary"
                       placeholder="(14) 9 9999-9999" required maxlength="20" />
              </div>

              <div class="mb-3">
                <label for="fotoServico" class="form-label">Foto do serviço <span class="text-muted small">(opcional, máx 2 MB)</span></label>
                <input type="file" id="fotoServico" name="foto" class="form-control bg-dark text-white border-secondary"
                       accept="image/jpeg,image/png,image/webp,image/gif" />
                <img id="previewFoto" src="" alt="Pré-visualização" class="w-100" />
              </div>

            </form>
          </div>

          <div class="modal-footer border-secondary">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" id="btnEnviar" class="btn btn-primary">Publicar Serviço</button>
          </div>

        </div>
      </div>
    </div>

    <footer class="mt-5 py-4 border-top border-secondary text-center text-secondary">
      <p>&copy; 2025 JahuAqui - Conectando Jaú.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../header/navbar.js"></script>

    <script>
      const usuarioLogado = <?= $logado ? 'true' : 'false' ?>;

      const CORES_CATEGORIA = {
        "Manutenção": "primary",
        "Logística":  "warning",
        "Limpeza":    "info",
        "Beleza":     "success",
        "Saúde":      "danger",
        "Outros":     "secondary",
      };

      if (usuarioLogado) {
        document.getElementById('btnAbrirModal').style.display = 'inline-block';
      }

      document.getElementById('fotoServico').addEventListener('change', function () {
        const preview = document.getElementById('previewFoto');
        const file = this.files[0];
        if (file) {
          preview.src = URL.createObjectURL(file);
          preview.style.display = 'block';
        } else {
          preview.style.display = 'none';
        }
      });

      document.getElementById('telefoneServico').addEventListener('input', function () {
        let v = this.value.replace(/\D/g, '').slice(0, 11);
        if (v.length > 10) {
          v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
        } else if (v.length > 6) {
          v = v.replace(/^(\d{2})(\d{4})(\d*)$/, '($1) $2-$3');
        } else if (v.length > 2) {
          v = v.replace(/^(\d{2})(\d*)$/, '($1) $2');
        }
        this.value = v;
      });

      document.getElementById('btnEnviar').addEventListener('click', async () => {
        const form     = document.getElementById('formServico');
        const feedback = document.getElementById('feedbackModal');
        const btn      = document.getElementById('btnEnviar');

        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }

        btn.disabled = true;
        btn.textContent = 'Publicando...';
        feedback.className = 'alert d-none';

        try {
          const dados = new FormData(form);
          const resp  = await fetch('cadastrar_servico.php', { method: 'POST', body: dados });
          const json  = await resp.json();

          if (json.status === 'sucesso') {
            feedback.className = 'alert alert-success';
            feedback.textContent = json.mensagem;
            form.reset();
            document.getElementById('previewFoto').style.display = 'none';
          } else {
            const msgs = {
              nao_logado:           'Você precisa estar logado.',
              campos_obrigatorios:  'Preencha todos os campos obrigatórios.',
              tipo_imagem_invalido: 'Tipo de imagem inválido. Use JPG, PNG ou WebP.',
              imagem_grande_demais: 'Imagem muito grande (máx. 2 MB).',
              erro_banco:           'Erro no servidor. Tente novamente.',
            };
            feedback.className = 'alert alert-danger';
            feedback.textContent = msgs[json.mensagem] || json.mensagem;
          }
        } catch (e) {
          feedback.className = 'alert alert-danger';
          feedback.textContent = 'Erro de conexão. Verifique sua internet.';
        }

        btn.disabled = false;
        btn.textContent = 'Publicar Serviço';
      });

      function montarCard(s) {
        const cor   = CORES_CATEGORIA[s.CATEGORIA] ?? 'secondary';
        const whats = s.TELEFONE.replace(/\D/g, '');
        const link  = `https://wa.me/55${whats}`;

        const fotoHtml = s.FOTO
          ? `<img src="${s.FOTO}" class="card-img-top" alt="${s.NOME}" />`
          : `<div class="card-foto-placeholder">🔧</div>`;

        return `
          <div class="col-md-4">
            <div class="card card-servico h-100 bg-body-tertiary">
              ${fotoHtml}
              <div class="card-body">
                <span class="badge bg-${cor} mb-2">${s.CATEGORIA}</span>
                <h5 class="card-title">${s.NOME}</h5>
                <p class="card-text text-body-secondary small">${s.DESCRICAO}</p>
                <p class="card-text small text-muted mb-3">👤 ${s.PRESTADOR}</p>
                <div class="d-flex justify-content-between align-items-center">
                  <a href="${link}" target="_blank" rel="noopener" class="btn btn-sm btn-success">
                    📲 WhatsApp
                  </a>
                  <span class="text-muted small">Jaú/SP</span>
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

        grade.innerHTML = `
          <div class="col-12 text-center text-secondary py-5">
            <div class="spinner-border" role="status"></div>
            <p class="mt-2">Carregando serviços...</p>
          </div>`;

        grade.classList.remove('saindo');

        try {
          const url   = `buscar_servicos.php?categoria=${encodeURIComponent(categoriaAtiva)}`;
          const resp  = await fetch(url);
          const lista = await resp.json();

          grade.classList.add('saindo');
          await new Promise(r => setTimeout(r, 150));

          if (lista.length === 0) {
            grade.innerHTML = `
              <div class="col-12 text-center text-secondary py-5">
                <p>Nenhum serviço encontrado nesta categoria.</p>
              </div>`;
          } else {
            grade.innerHTML = lista.map(montarCard).join('');
          }

          grade.classList.remove('saindo');

        } catch (e) {
          grade.innerHTML = `
            <div class="col-12 text-center text-danger py-5">
              <p>Erro ao carregar serviços.</p>
            </div>`;
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