</main>
<footer class="site-footer">
  <div class="container site-footer__inner">
    <div class="site-footer__brand">
      <img src="/assets/img/logo-maro-white.png" alt="Maro Camargo" class="brand-logo brand-logo--footer">
      <p><?= e(t('footer.tagline')) ?></p>
    </div>
    <div class="site-footer__links">
      <p class="site-footer__heading"><?= e(t('footer.nav_heading')) ?></p>
      <a href="/sobre.php"><?= e(t('nav.about')) ?></a>
      <a href="/livro.php"><?= e(t('nav.book')) ?></a>
      <a href="/palestras-consultorias.php"><?= e(t('nav.services')) ?></a>
      <a href="/agenda.php"><?= e(t('nav.agenda')) ?></a>
      <a href="/contato.php"><?= e(t('nav.contact')) ?></a>
    </div>
    <div class="site-footer__social">
      <p class="site-footer__heading"><?= e(t('footer.connect_heading')) ?></p>
      <a href="<?= e(INSTAGRAM_URL) ?>" target="_blank" rel="noopener">Instagram</a>
      <a href="<?= e(LINKEDIN_URL) ?>" target="_blank" rel="noopener">LinkedIn</a>
      <a href="<?= e(LATTES_URL) ?>" target="_blank" rel="noopener"><?= e(t('footer.lattes')) ?></a>
    </div>
  </div>
  <div class="site-footer__bottom">
    <div class="container">
      <p>&copy; <?= date('Y') ?> Maro Camargo. <?= e(t('footer.rights')) ?></p>
    </div>
  </div>
</footer>
<script src="/assets/js/main.js"></script>
</body>
</html>
