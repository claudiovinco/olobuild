<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Performance') }} <em>{{ t('& Cache') }}</em></h1>
      <p>{{ t('Ottimizzazioni che migliorano i Core Web Vitals senza toccare il design. Critical CSS, defer JS, lazy load, resource hints.') }}</p>
    </div>
    <div class="head-actions">
      <span class="cfg-pill" :class="scoreClass"><span class="dot"></span> {{ t('Score') }} {{ stats.score }}/100</span>
      <button class="cfg-btn cfg-btn-secondary" @click="purgeAll" :disabled="purging">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7l3 2.7"/><path d="M21 3v6h-6"/></svg>
        {{ t('Svuota tutto') }}
      </button>
    </div>
  </div>

  <!-- Stato cache -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14 8 10"/><circle cx="12" cy="14" r="9"/><path d="M3 14a9 9 0 0 1 18 0"/></svg>
      </div>
      <div>
        <h3>{{ t('Stato cache') }}</h3>
        <p>{{ t('Ultima generazione') }}: {{ stats.last_purge || '—' }}</p>
      </div>
      <div class="head-actions">
        <button class="cfg-btn cfg-btn-secondary" @click="regenerateCache">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7l3 2.7"/><path d="M21 3v6h-6"/></svg>
          {{ t('Rigenera critical CSS') }}
        </button>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="usage-grid">
        <div class="usage-card">
          <div class="usage-label">{{ t('Pagine cachate') }}</div>
          <div class="usage-value">{{ stats.pages_cached }}</div>
          <div class="usage-trend">{{ t('di') }} {{ stats.pages_total }} {{ t('totali') }}</div>
        </div>
        <div class="usage-card">
          <div class="usage-label">{{ t('Dimensione cache CSS') }}</div>
          <div class="usage-value">{{ stats.size }}</div>
          <div class="usage-trend">{{ t('limite') }} {{ stats.size_max }}</div>
        </div>
        <div class="usage-card">
          <div class="usage-label">{{ t('Flag attivi') }}</div>
          <div class="usage-value">{{ activeFlags }}<span class="of-total">/{{ FLAG_KEYS.length }}</span></div>
          <div class="usage-trend">{{ t('configurati') }}</div>
        </div>
        <div class="usage-card">
          <div class="usage-label">{{ t('Score performance') }}</div>
          <div class="usage-value">{{ stats.score }}<span class="of-total">/100</span></div>
          <div class="usage-trend">{{ scoreLabel }}</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Critical CSS -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
      </div>
      <div>
        <h3>{{ t('Critical CSS') }}</h3>
        <p>{{ t('Inietta inline il CSS above-the-fold per ogni pagina, defera il resto. Boost diretto su FCP e LCP.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Abilita Critical CSS') }}</label>
          <div class="hint">{{ t('Genera e inietta il CSS critico per ogni pagina visitata.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.critical_css_enabled }" @click="set('critical_css_enabled', !form.critical_css_enabled)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Durata cache (giorni)') }}</label>
          <div class="hint">{{ t('Dopo quanti giorni il critical CSS viene rigenerato. Invalidato comunque al save di un template.') }}</div>
        </div>
        <div class="control-col">
          <CfgNumber :min="1" :max="30" :suffix="t('giorni')" :model-value="form.critical_css_ttl" @update:model-value="set('critical_css_ttl', $event)" />
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Sezioni above-the-fold') }}</label>
          <div class="hint">{{ t('Quante sezioni iniziali analizzare per estrarre il CSS critico.') }}</div>
        </div>
        <div class="control-col">
          <CfgNumber :min="1" :max="5" :suffix="t('sezioni')" :model-value="form.critical_css_sections" @update:model-value="set('critical_css_sections', $event)" />
        </div>
      </div>
    </div>
  </div>

  <!-- Asset Optimizer -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 18 6-6-6-6M8 6l-6 6 6 6"/></svg>
      </div>
      <div>
        <h3>{{ t('Asset optimizer') }}</h3>
        <p>{{ t('Defer JS, cache file CSS, minify del CSS generato dai template.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Full-page cache') }}</label>
          <div class="hint">{{ t('Salva l\'HTML generato e lo serve prima di WordPress: abbatte il tempo di risposta (TTFB). Esclude automaticamente utenti loggati, carrello/checkout WooCommerce e richieste POST. Installa il drop-in advanced-cache.php e attiva WP_CACHE; si svuota a ogni modifica di contenuto.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.full_page_cache }" @click="set('full_page_cache', !form.full_page_cache)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Defer JavaScript') }}</label>
          <div class="hint">{{ t('Aggiunge defer agli script frontend di Olobuild. Non blocca il rendering della pagina.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.defer_js }" @click="set('defer_js', !form.defer_js)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Cache CSS su file statici') }}</label>
          <div class="hint">{{ t('Salva il CSS dei template come file invece di iniettarlo inline. Migliora il caching del browser.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.css_cache_files }" @click="set('css_cache_files', !form.css_cache_files)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Minifica CSS') }}</label>
          <div class="hint">{{ t('Rimuove commenti, spazi e newline dal CSS generato dai template e dal CSS frontend del plugin.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.minify_css }" @click="set('minify_css', !form.minify_css)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('CSS per-tile') }}</label>
          <div class="hint">{{ t('Serve solo le porzioni di CSS dei tile presenti in pagina (mappa, gallery, hero, postgrid…) invece dell\'intero foglio. Riduce il CSS inutilizzato; fallback automatico al CSS completo in caso di dubbio.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.css_per_tile }" @click="set('css_per_tile', !form.css_per_tile)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('UIkit subset') }}</label>
          <div class="hint">{{ t('Serve solo i componenti UIkit usati dal sito, appresi automaticamente dalle pagine. Prima visita col CSS completo, poi subset; si ri-apprende al salvataggio dei template.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.uikit_subset }" @click="set('uikit_subset', !form.uikit_subset)" role="switch"></button></div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Cache browser per i media (.htaccess)') }}</label>
          <div class="hint">{{ t('Scrive header Expires/Cache-Control per immagini, video e font (6 mesi) e CSS/JS (1 mese). Solo server Apache/LiteSpeed.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.browser_cache_headers }" @click="set('browser_cache_headers', !form.browser_cache_headers)" role="switch"></button></div>
      </div>
    </div>
  </div>

  <!-- Performance Hints -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      </div>
      <div>
        <h3>{{ t('Resource hints & loading') }}</h3>
        <p>{{ t('Suggerimenti al browser per precaricare risorse e ottimizzare il caricamento immagini.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('DNS prefetch & preconnect automatici') }}</label>
          <div class="hint">{{ t('dns-prefetch + preconnect per Google Fonts, YouTube, Vimeo e altri domini esterni rilevati.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.resource_hints }" @click="set('resource_hints', !form.resource_hints)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Preload font custom') }}</label>
          <div class="hint">{{ t('Precarica i font usati come body/heading per evitare FOUT (Flash of Unstyled Text).') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.font_preload }" @click="set('font_preload', !form.font_preload)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Video facade YouTube/Vimeo') }}</label>
          <div class="hint">{{ t('Mostra una preview statica, l\'iframe carica solo al click. ~500 KB risparmiati per video.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.video_facade }" @click="set('video_facade', !form.video_facade)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Lazy load video self-hosted') }}</label>
          <div class="hint">{{ t('I video autoplay (sfondi, filmreel, decorativi) si scaricano e partono solo quando entrano nel viewport. Risparmia decine di MB su pagine ricche di video.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.lazy_videos }" @click="set('lazy_videos', !form.lazy_videos)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('fetchpriority hero image') }}</label>
          <div class="hint">{{ t('Aggiunge fetchpriority="high" alla prima immagine e rimuove lazy dagli elementi above-fold.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.fetchpriority }" @click="set('fetchpriority', !form.fetchpriority)" role="switch"></button></div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Lazy loading immagini below-fold') }}</label>
          <div class="hint">{{ t('Aggiunge loading="lazy" alle immagini sotto la fold. Riduce il peso iniziale della pagina.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.lazy_images }" @click="set('lazy_images', !form.lazy_images)" role="switch"></button></div>
      </div>
    </div>
  </div>

  <!-- Domini custom -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
      </div>
      <div>
        <h3>{{ t('Domini custom') }}</h3>
        <p>{{ t('Aggiungi domini per dns-prefetch e preconnect personalizzati (uno per riga).') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('DNS prefetch') }}</label>
          <div class="hint">{{ t('Risolve il DNS in anticipo. Utile per CDN e servizi esterni.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-textarea">
            <textarea rows="3" :value="form.dns_prefetch_domains" @input="set('dns_prefetch_domains', $event.target.value)" placeholder="//cdn.example.com&#10;//api.example.com"></textarea>
          </div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Preconnect') }}</label>
          <div class="hint">{{ t('Apre connessione completa (DNS + TCP + TLS). Usare solo per risorse critiche.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-textarea">
            <textarea rows="3" :value="form.preconnect_domains" @input="set('preconnect_domains', $event.target.value)" placeholder="https://cdn.example.com&#10;https://api.example.com"></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Head cleanup -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/></svg>
      </div>
      <div>
        <h3>{{ t('Pulizia head') }}</h3>
        <p>{{ t('Rimuovi script e CSS non necessari dal <head> per ridurre richieste e peso.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Rimuovi jQuery Migrate') }}</label>
          <div class="hint">{{ t('jquery-migrate.js (~10 KB). Necessario solo per plugin molto vecchi.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.remove_jquery_migrate }" @click="set('remove_jquery_migrate', !form.remove_jquery_migrate)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Rimuovi emoji scripts') }}</label>
          <div class="hint">{{ t('wp-emoji-release.min.js — i browser moderni gestiscono emoji nativamente.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.remove_emoji_scripts }" @click="set('remove_emoji_scripts', !form.remove_emoji_scripts)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Rimuovi Block CSS') }}</label>
          <div class="hint">{{ t('wp-block-library-css (~30 KB). Solo se non usi blocchi Gutenberg nel frontend.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.remove_block_css }" @click="set('remove_block_css', !form.remove_block_css)" role="switch"></button></div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Rimuovi Classic Theme CSS') }}</label>
          <div class="hint">{{ t('classic-theme-styles-css. Inutile se non usi un tema classico WordPress.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.remove_classic_theme }" @click="set('remove_classic_theme', !form.remove_classic_theme)" role="switch"></button></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgNumber from './controls/CfgNumber.vue';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const purging = ref(false);

const form = ref({
  // Critical CSS
  critical_css_enabled: false,
  critical_css_ttl: 7,
  critical_css_sections: 2,
  // Asset Optimizer
  full_page_cache: false,
  defer_js: true,
  css_cache_files: true,
  minify_css: true,
  css_per_tile: false,
  uikit_subset: false,
  // Performance Hints
  resource_hints: true,
  font_preload: true,
  video_facade: true,
  fetchpriority: true,
  lazy_images: true,
  lazy_videos: true,
  browser_cache_headers: false,
  // Head cleanup
  remove_jquery_migrate: false,
  remove_emoji_scripts: false,
  remove_block_css: false,
  remove_classic_theme: false,
  // Custom domains
  dns_prefetch_domains: '',
  preconnect_domains: '',
});

const stats = ref({
  score: 0,
  last_purge: '—',
  pages_cached: 0,
  pages_total: 0,
  size: '—',
  size_max: '500 MB',
});

const FLAG_KEYS = [
  'critical_css_enabled', 'full_page_cache', 'defer_js', 'css_cache_files', 'minify_css',
  'css_per_tile', 'uikit_subset', 'resource_hints', 'font_preload', 'video_facade',
  'fetchpriority', 'lazy_images', 'lazy_videos', 'browser_cache_headers',
  'remove_jquery_migrate', 'remove_emoji_scripts',
];
const activeFlags = computed(() => FLAG_KEYS.filter(k => !!form.value[k]).length);

const scoreClass = computed(() => {
  if (stats.value.score >= 85) return 'ok';
  if (stats.value.score >= 70) return 'warn';
  return 'off';
});
const scoreLabel = computed(() => {
  if (stats.value.score >= 85) return t('ottimo');
  if (stats.value.score >= 70) return t('buono');
  return t('da migliorare');
});

function set(k, v) { form.value[k] = v; setDirty(true); }

async function purgeAll() {
  if (purging.value || !confirm(t('Svuotare tutta la cache?'))) return;
  purging.value = true;
  try {
    const body = new FormData();
    body.append('action', 'olobuild_perf_purge_critical');
    body.append('_nonce', window.oloData.perfNonce);
    const res = await fetch(window.oloData.ajaxUrl, { method: 'POST', body, credentials: 'same-origin' });
    const d = res.ok ? await res.json() : null;
    if (!d || !d.success) throw new Error(d && d.data ? d.data : 'ajax');
    showToast(d.data.message || t('Cache svuotata'), 'success');
    await loadStats();
  } catch (e) { showToast(t('Errore svuotamento cache'), 'error'); }
  finally { purging.value = false; }
}

async function regenerateCache() {
  try {
    const body = new FormData();
    body.append('action', 'olobuild_perf_regenerate_critical');
    body.append('_nonce', window.oloData.perfNonce);
    const res = await fetch(window.oloData.ajaxUrl, { method: 'POST', body, credentials: 'same-origin' });
    const d = res.ok ? await res.json() : null;
    if (!d || !d.success) throw new Error(d && d.data ? d.data : 'ajax');
    showToast(d.data.message || t('Rigenerazione completata'), 'success');
    await loadStats();
  } catch (e) { showToast(t('Errore rigenerazione'), 'error'); }
}

async function loadSettings() {
  try {
    const res = await fetch(`${window.oloData.restUrl}performance`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) Object.assign(form.value, await res.json());
  } catch (e) { /* defaults */ }
}
async function loadStats() {
  try {
    const res = await fetch(`${window.oloData.restUrl}performance/stats`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) Object.assign(stats.value, await res.json());
  } catch (e) { /* keep dashes */ }
}

async function saveSettings() {
  try {
    await fetch(`${window.oloData.restUrl}performance`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(form.value),
    });
    await loadStats(); // refresh score dopo save
  } catch (e) { showToast(t('Errore di salvataggio Performance'), 'error'); }
}

const onSave = () => saveSettings();
const onDiscard = () => loadSettings();

onMounted(() => {
  loadSettings();
  loadStats();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>

<style scoped>
.usage-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
@media (max-width: 900px) { .usage-grid { grid-template-columns: 1fr 1fr; } }
.usage-card { background: var(--c-bg); border-radius: 10px; padding: 14px; }
.usage-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--c-text-mute); }
.usage-value { font-family: var(--c-display); font-size: 32px; line-height: 1; margin-top: 6px; color: var(--c-navy); }
.usage-value .of-total { font-size: 16px; color: var(--c-text-faint); margin-left: 2px; }
.usage-trend { font-size: 11px; color: var(--c-text-faint); margin-top: 4px; }
</style>
