<template>
  <!--
    Il giro guidato del builder (1.4.1218): cinque passi indicati sullo schermo la prima volta che si apre il builder in
    questo browser (inserisci una tile, cambia lo stile, collega un dato, guarda sul telefono, salva e pubblica). Si
    rifà dalla finestra delle Scorciatoie. Ogni passo illumina la sua zona ([data-giro]); se la zona non c'è (pannello
    chiuso, barra stretta) la scheda sta al centro.
  -->
  <Teleport to="body">
    <div v-if="aperto" class="olo-giro" role="dialog" aria-modal="true" aria-labelledby="olo-giro-titolo" @keydown="tasti">
      <div v-if="buco" class="olo-giro-buco" :style="bucoStyle" aria-hidden="true"></div>
      <div v-else class="olo-giro-velo" aria-hidden="true"></div>
      <div ref="cardRef" class="olo-giro-card mb-bg-gray-800 mb-border mb-border-gray-600 mb-rounded-xl mb-shadow-2xl" :style="cardStyle">
        <div class="olo-giro-passo mb-text-gray-400">{{ t('Passo') }} {{ i + 1 }} {{ t('di') }} {{ passi.length }}</div>
        <h3 id="olo-giro-titolo" class="mb-text-white mb-text-base mb-font-semibold mb-m-0">{{ passo.titolo }}</h3>
        <p class="olo-giro-testo mb-text-gray-300">{{ passo.testo }}</p>
        <div class="olo-giro-punti" aria-hidden="true">
          <span v-for="(p, k) in passi" :key="k" :class="{ on: k === i, fatto: k < i }"></span>
        </div>
        <div class="olo-giro-azioni">
          <button type="button" class="olo-giro-salta mb-text-gray-400" @click="chiudi">{{ t('Salta il giro') }}</button>
          <span class="olo-giro-spazio"></span>
          <button v-if="i > 0" type="button" class="olo-giro-btn olo-giro-btn--2 mb-text-gray-300 mb-border mb-border-gray-600" @click="vai(i - 1)">{{ t('Indietro') }}</button>
          <button type="button" class="olo-giro-btn mb-bg-primary-600 mb-text-white" @click="avanti">{{ i < passi.length - 1 ? t('Avanti') : t('Ho capito') }}</button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import { useFocusTrap } from '@/composables/useFocusTrap';

const CHIAVE = 'olo_giro_guidato';

const passi = [
  { zona: 'elementi', titolo: t('Inserisci una tile'),
    testo: t('Qui ci sono tutte le tile: trascinane una nella pagina, o fai clic sul + che compare passando sulla pagina. Se sai già cosa ti serve, premi Ctrl+K e scrivi il nome.') },
  { zona: 'inspector', titolo: t('Cambia lo stile'),
    testo: t('Fai clic su una tile nella pagina: qui trovi cosa dice, nel Contenuto, e come appare, nello Stile: colori, caratteri, spazi, bordi e ombre.') },
  { zona: 'dato', titolo: t('Collega un dato'),
    testo: t('Il fulmine accanto a un campo lo collega a un dato: il titolo della pagina, la foto in evidenza, il prezzo di un prodotto, la data di uno spettacolo. Cambi il dato e la pagina si aggiorna da sola.') },
  { zona: 'dispositivi', titolo: t('Guarda sul telefono'),
    testo: t('Da qui passi dal computer al tablet e al telefono e vedi la pagina com\'è lì. I campi con l\'icona del dispositivo accanto si regolano per ognuno: sul telefono rimpicciolisci un titolo senza toccare il computer.') },
  { zona: 'pubblica', titolo: t('Salva e pubblica'),
    testo: t('Salva tiene il lavoro in bozza, Pubblica lo mette sul sito. Ctrl+S salva senza staccare le mani dalla tastiera, e dalle Versioni torni a una copia di prima.') },
];

const aperto = ref(false);
const i = ref(0);
const buco = ref(null);          // { x, y, w, h } della zona illuminata
const posCard = ref({ x: 0, y: 0 });
const cardRef = ref(null);
const passo = computed(() => passi[i.value]);
const trap = useFocusTrap(cardRef, { onEscape: () => chiudi() });

const bucoStyle = computed(() => buco.value ? {
  left: buco.value.x + 'px', top: buco.value.y + 'px', width: buco.value.w + 'px', height: buco.value.h + 'px',
} : {});
const cardStyle = computed(() => ({ left: posCard.value.x + 'px', top: posCard.value.y + 'px' }));

/** La zona del passo: il primo fulmine visibile per «dato» (altrimenti il pannello delle proprietà). */
function zonaDi(nome) {
  const visibili = (sel) => [...document.querySelectorAll(sel)].filter((el) => {
    const r = el.getBoundingClientRect();
    return r.width > 0 && r.height > 0 && r.bottom > 0 && r.top < window.innerHeight;
  });
  let els = nome === 'dato' ? visibili('[data-giro="inspector"] .dft-btn').slice(0, 1) : [];
  if (!els.length) els = visibili(`[data-giro="${nome === 'dato' ? 'inspector' : nome}"]`);
  if (!els.length) return null;
  const rs = els.map((el) => el.getBoundingClientRect());
  const x0 = Math.min(...rs.map((r) => r.left)), y0 = Math.min(...rs.map((r) => r.top));
  const x1 = Math.max(...rs.map((r) => r.right)), y1 = Math.max(...rs.map((r) => r.bottom));
  const m = 6;
  return { x: Math.max(4, x0 - m), y: Math.max(4, y0 - m), w: Math.min(window.innerWidth - 8, x1 - x0 + 2 * m), h: Math.min(window.innerHeight - 8, y1 - y0 + 2 * m) };
}

/** La scheda accanto alla zona: a destra, a sinistra, sotto o sopra, dove c'è posto; sempre dentro la finestra. */
function piazza() {
  buco.value = zonaDi(passo.value.zona);
  nextTick(() => {
    const c = cardRef.value;
    if (!c) return;
    const cw = c.offsetWidth, ch = c.offsetHeight, W = window.innerWidth, H = window.innerHeight, g = 16;
    let x = (W - cw) / 2, y = (H - ch) / 2;
    const b = buco.value;
    if (b) {
      const centroY = Math.min(Math.max(g, b.y + b.h / 2 - ch / 2), H - ch - g);
      const centroX = Math.min(Math.max(g, b.x + b.w / 2 - cw / 2), W - cw - g);
      if (b.x + b.w + g + cw <= W - g) { x = b.x + b.w + g; y = centroY; }
      else if (b.x - g - cw >= g) { x = b.x - g - cw; y = centroY; }
      else if (b.y + b.h + g + ch <= H - g) { x = centroX; y = b.y + b.h + g; }
      else if (b.y - g - ch >= g) { x = centroX; y = b.y - g - ch; }
    }
    posCard.value = { x: Math.round(x), y: Math.round(y) };
  });
}

function vai(n) {
  i.value = Math.max(0, Math.min(passi.length - 1, n));
  piazza();
  nextTick(() => cardRef.value?.querySelector('.olo-giro-btn:last-child')?.focus());
}
function avanti() {
  if (i.value < passi.length - 1) vai(i.value + 1); else chiudi();
}
function apri() {
  i.value = 0;
  aperto.value = true;
  piazza();
  nextTick(() => {
    trap.activate();
    cardRef.value?.querySelector('.olo-giro-btn:last-child')?.focus();
  });
}
function chiudi() {
  if (!aperto.value) return;
  aperto.value = false;
  trap.deactivate();
  try { localStorage.setItem(CHIAVE, '1'); } catch (e) { /* niente memoria del browser: il giro tornerà */ }
}
function tasti(e) {
  if (e.key === 'ArrowRight') { e.preventDefault(); avanti(); }
  else if (e.key === 'ArrowLeft') { e.preventDefault(); vai(i.value - 1); }
}
function suRidimensiona() { if (aperto.value) piazza(); }

let attesa = null;
onMounted(() => {
  window.addEventListener('olo-giro-guidato', apri);
  window.addEventListener('resize', suRidimensiona);
  let fatto = false;
  try { fatto = localStorage.getItem(CHIAVE) === '1'; } catch (e) { fatto = true; }
  // La prima volta, quando il builder si è disegnato (i pannelli e la barra ci sono già)
  if (!fatto) attesa = setTimeout(apri, 1400);
});
onBeforeUnmount(() => {
  clearTimeout(attesa);
  window.removeEventListener('olo-giro-guidato', apri);
  window.removeEventListener('resize', suRidimensiona);
});

defineExpose({ apri });
</script>

<style scoped>
.olo-giro { position: fixed; inset: 0; z-index: 100000; }
.olo-giro-velo { position: absolute; inset: 0; background: rgba(15, 23, 42, .55); }
.olo-giro-buco { position: absolute; border-radius: 10px; box-shadow: 0 0 0 200vmax rgba(15, 23, 42, .55), 0 0 0 2px var(--olo-ui-accent, #e1474f) inset;
  transition: left .35s cubic-bezier(.2,.7,.2,1), top .35s cubic-bezier(.2,.7,.2,1), width .35s cubic-bezier(.2,.7,.2,1), height .35s cubic-bezier(.2,.7,.2,1);
  pointer-events: none; }
.olo-giro-card { position: absolute; width: 340px; max-width: calc(100vw - 32px); padding: 20px 20px 16px; transition: left .35s cubic-bezier(.2,.7,.2,1), top .35s cubic-bezier(.2,.7,.2,1); }
.olo-giro-passo { font-size: 11px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 6px; }
.olo-giro-testo { font-size: 13px; line-height: 1.55; margin: 8px 0 14px; }
.olo-giro-punti { display: flex; gap: 6px; margin-bottom: 14px; }
.olo-giro-punti span { width: 6px; height: 6px; border-radius: 50%; background: rgba(148, 163, 184, .45); transition: width .25s ease, background .25s ease; }
.olo-giro-punti span.fatto { background: rgba(148, 163, 184, .8); }
.olo-giro-punti span.on { width: 18px; border-radius: 3px; background: var(--olo-ui-accent, #e1474f); }
.olo-giro-azioni { display: flex; align-items: center; gap: 8px; }
.olo-giro-spazio { flex: 1; }
.olo-giro-salta { background: none; border: 0; padding: 4px 0; font-size: 12px; cursor: pointer; text-decoration: underline; text-underline-offset: 3px; }
.olo-giro-btn { padding: 7px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; border-width: 0; }
.olo-giro-btn--2 { background: transparent; border-width: 1px; border-style: solid; }
.olo-giro-btn:focus-visible, .olo-giro-salta:focus-visible { outline: 2px solid var(--olo-ui-accent, #e1474f); outline-offset: 2px; }
@media (prefers-reduced-motion: reduce) { .olo-giro-buco, .olo-giro-card, .olo-giro-punti span { transition: none; } }
</style>
