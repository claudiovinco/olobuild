<template>
  <span style="display:none" ref="anchor"></span>
</template>

<script setup>
import { computed } from 'vue';
import { createThemePicker } from '../../theme-picker/themePicker.js';

const oloData = computed(() => window.oloData || {});

let picker = null;

function showToast(msg, isError) {
  const toast = document.createElement('div');
  toast.textContent = msg;
  toast.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);padding:12px 24px;border-radius:8px;font-size:14px;font-weight:500;z-index:9999999;box-shadow:0 4px 20px rgba(0,0,0,0.3);transition:opacity 0.3s;font-family:system-ui,-apple-system,sans-serif;'
    + (isError ? 'background:#991B1B;color:#FEF2F2' : 'background:#065F46;color:#ECFDF5');
  document.body.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}

async function importTheme(theme) {
  if (!theme || !theme.id) return;
  if (!confirm('Importare questo tema? Verranno creati nuovi template e impostati come header/footer attivi.')) return;

  picker && picker.setBusy(true);
  try {
    const res = await fetch(`${oloData.value.restUrl}/themes/${theme.id}/import`, {
      method: 'POST',
      headers: { 'X-WP-Nonce': oloData.value.nonce, 'Content-Type': 'application/json' },
      credentials: 'same-origin'
    });
    const result = await res.json();
    if (result.templates) {
      picker && picker.close();
      showToast(`✅ Tema importato! ${result.templates.length} template creati.`);
      // Genera subito le anteprime delle card (render REST → cattura), poi ricarica
      const ids = result.templates.map(t => t.id).filter(Boolean);
      if (ids.length && typeof window.oloGenerateMissingThumbs === 'function') {
        const progress = document.createElement('div');
        progress.style.cssText = 'position:fixed;bottom:72px;left:50%;transform:translateX(-50%);padding:12px 24px;border-radius:8px;font-size:14px;font-weight:500;z-index:9999999;box-shadow:0 4px 20px rgba(0,0,0,0.3);font-family:system-ui,-apple-system,sans-serif;background:#1E3A8A;color:#EFF6FF';
        document.body.appendChild(progress);
        try {
          await window.oloGenerateMissingThumbs(ids, {
            onProgress: (i, total) => { progress.textContent = `🖼 Generazione anteprime… ${i}/${total}`; },
          });
        } catch (e) { console.warn('thumb generation failed:', e); }
        progress.remove();
      }
      window.location.reload();
    } else {
      picker && picker.setBusy(false);
      showToast('❌ Errore nell\'importazione', true);
    }
  } catch (e) {
    console.error('importTheme error:', e);
    picker && picker.setBusy(false);
    showToast('❌ ' + (e.message || 'Errore'), true);
  }
}

function open() {
  if (picker) { picker.close(); picker = null; return; }
  picker = createThemePicker({
    mode: 'modal',
    card: { action: 'import' },
    loadThemes: async () => {
      const res = await fetch(`${oloData.value.restUrl}/themes`, {
        headers: { 'X-WP-Nonce': oloData.value.nonce },
        credentials: 'same-origin'
      });
      return await res.json();
    },
    onImport: importTheme,
    onClose: () => { picker = null; },
  });
  picker.open();
}

defineExpose({ open });
</script>
