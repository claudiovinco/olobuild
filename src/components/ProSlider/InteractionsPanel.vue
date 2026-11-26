<template>
  <!-- Le interazioni della slide come regole (1.4.1426). Utente, 24 nov 2026: «in altri slider pro che soluzioni hanno
       adottato per gestire montaggi complessi con iterazioni complesse?». Come le azioni di Slider Revolution 7 e
       attivatore → bersaglio → azioni di Webflow: ogni livello cliccabile è una frase («Clic su Spia olio → mostra
       Scelta: olio»), le scelte di uno stato stanno insieme e se ne aggiunge una copiando l'ultima. Sotto restano gli
       stati di sempre: niente cambia nel salvataggio. -->
  <div class="mps-int">
    <div class="mps-int-testa">
      <span class="mps-int-titolo">{{ t('Interazioni della slide') }}</span>
      <InfoTip :testo="t('Cosa succede quando chi guarda clicca. Clicca una riga per scegliere il livello che fa partire l’interazione: sotto ne vedi e cambi l’azione. Una scelta è un gruppo di livelli che compare col clic sul suo livello; «Aggiungi una scelta» copia l’ultima, poi cambi testi e immagini.')" :titolo="t('Interazioni della slide')" />
    </div>

    <p v-if="!scelte.length && !regole.length && !avvio.length" class="mps-int-vuoto">{{ t('Nessuna interazione in questa slide: scegli un livello e dagli un’azione al clic qui sotto.') }}</p>

    <!-- Le scelte di uno stato -->
    <section v-for="sc in scelte" :key="sc.stato" class="mps-int-scelta">
      <p class="mps-int-sotto">{{ t('Scelta «%1» · %2 scelte').replace('%1', sc.stato).replace('%2', sc.voci.length) }}</p>
      <div v-for="v in sc.voci" :key="v.key" class="mps-int-voce" :class="{ 'is-sel': v.trigger?.id === selectedId, 'is-scena': inScena(sc.stato, v.valore) }">
        <button type="button" class="mps-int-frase" @click="v.trigger && $emit('select', v.trigger.id)">
          <span class="mps-int-ico" aria-hidden="true" v-html="ico('mouse-pointer-click')"></span>
          <span class="mps-int-testo">{{ (v.trigger?.action?.trigger === 'hover' ? t('Passando su «%s»') : t('Clic su «%s»')).replace('%s', v.nomeTrigger) }} <span class="mps-int-freccia">→</span> {{ v.mostra || t('imposta %1 = %2').replace('%1', sc.stato).replace('%2', v.valore) }}</span>
        </button>
        <button type="button" class="mps-int-btn" :class="{ 'is-on': inScena(sc.stato, v.valore) }" @click="$emit('guarda', inScena(sc.stato, v.valore) ? null : { stato: sc.stato, valore: v.valore })"
                :title="t('Mostra sulla tela come quando è scelto')" :aria-label="t('Mostra sulla tela come quando è scelto')" :aria-pressed="inScena(sc.stato, v.valore) ? 'true' : 'false'"><span class="mps-int-ico" aria-hidden="true" v-html="ico('scan-eye')"></span></button>
        <button v-if="daTogliere !== v.key" type="button" class="mps-int-btn mps-int-via" @click="daTogliere = v.key" :title="t('Togli questa scelta')" :aria-label="t('Togli questa scelta')"><span class="mps-int-ico" aria-hidden="true" v-html="ico('trash-2')"></span></button>
        <span v-else class="mps-int-conferma">
          <button type="button" class="mps-int-si" @click="daTogliere = null; $emit('togli-scelta', { stato: sc.stato, valore: v.valore })">{{ t('Togli %d livelli').replace('%d', v.livelli) }}</button>
          <button type="button" class="mps-int-no" @click="daTogliere = null">{{ t('No') }}</button>
        </span>
      </div>
      <button type="button" class="mps-int-aggiungi" @click="$emit('aggiungi-scelta', sc.stato)">
        <span class="mps-int-ico" aria-hidden="true" v-html="ico('plus')"></span>
        {{ t('Aggiungi una scelta (copia «%s»)').replace('%s', sc.voci[sc.voci.length - 1].nomeTrigger) }}
      </button>
    </section>

    <!-- Le altre azioni al clic -->
    <div v-if="regole.length" class="mps-int-altre">
      <p class="mps-int-sotto">{{ t('Altre azioni') }}</p>
      <div v-for="r in regole" :key="r.key" class="mps-int-voce" :class="{ 'is-sel': r.id === selectedId }">
        <button type="button" class="mps-int-frase" @click="$emit('select', r.id)">
          <span class="mps-int-ico" aria-hidden="true" v-html="ico(r.ico)"></span>
          <span class="mps-int-testo">{{ r.frase }}</span>
        </button>
      </div>
    </div>

    <!-- All'avvio -->
    <div v-if="avvio.length" class="mps-int-altre">
      <p class="mps-int-sotto">{{ t('All’avvio') }}</p>
      <div v-for="a in avvio" :key="a.key" class="mps-int-voce">
        <span class="mps-int-frase mps-int-frase--fissa">
          <span class="mps-int-ico" aria-hidden="true" v-html="ico('play')"></span>
          <span class="mps-int-testo">{{ a.frase }}</span>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { t } from '@/i18n';
import InfoTip from '../Builder/InfoTip.vue';
import lucideIcons from './lucideIconsSvg.js';
import { nomeLivello, gruppiLivelli, nomeGruppo, leggiStati } from './livelloUtils.js';

const props = defineProps({
  // tutti i livelli dello slider (1.4.1475): il bersaglio di «mostra o nasconde» può stare in un'altra slide
  tuttiLivelli: { type: Array, default: () => [] },
  layers: { type: Array, default: () => [] },
  slides: { type: Array, default: () => [] },
  selectedId: { type: String, default: null },
  stati: { type: String, default: '' },
  statePreview: { type: Object, default: () => ({}) },
});
defineEmits(['select', 'guarda', 'aggiungi-scelta', 'togli-scelta']);

const ico = (k) => lucideIcons[k] || '';
const daTogliere = ref(null);
const nomeSt = (n) => String(n || '').trim().toLowerCase().replace(/[^a-z0-9_-]/g, '');
const gruppi = computed(() => gruppiLivelli(props.layers));

// Il nome del livello che fa partire la scelta: il suo nome, l'etichetta per la tastiera, il testo, il file
function nomeTrigger(l) {
  return String(l?.name || '').trim() || String(l?.action?.label || '').trim() || nomeLivello(l);
}

// Le scelte, per stato: una voce per ogni livello che imposta un valore col clic, in ordine da sinistra a destra
const scelte = computed(() => {
  const per = new Map();
  for (const g of gruppi.value) {
    if (g.tipo !== 'scelta') continue;
    const trigger = g.livelli.find((l) => l.action?.type === 'setState');
    if (!trigger) continue;
    if (!per.has(g.stato)) per.set(g.stato, []);
    const altri = g.livelli.filter((l) => l !== trigger);
    per.get(g.stato).push({
      key: g.key, valore: g.valore, trigger, nomeTrigger: nomeTrigger(trigger), livelli: g.livelli.length,
      // «mostra la sua scelta (5 livelli)»: il nome del gruppo ripeteva quello del livello cliccato
      mostra: altri.length ? t('mostra la sua scelta (%d livelli)').replace('%d', altri.length) : '', x: Number(trigger.x) || 0,
    });
  }
  return [...per.entries()].map(([stato, voci]) => ({ stato, voci: voci.sort((a, b) => a.x - b.x) }));
});

// Le altre azioni al clic, in frasi
const regole = computed(() => {
  const out = [];
  for (const l of props.layers || []) {
    const a = l.action;
    const chi = nomeTrigger(l);
    if (a && a.type && a.type !== 'none' && a.type !== 'setState') {
      let frase = '', icona = 'mouse-pointer-click';
      const clic = (a.trigger === 'hover' ? t('Passando su «%s»') : t('Clic su «%s»')).replace('%s', chi) + ' → ';
      if (a.type === 'cycleState') frase = clic + t('passa %1 al valore dopo: %2').replace('%1', nomeSt(a.target)).replace('%2', String(a.values || '').split(',').map((x) => x.trim()).filter(Boolean).join(' → '));
      else if (a.type === 'toggleLayer') {
        const bers = [...(props.tuttiLivelli || []), ...(props.layers || [])].find((x) => x.id === a.target);
        frase = clic + t('mostra o nasconde «%s»').replace('%s', bers ? nomeLivello(bers) : '?');
      } else if (a.type === 'nextSlide') frase = clic + t('va alla slide dopo');
      else if (a.type === 'prevSlide') frase = clic + t('torna alla slide prima');
      else if (a.type === 'goToSlide') frase = clic + t('va alla slide %d').replace('%d', (Number(a.target) || 0) + 1);
      else if (a.type === 'openUrl') { frase = clic + t('apre %s').replace('%s', a.url || '?'); icona = 'external-link'; }
      else if (a.type === 'scrollBelow') frase = clic + t('scorre sotto lo slider');
      else frase = clic + a.type;
      out.push({ key: l.id, id: l.id, frase, ico: icona });
    }
    if (l.pulisci?.attivo) {
      out.push({ key: l.id + ':p', id: l.id, frase: t('Pulendo «%1» col puntatore → %2').replace('%1', chi).replace('%2', l.pulisci.stato || t('si scopre quello che c’è sotto')), ico: 'eraser' });
    }
  }
  return out;
});

// All'avvio: gli stati scritti nelle impostazioni e i gruppi «Prima di ogni scelta»
const avvio = computed(() => {
  const out = [];
  for (const [n, v] of Object.entries(leggiStati(props.stati))) {
    if (v !== '') out.push({ key: 's:' + n, frase: t('%1 vale %2').replace('%1', n).replace('%2', v) });
  }
  for (const g of gruppi.value) {
    if (g.tipo === 'prima') out.push({ key: g.key, frase: t('si vede «%s»').replace('%s', nomeGruppo(g)) + ' (' + g.livelli.length + ')' });
  }
  return out;
});

const inScena = (stato, valore) => !!props.statePreview && Object.prototype.hasOwnProperty.call(props.statePreview, stato) && props.statePreview[stato] === valore;
</script>

<style scoped>
.mps-int { background: #111827; border-bottom: 1px solid #374151; padding: 8px 8px 10px; overflow-y: auto; }
.mps-int-testa { display: flex; align-items: center; gap: 4px; margin-bottom: 6px; }
.mps-int-titolo { font-size: 12px; font-weight: 600; color: #d1d5db; }
.mps-int-vuoto { font-size: 11px; color: #9ca3af; line-height: 1.45; margin: 2px 0 0; }
.mps-int-sotto { font-size: 10px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; color: #fcd34d; margin: 8px 0 4px; }
.mps-int-scelta { border: 1px solid #374151; border-radius: 6px; padding: 2px 6px 6px; margin-bottom: 8px; }
.mps-int-voce { display: flex; align-items: center; gap: 1px; border-radius: 4px; }
.mps-int-voce:hover { background: #1a2332; }
.mps-int-voce.is-sel { background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 22%, #111827); }
.mps-int-voce.is-scena { box-shadow: inset 2px 0 0 #facc15; }
.mps-int-frase { font: inherit; flex: 1; min-width: 0; display: flex; align-items: flex-start; gap: 6px; padding: 4px; background: transparent; border: 0; color: #e5e7eb; text-align: left; cursor: pointer; font-size: 11px; line-height: 1.4; }
.mps-int-frase--fissa { cursor: default; color: #cbd5e1; }
.mps-int-testo { flex: 1; min-width: 0; }
.mps-int-freccia { color: #fcd34d; }
.mps-int-ico { display: inline-flex; flex: none; color: #9ca3af; margin-top: 1px; }
.mps-int-ico :deep(svg) { width: 13px; height: 13px; }
.mps-int-btn { font: inherit; display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; flex: none; padding: 0; background: transparent; border: 0; border-radius: 4px; color: #6b7280; cursor: pointer; }
.mps-int-btn:hover { background: #1f2937; color: #e5e7eb; }
.mps-int-btn.is-on { color: #facc15; }
.mps-int-via:hover { color: #f87171; }
.mps-int-conferma { display: inline-flex; gap: 4px; flex: none; }
.mps-int-si, .mps-int-no { font: inherit; font-size: 10px; padding: 3px 6px; border-radius: 4px; border: 1px solid #4b5563; background: transparent; color: #e5e7eb; cursor: pointer; }
.mps-int-si { border-color: #f87171; color: #fecaca; }
.mps-int-aggiungi { font: inherit; width: 100%; margin-top: 6px; display: inline-flex; align-items: center; justify-content: center; gap: 5px; padding: 6px; font-size: 11px; font-weight: 600; color: #e5e7eb; background: #1f2937; border: 1px dashed #4b5563; border-radius: 5px; cursor: pointer; }
.mps-int-aggiungi:hover { border-color: var(--olo-color-primary, #e1474f); }
.mps-int-altre { margin-bottom: 4px; }
.mps-int-frase:focus-visible, .mps-int-btn:focus-visible, .mps-int-aggiungi:focus-visible, .mps-int-si:focus-visible, .mps-int-no:focus-visible { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: -2px; }
</style>
