<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Palestras e Consultorias — Maro Camargo';
$page_description = 'Palestras, consultorias e facilitação de World Café com Maro Camargo para empresas, escolas, ONGs e comunidades.';
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="padding:90px 0 70px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker">Palestras &amp; Consultorias</p>
      <h1>Diálogo aplicado a pessoas, times e organizações</h1>
      <p class="lead" style="margin:0 auto;">Formatos sob medida, unindo pesquisa acadêmica e vivência de campo em educação, cultura organizacional e metodologias participativas.</p>
      <div class="hero-actions" style="justify-content:center;">
        <a href="/contato.php" class="btn btn-primary">Solicitar proposta</a>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-3">
      <div class="service-card">
        <h3>Palestras</h3>
        <p>Conteúdos inspiradores e embasados para eventos corporativos, educacionais e institucionais.</p>
        <ul>
          <li>Diálogo e conexão em tempos de polarização</li>
          <li>Cultura organizacional e escuta ativa</li>
          <li>Educação, comunidade e transformação social</li>
        </ul>
      </div>
      <div class="service-card">
        <h3>Consultoria</h3>
        <p>Acompanhamento de processos participativos para equipes e organizações em momentos de mudança.</p>
        <ul>
          <li>Diagnóstico de cultura e comunicação interna</li>
          <li>Desenho de processos de diálogo e decisão coletiva</li>
          <li>Projetos educativos institucionais</li>
        </ul>
      </div>
      <div class="service-card">
        <h3>World Café &amp; Facilitação</h3>
        <p>Condução de rodas de conversa e metodologias colaborativas para temas complexos.</p>
        <ul>
          <li>Facilitação de World Café presencial ou online</li>
          <li>Mediação de conflitos socioambientais</li>
          <li>Formação de facilitadores e educadores comunitários</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="section section--dark">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Para quem</p>
      <h2>Empresas, escolas, ONGs e comunidades</h2>
      <p style="color:var(--teal-100);">Experiência consolidada em Terceiro Setor, educação e ambientes corporativos, com abordagem participativa e baseada em evidências.</p>
    </div>
    <div class="topics-tags" style="justify-content:center;">
      <span>Educação de adultos</span>
      <span>Formação de professores</span>
      <span>Agenda 21</span>
      <span>Educação Ambiental</span>
      <span>Cultura e lazer</span>
      <span>Educação global</span>
    </div>
  </div>
</section>

<section class="section" style="text-align:center;">
  <div class="container" style="max-width:640px;">
    <p class="eyebrow">Vamos conversar sobre o seu contexto?</p>
    <h2>Solicite uma proposta personalizada</h2>
    <a href="/contato.php" class="btn btn-accent">Falar com a Maro</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
