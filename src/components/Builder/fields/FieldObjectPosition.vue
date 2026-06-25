<template>
  <!--
    FieldObjectPosition — punto focale grafico per `object_position` del tile Immagine.
    Sostituisce il <select> a 9 voci. Emette la STESSA stringa CSS object-position:
      • modo "Aggancia"  → coppia keyword ('center center', 'left top', …)  ← valori di oggi
      • modo "Libero"    → percentuale ('34% 23%')
    Entrambe valide come object-position → il render del tile non cambia.

    Modello VIEWPORT WYSIWYG: il pad È il frame. L'immagine vive dentro con l'`object-fit`
    e l'`object-position` reali → l'anteprima mostra esattamente cosa il frame mostrerà e il
    "frame di riferimento" (il bordo del pad) è SEMPRE presente, in ogni modalità e con
    qualsiasi proporzione. Quando una proporzione è impostata, il pad ne assume l'aspetto
    (anteprima esatta); con proporzione 'Auto' il pad ha un'altezza fissa (anteprima
    indicativa, ma object-position % salvato resta corretto). Tutto in percentuali CSS native
    → niente calcoli px fragili, geometria corretta già al primo paint.
    Props di contesto (imageSrc / frameRatio / objectFit) OPZIONALI: senza immagine degrada
    elegante (pad neutro + griglia), restando pienamente funzionale.
  -->
  <div class="olo-op" ref="rootEl">
    <div class="olo-op-head">
      <span v-if="frameLabel" class="olo-op-frame" :title="t('Proporzione del frame')">{{ frameLabel }}</span>
      <span class="olo-op-kw">{{ snap ? anchorName : (Math.round(x) + '·' + Math.round(y)) }}</span>
    </div>

    <div ref="padEl" class="olo-op-pad" :class="{ 'is-free': !snap, 'is-empty': !imageSrc }" :style="padStyle"
         @mousedown="onDown" @touchstart="onDown">
      <img v-if="imageSrc" :src="imageSrc" alt="" class="olo-op-img" :style="imgStyle" draggable="false" @load="onImgLoad"/>
      <div v-else class="olo-op-empty">{{ t('Nessuna immagine') }}</div>
      <div v-if="imageSrc" class="olo-op-thirds" aria-hidden="true"></div>
      <template v-if="snap && imageSrc">
        <div v-for="a in ANCHORS" :key="a.key" class="olo-op-dot" :class="{ near: a === nearAnchor }"
             :style="{ left: a.x + '%', top: a.y + '%' }"></div>
      </template>
      <div v-if="imageSrc" class="olo-op-fp" :style="{ left: x + '%', top: y + '%' }"></div>
    </div>

    <p class="olo-op-axhint" v-html="axHint"></p>

    <div class="olo-op-seg" role="radiogroup" :aria-label="t('Modo posizione')">
      <button type="button" role="radio" :aria-checked="snap" :class="{ on: snap }" @click="setMode(true)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h7v7H3zM14 14h7v7h-7z"/><path d="M14 3h7v7h-7zM3 14h7v7H3z" opacity=".4"/></svg>{{ t('Aggancia') }}
      </button>
      <button type="button" role="radio" :aria-checked="!snap" :class="{ on: !snap }" @click="setMode(false)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 9l-3 3 3 3M9 5l3-3 3 3M15 19l-3 3-3-3M19 9l3 3-3 3M2 12h20M12 2v20"/></svg>{{ t('Libero') }}
      </button>
    </div>

    <div class="olo-op-row">
      <div class="olo-op-axis" :class="{ dim: snap || lockX }">
        <span class="olo-op-axlbl">X</span>
        <NumberScrubber class="olo-op-ns" :modelValue="Math.round(x)" :min="0" :max="100" :step="1"
          :defaultValue="50" emitAs="number" unit="%" :disabled="snap || lockX"
          :ariaLabel="t('Posizione orizzontale (%)')" @update:modelValue="onInput('x', $event)" />
      </div>
      <div class="olo-op-axis" :class="{ dim: snap || lockY }">
        <span class="olo-op-axlbl">Y</span>
        <NumberScrubber class="olo-op-ns" :modelValue="Math.round(y)" :min="0" :max="100" :step="1"
          :defaultValue="50" emitAs="number" unit="%" :disabled="snap || lockY"
          :ariaLabel="t('Posizione verticale (%)')" @update:modelValue="onInput('y', $event)" />
      </div>
      <button type="button" class="olo-op-reset" :title="t('Centra (50/50)')" :aria-label="t('Centra')" @click="center">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import NumberScrubber from './NumberScrubber.vue';

const props = defineProps({
  modelValue: { type: String, default: 'center center' },
  // CONTESTO (opzionale ma consigliato — vedi SPEC): immagine + frame del tile.
  imageSrc:   { type: String, default: '' },
  frameRatio: { type: [String, Number], default: 'auto' }, // es. '16/9', 1.5, 'auto'
  objectFit:  { type: String, default: 'cover' },
});
const emit = defineEmits(['update:modelValue']);

// ── 9 ancore canoniche: coppia keyword object-position (= valori salvati di oggi) ──
const ANCHORS = [
  { x:0,  y:0,  key:'left top',      name:'Alto sinistra' },{ x:50, y:0,  key:'center top',    name:'Alto centro' },{ x:100,y:0,  key:'right top',     name:'Alto destra' },
  { x:0,  y:50, key:'left center',   name:'Centro sinistra' },{ x:50, y:50, key:'center center', name:'Centro' },{ x:100,y:50, key:'right center',  name:'Centro destra' },
  { x:0,  y:100,key:'left bottom',   name:'Basso sinistra' },{ x:50, y:100,key:'center bottom', name:'Basso centro' },{ x:100,y:100,key:'right bottom',  name:'Basso destra' },
];
const KW_X = { left:0, center:50, right:100 };
const KW_Y = { top:0, center:50, bottom:100 };

function parseValue(v){
  v = String(v || '').trim().toLowerCase();
  const pct = v.match(/^(-?\d+(?:\.\d+)?)%\s+(-?\d+(?:\.\d+)?)%$/);
  if (pct) return { snap:false, x:clamp(+pct[1]), y:clamp(+pct[2]) };
  const a = ANCHORS.find(a => a.key === v);
  if (a) return { snap:true, x:a.x, y:a.y };
  // tolleranza: keyword singola o coppia in ENTRAMBI gli ordini (object-position
  // 'left top' = orizz-vert; background-position 'top center' = vert-orizz → accettati entrambi).
  const parts = v.split(/\s+/);
  if (parts.length === 1) {
    if (parts[0] === 'center') return { snap:true, x:50, y:50 };
    if (parts[0] in KW_X) return { snap:true, x:KW_X[parts[0]], y:50 };
    if (parts[0] in KW_Y) return { snap:true, x:50, y:KW_Y[parts[0]] };
  }
  if (parts.length === 2) {
    if ((parts[0] in KW_X) && (parts[1] in KW_Y)) return { snap:true, x:KW_X[parts[0]], y:KW_Y[parts[1]] };
    if ((parts[0] in KW_Y) && (parts[1] in KW_X)) return { snap:true, x:KW_X[parts[1]], y:KW_Y[parts[0]] };
  }
  return { snap:true, x:50, y:50 };
}

const init = parseValue(props.modelValue);
const snap = ref(init.snap);
const x = ref(init.x);
const y = ref(init.y);

const padEl = ref(null);
const rootEl = ref(null);
const availW = ref(300);   // larghezza disponibile (misurata) per dimensionare il pad
const imgAR = ref(0);      // aspetto naturale immagine (0 = non ancora noto)
const PAD_MAX_H = 210;     // altezza massima del pad (px)
const PAD_AUTO_H = 174;    // altezza del pad con proporzione 'Auto'

function clamp(v){ return Math.max(0, Math.min(100, +v || 0)); }
function snap3(v){ return v < 25 ? 0 : v < 75 ? 50 : 100; }

// ── frame ──
const frameAR = computed(() => {
  const r = props.frameRatio;
  if (r == null || r === 'auto' || r === '') return null;
  if (typeof r === 'number') return r;
  const m = String(r).match(/^(\d+(?:\.\d+)?)\s*[/:]\s*(\d+(?:\.\d+)?)$/);
  if (m) return (+m[1]) / (+m[2]);
  const n = parseFloat(r);
  return isNaN(n) ? null : n;
});
const hasFrame = computed(() => frameAR.value != null);

// Pad = frame: con proporzione nota assume l'aspetto reale (anteprima esatta), entro
// un'altezza massima — dimensionato in px così l'aspetto è onorato per OGNI ratio
// (largo 21:9, quadrato 1:1, verticale 9:16), niente schiacciamento da max-height.
// Con 'Auto' altezza fissa (anteprima indicativa, valore comunque corretto).
const padStyle = computed(() => {
  if (!hasFrame.value) return { width: '100%', height: PAD_AUTO_H + 'px' };
  const w = availW.value || 300;
  let pw = w, ph = w / frameAR.value;
  if (ph > PAD_MAX_H) { ph = PAD_MAX_H; pw = ph * frameAR.value; } // frame alto → riduci larghezza
  if (pw > w) { pw = w; ph = w / frameAR.value; }                  // mai oltre lo spazio disponibile
  return { width: Math.round(pw) + 'px', height: Math.round(ph) + 'px' };
});

// Immagine = render reale del tile dentro il viewport (WYSIWYG)
const imgStyle = computed(() => ({
  objectFit: props.objectFit || 'cover',
  objectPosition: `${x.value}% ${y.value}%`,
}));

// Con cover + frame noto, l'asse che il frame riempie del tutto è ininfluente
// (object-position su quell'asse non sposta nulla) → lo disabilitiamo + nota.
// In 'Auto' o con fit ≠ cover entrambi gli assi restano liberi.
const cropFrac = computed(() => {
  if (!hasFrame.value || props.objectFit !== 'cover' || !imgAR.value) return { cw:1, ch:1 };
  const r = frameAR.value / imgAR.value;
  return r >= 1 ? { cw:1, ch:1/r } : { cw:r, ch:1 };
});
const lockX = computed(() => props.objectFit === 'cover' && hasFrame.value && (1 - cropFrac.value.cw) <= 0.001 && (1 - cropFrac.value.ch) > 0.001);
const lockY = computed(() => props.objectFit === 'cover' && hasFrame.value && (1 - cropFrac.value.ch) <= 0.001 && (1 - cropFrac.value.cw) > 0.001);

const nearAnchor = computed(() => {
  if (!snap.value) return null;
  let best = ANCHORS[4], bd = 1e9;
  for (const a of ANCHORS){ const d = (a.x-x.value)**2 + (a.y-y.value)**2; if (d < bd){ bd = d; best = a; } }
  return best;
});
const anchorName = computed(() => { const a = ANCHORS.find(a => a.x===x.value && a.y===y.value); return a ? a.name : t('Personalizzato'); });
const frameLabel = computed(() => {
  const r = frameAR.value; if (r == null) return '';
  if (Math.abs(r-1)<.01) return '1:1'; if (Math.abs(r-16/9)<.02) return '16:9';
  if (Math.abs(r-9/16)<.02) return '9:16'; if (Math.abs(r-4/3)<.02) return '4:3';
  if (Math.abs(r-3/2)<.02) return '3:2'; if (Math.abs(r-21/9)<.03) return '21:9';
  return r.toFixed(2);
});
const axHint = computed(() => {
  if (!props.imageSrc) return t('Trascina il punto per impostare la posizione (object-position).');
  if (props.objectFit !== 'cover')
    return t('Anteprima dell\'allineamento. Con "Riempi (cover)" il punto sceglie la parte ritagliata.');
  if (!hasFrame.value)
    return t('Anteprima indicativa (proporzione "Auto"). Imposta una proporzione fissa per l\'anteprima esatta del frame.');
  if (lockX.value) return t('Il frame riempie la larghezza → conta l\'asse verticale.');
  if (lockY.value) return t('Il frame riempie l\'altezza → conta l\'asse orizzontale.');
  return t('Trascina per scegliere la parte d\'immagine che il frame mostrerà.');
});

// ── valore emesso ──
function emitValue(){
  if (snap.value){ const a = ANCHORS.find(a => a.x===x.value && a.y===y.value); emit('update:modelValue', (a||ANCHORS[4]).key); }
  else emit('update:modelValue', Math.round(x.value) + '% ' + Math.round(y.value) + '%');
}
function setXY(nx, ny){
  nx = clamp(nx); ny = clamp(ny);
  if (snap.value){ nx = snap3(nx); ny = snap3(ny); }
  // rispetta l'asse bloccato (cover che riempie un asse): resta a 50
  if (lockX.value) nx = 50;
  if (lockY.value) ny = 50;
  x.value = nx; y.value = ny; emitValue();
}
function center(){ setXY(50, 50); }
function setMode(s){ snap.value = s; setXY(x.value, y.value); }
function onInput(axis, v){ axis === 'x' ? setXY(+v, y.value) : setXY(x.value, +v); }

// ── drag: object-position = posizione del cursore nel viewport (focal point) ──
let dragging = false;
function fromEvent(e){
  const pad = padEl.value; if (!pad) return;
  const r = pad.getBoundingClientRect();
  const cx = (e.touches ? e.touches[0].clientX : e.clientX) - r.left;
  const cy = (e.touches ? e.touches[0].clientY : e.clientY) - r.top;
  setXY(r.width ? cx / r.width * 100 : 50, r.height ? cy / r.height * 100 : 50);
}
function onDown(e){ if (!props.imageSrc) return; dragging = true; fromEvent(e); if (e.cancelable && e.preventDefault) e.preventDefault(); }
function onMove(e){ if (dragging) fromEvent(e); }
function onUp(){ dragging = false; }

function onImgLoad(e){ const im = e.target; if (im.naturalWidth) imgAR.value = im.naturalWidth / im.naturalHeight; }

function measure(){ if (rootEl.value) availW.value = rootEl.value.clientWidth || 300; }

let ro;
onMounted(() => {
  measure();
  if (window.ResizeObserver){ ro = new ResizeObserver(measure); ro.observe(rootEl.value); }
  window.addEventListener('mousemove', onMove); window.addEventListener('mouseup', onUp);
  window.addEventListener('touchmove', onMove, { passive:false }); window.addEventListener('touchend', onUp);
});
onBeforeUnmount(() => {
  ro && ro.disconnect();
  window.removeEventListener('mousemove', onMove); window.removeEventListener('mouseup', onUp);
  window.removeEventListener('touchmove', onMove); window.removeEventListener('touchend', onUp);
});
</script>

<style scoped>
.olo-op { width: 100%; }
.olo-op-head { display: flex; align-items: center; justify-content: flex-end; gap: 6px; margin-bottom: 7px; }
.olo-op-frame { font: 600 9px/1 ui-monospace, monospace; color: #6b7280; background: #eef0f3; padding: 3px 6px; border-radius: 5px; letter-spacing: .03em; }
.olo-op-kw { font: 500 10.5px/1 ui-monospace, monospace; color: var(--olo-ui-accent, #e8622a);
  background: color-mix(in srgb, var(--olo-ui-accent, #e8622a) 12%, #fff); padding: 3px 7px; border-radius: 5px; }

/* Il pad È il frame di riferimento (bordo sempre visibile). Larghezza/altezza arrivano
   da padStyle (inline) per onorare l'aspetto a ogni ratio; margin auto = centra il pad
   quando è più stretto del contenitore (frame quadrati/verticali). */
.olo-op-pad { position: relative; margin: 0 auto; border-radius: 9px; overflow: hidden;
  cursor: grab; touch-action: none; user-select: none; border: 1px solid #cdd2d9;
  background: repeating-conic-gradient(#2b3340 0 25%, #222a36 0 50%) 50%/14px 14px;
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.06); }
.olo-op-pad:active { cursor: grabbing; }
.olo-op-pad.is-empty { background: repeating-linear-gradient(45deg, #eef0f3 0 6px, #f7f8fa 6px 12px); cursor: default; }
.olo-op-img { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; -webkit-user-drag: none; display: block; }
.olo-op-empty { position: absolute; inset: 0; display: grid; place-items: center; font-size: 11px; color: #9aa6b4; }

/* regola dei terzi — guida di composizione: SOLO le 2 linee interne per asse
   (a 1/3 e 2/3), niente linee sui bordi. Bianco semitrasparente + alone scuro
   (drop-shadow) per restare leggibile su immagini sia chiare sia scure. */
.olo-op-thirds { position: absolute; inset: 0; pointer-events: none;
  background-repeat: no-repeat;
  background-image:
    linear-gradient(rgba(255,255,255,.55), rgba(255,255,255,.55)),
    linear-gradient(rgba(255,255,255,.55), rgba(255,255,255,.55)),
    linear-gradient(rgba(255,255,255,.55), rgba(255,255,255,.55)),
    linear-gradient(rgba(255,255,255,.55), rgba(255,255,255,.55));
  background-size: 1px 100%, 1px 100%, 100% 1px, 100% 1px;
  background-position: 33.333% 0, 66.666% 0, 0 33.333%, 0 66.666%;
  filter: drop-shadow(0 0 1px rgba(0,0,0,.45)); }

.olo-op-dot { position: absolute; width: 7px; height: 7px; border-radius: 50%; background: rgba(255,255,255,.85);
  box-shadow: 0 0 0 1.5px rgba(0,0,0,.45); transform: translate(-50%,-50%); pointer-events: none; transition: background .12s, box-shadow .12s; }
.olo-op-dot.near { background: var(--olo-ui-accent, #e8622a); box-shadow: 0 0 0 4px color-mix(in srgb, var(--olo-ui-accent, #e8622a) 40%, transparent); }

/* marker focale — SEMPRE visibile, segue x,y in percentuale del frame */
.olo-op-fp { position: absolute; width: 16px; height: 16px; border-radius: 50%;
  background: color-mix(in srgb, var(--olo-ui-accent, #e8622a) 22%, transparent); border: 2px solid var(--olo-ui-accent, #e8622a);
  transform: translate(-50%,-50%); pointer-events: none; box-shadow: 0 1px 5px rgba(0,0,0,.5), 0 0 0 1px rgba(255,255,255,.5); }
.olo-op-fp::before, .olo-op-fp::after { content: ""; position: absolute; background: #fff; box-shadow: 0 0 1px rgba(0,0,0,.6); }
.olo-op-fp::before { left: 50%; top: 50%; width: 2px; height: 7px; transform: translate(-50%,-50%); }
.olo-op-fp::after  { left: 50%; top: 50%; width: 7px; height: 2px; transform: translate(-50%,-50%); }

.olo-op-axhint { font-size: 10px; color: #94a3b8; line-height: 1.35; margin: 7px 0 0; min-height: 13px; }

.olo-op-seg { display: flex; background: #2b3340; border-radius: 7px; padding: 3px; gap: 2px; margin-top: 10px; }
.olo-op-seg button { flex: 1; appearance: none; border: 0; background: transparent; font: 600 11.5px sans-serif;
  color: rgba(255,255,255,.62); padding: 5px 6px; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 5px; }
.olo-op-seg button svg { width: 12px; height: 12px; }
.olo-op-seg button.on { background: #fff; color: var(--olo-ui-accent, #e8622a); box-shadow: 0 1px 2px rgba(16,24,40,.12); }
.olo-op-seg button:focus-visible { outline: 2px solid var(--olo-ui-accent, #e8622a); outline-offset: 2px; }

.olo-op-row { display: flex; gap: 7px; margin-top: 9px; align-items: center; }
/* Assi X/Y: etichetta + NumberScrubber (pill compatta + slider a comparsa + rotellina) */
.olo-op-axis { flex: 1; display: flex; align-items: center; gap: 7px; min-width: 0; }
.olo-op-axlbl { font: 700 9px ui-monospace, monospace; color: #94a3b8; flex: none; }
.olo-op-axis.dim .olo-op-axlbl { opacity: .4; }
.olo-op-axis .olo-op-ns { flex: 1; min-width: 0; }
.olo-op-axis .olo-op-ns :deep(.olo-ns-box) { width: 100%; }
.olo-op-f { flex: 1; display: flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 7px; overflow: hidden; background: #fff; height: 30px; }
.olo-op-f .k { font: 700 9px ui-monospace, monospace; color: #94a3b8; padding: 0 0 0 9px; font-style: normal; }
.olo-op-f input { flex: 1; width: 100%; min-width: 0; border: 0; outline: none; background: transparent; font: 500 12px ui-monospace, monospace; color: #1f2937; text-align: center; padding: 0; }
.olo-op-f input::-webkit-outer-spin-button, .olo-op-f input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.olo-op-f .u { font-size: 10px; color: #94a3b8; font-weight: 600; padding: 0 7px; border-left: 1px solid #eef0f3; align-self: stretch; display: flex; align-items: center; background: #f6f7f9; font-style: normal; }
.olo-op-f:focus-within { border-color: var(--olo-ui-accent, #e8622a); }
.olo-op-f.dim { opacity: .4; }
.olo-op-reset { width: 30px; height: 30px; flex: none; border: 1px solid #e5e7eb; background: #fff; border-radius: 7px; color: #6b7280; display: grid; place-items: center; cursor: pointer; }
.olo-op-reset:hover { border-color: var(--olo-ui-accent, #e8622a); color: var(--olo-ui-accent, #e8622a); }
.olo-op-reset svg { width: 14px; height: 14px; }
.olo-op-reset:focus-visible { outline: 2px solid var(--olo-ui-accent, #e8622a); outline-offset: 1px; }
</style>
