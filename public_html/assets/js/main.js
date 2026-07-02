document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.getElementById('navToggle');
  var nav = document.getElementById('siteNav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var isOpen = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('submit', function (evt) {
      if (!window.confirm(el.getAttribute('data-confirm'))) {
        evt.preventDefault();
      }
    });
  });
});
