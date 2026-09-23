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
