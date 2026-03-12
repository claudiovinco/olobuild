<template>
  <span style="display:none" ref="anchor"></span>
</template>

<script setup>
import { ref, computed, onUnmounted } from 'vue';
import { useTilesStore } from '../../stores/tiles';
import { useBuilderStore } from '../../stores/builder';

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();

const anchor = ref(null);
const loading = ref(false);
const revisions = ref([]);
const comparing = ref(null);
const restoring = ref(null);
const comparisonData = ref(null);

let modalEl = null;

const oloData = computed(() => window.oloData || {});

// ─── Conta nodi ricorsivamente ───
function countNodes(nodes) {
  if (!Array.isArray(nodes)) return 0;
  let count = 0;
  for (const n of nodes) {
    count++;
    if (Array.isArray(n.children)) count += countNodes(n.children);
  }
  return count;
}

// ─── Fetch revisioni ───
async function fetchRevisions() {
  if (!builderStore.currentTemplate) return;

  loading.value = true;
  try {
    const res = await fetch(
      `${oloData.value.restUrl}/templates/${builderStore.currentTemplate.id}/revisions`,
      { headers: { 'X-WP-Nonce': oloData.value.nonce } }
    );
    if (!res.ok) throw new Error('Failed to fetch revisions');
    revisions.value = await res.json();
  } catch (err) {
    console.error('fetchRevisions error:', err);
    revisions.value = [];
  } finally {
    loading.value = false;
    renderModal();
  }
}

// ─── Formattazione date ───
function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr.replace(' ', 'T'));
  return d.toLocaleDateString('it-IT', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  });
}

function formatTimeAgo(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr.replace(' ', 'T'));
  const now = new Date();
  const diffMs = now - d;
  const diffMin = Math.floor(diffMs / 60000);
  if (diffMin < 1) return 'adesso';
  if (diffMin < 60) return diffMin + ' min fa';
  const diffH = Math.floor(diffMin / 60);
  if (diffH < 24) return diffH + ' ore fa';
  const diffD = Math.floor(diffH / 24);
  if (diffD < 30) return diffD + ' giorni fa';
  const diffM = Math.floor(diffD / 30);
  return diffM + ' mesi fa';
}

function formatSize(bytes) {
  if (!bytes) return '';
  const num = parseInt(bytes);
  if (num < 1024) return num + ' B';
  if (num < 1048576) return (num / 1024).toFixed(1) + ' KB';
  return (num / 1048576).toFixed(1) + ' MB';
}

// ─── Render modale via DOM puro su body ───
function renderModal() {
  if (!modalEl) return;
  const list = revisions.value;

  let html = '';
  if (loading.value) {
    html = '<div style="display:flex;align-items:center;justify-content:center;padding:3rem 0"><span style="font-size:0.875rem;color:#9ca3af">Caricamento revisioni...</span></div>';
  } else if (list.length === 0) {
    html = '<div style="padding:3rem 1.25rem;text-align:center;font-size:0.875rem;color:#6b7280">Nessuna revisione disponibile per questo template.</div>';
  } else {
    html = list.map(function(rev, idx) {
      return '<div style="padding:0.75rem 1.25rem;border-bottom:1px solid rgba(55,65,81,0.5)" data-rev-id="' + rev.id + '">'
        + '<div style="display:flex;align-items:center;justify-content:space-between">'
        + '<div style="flex:1">'
        + '<div style="display:flex;align-items:center;gap:0.5rem">'
        + '<span style="font-size:0.875rem;font-weight:500;color:#e5e7eb">'
        + (idx === 0 ? 'Revisione più recente' : 'Revisione #' + rev.id)
        + '</span>'
        + (idx === 0 ? '<span style="font-size:10px;padding:2px 6px;border-radius:4px;background:rgba(79,70,229,0.2);color:#a5b4fc;font-weight:500">Ultima</span>' : '')
        + '</div>'
        + '<div style="display:flex;align-items:center;gap:0.75rem;margin-top:4px;font-size:0.75rem;color:#6b7280">'
        + '<span>' + formatDate(rev.created_at) + '</span>'
        + '<span>' + formatSize(rev.content_size) + '</span>'
        + '<span>' + formatTimeAgo(rev.created_at) + '</span>'
        + '</div></div>'
        + '<div style="display:flex;align-items:center;gap:0.5rem">'
        + '<button data-action="restore" data-rev-id="' + rev.id + '" style="padding:4px 10px;font-size:0.75rem;font-weight:500;border-radius:4px;border:none;background:#4f46e5;color:white;cursor:pointer">Ripristina</button>'
        + '</div></div></div>';
    }).join('');
  }

  var content = modalEl.querySelector('[data-rev-content]');
  if (content) content.innerHTML = html;

  // Attach restore handlers
  modalEl.querySelectorAll('[data-action="restore"]').forEach(function(btn) {
    btn.onclick = function() {
      var revId = parseInt(btn.getAttribute('data-rev-id'));
      var rev = list.find(function(r) { return r.id === revId; });
      if (rev) restoreRevision(rev);
    };
  });
}

// ─── Ripristina revisione ───
async function restoreRevision(rev) {
  if (!confirm('Ripristinare questa revisione? Il template attuale verrà sovrascritto.')) return;

  try {
    const res = await fetch(
      `${oloData.value.restUrl}/revisions/${rev.id}`,
      { headers: { 'X-WP-Nonce': oloData.value.nonce } }
    );
    if (!res.ok) throw new Error('Failed to fetch revision');
    const fullRev = await res.json();
    const revContent = fullRev.content || [];
    tilesStore.setCanvasTiles(revContent);
    builderStore.isDirty = true;
    close();
  } catch (err) {
    console.error('restoreRevision error:', err);
  }
}

// ─── Apri/Chiudi ───
function open() {
  if (modalEl) { close(); return; }

  modalEl = document.createElement('div');
  modalEl.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;z-index:999999;display:flex;align-items:center;justify-content:center;font-family:system-ui,-apple-system,sans-serif';
  modalEl.innerHTML =
    '<div style="position:absolute;inset:0;background:rgba(0,0,0,0.6)" data-rev-overlay></div>'
    + '<div style="position:relative;width:100%;max-width:36rem;max-height:80vh;background:#1f2937;border-radius:0.75rem;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);border:1px solid #374151;display:flex;flex-direction:column;overflow:hidden;color:#e5e7eb">'
    + '<div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid #374151">'
    + '<div><h2 style="margin:0;font-size:1rem;font-weight:600;color:#f3f4f6">Cronologia revisioni</h2>'
    + '<p style="margin:4px 0 0;font-size:0.75rem;color:#6b7280" data-rev-count></p></div>'
    + '<button data-rev-close style="background:none;border:none;color:#6b7280;font-size:1.5rem;cursor:pointer;padding:4px 8px;line-height:1" title="Chiudi">&times;</button>'
    + '</div>'
    + '<div data-rev-content style="flex:1;overflow-y:auto">'
    + '<div style="display:flex;align-items:center;justify-content:center;padding:3rem 0"><span style="font-size:0.875rem;color:#9ca3af">Caricamento revisioni...</span></div>'
    + '</div></div>';

  document.body.appendChild(modalEl);

  // Close handlers
  modalEl.querySelector('[data-rev-overlay]').onclick = close;
  modalEl.querySelector('[data-rev-close]').onclick = close;

  // Escape key
  modalEl._onKey = function(e) { if (e.key === 'Escape') close(); };
  document.addEventListener('keydown', modalEl._onKey);

  fetchRevisions();
}

function close() {
  if (!modalEl) return;
  if (modalEl._onKey) document.removeEventListener('keydown', modalEl._onKey);
  modalEl.remove();
  modalEl = null;
}

onUnmounted(function() { close(); });

defineExpose({ open, close });
</script>
