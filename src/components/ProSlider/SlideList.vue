<template>
  <div class="mb-w-full mb-bg-gray-900 mb-border-r mb-border-gray-700 mb-flex mb-flex-col mb-overflow-hidden">
    <div class="mb-p-2 mb-border-b mb-border-gray-700 mb-text-xs mb-font-semibold mb-text-gray-300 mb-flex mb-items-center mb-justify-between">
      <span>{{ t('Slide') }} ({{ slides.length }})</span>
      <div class="mb-flex mb-items-center mb-gap-1">
        <button @click="doExport" class="mb-text-gray-400 hover:mb-text-primary-300" :title="t('Esporta le slide')">&#8595;</button>
        <button @click="triggerImport" class="mb-text-gray-400 hover:mb-text-primary-300" :title="t('Importa le slide')">&#8593;</button>
        <button @click="$emit('add')" class="mb-text-primary-400 hover:mb-text-primary-300" :title="t('Aggiungi slide')">+</button>
      </div>
      <input ref="importInput" type="file" accept=".json" class="mb-hidden" @change="handleImport" />
    </div>
    <!-- 1.4.1475: importando si sceglie se aggiungere le slide o sostituire quelle che ci sono (prima le sostituiva sempre) -->
    <div v-if="daImportare" class="mps-sl-importa" role="alert">
      <span>{{ daImportare.slides.length === 1 ? t('Importa 1 slide') : t('Importa %d slide').replace('%d', daImportare.slides.length) }}</span>
      <div class="mps-sl-importa-fila">
        <button type="button" class="mps-sl-btn is-si" @click="importa('aggiungi')">{{ t('Aggiungi in fondo') }}</button>
        <button type="button" class="mps-sl-btn is-via" @click="importa('sostituisci')">{{ t('Sostituisci tutte') }}</button>
        <button type="button" class="mps-sl-btn" @click="daImportare = null">{{ t('Annulla') }}</button>
      </div>
    </div>
    <!-- Scene dei temi (1.4.1422): si parte da un Pro Slider vero, poi si cambiano testi e foto -->
    <div class="mb-px-2 mb-pt-2">
      <button type="button" @click="$emit('scene')" class="mps-scene-apri">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" /><path d="M3 9h18M9 21V9" /></svg>
        {{ t('Scene dei temi') }}
      </button>
    </div>
    <div class="mb-flex-1 mb-overflow-y-auto mb-p-2 mb-space-y-2">
      <!-- 1.4.1475: le slide si riordinano trascinando la miniatura (o con «Sposta prima / dopo») -->
      <div
        v-for="(slide, idx) in slides"
        :key="slide.id"
        @click="$emit('select', idx)"
        draggable="true"
        @dragstart="inizioTrascina($event, idx)"
        @dragover.prevent="sopra($event, idx)"
        @dragenter.prevent
        @dragleave="esci($event)"
        @drop.prevent="lascia($event, idx)"
        @dragend="fineTrascina"
        :class="[
          'mb-rounded mb-overflow-hidden mb-cursor-pointer mb-border-2 mb-transition-colors mb-relative mb-group',
          idx === activeIndex ? 'mb-border-primary-500' : 'mb-border-gray-700 hover:mb-border-gray-500',
          trascinata === idx ? 'mps-sl-trascinata' : '',
          dove && dove.idx === idx ? (dove.prima ? 'mps-sl-sopra' : 'mps-sl-sotto') : ''
        ]"
      >
        <!-- Thumbnail -->
        <div class="mb-h-20 mb-bg-gray-800" :style="thumbStyle(slide)">
          <span class="mb-absolute mb-top-1 mb-left-1 mb-text-[10px] mb-bg-black/60 mb-text-white mb-px-1.5 mb-rounded">{{ idx + 1 }}</span>
          <!-- Slide dagli articoli (1.4.1457): la slide modello, che sul sito si ripete una volta per ogni articolo -->
          <span v-if="idx === contenuti.modello.value" class="mps-sl-modello" :title="t('Slide modello: sul sito si ripete una volta per ogni articolo')">{{ t('Modello') }}</span>
          <span v-if="slide.persistFor > 0" class="mb-absolute mb-bottom-1 mb-left-1 mb-text-[9px] mb-bg-primary-600/80 mb-text-white mb-px-1 mb-rounded" :title="t('Persiste per') + ' ' + slide.persistFor + ' ' + t('slide')">P{{ slide.persistFor }}</span>
        </div>
        <!-- Actions -->
        <div v-if="confermaVia !== idx" class="mb-absolute mb-top-1 mb-right-1 mb-flex mb-gap-2 mb-opacity-0 group-hover:mb-opacity-100 group-focus-within:mb-opacity-100 mb-transition-opacity">
          <!-- 1.4.1446: icone del set (prima una «D» e una ×), più distanti: con la barra di scorrimento dell'elenco
               «Duplica» finiva a pochi pixel da «Elimina» -->
          <button type="button" @click.stop="$emit('move', idx, idx - 1)" :disabled="idx === 0" class="mps-sl-az" :title="t('Sposta prima')" :aria-label="t('Sposta prima')"><span aria-hidden="true" v-html="ICO_SU"></span></button>
          <button type="button" @click.stop="$emit('move', idx, idx + 1)" :disabled="idx === slides.length - 1" class="mps-sl-az" :title="t('Sposta dopo')" :aria-label="t('Sposta dopo')"><span aria-hidden="true" v-html="ICO_GIU"></span></button>
          <button type="button" @click.stop="$emit('duplicate', idx)" class="mps-sl-az" :title="t('Duplica la slide')" :aria-label="t('Duplica la slide')"><span aria-hidden="true" v-html="ICO_COPIA"></span></button>
          <button v-if="slides.length > 1" type="button" @click.stop="confermaVia = idx" class="mps-sl-az mps-sl-az--via" :title="t('Elimina la slide')" :aria-label="t('Elimina la slide')"><span aria-hidden="true" v-html="ICO_VIA"></span></button>
        </div>
        <!-- 1.4.1475: eliminare chiede conferma (prima la slide spariva al primo clic, coi suoi livelli) -->
        <div v-else class="mps-sl-conferma" role="alert" @click.stop>
          <span>{{ t('Eliminare la slide %d?').replace('%d', idx + 1) }}</span>
          <div class="mps-sl-importa-fila">
            <button type="button" class="mps-sl-btn is-via" @click.stop="confermaVia = null; $emit('remove', idx)">{{ t('Elimina') }}</button>
            <button type="button" class="mps-sl-btn" @click.stop="confermaVia = null">{{ t('No') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { t } from '@/i18n';
import lucideIcons from './lucideIconsSvg.js';
import { useContenutiSlider, conEsempio } from './datiArticolo.js';

const ICO_COPIA = lucideIcons.copy || '';
const ICO_VIA = lucideIcons.x || '';
const ICO_SU = lucideIcons['chevron-up'] || '';
const ICO_GIU = lucideIcons['chevron-down'] || '';

const props = defineProps({
  slides: { type: Array, required: true },
  activeIndex: { type: Number, default: 0 },
  globalBackground: { type: Object, default: null },
});

const emit = defineEmits(['select', 'add', 'duplicate', 'remove', 'move', 'import-slides', 'scene']);

const importInput = ref(null);
const confermaVia = ref(null);
const daImportare = ref(null);

// Trascinare una miniatura (1.4.1475): la riga d'arrivo mostra se va prima o dopo
const trascinata = ref(null);
const dove = ref(null);
function inizioTrascina(e, idx) {
  trascinata.value = idx;
  confermaVia.value = null;
  try { e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', 'slide-' + idx); } catch (_) { /* browser vecchio */ }
}
function sopra(e, idx) {
  if (trascinata.value === null) return;
  const r = e.currentTarget.getBoundingClientRect();
  dove.value = { idx, prima: e.clientY < r.top + r.height / 2 };
}
// la riga si spegne solo uscendo dalla miniatura (non passando sopra il numero o le icone che ha dentro)
function esci(e) {
  if (!e.currentTarget.contains(e.relatedTarget)) dove.value = null;
}
// prima o dopo si decide dal punto in cui si lascia (non dall'ultima riga accesa, che può essersi spenta)
function lascia(e, idx) {
  const da = trascinata.value;
  const r = e.currentTarget.getBoundingClientRect();
  const prima = e.clientY < r.top + r.height / 2;
  fineTrascina();
  if (da === null) return;
  let a = prima ? idx : idx + 1;
  if (a > da) a--;
  if (a !== da) emit('move', da, a);
}
function fineTrascina() { trascinata.value = null; dove.value = null; }
function importa(modo) {
  const json = daImportare.value;
  daImportare.value = null;
  if (json) emit('import-slides', { json, modo });
}

function doExport() {
  const payload = {
    olo_export: 'proslider',
    version: (window.oloData || {}).pluginVersion || '1.22.0',
    slides: JSON.parse(JSON.stringify(props.slides)),
    globalBackground: props.globalBackground ? JSON.parse(JSON.stringify(props.globalBackground)) : {},
  };
  const blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'proslider-export.json';
  a.click();
  URL.revokeObjectURL(url);
}

function triggerImport() {
  importInput.value?.click();
}

async function handleImport(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  e.target.value = '';

  try {
    const text = await file.text();
    const json = JSON.parse(text);

    if (json.olo_export !== 'proslider' || !Array.isArray(json.slides)) {
      alert(t('File non valido: non è un export ProSlider.'));
      return;
    }

    daImportare.value = json;
  } catch (err) {
    console.error('ProSlider import error:', err);
    alert(t("Errore durante l'importazione:") + ' ' + err.message);
  }
}

const contenuti = useContenutiSlider();

function thumbStyle(slide) {
  const bg = slide.background || {};
  if (bg.type === 'transparent') {
    return { background: 'repeating-conic-gradient(#808080 0% 25%, #c0c0c0 0% 50%) 0 0 / 16px 16px' };
  }
  // 1.4.1447: anche la slide col video mostra la sua immagine d'attesa (prima la miniatura restava vuota)
  if ((bg.type === 'image' || bg.type === 'video') && bg.image) {
    return { backgroundImage: `url(${conEsempio(bg.image, contenuti.esempio.value)})`, backgroundSize: 'cover', backgroundPosition: 'center' };
  }
  if (bg.type === 'gradient') {
    return { background: `linear-gradient(${bg.gradientAngle || 180}deg, ${bg.gradientFrom || '#1e293b'}, ${bg.gradientTo || '#0f172a'})` };
  }
  return { background: bg.color || '#1e293b' };
}
</script>

<style scoped>
/* Riordino, conferme e importazione (1.4.1475) */
.mps-sl-trascinata { opacity: .45; }
.mps-sl-sopra { box-shadow: 0 -3px 0 0 rgb(var(--olo-primary-rgb, 129 140 248)); }
.mps-sl-sotto { box-shadow: 0 3px 0 0 rgb(var(--olo-primary-rgb, 129 140 248)); }
.mps-sl-az:disabled { opacity: .35; cursor: default; }
.mps-sl-conferma { position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; gap: 6px; padding: 6px 8px; background: rgb(17 24 39 / .92); color: #fde68a; font-size: 11px; }
.mps-sl-importa { margin: 8px 8px 0; display: flex; flex-direction: column; gap: 6px; padding: 8px; border: 1px solid #92400e; border-radius: 6px; background: #451a03; color: #fde68a; font-size: 11px; }
.mps-sl-importa-fila { display: flex; flex-wrap: wrap; gap: 4px; }
.mps-sl-btn { font: inherit; font-size: 10.5px; padding: 3px 8px; border-radius: 4px; border: 1px solid #4b5563; background: #374151; color: #e5e7eb; cursor: pointer; }
.mps-sl-btn:hover { background: #4b5563; }
.mps-sl-btn.is-si { background: rgb(var(--olo-primary-rgb, 129 140 248)); border-color: transparent; color: #fff; font-weight: 600; }
.mps-sl-btn.is-via { background: #b91c1c; border-color: #b91c1c; color: #fff; font-weight: 600; }
.mps-sl-btn:focus-visible { outline: 2px solid #fff; outline-offset: 1px; }
.mps-sl-modello { position: absolute; bottom: 4px; right: 4px; font-size: 9px; line-height: 1; padding: 3px 5px; border-radius: 3px; background: var(--olo-color-primary, #e1474f); color: var(--olo-color-primary-contrast, #ffffff); }
.mps-scene-apri { font: inherit; width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 7px 8px; font-size: 12px; font-weight: 600; color: var(--olo-color-primary-contrast, #fff); background: var(--olo-color-primary, #e1474f); border: 0; border-radius: 6px; cursor: pointer; }
.mps-scene-apri:hover { filter: brightness(1.08); }
.mps-scene-apri:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }
.mps-sl-az { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; padding: 0; border: 0; border-radius: 4px; color: #fff; background: rgb(0 0 0 / .6); cursor: pointer; }
.mps-sl-az :deep(svg) { width: 13px; height: 13px; }
.mps-sl-az:hover { background: rgb(0 0 0 / .8); }
.mps-sl-az--via { background: rgb(220 38 38 / .85); }
.mps-sl-az--via:hover { background: rgb(220 38 38); }
.mps-sl-az:focus-visible { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: 1px; }
</style>
