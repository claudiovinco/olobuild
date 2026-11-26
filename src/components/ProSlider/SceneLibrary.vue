<template>
  <!-- Le scene dei temi (1.4.1422): i Pro Slider dei temi, da cui si parte e si cambiano testi e foto.
       Utente, 24 nov 2026: «anche l'utente poco addestrato deve capire come utilizzare la pro slider». -->
  <div class="mps-scene" role="dialog" aria-modal="true" :aria-label="t('Scene dei temi')" @keydown="tasto" @click.self="lavoro || $emit('chiudi')">
    <div class="mps-scene-box" ref="box">
      <div class="mps-scene-testa">
        <div class="mb-min-w-0">
          <h3 class="mps-scene-titolo">{{ t('Scene dei temi') }}</h3>
          <p class="mps-scene-sotto">{{ t('Parti da un Pro Slider dei temi: poi cambi testi, foto e link.') }}</p>
        </div>
        <button type="button" class="mps-scene-x" @click="$emit('chiudi')" :aria-label="t('Chiudi')">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12" /></svg>
        </button>
      </div>

      <!-- Elenco -->
      <template v-if="!scelta">
        <div class="mps-scene-filtri">
          <label class="mps-scene-cerca">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" /></svg>
            <input ref="cerca" v-model="testo" type="search" :placeholder="t('Cerca un tema o una parola')" :aria-label="t('Cerca un tema o una parola')" />
          </label>
          <div class="mps-scene-tipi" role="group" :aria-label="t('Tipo di scena')">
            <button v-for="f in FILTRI" :key="f.k" type="button" class="mps-scene-tipo" :class="{ 'is-on': filtro === f.k }"
                    :aria-pressed="filtro === f.k ? 'true' : 'false'" @click="filtro = f.k" :title="f.spiega">
              {{ f.nome }} <span class="mps-scene-n">{{ conta(f.k) }}</span>
            </button>
          </div>
        </div>

        <div class="mps-scene-corpo">
          <p v-if="carico" class="mps-scene-stato">{{ t('Carico le scene…') }}</p>
          <p v-else-if="errore" class="mps-scene-stato mps-scene-stato--err">{{ errore }}</p>
          <p v-else-if="!visibili.length" class="mps-scene-stato">{{ t('Nessuna scena con questa ricerca.') }}</p>
          <div v-else class="mps-scene-griglia">
            <button v-for="s in visibili" :key="s.id" type="button" class="mps-scene-card" @click="apri(s)"
                    :aria-label="s.nome + ' — ' + nomeTipo(s.tipo) + (s.pro_ok ? '' : ' — ' + t('OLObuild Pro'))">
              <span class="mps-scene-img" :class="{ 'is-vuota': !s.anteprima }">
                <img v-if="s.anteprima" :src="s.anteprima" alt="" loading="lazy" :style="s.in_cima ? 'object-position:top center' : ''" @error="s.anteprima = ''" />
                <span v-else class="mps-scene-img-testo">{{ s.titolo || s.nome }}</span>
                <span class="mps-scene-bollo">{{ nomeTipo(s.tipo) }}</span>
                <span v-if="s.edizione === 'pro'" class="mps-scene-pro" :class="{ 'is-chiuso': !s.pro_ok }">
                  <svg v-if="!s.pro_ok" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><rect x="5" y="11" width="14" height="9" rx="2" /><path d="M8 11V8a4 4 0 0 1 8 0v3" /></svg>
                  Pro
                </span>
              </span>
              <span class="mps-scene-nome">{{ s.nome }}</span>
              <span class="mps-scene-riga">{{ s.titolo }}</span>
              <span class="mps-scene-riga mps-scene-riga--poco">{{ s.slide === 1 ? t('1 slide') : t('%d slide').replace('%d', s.slide) }}<template v-if="s.schede"> · {{ t('con le schede') }}</template><template v-if="s.pagina !== 'home'"> · {{ t('pagina') }} {{ s.pagina }}</template></span>
            </button>
          </div>
        </div>
      </template>

      <!-- La scena scelta -->
      <div v-else class="mps-scene-dettaglio">
        <button type="button" class="mps-scene-indietro" @click="indietro" ref="tornaBtn">
          <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>
          {{ t('Tutte le scene') }}
        </button>
        <div class="mps-scene-dett-griglia">
          <div class="mps-scene-dett-img" :class="{ 'is-vuota': !scelta.anteprima }">
            <img v-if="scelta.anteprima" :src="scelta.anteprima" alt="" :style="scelta.in_cima ? 'object-position:top center' : ''" />
            <span v-else class="mps-scene-img-testo">{{ scelta.titolo || scelta.nome }}</span>
          </div>
          <div class="mps-scene-dett-info">
            <p class="mps-scene-dett-tipo">{{ nomeTipo(scelta.tipo) }}<template v-if="scelta.edizione === 'pro'"> · OLObuild Pro</template></p>
            <h4 class="mps-scene-dett-nome">{{ scelta.nome }}</h4>
            <p class="mps-scene-dett-testo">{{ spiegaTipo(scelta.tipo) }}</p>
            <ul class="mps-scene-fatti">
              <li>{{ scelta.slide === 1 ? t('1 slide') : t('%d slide').replace('%d', scelta.slide) }} · {{ t('%d livelli').replace('%d', scelta.livelli) }}</li>
              <li v-if="scelta.video">{{ t('Video di fondo') }}</li>
              <li v-if="scelta.schede">{{ t('Con le schede: si passa da una slide all’altra dalle linguette') }}</li>
            </ul>
            <p v-if="scelta.descrizione" class="mps-scene-dett-desc">{{ scelta.descrizione }}</p>
            <p class="mps-scene-nota">{{ t('Le foto e i video si copiano nella tua Libreria media. I colori diventano quelli del tuo sito; i pulsanti portano a «#»: cambia il loro link.') }}</p>

            <template v-if="!scelta.pro_ok">
              <p class="mps-scene-nota">{{ t('Questa scena viene da un tema di OLObuild Pro: si usa con la licenza Pro.') }}</p>
              <a class="mps-scene-btn" :href="scelta.pro_url" target="_blank" rel="noopener">{{ t('Scopri OLObuild Pro') }}</a>
            </template>
            <template v-else-if="conferma">
              <p class="mps-scene-avviso" role="alert">{{ (numSlide === 1 ? t('La slide di adesso viene sostituita.') : t('Le %d slide di adesso vengono sostituite.').replace('%d', numSlide)) + ' ' + t('Finché non salvi, «Annulla» dell’editor rimette tutto com’era.') }}</p>
              <div class="mps-scene-azioni">
                <button type="button" class="mps-scene-btn" :disabled="lavoro" @click="usa('sostituisci')">{{ t('Sostituisci') }}</button>
                <button type="button" class="mps-scene-btn mps-scene-btn--2" :disabled="lavoro" @click="conferma = false">{{ t('Indietro') }}</button>
              </div>
            </template>
            <div v-else class="mps-scene-azioni">
              <button type="button" class="mps-scene-btn" :disabled="lavoro" @click="haContenuto ? (conferma = true) : usa('sostituisci')">{{ t('Usa questa scena') }}</button>
              <button v-if="haContenuto" type="button" class="mps-scene-btn mps-scene-btn--2" :disabled="lavoro" @click="usa('aggiungi')">{{ t('Aggiungi le slide in fondo') }}</button>
            </div>
            <p v-if="scelta.pro_ok && haContenuto && !conferma && (scelta.tipo === 'scorrimento' || scelta.tipo === 'scelte')" class="mps-scene-nota">{{ t('«Aggiungi le slide in fondo» tiene le impostazioni del tuo slider: una scena che si muove scorrendo o che cambia con un clic si vede come nel tema solo con «Usa questa scena».') }}</p>
            <p v-if="lavoro" class="mps-scene-stato" role="status">{{ t('Copio foto e video nella Libreria media…') }}</p>
            <p v-if="erroreUso" class="mps-scene-stato mps-scene-stato--err" role="alert">{{ erroreUso }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { t } from '@/i18n';

defineProps({
  // Lo slider ha già delle slide fatte (non solo quella vuota di partenza): «Usa» chiede conferma.
  haContenuto: { type: Boolean, default: false },
  numSlide: { type: Number, default: 0 },
});
const emit = defineEmits(['usa', 'chiudi']);

const TIPI = {
  classica: [t('Apertura classica'), t('Foto o video di fondo, titolo, testo e pulsanti che entrano e restano.')],
  scorrimento: [t('Racconto a scorrimento'), t('I livelli si muovono mentre si scorre la pagina: la scena si racconta scorrendo.')],
  animata: [t('Scena animata'), t('Carte che girano, tende, pieghe e animazioni che si ripetono nel tempo.')],
  scelte: [t('Scelte e interruttori'), t('Si tocca un elemento (una spia, una leva, un colore) e la scena cambia.')],
};
const FILTRI = [
  { k: '', nome: t('Tutte'), spiega: '' },
  { k: 'classica', nome: TIPI.classica[0], spiega: TIPI.classica[1] },
  { k: 'scorrimento', nome: TIPI.scorrimento[0], spiega: TIPI.scorrimento[1] },
  { k: 'animata', nome: TIPI.animata[0], spiega: TIPI.animata[1] },
  { k: 'scelte', nome: TIPI.scelte[0], spiega: TIPI.scelte[1] },
  { k: 'schede', nome: t('Con le schede'), spiega: t('Più slide, con le linguette per passare dall’una all’altra.') },
];
const nomeTipo = (k) => (TIPI[k] || TIPI.classica)[0];
const spiegaTipo = (k) => (TIPI[k] || TIPI.classica)[1];

const scene = ref([]);
const carico = ref(true);
const errore = ref('');
const filtro = ref('');
const testo = ref('');
const scelta = ref(null);
const conferma = ref(false);
const lavoro = ref(false);
const erroreUso = ref('');
const cerca = ref(null);
const tornaBtn = ref(null);
let ultimaCard = null;

const piano = (s) => String(s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
const passaFiltro = (s, f) => !f || (f === 'schede' ? s.schede : s.tipo === f);
const cercate = computed(() => {
  const q = piano(testo.value).trim();
  if (!q) return scene.value;
  return scene.value.filter((s) => piano(s.nome + ' ' + s.titolo + ' ' + s.descrizione + ' ' + s.tema).includes(q));
});
const visibili = computed(() => cercate.value.filter((s) => passaFiltro(s, filtro.value)));
const conta = (f) => cercate.value.filter((s) => passaFiltro(s, f)).length;

function o() {
  return (typeof window !== 'undefined' && window.oloData) || {};
}

onMounted(async () => {
  nextTick(() => cerca.value?.focus());
  try {
    const r = await fetch(`${o().restUrl}scene-slider`, { headers: { 'X-WP-Nonce': o().nonce }, credentials: 'same-origin' });
    const d = await r.json().catch(() => null);
    if (!r.ok || !d || !Array.isArray(d.scene)) throw new Error((d && d.message) || ('HTTP ' + r.status));
    // Le più nuove per prime, come nel selettore dei temi; a parità, le aperture della home prima delle altre.
    scene.value = d.scene.slice().sort((a, b) => String(b.created).localeCompare(String(a.created)) || (b.in_cima - a.in_cima));
  } catch (e) {
    errore.value = t('Non riesco a leggere le scene:') + ' ' + (e?.message || e);
  } finally {
    carico.value = false;
  }
});

function apri(s) {
  ultimaCard = document.activeElement;
  scelta.value = s;
  conferma.value = false;
  erroreUso.value = '';
  nextTick(() => tornaBtn.value?.focus());
}

// I tasti restano qui: l'editor sotto chiuderebbe tutto con Esc e toglierebbe il livello scelto con Canc.
function tasto(e) {
  e.stopPropagation();
  if (e.key === 'Escape') {
    e.preventDefault();
    indietro();
  }
}

function indietro() {
  if (lavoro.value) return;
  if (!scelta.value) { emit('chiudi'); return; }
  scelta.value = null;
  nextTick(() => (ultimaCard && document.contains(ultimaCard) ? ultimaCard : cerca.value)?.focus());
}

async function usa(modo) {
  if (!scelta.value || lavoro.value) return;
  lavoro.value = true;
  erroreUso.value = '';
  try {
    const r = await fetch(`${o().restUrl}scene-slider/usa`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': o().nonce },
      credentials: 'same-origin',
      body: JSON.stringify({ id: scelta.value.id }),
    });
    const d = await r.json().catch(() => null);
    if (!r.ok || !d || !d.settings) throw new Error((d && d.message) || ('HTTP ' + r.status));
    emit('usa', { settings: d.settings, nome: d.nome || scelta.value.nome, media: d.media || {}, colori: d.colori || 0, modo });
  } catch (e) {
    erroreUso.value = t('Non sono riuscito a usare la scena:') + ' ' + (e?.message || e);
  } finally {
    lavoro.value = false;
  }
}
</script>

<style scoped>
.mps-scene { position: absolute; inset: 0; z-index: 60; background: rgba(3, 7, 18, .72); display: flex; align-items: center; justify-content: center; padding: 24px; }
.mps-scene-box { width: min(1180px, 100%); height: min(860px, 100%); background: #111827; border: 1px solid #374151; border-radius: 10px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 24px 60px rgba(0, 0, 0, .5); }
.mps-scene-testa { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 16px 20px 12px; border-bottom: 1px solid #1f2937; }
.mps-scene-titolo { margin: 0; font-size: 16px; font-weight: 700; color: #f3f4f6; }
.mps-scene-sotto { margin: 2px 0 0; font-size: 12px; color: #9ca3af; }
.mps-scene-x { flex: none; display: inline-flex; padding: 6px; border-radius: 6px; color: #9ca3af; background: transparent; border: 0; cursor: pointer; }
.mps-scene-x:hover { color: #f3f4f6; background: #1f2937; }
.mps-scene-filtri { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; padding: 12px 20px; border-bottom: 1px solid #1f2937; }
.mps-scene-cerca { display: inline-flex; align-items: center; gap: 6px; padding: 0 10px; height: 32px; min-width: 220px; border: 1px solid #374151; border-radius: 6px; background: #0b1220; color: #9ca3af; cursor: text; }
.mps-scene-cerca:focus-within { border-color: var(--olo-color-primary, #e1474f); }
.mps-scene-cerca input { border: 0; outline: 0; background: transparent; color: #f3f4f6; font-size: 13px; width: 100%; min-width: 0; padding: 0; box-shadow: none; }
.mps-scene-tipi { display: flex; flex-wrap: wrap; gap: 6px; }
.mps-scene-tipo { font: inherit; font-size: 12px; padding: 5px 10px; border-radius: 999px; border: 1px solid #374151; background: transparent; color: #d1d5db; cursor: pointer; }
.mps-scene-tipo:hover { border-color: #6b7280; }
.mps-scene-tipo.is-on { background: var(--olo-color-primary, #e1474f); border-color: var(--olo-color-primary, #e1474f); color: var(--olo-color-primary-contrast, #fff); }
.mps-scene-n { opacity: .65; font-size: 11px; margin-left: 2px; }
.mps-scene-corpo { flex: 1; overflow-y: auto; padding: 16px 20px 20px; }
.mps-scene-griglia { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
.mps-scene-card { font: inherit; text-align: left; display: flex; flex-direction: column; gap: 3px; padding: 0 0 10px; background: #0b1220; border: 1px solid #1f2937; border-radius: 8px; overflow: hidden; cursor: pointer; color: inherit; transition: border-color .15s ease, transform .15s ease; }
.mps-scene-card:hover { border-color: #4b5563; transform: translateY(-2px); }
.mps-scene-img { position: relative; display: block; aspect-ratio: 16 / 10; background: #1f2937; overflow: hidden; margin-bottom: 7px; }
.mps-scene-img img, .mps-scene-dett-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
.mps-scene-img.is-vuota, .mps-scene-dett-img.is-vuota { display: flex; align-items: center; justify-content: center; padding: 14px; background: linear-gradient(135deg, #1f2937, #0b1220); }
.mps-scene-img-testo { color: #e5e7eb; font-size: 15px; font-weight: 600; line-height: 1.2; text-align: center; }
.mps-scene-bollo { position: absolute; left: 8px; bottom: 8px; font-size: 10px; font-weight: 600; letter-spacing: .02em; padding: 3px 7px; border-radius: 999px; background: rgba(3, 7, 18, .78); color: #f3f4f6; }
.mps-scene-pro { position: absolute; right: 8px; top: 8px; display: inline-flex; align-items: center; gap: 3px; font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; padding: 3px 7px; border-radius: 4px; background: var(--olo-color-primary, #e1474f); color: var(--olo-color-primary-contrast, #fff); }
.mps-scene-pro.is-chiuso { background: rgba(3, 7, 18, .82); color: #fbbf24; }
.mps-scene-nome { padding: 0 10px; font-size: 13px; font-weight: 700; color: #f3f4f6; }
.mps-scene-riga { padding: 0 10px; font-size: 11.5px; color: #9ca3af; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.mps-scene-riga--poco { color: #6b7280; }
.mps-scene-stato { font-size: 13px; color: #9ca3af; margin: 8px 0; }
.mps-scene-stato--err { color: #fca5a5; }
.mps-scene-dettaglio { flex: 1; overflow-y: auto; padding: 14px 20px 20px; }
.mps-scene-indietro { font: inherit; display: inline-flex; align-items: center; gap: 4px; font-size: 12px; color: #d1d5db; background: transparent; border: 0; padding: 4px 6px 4px 0; cursor: pointer; margin-bottom: 12px; }
.mps-scene-indietro:hover { color: #fff; }
.mps-scene-dett-griglia { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 24px; align-items: start; }
@media (max-width: 900px) { .mps-scene-dett-griglia { grid-template-columns: minmax(0, 1fr); } }
.mps-scene-dett-img { aspect-ratio: 16 / 10; border-radius: 8px; overflow: hidden; background: #1f2937; border: 1px solid #1f2937; }
.mps-scene-dett-info { display: flex; flex-direction: column; gap: 8px; min-width: 0; }
.mps-scene-dett-tipo { margin: 0; font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--olo-color-primary, #e1474f); }
.mps-scene-dett-nome { margin: 0; font-size: 22px; font-weight: 700; color: #f9fafb; }
.mps-scene-dett-testo { margin: 0; font-size: 13px; color: #e5e7eb; line-height: 1.5; }
.mps-scene-fatti { margin: 0; padding-left: 16px; font-size: 12px; color: #9ca3af; line-height: 1.6; }
.mps-scene-dett-desc { margin: 0; font-size: 12px; color: #9ca3af; line-height: 1.55; }
.mps-scene-nota { margin: 0; font-size: 11.5px; color: #9ca3af; line-height: 1.5; }
.mps-scene-avviso { margin: 0; font-size: 12.5px; color: #fde68a; line-height: 1.5; }
.mps-scene-azioni { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
.mps-scene-btn { font: inherit; font-size: 13px; font-weight: 600; padding: 8px 14px; border-radius: 6px; border: 1px solid var(--olo-color-primary, #e1474f); background: var(--olo-color-primary, #e1474f); color: var(--olo-color-primary-contrast, #fff); cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; align-self: flex-start; }
.mps-scene-btn:hover { filter: brightness(1.08); }
.mps-scene-btn:disabled { opacity: .55; cursor: progress; }
.mps-scene-btn--2 { background: transparent; color: #e5e7eb; border-color: #4b5563; }
.mps-scene-x:focus-visible, .mps-scene-tipo:focus-visible, .mps-scene-card:focus-visible, .mps-scene-indietro:focus-visible, .mps-scene-btn:focus-visible {
  outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: 2px;
}
@media (prefers-reduced-motion: reduce) { .mps-scene-card { transition: none; } .mps-scene-card:hover { transform: none; } }
</style>
