const menuButton = document.querySelector('.menu-toggle');
const mainNav = document.querySelector('#main-nav');
const serviceMenu = document.querySelector('.services-menu');
const closeMenu = () => {
  menuButton?.setAttribute('aria-expanded', 'false');
  mainNav?.classList.remove('is-open');
};
menuButton?.addEventListener('click', () => {
  const opening = menuButton.getAttribute('aria-expanded') !== 'true';
  menuButton.setAttribute('aria-expanded', String(opening));
  mainNav.classList.toggle('is-open', opening);
});
document.addEventListener('keydown', (event) => {
  if (event.key !== 'Escape') return;
  if (serviceMenu?.open) {
    serviceMenu.open = false;
    serviceMenu.querySelector('summary').focus();
  } else if (menuButton?.getAttribute('aria-expanded') === 'true') {
    closeMenu();
    menuButton.focus();
  }
});
document.addEventListener('click', (event) => {
  if (serviceMenu?.open && !serviceMenu.contains(event.target)) serviceMenu.open = false;
  if (menuButton?.getAttribute('aria-expanded') === 'true' && !mainNav.contains(event.target) && !menuButton.contains(event.target)) closeMenu();
});
mainNav?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMenu));
window.matchMedia('(min-width: 1001px)').addEventListener('change', (event) => { if (event.matches) closeMenu(); });
document.querySelectorAll('[data-year]').forEach((el) => { el.textContent = new Date().getFullYear(); });
document.documentElement.classList.add('js');

// Before/after rotation. Keep every photo accessible when JavaScript is unavailable.
document.querySelectorAll('[data-repair-carousel]').forEach((carousel) => {
  const slides = [...carousel.querySelectorAll('.repair-slide')];
  if (slides.length < 2) return;
  const track = carousel.querySelector('.repair-track');
  const viewport = carousel.querySelector('.repair-viewport');
  const pauseButton = carousel.querySelector('[data-repair-pause]');
  const status = carousel.querySelector('[data-repair-status]');
  const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let current = 0;
  let paused = motion.matches;
  let hovered = false;
  let visible = !('IntersectionObserver' in window);
  let timer;
  let pointerStart;
  let suppressClick = false;
  let pauseIntent;

  const schedule = () => {
    window.clearTimeout(timer);
    if (!paused && !hovered && visible && !document.hidden) {
      timer = window.setTimeout(() => { show(current + 1); schedule(); }, 6000);
    }
  };
  const show = (index) => {
    current = (index + slides.length) % slides.length;
    track.style.transform = `translateX(-${current * 100}%)`;
    slides.forEach((slide, i) => {
      slide.setAttribute('aria-hidden', String(i !== current));
      slide.inert = i !== current;
      slide.querySelectorAll('a').forEach((link) => { link.tabIndex = i === current ? 0 : -1; });
    });
    const slide = slides[current];
    carousel.querySelector('[data-repair-title]').textContent = slide.dataset.title;
    carousel.querySelector('[data-repair-description]').textContent = slide.dataset.description;
    carousel.querySelector('[data-repair-count]').textContent = `${current + 1} / ${slides.length}`;
    status.textContent = `${slide.dataset.phase} · Photo ${current + 1} of ${slides.length}`;
  };
  const setPaused = (value) => {
    paused = value;
    pauseButton.textContent = paused ? 'Play slideshow' : 'Pause slideshow';
    status.setAttribute('aria-live', paused ? 'polite' : 'off');
    schedule();
  };
  const navigate = (direction) => { setPaused(true); show(current + direction); };
  carousel.classList.add('is-ready');
  carousel.querySelector('.repair-controls').hidden = false;
  slides.forEach((slide, i) => {
    slide.setAttribute('role', 'group');
    slide.setAttribute('aria-roledescription', 'slide');
    slide.setAttribute('aria-label', `${slide.dataset.title}, ${slide.dataset.phase}, ${i + 1} of ${slides.length}`);
  });
  show(0);
  setPaused(paused);
  pauseButton.addEventListener('pointerdown', () => { pauseIntent = !paused; });
  pauseButton.addEventListener('click', () => { setPaused(pauseIntent ?? !paused); pauseIntent = undefined; });
  carousel.querySelector('[data-repair-previous]').addEventListener('click', () => navigate(-1));
  carousel.querySelector('[data-repair-next]').addEventListener('click', () => navigate(1));
  carousel.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
      event.preventDefault();
      navigate(event.key === 'ArrowRight' ? 1 : -1);
    }
  });
  // Keyboard focus stops automatic movement until the visitor explicitly starts it.
  carousel.addEventListener('focusin', () => setPaused(true));
  carousel.addEventListener('mouseenter', () => { hovered = true; schedule(); });
  carousel.addEventListener('mouseleave', () => { hovered = false; schedule(); });
  document.addEventListener('visibilitychange', schedule);
  motion.addEventListener('change', (event) => { if (event.matches) setPaused(true); });
  if ('IntersectionObserver' in window) {
    new IntersectionObserver(([entry]) => { visible = entry.isIntersecting && entry.intersectionRatio >= 0.25; schedule(); }, { threshold: 0.25 }).observe(carousel);
  }
  viewport.addEventListener('pointerdown', (event) => {
    pointerStart = event.isPrimary && event.button === 0 ? { x: event.clientX, y: event.clientY } : null;
    suppressClick = false;
  });
  viewport.addEventListener('pointerup', (event) => {
    if (!pointerStart) return;
    const dx = event.clientX - pointerStart.x, dy = event.clientY - pointerStart.y;
    pointerStart = null;
    if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(dy) * 1.5) {
      suppressClick = true;
      navigate(dx < 0 ? 1 : -1);
    }
  });
  viewport.addEventListener('pointercancel', () => { pointerStart = null; });
  viewport.addEventListener('click', (event) => {
    if (suppressClick) { event.preventDefault(); event.stopPropagation(); suppressClick = false; }
  }, true);
});

// Forward analytics events only from this shop's embedded Shopmonkey form.
const workRequestForm = document.querySelector('#shopmonkey-work-request');
if (workRequestForm) {
  window.addEventListener('message', (event) => {
    if (event.origin !== 'https://app.shopmonkey.cloud' || event.source !== workRequestForm.contentWindow) return;
    const payload = event.data;
    if (!payload || payload.source !== 'sm_wrf' || !payload.dataLayer || typeof payload.dataLayer !== 'object' || Array.isArray(payload.dataLayer)) return;
    window.dataLayer = window.dataLayer || [];
    if (typeof window.dataLayer.push === 'function') window.dataLayer.push(payload.dataLayer);
  });
}
