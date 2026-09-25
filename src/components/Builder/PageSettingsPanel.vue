<template>
  <div class="psp mb-space-y-3">
    <!--
      Impostazioni pagina: stesse superfici chiare dell'inspector delle tile. Testata
      (.v2i-head) e corpo che scorre (.v2i-content, che schiarisce anche i CollapseSection)
      li mette BuilderInspector. Qui solo classi proprie psp-*: le mb-text-gray-* sono
      rimappate da main.scss con l'ID di #olobuilder-app e !important, e sul bianco
      scendevano sotto 4,5:1 (#888, #aaa) senza che un override scoped potesse vincere.

      Tre gruppi, uno per modo di salvare, ciascuno detto nella sua nota: Pagina (col
      template, Salva / Ctrl+S) · SEO (salvataggio automatico sul post collegato) · Sito
      (opzione di WordPress, subito, solo con manage_options). Aperto/chiuso si ricorda
      per utente. L'Evidenziazione scroll, preferenza di chi usa l'editor, sta in
      «Preferenze editor» (EditorPrefsDialog, dalla finestra delle Scorciatoie), non qui.
    -->
    <CollapseSection macro :title="t('Pagina')" :defaultOpen="true" :storageKey="chiaveGruppo('pagina')">
      <p class="psp-note">{{ t('Si salva con la pagina: Salva o Ctrl+S (⌘S su Mac).') }}</p>

      <!-- Layout -->
      <CollapseSection :title="t('Layout')" :defaultOpen="true">
        <div class="psp-group">
          <div v-if="isSingleTemplate" class="psp-field">
            <span class="psp-label">{{ t('Post Type') }}</span>
            <FieldSelect
              ui="dropdown"
              :aria-label="t('Post Type')"
              :modelValue="pageSettings.single_post_type || ''"
              :options="postTypes"
              @update:modelValue="builderStore.updatePageSetting('single_post_type', $event)"
            />
          </div>
          <div class="psp-field">
            <span class="psp-label">{{ t('Larghezza max contenuto') }}</span>
            <FieldSelect
              ui="dropdown"
              :aria-label="t('Larghezza max contenuto')"
              :modelValue="pageSettings.content_max_width"
              :options="contentWidthOptions"
              @update:modelValue="builderStore.updatePageSetting('content_max_width', $event)"
            />
          </div>
        </div>
      </CollapseSection>

      <!-- Sfondo pagina: BackgroundControls è già una card chiara autosufficiente -->
      <CollapseSection :title="t('Sfondo pagina')" :defaultOpen="true">
        <div class="psp-group">
          <BackgroundControls
            :modelValue="pageSettings.page_bg"
            :showParallax="true"
            @update:modelValue="onBgUpdate"
          />
        </div>
      </CollapseSection>

      <!-- Effetti di pagina: aperto da sé se un effetto è acceso (chiuso lo nasconderebbe) -->
      <CollapseSection :title="t('Effetti di pagina')" :defaultOpen="effettiAttivi">
        <div class="psp-group">
          <div class="psp-row">
            <label for="psp-crt" class="psp-label">{{ t('Overlay CRT (scanline + vignetta)') }}</label>
            <FieldToggle
              id="psp-crt"
              class="psp-switch"
              role="switch"
              :aria-checked="String(pageSettings.page_crt_enabled === true)"
              :modelValue="pageSettings.page_crt_enabled === true"
              @update:modelValue="builderStore.updatePageSetting('page_crt_enabled', $event)"
            />
          </div>
          <p class="psp-help">{{ t('Decoratore a tutta pagina in stile schermo CRT. Statico con riduzione del movimento.') }}</p>
          <template v-if="pageSettings.page_crt_enabled">
            <div v-for="r in CRT_RANGES" :key="r.key" class="psp-row">
              <span class="psp-label">{{ t(r.label) }}</span>
              <FieldRange
                compact
                :unit="r.unit"
                :min="r.min"
                :max="r.max"
                :step="r.step"
                :defaultValue="r.def"
                :aria-label="t(r.label)"
                :modelValue="pageNum(r)"
                @update:modelValue="setPageInt(r, $event)"
                @focusout="risincronizza($event, pageNum(r))"
              />
            </div>
            <div class="psp-row psp-row--fill">
              <span class="psp-label">{{ t('Fusione') }}</span>
              <FieldSelect ui="dropdown" :aria-label="t('Fusione')" :modelValue="pageSettings.page_crt_blend_mode || 'overlay'" :options="crtBlendOptions" @update:modelValue="builderStore.updatePageSetting('page_crt_blend_mode', $event)" />
            </div>
            <div class="psp-row">
              <label for="psp-crt-flicker" class="psp-label">{{ t('Sfarfallio animato') }}</label>
              <FieldToggle
                id="psp-crt-flicker"
                class="psp-switch"
                role="switch"
                :aria-checked="String(pageSettings.page_crt_flicker === true)"
                :modelValue="pageSettings.page_crt_flicker === true"
                @update:modelValue="builderStore.updatePageSetting('page_crt_flicker', $event)"
              />
            </div>
          </template>

          <div class="psp-row">
            <label for="psp-grain" class="psp-label">{{ t('Grana pellicola') }}</label>
            <FieldToggle
              id="psp-grain"
              class="psp-switch"
              role="switch"
              :aria-checked="String(pageSettings.page_grain_enabled === true)"
              :modelValue="pageSettings.page_grain_enabled === true"
              @update:modelValue="builderStore.updatePageSetting('page_grain_enabled', $event)"
            />
          </div>
          <p class="psp-help">{{ t('Rumore organico a tutta pagina, animato a scatti come una pellicola. Statico con riduzione del movimento.') }}</p>
          <template v-if="pageSettings.page_grain_enabled">
            <div v-for="r in GRAIN_RANGES" :key="r.key" class="psp-row">
              <span class="psp-label">{{ t(r.label) }}</span>
              <FieldRange
                compact
                :unit="r.unit"
                :min="r.min"
                :max="r.max"
                :step="r.step"
                :defaultValue="r.def"
                :aria-label="t(r.label)"
                :modelValue="pageNum(r)"
                @update:modelValue="setPageInt(r, $event)"
                @focusout="risincronizza($event, pageNum(r))"
              />
            </div>
            <!-- Chiave assente = animata, come il PHP (array_key_exists): da qui `?? true` -->
            <div class="psp-row">
              <label for="psp-grain-animate" class="psp-label">{{ t('Animazione a scatti') }}</label>
              <FieldToggle
                id="psp-grain-animate"
                class="psp-switch"
                role="switch"
                :aria-checked="String((pageSettings.page_grain_animate ?? true) === true)"
                :modelValue="(pageSettings.page_grain_animate ?? true) === true"
                @update:modelValue="builderStore.updatePageSetting('page_grain_animate', $event)"
              />
            </div>
            <div class="psp-row">
              <label for="psp-grain-mobile" class="psp-label">{{ t('Mostra anche su touch/mobile') }}</label>
              <FieldToggle
                id="psp-grain-mobile"
                class="psp-switch"
                role="switch"
                :aria-checked="String(pageSettings.page_grain_mobile === true)"
                :modelValue="pageSettings.page_grain_mobile === true"
                @update:modelValue="builderStore.updatePageSetting('page_grain_mobile', $event)"
              />
            </div>
            <p class="psp-help">{{ t('Di default la grana è disattivata sui dispositivi touch: il layer in blend a tutto schermo può rendere lo scorrimento meno fluido.') }}</p>
          </template>
        </div>
      </CollapseSection>
    </CollapseSection>

    <!-- SEO: solo quando il template è collegato a un post -->
    <CollapseSection v-if="seo.isReady.value" macro :title="t('SEO')" :storageKey="chiaveGruppo('seo')">
      <template #header-right>
        <span class="psp-status" :class="{ 'psp-status--err': seoStatoErrore }" aria-live="polite">{{ seoStato }}</span>
      </template>
      <p class="psp-note">{{ t('Salvataggio automatico sul contenuto collegato: non serve Salva.') }}</p>
      <div class="psp-group">
        <FieldSelect
          ui="segmented"
          :modelValue="seoTab"
          :options="seoTabOptions"
          @update:modelValue="seoTab = $event"
        />

        <!-- TAB: Base (title + description + keyword + Google preview) -->
        <div v-if="seoTab === 'seo'" class="psp-stack">
          <!-- Anteprima Google: i colori sono quelli della pagina dei risultati, non del chrome -->
          <div class="psp-serp mb-rounded-md mb-bg-white mb-text-gray-900 mb-px-3 mb-py-2 mb-text-[12px] mb-leading-snug">
            <div class="mb-text-[14px]" style="color:#1a0dab;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ seoTitleDisplay }}</div>
            <div style="color:#006621;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ seoUrlDisplay }}</div>
            <div style="color:#545454;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ seoDescDisplay || t('(Nessuna descrizione: imposta la Meta Description qui sotto.)') }}</div>
          </div>

          <div class="psp-field">
            <label for="psp-seo-title" class="psp-label psp-label--split">
              <span>{{ t('SEO Title') }}</span>
              <span :class="seoTitleCls">{{ seoTitleLen }}/60</span>
            </label>
            <input id="psp-seo-title" type="text"
              :value="seo.data.value.title"
              @input="seoUpdate('title', $event.target.value)"
              :placeholder="seo.defaults.value.post_title + ' · ' + seo.defaults.value.site_name"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
          </div>

          <div class="psp-field">
            <label for="psp-seo-desc" class="psp-label psp-label--split">
              <span>{{ t('Meta Description') }}</span>
              <span :class="seoDescCls">{{ seoDescLen }}/160</span>
            </label>
            <textarea id="psp-seo-desc" rows="3"
              :value="seo.data.value.description"
              @input="seoUpdate('description', $event.target.value)"
              :placeholder="t('Descrivi questa pagina in 120-160 caratteri…')"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"></textarea>
          </div>

          <div class="psp-field">
            <label for="psp-seo-kw" class="psp-label">{{ t('Focus keyword') }}</label>
            <input id="psp-seo-kw" type="text"
              :value="seo.data.value.focus_keyword"
              @input="seoUpdate('focus_keyword', $event.target.value)"
              :placeholder="t('Es. page builder wordpress')"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
          </div>
        </div>

        <!-- TAB: Social (OG + Twitter) -->
        <div v-else-if="seoTab === 'social'" class="psp-stack">
          <div class="psp-field">
            <label for="psp-og-title" class="psp-label">{{ t('OG Title (Facebook/LinkedIn)') }}</label>
            <input id="psp-og-title" type="text"
              :value="seo.data.value.og_title"
              @input="seoUpdate('og_title', $event.target.value)"
              :placeholder="t('Usa SEO Title se vuoto')"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
          </div>
          <div class="psp-field">
            <label for="psp-og-desc" class="psp-label">{{ t('OG Description') }}</label>
            <textarea id="psp-og-desc" rows="2"
              :value="seo.data.value.og_description"
              @input="seoUpdate('og_description', $event.target.value)"
              :placeholder="t('Usa Meta Description se vuoto')"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"></textarea>
          </div>
          <div class="psp-field">
            <label for="psp-og-image" class="psp-label">{{ t('OG Image (1200×630)') }}</label>
            <div class="mb-flex mb-gap-2 mb-items-center">
              <input id="psp-og-image" type="text"
                :value="seo.data.value.og_image"
                @input="seoUpdate('og_image', $event.target.value)"
                placeholder="https://…"
                class="mb-flex-1 mb-min-w-0 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
              <button type="button" class="psp-btn" @click="seoPickOgImage">{{ t('Seleziona') }}</button>
            </div>
            <img v-if="seo.data.value.og_image" :src="seo.data.value.og_image" alt="" class="psp-thumb mb-mt-2 mb-max-h-24" />
          </div>
          <div class="psp-sep psp-stack">
            <p class="psp-help">{{ t('Twitter / X (fallback OG se vuoto)') }}</p>
            <input type="text"
              :value="seo.data.value.tw_title"
              @input="seoUpdate('tw_title', $event.target.value)"
              :placeholder="t('Twitter Title')"
              :aria-label="t('Twitter Title')"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
            <textarea rows="2"
              :value="seo.data.value.tw_description"
              @input="seoUpdate('tw_description', $event.target.value)"
              :placeholder="t('Twitter Description')"
              :aria-label="t('Twitter Description')"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"></textarea>
          </div>
        </div>

        <!-- TAB: Robots (canonical + noindex + nofollow) -->
        <div v-else-if="seoTab === 'advanced'" class="psp-stack">
          <div class="psp-field">
            <label for="psp-canonical" class="psp-label">{{ t('Canonical URL') }}</label>
            <input id="psp-canonical" type="text"
              :value="seo.data.value.canonical"
              @input="seoUpdate('canonical', $event.target.value)"
              :placeholder="seo.defaults.value.post_url"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
            <p class="psp-help">{{ t('Lascia vuoto per usare l\'URL della pagina.') }}</p>
          </div>
          <div class="psp-row">
            <label for="psp-noindex" class="psp-label"><code>noindex</code> — {{ t('non indicizzare') }}</label>
            <FieldToggle
              id="psp-noindex"
              class="psp-switch"
              role="switch"
              :aria-checked="String(!!seo.data.value.noindex)"
              :modelValue="!!seo.data.value.noindex"
              @update:modelValue="seoToggle('noindex')"
            />
          </div>
          <div class="psp-row">
            <label for="psp-nofollow" class="psp-label"><code>nofollow</code> — {{ t('non seguire i link') }}</label>
            <FieldToggle
              id="psp-nofollow"
              class="psp-switch"
              role="switch"
              :aria-checked="String(!!seo.data.value.nofollow)"
              :modelValue="!!seo.data.value.nofollow"
              @update:modelValue="seoToggle('nofollow')"
            />
          </div>
        </div>

        <!-- TAB: Schema (schema_type + extra_jsonld) -->
        <div v-else-if="seoTab === 'schema'" class="psp-stack">
          <div class="psp-field">
            <span class="psp-label">{{ t('Schema.org type (auto se vuoto)') }}</span>
            <FieldSelect
              ui="dropdown"
              :aria-label="t('Schema.org type (auto se vuoto)')"
              :modelValue="seo.data.value.schema_type"
              :options="schemaTypeOptions"
              @update:modelValue="seoUpdate('schema_type', $event)"
            />
          </div>
          <div class="psp-field">
            <label for="psp-jsonld" class="psp-label psp-label--split">
              <span>{{ t('JSON-LD personalizzato') }}</span>
              <span v-if="seoJsonldStatus.ok === true" class="psp-ok">✓ {{ seoJsonldStatus.msg }}</span>
              <span v-else-if="seoJsonldStatus.ok === false" class="psp-err">✗ {{ seoJsonldStatus.msg }}</span>
            </label>
            <textarea id="psp-jsonld" rows="10"
              :value="seo.data.value.extra_jsonld"
              @input="seoUpdate('extra_jsonld', $event.target.value)"
              :placeholder="'{ &quot;@context&quot;: &quot;https://schema.org&quot;, &quot;@graph&quot;: [ … ] }'"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-900 mb-font-mono"
              style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; tab-size: 2"></textarea>
            <p class="psp-help">{{ t('Iniettato come <script type=\"application/ld+json\"> nel <head>. Tag <script> facoltativi, vengono ripuliti server-side. JSON deve essere parsabile.') }}</p>
            <p v-if="seo.validationErrors.value.extra_jsonld" class="psp-help psp-err">{{ seo.validationErrors.value.extra_jsonld }}</p>
          </div>
        </div>

        <!-- TAB: FAQ schema -->
        <div v-else-if="seoTab === 'faq'" class="psp-stack">
          <p class="psp-help">{{ t('Coppie domanda/risposta — genera markup FAQPage (rich results Google).') }}</p>
          <div v-for="(item, idx) in seo.data.value.faq" :key="idx" class="psp-card">
            <div class="psp-card-head">
              <span class="psp-label">{{ t('Domanda') }} {{ idx + 1 }}</span>
              <button type="button" class="psp-del" :title="t('Elimina')" :aria-label="t('Elimina')" @click="seoRemoveFaq(idx)">×</button>
            </div>
            <input type="text"
              :value="item.q"
              @input="seoUpdateFaq(idx, 'q', $event.target.value)"
              :placeholder="t('Domanda')"
              :aria-label="t('Domanda')"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
            <textarea rows="2"
              :value="item.a"
              @input="seoUpdateFaq(idx, 'a', $event.target.value)"
              :placeholder="t('Risposta')"
              :aria-label="t('Risposta')"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"></textarea>
          </div>
          <p v-if="seo.validationErrors.value.faq" class="psp-help psp-err">{{ seo.validationErrors.value.faq }}</p>
          <button type="button" class="psp-btn psp-btn--full" @click="seoAddFaq">+ {{ t('Aggiungi FAQ') }}</button>
        </div>

        <p v-if="seo.lastError.value" class="psp-help psp-err">{{ seo.lastError.value }}</p>
      </div>
    </CollapseSection>

    <!-- Sito: opzioni di WordPress, valgono per tutte le pagine e si applicano subito -->
    <CollapseSection macro :title="t('Sito')" :storageKey="chiaveGruppo('sito')">
      <p class="psp-note">{{ t('Vale per tutto il sito e si applica subito, senza Salva.') }}</p>
      <div class="psp-group">
        <span class="psp-label">{{ t('Favicon') }}</span>
        <div v-if="faviconUrl" class="psp-fav">
          <img :src="faviconUrl" alt="" class="psp-fav-img" />
          <button
            v-if="canManageSite"
            type="button"
            class="psp-fav-del"
            :disabled="faviconBusy"
            :title="t('Rimuovi favicon')"
            :aria-label="t('Rimuovi favicon')"
            @click="removeFavicon"
          >×</button>
        </div>
        <button
          type="button"
          class="psp-btn psp-btn--full"
          :disabled="!canManageSite || faviconBusy"
          aria-describedby="psp-favicon-help"
          @click="pickFavicon"
        >
          {{ faviconUrl ? t('Cambia favicon') : t('Seleziona favicon') }}
        </button>
        <p v-if="canManageSite" id="psp-favicon-help" class="psp-help">{{ t('Imposta la favicon del sito (salvata in WordPress).') }}</p>
        <p v-else id="psp-favicon-help" class="psp-help">{{ t("Solo un amministratore può cambiare la favicon del sito.") }}</p>
      </div>
    </CollapseSection>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed, ref } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import BackgroundControls from './BackgroundControls.vue';
import CollapseSection from './CollapseSection.vue';
import { useMediaPicker } from '@/composables/useMediaPicker';
import { usePageSeo } from '@/composables/usePageSeo';
import { useToast } from '@/composables/useToast';
import { interoNelRange, risincronizzaNumero } from '@/utils/numeroIntero';
import FieldSelect from './fields/FieldSelect.vue';
import FieldToggle from './fields/FieldToggle.vue';
import FieldRange from './fields/FieldRange.vue';

const builderStore = useBuilderStore();
const pageSettings = computed(() => builderStore.pageSettings);
const { openSingleImage } = useMediaPicker();
const toast = useToast();
const oloData = window.oloData || {};

// Aperto/chiuso dei tre gruppi, ricordato per utente (CollapseSection storageKey).
function chiaveGruppo(nome) {
  return 'pagina-' + (oloData.userId || 0) + '-' + nome;
}

// Opzioni dei dropdown custom (FieldSelect applica t() alle label).
// Value numerici per content_max_width: FieldSelect emette il value originale,
// quindi il setting resta un number come col vecchio parseInt.
const contentWidthOptions = [
  { value: 960, label: '960px (Stretto)' },
  { value: 1200, label: '1200px (Predefinito)' },
  { value: 1400, label: '1400px (Largo)' },
  { value: 9999, label: 'Larghezza piena' },
];
const crtBlendOptions = [
  { value: 'overlay', label: 'Overlay' },
  { value: 'screen', label: 'Screen' },
  { value: 'soft-light', label: 'Soft Light' },
  { value: 'multiply', label: 'Multiply' },
  { value: 'normal', label: 'Normale' },
];
const schemaTypeOptions = [
  { value: '', label: 'Automatico' },
  { value: 'Article', label: 'Article' },
  { value: 'BlogPosting', label: 'BlogPosting' },
  { value: 'NewsArticle', label: 'NewsArticle' },
  { value: 'WebPage', label: 'WebPage' },
  { value: 'FAQPage', label: 'FAQPage' },
  { value: 'HowTo', label: 'HowTo' },
  { value: 'Product', label: 'Product' },
  { value: 'Event', label: 'Event' },
  { value: 'Recipe', label: 'Recipe' },
  { value: 'LocalBusiness', label: 'LocalBusiness' },
  { value: 'none', label: 'Nessuno' },
];
const seoTabOptions = [
  { value: 'seo', label: 'Base' },
  { value: 'social', label: 'Social' },
  { value: 'advanced', label: 'Robots' },
  { value: 'schema', label: 'Schema' },
  { value: 'faq', label: 'FAQ' },
];

// ── Campi numerici degli effetti di pagina ──
// Estremi e passi dei vecchi cursori nativi; `def` = il predefinito del renderer
// PHP (render_page_effects: intval( … ?? 50 ) ecc.), mostrato quando la chiave
// manca e ripristinato col doppio clic. L'unità sta nel controllo, non nell'etichetta.
const CRT_RANGES = [
  { key: 'page_crt_scanline_opacity', label: 'Intensità scanline', unit: '%', min: 0, max: 100, step: 5, def: 50 },
  { key: 'page_crt_scanline_gap', label: 'Passo scanline', unit: 'px', min: 2, max: 12, step: 1, def: 3 },
  { key: 'page_crt_vignette', label: 'Vignetta', unit: '%', min: 0, max: 100, step: 5, def: 55 },
];
const GRAIN_RANGES = [
  { key: 'page_grain_opacity', label: 'Intensità grana', unit: '%', min: 1, max: 30, step: 1, def: 7 },
  { key: 'page_grain_size', label: 'Dimensione pattern', unit: 'px', min: 80, max: 480, step: 20, def: 240 },
];
const effettiAttivi = computed(() =>
  pageSettings.value.page_crt_enabled === true || pageSettings.value.page_grain_enabled === true
);

// FieldRange emette STRINGHE: si torna all'intero entro gli estremi (i cursori nativi
// di prima salvavano parseInt), così le chiavi page_* restano numeri.
function pageNum(r) {
  return pageSettings.value[r.key] ?? r.def;
}
function setPageInt(r, raw) {
  const n = interoNelRange(raw, r.min, r.max);
  if (n !== null) builderStore.updatePageSetting(r.key, n);
}
const risincronizza = risincronizzaNumero;

// ── Favicon del sito ──
// È l'opzione site_icon di WordPress, non un dato della pagina: vale per tutto il
// sito e si applica subito. L'icona attuale arriva da oloData.siteInfo (niente GET a
// ogni apertura del pannello), che un cambio riuscito aggiorna: è la fonte da cui il
// pannello riparte quando si rimonta. Si scrive su wp/v2/settings dalla radice REST vera
// (oloData.wpRestRoot = rest_url(): sottocartella e ?rest_route= compresi), che
// richiede manage_options. Esito sempre detto: prima un 403 o un 404 passavano muti
// e il pannello mostrava un'icona mai salvata.
const canManageSite = !!oloData.canManageOptions;
const faviconUrl = ref((oloData.siteInfo && oloData.siteInfo.icon_url) || '');
const faviconBusy = ref(false);

function wpRestUrl(path) {
  const root = String(oloData.wpRestRoot || '');
  if (!root) throw new Error(t('indirizzo REST di WordPress mancante'));
  return (root.endsWith('/') ? root : root + '/') + path;
}

async function salvaIconaSito(id) {
  const res = await fetch(wpRestUrl('wp/v2/settings'), {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': oloData.nonce || '',
    },
    body: JSON.stringify({ site_icon: id }),
  });
  const j = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(j.message || ('HTTP ' + res.status));
  // Un 200 che non riporta l'icona chiesta non la dà per salvata.
  if (Number(j.site_icon) !== Number(id)) throw new Error(t('WordPress non ha registrato la favicon'));
}

async function cambiaIconaSito(id, url, domanda, esito) {
  if (!canManageSite || faviconBusy.value) return;
  if (!window.confirm(domanda)) return;
  faviconBusy.value = true;
  try {
    await salvaIconaSito(id);
    faviconUrl.value = url;
    // Anche nella fonte condivisa: il pannello si smonta a ogni selezione di una
    // tile e rinasce da oloData.siteInfo, che mostrerebbe l'icona di prima.
    if (!oloData.siteInfo) oloData.siteInfo = {};
    oloData.siteInfo.icon_url = url;
    toast.success(esito);
  } catch (e) {
    toast.error(t('Favicon non salvata') + ': ' + (e && e.message ? e.message : String(e)), 6000);
  } finally {
    faviconBusy.value = false;
  }
}

function pickFavicon() {
  if (!canManageSite) return;
  openSingleImage(({ url, id }) => cambiaIconaSito(
    id,
    url,
    t('Usare questa immagine come favicon di tutto il sito? Si applica subito, senza Salva.'),
    t('Favicon del sito aggiornata'),
  ));
}

function removeFavicon() {
  cambiaIconaSito(
    0,
    '',
    t('Togliere la favicon del sito? Si applica subito a tutte le pagine.'),
    t('Favicon del sito tolta'),
  );
}

const postTypes = oloData.postTypes || [];
const isSingleTemplate = computed(() => builderStore.currentTemplate?.type === 'single');

// ── SEO per-pagina ──
// Disponibile solo quando il template è associato a un post (page/single).
// Priorità: linked_post_id (calcolato server-side, primo post publish) → settings.post_id (legacy/preview).
// In passato usavamo settings.post_id direttamente, ma Olobuild crea draft "Handoff …" che ne sporcano
// il valore: il pannello SEO finiva a leggere/scrivere sul draft phantom invece della pagina pubblicata.
const seoPostId = computed(() => {
  const tpl = builderStore.currentTemplate;
  const linked = tpl?.linked_post_id;
  if (linked) return Number(linked);
  const sid = tpl?.settings?.post_id;
  return sid ? Number(sid) : 0;
});
const seoTab = ref('seo'); // 'seo' | 'social' | 'advanced' | 'schema' | 'faq'
const seo = usePageSeo(seoPostId);
// Nella testata del gruppo, visibile anche a gruppo chiuso: la SEO si salva da sola.
// «Salvato» solo se il server ha tenuto TUTTO; un campo scartato (JSON-LD non valido,
// voci FAQ senza domanda o risposta: risposta 200 con errors) lo dice anche a gruppo
// chiuso, dove l'errore sotto il campo non si vede. «Non salvato» è il POST fallito,
// «Non caricato» il GET iniziale.
const NOMI_CAMPI_SEO = { extra_jsonld: 'JSON-LD', faq: 'FAQ' };
const seoScartati = computed(() =>
  Object.keys(seo.validationErrors.value || {}).map((k) => NOMI_CAMPI_SEO[k] || k)
);
const seoStato = computed(() => {
  if (seo.saving.value) return t('Salvataggio…');
  if (seo.errorKind.value === 'save') return t('Non salvato');
  if (seoScartati.value.length) return t('Salvato in parte, scartato:') + ' ' + seoScartati.value.join(', ');
  if (seo.saved.value) return t('Salvato');
  if (seo.errorKind.value === 'load') return t('Non caricato');
  return '';
});
const seoStatoErrore = computed(() =>
  !seo.saving.value && (!!seo.errorKind.value || seoScartati.value.length > 0)
);

function seoUpdate(key, value) { seo.update(key, value); }
function seoToggle(key) { seoUpdate(key, !seo.data.value[key]); }
function seoAddFaq() {
  const list = Array.isArray(seo.data.value.faq) ? [...seo.data.value.faq] : [];
  list.push({ q: '', a: '' });
  seoUpdate('faq', list);
}
function seoUpdateFaq(idx, field, val) {
  const list = Array.isArray(seo.data.value.faq) ? [...seo.data.value.faq] : [];
  if (!list[idx]) return;
  list[idx] = { ...list[idx], [field]: val };
  seoUpdate('faq', list);
}
function seoRemoveFaq(idx) {
  const list = Array.isArray(seo.data.value.faq) ? [...seo.data.value.faq] : [];
  list.splice(idx, 1);
  seoUpdate('faq', list);
}
function seoPickOgImage() {
  openSingleImage(({ url }) => seoUpdate('og_image', url));
}
const seoTitleDisplay = computed(() => seo.data.value.title || (seo.defaults.value.post_title + ' · ' + seo.defaults.value.site_name));
const seoDescDisplay = computed(() => seo.data.value.description || '');
const seoUrlDisplay = computed(() => (seo.defaults.value.post_url || '').replace(/^https?:\/\//, ''));
const seoTitleLen = computed(() => (seo.data.value.title || '').length);
const seoDescLen = computed(() => (seo.data.value.description || '').length);
// Contatori: colori per superficie chiara (≥ 4,5:1), non i -400 pensati per il grafite.
const seoTitleCls = computed(() => {
  const n = seoTitleLen.value;
  if (n === 0) return 'psp-n psp-n--empty';
  if (n > 60) return 'psp-n psp-n--over';
  if (n >= 30) return 'psp-n psp-n--ok';
  return 'psp-n psp-n--short';
});
const seoDescCls = computed(() => {
  const n = seoDescLen.value;
  if (n === 0) return 'psp-n psp-n--empty';
  if (n > 160) return 'psp-n psp-n--over';
  if (n >= 120) return 'psp-n psp-n--ok';
  return 'psp-n psp-n--short';
});
const seoJsonldStatus = computed(() => {
  const v = (seo.data.value.extra_jsonld || '').trim().replace(/<\/?script[^>]*>/gi, '').trim();
  if (!v) return { ok: null, msg: '' };
  try { JSON.parse(v); return { ok: true, msg: t('JSON valido') }; }
  catch (e) { return { ok: false, msg: e.message }; }
});

function onBgUpdate(newBg) {
  // Serializza il bg PRIMA di toccare lo store: rimuove i proxy Vue reactive così
  // postMessage riesce a clonare (structured clone non supporta i proxy).
  // Senza questo step, `postMessage({ page_bg: newBg })` lancia DataCloneError
  // sui field array nested (es. gradient stops).
  let plainBg;
  try { plainBg = JSON.parse(JSON.stringify(newBg)); } catch (e) { plainBg = newBg; }

  if (!builderStore.currentTemplate) return;
  const prev = builderStore.currentTemplate.settings || {};
  builderStore.currentTemplate.settings = {
    ...prev,
    page_bg: plainBg,
  };
  builderStore.isDirty = true;

  // Triplo meccanismo per garantire l'aggiornamento del canvas:
  //  1. postMessage 'olo:set-page-bg' → l'iframe applica CSS html/body istantaneamente
  //  2. Chiamata diretta a scheduleFullRender → render REST completo (incluso style
  //     server-side, hover states, ecc.)
  //  3. CustomEvent come fallback se il bridge non ha ancora esposto le funzioni globali
  try {
    if (typeof window.__oloBridgePostToIframe === 'function') {
      window.__oloBridgePostToIframe('olo:set-page-bg', { page_bg: plainBg });
    }
    if (typeof window.__oloBridgeForceRerender === 'function') {
      window.__oloBridgeForceRerender();
    }
    window.dispatchEvent(new CustomEvent('olo:builder-force-rerender', { detail: { reason: 'page_bg' } }));
  } catch (e) { console.warn('[PageSettings] dispatch error', e); }
}
</script>

<style scoped>
/*
 * Colori del CHROME (non del cliente), per la superficie chiara dell'inspector e il
 * fondo #f9fafb dei gruppi: etichette #475569 (7,2:1), aiuti #64748b (4,5:1),
 * stati ok #047857 / attenzione #b45309 / errore #b91c1c (≥ 4,8:1); l'accento è
 * quello del chrome, --olo-ui-accent. Scritti a mano, non Tailwind arbitrario.
 */
.psp { color: #1e293b; }

.psp-group {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 0 8px 8px;
}
.psp-stack { display: flex; flex-direction: column; gap: 12px; }
.psp-field { display: flex; flex-direction: column; gap: 4px; }

/* Riga «etichetta a sinistra, controllo a destra», come i campi in linea dell'inspector */
.psp-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  min-height: 30px;
}
.psp-row > .psp-label { flex: 1 1 auto; min-width: 0; }
.psp-row > :not(.psp-label) { flex: 0 0 auto; }
.psp-row--fill > .psp-label {
  flex: 0 1 auto;
  max-width: 46%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.psp-row--fill > :not(.psp-label) { flex: 1 1 0; min-width: 0; }

.psp-label {
  margin: 0;
  font-size: 12px;
  font-weight: 500;
  line-height: 1.35;
  color: #475569;
}
label.psp-label { cursor: pointer; }
.psp-label--split {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 8px;
}
.psp-label code {
  padding: 0 3px;
  font-size: 11px;
  color: #1e293b;
  background: #eef2f6;
  border-radius: 3px;
}
.psp-help {
  margin: 0;
  font-size: 11px;
  line-height: 1.4;
  color: #64748b;
}
.psp-sep {
  border-top: 1px solid #e5e7eb;
  padding-top: 12px;
}

/* «Salvataggio… / Salvato» nella testata del gruppo SEO (che è maiuscola e spaziata):
   #475569 regge anche sul fondo della testata, #f1f5f9 chiusa e arancio tenue aperta */
.psp-status {
  margin-right: 6px;
  font-size: 10px;
  font-weight: 500;
  letter-spacing: 0;
  text-transform: none;
  color: #475569;
}
/* Non salvato / salvato in parte / non caricato: rosso del chrome, >= 5,2:1 sui fondi della testata */
.psp-status--err { color: #b91c1c; }

/* Come si salva il gruppo: la prima riga di ogni gruppo principale */
.psp-note {
  margin: 0;
  padding: 6px 8px;
  font-size: 11px;
  line-height: 1.4;
  color: #475569;
  background: #f8fafc;
  border-left: 2px solid var(--olo-ui-accent, #e8622a);
  border-radius: 0 6px 6px 0;
}

/* Contatori e stati SEO */
.psp-n { font-variant-numeric: tabular-nums; font-weight: 600; }
.psp-n--empty { color: #64748b; }
.psp-n--short { color: #b45309; }
.psp-n--ok, .psp-ok { color: #047857; }
.psp-n--over, .psp-err { color: #b91c1c; }

.psp-serp { border: 1px solid #e5e7eb; }
.psp-thumb {
  display: block;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
}

/* Pulsanti a contorno (Seleziona, Aggiungi FAQ, favicon) */
.psp-btn {
  flex-shrink: 0;
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 500;
  line-height: 1.2;
  color: #334155;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  cursor: pointer;
  transition: border-color 0.12s, color 0.12s;
}
.psp-btn:hover:not(:disabled) { border-color: var(--olo-ui-accent, #e8622a); color: #1e293b; }
.psp-btn:disabled { color: #64748b; background: #f8fafc; cursor: not-allowed; }
.psp-btn--full { width: 100%; }

/* Voce FAQ: card bianca sul fondo del gruppo */
.psp-card {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 8px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
}
.psp-card-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.psp-del {
  width: 22px;
  height: 22px;
  padding: 0;
  font-size: 16px;
  line-height: 1;
  color: #b91c1c;
  background: transparent;
  border: 0;
  border-radius: 4px;
  cursor: pointer;
}
.psp-del:hover { background: #fef2f2; }

/* Favicon: la × compare al passaggio E al focus da tastiera */
.psp-fav {
  position: relative;
  width: 32px;
  height: 32px;
}
.psp-fav-img {
  display: block;
  width: 32px;
  height: 32px;
  object-fit: contain;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
}
.psp-fav-del {
  position: absolute;
  top: -6px;
  right: -6px;
  width: 16px;
  height: 16px;
  padding: 0;
  font-size: 11px;
  line-height: 16px;
  color: #fff;
  background: #b91c1c;
  border: 0;
  border-radius: 999px;
  cursor: pointer;
  opacity: 0;
  transition: opacity 0.12s;
}
.psp-fav:hover .psp-fav-del,
.psp-fav-del:focus-visible { opacity: 1; }

.psp-btn:focus-visible,
.psp-del:focus-visible,
.psp-fav-del:focus-visible,
.psp-switch:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: 2px;
}
</style>
