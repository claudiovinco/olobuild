<template>
  <div class="olo-progresstracker-preview" :style="wrapStyle">
    <!-- Horizontal layout -->
    <template v-if="s.layout === 'horizontal'">
      <div style="display:flex;align-items:flex-start;position:relative;width:100%">
        <div
          v-for="(item, i) in items"
          :key="item.id || i"
          :style="hStepStyle"
        >
          <!-- Circle row with connectors -->
          <div style="display:flex;align-items:center;width:100%">
            <!-- Connector before (except first) -->
            <div v-if="i > 0" :style="hConnectorStyle(i)"></div>
            <!-- Circle -->
            <div :style="circleStyle(item, i)">
              <template v-if="item.status === 'completed'">
                <svg :width="circleIconSize" :height="circleIconSize" viewBox="0 0 24 24" fill="none">
                  <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </template>
              <template v-else-if="showNumbers">
                {{ i + 1 }}
              </template>
            </div>
            <!-- Connector after (except last) -->
            <div v-if="i < items.length - 1" :style="hConnectorStyle(i + 1)"></div>
          </div>
          <!-- Title + Description -->
          <div :style="labelStyle">
            <div :style="stepTitleStyle" :data-olo-editable="`items.${i}.title`">{{ item.title || 'Passaggio' }}</div>
            <div v-if="showDesc && item.description" :style="stepDescStyle" :data-olo-editable="`items.${i}.description`">{{ item.description }}</div>
          </div>
        </div>
      </div>
    </template>

    <!-- Vertical layout -->
    <template v-else>
      <div style="position:relative">
        <div
          v-for="(item, i) in items"
          :key="item.id || i"
          :style="vStepStyle"
        >
          <!-- Circle + vertical connector -->
          <div :style="vCircleCol">
            <div :style="circleStyle(item, i)">
              <template v-if="item.status === 'completed'">
                <svg :width="circleIconSize" :height="circleIconSize" viewBox="0 0 24 24" fill="none">
                  <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </template>
              <template v-else-if="showNumbers">
                {{ i + 1 }}
              </template>
            </div>
            <!-- Vertical connector -->
            <div v-if="i < items.length - 1" :style="vConnectorStyle(i)"></div>
          </div>
          <!-- Content -->
          <div :style="vContentStyle">
            <div :style="stepTitleStyle" :data-olo-editable="`items.${i}.title`">{{ item.title || 'Passaggio' }}</div>
            <div v-if="showDesc && item.description" :style="stepDescStyle" :data-olo-editable="`items.${i}.description`">{{ item.description }}</div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const defaults = {
  items: [
    { id: 'pt-1', title: 'Ordine ricevuto', description: 'Il tuo ordine è stato confermato.', icon: 'check', status: 'completed' },
    { id: 'pt-2', title: 'In preparazione', description: 'Stiamo preparando il tuo ordine.', icon: 'settings', status: 'active' },
    { id: 'pt-3', title: 'Spedito', description: 'Il pacco è in viaggio.', icon: 'cart', status: 'pending' },
    { id: 'pt-4', title: 'Consegnato', description: 'Consegna completata.', icon: 'home', status: 'pending' },
  ],
  layout: 'horizontal',
  connector_style: 'line',
  connector_color: '',
  completed_color: '',
  active_color: '',
  pending_color: '',
  text_color: 'var(--olo-color-text, #374151)',
  show_description: true,
  show_numbers: true,
  circle_size: '40',
  font_size: '14',
  gap: '0',
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({ ...defaults, ...props.settings }));

const items = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw)) return raw;
  return [];
});

const showNumbers = computed(() => {
  const v = s.value.show_numbers;
  return v && v !== 'false' && v !== '0';
});

const showDesc = computed(() => {
  const v = s.value.show_description;
  return v && v !== 'false' && v !== '0';
});

const cSize = computed(() => parseInt(s.value.circle_size) || 40);
const circleIconSize = computed(() => Math.round(cSize.value * 0.45));
const fSize = computed(() => (parseInt(s.value.font_size) || 14) + 'px');

function getStatusColor(status) {
  switch (status) {
    // completato = semantico success, attivo = primario brand (era #3b82f6),
    // in attesa = grigio tenue neutro.
    case 'completed': return resolveColor(s.value.completed_color, TOKENS.success.fg);
    case 'active': return resolveColor(s.value.active_color, TOKENS.primary);
    default: return resolveColor(s.value.pending_color, TOKENS.textFaint);
  }
}

function circleStyle(item, i) {
  const color = getStatusColor(item.status);
  const size = cSize.value;
  const st = {
    width: size + 'px',
    height: size + 'px',
    borderRadius: '50%',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    flexShrink: '0',
    fontWeight: '700',
    fontSize: (size * 0.38) + 'px',
    color: TOKENS.onPrimary,
    background: color,
    position: 'relative',
    zIndex: '2',
    transition: 'all 0.3s',
  };
  if (item.status === 'active') {
    st.boxShadow = '0 0 0 4px color-mix(in srgb, ' + color + ' 25%, transparent)';
  }
  if (item.status === 'pending') {
    st.background = 'transparent';
    st.border = '3px solid ' + color;
    st.color = color;
  }
  return st;
}

const connectorBorderStyle = computed(() => {
  const style = s.value.connector_style || 'line';
  if (style === 'dashed') return 'dashed';
  if (style === 'dotted') return 'dotted';
  return 'solid';
});

// --- Horizontal ---
const wrapStyle = computed(() => ({
  padding: '16px',
  minHeight: '60px',
}));

const hStepStyle = computed(() => ({
  flex: '1',
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  position: 'relative',
}));

function hConnectorStyle(i) {
  return {
    flex: '1',
    height: '0',
    borderTop: '2px ' + connectorBorderStyle.value + ' ' + resolveColor(s.value.connector_color, TOKENS.border),
    alignSelf: 'center',
  };
}

const labelStyle = computed(() => ({
  textAlign: 'center',
  marginTop: '8px',
  padding: '0 4px',
}));

const stepTitleStyle = computed(() => ({
  color: s.value.text_color || 'var(--olo-color-text, #374151)',
  fontSize: fSize.value,
  fontWeight: '600',
  lineHeight: '1.3',
}));

const stepDescStyle = computed(() => ({
  color: s.value.text_color || 'var(--olo-color-text, #374151)',
  fontSize: '12px',
  opacity: '0.7',
  marginTop: '2px',
  lineHeight: '1.4',
}));

// --- Vertical ---
const vStepStyle = computed(() => ({
  display: 'flex',
  gap: '16px',
  position: 'relative',
}));

const vCircleCol = computed(() => ({
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  flexShrink: '0',
}));

function vConnectorStyle(i) {
  return {
    width: '0',
    flex: '1',
    minHeight: '24px',
    borderLeft: '2px ' + connectorBorderStyle.value + ' ' + resolveColor(s.value.connector_color, TOKENS.border),
    marginTop: '4px',
    marginBottom: '4px',
  };
}

const vContentStyle = computed(() => ({
  paddingBottom: '24px',
  paddingTop: (cSize.value / 2 - 8) + 'px',
}));
</script>
