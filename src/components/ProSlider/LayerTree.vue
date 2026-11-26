<template>
  <!-- Il pannello Livelli (1.4.1424): i livelli in colonna, raccolti in gruppi che si fanno da soli (gruppiLivelli in
       livelloUtils.js). Utente, 24 nov 2026: «quando i livelli sono tanti come si fa a scorrerne 10-15 o più e capirci
       ancora qualche cosa?». Come il pannello Livelli di Slider Revolution 7: gruppi rientrati che si richiudono, ricerca,
       occhio e lucchetto che valgono solo nell'editor; in più «solo» (After Effects) e «Mostra sulla tela» per le scelte. -->
  <div class="mps-lt">
    <div class="mps-lt-testa">
      <span class="mps-lt-titolo">{{ t('Livelli') }} <span class="mps-lt-n">{{ layers.length }}</span></span>
      <button v-if="gruppi.length > 1" type="button" class="mps-lt-link" @click="tuttiAperti ? chiudiTutti() : apriTutti()">
        {{ tuttiAperti ? t('Chiudi i gruppi') : t('Apri i gruppi') }}
      </button>
    </div>
    <label class="mps-lt-cerca">
      <span class="mps-lt-ico" aria-hidden="true" v-html="ico('search')"></span>
      <input v-model="cerca" type="search" :placeholder="t('Cerca un livello')" :aria-label="t('Cerca un livello')" />
    </label>
    <!-- Filtro per tipo e per quello che fanno, ordine (Slider Revolution 7) e livelli messi da parte (gli «shy» di After
         Effects): 1.4.1428 -->
    <div class="mps-lt-strumenti">
      <div class="mps-lt-sel" :title="t('Mostra')">
        <span class="mps-lt-ico" aria-hidden="true" v-html="ico('list-filter')"></span>
        <!-- il menu dell'editor, non quello del browser (1.4.1480): stesso aspetto e stessa tastiera degli altri -->
        <FieldSelect class="mps-lt-fs" ui="dropdown" size="compact" theme="dark" :model-value="filtro" :options="FILTRI.map((f) => ({ value: f.k, label: f.nome }))" :ariaLabel="t('Mostra')" @update:model-value="filtro = $event" />
      </div>
      <div class="mps-lt-sel" :title="t('Ordine')">
        <span class="mps-lt-ico" aria-hidden="true" v-html="ico('arrow-down-up')"></span>
        <FieldSelect class="mps-lt-fs" ui="dropdown" size="compact" theme="dark" :model-value="ordine" :options="[{ value: 'slide', label: t('Come sulla slide') }, { value: 'nome', label: t('Per nome') }, { value: 'tipo', label: t('Per tipo') }]" :ariaLabel="t('Ordine')" @update:model-value="ordine = $event" />
      </div>
    </div>
    <button v-if="quantiDaParte" type="button" class="mps-lt-daparte" @click="mostraDaParte = !mostraDaParte">
      <span class="mps-lt-ico" aria-hidden="true" v-html="ico('archive')"></span>
      {{ mostraDaParte ? t('Togli dall’elenco i messi da parte') : t('%d livelli messi da parte: mostrali').replace('%d', quantiDaParte) }}
    </button>

    <div class="mps-lt-lista" ref="lista">
      <p v-if="!layers.length" class="mps-lt-vuoto">{{ t('Nessun livello in questa slide') }}</p>
      <p v-else-if="cerca && !visti.length" class="mps-lt-vuoto">{{ t('Nessun livello con questo nome') }}</p>

      <div v-for="g in visti" :key="g.key" class="mps-lt-g" :class="{ 'is-scena': inScena(g), 'is-solo': soloKey === g.key, 'is-arrivo': arrivo === g.key }"
           :style="coloreGruppo(g) ? { '--etichetta': coloreGruppo(g) } : null">
        <div class="mps-lt-gt" @dragover.prevent="sopraGruppo($event, g)" @dragleave="arrivo = null" @drop.prevent="lascia(g, null)">
          <input v-if="rinominando === g.key" ref="campoNome" v-model="nuovoNome" class="mps-lt-rinomina" :aria-label="t('Nome del gruppo')"
                 @keydown.enter.prevent="confermaNome(g)" @keydown.esc.stop.prevent="rinominando = null" @blur="confermaNome(g)" />
          <button v-else type="button" class="mps-lt-apri" :aria-expanded="aperto(g) ? 'true' : 'false'" @click="alterna(g)"
                  @dblclick.prevent="iniziaNome(g)" :title="t('Doppio clic per rinominare il gruppo')">
            <span class="mps-lt-freccia" :class="{ 'is-giu': aperto(g) }" aria-hidden="true" v-html="ico('chevron-right')"></span>
            <span class="mps-lt-ico mps-lt-gico" :class="'mps-lt-gico--' + g.tipo" aria-hidden="true" v-html="ico(ICO_GRUPPO[g.tipo] || 'folder')"></span>
            <span class="mps-lt-gnome">{{ nomeGruppo(g) }}</span>
            <span class="mps-lt-n">{{ g.livelli.length }}</span>
          </button>
          <button type="button" class="mps-lt-btn" @click="$emit('select-many', ids(g))" :title="t('Scegli tutto il gruppo: si sposta e si modifica insieme')"
                  :aria-label="t('Scegli tutto il gruppo: si sposta e si modifica insieme')"><span class="mps-lt-ico" aria-hidden="true" v-html="ico('square-dashed-mouse-pointer')"></span></button>
          <button v-if="guardabile(g)" type="button" class="mps-lt-btn" :class="{ 'is-on': inScena(g) }" @click="guarda(g)"
                  :title="inScena(g) ? t('Torna a com’è all’avvio') : t('Mostra sulla tela come quando è scelto')" :aria-pressed="inScena(g) ? 'true' : 'false'"
                  :aria-label="t('Mostra sulla tela come quando è scelto')"><span class="mps-lt-ico" aria-hidden="true" v-html="ico('scan-eye')"></span></button>
          <button v-if="gruppi.length > 1" type="button" class="mps-lt-btn mps-lt-solo" :class="{ 'is-on': soloKey === g.key }" @click="solo(g)"
                  :title="soloKey === g.key ? t('Mostra di nuovo tutti i livelli') : t('Sulla tela solo questo gruppo')" :aria-pressed="soloKey === g.key ? 'true' : 'false'"
                  :aria-label="t('Sulla tela solo questo gruppo')">S</button>
          <button type="button" class="mps-lt-btn" :class="{ 'is-spento': tuttiNascosti(g) }" @click="nascondi(g)"
                  :title="tuttiNascosti(g) ? t('Mostra il gruppo nell’editor') : t('Nascondi il gruppo nell’editor (sul sito resta)')"
                  :aria-label="tuttiNascosti(g) ? t('Mostra il gruppo nell’editor') : t('Nascondi il gruppo nell’editor (sul sito resta)')"><span class="mps-lt-ico" aria-hidden="true" v-html="ico(tuttiNascosti(g) ? 'eye-off' : 'eye')"></span></button>
          <button type="button" class="mps-lt-btn" :class="{ 'is-on': tuttiBloccati(g) }" @click="blocca(g)"
                  :title="tuttiBloccati(g) ? t('Sblocca il gruppo') : t('Blocca il gruppo: sulla tela non si sposta e i clic passano sotto')"
                  :aria-label="tuttiBloccati(g) ? t('Sblocca il gruppo') : t('Blocca il gruppo: sulla tela non si sposta e i clic passano sotto')"><span class="mps-lt-ico" aria-hidden="true" v-html="ico(tuttiBloccati(g) ? 'lock' : 'lock-open')"></span></button>
        </div>

        <div v-if="aperto(g)" class="mps-lt-figli">
          <div v-for="l in g.livelli" :key="l.id" :data-id="l.id" class="mps-lt-l" draggable="true"
               :class="{ 'is-sel': inScelta(l), 'is-primo': l.id === selectedId && !editingGlobal, 'is-nasc': hiddenIds.has(l.id), 'is-bloc': lockedIds.has(l.id),
                         'is-daparte': l.daParte, 'is-sopra': arrivoRiga && arrivoRiga.id === l.id && arrivoRiga.dove === 'sopra', 'is-sotto': arrivoRiga && arrivoRiga.id === l.id && arrivoRiga.dove === 'sotto' }"
               @dragstart="inizia($event, l)" @dragend="trascinato = null; arrivo = null; arrivoRiga = null"
               @dragover.prevent="sopraRiga($event, l)" @drop.prevent.stop="lascia(g, l)">
            <button type="button" class="mps-lt-lnome" @click="clicRiga(l, $event)"
                    :aria-current="l.id === selectedId && !editingGlobal ? 'true' : null" :title="t('Clic per sceglierlo; Ctrl + clic per aggiungerne uno; Maiusc + clic per scegliere tutti quelli fino a qui; trascinalo per cambiare ordine o gruppo')">
              <span v-if="l.etichetta" class="mps-lt-colore" :style="{ background: coloreDi(l.etichetta) }" aria-hidden="true"></span>
              <span class="mps-lt-ico" aria-hidden="true" v-html="ico(ICO_TIPO[l.type] || 'square')"></span>
              <span class="mps-lt-testo" :title="nomeLivello(l)">{{ nomeLivello(l) }}</span>
              <span v-for="a in animazioniLivello(l)" :key="a.k" class="mps-lt-bollo" :class="'mps-lt-bollo--' + a.k" :title="a.titolo">{{ a.testo }}</span>
              <span v-if="valoriPropri(l)" class="mps-lt-punto" :title="t('Ha valori propri su questo schermo')"></span>
            </button>
            <span class="mps-lt-az">
              <button type="button" class="mps-lt-btn" @click="$emit('move-up', l.id)" :title="t('Porta avanti')" :aria-label="t('Porta avanti')"><span class="mps-lt-ico" aria-hidden="true" v-html="ico('arrow-up')"></span></button>
              <button type="button" class="mps-lt-btn" @click="$emit('move-down', l.id)" :title="t('Manda indietro')" :aria-label="t('Manda indietro')"><span class="mps-lt-ico" aria-hidden="true" v-html="ico('arrow-down')"></span></button>
              <button type="button" class="mps-lt-btn" @click="$emit('da-parte', [l.id], !l.daParte)" :title="l.daParte ? t('Rimetti nell’elenco') : t('Metti da parte: sparisce dall’elenco, resta nella slide')"
                      :aria-label="l.daParte ? t('Rimetti nell’elenco') : t('Metti da parte: sparisce dall’elenco, resta nella slide')"><span class="mps-lt-ico" aria-hidden="true" v-html="ico('archive')"></span></button>
              <button type="button" class="mps-lt-btn mps-lt-via" @click="$emit('remove', l.id)" :title="t('Elimina')" :aria-label="t('Elimina')"><span class="mps-lt-ico" aria-hidden="true" v-html="ico('x')"></span></button>
            </span>
            <button type="button" class="mps-lt-btn" :class="{ 'is-spento': hiddenIds.has(l.id) }" @click="$emit('nascondi', [l.id], !hiddenIds.has(l.id))"
                    :title="hiddenIds.has(l.id) ? t('Mostra nell’editor') : t('Nascondi nell’editor (sul sito resta)')" :aria-label="hiddenIds.has(l.id) ? t('Mostra nell’editor') : t('Nascondi nell’editor (sul sito resta)')"><span class="mps-lt-ico" aria-hidden="true" v-html="ico(hiddenIds.has(l.id) ? 'eye-off' : 'eye')"></span></button>
            <button type="button" class="mps-lt-btn" :class="{ 'is-on': lockedIds.has(l.id) }" @click="$emit('blocca', [l.id], !lockedIds.has(l.id))"
                    :title="lockedIds.has(l.id) ? t('Sblocca') : t('Blocca: sulla tela non si sposta e i clic passano sotto')" :aria-label="lockedIds.has(l.id) ? t('Sblocca') : t('Blocca: sulla tela non si sposta e i clic passano sotto')"><span class="mps-lt-ico" aria-hidden="true" v-html="ico(lockedIds.has(l.id) ? 'lock' : 'lock-open')"></span></button>
          </div>
        </div>
      </div>

      <!-- I livelli di tutte le slide (prima stavano in un riquadro a parte sotto le slide) -->
      <div class="mps-lt-g mps-lt-globali">
        <div class="mps-lt-gt">
          <button type="button" class="mps-lt-apri" :aria-expanded="apertiGlobali ? 'true' : 'false'" @click="apertiGlobali = !apertiGlobali">
            <span class="mps-lt-freccia" :class="{ 'is-giu': apertiGlobali }" aria-hidden="true" v-html="ico('chevron-right')"></span>
            <span class="mps-lt-ico mps-lt-gico" aria-hidden="true" v-html="ico('globe')"></span>
            <span class="mps-lt-gnome">{{ t('Su tutte le slide') }}</span>
            <span class="mps-lt-n">{{ globalLayers.length }}</span>
          </button>
          <button v-for="tp in TIPI_GLOBALI" :key="tp.type" type="button" class="mps-lt-btn" @click="apertiGlobali = true; $emit('add-global', tp.type)"
                  :title="t('Aggiungi su tutte le slide:') + ' ' + tp.label" :aria-label="t('Aggiungi su tutte le slide:') + ' ' + tp.label"><span class="mps-lt-ico" aria-hidden="true" v-html="ico(tp.ico)"></span></button>
        </div>
        <div v-if="apertiGlobali" class="mps-lt-figli">
          <p v-if="!globalLayers.length" class="mps-lt-vuoto">{{ t('Nessun livello globale') }}</p>
          <div v-for="l in globalLayers" :key="l.id" :data-id="l.id" class="mps-lt-l" :class="{ 'is-sel': l.id === selectedId && editingGlobal }">
            <button type="button" class="mps-lt-lnome" @click="$emit('select-global', l.id)">
              <span class="mps-lt-ico" aria-hidden="true" v-html="ico(ICO_TIPO[l.type] || 'square')"></span>
              <span class="mps-lt-testo">{{ nomeLivello(l) }}</span>
              <span class="mps-lt-dove">{{ l.globalPosition === 'back' ? t('dietro') : t('davanti') }}</span>
            </button>
            <span class="mps-lt-az mps-lt-az--sempre">
              <button type="button" class="mps-lt-btn mps-lt-via" @click="$emit('remove-global', l.id)" :title="t('Elimina')" :aria-label="t('Elimina')"><span class="mps-lt-ico" aria-hidden="true" v-html="ico('x')"></span></button>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { t } from '@/i18n';
import FieldSelect from '../Builder/fields/FieldSelect.vue';
import lucideIcons from './lucideIconsSvg.js';
import { nomeLivello, animazioniLivello, gruppiLivelli, nomeGruppo, ETICHETTE } from './livelloUtils.js';

const props = defineProps({
  layers: { type: Array, default: () => [] },
  globalLayers: { type: Array, default: () => [] },
  selectedId: { type: String, default: null },
  // i livelli scelti insieme (1.4.1428)
  selectedIds: { type: Set, default: () => new Set() },
  editingGlobal: { type: Boolean, default: false },
  hiddenIds: { type: Set, default: () => new Set() },
  lockedIds: { type: Set, default: () => new Set() },
  soloKey: { type: String, default: null },
  // { nome: valore } = lo stato mostrato sulla tela; {} come all'avvio; null tutti i livelli
  statePreview: { type: Object, default: () => ({}) },
  activeBreakpoint: { type: String, default: 'desktop' },
});
const emit = defineEmits(['select', 'select-many', 'select-global', 'nascondi', 'blocca', 'solo', 'guarda', 'rinomina', 'move-up', 'move-down', 'remove', 'add-global', 'remove-global', 'da-parte', 'sposta']);

const ICO_TIPO = { text: 'type', image: 'image', video: 'video', button: 'rectangle-horizontal', icon: 'star', shape: 'shapes', audio: 'music' };
const ICO_GRUPPO = { manuale: 'folder', scelta: 'mouse-pointer-click', prima: 'play', stato: 'contrast', serie: 'layers', veli: 'blend', testi: 'type', forme: 'shapes', media: 'image' };
// 1.4.1461: gli stessi tipi della barra AGGIUNGI. Prima solo testo, pulsante, immagine e icona, mentre i temi usano su
// tutte le slide 101 forme (veli, cornici, righe) e 2 video: nell'editor non si potevano rifare.
const TIPI_GLOBALI = [
  { type: 'text', label: t('Testo'), ico: 'type' },
  { type: 'image', label: t('Immagine'), ico: 'image' },
  { type: 'video', label: t('Video'), ico: 'video' },
  { type: 'button', label: t('Pulsante'), ico: 'rectangle-horizontal' },
  { type: 'icon', label: t('Icona'), ico: 'star' },
  { type: 'shape', label: t('Forma'), ico: 'shapes' },
  { type: 'audio', label: t('Audio'), ico: 'music' },
];
const ico = (k) => lucideIcons[k] || '';

const cerca = ref('');
const aperti = ref(new Set());
const apertiGlobali = ref(false);
const rinominando = ref(null);
const nuovoNome = ref('');
const campoNome = ref(null);
const lista = ref(null);

const gruppi = computed(() => gruppiLivelli(props.layers));
const piano = (s) => String(s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
// Filtro e ordine (1.4.1428)
const filtro = ref('tutti');
const ordine = ref('slide');
const mostraDaParte = ref(false);
const agisce = (l) => !!(l.action && l.action.type && l.action.type !== 'none');
const FILTRI = [
  { k: 'tutti', nome: t('Tutti i livelli'), f: () => true },
  { k: 'testi', nome: t('Testi e pulsanti'), f: (l) => l.type === 'text' || l.type === 'button' },
  { k: 'media', nome: t('Foto, video e icone'), f: (l) => ['image', 'video', 'icon', 'audio'].includes(l.type) },
  { k: 'forme', nome: t('Forme'), f: (l) => l.type === 'shape' },
  { k: 'clic', nome: t('Che fanno qualcosa al clic'), f: agisce },
  { k: 'stato', nome: t('Che si vedono solo in uno stato'), f: (l) => !!l.stato?.nome },
  { k: 'moto', nome: t('Che si muovono (scorrimento, linea del tempo, ciclo)'), f: (l) => !!l.scrollMove || (l.timeline?.keyframes?.length >= 2) || (l.animLoop && l.animLoop !== 'none') },
  ...ETICHETTE.map((e) => ({ k: 'e:' + e.k, nome: t('Etichetta: %s').replace('%s', e.nome), f: (l) => l.etichetta === e.k })),
];
const coloreDi = (k) => ETICHETTE.find((e) => e.k === k)?.c || '';
const coloreGruppo = (g) => {
  const e = g.livelli[0]?.etichetta;
  return e && g.livelli.every((l) => l.etichetta === e) ? coloreDi(e) : '';
};
const quantiDaParte = computed(() => props.layers.filter((l) => l.daParte).length);
const ORDINE_TIPI = ['text', 'button', 'image', 'video', 'icon', 'shape', 'audio'];
// Con la ricerca o un filtro: i gruppi con almeno un livello che corrisponde, aperti
const visti = computed(() => {
  const q = piano(cerca.value).trim();
  const fx = (FILTRI.find((f) => f.k === filtro.value) || FILTRI[0]).f;
  return gruppi.value
    .map((g) => {
      let ls = g.livelli.filter((l) => (mostraDaParte.value || !l.daParte) && fx(l));
      if (q && !piano(nomeGruppo(g)).includes(q)) ls = ls.filter((l) => piano(nomeLivello(l)).includes(q));
      if (ordine.value === 'nome') ls = ls.slice().sort((a, b) => nomeLivello(a).localeCompare(nomeLivello(b)));
      else if (ordine.value === 'tipo') ls = ls.slice().sort((a, b) => ORDINE_TIPI.indexOf(a.type) - ORDINE_TIPI.indexOf(b.type));
      return ls.length === g.livelli.length && ordine.value === 'slide' ? g : { ...g, livelli: ls };
    })
    .filter((g) => g.livelli.length);
});
const inScelta = (l) => !props.editingGlobal && (props.selectedIds.has(l.id) || l.id === props.selectedId);
// Maiusc + clic sceglie tutti i livelli fra quello scelto e questo, nell'ordine in cui l'elenco li mostra (1.4.1440,
// come nei programmi di grafica); Ctrl + clic ne aggiunge o toglie uno. Prima Maiusc faceva come Ctrl: per togliere
// i quattro livelli di partenza di uno slider nuovo servivano quattro clic.
function clicRiga(l, e) {
  if (e.shiftKey && props.selectedId && props.selectedId !== l.id && !props.editingGlobal) {
    const ordine = visti.value.flatMap((g) => (aperto(g) ? g.livelli : [])).map((x) => x.id);
    const a = ordine.indexOf(props.selectedId), b = ordine.indexOf(l.id);
    if (a >= 0 && b >= 0) {
      const tratto = ordine.slice(Math.min(a, b), Math.max(a, b) + 1);
      emit('select-many', [props.selectedId, ...tratto.filter((id) => id !== props.selectedId)]);
      return;
    }
  }
  emit('select', l.id, e.shiftKey || e.ctrlKey || e.metaKey);
}

// Trascinare le righe (1.4.1428, come l'elenco dei livelli di Smart Slider): sopra o sotto un'altra riga cambia
// l'ordine sulla slide; nella riga di un gruppo lo porta in quel gruppo. In una scelta il livello compare con quella
// scelta, in «Prima di ogni scelta» finché non si è scelto niente.
const trascinato = ref(null);
const arrivo = ref(null);
const arrivoRiga = ref(null);
function inizia(e, l) {
  trascinato.value = l.id;
  try { e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', l.id); } catch (_) { /* alcuni browser non lo permettono */ }
}
function sopraGruppo(e, g) {
  if (!trascinato.value) return;
  arrivo.value = g.key;
  arrivoRiga.value = null;
}
function sopraRiga(e, l) {
  if (!trascinato.value || trascinato.value === l.id) return;
  const r = e.currentTarget.getBoundingClientRect();
  arrivoRiga.value = { id: l.id, dove: e.clientY < r.top + r.height / 2 ? 'sopra' : 'sotto' };
  arrivo.value = null;
}
function lascia(g, l) {
  const id = trascinato.value;
  const dove = arrivoRiga.value?.dove || 'sopra';
  trascinato.value = null;
  arrivo.value = null;
  arrivoRiga.value = null;
  if (!id || (l && l.id === id)) return;
  emit('sposta', { id, su: l ? l.id : null, dove: l ? dove : 'sopra', gruppo: { key: g.key, tipo: g.tipo, nome: nomeGruppo(g), stato: g.stato, valore: g.valore, vale: g.vale, ids: ids(g) } });
}

const aperto = (g) => !!cerca.value.trim() || filtro.value !== 'tutti' || aperti.value.has(g.key);
const tuttiAperti = computed(() => gruppi.value.every((g) => aperti.value.has(g.key)));
function alterna(g) {
  const s = new Set(aperti.value);
  if (s.has(g.key)) s.delete(g.key); else s.add(g.key);
  aperti.value = s;
}
function apriTutti() { aperti.value = new Set(gruppi.value.map((g) => g.key)); }
function chiudiTutti() { aperti.value = new Set(); }

// Una slide nuova: con pochi livelli tutti i gruppi aperti, con tanti tutti chiusi (si apre quello del livello scelto)
watch(() => props.layers, (ls) => {
  aperti.value = (ls || []).length <= 12 ? new Set(gruppi.value.map((g) => g.key)) : new Set();
  cerca.value = '';
  apriDelScelto();
}, { immediate: true });

// Il livello scelto sulla tela si vede nell'elenco: il suo gruppo si apre e la riga scorre in vista
function apriDelScelto() {
  const id = props.selectedId;
  if (!id || props.editingGlobal) return;
  const g = gruppi.value.find((x) => x.livelli.some((l) => l.id === id));
  if (g && !aperti.value.has(g.key)) aperti.value = new Set([...aperti.value, g.key]);
  nextTick(() => {
    const el = lista.value?.querySelector('[data-id="' + (window.CSS?.escape ? CSS.escape(id) : id) + '"]');
    el?.scrollIntoView({ block: 'nearest' });
  });
}
watch(() => props.selectedId, apriDelScelto);

const ids = (g) => g.livelli.map((l) => l.id);
const tuttiNascosti = (g) => g.livelli.every((l) => props.hiddenIds.has(l.id));
const tuttiBloccati = (g) => g.livelli.every((l) => props.lockedIds.has(l.id));
function nascondi(g) { emit('nascondi', ids(g), !tuttiNascosti(g)); }
function blocca(g) { emit('blocca', ids(g), !tuttiBloccati(g)); }
function solo(g) { emit('solo', props.soloKey === g.key ? null : g.key, ids(g)); }

// «Mostra sulla tela»: la scena di quella scelta (lo stato che la fa vedere)
const valoreDi = (g) => (g.tipo === 'scelta' || g.tipo === 'prima' ? g.valore : String(g.vale || ''));
const guardabile = (g) => (g.tipo === 'scelta' || g.tipo === 'prima' || (g.tipo === 'stato' && !String(g.vale).startsWith('!') && !String(g.vale).includes(',')));
const inScena = (g) => guardabile(g) && !!props.statePreview && Object.prototype.hasOwnProperty.call(props.statePreview, g.stato) && props.statePreview[g.stato] === valoreDi(g);
function guarda(g) { emit('guarda', inScena(g) ? null : { stato: g.stato, valore: valoreDi(g) }); }

function iniziaNome(g) {
  rinominando.value = g.key;
  nuovoNome.value = nomeGruppo(g);
  nextTick(() => { const c = Array.isArray(campoNome.value) ? campoNome.value[0] : campoNome.value; c?.focus(); c?.select(); });
}
function confermaNome(g) {
  if (rinominando.value !== g.key) return;
  rinominando.value = null;
  const n = nuovoNome.value.trim();
  if (n === nomeGruppo(g)) return;
  // vuoto = torna al gruppo automatico
  emit('rinomina', ids(g), n);
}

function valoriPropri(l) {
  if (props.activeBreakpoint === 'desktop') return false;
  const ov = l.responsive?.[props.activeBreakpoint];
  return !!ov && Object.keys(ov).length > 0;
}
</script>

<style scoped>
.mps-lt { display: flex; flex-direction: column; min-height: 0; flex: 1; background: #111827; border-top: 1px solid #374151; }
.mps-lt-testa { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 8px 8px 4px; }
.mps-lt-titolo { font-size: 12px; font-weight: 600; color: #d1d5db; }
.mps-lt-n { font-size: 10px; font-weight: 500; color: #6b7280; margin-left: 2px; }
.mps-lt-link { font: inherit; font-size: 10px; color: #9ca3af; background: transparent; border: 0; cursor: pointer; padding: 2px 0; }
.mps-lt-link:hover { color: #f3f4f6; }
.mps-lt-cerca { display: flex; align-items: center; gap: 5px; margin: 0 8px 6px; padding: 0 7px; height: 26px; border: 1px solid #374151; border-radius: 5px; background: #0b1220; color: #6b7280; }
.mps-lt-cerca:focus-within { border-color: var(--olo-color-primary, #e1474f); }
.mps-lt-cerca input { flex: 1; min-width: 0; border: 0; outline: 0; background: transparent; color: #e5e7eb; font-size: 11px; padding: 0; box-shadow: none; }
.mps-lt-lista { flex: 1; min-height: 0; overflow-y: auto; padding: 0 4px 8px; }
.mps-lt-vuoto { font-size: 10px; color: #6b7280; font-style: italic; margin: 6px 6px; }
.mps-lt-ico { display: inline-flex; flex: none; }
.mps-lt-ico :deep(svg) { width: 13px; height: 13px; }
.mps-lt-g { border-radius: 5px; margin-bottom: 1px; }
.mps-lt-g.is-scena { background: rgba(250, 204, 21, .07); box-shadow: inset 2px 0 0 #facc15; }
.mps-lt-g.is-solo { background: rgba(96, 165, 250, .08); box-shadow: inset 2px 0 0 #60a5fa; }
.mps-lt-gt { display: flex; align-items: center; gap: 1px; min-height: 26px; }
/* 1.4.1461: sette tipi da aggiungere su tutte le slide: la riga va a capo invece di uscire dalla colonna */
.mps-lt-globali .mps-lt-gt { flex-wrap: wrap; }
.mps-lt-apri { font: inherit; flex: 1; min-width: 0; display: flex; align-items: center; gap: 5px; padding: 4px 4px; background: transparent; border: 0; border-radius: 4px; color: #e5e7eb; cursor: pointer; text-align: left; }
.mps-lt-apri:hover { background: #1f2937; }
.mps-lt-freccia { display: inline-flex; color: #6b7280; transition: transform .12s ease; }
.mps-lt-freccia :deep(svg) { width: 12px; height: 12px; }
.mps-lt-freccia.is-giu { transform: rotate(90deg); }
.mps-lt-gico { color: #9ca3af; }
.mps-lt-gico--scelta, .mps-lt-gico--prima, .mps-lt-gico--stato { color: #fcd34d; }
.mps-lt-gnome { flex: 1; min-width: 0; font-size: 11.5px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.mps-lt-rinomina { flex: 1; min-width: 0; height: 24px; font-size: 11.5px; padding: 0 6px; background: #0b1220; color: #f3f4f6; border: 1px solid var(--olo-color-primary, #e1474f); border-radius: 4px; outline: 0; }
.mps-lt-btn { font: inherit; display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; flex: none; padding: 0; background: transparent; border: 0; border-radius: 4px; color: #6b7280; cursor: pointer; font-size: 10px; font-weight: 700; }
.mps-lt-btn:hover { background: #1f2937; color: #e5e7eb; }
.mps-lt-btn.is-on { color: #60a5fa; }
.mps-lt-btn.is-spento { color: #4b5563; }
.mps-lt-solo.is-on { color: #fff; background: #2563eb; }
.mps-lt-via:hover { color: #f87171; }
.mps-lt-figli { padding: 0 0 4px 14px; }
.mps-lt-l { display: flex; align-items: center; gap: 1px; border-radius: 4px; min-height: 24px; }
.mps-lt-l:hover { background: #1a2332; }
.mps-lt-l.is-sel { background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 18%, #111827); }
.mps-lt-l.is-nasc .mps-lt-lnome { opacity: .45; }
.mps-lt-l.is-bloc .mps-lt-testo { font-style: italic; }
.mps-lt-lnome { font: inherit; flex: 1; min-width: 0; display: flex; align-items: center; gap: 5px; padding: 3px 4px; background: transparent; border: 0; color: #cbd5e1; cursor: pointer; text-align: left; font-size: 11px; }
.mps-lt-testo { flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.mps-lt-dove { font-size: 9.5px; color: #6b7280; }
.mps-lt-bollo { flex: none; font-size: 9px; line-height: 1; padding: 2px 3px; border-radius: 3px; background: #1f2937; color: #9ca3af; }
.mps-lt-bollo--stato, .mps-lt-bollo--azione, .mps-lt-bollo--pulisci { background: #4a3a14; color: #fcd34d; }
.mps-lt-bollo--scroll, .mps-lt-bollo--timeline { background: #172554; color: #93c5fd; }
.mps-lt-punto { flex: none; width: 6px; height: 6px; border-radius: 50%; background: #facc15; }
/* I comandi compaiono passando col puntatore (o col fuoco), come nei pannelli Livelli di Figma e Slider Revolution:
   così i nomi dei gruppi si leggono interi. Restano sempre visibili quelli accesi (scena, solo, nascosto, bloccato). */
.mps-lt-gt .mps-lt-btn, .mps-lt-l > .mps-lt-btn { visibility: hidden; }
.mps-lt-gt:hover .mps-lt-btn, .mps-lt-gt:focus-within .mps-lt-btn, .mps-lt-l:hover > .mps-lt-btn, .mps-lt-l:focus-within > .mps-lt-btn,
.mps-lt-btn.is-on, .mps-lt-btn.is-spento, .mps-lt-globali .mps-lt-gt .mps-lt-btn { visibility: visible; }
.mps-lt-gt:not(:hover):not(:focus-within) .mps-lt-btn:not(.is-on):not(.is-spento) { width: 0; overflow: hidden; }
.mps-lt-l:not(:hover):not(:focus-within) > .mps-lt-btn:not(.is-on):not(.is-spento) { width: 0; overflow: hidden; }
/* 1.4.1479: la regola qui sopra (sei classi) vinceva su «.mps-lt-globali .mps-lt-gt .mps-lt-btn» e i pulsanti di «Su tutte
   le slide» restavano larghi 0 finché il puntatore non passava sulla riga: ora la stessa regola, col gruppo davanti */
.mps-lt-globali .mps-lt-gt .mps-lt-btn,
.mps-lt-globali .mps-lt-gt:not(:hover):not(:focus-within) .mps-lt-btn:not(.is-on):not(.is-spento) { width: 22px; overflow: visible; }
.mps-lt-az { display: none; }
.mps-lt-strumenti { display: flex; gap: 4px; margin: 0 8px 6px; }
.mps-lt-sel { flex: 1; min-width: 0; display: flex; align-items: center; gap: 3px; color: #6b7280; }
.mps-lt-sel .mps-lt-fs { flex: 1; min-width: 0; }
.mps-lt-sel select { flex: 1; min-width: 0; height: 22px; border: 0; background: transparent; color: #d1d5db; font-size: 10.5px; padding: 0; outline: 0; box-shadow: none; }
.mps-lt-sel select option { background: #111827; color: #e5e7eb; }
.mps-lt-sel:focus-within { border-color: var(--olo-color-primary, #e1474f); }
.mps-lt-daparte { font: inherit; display: flex; align-items: center; gap: 5px; margin: 0 8px 6px; padding: 3px 6px; font-size: 10.5px; color: #9ca3af; background: #1f2937; border: 0; border-radius: 4px; cursor: pointer; text-align: left; }
.mps-lt-daparte:hover { color: #f3f4f6; }
.mps-lt-g[style*="--etichetta"] > .mps-lt-gt { box-shadow: inset 3px 0 0 var(--etichetta); }
.mps-lt-g.is-arrivo { background: rgba(96, 165, 250, .12); outline: 1px dashed #60a5fa; }
.mps-lt-l.is-sopra { box-shadow: inset 0 2px 0 #60a5fa; }
.mps-lt-l.is-sotto { box-shadow: inset 0 -2px 0 #60a5fa; }
.mps-lt-l.is-daparte .mps-lt-testo { color: #6b7280; }
.mps-lt-l.is-primo { box-shadow: inset 2px 0 0 var(--olo-color-primary, #e1474f); }
.mps-lt-colore { flex: none; width: 7px; height: 7px; border-radius: 2px; }
.mps-lt-daparte:focus-visible, .mps-lt-sel:focus-within { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: -2px; }
/* (1.4.1445) Porta avanti/indietro, metti da parte ed elimina compaiono solo sul livello scelto (o col fuoco da
   tastiera), non al passaggio: comparendo sotto il puntatore accorciavano il nome, e il clic per scegliere un livello
   dal nome lungo cadeva su «Porta avanti» (rifacendo a mano Elastan). Occhio e lucchetto restano al passaggio. */
/* 1.4.1446: col fuoco da TASTIERA soltanto. Con :focus-within il clic del mouse sul nome dava il fuoco al pulsante,
   i comandi comparivano e accorciavano il nome prima del rilascio: il puntatore finiva su un'icona e il livello dal
   nome lungo non si sceglieva. */
.mps-lt-l.is-primo .mps-lt-az, .mps-lt-l:has(:focus-visible) .mps-lt-az, .mps-lt-az--sempre { display: inline-flex; }
.mps-lt-link:focus-visible, .mps-lt-apri:focus-visible, .mps-lt-btn:focus-visible, .mps-lt-lnome:focus-visible { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: -2px; }
@media (prefers-reduced-motion: reduce) { .mps-lt-freccia { transition: none; } }
</style>
