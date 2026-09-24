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
          <button type="button" class="cfg-btn-icon cfg-btn-ghost xg-act" :title="t('Elimina')" :aria-label="t('Elimina') + ' ' + (g.label || g.id)" :disabled="xgFermi" @click="removeExtraGlobal(g.id)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </div>
      </div>
      <div v-else class="xg-empty">{{ t('Nessun colore globale extra. Aggiungine uno, oppure generane con la regola Triade/Tetrade qui sopra.') }}</div>

      <!-- Colori nascosti: fuori dalle swatch del builder, ma ancora nel CSS del sito. -->
      <div v-if="hiddenGlobals.length" class="xg-hidden">
        <div class="xg-hidden-head">
          <b>{{ t('Nascosti') }}</b>
          <span class="cfg-pill off">{{ hiddenGlobals.length }}</span>
        </div>
        <p class="xg-hidden-hint">{{ t('Tolti dalle swatch del builder ma ancora nel CSS del sito: le tile che li usano restano del loro colore.') }}</p>
        <div class="brand-list">
          <div v-for="g in hiddenGlobals" :key="g.id" class="brand-row">
            <span class="brand-swatch xg-hidden-sw" :style="{ background: g.value }" aria-hidden="true"></span>
            <div class="brand-info">
              <div class="brand-name">{{ g.label || g.id }}</div>
              <div class="brand-role">var(--olo-color-{{ g.id }})</div>
            </div>
            <button type="button" class="cfg-btn cfg-btn-secondary xg-act" :disabled="xgFermi" @click="showExtraGlobal(g.id)">{{ t('Mostra di nuovo') }}</button>
            <button type="button" class="cfg-btn-icon cfg-btn-ghost xg-act" :title="t('Elimina')" :aria-label="t('Elimina') + ' ' + (g.label || g.id)" :disabled="xgFermi" @click="removeExtraGlobal(g.id)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
          </div>
        </div>
      </div>
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
          <CfgSelect :model-value="darkMode.strategy" :options="DARK_STRATEGY_OPTS" size="md" @update:model-value="setDark('strategy', $event)" />
        </div>
      </div>
    </div>
  </div>

  <!-- 7) VERSIONI DELLO STILE (istantanee, olobuild_design_preset_snapshots) -->
  <div v-if="versioniLette" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l3 2"/></svg>
      </div>
      <div>
        <h3>{{ t('Versioni dello stile') }}</h3>
        <p>{{ t('Com\'era lo stile del sito prima di ogni import di tema o del sito, salvataggio degli stili e ripristino dei predefiniti: colori, tipografia, set tipografici e font caricati, header, footer e pagina 404 attivi, cursore e mirino, pagina iniziale e il template delle pagine che l\'import ha riusato. Non tornano i template e le pagine creati dall\'import, né il testo delle pagine riusate dall\'import del sito. Restano le ultime 10, e sempre quella di prima dell\'ultimo import.') }}</p>
      </div>
      <div class="head-actions">
        <span class="cfg-pill">{{ versioni.length }}</span>
      </div>
    </div>
    <div class="cfg-card-body">
      <div v-if="versioni.length" class="brand-list">
        <div v-for="v in versioni" :key="v.id" class="brand-row snap-row">
          <span class="snap-sw" :style="v.primario ? { background: v.primario } : null" aria-hidden="true"></span>
          <div class="brand-info">
            <div class="brand-name">{{ motivoVersione(v) }}</div>
            <div class="brand-role">{{ dataVersione(v.ora) }}<template v-if="v.utente"> · {{ v.utente }}</template><template v-if="v.font_titoli"> · {{ v.font_titoli }}</template></div>
          </div>
          <button type="button" class="cfg-btn cfg-btn-secondary xg-act" :disabled="ripristinando" @click="ripristinaVersione(v)">{{ t('Ripristina') }}</button>
        </div>
      </div>
      <div v-else class="xg-empty">{{ t('Nessuna versione ancora: la prima nasce al prossimo import o salvataggio degli stili.') }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import { HARMONY_RULES, harmonize, paletteToRoles, neutralsFromSeed, readableText, contrastRatio, isValidHex } from '@/utils/colorHarmony';
import CfgSelect from './controls/CfgSelect.vue';
import { okOrThrow, cfgJob, assertLoaded, reloadJob } from './cfgSave';
import { ripristinaIstantanea, descriviSaltati } from '@/utils/styleSnapshots';

const TAB_ID = 'colori';
const showToast = inject('showToast', () => {});
const shellDirty = inject('setDirty', () => {});
const setDirty = (v) => shellDirty(v, TAB_ID);
const loaded = ref(false);

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
const DARK_STRATEGY_OPTS = [
  { value: 'auto', label: t('Automatica (consigliata)') },
  { value: 'manual', label: t('Manuale, palette separata') },
  { value: 'luminance', label: t('Solo aggiusta luminanza') },
];

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

// Un globale che copre un ruolo della Palette (copreRuolo: primary, text…) si modifica
// nella riga del ruolo; tutti gli altri sono "extra" riutilizzabili. Anche text_muted o
// primary_contrast: escono come --olo-color-text_muted, non coprono il ruolo e, nascosti
// dopo un import, prima non si potevano né rimettere in vista né eliminare.
// I nascosti (`hidden`) sono fuori dalle swatch del builder ma ancora emessi nel CSS:
// stanno in un gruppo a parte, da cui si rimettono in vista o si eliminano.
const extraGlobals = computed(() => (globalColors.value || []).filter((g) => g && g.id && !copreRuolo(g) && !g.hidden));
const hiddenGlobals = computed(() => (globalColors.value || []).filter((g) => g && g.id && !copreRuolo(g) && g.hidden));
const xgBusy = ref(false);

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
      colors.value = { ...DEFAULT_COLORS, ...(s.colors || {}) };
      if (s.neutrals) neutrals.value = { mode: s.neutrals.mode || 'auto', tint: s.neutrals.tint || 'zinc', scale: (s.neutrals.scale && s.neutrals.scale.length) ? [...s.neutrals.scale] : [...NEUTRAL_PRESETS[s.neutrals.tint || 'zinc']] };
      if (s.dark_mode) darkMode.value = { enabled: s.dark_mode.enabled !== false, strategy: s.dark_mode.strategy || 'auto' };
      loaded.value = true;
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
        if (gc && gc.value && copreRuolo(gc)) {
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
// Solo gli id senza «_»: il ruolo text_muted esce come --olo-color-text-muted, mentre un
// globale «text_muted» esce come --olo-color-text_muted e non copre niente (se è nascosto,
// dopo un import o un ripristino, riporterebbe in Palette un valore che il sito non usa):
// sta fra gli extra (o fra i nascosti) col suo valore. Gemello PHP: sync_global_palette().
function copreRuolo(gc) {
  return !!(gc && gc.id) && !String(gc.id).includes('_') && colors.value[gc.id] !== undefined;
}

async function syncGlobalColors() {
  // PUT /global-colors fa REPLACE totale dell'array: rileggo lo stato corrente dal
  // server per non cancellare colori aggiunti altrove (es. il "+" del builder) con
  // uno snapshot stale. Senza rilettura non si scrive: l'errore arriva alla shell
  // e la scheda resta da salvare (prima si ripiegava sulla lista a video).
  const server = await leggiGlobalColors();
  let changed = globalColorsTouched.value;
  // 1) allinea i ruoli core ai valori correnti del pannello
  const next = server.map((gc) => {
    if (copreRuolo(gc) && gc.value !== colors.value[gc.id]) {
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
  // I global color dei ruoli core vincono nel CSS: se questo PUT fallisce la
  // scheda non è salvata davvero, e resta da salvare (il flag torna com'era).
  const touchedBefore = globalColorsTouched.value;
  globalColorsTouched.value = false;
  try {
    await okOrThrow(fetch(`${window.oloData.restUrl}global-colors`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(next),
    }));
  } catch (e) {
    globalColorsTouched.value = touchedBefore;
    throw e;
  }
}

// Lista dei colori globali com'è sul server; lancia se non si riesce a leggerla.
async function leggiGlobalColors() {
  const r = await okOrThrow(fetch(`${window.oloData.restUrl}global-colors`, { headers: { 'X-WP-Nonce': window.oloData.nonce } }));
  const s = await r.json();
  if (!Array.isArray(s)) throw new Error(t('risposta non valida del server'));
  return s;
}

// I colori globali EXTRA si salvano subito e in modo merge-safe (rileggo dal server,
// applico la mutazione, riscrivo): così add/edit/delete non si pestano col builder.
// Se la rilettura non riesce non si scrive (la lista a video, forse vecchia,
// cancellerebbe i colori aggiunti altrove); se il PUT fallisce la lista torna a
// quella del server: a video resta ciò che è salvato davvero, più il toast.
async function scriviGlobalColors(mutator) {
  let list;
  try {
    list = await leggiGlobalColors();
  } catch (e) {
    globalColors.value = (globalColors.value || []).slice(); // il campo toccato torna al valore salvato
    showToast(t('Errore salvataggio colori globali'), 'error');
    return;
  }
  const next = mutator(list.map((x) => ({ ...x })));
  globalColors.value = next;
  try {
    await okOrThrow(fetch(`${window.oloData.restUrl}global-colors`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(next),
    }));
  } catch (e) {
    globalColors.value = list;
    showToast(t('Errore salvataggio colori globali'), 'error');
  }
}
function persistGlobalColors(mutator) {
  return inCodaColori(() => scriviGlobalColors(mutator));
}
// Le scritture della lista passano una alla volta, nell'ordine in cui partono: ognuna
// rilegge dal server e riscrive l'INTERA lista, e due in volo insieme («Mostra di nuovo»
// su un colore ed «Elimina» su un altro, due ritocchi di fila, il «Salva» della scheda)
// potevano leggere la lista prima della scrittura dell'altra e riportarla indietro.
// Finché una scrittura è in coda o in volo, e per tutta un'eliminazione (xgBusy: conteggio,
// conferma, scrittura), «Mostra di nuovo» ed «Elimina» restano disattivati (xgFermi).
// Chi è in coda non deve accodarne un'altra e aspettarla: si fermerebbe per sempre.
let codaColori = Promise.resolve();
const scrittureColori = ref(0);
const xgFermi = computed(() => xgBusy.value || scrittureColori.value > 0);
function inCodaColori(fn) {
  scrittureColori.value++;
  const giro = codaColori.then(() => fn());
  codaColori = giro.then(() => {}, () => {}); // un errore non ferma la coda
  return giro.finally(() => { scrittureColori.value--; });
}
function addExtraGlobal() {
  const id = 'c' + Date.now().toString(36);
  persistGlobalColors((list) => [...list, { id, label: t('Nuovo colore'), value: '#888888' }]);
}
// Dove il colore è usato, detto a parole: «3 template (Home, Chi siamo, Header); 1 widget globale».
// ruolo_tile (accent, dark, light): i default delle tile lo leggono anche dove nessuno lo nomina.
function descriviUso(uso) {
  const parti = [];
  const nt = uso.template_count || 0;
  if (nt) {
    const titoli = (uso.templates || []).map((x) => x.title || ('#' + x.id));
    parti.push(nt + ' ' + t('template') + (titoli.length ? ' (' + titoli.join(', ') + (nt > titoli.length ? ', …' : '') + ')' : ''));
  }
  if (uso.widgets) parti.push(uso.widgets + ' ' + (uso.widgets === 1 ? t('widget globale') : t('widget globali')));
  if (uso.ab_tests) parti.push(uso.ab_tests + ' ' + t('test A/B'));
  if (uso.sezioni) parti.push(uso.sezioni + ' ' + (uso.sezioni === 1 ? t('sezione salvata nella libreria') : t('sezioni salvate nella libreria')));
  if (uso.styles) parti.push(t('stili globali'));
  if (uso.ruolo_tile) parti.push(t('tutte le tile senza un colore scelto (è il loro colore predefinito)'));
  return parti.join('; ');
}

// «Elimina» chiede prima al server dove il colore è usato. Un colore usato non si
// cancella: si NASCONDE (resta nel CSS, le tile non cambiano). Cancellarlo davvero
// toglie la variabile e quelle tile perdono il colore, in silenzio, su pagine che
// nessuno sta guardando: da nascosto e ancora usato serve una seconda conferma.
// Senza la risposta del conteggio non si elimina niente.
async function removeExtraGlobal(id) {
  if (xgFermi.value) return;
  xgBusy.value = true;
  try {
    let uso;
    try {
      const r = await okOrThrow(fetch(`${window.oloData.restUrl}global-colors/${encodeURIComponent(id)}/usage`, {
        headers: { 'X-WP-Nonce': window.oloData.nonce },
      }));
      uso = await r.json();
      if (!uso || typeof uso.total !== 'number') throw new Error(t('risposta non valida del server'));
    } catch (e) {
      showToast(t('Non so dove è usato questo colore: non lo elimino') + (e && e.message ? ' — ' + e.message : ''), 'error');
      return;
    }
    const attuale = (globalColors.value || []).find((g) => g && g.id === id);
    if (uso.total > 0) {
      const dove = descriviUso(uso);
      if (!(attuale && attuale.hidden)) {
        if (!confirm(t('Questo colore è usato in') + ' ' + dove + '.\n\n'
          + t('Invece di eliminarlo lo nascondo: sparisce dalle swatch del builder ma resta nel CSS, e quelle tile non cambiano.'))) return;
        await persistGlobalColors((list) => list.map((g) => (g.id === id ? { ...g, hidden: true } : g)));
        return;
      }
      if (!confirm(t('Questo colore nascosto è ancora usato in') + ' ' + dove + '.\n\n'
        + t('Se lo elimini, quelle tile perdono questo colore: resta solo il colore di riserva, dove la tile ne ha uno. Eliminarlo comunque?'))) return;
    } else {
      const rev = uso.revisions
        ? '\n\n' + t('Compare in') + ' ' + uso.revisions + ' ' + t('revisioni: ripristinandone una, quel colore mancherà.')
        : '';
      if (!confirm(t('Eliminare questo colore globale? Non risulta usato in template, widget globali, test A/B, sezioni salvate nella libreria o stili globali.') + rev)) return;
    }
    await persistGlobalColors((list) => list.filter((g) => g.id !== id));
  } finally {
    xgBusy.value = false;
  }
}
// Un colore nascosto torna fra le swatch del builder.
function showExtraGlobal(id) {
  if (xgFermi.value) return;
  persistGlobalColors((list) => list.map((g) => {
    if (g.id !== id) return g;
    const copia = { ...g };
    delete copia.hidden;
    return copia;
  }));
}
function setExtraGlobalHex(id, val) {
  const clean = '#' + String(val).replace(/[^0-9a-fA-F]/g, '').slice(0, 6).toUpperCase();
  persistGlobalColors((list) => list.map((g) => (g.id === id ? { ...g, value: clean } : g)));
}
function setExtraGlobalLabel(id, label) {
  persistGlobalColors((list) => list.map((g) => (g.id === id ? { ...g, label } : g)));
}
// Solo i blocchi di questa scheda: il server fonde per blocco (array_replace), e
// rimandare l'intero olo_styles letto all'apertura riportava indietro la
// tipografia (e gli altri blocchi) salvata nel frattempo dalle altre schede.
async function saveStyles() {
  assertLoaded(loaded);
  recomputeContrasts();
  const body = {
    colors: { ...colors.value },
    neutrals: { mode: neutrals.value.mode, tint: neutrals.value.tint, scale: [...displayNeutrals.value] },
    dark_mode: { enabled: darkMode.value.enabled, strategy: darkMode.value.strategy },
  };
  await okOrThrow(fetch(`${window.oloData.restUrl}styles`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
    body: JSON.stringify(body),
  }));
  await inCodaColori(syncGlobalColors); // una scrittura della lista alla volta (inCodaColori)
}

// ── Versioni dello stile ──
// Il server copia lo stile del sito prima di import (tema, sito), salvataggi degli
// stili e ripristino dei predefiniti; qui si elencano e si ripristinano. La card
// non rende la scheda «da salvare»: il ripristino si applica subito.
const versioni = ref([]);
const versioniLette = ref(false); // senza permesso (o con errore) la card non c'è
const ripristinando = ref(false);
const MOTIVI_VERSIONE = {
  import_tema: 'Prima dell\'import del tema',
  stili_salvati: 'Prima di un salvataggio degli stili',
  ripristino_stili: 'Prima del ripristino degli stili predefiniti',
  prima_del_ripristino: 'Prima di un ripristino di versione',
  import_sito: 'Prima dell\'import del sito',
};
function motivoVersione(v) {
  const base = t(MOTIVI_VERSIONE[v.motivo] || 'Versione dello stile');
  return v.dettaglio ? base + ' «' + v.dettaglio + '»' : base;
}
function dataVersione(ora) {
  const n = Number(ora) || 0;
  return n ? new Date(n * 1000).toLocaleString() : '';
}
async function caricaVersioni() {
  try {
    const r = await okOrThrow(fetch(`${window.oloData.restUrl}design-presets/snapshots`, { headers: { 'X-WP-Nonce': window.oloData.nonce } }));
    const d = await r.json();
    versioni.value = Array.isArray(d) ? d : [];
    versioniLette.value = true;
  } catch (e) { /* senza elenco la card resta nascosta */ }
}
async function ripristinaVersione(v) {
  if (ripristinando.value) return;
  if (!confirm(t('Ripristinare lo stile del sito com\'era in quel momento?') + '\n\n'
    + t('Colori, tipografia, set tipografici e font, header, footer e pagina 404 attivi, cursore e mirino, pagina iniziale e il template delle pagine riusate dall\'import tornano a quel punto; lo stato di adesso resta fra le versioni. I set tipografici, i font e i colori globali creati dopo restano, tranne i colori che ricolorano tutto il sito: quelli con il nome di un ruolo del tema (primary, text…) o di un ruolo delle tile (accent, dark, light) tornano come erano o spariscono. Non tornano il testo delle pagine riusate dall\'import del sito né i template, le pagine e le voci di menu creati, né ciò che nel frattempo è stato eliminato. La pagina si ricarica.'))) return;
  ripristinando.value = true;
  try {
    const esito = await ripristinaIstantanea(v.id);
    const saltati = descriviSaltati(esito);
    showToast(saltati
      ? t('Stile ripristinato, tranne ciò che nel frattempo è stato eliminato (resta com\'è ora)') + ': ' + saltati
      : t('Stile ripristinato'), 'success', saltati ? 6000 : 2500);
    // Le altre schede tengono i blocchi di stile letti all'apertura: al primo
    // «Salva» riscriverebbero quelli di prima del ripristino.
    setTimeout(() => window.location.reload(), saltati ? 4000 : 800);
  } catch (e) {
    ripristinando.value = false;
    showToast(t('Ripristino non riuscito') + (e && e.message ? ' — ' + e.message : ''), 'error', 6000);
  }
}

const onSave = cfgJob(TAB_ID, async () => { await saveStyles(); caricaVersioni(); });
const onDiscard = cfgJob(TAB_ID, reloadJob(loaded, loadStyles));

onMounted(() => {
  loadStyles();
  caricaVersioni();
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
.contrast-badge.ok   { background: var(--c-success-soft); color: var(--c-success); }
.contrast-badge.warn { background: var(--c-warning-soft); color: var(--c-warning); }

.xg-label { border: 0; background: transparent; font: 600 14px var(--c-sans); color: var(--c-navy); padding: 1px 0; outline: none; width: 100%; border-bottom: 1px solid transparent; }
.xg-label:focus { border-bottom-color: var(--c-line); }
.xg-empty { font-size: 13px; color: var(--c-text-mute); padding: 10px 4px; }
.xg-act:focus-visible { outline: 2px solid var(--c-red); outline-offset: 2px; }
.xg-hidden { margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--c-line-soft); }
.xg-hidden-head { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--c-navy); }
.xg-hidden-hint { font-size: 12px; color: var(--c-text-mute); margin: 4px 0 10px; }
.xg-hidden-sw { cursor: default; opacity: .7; }
.xg-hidden-sw:hover { transform: none; }
.snap-row { grid-template-columns: 32px 1fr auto; }
.snap-sw { width: 32px; height: 32px; border-radius: 8px; background: var(--c-bg); box-shadow: inset 0 0 0 1px rgba(0,0,0,.06); }

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
  border: 0; border-radius: 6px; background: rgba(255,255,255,.92); color: var(--c-red-dark);
  cursor: pointer; display: grid; place-items: center; padding: 0; opacity: 0;
  box-shadow: 0 1px 4px rgba(0,0,0,.15); transition: opacity .12s, background .12s; z-index: 2;
}
.preset-card:hover .preset-del { opacity: 1; }
.preset-del:hover { background: var(--c-red-soft-2); }
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
