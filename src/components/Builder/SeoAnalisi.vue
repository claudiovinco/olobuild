<template>
  <!--
    Analisi SEO della pagina (Impostazioni pagina › SEO › Base). La fa il PHP sulla pagina resa come sul sito
    (Olobuild_Seo_Analisi, olobuild/v1/seo/analisi): le tile, anche non salvate, e i campi SEO appena scritti.
    Parte solo quando il riquadro si vede (gruppo SEO aperto e scheda Base), e si rifà dopo ogni modifica.
  -->
  <section ref="radice" class="sa" :aria-busy="inCorso ? 'true' : 'false'">
    <div class="sa-testa">
      <span class="sa-titolo">{{ t('Analisi della pagina') }}<InfoTip class="sa-info" :titolo="t('Analisi della pagina')" :testo="[t('Si legge la pagina come la vedono Google e gli assistenti IA: il testo delle tile, i titoli H1–H6, le immagini e i link. Testata e piè di pagina restano fuori.'), t('Le modifiche contano subito, anche prima di salvare.')]" /></span>
      <button type="button" class="sa-rifai" :disabled="inCorso" :title="t('Analizza di nuovo')" :aria-label="t('Analizza di nuovo')" @click="analizza">
        <svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" :class="{ 'sa-gira': inCorso }"><path d="M20 12a8 8 0 1 1-2.34-5.66M20 4v4h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
      </button>
    </div>

    <p v-if="errore" class="sa-nota sa-nota--err" role="status">{{ errore }}</p>
    <p v-else-if="!risultato" class="sa-nota" role="status">{{ t('Analisi della pagina…') }}</p>

    <template v-if="risultato">
      <p class="sa-riepilogo" role="status">
        <span v-if="conta.errore" class="sa-chip sa-chip--errore">{{ conta.errore }} {{ t('da correggere') }}</span>
        <span v-if="conta.attenzione" class="sa-chip sa-chip--attenzione">{{ conta.attenzione }} {{ t('da migliorare') }}</span>
        <span v-if="!conta.errore && !conta.attenzione" class="sa-chip sa-chip--ok">{{ t('Tutto a posto') }}</span>
      </p>

      <ul v-if="problemi.length" class="sa-lista">
        <li v-for="c in problemi" :key="c.id" class="sa-voce" :class="'sa-voce--' + c.esito">
          <svg class="sa-icona" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true"><path :d="ICONE[c.esito]" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" /></svg>
          <span class="sa-testo"><span class="mb-sr-only">{{ NOMI_ESITO[c.esito] }}: </span>{{ c.testo }}<InfoTip v-if="c.aiuto" class="sa-info" :titolo="c.testo" :testo="c.aiuto" /></span>
        </li>
      </ul>

      <button v-if="superati.length" type="button" class="sa-apri" :aria-expanded="String(mostraSuperati)" @click="mostraSuperati = !mostraSuperati">
        <svg viewBox="0 0 24 24" width="12" height="12" aria-hidden="true" :class="{ 'sa-giu': mostraSuperati }"><path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" /></svg>
        {{ t('Superati') }} ({{ superati.length }})
      </button>
      <ul v-if="mostraSuperati" class="sa-lista">
        <li v-for="c in superati" :key="c.id" class="sa-voce sa-voce--ok">
          <svg class="sa-icona" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true"><path :d="ICONE.ok" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" /></svg>
          <span class="sa-testo">{{ c.testo }}</span>
        </li>
      </ul>

      <button type="button" class="sa-apri" :aria-expanded="String(mostraTitoli)" @click="mostraTitoli = !mostraTitoli">
        <svg viewBox="0 0 24 24" width="12" height="12" aria-hidden="true" :class="{ 'sa-giu': mostraTitoli }"><path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" /></svg>
        {{ t('Titoli della pagina') }} ({{ titoli.length }})
      </button>
      <template v-if="mostraTitoli">
        <p v-if="!titoli.length" class="sa-nota">{{ t('Nessun titolo nella pagina.') }}</p>
        <ol v-else class="sa-titoli">
          <li v-for="(h, i) in titoli" :key="i" class="sa-h" :class="{ 'sa-h--problema': h.problema }" :style="{ paddingLeft: ((h.livello - 1) * 12) + 'px' }">
            <span class="sa-tag">H{{ h.livello }}</span>
            <span class="sa-h-testo">{{ h.testo || '—' }}</span>
            <span v-if="h.problema" class="sa-h-nota">{{ h.problema === 'salto' ? t('salta un livello') : t('vuoto') }}</span>
          </li>
        </ol>
      </template>
    </template>
  </section>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { t } from '@/i18n';
import { useBuilderStore } from '@/stores/builder';
import { useTilesStore } from '@/stores/tiles';
import InfoTip from './InfoTip.vue';

const props = defineProps({
  // Il contenuto a cui appartiene la SEO (lo stesso di usePageSeo).
  postId: { type: Number, required: true },
  // I campi SEO della pagina, anche non ancora salvati (usePageSeo().data).
  seo: { type: Object, required: true },
});

const builderStore = useBuilderStore();
const tilesStore = useTilesStore();

// Icone del set (tratto), mai emoji.
const ICONE = {
  errore: 'M12 3a9 9 0 1 0 0 18a9 9 0 0 0 0-18zM15 9l-6 6M9 9l6 6',
  attenzione: 'M12 3.5l9.5 16.5h-19L12 3.5zM12 10v4M12 17h.01',
  info: 'M12 3a9 9 0 1 0 0 18a9 9 0 0 0 0-18zM12 11v5M12 8h.01',
  ok: 'M12 3a9 9 0 1 0 0 18a9 9 0 0 0 0-18zM8 12.5l2.5 2.5L16 9.5',
};
const NOMI_ESITO = { errore: t('Da correggere'), attenzione: t('Da migliorare'), info: t('Nota'), ok: t('Superato') };
const ORDINE = { errore: 0, attenzione: 1, info: 2, ok: 3 };

const radice = ref(null);
const risultato = ref(null);
const errore = ref('');
const inCorso = ref(false);
const mostraSuperati = ref(false);
const mostraTitoli = ref(false);

const conta = computed(() => risultato.value?.riepilogo || {});
const controlli = computed(() => (risultato.value?.controlli || []).slice().sort((a, b) => ORDINE[a.esito] - ORDINE[b.esito]));
const problemi = computed(() => controlli.value.filter((c) => c.esito !== 'ok'));
const superati = computed(() => controlli.value.filter((c) => c.esito === 'ok'));
const titoli = computed(() => risultato.value?.titoli || []);

const oloData = window.oloData || {};
const URL_ANALISI = (oloData.restUrl || '/wp-json/olobuild/v1/').replace(/\/$/, '') + '/seo/analisi';

// Solo i campi che l'analisi legge: scrivere l'OG Title non deve rifarla.
function campiSeo() {
  const s = props.seo || {};
  return {
    title: s.title || '',
    description: s.description || '',
    focus_keyword: s.focus_keyword || '',
    noindex: !!s.noindex,
    canonical: s.canonical || '',
    og_image: s.og_image || '',
    schema_type: s.schema_type || '',
    schema_data: (s.schema_data && typeof s.schema_data === 'object' && !Array.isArray(s.schema_data)) ? s.schema_data : {},
  };
}

let richiesta = 0;
let timer = null;
let daRifare = false; // cambiato qualcosa mentre il riquadro non si vedeva

async function analizza() {
  clearTimeout(timer);
  if (!props.postId) return;
  const n = ++richiesta;
  inCorso.value = true;
  try {
    const res = await fetch(URL_ANALISI, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': oloData.nonce || '' },
      body: JSON.stringify({
        post_id: props.postId,
        tiles: JSON.parse(JSON.stringify(tilesStore.canvasTiles || [])),
        page_settings: JSON.parse(JSON.stringify(builderStore.pageSettings || {})),
        seo: campiSeo(),
      }),
    });
    const j = await res.json().catch(() => ({}));
    if (n !== richiesta) return; // ne è partita un'altra: vale l'ultima
    if (!res.ok) throw new Error((j && j.message) || 'HTTP ' + res.status);
    risultato.value = j;
    errore.value = '';
  } catch (e) {
    if (n !== richiesta) return;
    errore.value = t('Analisi non riuscita') + ': ' + String((e && e.message) || e);
  } finally {
    if (n === richiesta) inCorso.value = false;
  }
}

// ── Si analizza solo quando il riquadro si vede ──
const visibile = ref(false);
let osservatore = null;

function programma(ritardo) {
  if (!visibile.value) { daRifare = true; return; }
  clearTimeout(timer);
  timer = setTimeout(analizza, ritardo);
}

onMounted(() => {
  if (typeof IntersectionObserver === 'undefined') {
    visibile.value = true;
    analizza();
    return;
  }
  osservatore = new IntersectionObserver((voci) => {
    const ora = voci.some((v) => v.isIntersecting);
    if (ora && !visibile.value && (daRifare || !risultato.value)) {
      daRifare = false;
      clearTimeout(timer);
      timer = setTimeout(analizza, 50);
    }
    visibile.value = ora;
  });
  if (radice.value) osservatore.observe(radice.value);
});

onBeforeUnmount(() => {
  clearTimeout(timer);
  richiesta++; // una risposta in volo non tocca più niente
  if (osservatore) osservatore.disconnect();
});

// Le tile: struttura (tilesVersion) e proprietà (deep) — dopo un attimo di calma, il render costa.
watch(() => tilesStore.tilesVersion, () => programma(1500));
watch(() => tilesStore.canvasTiles, () => programma(1500), { deep: true });
watch(() => builderStore.pageSettings, () => programma(1500), { deep: true });
// I campi SEO: un render in meno, si risponde prima.
watch(() => JSON.stringify(campiSeo()), () => programma(700));
watch(() => props.postId, () => { risultato.value = null; programma(50); });
</script>

<style scoped>
/*
 * Colori del CHROME, per la superficie chiara del pannello (come PageSettingsPanel): testo #1e293b, aiuti
 * #64748b (4,5:1), ok #047857, attenzione #b45309, errore #b91c1c (≥ 4,8:1), nota #1d4ed8. Scritti a mano.
 */
.sa { display: flex; flex-direction: column; gap: 8px; padding-top: 10px; border-top: 1px solid #e2e8f0; }
.sa-testa { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.sa-titolo { font-size: 11px; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase; color: #475569; display: inline-flex; align-items: center; }
.sa-info { margin-left: 4px; color: #64748b; text-transform: none; letter-spacing: normal; font-weight: 400; }
.sa-rifai {
  display: inline-flex; align-items: center; justify-content: center;
  width: 26px; height: 26px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #475569; cursor: pointer;
}
.sa-rifai:hover:not(:disabled) { color: #1e293b; border-color: #94a3b8; }
.sa-rifai:disabled { cursor: progress; }
.sa-gira { animation: sa-gira 0.9s linear infinite; }
@keyframes sa-gira { to { transform: rotate(360deg); } }
@media (prefers-reduced-motion: reduce) { .sa-gira { animation: none; } }

.sa-nota { margin: 0; font-size: 12px; color: #64748b; }
.sa-nota--err { color: #b91c1c; }

.sa-riepilogo { display: flex; flex-wrap: wrap; gap: 6px; margin: 0; }
.sa-chip { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 600; }
.sa-chip--errore { background: #fef2f2; color: #b91c1c; }
.sa-chip--attenzione { background: #fffbeb; color: #b45309; }
.sa-chip--ok { background: #ecfdf5; color: #047857; }

.sa-lista { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 6px; }
.sa-voce { display: flex; align-items: flex-start; gap: 6px; font-size: 12px; line-height: 1.4; color: #1e293b; }
.sa-icona { flex: 0 0 auto; margin-top: 2px; }
.sa-voce--errore .sa-icona { color: #b91c1c; }
.sa-voce--attenzione .sa-icona { color: #b45309; }
.sa-voce--info .sa-icona { color: #1d4ed8; }
.sa-voce--ok .sa-icona { color: #047857; }
.sa-testo { flex: 1 1 auto; min-width: 0; }

.sa-apri {
  display: inline-flex; align-items: center; gap: 4px; align-self: flex-start;
  padding: 2px 0; border: 0; background: none; font-size: 12px; font-weight: 600; color: #475569; cursor: pointer;
}
.sa-apri:hover { color: #1e293b; }
.sa-apri svg { transition: transform 0.15s; }
.sa-giu { transform: rotate(90deg); }

.sa-titoli { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 4px; }
.sa-h { display: flex; align-items: baseline; gap: 6px; font-size: 12px; color: #1e293b; min-width: 0; }
.sa-tag { flex: 0 0 auto; font: 600 10px/1.6 ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; color: #475569; background: #f1f5f9; border-radius: 4px; padding: 0 4px; }
.sa-h-testo { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sa-h--problema .sa-tag { background: #fffbeb; color: #b45309; }
.sa-h-nota { flex: 0 0 auto; font-size: 11px; color: #b45309; }

.sa-rifai:focus-visible,
.sa-apri:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e1474f);
  outline-offset: 2px;
}
</style>
