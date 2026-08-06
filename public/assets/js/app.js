/**
 * MCYF — Community Youth Forum
 * app.js — Client-side utilities
 * No jQuery. Pure vanilla JS + Bootstrap 5.
 */

'use strict';

// ── 1. Confirm-delete helper ─────────────────────────────────────────────────
// Any <a> or <form> with data-confirm="message" triggers a modal before action.
document.addEventListener('click', function (e) {
  const btn = e.target.closest('[data-confirm]');
  if (!btn) return;
  const msg = btn.dataset.confirm || 'کیا آپ واقعی حذف کرنا چاہتے ہیں؟';
  if (!confirm(msg)) e.preventDefault();
});

// ── 2. Auto-dismiss alerts ───────────────────────────────────────────────────
document.querySelectorAll('.alert:not(.alert-permanent)').forEach(el => {
  setTimeout(() => {
    el.style.transition = 'opacity 0.5s';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 500);
  }, 5000);
});

// ── 3. Active nav-link highlight (fallback) ──────────────────────────────────
// PHP already sets active class; this is a JS safety net for SPAs / hash nav.
(function () {
  const path = window.location.pathname.split('/').pop();
  document.querySelectorAll('.navbar-forum .nav-link').forEach(link => {
    const href = link.getAttribute('href') || '';
    if (href && href.includes(path) && path !== '') {
      link.classList.add('active');
    }
  });
})();

// ── 4. Image lazy-load (IntersectionObserver) ─────────────────────────────────
if ('IntersectionObserver' in window) {
  const lazyImages = document.querySelectorAll('img[data-src]');
  const observer   = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.removeAttribute('data-src');
        observer.unobserve(img);
      }
    });
  }, { rootMargin: '200px' });
  lazyImages.forEach(img => observer.observe(img));
}

// ── 5. Gallery lightbox (simple) ─────────────────────────────────────────────
// Add class "gallery-link" to any <a> wrapping a gallery image for lightbox.
document.addEventListener('click', function (e) {
  const link = e.target.closest('.gallery-link');
  if (!link) return;
  e.preventDefault();

  const src  = link.getAttribute('href') || link.querySelector('img')?.src;
  const cap  = link.dataset.caption || '';

  if (!src) return;

  // Build overlay
  const overlay = document.createElement('div');
  overlay.className = 'gallery-overlay';
  overlay.innerHTML = `
    <div class="gallery-overlay-inner">
      <button class="gallery-overlay-close" aria-label="Close">&times;</button>
      <img src="${src}" alt="${cap}" style="max-width:90vw;max-height:80vh;border-radius:8px;">
      ${cap ? `<p class="mt-2 text-white text-center small">${cap}</p>` : ''}
    </div>`;

  const style = document.createElement('style');
  style.textContent = `
    .gallery-overlay{position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:9999;display:flex;align-items:center;justify-content:center;animation:fadeIn .2s}
    @keyframes fadeIn{from{opacity:0}to{opacity:1}}
    .gallery-overlay-inner{position:relative;text-align:center}
    .gallery-overlay-close{position:absolute;top:-14px;right:-14px;background:var(--forum-gold);border:none;color:#000;width:30px;height:30px;border-radius:50%;font-size:1.2rem;cursor:pointer;line-height:1}
  `;
  document.head.appendChild(style);
  document.body.appendChild(overlay);

  overlay.addEventListener('click', function (ev) {
    if (ev.target === overlay || ev.target.classList.contains('gallery-overlay-close')) {
      overlay.remove();
      style.remove();
    }
  });
});

// ── 6. Search filter (client-side table) ─────────────────────────────────────
// Add data-search-table="#tableId" to an input to filter table rows live.
document.querySelectorAll('[data-search-table]').forEach(input => {
  const tableId = input.dataset.searchTable;
  const table   = document.querySelector(tableId);
  if (!table) return;
  const rows = table.querySelectorAll('tbody tr');

  input.addEventListener('input', function () {
    const query = this.value.toLowerCase();
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(query) ? '' : 'none';
    });
  });
});

// ── 7. Form loader — disable submit button on POST ────────────────────────────
document.querySelectorAll('form[method="post"], form[method="POST"]').forEach(form => {
  form.addEventListener('submit', function () {
    const btn = this.querySelector('[type="submit"]');
    if (btn) {
      btn.disabled = true;
      const original = btn.innerHTML;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';
      // Re-enable after 8 s as safety fallback
      setTimeout(() => {
        btn.disabled  = false;
        btn.innerHTML = original;
      }, 8000);
    }
  });
});

// ── 8. Bootstrap tooltip init ─────────────────────────────────────────────────
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
  new bootstrap.Tooltip(el, { trigger: 'hover focus' });
});

// ── 9. Print helper ───────────────────────────────────────────────────────────
function forumPrint() { window.print(); }
