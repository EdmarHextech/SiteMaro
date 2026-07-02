<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Maro Camargo — Diálogo, Educação e Conexões que Transformam';
$page_description = 'Palestras, consultorias e o livro Ponto de Encontro, de Maro Camargo — Doutora em Educação pela USP, especialista em World Café e facilitação de diálogos.';
require __DIR__ . '/includes/header.php';

$proximos_eventos = array_slice(buscar_eventos(), 0, 2);
?>

<section class="hero">
  <div class="container">
    <div class="hero-copy">
      <p class="hero-kicker">Doutora em Educação (USP) · Palestrante · Consultora</p>
      <h1>Conversas que <em>curam</em>, conexões que <em>transformam</em></h1>
      <p class="lead">Maro Camargo ajuda pessoas, times e comunidades a dialogar sobre temas complexos — com metodologias participativas como o World Café — para construir pontos de encontro reais.</p>
      <div class="hero-actions">
        <a href="/agenda.php" class="btn btn-primary">Ver agenda de palestras</a>
        <a href="/livro.php" class="btn btn-outline">Conhecer o livro</a>
      </div>
    </div>
    <div class="hero-portrait">
      <img src="/assets/img/maro-camargo.jpg" alt="Retrato de Maro Camargo sorrindo">
    </div>
  </div>
</section>

<section class="section">
  <div class="container about-grid">
    <div class="about-photo">
      <img src="/assets/img/livro-ponto-de-encontro.jpg" alt="Capa do livro Ponto de Encontro: conversas que curam, conexões que transformam">
    </div>
    <div>
      <p class="eyebrow">Sobre a Maro</p>
      <h2>Diálogo como caminho para transformação</h2>
      <p>Doutora em Educação pela Faculdade de Educação da USP (FEUSP), com estágio de doutorado-sanduíche na Ohio State University (EUA), bolsa Capes/Fulbright, aprofundando a metodologia World Café. Professora universitária, consultora de projetos educativos e empreendedora digital, com décadas de atuação em Terceiro Setor, educação ambiental e formação de educadores.</p>
      <p>Autora do livro <strong>Ponto de Encontro: conversas que curam, conexões que transformam</strong>, reúne ciência, sensibilidade e prática para facilitar diálogos sobre assuntos complexos em organizações, comunidades e times.</p>
      <a href="/sobre.php" class="btn btn-teal">Conhecer a trajetória completa</a>
    </div>
  </div>
</section>

<section class="section section--tint">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Palestras &amp; Consultorias</p>
      <h2>Facilitação de diálogos para temas complexos</h2>
      <p>Formatos sob medida para empresas, instituições de ensino, ONGs e comunidades que querem construir conversas mais verdadeiras.</p>
    </div>
    <div class="grid grid-3">
      <div class="service-card">
        <h3>Palestras</h3>
        <p>Conteúdos sobre diálogo, cultura organizacional, educação e conexão humana, com base em pesquisa acadêmica e vivência de campo.</p>
      </div>
      <div class="service-card">
        <h3>Consultoria</h3>
        <p>Processos participativos para times e organizações que precisam alinhar pessoas em torno de decisões e mudanças complexas.</p>
      </div>
      <div class="service-card">
        <h3>World Café &amp; Facilitação</h3>
        <p>Condução de rodas de diálogo e metodologias colaborativas para comunidades, escolas e projetos socioambientais.</p>
      </div>
    </div>
    <p style="text-align:center; margin-top:36px;">
      <a href="/palestras-consultorias.php" class="btn btn-teal">Ver todos os formatos</a>
    </p>
  </div>
</section>

<section class="section section--dark">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Agenda</p>
      <h2>Próximas palestras e encontros</h2>
      <p>Confira data, local e como se inscrever nos próximos eventos com a Maro.</p>
    </div>

    <?php if (empty($proximos_eventos)): ?>
      <div class="agenda-empty" style="background:rgba(255,255,255,0.06); color:var(--teal-100);">
        Nenhum evento agendado no momento. Volte em breve!
      </div>
    <?php else: ?>
      <div class="agenda-list">
        <?php foreach ($proximos_eventos as $evento): $d = formatar_data_evento($evento['data_evento'], $evento['hora_evento']); ?>
          <div class="agenda-card">
            <div class="agenda-date">
              <span class="dia"><?= e($d['dia']) ?></span>
              <span class="mes"><?= e($d['mes']) ?></span>
            </div>
            <div class="agenda-info">
              <span class="agenda-badge"><?= e(ucfirst($evento['tipo'])) ?></span>
              <h3><?= e($evento['titulo']) ?></h3>
              <div class="agenda-meta">
                <span><strong><?= e($d['dia_semana']) ?></strong> · <?= e($d['hora']) ?>h</span>
                <span>📍 <?= e($evento['local']) ?></span>
              </div>
              <?php if (!empty($evento['vagas'])): ?>
                <span class="agenda-vagas"><?= (int) $evento['vagas'] ?> vagas disponíveis</span>
              <?php endif; ?>
            </div>
            <div class="agenda-actions">
              <?php if (!empty($evento['link_inscricao'])): ?>
                <a href="<?= e($evento['link_inscricao']) ?>" class="btn btn-accent btn-sm" target="_blank" rel="noopener">Inscrever-se</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <p style="text-align:center; margin-top:36px;">
        <a href="/agenda.php" class="btn btn-outline">Ver agenda completa</a>
      </p>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container" style="text-align:center; max-width:680px;">
    <p class="eyebrow">Vamos conversar?</p>
    <h2>Leve o diálogo para o seu time, escola ou comunidade</h2>
    <p style="color:var(--ink-soft); font-size:1.05rem;">Entre em contato para palestras, consultorias ou parcerias.</p>
    <a href="/contato.php" class="btn btn-accent">Falar com a Maro</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
