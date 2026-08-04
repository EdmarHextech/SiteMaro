document.addEventListener('DOMContentLoaded', function () {
  var overlay = document.getElementById('lightboxOverlay');
  var img = document.getElementById('lightboxImg');
  var caption = document.getElementById('lightboxCaption');
  var closeBtn = document.getElementById('lightboxClose');
  if (!overlay || !img) return;

  var lastFocused = null;

  function abrir(src, legenda) {
    lastFocused = document.activeElement;
    img.src = src;
    img.alt = legenda || '';
    caption.textContent = legenda || '';
    overlay.hidden = false;
    document.body.style.overflow = 'hidden';
    closeBtn.focus();
  }

  function fechar() {
    overlay.hidden = true;
    img.src = '';
    document.body.style.overflow = '';
    if (lastFocused) lastFocused.focus();
  }

  document.querySelectorAll('[data-lightbox-trigger]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      abrir(btn.getAttribute('data-src'), btn.getAttribute('data-legenda'));
    });
  });

  closeBtn.addEventListener('click', fechar);
  overlay.addEventListener('click', function (evt) {
    if (evt.target === overlay) fechar();
  });
  document.addEventListener('keydown', function (evt) {
    if (!overlay.hidden && evt.key === 'Escape') fechar();
  });
});
