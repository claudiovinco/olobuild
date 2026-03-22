<template>
  <span style="display:none" ref="anchor"></span>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useBuilderStore } from '../../stores/builder';
import { useTilesStore } from '../../stores/tiles';

const builderStore = useBuilderStore();
const tilesStore = useTilesStore();
const oloData = computed(() => window.oloData || {});

let modalEl = null;
const themes = ref([]);
const loading = ref(false);
const importing = ref(false);

async function fetchThemes() {
  loading.value = true;
  try {
    const res = await fetch(`${oloData.value.restUrl}/themes`, {
      headers: { 'X-WP-Nonce': oloData.value.nonce },
      credentials: 'same-origin'
    });
    themes.value = await res.json();
  } catch (e) {
    console.error('fetchThemes error:', e);
  }
  loading.value = false;
  renderModal();
}

async function importTheme(themeId) {
  if (!confirm('Importare questo tema? Verranno creati nuovi template e impostati come header/footer attivi.')) return;

  importing.value = true;
  renderModal();

  try {
    const res = await fetch(`${oloData.value.restUrl}/themes/${themeId}/import`, {
      method: 'POST',
      headers: {
        'X-WP-Nonce': oloData.value.nonce,
        'Content-Type': 'application/json'
      },
      credentials: 'same-origin'
    });
    const result = await res.json();

    if (result.templates) {
      close();
      showToast(`✅ Tema importato! ${result.templates.length} template creati.`);

      // Reload the page to pick up new header/footer
      setTimeout(() => window.location.reload(), 1500);
    } else {
      showToast('❌ Errore nell\'importazione', true);
    }
  } catch (e) {
    console.error('importTheme error:', e);
    showToast('❌ ' + (e.message || 'Errore'), true);
  }

  importing.value = false;
}

function renderModal() {
  if (!modalEl) return;
  const content = modalEl.querySelector('[data-theme-content]');
  if (!content) return;

  if (loading.value) {
    content.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;padding:3rem"><span style="color:#9ca3af">Caricamento temi...</span></div>';
    return;
  }

  if (importing.value) {
    content.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;padding:3rem;flex-direction:column;gap:12px"><div style="width:32px;height:32px;border:3px solid #374151;border-top-color:#818cf8;border-radius:50%;animation:spin 0.8s linear infinite"></div><span style="color:#9ca3af;font-size:14px">Importazione in corso...</span></div>';
    return;
  }

  if (!themes.value.length) {
    content.innerHTML = '<div style="padding:3rem;text-align:center;color:#6b7280">Nessun tema disponibile.</div>';
    return;
  }

  content.innerHTML = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;padding:20px">'
    + themes.value.map(t => {
      const screenshot = t.screenshot
        ? `<div style="height:160px;background:url(${t.screenshot}) center/cover no-repeat;border-radius:8px 8px 0 0"></div>`
        : `<div style="height:160px;background:linear-gradient(135deg,#1e3a5f,#0f172a);border-radius:8px 8px 0 0;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:48px">🎨</div>`;
      const tags = (t.tags || []).map(tag => `<span style="font-size:10px;padding:2px 6px;background:#374151;color:#9ca3af;border-radius:3px">${tag}</span>`).join(' ');
      return `<div style="background:#1f2937;border:1px solid #374151;border-radius:8px;overflow:hidden;transition:border-color 0.2s" onmouseover="this.style.borderColor='#4f46e5'" onmouseout="this.style.borderColor='#374151'">`
        + screenshot
        + `<div style="padding:16px">`
        + `<h3 style="margin:0 0 4px;font-size:16px;font-weight:600;color:#f3f4f6">${t.name}</h3>`
        + `<p style="margin:0 0 8px;font-size:12px;color:#6b7280;line-height:1.4">${t.description || ''}</p>`
        + (tags ? `<div style="margin-bottom:12px;display:flex;gap:4px;flex-wrap:wrap">${tags}</div>` : '')
        + `<div style="display:flex;align-items:center;justify-content:space-between">`
        + `<span style="font-size:11px;color:#4b5563">v${t.version || '1.0'}</span>`
        + `<button data-import="${t.id}" style="padding:6px 16px;font-size:13px;font-weight:500;border-radius:6px;border:none;background:#4f46e5;color:white;cursor:pointer;transition:background 0.2s" onmouseover="this.style.background='#4338ca'" onmouseout="this.style.background='#4f46e5'">Importa tema</button>`
        + `</div></div></div>`;
    }).join('')
    + '</div>';

  // Attach import handlers
  content.querySelectorAll('[data-import]').forEach(btn => {
    btn.onclick = () => importTheme(btn.getAttribute('data-import'));
  });
}

function open() {
  if (modalEl) { close(); return; }

  modalEl = document.createElement('div');
  modalEl.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;z-index:999999;display:flex;align-items:center;justify-content:center;font-family:system-ui,-apple-system,sans-serif';
  modalEl.innerHTML =
    '<div style="position:absolute;inset:0;background:rgba(0,0,0,0.6)" data-overlay></div>'
    + '<div style="position:relative;width:100%;max-width:56rem;max-height:85vh;background:#111827;border-radius:0.75rem;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);border:1px solid #1f2937;display:flex;flex-direction:column;overflow:hidden;color:#e5e7eb">'
    + '<div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid #1f2937;background:#0f172a">'
    + '<div><h2 style="margin:0;font-size:1.1rem;font-weight:600;color:#f3f4f6">🎨 Temi Olobuild</h2>'
    + '<p style="margin:4px 0 0;font-size:0.75rem;color:#6b7280">Seleziona un tema per configurare il sito completo in un click</p></div>'
    + '<button data-close style="background:none;border:none;color:#6b7280;font-size:1.5rem;cursor:pointer;padding:4px 8px;line-height:1">&times;</button>'
    + '</div>'
    + '<div data-theme-content style="flex:1;overflow-y:auto"></div>'
    + '</div>'
    + '<style>@keyframes spin{to{transform:rotate(360deg)}}</style>';

  document.body.appendChild(modalEl);
  modalEl.querySelector('[data-overlay]').onclick = close;
  modalEl.querySelector('[data-close]').onclick = close;
  modalEl._onKey = e => { if (e.key === 'Escape') close(); };
  document.addEventListener('keydown', modalEl._onKey);

  fetchThemes();
}

function close() {
  if (!modalEl) return;
  document.removeEventListener('keydown', modalEl._onKey);
  modalEl.remove();
  modalEl = null;
}

function showToast(msg, isError) {
  const toast = document.createElement('div');
  toast.textContent = msg;
  toast.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);padding:12px 24px;border-radius:8px;font-size:14px;font-weight:500;z-index:9999999;box-shadow:0 4px 20px rgba(0,0,0,0.3);transition:opacity 0.3s;'
    + (isError ? 'background:#991B1B;color:#FEF2F2' : 'background:#065F46;color:#ECFDF5');
  document.body.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}

defineExpose({ open });
</script>
