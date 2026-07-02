<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Sobre Maro Camargo — Trajetória e Formação';
$page_description = 'Conheça a trajetória acadêmica e profissional de Maro Camargo: doutorado pela USP, doutorado-sanduíche na Ohio State University, mestrado em Ciência Ambiental e atuação em educação, diálogo e Terceiro Setor.';
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="padding:80px 0 70px;">
  <div class="container" style="grid-template-columns: 1fr; text-align:center;">
    <div>
      <p class="hero-kicker">Sobre</p>
      <h1>Maria Eugênia "Maro" Camargo</h1>
      <p class="lead" style="margin:0 auto;">Doutora em Educação, pesquisadora de metodologias de diálogo e facilitadora de processos participativos para organizações, comunidades e times.</p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container about-grid">
    <div class="about-photo">
      <img src="/assets/img/maro-camargo.jpg" alt="Maro Camargo">
      <div class="topics-tags" style="margin-top:24px;">
        <span>Diálogo</span>
        <span>World Café</span>
        <span>Educação Ambiental</span>
        <span>Cultura Organizacional</span>
        <span>Facilitação</span>
        <span>Formação de Educadores</span>
      </div>
    </div>
    <div>
      <p class="eyebrow">Trajetória</p>
      <h2>Ciência, sensibilidade e prática de campo</h2>
      <p>Doutora em Educação pela Faculdade de Educação da Universidade de São Paulo (FEUSP), na área de Cultura, Organização e Educação, com pesquisa desenvolvida na APA Embu-Verde, no município de Embu das Artes, região metropolitana de São Paulo.</p>
      <p>Realizou estágio de doutorado-sanduíche com bolsa da Capes/Fulbright na Ohio State University, em Columbus, Ohio (EUA), com o objetivo de aprofundar os estudos sobre a metodologia World Café — diálogo e facilitação de processos em assuntos complexos.</p>
      <p>É bacharel e licenciada em Ciências Biológicas pela USP (2002) e mestre em Ciência Ambiental pelo PROCAM/USP (2006), com dissertação sobre metodologias participativas como jogos de papéis (RPG) para negociação de conflitos socioambientais.</p>

      <ul class="credentials">
        <li>🎓 <div><strong>Doutorado em Educação</strong> — FEUSP, Cultura, Organização e Educação</div></li>
        <li>🌎 <div><strong>Doutorado-sanduíche</strong> — Ohio State University (EUA), bolsa Capes/Fulbright, metodologia World Café</div></li>
        <li>🌱 <div><strong>Mestrado em Ciência Ambiental</strong> — PROCAM/USP, metodologias participativas e RPG para conflitos socioambientais</div></li>
        <li>🔬 <div><strong>Graduação em Ciências Biológicas</strong> — USP (bacharelado e licenciatura), 2002</div></li>
      </ul>
    </div>
  </div>
</section>

<section class="section section--tint">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Atuação hoje</p>
      <h2>Docência, consultoria e empreendedorismo digital</h2>
    </div>
    <div class="grid grid-3">
      <div class="service-card">
        <h3>Professora universitária</h3>
        <p>Atua como docente no IPOG e na PUC Campinas, formando novas gerações de profissionais e educadores.</p>
      </div>
      <div class="service-card">
        <h3>Consultora de projetos educativos</h3>
        <p>Desenvolve e conduz projetos educativos e processos de diálogo para organizações e instituições de ensino.</p>
      </div>
      <div class="service-card">
        <h3>Empreendedora digital</h3>
        <p>Leva conteúdo, cursos e vivências sobre diálogo e conexão humana para públicos além da sala de aula.</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Terceiro Setor</p>
      <h2>Temas e frentes de atuação</h2>
      <p>Experiência multidisciplinar, com ênfase em Educação Ambiental, atuando em ONGs, projetos sociais e iniciativas comunitárias.</p>
    </div>
    <div class="topics-tags" style="justify-content:center;">
      <span>Agenda 21</span>
      <span>Educação de adultos</span>
      <span>Cultura e lazer</span>
      <span>Comunidade</span>
      <span>Educação global</span>
      <span>Formação de educadores comunitários</span>
      <span>Formação de professores</span>
      <span>Desenvolvimento científico</span>
      <span>Diálogo e facilitação de processos participativos</span>
    </div>
  </div>
</section>

<section class="section section--dark" style="text-align:center;">
  <div class="container" style="max-width:640px;">
    <p class="eyebrow">Currículo</p>
    <h2>Trajetória acadêmica completa</h2>
    <p style="color:var(--teal-100);">Consulte o currículo completo na Plataforma Lattes (CNPq) ou conecte-se pelo LinkedIn.</p>
    <div class="hero-actions" style="justify-content:center;">
      <a href="<?= e(LATTES_URL) ?>" target="_blank" rel="noopener" class="btn btn-primary">Currículo Lattes</a>
      <a href="<?= e(LINKEDIN_URL) ?>" target="_blank" rel="noopener" class="btn btn-outline">LinkedIn</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
