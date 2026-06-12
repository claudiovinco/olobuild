<template>
  <div class="olo-evonotes" :style="rootStyle">
    <div :style="noteStyle">{{ t('Layer di pagina — anteprima') }}</div>

    <!-- Sezione fittizia: rappresenta una sezione della pagina a cui si ancora il marker -->
    <div :style="fauxSectionStyle">
      <span :style="fauxLabelStyle">{{ t('Sezione della pagina') }}</span>

      <!-- Marker numerato (stato aperto) -->
      <button type="button" :style="markStyle" :aria-label="exampleAria">{{ example.number }}</button>

      <!-- Card aperta -->
      <div :style="cardStyle" role="note">
        <div v-if="s.kicker_label" :style="kStyle">{{ s.kicker_label }}<template v-if="example.number"> · {{ example.number }}</template></div>
        <h5 v-if="example.title" :style="h5Style">{{ example.title }}</h5>
        <p v-if="example.text" :style="pStyle">{{ example.text }}</p>
        <div v-if="example.before || example.after" :style="baStyle">
          <span :style="beforeStyle">{{ example.before }}</span>
          <span :style="arrStyle" aria-hidden="true">→</span>
          <span :style="afterStyle">{{ example.after }}</span>
        </div>
      </div>
    </div>

    <div :style="hintNoteStyle">{{ t('Sul frontend il bottone è fisso in basso a destra e i marker si ancorano alle sezioni reali.') }}</div>

    <!-- Hint a pillola (visibile quando il layer è attivo) -->
    <div v-if="s.show_hint && s.hint_text" :style="hintStyle" role="note">
      <span :style="sigStyle" aria-hidden="true">●</span><span>{{ s.hint_text }}</span>
    </div>

    <!-- Bottone toggle del layer -->
    <button type="button" :style="toggleStyle" aria-pressed="false">
      <span :style="icStyle" aria-hidden="true"><b :style="icBStyle"></b></span>
      <span>{{ s.toggle_label }}</span>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { resolveColor } from '@/composables/oloTileDefaults';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  toggle_label: 'Perché questa evoluzione',
  toggle_label_active: 'Nascondi motivazioni',
  show_hint: true,
  hint_text: 'Tocca i numeri per leggere ogni scelta — Prima → Ora',
  kicker_label: 'Evoluzione',
  accent: '',
  card_bg: '',
  text_color: '',
  items: [
    { number: '01', title: 'Identità tipografica', text: 'Un display industriale proprio dà voce allo studio fin dalla prima schermata, invece di affidarsi al look di un tema preconfezionato.', before: 'Tema YOOtheme', after: 'Carattere proprio', anchor: '', side: 'right', offset: '38%' },
    { number: '02', title: 'Una voce, non effetti', text: 'Un messaggio editoriale netto sostituisce slider e animazioni: chi arriva capisce subito cosa fai e perché conta.', before: 'Slider Revolution', after: 'Messaggio chiaro', anchor: '', side: 'left', offset: '70%' },
    { number: '03', title: 'Gerarchia leggibile', text: "I servizi diventano una lista numerata, scansionabile in un colpo d'occhio — non più cinque parole schiacciate su una riga.", before: 'Riga unica', after: 'Lista 01–05', anchor: 'servizi', side: 'right', offset: '30%' },
    { number: '04', title: 'Il lavoro al centro', text: 'I progetti scorrono in un reel orizzontale cinematografico — trascina, usa la rotella o scorri — al posto di video sparsi senza ordine.', before: 'Media sparsi', after: 'Reel orizzontale', anchor: 'lavori', side: 'left', offset: '18px' },
    { number: '05', title: 'Il sito è il prodotto', text: 'Questo stesso sito è costruito come un OLOtheme: visitarlo significa vedere dal vivo cosa sa fare lo studio. Showreel e prova insieme.', before: 'Portfolio statico', after: 'Showreel vivo', anchor: 'rs', side: 'right', offset: '20%' },
    { number: '06', title: 'Sala di regia', text: 'Mirino col nome della sezione, timecode di scroll, grana pellicola, fotogrammi che si inclinano col drag: il mestiere — video e media — diventa il linguaggio stesso del sito.', before: 'Pagina statica', after: 'Monitor live', anchor: 'contatto', side: 'left', offset: '26%' },
  ],
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => (Array.isArray(s.value.items) && s.value.items.length ? s.value.items : defaults.items));
const example = computed(() => items.value[0] || defaults.items[0]);
const exampleAria = computed(() => t('Apri nota') + ' ' + (example.value.number || '') + ' — ' + (example.value.title || ''));

// ── Colori token-first (stesse stringhe del render PHP) ──
const accent = computed(() => resolveColor(s.value.accent, 'var(--olo-color-primary, #C6F24E)'));
const cardBg = computed(() => resolveColor(s.value.card_bg, 'var(--olo-color-surface-alt, #161922)'));
const txt    = computed(() => resolveColor(s.value.text_color, 'var(--olo-color-text, #ECEAE3)'));
const onAcc  = 'var(--olo-color-on-primary, #0b0c0f)';
const ink    = 'var(--olo-color-background, #0b0c0f)';
const soft   = 'var(--olo-color-text-soft, #a0a298)';
const faint  = 'var(--olo-color-text-faint, #6a6c64)';
// line-2 del blueprint = testo al 20% (rgba(236,234,227,.20)) — segue la palette.
const line2  = computed(() => `color-mix(in srgb, ${txt.value} 20%, transparent)`);
const ring   = computed(() => `color-mix(in srgb, ${accent.value} 30%, transparent)`);
const glow   = computed(() => `color-mix(in srgb, ${accent.value} 50%, transparent)`);

// ── Font via ruoli del tema ──
const DISP = "var(--olo-font-family-heading, 'Big Shoulders Display', sans-serif)";
const SANS = "var(--olo-font-family, 'Hanken Grotesk', sans-serif)";
const MONO = "var(--olo-font-family-mono, 'Space Mono', ui-monospace, monospace)";

const rootStyle = computed(() => ({
  position: 'relative',
  background: ink,
  borderRadius: '13px',
  minHeight: '480px',
  overflow: 'hidden',
  padding: '18px 18px 84px',
  fontFamily: SANS,
  color: txt.value,
}));

const noteStyle = computed(() => ({
  fontFamily: MONO,
  fontSize: '10.5px',
  letterSpacing: '.1em',
  textTransform: 'uppercase',
  color: faint,
  marginBottom: '12px',
}));

const fauxSectionStyle = computed(() => ({
  position: 'relative',
  border: `1px dashed ${line2.value}`,
  borderRadius: '13px',
  height: '230px',
  marginBottom: '150px',
}));

const fauxLabelStyle = computed(() => ({
  position: 'absolute',
  top: '12px',
  left: '14px',
  fontFamily: MONO,
  fontSize: '10.5px',
  letterSpacing: '.1em',
  textTransform: 'uppercase',
  color: faint,
}));

const markStyle = computed(() => {
  const st = {
    position: 'absolute',
    zIndex: 4,
    width: '34px',
    height: '34px',
    borderRadius: '50%',
    background: txt.value, // stato "open" — bg testo come .evo-mark.open del blueprint
    color: onAcc,
    fontFamily: MONO,
    fontWeight: 700,
    fontSize: '13px',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    cursor: 'pointer',
    border: `2px solid ${ink}`,
    padding: 0,
    boxShadow: '0 6px 18px -6px rgba(0,0,0,.7)',
    top: example.value.offset || '30%',
  };
  if ((example.value.side || 'right') === 'left') { st.left = '20px'; } else { st.right = '20px'; }
  return st;
});

const cardStyle = computed(() => {
  const st = {
    position: 'absolute',
    zIndex: 5,
    width: '300px',
    maxWidth: '78vw',
    background: cardBg.value,
    border: `1px solid ${line2.value}`,
    borderRadius: '13px',
    padding: '17px 18px',
    boxShadow: '0 30px 70px -20px rgba(0,0,0,.85)',
    textAlign: 'left',
    top: `calc(${example.value.offset || '30%'} + 44px)`,
  };
  if ((example.value.side || 'right') === 'left') { st.left = '20px'; } else { st.right = '20px'; }
  return st;
});

const kStyle = computed(() => ({
  fontFamily: MONO,
  fontSize: '10.5px',
  letterSpacing: '.1em',
  textTransform: 'uppercase',
  color: accent.value,
  margin: '0 0 9px',
}));

const h5Style = computed(() => ({
  fontFamily: DISP,
  fontWeight: 700,
  fontSize: '21px',
  textTransform: 'uppercase',
  lineHeight: 1,
  letterSpacing: 0,
  color: txt.value,
  margin: '0 0 9px',
}));

const pStyle = computed(() => ({
  fontFamily: SANS,
  fontSize: '13.5px',
  lineHeight: 1.55,
  color: soft,
  margin: 0,
}));

const baStyle = computed(() => ({
  display: 'flex',
  gap: '8px',
  marginTop: '13px',
  fontFamily: MONO,
  fontSize: '10.5px',
  textTransform: 'uppercase',
  letterSpacing: '.04em',
}));

const beforeStyle = computed(() => ({ color: faint, textDecoration: 'line-through' }));
const arrStyle = computed(() => ({ color: accent.value }));
const afterStyle = computed(() => ({ color: txt.value }));

const hintNoteStyle = computed(() => ({
  fontFamily: MONO,
  fontSize: '10.5px',
  letterSpacing: '.06em',
  textTransform: 'uppercase',
  color: faint,
  maxWidth: '420px',
  lineHeight: 1.6,
}));

const hintStyle = computed(() => ({
  position: 'absolute',
  left: '50%',
  bottom: '22px',
  transform: 'translateX(-50%)',
  zIndex: 6,
  background: cardBg.value,
  border: `1px solid ${line2.value}`,
  borderRadius: '999px',
  padding: '10px 18px',
  fontFamily: SANS,
  fontSize: '13px',
  lineHeight: 1.4,
  color: soft,
  display: 'flex',
  alignItems: 'center',
  gap: '9px',
  whiteSpace: 'nowrap',
  maxWidth: 'calc(100% - 240px)',
  overflow: 'hidden',
  textOverflow: 'ellipsis',
}));

const sigStyle = computed(() => ({ color: accent.value, fontWeight: 700 }));

const toggleStyle = computed(() => ({
  position: 'absolute',
  right: '22px',
  bottom: '22px',
  zIndex: 7,
  display: 'inline-flex',
  alignItems: 'center',
  gap: '10px',
  background: accent.value,
  color: onAcc,
  fontFamily: SANS,
  fontWeight: 700,
  fontSize: '13.5px',
  lineHeight: 1.2,
  border: 0,
  borderRadius: '999px',
  padding: '13px 19px',
  cursor: 'pointer',
  boxShadow: `0 14px 38px -10px ${glow.value}`,
  outlineColor: ring.value,
}));

const icStyle = computed(() => ({
  width: '16px',
  height: '16px',
  display: 'grid',
  placeItems: 'center',
}));

const icBStyle = computed(() => ({
  width: '9px',
  height: '9px',
  border: `2px solid ${onAcc}`,
  borderRadius: '50%',
  boxSizing: 'border-box',
}));
</script>
