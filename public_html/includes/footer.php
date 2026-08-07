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
      <a href="/agende-um-horario.php"><?= e(t('nav.schedule_slot')) ?></a>
      <a href="/blog.php"><?= e(t('nav.blog')) ?></a>
      <a href="/loja.php"><?= e(t('nav.store')) ?></a>
      <a href="/galeria.php"><?= e(t('nav.gallery')) ?></a>
      <a href="/contato.php"><?= e(t('nav.contact')) ?></a>
    </div>
    <div class="site-footer__social">
      <p class="site-footer__heading"><?= e(t('footer.connect_heading')) ?></p>
      <a href="<?= e(INSTAGRAM_URL) ?>" target="_blank" rel="noopener">Instagram</a>
      <a href="<?= e(LINKEDIN_URL) ?>" target="_blank" rel="noopener">LinkedIn</a>
      <a href="<?= e(YOUTUBE_URL) ?>" target="_blank" rel="noopener">YouTube</a>
      <a href="<?= e(FACEBOOK_URL) ?>" target="_blank" rel="noopener">Facebook</a>
      <a href="<?= e(MEDIUM_URL) ?>" target="_blank" rel="noopener">Medium</a>
      <a href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>?text=<?= urlencode(WHATSAPP_MESSAGE) ?>" target="_blank" rel="noopener">WhatsApp</a>
      <a href="<?= e(LATTES_URL) ?>" target="_blank" rel="noopener"><?= e(t('footer.lattes')) ?></a>
    </div>
  </div>
  <div class="site-footer__bottom">
    <div class="container">
      <p>&copy; <?= date('Y') ?> Maro Camargo. <?= e(t('footer.rights')) ?></p>
      <p class="site-footer__credit"><?= e(t('footer.credit_prefix')) ?> <a href="https://www.instagram.com/edmarb/" target="_blank" rel="noopener">Edmar Benedito</a></p>
    </div>
  </div>
</footer>
<?php require __DIR__ . '/whatsapp-float.php'; ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
