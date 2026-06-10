<template>
  <div class="field-font-family" ref="rootEl">
    <!-- Current value display + toggle -->
    <button
      type="button"
      @click="open = !open"
      :aria-expanded="open"
      class="ff-trigger"
    >
      <span class="ff-preview" :style="previewStyle">{{ displayLabel }}</span>
      <span class="ff-arrow">&#9662;</span>
    </button>

    <!-- Dropdown panel -->
    <Teleport to="body">
      <div v-if="open" class="ff-backdrop" @click="open = false"></div>
      <div v-if="open" class="ff-dropdown" ref="dropdownEl" :style="dropdownPos">
        <!-- Search -->
        <div class="ff-search-wrap">
          <input
            ref="searchInput"
            v-model="search"
            type="text"
            :placeholder="t('Cerca font...')"
            class="ff-search"
          />
        </div>

        <div class="ff-list">
          <!-- Default option -->
          <div
            v-if="!search"
            class="ff-item ff-item--default"
            :class="{ 'ff-item--active': !modelValue }"
            @click="selectFont('')"
          >{{ t('Predefinito (eredita)') }}</div>

          <!-- Ruoli del tema: la tile segue la tipografia globale del sito
               (pattern FieldColor: ruoli prima, valori specifici poi) -->
          <template v-if="filteredThemeRoles.length">
            <div class="ff-group-label">{{ t('Ruoli del tema') }}</div>
            <div
              v-for="role in filteredThemeRoles"
              :key="'role-' + role.value"
              class="ff-item"
              :class="{ 'ff-item--active': modelValue === role.value || legacyOfRole(role) === modelValue }"
              :style="{ fontFamily: role.value }"
              @click="selectFont(role.value)"
            >
              {{ role.label }}
              <span class="ff-badge ff-badge--role">{{ t('Tema') }}</span>
            </div>
          </template>

          <!-- Set tipografici globali (GlobalTypographyPanel) -->
          <template v-if="filteredTypoSets.length">
            <div class="ff-group-label">{{ t('Set tipografici') }}</div>
            <div
              v-for="set in filteredTypoSets"
              :key="'set-' + set.id"
              class="ff-item"
              :class="{ 'ff-item--active': modelValue === set.value }"
              :style="{ fontFamily: set.family + ', sans-serif' }"
              @click="selectFont(set.value)"
            >
              {{ set.label }}
              <span class="ff-badge ff-badge--role">{{ t('Set') }}</span>
            </div>
          </template>

          <!-- Custom Fonts (uploaded) -->
          <template v-if="filteredCustomFonts.length">
            <div class="ff-group-label">{{ t('Font personalizzati') }}</div>
            <div
              v-for="font in filteredCustomFonts"
              :key="'cf-' + font.id"
              class="ff-item"
              :class="{ 'ff-item--active': modelValue === font.name }"
              :style="{ fontFamily: font.name + ', sans-serif' }"
              @click="selectFont(font.name)"
            >
              {{ font.name }}
              <span class="ff-badge ff-badge--custom">{{ t('CF') }}</span>
            </div>
          </template>

          <!-- Project Google Fonts (if any) -->
          <template v-if="projectFonts.length && filteredProjectFonts.length">
            <div class="ff-group-label">Font del progetto</div>
            <div
              v-for="font in filteredProjectFonts"
              :key="'p-' + font"
              class="ff-item"
              :class="{ 'ff-item--active': modelValue === font }"
              :style="{ fontFamily: font + ', sans-serif' }"
              @click="selectFont(font)"
            >
              {{ font }}
              <span class="ff-badge ff-badge--g">G</span>
            </div>
          </template>

          <!-- Web Safe -->
          <template v-if="filteredWebSafe.length">
            <div class="ff-group-label">Web Safe</div>
            <div
              v-for="font in filteredWebSafe"
              :key="'ws-' + font.value"
              class="ff-item"
              :class="{ 'ff-item--active': modelValue === font.value }"
              :style="{ fontFamily: font.value }"
              @click="selectFont(font.value)"
            >{{ font.label }}</div>
          </template>

          <!-- Popular Google Fonts -->
          <template v-if="filteredGoogle.length">
            <div class="ff-group-label">Google Fonts</div>
            <div
              v-for="font in filteredGoogle"
              :key="'gf-' + font"
              class="ff-item"
              :class="{ 'ff-item--active': modelValue === font }"
              @click="selectFont(font, true)"
            >
              {{ font }}
              <span class="ff-badge ff-badge--g">G</span>
            </div>
          </template>

          <div v-if="!filteredCustomFonts.length && !filteredWebSafe.length && !filteredGoogle.length && !filteredProjectFonts.length" class="ff-empty">
            Nessun risultato
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { useStylesStore } from '@/stores/styles';
import { useFocusTrap } from '@/composables/useFocusTrap';
import { FONT_ROLE_VARS } from '@/composables/oloTileDefaults';

const props = defineProps({
  modelValue: { type: String, default: '' },
});

// Ruoli del tema: selezionarli salva il var() CSS pronto, così la tile segue
// la tipografia globale (cfg → Typography). `legacy` = i valori-codice che le
// vecchie select salvavano per lo stesso ruolo (per evidenziare l'attivo).
const THEME_ROLES = [
  { value: 'var(--olo-font-family)',         label: t('Testo'),  legacy: ['sans', 'body'] },
  { value: 'var(--olo-font-family-heading)', label: t('Titoli'), legacy: ['heading', 'serif'] },
  { value: 'var(--olo-font-family-mono)',    label: t('Mono'),   legacy: ['mono'] },
];

// Label umane per i valori legacy ancora salvati nei template.
const LEGACY_LABELS = {
  sans: t('Testo (tema)'),
  body: t('Testo (tema)'),
  serif: t('Titoli (tema)'),
  heading: t('Titoli (tema)'),
  mono: t('Mono (tema)'),
  inherit: t('Eredita'),
};

const emit = defineEmits(['update:modelValue']);

const stylesStore = useStylesStore();
const open = ref(false);
const search = ref('');
const rootEl = ref(null);
const dropdownEl = ref(null);
const searchInput = ref(null);
const dropdownPos = ref({});

// Focus trap per accessibilità dropdown
const { activate: activateTrap, deactivate: deactivateTrap } = useFocusTrap(dropdownEl);

// Custom fonts (uploaded via REST API)
const oloData = window.oloData || {};
const customFonts = ref([]);
const customFontsLoaded = ref(false);

async function loadCustomFonts() {
  if (customFontsLoaded.value) return;
  try {
    const res = await fetch(`${oloData.restUrl}/fonts`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      customFonts.value = await res.json();
    }
  } catch (e) {
    // silently fail
  }
  customFontsLoaded.value = true;
}

const filteredCustomFonts = computed(() => {
  if (!customFonts.value.length) return [];
  if (!search.value) return customFonts.value;
  const q = search.value.toLowerCase();
  return customFonts.value.filter(f => f.name.toLowerCase().includes(q));
});

// Inject @font-face for custom fonts preview in the builder
function injectCustomFontFaces() {
  if (!customFonts.value.length) return;
  let styleEl = document.getElementById('olo-custom-fonts-preview');
  if (!styleEl) {
    styleEl = document.createElement('style');
    styleEl.id = 'olo-custom-fonts-preview';
    document.head.appendChild(styleEl);
  }
  let css = '';
  for (const font of customFonts.value) {
    if (!font.variants) continue;
    for (const v of font.variants) {
      if (!v.file) continue;
      const ext = v.file.split('.').pop().toLowerCase();
      let format = 'woff2';
      if (ext === 'ttf') format = 'truetype';
      else if (ext === 'otf') format = 'opentype';
      else if (ext === 'woff') format = 'woff';
      css += `@font-face { font-family: '${font.name}'; src: url('${v.file}') format('${format}'); font-weight: ${v.weight || '400'}; font-style: ${v.style || 'normal'}; font-display: swap; }\n`;
    }
  }
  styleEl.textContent = css;
}

// Web-safe fonts
const webSafeFonts = [
  { label: 'System UI', value: 'system-ui, -apple-system, sans-serif' },
  { label: 'Arial', value: 'Arial, Helvetica, sans-serif' },
  { label: 'Verdana', value: 'Verdana, Geneva, sans-serif' },
  { label: 'Tahoma', value: 'Tahoma, Geneva, sans-serif' },
  { label: 'Trebuchet MS', value: "'Trebuchet MS', sans-serif" },
  { label: 'Segoe UI', value: "'Segoe UI', Tahoma, sans-serif" },
  { label: 'Georgia', value: 'Georgia, serif' },
  { label: 'Times New Roman', value: "'Times New Roman', Times, serif" },
  { label: 'Garamond', value: 'Garamond, serif' },
  { label: 'Palatino', value: "'Palatino Linotype', Palatino, serif" },
  { label: 'Courier New', value: "'Courier New', Courier, monospace" },
  { label: 'Consolas', value: 'Consolas, monospace' },
];

// Popular Google Fonts
const popularGoogleFonts = [
  'Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat',
  'Poppins', 'Nunito', 'Raleway', 'Oswald', 'Source Sans 3',
  'Ubuntu', 'Merriweather', 'Playfair Display', 'PT Sans', 'Noto Sans',
  'Work Sans', 'DM Sans', 'Fira Sans', 'Barlow', 'Rubik',
  'Libre Baskerville', 'Crimson Text', 'Josefin Sans', 'Quicksand', 'Cabin',
  'Archivo', 'Manrope', 'Space Grotesk', 'Outfit', 'Plus Jakarta Sans',
  'Bitter', 'Lora', 'EB Garamond', 'Cormorant Garamond', 'Spectral',
];

// Project fonts from store
const projectFonts = computed(() => stylesStore.googleFonts || []);

const filteredThemeRoles = computed(() => {
  if (!search.value) return THEME_ROLES;
  const q = search.value.toLowerCase();
  return THEME_ROLES.filter(r => r.label.toLowerCase().includes(q));
});

// Set tipografici globali con una family definita → var(--olo-font-{id}-family)
const typoSets = computed(() =>
  (stylesStore.globalTypography || [])
    .filter(s => s && s.id && s.family)
    .map(s => ({ id: s.id, label: s.label || s.id, family: s.family, value: `var(--olo-font-${s.id}-family)` }))
);
const filteredTypoSets = computed(() => {
  if (!search.value) return typoSets.value;
  const q = search.value.toLowerCase();
  return typoSets.value.filter(s => s.label.toLowerCase().includes(q) || s.family.toLowerCase().includes(q));
});

function legacyOfRole(role) {
  return (role.legacy || []).find(l => l === props.modelValue) || null;
}

const filteredProjectFonts = computed(() => {
  if (!search.value) return projectFonts.value;
  const q = search.value.toLowerCase();
  return projectFonts.value.filter(f => f.toLowerCase().includes(q));
});

const filteredWebSafe = computed(() => {
  if (!search.value) return webSafeFonts;
  const q = search.value.toLowerCase();
  return webSafeFonts.filter(f => f.label.toLowerCase().includes(q));
});

const filteredGoogle = computed(() => {
  // Exclude fonts already in project list
  const projSet = new Set(projectFonts.value.map(f => f.toLowerCase()));
  let list = popularGoogleFonts.filter(f => !projSet.has(f.toLowerCase()));
  if (search.value) {
    const q = search.value.toLowerCase();
    list = list.filter(f => f.toLowerCase().includes(q));
  }
  return list;
});

const displayLabel = computed(() => {
  const v = props.modelValue;
  if (!v) return t('Predefinito');
  // Ruoli del tema (formato nuovo var(...)) — prefix-match così anche i var
  // con fallback inline (es. legacy "var(--olo-font-family-heading, 'Libre…')")
  // mostrano la label del ruolo invece della stringa CSS grezza.
  if (v.startsWith('var(--olo-font-family-heading')) return t('Titoli') + ' (tema)';
  if (v.startsWith('var(--olo-font-family-mono')) return t('Mono') + ' (tema)';
  if (v.startsWith('var(--olo-font-family')) return t('Testo') + ' (tema)';
  const role = THEME_ROLES.find(r => r.value === v);
  if (role) return role.label + ' (tema)';
  // Set tipografici globali
  const set = typoSets.value.find(s => s.value === v);
  if (set) return set.label + ' (set)';
  // Valori legacy delle vecchie select per-tile
  if (LEGACY_LABELS[v]) return LEGACY_LABELS[v];
  // Try to match web-safe label
  const ws = webSafeFonts.find(f => f.value === v);
  if (ws) return ws.label;
  // Return raw value (probably a Google Font name)
  return v;
});

const previewStyle = computed(() => {
  const v = props.modelValue;
  if (!v || v === 'inherit') return {};
  // I valori legacy si vedono in anteprima col var del ruolo corrispondente.
  if (FONT_ROLE_VARS[v]) return { fontFamily: FONT_ROLE_VARS[v] };
  if (v.startsWith('var(')) return { fontFamily: v };
  return { fontFamily: v + ', sans-serif' };
});

function selectFont(value, isGoogle = false) {
  if (isGoogle && value) {
    // Auto-add to Google Fonts if not already there
    const existing = (stylesStore.googleFonts || []).map(f => f.toLowerCase());
    if (!existing.includes(value.toLowerCase())) {
      stylesStore.addGoogleFont(value);
    }
  }
  emit('update:modelValue', value);
  open.value = false;
  search.value = '';
}

// Position dropdown near trigger
function positionDropdown() {
  if (!rootEl.value) return;
  const rect = rootEl.value.getBoundingClientRect();
  const spaceBelow = window.innerHeight - rect.bottom;
  const dropH = 360;

  if (spaceBelow >= dropH) {
    dropdownPos.value = {
      position: 'fixed',
      top: rect.bottom + 2 + 'px',
      left: rect.left + 'px',
      width: Math.max(rect.width, 260) + 'px',
    };
  } else {
    dropdownPos.value = {
      position: 'fixed',
      bottom: (window.innerHeight - rect.top + 2) + 'px',
      left: rect.left + 'px',
      width: Math.max(rect.width, 260) + 'px',
    };
  }
}

// Load Google Fonts preview CSS
const previewLinkEl = ref(null);
function loadGoogleFontsPreview() {
  // Load popular + project fonts for preview
  const all = [...new Set([...projectFonts.value, ...popularGoogleFonts])];
  if (!all.length) return;
  const families = all.map(f => 'family=' + f.replace(/ /g, '+')).join('&');
  const href = 'https://fonts.googleapis.com/css2?' + families + '&display=swap';

  if (previewLinkEl.value) {
    previewLinkEl.value.href = href;
  } else {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    link.id = 'olo-font-preview';
    document.head.appendChild(link);
    previewLinkEl.value = link;
  }
}

watch(open, async (val) => {
  if (val) {
    positionDropdown();
    loadGoogleFontsPreview();
    await loadCustomFonts();
    injectCustomFontFaces();
    // Doppio nextTick per garantire che Teleport abbia renderizzato il DOM
    nextTick(() => {
      nextTick(() => {
        searchInput.value?.focus();
        activateTrap();
      });
    });
  } else {
    search.value = '';
    deactivateTrap();
  }
});

onBeforeUnmount(() => {
  if (previewLinkEl.value) {
    previewLinkEl.value.remove();
    previewLinkEl.value = null;
  }
});
</script>

<style scoped>
.field-font-family {
  position: relative;
}

.ff-trigger {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 5px 8px;
  cursor: pointer;
  font-size: 12px;
  color: #1f2937;
  text-align: left;
}
.ff-trigger:hover {
  border-color: #9ca3af;
}
.ff-preview {
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.ff-arrow {
  color: #9ca3af;
  font-size: 10px;
  margin-left: 4px;
  flex-shrink: 0;
}

.ff-backdrop {
  position: fixed;
  inset: 0;
  z-index: 99998;
}

.ff-dropdown {
  z-index: 99999;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  max-height: 360px;
}

.ff-search-wrap {
  padding: 8px;
  border-bottom: 1px solid #e5e7eb;
}
.ff-search {
  width: 100%;
  background: #f9fafb;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  padding: 6px 8px;
  font-size: 12px;
  color: #1f2937;
  outline: none;
}
.ff-search:focus {
  border-color: var(--olo-color-primary, #6366f1);
}
.ff-search::placeholder {
  color: #9CA3AF;
}

.ff-list {
  overflow-y: auto;
  flex: 1;
  padding: 4px 0;
}

.ff-group-label {
  padding: 6px 12px 3px;
  font-size: 9px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #9CA3AF;
}

.ff-item {
  padding: 6px 12px;
  font-size: 13px;
  color: #374151;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.ff-item:hover {
  background: #f3f4f6;
  color: #1a1a1a;
}
.ff-item--active {
  background: var(--olo-color-primary, #6366F1);
  color: #fff;
}
.ff-item--active:hover {
  filter: brightness(0.88);
}
.ff-item--default {
  font-style: italic;
  color: #9ca3af;
}

.ff-badge {
  font-size: 9px;
  font-weight: 700;
  padding: 1px 4px;
  border-radius: 3px;
  flex-shrink: 0;
  font-style: normal;
}
.ff-badge--g {
  background: #4285f4;
  color: #fff;
}
.ff-badge--custom {
  background: #10b981;
  color: #fff;
}
.ff-badge--role {
  background: #e8622a;
  color: #fff;
}

.ff-empty {
  padding: 16px;
  text-align: center;
  color: #9CA3AF;
  font-size: 12px;
}

/* ── Scrollbar leggera ── */
.ff-list::-webkit-scrollbar { width: 6px; }
.ff-list::-webkit-scrollbar-track { background: transparent; }
.ff-list::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
.ff-list::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>
