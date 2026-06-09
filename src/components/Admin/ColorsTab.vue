<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Palette') }} <em>{{ t('colori') }}</em></h1>
      <p>{{ t('Colori globali, preset, generatore di palette, scala neutri e modalità dark — tutto in un posto. Le modifiche si propagano a tutti i template, agli elementi del builder e agli stili dei post type.') }}</p>
    </div>
    <div class="head-actions">
      <button class="cfg-btn cfg-btn-secondary" @click="importFromCoolors">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3s7 8 7 13a7 7 0 0 1-14 0c0-5 7-13 7-13z"/></svg>
        {{ t('Importa da Coolors') }}
      </button>
      <button class="cfg-btn cfg-btn-secondary" @click="saveAsPreset">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
        {{ t('Salva come preset') }}
      </button>
    </div>
  </div>

  <!-- 1) PRESET -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 9 5-9 5-9-5 9-5z"/><path d="m3 17 9 5 9-5"/><path d="m3 12 9 5 9-5"/></svg>
      </div>
      <div>
        <h3>{{ t('Preset di stile') }}</h3>
        <p>{{ t('Un punto di partenza con un click. Applica la palette completa del preset ai ruoli qui sotto.') }}</p>
      </div>
      <div class="head-actions">
        <span class="cfg-pill">{{ presetList.length }} {{ t('preset') }}</span>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="preset-grid">
        <div
          v-for="p in presetList" :key="p.key"
          class="preset-card"
          :class="{ 'is-active': activePresetKey === p.key }"
          role="button" tabindex="0"
          @click="applyPreset(p)"
          @keydown.enter="applyPreset(p)"
        >
          <button v-if="p.custom" class="preset-del" type="button" :title="t('Elimina preset')" @click.stop="deletePreset(p.id)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
          <span class="preset-swatches">
            <span v-for="(c, i) in p.swatches" :key="i" class="preset-sw" :style="{ background: c }"></span>
          </span>
          <span class="preset-meta">
            <b>{{ p.name }}</b>
            <span v-if="p.custom" class="cfg-pill preset-tag">{{ t('Tuo') }}</span>
            <span v-if="activePresetKey === p.key" class="cfg-pill ok preset-active"><span class="dot"></span> {{ t('Attivo') }}</span>
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- 2) GENERATORE PALETTE -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3z"/><path d="M19 14l.8 2.4L22 17l-2.2.6L19 20l-.8-2.4L16 17l2.2-.6L19 14z"/></svg>
      </div>
      <div>
        <h3>{{ t('Genera palette') }}</h3>
        <p>{{ t('Parti da uno o due colori e ottieni una palette armonica con le regole della teoria del colore.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="gen-controls">
        <div class="gen-seeds">
          <div class="gen-seed">
            <span class="gen-seed-label">{{ t('Colore base') }}</span>
            <label class="brand-swatch sm" :style="{ background: seed1 }" :title="t('Scegli colore base')">
              <input type="color" class="swatch-native" :value="hexInput(seed1)" @input="onPickSeed('seed1', $event.target.value)" :aria-label="t('Scegli colore base')" />
            </label>
            <div class="cfg-input mono sm">
              <span class="prefix">#</span>
              <input type="text" :value="seed1.replace(/^#/, '').toUpperCase()" @input="onSeedInput('seed1', $event.target.value)" maxlength="6" spellcheck="false" />
            </div>
          </div>
          <div v-if="ruleNeedsTwo" class="gen-seed">
            <span class="gen-seed-label">{{ t('Secondo colore') }}</span>
            <label class="brand-swatch sm" :style="{ background: seed2 }" :title="t('Scegli secondo colore')">
              <input type="color" class="swatch-native" :value="hexInput(seed2)" @input="onPickSeed('seed2', $event.target.value)" :aria-label="t('Scegli secondo colore')" />
            </label>
            <div class="cfg-input mono sm">
              <span class="prefix">#</span>
              <input type="text" :value="seed2.replace(/^#/, '').toUpperCase()" @input="onSeedInput('seed2', $event.target.value)" maxlength="6" spellcheck="false" />
            </div>
          </div>
        </div>
        <div class="gen-rules">
          <button
            v-for="r in HARMONY_RULES" :key="r.id"
            class="gen-rule"
            :class="{ 'is-on': rule === r.id }"
            :title="r.desc"
            @click="rule = r.id"
          >{{ t(r.label) }}</button>
        </div>
        <label class="gen-neutral">
          <button class="cfg-switch" :class="{ 'is-on': genNeutrals }" @click="genNeutrals = !genNeutrals" role="switch" type="button"></button>
          <span>{{ t('Coordina anche i neutri (testo, sfondo, superfici, bordi) con la tinta base') }}</span>
        </label>
      </div>
      <div class="gen-preview">
        <div class="gen-swatches">
          <div v-for="(c, i) in generated" :key="i" class="gen-sw" :style="{ background: c }">
            <span class="gen-sw-role">{{ t(generatedRoles[i]) }}</span>
            <span class="gen-sw-hex">{{ c }}</span>
          </div>
          <template v-if="genNeutrals">
            <div v-for="(c, k) in generatedNeutrals" :key="'n-' + k" class="gen-sw is-neutral" :style="{ background: c }">
              <span class="gen-sw-role">{{ neutralLabel(k) }}</span>
              <span class="gen-sw-hex">{{ c }}</span>
            </div>
          </template>
        </div>
        <button class="cfg-btn cfg-btn-primary" @click="applyGenerated">
          {{ t('Applica ai ruoli') }}
        </button>
      </div>
    </div>
  </div>

  <!-- 3) COLORI DEL BRAND -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r="1.5"/><circle cx="17.5" cy="10.5" r="1.5"/><circle cx="8.5" cy="7.5" r="1.5"/><circle cx="6.5" cy="12.5" r="1.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.5-.5 1.5-1.5 0-.4-.2-.8-.5-1.2a1.5 1.5 0 0 1 1.2-2.3h2C18.5 17 20.5 15 20.5 12.4 20.5 6.5 16.7 2 12 2z"/></svg>
      </div>
      <div>
        <h3>{{ t('Colori del brand') }}</h3>
        <p>{{ t('Ogni colore ha un ruolo. Cambialo e ovunque sul sito si aggiorna di conseguenza.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="brand-list">
        <div v-for="r in BRAND_ROLES" :key="r.key" class="brand-row">
          <label class="brand-swatch" :style="{ background: colors[r.key] || '#000' }" :title="t('Modifica colore ') + r.name">
            <input type="color" class="swatch-native" :value="hexInput(colors[r.key])" @input="onPickRole(r.key, $event.target.value)" :aria-label="t('Modifica colore ') + r.name" />
          </label>
          <div class="brand-info">
            <div class="brand-name">{{ t(r.name) }}</div>
            <div class="brand-role">{{ t(r.role) }}</div>
          </div>
          <div class="cfg-input mono">
            <span class="prefix">#</span>
            <input type="text" :value="(colors[r.key] || '').replace(/^#/, '').toUpperCase()" @input="updateHex(r.key, $event.target.value)" maxlength="6" spellcheck="false" />
          </div>
          <span class="contrast-badge" :class="contrastClass(r.key)" :title="t('Contrasto del testo sul colore')">{{ contrastLabel(r.key) }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- 4) NEUTRI & SUPERFICI (ruoli semantici) -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
      </div>
      <div>
        <h3>{{ t('Neutri & superfici') }}</h3>
        <p>{{ t('Testo, sfondi, sezioni muted e bordi. La base neutra di tutto il sito.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="brand-list">
        <div v-for="r in NEUTRAL_ROLES" :key="r.key" class="brand-row">
          <label class="brand-swatch" :style="{ background: colors[r.key] || '#fff' }" :title="t('Modifica colore ') + r.name">
            <input type="color" class="swatch-native" :value="hexInput(colors[r.key])" @input="onPickRole(r.key, $event.target.value)" :aria-label="t('Modifica colore ') + r.name" />
          </label>
          <div class="brand-info">
            <div class="brand-name">{{ t(r.name) }}</div>
            <div class="brand-role">{{ t(r.role) }}</div>
          </div>
          <div class="cfg-input mono">
            <span class="prefix">#</span>
            <input type="text" :value="(colors[r.key] || '').replace(/^#/, '').toUpperCase()" @input="updateHex(r.key, $event.target.value)" maxlength="6" spellcheck="false" />
          </div>
          <span></span>
        </div>
      </div>
    </div>
  </div>

  <!-- 4b) COLORI GLOBALI EXTRA (olo_global_colors non-core) -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r="1.5"/><circle cx="17.5" cy="10.5" r="1.5"/><circle cx="8.5" cy="7.5" r="1.5"/><circle cx="6.5" cy="12.5" r="1.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.5-.5 1.5-1.5 0-.4-.2-.8-.5-1.2a1.5 1.5 0 0 1 1.2-2.3h2C18.5 17 20.5 15 20.5 12.4 20.5 6.5 16.7 2 12 2z"/></svg>
      </div>
      <div>
        <h3>{{ t('Colori globali extra') }}</h3>
        <p>{{ t('Swatch riutilizzabili oltre ai ruoli (accenti, colori secondari del brand). Disponibili come token --olo-color-{id} e nel selettore colore delle tile.') }}</p>
      </div>
      <div class="head-actions">
        <button class="cfg-btn cfg-btn-secondary" @click="addExtraGlobal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
          {{ t('Aggiungi') }}
        </button>
      </div>
    </div>
    <div class="cfg-card-body">
      <div v-if="extraGlobals.length" class="brand-list">
        <div v-for="g in extraGlobals" :key="g.id" class="brand-row">
          <label class="brand-swatch" :style="{ background: g.value }" :title="t('Modifica colore')">
            <input type="color" class="swatch-native" :value="hexInput(g.value)" @change="setExtraGlobalHex(g.id, $event.target.value)" :aria-label="t('Modifica colore')" />
          </label>
          <div class="brand-info">
            <input class="xg-label" :value="g.label" @change="setExtraGlobalLabel(g.id, $event.target.value)" :placeholder="t('Nome colore')" spellcheck="false" />
            <div class="brand-role">var(--olo-color-{{ g.id }})</div>
          </div>
          <div class="cfg-input mono">
            <span class="prefix">#</span>
            <input type="text" :value="(g.value || '').replace(/^#/, '').toUpperCase()" @change="setExtraGlobalHex(g.id, $event.target.value)" maxlength="6" spellcheck="false" />
          </div>
          <button class="cfg-btn-icon cfg-btn-ghost" :title="t('Elimina')" @click="removeExtraGlobal(g.id)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </div>
      </div>
      <div v-else class="xg-empty">{{ t('Nessun colore globale extra. Aggiungine uno, oppure generane con la regola Triade/Tetrade qui sopra.') }}</div>
    </div>
  </div>

  <!-- 5) SCALA NEUTRI (7 livelli, neutrals.*) -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 9 5-9 5-9-5 9-5z"/><path d="m3 17 9 5 9-5"/><path d="m3 12 9 5 9-5"/></svg>
      </div>
      <div>
        <h3>{{ t('Scala neutri') }}</h3>
        <p>{{ t('Sfumature grigie utilizzate per testi, bordi, sfondi e stati disabilitati.') }}</p>
      </div>
      <div class="head-actions">
        <div class="cfg-segment">
          <button :class="{ 'is-on': neutrals.mode === 'auto' }"   @click="setNeutralMode('auto')">{{ t('Auto') }}</button>
          <button :class="{ 'is-on': neutrals.mode === 'manual' }" @click="setNeutralMode('manual')">{{ t('Manuale') }}</button>
        </div>
      </div>
    </div>
    <div v-if="neutrals.mode === 'auto'" class="cfg-card-body tight">
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Tinta neutri') }}</label>
          <div class="hint">{{ t('Scegli la sfumatura di base. I 7 livelli vengono generati automaticamente.') }}</div>
        </div>
        <div class="control-col">
          <div class="tint-chips">
            <button
              v-for="opt in tintOptions" :key="opt.id"
              class="tint-chip"
              :class="{ 'is-on': neutrals.tint === opt.id }"
              :title="opt.label"
              @click="setNeutralTint(opt.id)"
            >
              <span class="tint-dot" :style="{ background: NEUTRAL_PRESETS[opt.id][4] }"></span>
              {{ opt.label }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="neutral-scale">
        <div v-for="(c, i) in displayNeutrals" :key="i" class="neutral-col">
          <label
            class="neutral-swatch"
            :class="{ 'is-locked': neutrals.mode === 'auto' }"
            :style="{ background: c }"
            :title="neutrals.mode === 'manual' ? t('Clicca per modificare') : t('Passa a Manuale per modificare')"
          >
            <input type="color" class="swatch-native" :value="hexInput(c)" :disabled="neutrals.mode === 'auto'" @input="onPickNeutral(i, $event.target.value)" :aria-label="t('Modifica neutro')" />
          </label>
          <div class="neutral-label">{{ i === 0 ? 50 : i * 100 }}</div>
        </div>
      </div>
    </div>
  </div>

  <!-- 6) MODALITÀ DARK (dark_mode.*) -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
      </div>
      <div>
        <h3>{{ t('Modalità dark') }}</h3>
        <p>{{ t('Configura come la palette si adatta automaticamente al tema scuro.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Abilita modalità dark') }}</label>
          <div class="hint">{{ t('Mostra il selettore dark/light nell\'header del sito.') }}</div>
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': darkMode.enabled }" @click="setDark('enabled', !darkMode.enabled)" role="switch"></button>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Strategia di inversione') }}</label>
          <div class="hint">{{ t('Come generare i colori scuri.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-select">
            <select :value="darkMode.strategy" @change="setDark('strategy', $event.target.value)">
              <option value="auto">{{ t('Automatica (consigliata)') }}</option>
              <option value="manual">{{ t('Manuale, palette separata') }}</option>
              <option value="luminance">{{ t('Solo aggiusta luminanza') }}</option>
            </select>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import { HARMONY_RULES, harmonize, paletteToRoles, neutralsFromSeed, readableText, contrastRatio, isValidHex } from '@/utils/colorHarmony';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const BRAND_ROLES = [
  { key: 'primary',   name: 'Primary',   role: 'Brand · CTA · pulsanti' },
  { key: 'secondary', name: 'Secondary', role: 'Accenti · titoli' },
  { key: 'success',   name: 'Success',   role: 'Stato positivo' },
  { key: 'warning',   name: 'Warning',   role: 'Stato attenzione' },
  { key: 'danger',    name: 'Danger',    role: 'Stato errore' },
  { key: 'link',      name: 'Link',      role: 'Collegamenti' },
];
const NEUTRAL_ROLES = [
  { key: 'text',       name: 'Testo',          role: 'Testo principale' },
  { key: 'text_muted', name: 'Testo soft',     role: 'Testo secondario' },
  { key: 'background', name: 'Sfondo',         role: 'Superficie base' },
  { key: 'muted',      name: 'Superficie alt', role: 'Sezioni muted' },
  { key: 'border',     name: 'Bordo',          role: 'Linee e divisori' },
];
const DEFAULT_COLORS = {
  primary: '#E1474F', primary_contrast: '#FFFFFF', secondary: '#16263D', secondary_contrast: '#FFFFFF',
  muted: '#F3F4F6', muted_contrast: '#374151', success: '#10B981', warning: '#F59E0B', danger: '#EF4444',
  text: '#374151', text_muted: '#9CA3AF', background: '#FFFFFF', border: '#E5E7EB', link: '#E1474F',
};
const NEUTRAL_PRESETS = {
  slate:   ['#F8FAFC', '#F1F5F9', '#E2E8F0', '#94A3B8', '#475569', '#1E293B', '#0F172A'],
  gray:    ['#F9FAFB', '#F3F4F6', '#E5E7EB', '#9CA3AF', '#4B5563', '#1F2937', '#111827'],
  zinc:    ['#FAFAFA', '#F4F4F5', '#E4E4E7', '#A1A1AA', '#52525B', '#27272A', '#09090B'],
  neutral: ['#FAFAFA', '#F5F5F5', '#E5E5E5', '#A3A3A3', '#525252', '#262626', '#0A0A0A'],
  stone:   ['#FAFAF9', '#F5F5F4', '#E7E5E4', '#A8A29E', '#57534E', '#292524', '#0C0A09'],
};
const tintOptions = [
  { id: 'slate', label: 'Slate' }, { id: 'gray', label: 'Gray' }, { id: 'zinc', label: 'Zinc' },
  { id: 'neutral', label: 'Neutral' }, { id: 'stone', label: 'Stone' },
];

const fullStyles = ref({});            // tutto olo_styles (per non perdere typography/layout al save)
const colors = ref({ ...DEFAULT_COLORS });
const presets = ref({});               // builtin presets {key: {name, colors, ...}}
const customPresets = ref([]);         // olo_design_presets: preset salvati dall'utente
const globalColors = ref([]);          // olo_global_colors: per i ruoli core VINCE nel CSS → va sincronizzato
const globalColorsTouched = ref(false);// true quando il generatore aggiunge/modifica accent → forza il PUT
const neutrals = ref({ mode: 'auto', tint: 'zinc', scale: [...NEUTRAL_PRESETS.zinc] });
const darkMode = ref({ enabled: true, strategy: 'auto' });

// Generatore
const seed1 = ref('#E1474F');
const seed2 = ref('#16263D');
const rule  = ref('complementary');
const genNeutrals = ref(false);
const ruleNeedsTwo = computed(() => (HARMONY_RULES.find((r) => r.id === rule.value)?.seeds || 1) >= 2);
const generated = computed(() => harmonize(seed1.value, rule.value, seed2.value));
const generatedNeutrals = computed(() => neutralsFromSeed(seed1.value));
// Etichetta ogni swatch generata col ruolo a cui finirà davvero (calcolato dal ruolo,
// non dall'indice: per alcune regole il seed non è il primo colore della lista).
const generatedRoles = computed(() => {
  const seedU = seed1.value.toUpperCase();
  const roles = paletteToRoles(seed1.value, rule.value, seed2.value);
  const secU = (roles.secondary || '').toUpperCase();
  const accents = ['Accent', 'Accent 2', 'Accent 3'];
  let ai = 0;
  return generated.value.map((c) => {
    const u = c.toUpperCase();
    if (u === seedU) return 'Primary';
    if (u === secU) return 'Secondary';
    return accents[ai++] || 'Accent';
  });
});
const NEUTRAL_LABELS = { background: 'Sfondo', muted: 'Superficie', border: 'Bordo', text_muted: 'Testo soft', text: 'Testo' };
function neutralLabel(k) { return t(NEUTRAL_LABELS[k] || k); }

const displayNeutrals = computed(() =>
  neutrals.value.mode === 'auto'
    ? (NEUTRAL_PRESETS[neutrals.value.tint] || NEUTRAL_PRESETS.zinc)
    : (neutrals.value.scale && neutrals.value.scale.length ? neutrals.value.scale : NEUTRAL_PRESETS.zinc));

const presetList = computed(() => {
  const builtin = Object.entries(presets.value).map(([key, p]) => {
    const c = p.colors || {};
    return { key, name: p.name || key, colors: c, swatches: [c.primary, c.secondary, c.success, c.warning].filter(Boolean), custom: false };
  });
  const mine = (customPresets.value || []).map((p) => {
    const c = (p.style && p.style.colors) || {};
    return { key: p.id, id: p.id, name: p.name || 'Preset', colors: c, swatches: [c.primary, c.secondary, c.success, c.warning].filter(Boolean), custom: true };
  });
  return [...builtin, ...mine];
});

// Id riservati ai ruoli del pannello: i restanti global sono "extra" riutilizzabili.
const CORE_IDS = ['primary', 'secondary', 'success', 'warning', 'danger', 'link', 'text', 'text_muted', 'background', 'muted', 'border', 'primary_contrast', 'secondary_contrast', 'muted_contrast'];
const extraGlobals = computed(() => (globalColors.value || []).filter((g) => g && g.id && !CORE_IDS.includes(g.id)));

// Badge "Attivo" REALE: il preset i cui primary+secondary combaciano con i colori correnti.
const activePresetKey = computed(() => {
  const cp = (colors.value.primary || '').toUpperCase();
  const cs = (colors.value.secondary || '').toUpperCase();
  const hit = presetList.value.find((p) =>
    (p.colors.primary || '').toUpperCase() === cp && (p.colors.secondary || '').toUpperCase() === cs);
  return hit ? hit.key : '';
});

// ── Contrasto brand ──
function contrastLabel(key) {
  const bg = colors.value[key]; if (!bg) return '';
  const ratio = contrastRatio(bg, readableText(bg));
  return ratio >= 7 ? 'AAA' : ratio >= 4.5 ? 'AA' : 'AA-';
}
function contrastClass(key) {
  const bg = colors.value[key]; if (!bg) return '';
  return contrastRatio(bg, readableText(bg)) >= 4.5 ? 'ok' : 'warn';
}

// ── Edit colori ──
function recomputeContrasts() {
  colors.value.primary_contrast   = readableText(colors.value.primary || '#000');
  colors.value.secondary_contrast = readableText(colors.value.secondary || '#000');
  colors.value.muted_contrast     = readableText(colors.value.muted || '#fff');
}
function updateHex(key, val) {
  const clean = String(val).replace(/[^0-9a-fA-F]/g, '').slice(0, 6);
  colors.value[key] = '#' + clean.toUpperCase();
  if (clean.length === 6) recomputeContrasts();
  setDirty(true);
}
// Color picker: usiamo un <input type="color"> REALE trasparente sovrapposto allo swatch
// (classe .swatch-native nel template). È l'utente a cliccarlo, quindi Chrome ancora il
// picker nativo esattamente allo swatch. NIENTE input nascosto + .click() programmatico:
// in quel caso Chrome ignora la posizione e apre il picker in alto a sinistra.
function hexInput(v) {
  let s = String(v || '').trim();
  if (s && s[0] !== '#') s = '#' + s;
  if (/^#[0-9a-fA-F]{3}$/.test(s)) s = '#' + s.slice(1).split('').map((c) => c + c).join('');
  return /^#[0-9a-fA-F]{6}$/.test(s) ? s : '#000000';
}
function onPickRole(key, val) {
  colors.value[key] = String(val).toUpperCase();
  recomputeContrasts();
  setDirty(true);
}
function onPickSeed(which, val) {
  if (which === 'seed1') seed1.value = String(val).toUpperCase();
  else seed2.value = String(val).toUpperCase();
}
function onPickNeutral(i, val) {
  if (neutrals.value.mode !== 'manual') return;
  const next = [...neutrals.value.scale];
  next[i] = String(val).toUpperCase();
  neutrals.value.scale = next;
  setDirty(true);
}

// ── Generatore ──
function onSeedInput(which, val) {
  const clean = String(val).replace(/[^0-9a-fA-F]/g, '').slice(0, 6);
  const hex = '#' + clean.toUpperCase();
  if (which === 'seed1') seed1.value = hex; else seed2.value = hex;
}
// Aggiunge/aggiorna un global color riutilizzabile (token --olo-color-{id}).
function setGlobalAccent(id, hex, label) {
  if (!hex) return;
  const existing = globalColors.value.find((g) => g && g.id === id);
  if (existing) { existing.value = hex; } else { globalColors.value.push({ id, label, value: hex }); }
  globalColorsTouched.value = true;
}

function applyGenerated() {
  const harmony = harmonize(seed1.value, rule.value, seed2.value);
  const roles = paletteToRoles(seed1.value, rule.value, seed2.value);
  Object.assign(colors.value, roles);
  if (genNeutrals.value) {
    Object.assign(colors.value, neutralsFromSeed(seed1.value));
  }
  // I colori armonici oltre primary+secondary diventano accenti globali riutilizzabili
  // (--olo-color-accent / --olo-color-accent-2), così la tetrade/triade non spreca colori.
  const usedP = (roles.primary || '').toUpperCase();
  const usedS = (roles.secondary || '').toUpperCase();
  const extras = harmony.filter((c) => { const u = c.toUpperCase(); return u !== usedP && u !== usedS; });
  if (extras[0]) setGlobalAccent('accent', extras[0], t('Accento'));
  if (extras[1]) setGlobalAccent('accent-2', extras[1], t('Accento 2'));
  recomputeContrasts();
  setDirty(true);
  const nAcc = [extras[0], extras[1]].filter(Boolean).length;
  showToast(nAcc ? t('Applicati: Primary, Secondary e ') + nAcc + t(' accenti globali') : t('Palette generata applicata ai ruoli brand'), 'success');
}

// ── Scala neutri ──
function setNeutralMode(m) {
  if (m === 'manual' && neutrals.value.mode === 'auto') {
    // congela la tinta corrente come base editabile
    neutrals.value.scale = [...(NEUTRAL_PRESETS[neutrals.value.tint] || NEUTRAL_PRESETS.zinc)];
  }
  neutrals.value.mode = m;
  setDirty(true);
}
function setNeutralTint(id) {
  neutrals.value.tint = id;
  neutrals.value.scale = [...(NEUTRAL_PRESETS[id] || NEUTRAL_PRESETS.zinc)];
  setDirty(true);
}
// ── Modalità dark ──
function setDark(k, v) { darkMode.value = { ...darkMode.value, [k]: v }; setDirty(true); }

// ── Preset ──
function applyPreset(p) {
  if (!p.colors) return;
  colors.value = { ...colors.value, ...p.colors };
  recomputeContrasts();
  if (p.colors.primary) seed1.value = p.colors.primary.toUpperCase();
  if (p.colors.secondary) seed2.value = p.colors.secondary.toUpperCase();
  setDirty(true);
  showToast(t('Preset applicato: ') + p.name, 'success');
}
async function saveAsPreset() {
  const name = prompt(t('Nome del preset da salvare:'));
  if (!name) return;
  try {
    const res = await fetch(`${window.oloData.restUrl}design-presets`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ name, style: { colors: { ...colors.value } } }),
    });
    if (!res.ok) throw new Error();
    const np = await res.json();
    if (np && np.id) customPresets.value.push(np);
    showToast(t('Preset salvato'), 'success');
  } catch (e) { showToast(t('Errore nel salvataggio del preset'), 'error'); }
}

async function deletePreset(id) {
  if (!confirm(t('Eliminare questo preset?'))) return;
  try {
    const res = await fetch(`${window.oloData.restUrl}design-presets/${id}`, {
      method: 'DELETE',
      headers: { 'X-WP-Nonce': window.oloData.nonce },
    });
    if (!res.ok) throw new Error();
    customPresets.value = customPresets.value.filter((p) => p.id !== id);
    showToast(t('Preset eliminato'), 'success');
  } catch (e) { showToast(t('Errore eliminazione preset'), 'error'); }
}

function importFromCoolors() {
  const url = prompt(t('Incolla l\'URL Coolors (es. https://coolors.co/...)'));
  if (!url) return;
  const hexes = (url.match(/[0-9a-fA-F]{6}/g) || []).slice(0, 5);
  if (hexes.length < 2) { showToast(t('URL Coolors non valido'), 'error'); return; }
  const keys = ['primary', 'secondary', 'background', 'text', 'muted'];
  hexes.forEach((h, i) => { if (keys[i]) colors.value[keys[i]] = '#' + h.toUpperCase(); });
  recomputeContrasts();
  if (hexes[0]) seed1.value = '#' + hexes[0].toUpperCase();
  setDirty(true);
  showToast(t('Palette importata da Coolors'), 'success');
}

// ── Load / Save (endpoint REALE /styles) ──
async function loadStyles() {
  try {
    const res = await fetch(`${window.oloData.restUrl}styles`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      const s = data.styles || {};
      fullStyles.value = s;
      colors.value = { ...DEFAULT_COLORS, ...(s.colors || {}) };
      if (s.neutrals) neutrals.value = { mode: s.neutrals.mode || 'auto', tint: s.neutrals.tint || 'zinc', scale: (s.neutrals.scale && s.neutrals.scale.length) ? [...s.neutrals.scale] : [...NEUTRAL_PRESETS[s.neutrals.tint || 'zinc']] };
      if (s.dark_mode) darkMode.value = { enabled: s.dark_mode.enabled !== false, strategy: s.dark_mode.strategy || 'auto' };
    }
  } catch (e) { /* defaults */ }
  // Global colors: per i ruoli core (primary/secondary/...) il global color VINCE nel CSS
  // (è emesso dopo in generate_css). Il pannello deve quindi MOSTRARE quel valore effettivo
  // e ri-sincronizzarlo al save, altrimenti il cambio sembra non applicarsi al frontend.
  try {
    const rg = await fetch(`${window.oloData.restUrl}global-colors`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (rg.ok) {
      const g = await rg.json();
      globalColors.value = Array.isArray(g) ? g : [];
      globalColors.value.forEach((gc) => {
        if (gc && gc.id && gc.value && colors.value[gc.id] !== undefined) {
          colors.value[gc.id] = gc.value.toUpperCase ? gc.value.toUpperCase() : gc.value;
        }
      });
    }
  } catch (e) { /* no global colors */ }
  if (colors.value.primary) seed1.value = colors.value.primary.toUpperCase();
  if (colors.value.secondary) seed2.value = colors.value.secondary.toUpperCase();
  try {
    const r2 = await fetch(`${window.oloData.restUrl}design-presets/builtin`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (r2.ok) presets.value = await r2.json();
  } catch (e) { /* no presets */ }
  try {
    const r3 = await fetch(`${window.oloData.restUrl}design-presets`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (r3.ok) { const cp = await r3.json(); customPresets.value = Array.isArray(cp) ? cp : []; }
  } catch (e) { /* no custom presets */ }
}

// I global color con id = ruolo core (primary/secondary/...) vincono nel CSS frontend.
// Li riallineiamo ai valori correnti del pannello così il cambio è davvero visibile.
async function syncGlobalColors() {
  // PUT /global-colors fa REPLACE totale dell'array: rileggo lo stato corrente dal
  // server per non cancellare colori aggiunti altrove (es. il "+" del builder) con
  // uno snapshot stale.
  let server = globalColors.value;
  try {
    const r = await fetch(`${window.oloData.restUrl}global-colors`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (r.ok) { const s = await r.json(); if (Array.isArray(s)) server = s; }
  } catch (e) { /* fallback allo stato locale */ }
  let changed = globalColorsTouched.value;
  // 1) allinea i ruoli core ai valori correnti del pannello
  const next = server.map((gc) => {
    if (gc && gc.id && colors.value[gc.id] !== undefined && gc.value !== colors.value[gc.id]) {
      changed = true;
      return { ...gc, value: colors.value[gc.id] };
    }
    return gc;
  });
  // 2) aggiungi gli accent del generatore non ancora presenti sul server
  for (const local of globalColors.value) {
    if (local && local.id && !next.some((g) => g.id === local.id)) {
      next.push(local);
      changed = true;
    }
  }
  globalColors.value = next;
  if (!changed) return;
  globalColorsTouched.value = false;
  await fetch(`${window.oloData.restUrl}global-colors`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
    body: JSON.stringify(next),
  });
}

// I colori globali EXTRA si salvano subito e in modo merge-safe (rileggo dal server,
// applico la mutazione, riscrivo): così add/edit/delete non si pestano col builder.
async function persistGlobalColors(mutator) {
  let list = Array.isArray(globalColors.value) ? globalColors.value : [];
  try {
    const r = await fetch(`${window.oloData.restUrl}global-colors`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (r.ok) { const s = await r.json(); if (Array.isArray(s)) list = s; }
  } catch (e) { /* usa stato locale */ }
  const next = mutator(list.map((x) => ({ ...x })));
  globalColors.value = next;
  try {
    const res = await fetch(`${window.oloData.restUrl}global-colors`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(next),
    });
    if (!res.ok) throw new Error();
  } catch (e) { showToast(t('Errore salvataggio colori globali'), 'error'); }
}
function addExtraGlobal() {
  const id = 'c' + Date.now().toString(36);
  persistGlobalColors((list) => [...list, { id, label: t('Nuovo colore'), value: '#888888' }]);
}
function removeExtraGlobal(id) {
  if (!confirm(t('Eliminare questo colore globale?'))) return;
  persistGlobalColors((list) => list.filter((g) => g.id !== id));
}
function setExtraGlobalHex(id, val) {
  const clean = '#' + String(val).replace(/[^0-9a-fA-F]/g, '').slice(0, 6).toUpperCase();
  persistGlobalColors((list) => list.map((g) => (g.id === id ? { ...g, value: clean } : g)));
}
function setExtraGlobalLabel(id, label) {
  persistGlobalColors((list) => list.map((g) => (g.id === id ? { ...g, label } : g)));
}
async function saveStyles() {
  recomputeContrasts();
  try {
    const body = {
      ...fullStyles.value,
      colors: { ...colors.value },
      neutrals: { mode: neutrals.value.mode, tint: neutrals.value.tint, scale: [...displayNeutrals.value] },
      dark_mode: { enabled: darkMode.value.enabled, strategy: darkMode.value.strategy },
    };
    const res = await fetch(`${window.oloData.restUrl}styles`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(body),
    });
    if (!res.ok) throw new Error();
    fullStyles.value = body;
    await syncGlobalColors();
  } catch (e) { showToast(t('Errore di salvataggio colori'), 'error'); }
}

const onSave = () => saveStyles();
const onDiscard = () => loadStyles();

onMounted(() => {
  loadStyles();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>

<style scoped>
.brand-list { display: grid; gap: 10px; }
.brand-row {
  display: grid;
  grid-template-columns: 56px 1fr 200px 56px;
  gap: 14px; align-items: center;
  padding: 10px 12px;
  background: #fff;
  border: 1px solid var(--c-line-soft);
  border-radius: 10px;
}
.brand-swatch {
  position: relative; display: block; overflow: hidden;
  width: 56px; height: 56px; border-radius: 8px;
  box-shadow: inset 0 0 0 1px rgba(0,0,0,.06);
  border: 0; cursor: pointer; padding: 0;
  transition: transform .12s;
}
.swatch-native {
  position: absolute; inset: 0; width: 100%; height: 100%;
  margin: 0; padding: 0; border: 0; background: none;
  opacity: 0; cursor: pointer; -webkit-appearance: none; appearance: none;
}
.swatch-native:disabled { cursor: not-allowed; }
.brand-swatch.sm { width: 38px; height: 38px; }
.brand-swatch:hover { transform: scale(1.05); }
.brand-name { font-weight: 600; font-size: 14px; color: var(--c-navy); }
.brand-role { font-size: 12px; color: var(--c-text-mute); margin-top: 2px; }
.contrast-badge {
  font: 700 11px var(--c-mono); text-align: center;
  padding: 4px 6px; border-radius: 6px;
  background: var(--c-bg-soft, #f1f5f9); color: var(--c-text-mute);
}
.contrast-badge.ok   { background: #dcfce7; color: #15803d; }
.contrast-badge.warn { background: #fef9c3; color: #a16207; }

.xg-label { border: 0; background: transparent; font: 600 14px var(--c-sans); color: var(--c-navy); padding: 1px 0; outline: none; width: 100%; border-bottom: 1px solid transparent; }
.xg-label:focus { border-bottom-color: var(--c-line); }
.xg-empty { font-size: 13px; color: var(--c-text-mute); padding: 10px 4px; }

/* Preset grid */
.preset-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; }
.preset-card {
  position: relative;
  text-align: left; cursor: pointer;
  background: #fff; border: 1.5px solid var(--c-line); border-radius: 12px;
  padding: 10px; display: grid; gap: 10px;
  transition: border-color .12s, box-shadow .12s, transform .12s;
}
.preset-del {
  position: absolute; top: 6px; right: 6px; width: 22px; height: 22px;
  border: 0; border-radius: 6px; background: rgba(255,255,255,.92); color: #b42318;
  cursor: pointer; display: grid; place-items: center; padding: 0; opacity: 0;
  box-shadow: 0 1px 4px rgba(0,0,0,.15); transition: opacity .12s, background .12s; z-index: 2;
}
.preset-card:hover .preset-del { opacity: 1; }
.preset-del:hover { background: #fee2e2; }
.preset-del svg { width: 13px; height: 13px; }
.preset-tag { font-size: 10px; background: var(--c-bg-soft, #f1f5f9); color: var(--c-text-mute); }
.preset-card:hover { border-color: var(--c-text-faint); transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,0,0,.06); }
.preset-card.is-active { border-color: var(--c-red); box-shadow: 0 0 0 1px var(--c-red); }
.preset-swatches { display: flex; height: 44px; border-radius: 8px; overflow: hidden; box-shadow: inset 0 0 0 1px rgba(0,0,0,.06); }
.preset-sw { flex: 1; }
.preset-meta { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.preset-meta b { font-size: 13.5px; color: var(--c-navy); }
.preset-active { font-size: 10.5px; }

/* Generatore */
.gen-controls { display: grid; gap: 14px; }
.gen-seeds { display: flex; flex-wrap: wrap; gap: 18px; }
.gen-seed { display: flex; align-items: center; gap: 10px; }
.gen-seed-label { font: 600 12.5px var(--c-sans); color: var(--c-text-mute); }
.cfg-input.mono.sm { max-width: 120px; }
.gen-rules { display: flex; flex-wrap: wrap; gap: 6px; }
.gen-rule {
  padding: 7px 12px; background: #fff; border: 1px solid var(--c-line); border-radius: 8px;
  font: 600 12.5px var(--c-sans); color: var(--c-text-mute); cursor: pointer;
  transition: border-color .12s, color .12s, background .12s;
}
.gen-rule:hover { border-color: var(--c-text-faint); color: var(--c-navy); }
.gen-rule.is-on { border-color: var(--c-red); color: var(--c-navy); background: var(--c-red-soft); }
.gen-neutral { display: flex; align-items: center; gap: 10px; font: 500 12.5px var(--c-sans); color: var(--c-text-mute); cursor: pointer; }
.gen-sw.is-neutral { box-shadow: inset 0 0 0 1px rgba(0,0,0,.12); }
.gen-preview { display: flex; align-items: center; gap: 16px; margin-top: 14px; flex-wrap: wrap; }
.gen-swatches { display: flex; gap: 8px; flex: 1; min-width: 240px; }
.gen-sw {
  flex: 1; height: 64px; border-radius: 10px; box-shadow: inset 0 0 0 1px rgba(0,0,0,.06);
  display: flex; flex-direction: column; align-items: center; justify-content: space-between; padding: 6px;
}
.gen-sw-role { font: 700 9px var(--c-sans); text-transform: uppercase; letter-spacing: .03em; background: rgba(255,255,255,.9); color: #111; padding: 2px 6px; border-radius: 4px; }
.gen-sw-hex { font: 600 10px var(--c-mono); background: rgba(255,255,255,.85); color: #111; padding: 2px 5px; border-radius: 4px; }

/* Scala neutri */
.neutral-scale { display: flex; gap: 6px; }
.neutral-col { flex: 1; }
.neutral-swatch {
  position: relative; display: block; overflow: hidden;
  width: 100%; height: 64px; border-radius: 8px;
  box-shadow: inset 0 0 0 1px rgba(0,0,0,.06);
  border: 0; padding: 0; cursor: pointer;
  transition: transform .12s, box-shadow .12s;
}
.neutral-swatch:hover:not(.is-locked) { transform: translateY(-1px); box-shadow: inset 0 0 0 1px rgba(0,0,0,.1), 0 4px 10px rgba(0,0,0,.08); }
.neutral-swatch:disabled, .neutral-swatch.is-locked { cursor: not-allowed; }
.neutral-label { text-align: center; margin-top: 6px; font-family: var(--c-mono); font-size: 11px; color: var(--c-text-mute); }
.tint-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.tint-chip {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 7px 12px; background: #fff; border: 1px solid var(--c-line); border-radius: 8px;
  font: 600 12.5px var(--c-sans); color: var(--c-text-mute); cursor: pointer;
  transition: border-color .12s, color .12s, background .12s;
}
.tint-chip:hover { border-color: var(--c-text-faint); color: var(--c-navy); }
.tint-chip.is-on { border-color: var(--c-red); color: var(--c-navy); background: var(--c-red-soft); }
.tint-dot { width: 14px; height: 14px; border-radius: 50%; box-shadow: inset 0 0 0 1px rgba(0,0,0,.12); }
</style>
