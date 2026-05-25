(function () {
  const toggle = document.querySelector('[data-mobile-toggle]');
  const menu = document.querySelector('[data-mobile-menu]');

  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      const isOpen = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!isOpen));
      menu.classList.toggle('is-open', !isOpen);
      document.body.classList.toggle('mobile-nav-open', !isOpen);
    });

    menu.addEventListener('click', function (event) {
      if (event.target && event.target.tagName === 'A') {
        toggle.setAttribute('aria-expanded', 'false');
        menu.classList.remove('is-open');
        document.body.classList.remove('mobile-nav-open');
      }
    });
  }

  const revealItems = document.querySelectorAll('.reveal');
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (!revealItems.length) {
    return;
  }

  document.documentElement.classList.add('reveal-ready');

  const revealAll = function () {
    revealItems.forEach(function (item) {
      item.classList.add('in');
    });
  };

  if (reduceMotion || !('IntersectionObserver' in window)) {
    revealAll();
    return;
  }

  const observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('in');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });

  revealItems.forEach(function (item) {
    observer.observe(item);
  });

  window.addEventListener('load', function () {
    window.setTimeout(revealAll, 1400);
  }, { once: true });
})();
