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
          <div class="cfg-input mono"><input type="text" :value="titles.pattern" @input="setT('pattern', $event.target.value)" placeholder="{page} {sep} {site}" /></div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Separatore') }}</label>
          <div class="hint">{{ t('Carattere che separa i blocchi nel title.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-select">
            <select :value="titles.separator" @change="setT('separator', $event.target.value)">
              <option value="—">— (em dash)</option>
              <option value="|">|</option>
              <option value="·">·</option>
              <option value="-">-</option>
            </select>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
          </div>
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
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Lingua sito') }}</label>
          <div class="hint">{{ t('Attributo lang dell\'HTML.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-select">
            <select :value="titles.language" @change="setT('language', $event.target.value)">
              <option value="it_IT">{{ t('Italiano (it_IT)') }}</option>
              <option value="en_US">{{ t('English (en_US)') }}</option>
              <option value="es_ES">{{ t('Español (es_ES)') }}</option>
              <option value="fr_FR">{{ t('Français (fr_FR)') }}</option>
              <option value="de_DE">{{ t('Deutsch (de_DE)') }}</option>
            </select>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
          </div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Robots default') }}</label>
          <div class="hint">{{ t('Comportamento di default per i motori di ricerca.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': titles.robots === 'index_follow' }"   @click="setT('robots', 'index_follow')">{{ t('Index, Follow') }}</button>
            <button :class="{ 'is-on': titles.robots === 'noindex' }"        @click="setT('robots', 'noindex')">NoIndex</button>
            <button :class="{ 'is-on': titles.robots === 'nofollow' }"       @click="setT('robots', 'nofollow')">NoFollow</button>
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
              <span v-if="!social.og_image">{{ t('Anteprima OG') }}</span>
            </div>
            <div class="og-actions">
              <button class="cfg-btn cfg-btn-secondary" @click="pickOgImage">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                {{ social.og_image ? t('Sostituisci') : t('Carica immagine') }}
              </button>
              <div class="text-xs mt-2" style="color:var(--c-text-faint);" v-if="social.og_image">
                {{ social.og_image.split('/').pop() }}
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Twitter handle') }}</label>
          <div class="hint">{{ t('Username Twitter del sito (con @).') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-input mono with-prefix">
            <span class="prefix">@</span>
            <input type="text" :value="(social.twitter_handle || '').replace(/^@/, '')" @input="setS('twitter_handle', $event.target.value)" placeholder="hotelcomo" />
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
            <button :class="{ 'is-on': social.card_type === 'summary' }"       @click="setS('card_type', 'summary')">Summary</button>
            <button :class="{ 'is-on': social.card_type === 'summary_large' }" @click="setS('card_type', 'summary_large')">Summary Large Image</button>
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
          <div class="hint">{{ t('Generato automaticamente a /sitemap.xml.') }}</div>
        </div>
        <div class="control-col" style="display:flex; align-items:center; gap:12px;">
          <button class="cfg-switch" :class="{ 'is-on': sitemap.enabled }" @click="setSM('enabled', !sitemap.enabled)" role="switch"></button>
          <a v-if="sitemap.enabled" class="text-xs" :href="sitemapUrl" target="_blank" rel="noopener" style="color:var(--c-red); text-decoration:none;">{{ t('Vedi sitemap →') }}</a>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Tipo organizzazione') }}</label>
          <div class="hint">{{ t('Schema.org markup iniettato in ogni pagina.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-select">
            <select :value="schema.type" @change="setSch('type', $event.target.value)">
              <option value="Organization">Organization</option>
              <option value="LocalBusiness">LocalBusiness</option>
              <option value="Hotel">Hotel</option>
              <option value="Restaurant">Restaurant</option>
              <option value="Store">Store</option>
              <option value="ProfessionalService">ProfessionalService</option>
            </select>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
          </div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Auto-ping search engines') }}</label>
          <div class="hint">{{ t('Notifica Google e Bing ad ogni pubblicazione.') }}</div>
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': schema.auto_ping }" @click="setSch('auto_ping', !schema.auto_ping)" role="switch"></button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const titles = ref({
  pattern: '{page} {sep} {site}',
  separator: '—',
  description: '',
  language: 'it_IT',
  robots: 'index_follow',
});
const social = ref({
  twitter_handle: '',
  card_type: 'summary_large',
  og_image: '',
});
const sitemap = ref({ enabled: true });
const schema  = ref({ type: 'Organization', auto_ping: true });

const sitemapUrl = computed(() => (window.oloData?.siteUrl || '/') + 'sitemap.xml');
const googleSearchConsole = 'https://search.google.com/search-console';

const ogPreviewStyle = computed(() => ({
  width: '160px', height: '84px', borderRadius: '8px',
  display: 'grid', placeItems: 'center',
  color: '#fff', fontFamily: 'Instrument Serif, serif', fontSize: '18px',
  background: social.value.og_image
    ? `center / cover no-repeat url(${social.value.og_image})`
    : 'linear-gradient(135deg, #e1474f, #7a1d23)',
}));

function setT(k, v)   { titles.value[k]  = v; setDirty(true); }
function setS(k, v)   { social.value[k]  = v; setDirty(true); }
function setSM(k, v)  { sitemap.value[k] = v; setDirty(true); }
function setSch(k, v) { schema.value[k]  = v; setDirty(true); }

function pickOgImage() {
  if (!window.wp || !window.wp.media) {
    showToast(t('WP Media Library non disponibile'), 'error');
    return;
  }
  const frame = window.wp.media({ title: t('Scegli immagine OG'), button: { text: t('Usa questa immagine') }, multiple: false });
  frame.on('select', () => {
    const att = frame.state().get('selection').first().toJSON();
    social.value.og_image = att.url;
    setDirty(true);
  });
  frame.open();
}

async function loadSettings() {
  try {
    const headers = { 'X-WP-Nonce': window.oloData.nonce };
    const base = `${window.oloData.restUrl}seo/`;
    const [tRes, sRes, smRes, schRes] = await Promise.all([
      fetch(base + 'titles', { headers }).then(r => r.ok ? r.json() : null).catch(() => null),
      fetch(base + 'social', { headers }).then(r => r.ok ? r.json() : null).catch(() => null),
      fetch(base + 'sitemap', { headers }).then(r => r.ok ? r.json() : null).catch(() => null),
      fetch(base + 'advanced', { headers }).then(r => r.ok ? r.json() : null).catch(() => null),
    ]);
    if (tRes)  Object.assign(titles.value, tRes);
    if (sRes)  Object.assign(social.value, sRes);
    if (smRes) Object.assign(sitemap.value, smRes);
    if (schRes && schRes.schema) Object.assign(schema.value, schRes.schema);
  } catch (e) { /* defaults */ }
}

async function saveSettings() {
  try {
    const base = `${window.oloData.restUrl}seo/`;
    const headers = { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce };
    await Promise.all([
      fetch(base + 'titles',  { method: 'POST', headers, body: JSON.stringify(titles.value) }),
      fetch(base + 'social',  { method: 'POST', headers, body: JSON.stringify(social.value) }),
      fetch(base + 'sitemap', { method: 'POST', headers, body: JSON.stringify(sitemap.value) }),
      fetch(base + 'advanced',{ method: 'POST', headers, body: JSON.stringify({ schema: schema.value }) }),
    ]);
  } catch (e) { showToast(t('Errore di salvataggio SEO'), 'error'); }
}

const onSave = () => saveSettings();
const onDiscard = () => loadSettings();

onMounted(() => {
  loadSettings();
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
.og-preview { box-shadow: inset 0 0 0 1px rgba(0,0,0,.06); position: relative; overflow: hidden; }
.og-actions { flex: 1; }
</style>
