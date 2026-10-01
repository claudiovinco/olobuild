<template>
  <!--
    InfoTip — la (i) accanto a un titolo (sezione, campo, blocco) che apre la spiegazione in
    un popup. Regola dell'utente (1 ott 2026): le spiegazioni lunghe NON si scrivono nel
    pannello, che è stretto e le spinge sotto i controlli; chi le cerca le apre da qui.
    Il popup va nel <body> (Teleport): l'inspector taglia ciò che sporge. Si chiude con Esc,
    col clic fuori, con la × e scorrendo il pannello.
  -->
  <span class="olo-info" @click.stop @pointerdown.stop @mousedown.stop>
    <button
      ref="btn"
      type="button"
      class="olo-info-btn"
      :class="{ 'is-open': aperto }"
      :aria-label="titolo ? t('Informazioni') + ': ' + titolo : t('Informazioni')"
      :aria-expanded="aperto"
      :aria-controls="id"
      :title="t('Informazioni')"
      @click="alterna"
      @keydown.esc.stop="chiudi(true)"
    >
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="9.5" />
        <line x1="12" y1="11" x2="12" y2="16.5" />
        <circle cx="12" cy="7.6" r="0.6" fill="currentColor" />
      </svg>
    </button>
    <Teleport to="body">
      <div
        v-if="aperto"
        :id="id"
        ref="pop"
        class="olo-info-pop"
        role="dialog"
        :aria-label="titolo || t('Informazioni')"
        tabindex="-1"
        :style="posizione"
        @keydown.esc.stop="chiudi(true)"
        @pointerdown.stop
        @mousedown.stop
        @click.stop
      >
        <div class="olo-info-pop-head">
          <span class="olo-info-pop-title">{{ titolo || t('Informazioni') }}</span>
          <button type="button" class="olo-info-pop-close" :aria-label="t('Chiudi')" @click="chiudi(true)">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" /></svg>
          </button>
        </div>
        <div class="olo-info-pop-body">
          <p v-for="(p, i) in paragrafi" :key="i">{{ p }}</p>
        </div>
      </div>
    </Teleport>
  </span>
</template>

<script setup>
import { ref, computed, nextTick, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  // Testo della spiegazione: una stringa o più paragrafi (già tradotti).
  testo: { type: [String, Array], default: '' },
  // Di cosa parla (titolo del popup e nome accessibile del pulsante).
  titolo: { type: String, default: '' },
});

const id = 'olo-info-' + Math.random().toString(36).slice(2, 10);
const aperto = ref(false);
const btn = ref(null);
const pop = ref(null);
const posizione = ref({});

const paragrafi = computed(() => (Array.isArray(props.testo) ? props.testo : [props.testo])
  .map((p) => String(p || '').trim())
  .filter(Boolean));

const LARGHEZZA = 300;
const MARGINE = 8;

function posiziona() {
  const b = btn.value && btn.value.getBoundingClientRect();
  if (!b) return;
  const vw = window.innerWidth;
  const vh = window.innerHeight;
  const w = Math.min(LARGHEZZA, vw - MARGINE * 2);
  let left = b.left + b.width / 2 - w / 2;
  left = Math.max(MARGINE, Math.min(left, vw - w - MARGINE));
  const sotto = vh - b.bottom;
  const sopra = b.top;
  const st = { width: w + 'px', left: left + 'px' };
  if (sotto >= 180 || sotto >= sopra) {
    st.top = (b.bottom + 6) + 'px';
    st.maxHeight = Math.max(120, sotto - 16) + 'px';
  } else {
    st.bottom = (vh - b.top + 6) + 'px';
    st.maxHeight = Math.max(120, sopra - 16) + 'px';
  }
  posizione.value = st;
}

function fuori(e) {
  if (pop.value && pop.value.contains(e.target)) return;
  if (btn.value && btn.value.contains(e.target)) return;
  chiudi(false);
}
function onScroll(e) {
  // Scorrere DENTRO il popup non lo chiude; scorrere il pannello sì.
  if (pop.value && e.target instanceof Node && pop.value.contains(e.target)) return;
  chiudi(false);
}
function ascolta(on) {
  const f = on ? 'addEventListener' : 'removeEventListener';
  document[f]('pointerdown', fuori, true);
  window[f]('scroll', onScroll, true);
  window[f]('resize', posiziona);
}

async function apri() {
  if (!paragrafi.value.length) return;
  aperto.value = true;
  posiziona();
  ascolta(true);
  await nextTick();
  posiziona();
}
function chiudi(rimettiFocus) {
  if (!aperto.value) return;
  aperto.value = false;
  ascolta(false);
  if (rimettiFocus && btn.value) btn.value.focus();
}
function alterna() {
  if (aperto.value) chiudi(false); else apri();
}

onBeforeUnmount(() => ascolta(false));
</script>

<style scoped>
.olo-info {
  display: inline-flex;
  align-items: center;
  flex: none;
  vertical-align: middle;
}
.olo-info-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  padding: 0;
  margin: 0;
  border: none;
  border-radius: 50%;
  background: transparent;
  color: inherit;
  opacity: 0.6;
  cursor: pointer;
  transition: opacity 0.12s, color 0.12s, background 0.12s;
}
.olo-info-btn:hover,
.olo-info-btn.is-open {
  opacity: 1;
  color: var(--olo-ui-accent, #e8622a);
}
.olo-info-btn:focus-visible {
  opacity: 1;
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: 1px;
}

/* Popup: superficie chiara del chrome, fuori dal pannello (Teleport nel body) */
.olo-info-pop {
  position: fixed;
  z-index: 100200;
  display: flex;
  flex-direction: column;
  box-sizing: border-box;
  background: #ffffff;
  color: #1f2937;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow: 0 12px 32px rgba(15, 23, 42, 0.18), 0 2px 6px rgba(15, 23, 42, 0.08);
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  text-transform: none;
  letter-spacing: normal;
  overflow: hidden;
}
.olo-info-pop:focus { outline: none; }
.olo-info-pop-head {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 8px 6px 12px;
  border-bottom: 1px solid #f1f5f9;
}
.olo-info-pop-title {
  flex: 1;
  min-width: 0;
  font-size: 12px;
  font-weight: 700;
  color: #111827;
}
.olo-info-pop-close {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  padding: 0;
  border: none;
  border-radius: 6px;
  background: transparent;
  color: #64748b;
  cursor: pointer;
}
.olo-info-pop-close:hover { background: #f1f5f9; color: #1e293b; }
.olo-info-pop-close:focus-visible { outline: 2px solid var(--olo-ui-accent, #e8622a); outline-offset: 1px; }
.olo-info-pop-body {
  padding: 8px 12px 12px;
  overflow-y: auto;
  font-size: 12px;
  line-height: 1.55;
  color: #374151;
}
.olo-info-pop-body p { margin: 0; }
.olo-info-pop-body p + p { margin-top: 8px; }
</style>
