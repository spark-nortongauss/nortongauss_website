document.addEventListener('DOMContentLoaded', () => {
  const navToggle = document.querySelector('.ng-nav-toggle');
  const navLinks = document.querySelector('.ng-nav-links');

  if (navToggle && navLinks) {
    navToggle.addEventListener('click', () => {
      navToggle.classList.toggle('active');
      navLinks.classList.toggle('open');
    });

    navLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        navToggle.classList.remove('active');
        navLinks.classList.remove('open');
      });
    });
  }

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('revealed');
        obs.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.2,
    rootMargin: '0px 0px -60px 0px'
  });

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

  const yearEl = document.querySelector('[data-current-year]');
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }

  const metrics = document.querySelectorAll('[data-counter]');
  const metricObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const target = parseFloat(el.dataset.counter || '0');
      const duration = parseInt(el.dataset.duration || '1400', 10);
      const suffix = el.dataset.suffix || '';
      let start = 0;
      const stepTime = Math.max(Math.floor(duration / 90), 16);

      const increment = () => {
        start += (target - start) * 0.18;
        if (Math.abs(target - start) < 0.5) {
          el.textContent = `${target}${suffix}`;
          clearInterval(interval);
        } else {
          el.textContent = `${Math.round(start)}${suffix}`;
        }
      };

      const interval = setInterval(increment, stepTime);
      metricObserver.unobserve(el);
    });
  }, {
    threshold: 0.5
  });

  metrics.forEach(el => metricObserver.observe(el));
});
