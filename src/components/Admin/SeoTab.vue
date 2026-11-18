<template>
  <div class="cfg-page-head">
    <div>
      <h1>SEO <em>{{ t('globale') }}</em></h1>
      <p>{{ t('Default meta-tag, Open Graph, sitemap e schema.org per tutto il sito. Sovrascrivibile pagina per pagina dall\'editor.') }}</p>
    </div>
    <div class="head-actions">
      <a class="cfg-btn cfg-btn-secondary" :href="googleSearchConsole" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
        {{ t('Test su Google') }}
      </a>
    </div>
  </div>

  <!-- Default site-wide -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      </div>
      <div>
        <h3>{{ t('Default site-wide') }}</h3>
        <p>{{ t('Usati quando una pagina non ha meta-tag specifici.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Title pattern') }}</label>
          <div class="hint">{{ t('Schema del title delle pagine. Variabili: {page}, {sep}, {site}.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-input mono cfg-w-md"><input type="text" :value="titles.pattern" @input="setT('pattern', $event.target.value)" placeholder="{page} {sep} {site}" /></div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Separatore') }}</label>
          <div class="hint">{{ t('Carattere che separa i blocchi nel title.') }}</div>
        </div>
        <div class="control-col">
          <CfgSelect size="md" :model-value="titles.separator" :options="SEPARATOR_OPTIONS" @update:model-value="setT('separator', $event)" />
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Meta description default') }}</label>
          <div class="hint">{{ t('Massimo 160 caratteri. Usato come fallback quando la pagina non ne ha una propria.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-textarea">
            <textarea rows="3" :value="titles.description" @input="setT('description', $event.target.value.slice(0, 160))"></textarea>
          </div>
          <div class="text-xs mt-2" style="color:var(--c-text-faint);">{{ (titles.description || '').length }} / 160</div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Robots default') }}</label>
          <div class="hint">{{ t('È la stessa scelta di Impostazioni › Lettura di WordPress. Le pagine si escludono una per una dall\'editor.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': titles.robots === 'index' }"   @click="setT('robots', 'index')">{{ t('Indicizza') }}</button>
            <button :class="{ 'is-on': titles.robots === 'noindex' }" @click="setT('robots', 'noindex')">{{ t('Non indicizzare') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Open Graph & Twitter Card -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>
      </div>
      <div>
        <h3>{{ t('Open Graph & Twitter Card') }}</h3>
        <p>{{ t('Come appare il sito quando viene condiviso su social e messaggistica.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Immagine OG default') }}</label>
          <div class="hint">{{ t('1200×630 consigliato. Usata quando la pagina non ne ha una propria.') }}</div>
        </div>
        <div class="control-col">
          <div class="og-preview-wrap">
            <div class="og-preview" :style="ogPreviewStyle">
              <span v-if="!social.og_default_image">{{ t('Anteprima OG') }}</span>
            </div>
            <div class="og-actions">
              <button class="cfg-btn cfg-btn-secondary" @click="pickOgImage">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                {{ social.og_default_image ? t('Sostituisci') : t('Carica immagine') }}
              </button>
              <div class="text-xs mt-2" style="color:var(--c-text-faint);" v-if="social.og_default_image">
                {{ social.og_default_image.split('/').pop() }}
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- L'immagine disegnata (1.4.1218): una per contenuto, col titolo e la foto in evidenza -->
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Immagine disegnata') }}</label>
          <div class="hint">{{ t('Per ogni contenuto senza un\'immagine sua: il titolo, il nome del sito e la foto in evidenza, nel colore e nel carattere del sito.') }}</div>
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': social.og_disegnata }" role="switch" :aria-checked="social.og_disegnata ? 'true' : 'false'"
            :aria-label="t('Immagine disegnata')" @click="setS('og_disegnata', !social.og_disegnata)"></button>
          <template v-if="social.og_disegnata">
            <div class="cfg-segment og-stili">
              <button v-for="s in OG_STILI" :key="s.value" :class="{ 'is-on': (social.og_stile || 'foto') === s.value }" @click="setS('og_stile', s.value)">{{ s.label }}</button>
            </div>
            <div class="og-disegnata">
              <img v-if="ogAnteprima.url" :src="ogAnteprima.url" :alt="t('Anteprima dell\'immagine disegnata')" width="1200" height="630" />
              <span v-else class="og-disegnata-vuota">{{ ogAnteprima.errore || t('Disegno l\'anteprima…') }}</span>
            </div>
            <div v-if="ogAnteprima.titolo" class="text-xs mt-2" style="color:var(--c-text-faint);">{{ t('Prova su: %s').replace('%s', ogAnteprima.titolo) }}</div>
          </template>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Twitter handle') }}</label>
          <div class="hint">{{ t('Username Twitter del sito (con @).') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-input mono with-prefix cfg-w-sm">
            <span class="prefix">@</span>
            <input type="text" :value="(social.twitter_user || '').replace(/^@/, '')" @input="setS('twitter_user', $event.target.value.replace(/^@/, ''))" placeholder="hotelcomo" />
          </div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Card type') }}</label>
          <div class="hint">{{ t('Formato della preview su Twitter.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': social.twitter_card_type === 'summary' }"             @click="setS('twitter_card_type', 'summary')">Summary</button>
            <button :class="{ 'is-on': social.twitter_card_type === 'summary_large_image' }" @click="setS('twitter_card_type', 'summary_large_image')">Summary Large Image</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Sitemap & schema.org -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7l3 2.7"/><path d="M21 3v6h-6"/></svg>
      </div>
      <div>
        <h3>{{ t('Sitemap & schema.org') }}</h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Sitemap XML') }}</label>
          <div class="hint">{{ t('La sitemap di WordPress, senza le pagine in noindex.') }}</div>
        </div>
        <div class="control-col" style="display:flex; align-items:center; gap:12px;">
          <button class="cfg-switch" :class="{ 'is-on': sitemap.enabled }" @click="setSM('enabled', !sitemap.enabled)" role="switch"></button>
          <a v-if="sitemap.enabled" class="text-xs" :href="sitemapUrl" target="_blank" rel="noopener" style="color:var(--c-red); text-decoration:none;">{{ t('Vedi sitemap →') }}</a>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Tipo organizzazione') }}</label>
          <div class="hint">{{ t('Schema.org markup iniettato in ogni pagina.') }}</div>
        </div>
        <div class="control-col">
          <CfgSelect size="md" :model-value="titles.kg_type" :options="SCHEMA_TYPE_OPTIONS" @update:model-value="setT('kg_type', $event)" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, watch, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgSelect from './controls/CfgSelect.vue';
import { okOrThrow, cfgJob, assertLoaded, reloadJob, conIniziali } from './cfgSave';
import { useCfgLettura } from './composables/useCfgLettura';

const TAB_ID = 'seo';
const showToast = inject('showToast', () => {});
const shellDirty = inject('setDirty', () => {});
const setDirty = (v) => shellDirty(v, TAB_ID);
const loaded = ref(false);
// Prima lettura: finché non riesce la shell mostra scheletro o errore al posto della scheda.
const lettura = useCfgLettura(TAB_ID, loaded, () => loadSettings());

const SEPARATOR_OPTIONS = [
  { value: '—', label: t('— (em dash)') },
  { value: '|', label: '|' },
  { value: '·', label: '·' },
  { value: '-', label: '-' },
];
const SCHEMA_TYPE_OPTIONS = [
  { value: 'Organization',        label: 'Organization' },
  { value: 'Person',              label: 'Person' },
  { value: 'LocalBusiness',       label: 'LocalBusiness' },
  { value: 'Hotel',               label: 'Hotel' },
  { value: 'Restaurant',          label: 'Restaurant' },
  { value: 'Store',               label: 'Store' },
  { value: 'ProfessionalService', label: 'ProfessionalService' },
];

// Valori di partenza: una sezione mai salvata torna [] dal server, e «Annulla modifiche»
// deve riportare a QUESTI, non lasciare a video quelli annullati.
// Le chiavi sono quelle che Olobuild_Seo_Head legge (fino alla 1.4.501 la scheda ne
// scriveva altre e i controlli non agivano). «Robots default» arriva SEMPRE dal server:
// è blog_public, e un valore inventato qui lo cambierebbe al primo salvataggio.
const INIZIALE_TITLES = {
  pattern: '{page} {sep} {site}',
  separator: '—',
  description: '',
  robots: 'index',
  kg_type: 'Organization',
};
const INIZIALE_SOCIAL = {
  twitter_user: '',
  twitter_card_type: 'summary_large_image',
  og_default_image: '',
};
const INIZIALE_SITEMAP = { enabled: true };

// L'immagine disegnata (1.4.1218): tre stili; senza foto in evidenza il server usa «Solo colore»
const OG_STILI = [
  { value: 'foto',   label: t('Foto con titolo') },
  { value: 'meta',   label: t('Metà e metà') },
  { value: 'colore', label: t('Solo colore') },
];
const ogAnteprima = ref({ url: '', titolo: '', errore: '' });
let ogRichiesta = 0;
async function aggiornaOgAnteprima() {
  if (!social.value.og_disegnata) return;
  const mia = ++ogRichiesta;
  ogAnteprima.value = { url: '', titolo: '', errore: '' };
  try {
    const r = await fetch(`${window.oloData.restUrl}seo/og-anteprima?stile=${encodeURIComponent(social.value.og_stile || 'foto')}`,
      { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    const j = await r.json().catch(() => ({}));
    if (mia !== ogRichiesta) return;
    ogAnteprima.value = r.ok ? { url: j.url || '', titolo: j.titolo || '', errore: '' } : { url: '', titolo: '', errore: j.message || t('Anteprima non disponibile') };
  } catch (e) {
    if (mia === ogRichiesta) ogAnteprima.value = { url: '', titolo: '', errore: t('Anteprima non disponibile') };
  }
}
const titles  = ref({ ...INIZIALE_TITLES });
const social  = ref({ ...INIZIALE_SOCIAL });
const sitemap = ref({ ...INIZIALE_SITEMAP });

watch(() => [social.value.og_disegnata, social.value.og_stile], aggiornaOgAnteprima);

const sitemapUrl = computed(() => window.oloData?.sitemapUrl || ((window.oloData?.siteUrl || '/') + 'wp-sitemap.xml'));
const googleSearchConsole = 'https://search.google.com/search-console';

const ogPreviewStyle = computed(() => ({
  width: '160px', height: '84px', borderRadius: '8px',
  display: 'grid', placeItems: 'center',
  color: '#fff', fontFamily: 'Instrument Serif, serif', fontSize: '18px',
  background: social.value.og_default_image
    ? `center / cover no-repeat url(${social.value.og_default_image})`
    : 'linear-gradient(135deg, #e1474f, #7a1d23)',
}));

function setT(k, v)   { titles.value[k]  = v; setDirty(true); }
function setS(k, v)   { social.value[k]  = v; setDirty(true); }
function setSM(k, v)  { sitemap.value[k] = v; setDirty(true); }

function pickOgImage() {
  if (!window.wp || !window.wp.media) {
    showToast(t('WP Media Library non disponibile'), 'error');
    return;
  }
  const frame = window.wp.media({ title: t('Scegli immagine OG'), button: { text: t('Usa questa immagine') }, multiple: false });
  frame.on('select', () => {
    const att = frame.state().get('selection').first().toJSON();
    social.value.og_default_image = att.url;
    setDirty(true);
  });
  frame.open();
}

async function loadSettings() {
  try {
    const headers = { 'X-WP-Nonce': window.oloData.nonce };
    const base = `${window.oloData.restUrl}seo/`;
    const [tRes, sRes, smRes] = await Promise.all([
      lettura.fetch(base + 'titles', { headers }).then(r => r.ok ? r.json() : null).catch(() => null),
      lettura.fetch(base + 'social', { headers }).then(r => r.ok ? r.json() : null).catch(() => null),
      lettura.fetch(base + 'sitemap', { headers }).then(r => r.ok ? r.json() : null).catch(() => null),
    ]);
    if (tRes)   titles.value  = conIniziali(INIZIALE_TITLES, tRes);
    if (sRes)   social.value  = conIniziali(INIZIALE_SOCIAL, sRes);
    if (smRes)  sitemap.value = conIniziali(INIZIALE_SITEMAP, smRes);
    // Si salvano tutte e tre: basta una lettura mancata per spedire i default.
    if (tRes && sRes && smRes) loaded.value = true;
  } catch (e) { /* defaults */ }
}

async function saveSettings() {
  assertLoaded(loaded);
  const base = `${window.oloData.restUrl}seo/`;
  const headers = { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce };
  await Promise.all([
    okOrThrow(fetch(base + 'titles',  { method: 'POST', headers, body: JSON.stringify(titles.value) })),
    okOrThrow(fetch(base + 'social',  { method: 'POST', headers, body: JSON.stringify(social.value) })),
    okOrThrow(fetch(base + 'sitemap', { method: 'POST', headers, body: JSON.stringify(sitemap.value) })),
  ]);
}

const onSave = cfgJob(TAB_ID, saveSettings);
const onDiscard = cfgJob(TAB_ID, reloadJob(loaded, loadSettings));

onMounted(() => {
  lettura.leggi();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>

<style scoped>
.og-preview-wrap { display: flex; gap: 12px; align-items: flex-start; }
.og-stili { margin-top: 12px; }
.og-disegnata { margin-top: 12px; width: 100%; max-width: 360px; aspect-ratio: 1200 / 630; border-radius: 8px; overflow: hidden;
  background: var(--c-bg-soft, #f3f4f6); box-shadow: inset 0 0 0 1px rgba(0,0,0,.06); display: grid; place-items: center; }
.og-disegnata img { display: block; width: 100%; height: 100%; object-fit: cover; }
.og-disegnata-vuota { font-size: 12px; color: var(--c-text-faint); padding: 0 12px; text-align: center; }
.og-preview { box-shadow: inset 0 0 0 1px rgba(0,0,0,.06); position: relative; overflow: hidden; }
.og-actions { flex: 1; }
</style>
