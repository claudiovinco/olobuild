<template>
  <!-- Il testo di un livello (1.4.1431). Utente, 24 nov 2026: «non devono esserci css personalizzati o hardcoded nelle cose
       fatte con pro slider ma comandi usabili da chiunque». Prima era un'area in cui si scriveva l'HTML a mano
       (<span style="color:…">): ora si scelgono le parole e si preme un pulsante. Il formato salvato resta lo stesso. -->
  <div class="mps-tl">
    <div class="mps-tl-barra" role="toolbar" :aria-label="t('Stile delle parole scelte')">
      <button type="button" class="mps-tl-btn" :title="t('Grassetto')" :aria-label="t('Grassetto')" @mousedown.prevent @click="avvolgi('strong')"><strong>B</strong></button>
      <button type="button" class="mps-tl-btn" :title="t('Corsivo')" :aria-label="t('Corsivo')" @mousedown.prevent @click="avvolgi('em')"><em>I</em></button>
      <button type="button" class="mps-tl-btn" :title="t('A capo (Maiusc+Invio)')" :aria-label="t('A capo')" @mousedown.prevent @click="aCapo">↵</button>
      <span class="mps-tl-sep" aria-hidden="true"></span>
      <button type="button" class="mps-tl-btn" :class="{ 'is-on': coloreAperto }" :title="t('Colore delle parole scelte')" :aria-label="t('Colore delle parole scelte')" :aria-expanded="coloreAperto ? 'true' : 'false'" @mousedown.prevent @click="apriColore">
        <span class="mps-tl-a">A</span>
      </button>
      <button type="button" class="mps-tl-btn" :title="t('Più piccole')" :aria-label="t('Più piccole')" @mousedown.prevent @click="avvolgi('span', { fontSize: '.78em' })"><span class="mps-tl-grande" aria-hidden="true">A</span><span class="mps-tl-piccola" aria-hidden="true">A</span></button>
      <button type="button" class="mps-tl-btn" :title="t('Più tenui')" :aria-label="t('Più tenui')" @mousedown.prevent @click="avvolgi('span', { opacity: '.7' })"><span class="mps-tl-tenue" aria-hidden="true">A</span></button>
      <span class="mps-tl-sep" aria-hidden="true"></span>
      <button type="button" class="mps-tl-btn" :title="t('Togli lo stile alle parole scelte')" :aria-label="t('Togli lo stile alle parole scelte')" @mousedown.prevent @click="togliStile">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7V4h16v3"/><path d="M5 20h6"/><path d="M13 4 8 20"/><path d="m15 15 5 5"/><path d="m20 15-5 5"/></svg>
      </button>
    </div>
    <div v-if="coloreAperto" class="mps-tl-colore">
      <FieldColor :modelValue="coloreScelto" @update:modelValue="colora" />
    </div>
    <div
      ref="area"
      class="mps-tl-area"
      contenteditable="true"
      role="textbox"
      aria-multiline="true"
      :aria-label="t('Testo')"
      @input="scrivi"
      @keydown.enter.prevent="aCapo"
      @paste.prevent="incolla"
      @keyup="ricordaScelta"
      @mouseup="ricordaScelta"
      @blur="ricordaScelta"
    ></div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { t } from '@/i18n';
import FieldColor from '../Builder/fields/FieldColor.vue';
import { testoSicuro } from './livelloUtils.js';

const props = defineProps({ modelValue: { type: String, default: '' } });
const emit = defineEmits(['update:modelValue']);
const area = ref(null);
const coloreAperto = ref(false);
const coloreScelto = ref('');
let scelta = null;   // l'ultima selezione dentro il campo: il colore si sceglie fuori, e la selezione va ricordata
let ultimo = '';     // l'ultimo valore mandato: quando torna indietro uguale, il campo non si riscrive (il cursore resta)

function riempi(v) {
  if (!area.value) return;
  const html = testoSicuro(v || '');
  if (area.value.innerHTML !== html) area.value.innerHTML = html;
}
onMounted(() => { ultimo = props.modelValue || ''; riempi(ultimo); });
watch(() => props.modelValue, (v) => {
  if ((v || '') === ultimo) return;
  ultimo = v || '';
  riempi(ultimo);
});

function manda() {
  if (!area.value) return;
  ultimo = testoSicuro(area.value.innerHTML).replace(/(<br>)+$/, '');
  emit('update:modelValue', ultimo);
}
let timer = null;
function scrivi() { ricordaScelta(); clearTimeout(timer); timer = setTimeout(manda, 250); }

function ricordaScelta() {
  const sel = window.getSelection();
  if (sel && sel.rangeCount && area.value?.contains(sel.getRangeAt(0).commonAncestorContainer)) scelta = sel.getRangeAt(0).cloneRange();
}
function sceltaValida() {
  if (!scelta || scelta.collapsed || !area.value?.contains(scelta.commonAncestorContainer)) return null;
  return scelta;
}
function riseleziona(nodo) {
  const r = document.createRange();
  r.selectNodeContents(nodo);
  const sel = window.getSelection();
  sel.removeAllRanges();
  sel.addRange(r);
  scelta = r.cloneRange();
}

// Avvolge le parole scelte in un tag (grassetto, corsivo) o in uno span con uno stile (colore, più piccole, più tenui)
function avvolgi(tag, stile = null) {
  const r = sceltaValida();
  if (!r) { area.value?.focus(); return; }
  const el = document.createElement(tag);
  if (stile) Object.assign(el.style, stile);
  if (stile?.color) el.setAttribute('style', 'color:' + stile.color); // var(--olo-color-…) resta scritto com'è
  el.appendChild(r.extractContents());
  // lo stesso stile dentro le parole scelte non si ripete: <span color><span color>…
  if (stile) el.querySelectorAll('span').forEach((s) => { for (const k of Object.keys(stile)) s.style[k] = ''; if (!s.getAttribute('style')) s.replaceWith(...s.childNodes); });
  r.insertNode(el);
  riseleziona(el);
  manda();
}
function apriColore() {
  coloreAperto.value = !coloreAperto.value;
  const r = sceltaValida();
  const nodo = r ? (r.startContainer.nodeType === 1 ? r.startContainer : r.startContainer.parentElement) : null;
  coloreScelto.value = nodo?.closest?.('span[style*="color"]')?.style.color || '';
}
function colora(c) {
  coloreScelto.value = c;
  if (!c) { togliStile(); return; }
  avvolgi('span', { color: c });
}
function togliStile() {
  const r = sceltaValida();
  if (!r) return;
  const testo = r.toString();
  r.deleteContents();
  const n = document.createTextNode(testo);
  r.insertNode(n);
  riseleziona(n);
  manda();
}
function aCapo() {
  const sel = window.getSelection();
  if (!sel || !sel.rangeCount || !area.value?.contains(sel.getRangeAt(0).commonAncestorContainer)) return;
  const r = sel.getRangeAt(0);
  r.deleteContents();
  const br = document.createElement('br');
  r.insertNode(br);
  // a fine testo serve un secondo <br> perché la riga nuova si veda (si toglie al salvataggio)
  if (!br.nextSibling || (br.nextSibling.nodeType === 3 && br.nextSibling.textContent === '' && !br.nextSibling.nextSibling)) br.after(document.createElement('br'));
  const dopo = document.createRange();
  dopo.setStartAfter(br);
  dopo.collapse(true);
  sel.removeAllRanges();
  sel.addRange(dopo);
  scrivi();
}
function incolla(e) {
  const testo = (e.clipboardData || window.clipboardData)?.getData('text/plain') || '';
  const sel = window.getSelection();
  if (!sel || !sel.rangeCount) return;
  const r = sel.getRangeAt(0);
  r.deleteContents();
  const righe = testo.replace(/\r/g, '').split('\n');
  const frag = document.createDocumentFragment();
  righe.forEach((riga, i) => { if (i) frag.appendChild(document.createElement('br')); frag.appendChild(document.createTextNode(riga)); });
  const ultimoNodo = frag.lastChild;
  r.insertNode(frag);
  if (ultimoNodo) { const dopo = document.createRange(); dopo.setStartAfter(ultimoNodo); dopo.collapse(true); sel.removeAllRanges(); sel.addRange(dopo); }
  scrivi();
}
</script>

<style scoped>
.mps-tl { display: flex; flex-direction: column; gap: 4px; }
.mps-tl-barra { display: flex; flex-wrap: wrap; align-items: center; gap: 2px; }
.mps-tl-btn { font: inherit; font-size: 11px; min-width: 24px; height: 24px; padding: 0 5px; border: 1px solid #374151; border-radius: 4px; background: #1f2937; color: #d1d5db; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
.mps-tl-btn:hover { background: #374151; color: #fff; }
.mps-tl-btn.is-on { border-color: var(--olo-color-primary, #e1474f); }
.mps-tl-btn:focus-visible { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: 1px; }
.mps-tl-grande { font-size: 12px; font-weight: 600; line-height: 1; }
.mps-tl-piccola { font-size: 8px; font-weight: 600; line-height: 1; margin-left: 1px; align-self: flex-end; margin-bottom: 6px; }
.mps-tl-tenue { font-size: 12px; font-weight: 600; opacity: .45; }
.mps-tl-a { font-weight: 700; border-bottom: 3px solid var(--olo-color-primary, #e1474f); line-height: 1; padding: 0 1px; }
.mps-tl-sep { width: 1px; height: 16px; background: #374151; margin: 0 3px; }
.mps-tl-colore { padding: 6px; border: 1px solid #374151; border-radius: 5px; }
.mps-tl-area { min-height: 64px; max-height: 220px; overflow: auto; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; background: #fff; color: #111827; font-size: 13px; line-height: 1.45; white-space: pre-wrap; word-break: break-word; outline: none; }
.mps-tl-area:focus { border-color: var(--olo-color-primary, #e1474f); box-shadow: 0 0 0 2px color-mix(in srgb, var(--olo-color-primary, #e1474f) 25%, transparent); }
</style>
