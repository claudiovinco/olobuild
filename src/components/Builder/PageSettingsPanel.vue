<template>
  <div class="mb-space-y-4">
    <!-- Header -->
    <div class="mb-flex mb-items-center mb-justify-between">
      <h3 class="mb-text-sm mb-font-semibold mb-text-gray-200">{{ t('Impostazioni Pagina') }}</h3>
      <button
        @click="builderStore.togglePageSettings()"
        class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-lg"
      >
        {{ t('&times;') }}
      </button>
    </div>

    <!-- Single Post Type selector (only for single templates) -->
    <div v-if="isSingleTemplate">
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">{{ t('Post Type') }}</label>
      <FieldSelect
        ui="dropdown"
        :modelValue="pageSettings.single_post_type || ''"
        :options="postTypes"
        @update:modelValue="builderStore.updatePageSetting('single_post_type', $event)"
      />
    </div>

    <!-- Separator (single) -->
    <div v-if="isSingleTemplate" class="mb-border-t mb-border-gray-700"></div>

    <!-- Layout -->
    <div>
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">{{ t('Larghezza max contenuto') }}</label>
      <FieldSelect
        ui="dropdown"
        :modelValue="pageSettings.content_max_width"
        :options="contentWidthOptions"
        @update:modelValue="builderStore.updatePageSetting('content_max_width', $event)"
      />
    </div>

    <!-- Separator -->
    <div class="mb-border-t mb-border-gray-700"></div>

    <!-- Background -->
    <div>
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">{{ t('Sfondo pagina') }}</label>
      <BackgroundControls
        :modelValue="pageSettings.page_bg"
        :showParallax="true"
        @update:modelValue="onBgUpdate"
      />
    </div>

    <!-- Separator -->
    <div class="mb-border-t mb-border-gray-700"></div>

    <!-- Effetti di pagina -->
    <div class="mb-space-y-3">
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300">{{ t('Effetti di pagina') }}</label>
      <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
        <input type="checkbox" :checked="pageSettings.page_crt_enabled === true" @change="builderStore.updatePageSetting('page_crt_enabled', $event.target.checked)" class="mb-accent-primary-500" />
        <span class="mb-text-xs mb-text-gray-300">{{ t('Overlay CRT (scanline + vignetta)') }}</span>
      </label>
      <p class="mb-text-[10px] mb-text-gray-500 mb-leading-snug">{{ t('Decoratore a tutta pagina in stile schermo CRT. Statico con riduzione del movimento.') }}</p>
      <template v-if="pageSettings.page_crt_enabled">
        <div>
          <label class="mb-block mb-text-xs mb-text-gray-400 mb-mb-1">{{ t('Intensità scanline') }}: {{ pageSettings.page_crt_scanline_opacity ?? 50 }}%</label>
          <input type="range" min="0" max="100" step="5" :value="pageSettings.page_crt_scanline_opacity ?? 50" @input="builderStore.updatePageSetting('page_crt_scanline_opacity', parseInt($event.target.value))" class="mb-w-full mb-accent-primary-500" />
        </div>
        <div>
          <label class="mb-block mb-text-xs mb-text-gray-400 mb-mb-1">{{ t('Passo scanline (px)') }}: {{ pageSettings.page_crt_scanline_gap ?? 3 }}</label>
          <input type="range" min="2" max="12" step="1" :value="pageSettings.page_crt_scanline_gap ?? 3" @input="builderStore.updatePageSetting('page_crt_scanline_gap', parseInt($event.target.value))" class="mb-w-full mb-accent-primary-500" />
        </div>
        <div>
          <label class="mb-block mb-text-xs mb-text-gray-400 mb-mb-1">{{ t('Vignetta') }}: {{ pageSettings.page_crt_vignette ?? 55 }}%</label>
          <input type="range" min="0" max="100" step="5" :value="pageSettings.page_crt_vignette ?? 55" @input="builderStore.updatePageSetting('page_crt_vignette', parseInt($event.target.value))" class="mb-w-full mb-accent-primary-500" />
        </div>
        <div>
          <label class="mb-block mb-text-xs mb-text-gray-400 mb-mb-1">{{ t('Fusione') }}</label>
          <FieldSelect ui="dropdown" :modelValue="pageSettings.page_crt_blend_mode || 'overlay'" :options="crtBlendOptions" @update:modelValue="builderStore.updatePageSetting('page_crt_blend_mode', $event)" />
        </div>
        <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
          <input type="checkbox" :checked="pageSettings.page_crt_flicker === true" @change="builderStore.updatePageSetting('page_crt_flicker', $event.target.checked)" class="mb-accent-primary-500" />
          <span class="mb-text-xs mb-text-gray-300">{{ t('Sfarfallio animato') }}</span>
        </label>
      </template>

      <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
        <input type="checkbox" :checked="pageSettings.page_grain_enabled === true" @change="builderStore.updatePageSetting('page_grain_enabled', $event.target.checked)" class="mb-accent-primary-500" />
        <span class="mb-text-xs mb-text-gray-300">{{ t('Grana pellicola') }}</span>
      </label>
      <p class="mb-text-[10px] mb-text-gray-500 mb-leading-snug">{{ t('Rumore organico a tutta pagina, animato a scatti come una pellicola. Statico con riduzione del movimento.') }}</p>
      <template v-if="pageSettings.page_grain_enabled">
        <div>
          <label class="mb-block mb-text-xs mb-text-gray-400 mb-mb-1">{{ t('Intensità grana') }}: {{ pageSettings.page_grain_opacity ?? 7 }}%</label>
          <input type="range" min="1" max="30" step="1" :value="pageSettings.page_grain_opacity ?? 7" @input="builderStore.updatePageSetting('page_grain_opacity', parseInt($event.target.value))" class="mb-w-full mb-accent-primary-500" />
        </div>
        <div>
          <label class="mb-block mb-text-xs mb-text-gray-400 mb-mb-1">{{ t('Dimensione pattern (px)') }}: {{ pageSettings.page_grain_size ?? 240 }}</label>
          <input type="range" min="80" max="480" step="20" :value="pageSettings.page_grain_size ?? 240" @input="builderStore.updatePageSetting('page_grain_size', parseInt($event.target.value))" class="mb-w-full mb-accent-primary-500" />
        </div>
        <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
          <input type="checkbox" :checked="(pageSettings.page_grain_animate ?? true) === true" @change="builderStore.updatePageSetting('page_grain_animate', $event.target.checked)" class="mb-accent-primary-500" />
          <span class="mb-text-xs mb-text-gray-300">{{ t('Animazione a scatti') }}</span>
        </label>
        <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
          <input type="checkbox" :checked="pageSettings.page_grain_mobile === true" @change="builderStore.updatePageSetting('page_grain_mobile', $event.target.checked)" class="mb-accent-primary-500" />
          <span class="mb-text-xs mb-text-gray-300">{{ t('Mostra anche su touch/mobile') }}</span>
        </label>
        <p class="mb-text-[10px] mb-text-gray-500 mb-leading-snug">{{ t('Di default la grana è disattivata sui dispositivi touch: il layer in blend a tutto schermo può rendere lo scorrimento meno fluido.') }}</p>
      </template>
    </div>

    <!-- Separator -->
    <div class="mb-border-t mb-border-gray-700"></div>

    <!-- SEO Section: only when template is bound to a post -->
    <div v-if="seo.isReady.value">
      <div class="mb-flex mb-items-center mb-justify-between mb-mb-2">
        <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300">{{ t('SEO della pagina') }}</label>
        <span v-if="seo.saving.value" class="mb-text-[10px] mb-text-gray-500">{{ t('Salvataggio…') }}</span>
      </div>

      <!-- Tab nav -->
      <div class="mb-flex mb-gap-1 mb-mb-3 mb-text-[11px]">
        <button
          v-for="tab in [
            { id: 'seo', label: t('Base') },
            { id: 'social', label: t('Social') },
            { id: 'advanced', label: t('Robots') },
            { id: 'schema', label: t('Schema') },
            { id: 'faq', label: t('FAQ') },
          ]"
          :key="tab.id"
          @click="seoTab = tab.id"
          :class="['mb-px-2 mb-py-1 mb-rounded-md mb-transition-colors',
                   seoTab === tab.id ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-800 mb-text-gray-300 hover:mb-bg-gray-700']"
        >{{ tab.label }}</button>
      </div>

      <!-- TAB: Base (title + description + keyword + Google preview) -->
      <div v-if="seoTab === 'seo'" class="mb-space-y-3">
        <!-- Google preview -->
        <div class="mb-rounded-md mb-bg-white mb-text-gray-900 mb-px-3 mb-py-2 mb-text-[12px] mb-leading-snug">
          <div class="mb-text-[14px]" style="color:#1a0dab;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ seoTitleDisplay }}</div>
          <div style="color:#006621;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ seoUrlDisplay }}</div>
          <div style="color:#545454;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ seoDescDisplay || t('(Nessuna descrizione: imposta la Meta Description qui sotto.)') }}</div>
        </div>

        <!-- SEO Title -->
        <div>
          <label class="mb-flex mb-justify-between mb-text-[10px] mb-text-gray-400 mb-mb-1">
            <span>{{ t('SEO Title') }}</span>
            <span :class="seoTitleCls">{{ seoTitleLen }}/60</span>
          </label>
          <input type="text"
            :value="seo.data.value.title"
            @input="seoUpdate('title', $event.target.value)"
            :placeholder="seo.defaults.value.post_title + ' · ' + seo.defaults.value.site_name"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
        </div>

        <!-- Meta description -->
        <div>
          <label class="mb-flex mb-justify-between mb-text-[10px] mb-text-gray-400 mb-mb-1">
            <span>{{ t('Meta Description') }}</span>
            <span :class="seoDescCls">{{ seoDescLen }}/160</span>
          </label>
          <textarea rows="3"
            :value="seo.data.value.description"
            @input="seoUpdate('description', $event.target.value)"
            :placeholder="t('Descrivi questa pagina in 120-160 caratteri…')"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"></textarea>
        </div>

        <!-- Focus keyword -->
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Focus keyword') }}</label>
          <input type="text"
            :value="seo.data.value.focus_keyword"
            @input="seoUpdate('focus_keyword', $event.target.value)"
            :placeholder="t('Es. page builder wordpress')"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
        </div>
      </div>

      <!-- TAB: Social (OG + Twitter) -->
      <div v-else-if="seoTab === 'social'" class="mb-space-y-3">
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('OG Title (Facebook/LinkedIn)') }}</label>
          <input type="text"
            :value="seo.data.value.og_title"
            @input="seoUpdate('og_title', $event.target.value)"
            :placeholder="t('Usa SEO Title se vuoto')"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
        </div>
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('OG Description') }}</label>
          <textarea rows="2"
            :value="seo.data.value.og_description"
            @input="seoUpdate('og_description', $event.target.value)"
            :placeholder="t('Usa Meta Description se vuoto')"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"></textarea>
        </div>
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('OG Image (1200×630)') }}</label>
          <div class="mb-flex mb-gap-2 mb-items-center">
            <input type="text"
              :value="seo.data.value.og_image"
              @input="seoUpdate('og_image', $event.target.value)"
              placeholder="https://…"
              class="mb-flex-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
            <button @click="seoPickOgImage" class="mb-px-3 mb-py-1.5 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600">{{ t('Seleziona') }}</button>
          </div>
          <img v-if="seo.data.value.og_image" :src="seo.data.value.og_image" class="mb-mt-2 mb-max-h-24 mb-rounded mb-border mb-border-gray-700" />
        </div>
        <div class="mb-border-t mb-border-gray-700 mb-pt-3">
          <div class="mb-text-[11px] mb-text-gray-500 mb-mb-2">{{ t('Twitter / X (fallback OG se vuoto)') }}</div>
          <input type="text"
            :value="seo.data.value.tw_title"
            @input="seoUpdate('tw_title', $event.target.value)"
            :placeholder="t('Twitter Title')"
            class="mb-w-full mb-mb-2 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
          <textarea rows="2"
            :value="seo.data.value.tw_description"
            @input="seoUpdate('tw_description', $event.target.value)"
            :placeholder="t('Twitter Description')"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"></textarea>
        </div>
      </div>

      <!-- TAB: Robots (canonical + noindex + nofollow) -->
      <div v-else-if="seoTab === 'advanced'" class="mb-space-y-3">
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Canonical URL') }}</label>
          <input type="text"
            :value="seo.data.value.canonical"
            @input="seoUpdate('canonical', $event.target.value)"
            :placeholder="seo.defaults.value.post_url"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
          <p class="mb-text-[10px] mb-text-gray-500 mb-mt-1">{{ t('Lascia vuoto per usare l\'URL della pagina.') }}</p>
        </div>
        <label class="mb-flex mb-items-center mb-gap-2 mb-text-xs mb-text-gray-300 mb-cursor-pointer">
          <input type="checkbox" :checked="seo.data.value.noindex" @change="seoToggle('noindex')" class="mb-accent-primary-600" />
          <code class="mb-text-[11px]">noindex</code>
          <span class="mb-text-gray-500">— {{ t('non indicizzare') }}</span>
        </label>
        <label class="mb-flex mb-items-center mb-gap-2 mb-text-xs mb-text-gray-300 mb-cursor-pointer">
          <input type="checkbox" :checked="seo.data.value.nofollow" @change="seoToggle('nofollow')" class="mb-accent-primary-600" />
          <code class="mb-text-[11px]">nofollow</code>
          <span class="mb-text-gray-500">— {{ t('non seguire i link') }}</span>
        </label>
      </div>

      <!-- TAB: Schema (schema_type + extra_jsonld) -->
      <div v-else-if="seoTab === 'schema'" class="mb-space-y-3">
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Schema.org type (auto se vuoto)') }}</label>
          <FieldSelect
            ui="dropdown"
            :modelValue="seo.data.value.schema_type"
            :options="schemaTypeOptions"
            @update:modelValue="seoUpdate('schema_type', $event)"
          />
        </div>
        <div>
          <label class="mb-flex mb-justify-between mb-text-[10px] mb-text-gray-400 mb-mb-1">
            <span>{{ t('JSON-LD personalizzato') }}</span>
            <span v-if="seoJsonldStatus.ok === true" class="mb-text-green-400">✓ {{ seoJsonldStatus.msg }}</span>
            <span v-else-if="seoJsonldStatus.ok === false" class="mb-text-red-400">✗ {{ seoJsonldStatus.msg }}</span>
          </label>
          <textarea rows="10"
            :value="seo.data.value.extra_jsonld"
            @input="seoUpdate('extra_jsonld', $event.target.value)"
            :placeholder="'{ &quot;@context&quot;: &quot;https://schema.org&quot;, &quot;@graph&quot;: [ … ] }'"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-900 mb-font-mono"
            style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; tab-size: 2"></textarea>
          <p class="mb-text-[10px] mb-text-gray-500 mb-mt-1">{{ t('Iniettato come <script type=\"application/ld+json\"> nel <head>. Tag <script> facoltativi, vengono ripuliti server-side. JSON deve essere parsabile.') }}</p>
          <p v-if="seo.validationErrors.value.extra_jsonld" class="mb-text-[10px] mb-text-red-400 mb-mt-1">{{ seo.validationErrors.value.extra_jsonld }}</p>
        </div>
      </div>

      <!-- TAB: FAQ schema -->
      <div v-else-if="seoTab === 'faq'" class="mb-space-y-3">
        <p class="mb-text-[10px] mb-text-gray-500">{{ t('Coppie domanda/risposta — genera markup FAQPage (rich results Google).') }}</p>
        <div v-for="(item, idx) in seo.data.value.faq" :key="idx" class="mb-rounded-md mb-bg-gray-800 mb-border mb-border-gray-700 mb-p-2 mb-space-y-2 mb-relative">
          <button @click="seoRemoveFaq(idx)" class="mb-absolute mb-top-1 mb-right-2 mb-text-red-400 hover:mb-text-red-300 mb-text-base">×</button>
          <input type="text"
            :value="item.q"
            @input="seoUpdateFaq(idx, 'q', $event.target.value)"
            :placeholder="t('Domanda')"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900" />
          <textarea rows="2"
            :value="item.a"
            @input="seoUpdateFaq(idx, 'a', $event.target.value)"
            :placeholder="t('Risposta')"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"></textarea>
        </div>
        <button @click="seoAddFaq" class="mb-w-full mb-py-1.5 mb-px-3 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600">+ {{ t('Aggiungi FAQ') }}</button>
      </div>

      <p v-if="seo.lastError.value" class="mb-text-[10px] mb-text-red-400 mb-mt-2">{{ seo.lastError.value }}</p>
    </div>

    <!-- Separator (only if SEO section is shown) -->
    <div v-if="seo.isReady.value" class="mb-border-t mb-border-gray-700"></div>

    <!-- Favicon -->
    <div>
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">{{ t('Favicon') }}</label>
      <div v-if="faviconUrl" class="mb-relative mb-group mb-mb-2">
        <img :src="faviconUrl" class="mb-w-8 mb-h-8 mb-rounded mb-border mb-border-gray-600" />
        <button @click="removeFavicon" class="mb-absolute mb-top-0 mb-right-0 mb-bg-red-600 mb-text-white mb-rounded-full mb-w-4 mb-h-4 mb-text-[10px] mb-flex mb-items-center mb-justify-center mb-opacity-0 group-hover:mb-opacity-100">{{ t('&times;') }}</button>
      </div>
      <button @click="pickFavicon" class="mb-w-full mb-py-1.5 mb-px-3 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600">
        {{ faviconUrl ? 'Cambia favicon' : 'Seleziona favicon' }}
      </button>
      <p class="mb-text-[10px] mb-text-gray-500 mb-mt-1">{{ t('Imposta la favicon del sito (salvata in WordPress).') }}</p>
    </div>

    <!-- Separator -->
    <div class="mb-border-t mb-border-gray-700"></div>

    <!-- Scroll flash settings -->
    <div>
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-1">{{ t('Evidenziazione scroll') }}</label>
      <p class="mb-text-[10px] mb-text-gray-500 mb-mb-3">{{ t('Effetto visivo quando selezioni un tile dalla Struttura.') }}</p>

      <!-- Effect type -->
      <div class="mb-mb-3">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Tipo effetto') }}</label>
        <FieldSelect
          ui="dropdown"
          :modelValue="sf.effect"
          :options="scrollEffectOptions"
          @update:modelValue="updateSf('effect', $event)"
        />
      </div>

      <!-- Color -->
      <div class="mb-mb-3">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Colore') }}</label>
        <FieldColor :modelValue="sf.color" @update:modelValue="updateSf('color', $event)" />
      </div>

      <!-- Size -->
      <div class="mb-mb-3">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Dimensione effetto') }}</label>
        <div class="mb-flex mb-items-center mb-gap-2">
          <input type="range" :value="sf.size" @input="updateSf('size', parseInt($event.target.value))" min="2" max="20" step="1" class="mb-flex-1" />
          <span class="mb-text-[10px] mb-text-gray-400 mb-w-8 mb-text-right">{{ sf.size }}px</span>
        </div>
      </div>

      <!-- Duration -->
      <div class="mb-mb-3">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Durata effetto') }}</label>
        <div class="mb-flex mb-items-center mb-gap-2">
          <input type="range" :value="sf.duration" @input="updateSf('duration', parseInt($event.target.value))" min="300" max="3000" step="100" class="mb-flex-1" />
          <span class="mb-text-[10px] mb-text-gray-400 mb-w-12 mb-text-right">{{ sf.duration }}ms</span>
        </div>
      </div>

      <!-- Pulse count -->
      <div v-if="sf.effect === 'pulse'" class="mb-mb-3">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Ripetizioni pulse') }}</label>
        <div class="mb-flex mb-items-center mb-gap-2">
          <input type="range" :value="sf.pulse_count" @input="updateSf('pulse_count', parseInt($event.target.value))" min="1" max="6" step="1" class="mb-flex-1" />
          <span class="mb-text-[10px] mb-text-gray-400 mb-w-6 mb-text-right">{{ sf.pulse_count }}x</span>
        </div>
      </div>

      <!-- Scroll speed -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Velocit&agrave; scorrimento') }}</label>
        <div class="mb-flex mb-items-center mb-gap-2">
          <input type="range" :value="sf.scroll_ms" @input="updateSf('scroll_ms', parseInt($event.target.value))" min="0" max="1500" step="50" class="mb-flex-1" />
          <span class="mb-text-[10px] mb-text-gray-400 mb-w-12 mb-text-right">{{ sf.scroll_ms === 0 ? 'Istant.' : sf.scroll_ms + 'ms' }}</span>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed, reactive, ref } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import BackgroundControls from './BackgroundControls.vue';
import { loadScrollFlashPrefs, saveScrollFlashPrefs } from '@/utils/scrollFlashPrefs';
import { useMediaPicker } from '@/composables/useMediaPicker';
import { usePageSeo } from '@/composables/usePageSeo';
import FieldColor from './fields/FieldColor.vue';
import FieldSelect from './fields/FieldSelect.vue';

const builderStore = useBuilderStore();
const pageSettings = computed(() => builderStore.pageSettings);
const sf = reactive(loadScrollFlashPrefs());
const { openSingleImage } = useMediaPicker();

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
const scrollEffectOptions = [
  { value: 'flash', label: 'Flash (singolo)' },
  { value: 'pulse', label: 'Pulse (ripetuto)' },
];

function updateSf(key, value) {
  sf[key] = value;
  saveScrollFlashPrefs(sf);
}

// ── Favicon ──
const faviconUrl = ref('');

// Load current favicon on mount
(async () => {
  try {
    const res = await fetch('/wp-json/wp/v2/settings', { credentials: 'same-origin', headers: { 'X-WP-Nonce': window.oloData?.nonce || '' } });
    const data = await res.json();
    if (data.site_icon) {
      const mediaRes = await fetch('/wp-json/wp/v2/media/' + data.site_icon, { credentials: 'same-origin' });
      const media = await mediaRes.json();
      faviconUrl.value = media.source_url || '';
    }
  } catch (e) { /* ignore */ }
})();

function pickFavicon() {
  openSingleImage(({ url, id }) => {
    faviconUrl.value = url;
    // Save to WordPress via REST API
    fetch('/wp-json/wp/v2/settings', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': window.oloData?.nonce || '',
      },
      body: JSON.stringify({ site_icon: id }),
    }).catch(() => {});
  });
}

function removeFavicon() {
  faviconUrl.value = '';
  fetch('/wp-json/wp/v2/settings', {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': window.oloData?.nonce || '',
    },
    body: JSON.stringify({ site_icon: 0 }),
  }).catch(() => {});
}
const oloData = window.oloData || {};
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
const seoTitleCls = computed(() => {
  const n = seoTitleLen.value;
  if (n === 0) return 'mb-text-gray-500';
  if (n > 60) return 'mb-text-red-400';
  if (n >= 30) return 'mb-text-green-400';
  return 'mb-text-yellow-400';
});
const seoDescCls = computed(() => {
  const n = seoDescLen.value;
  if (n === 0) return 'mb-text-gray-500';
  if (n > 160) return 'mb-text-red-400';
  if (n >= 120) return 'mb-text-green-400';
  return 'mb-text-yellow-400';
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
  console.log('[PageSettings] page_bg changed:', plainBg);
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
