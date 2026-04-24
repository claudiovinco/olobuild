<template>
  <dl class="olo-desclist-preview" :style="dlStyle">
    <template v-for="(item, i) in parsedItems" :key="i">
      <!-- Inline layout -->
      <div v-if="s.layout === 'inline'" class="olo-dl-item" :style="itemStyle(i)">
        <div class="olo-dl-row" style="display:flex;align-items:baseline;gap:12px">
          <span v-if="showIcon && item.icon" class="olo-dl-icon" :style="iconWrapStyle" v-html="renderIcon(item.icon)"></span>
          <dt class="olo-dl-term" :style="termStyle" :data-olo-editable="'items.' + i + '.term'">{{ item.term }}</dt>
          <dd class="olo-dl-def" :style="{ ...defStyle, flex: 1, minWidth: 0 }" :data-olo-editable="'items.' + i + '.definition'" data-olo-multiline>{{ item.definition }}</dd>
          <span v-if="item.link" class="olo-dl-link-badge" :title="t('Link')">{{ t('&#x1F517;') }}</span>
        </div>
      </div>

      <!-- Grid layout -->
      <div v-else-if="s.layout === 'grid'" class="olo-dl-item" :style="itemStyle(i)">
        <div class="olo-dl-row" :style="gridRowStyle">
          <span v-if="showIcon && item.icon" class="olo-dl-icon" :style="{ ...iconWrapStyle, gridRow: 'span 2', alignSelf: 'start', paddingTop: '2px' }" v-html="renderIcon(item.icon)"></span>
          <dt class="olo-dl-term" :style="termStyle" :data-olo-editable="'items.' + i + '.term'">{{ item.term }}<span v-if="item.link" class="olo-dl-link-badge" :title="t('Link')"> {{ t('&#x1F517;') }}</span></dt>
          <dd class="olo-dl-def" :style="defStyle" :data-olo-editable="'items.' + i + '.definition'" data-olo-multiline>{{ item.definition }}</dd>
        </div>
      </div>

      <!-- Stacked layout (default) -->
      <div v-else class="olo-dl-item" :style="itemStyle(i)">
        <div class="olo-dl-row" style="display:flex;align-items:flex-start;gap:12px">
          <span v-if="showIcon && item.icon" class="olo-dl-icon" :style="iconWrapStyle" v-html="renderIcon(item.icon)"></span>
          <div style="flex:1;min-width:0">
            <dt class="olo-dl-term" :style="termStyle" :data-olo-editable="'items.' + i + '.term'">{{ item.term }}<span v-if="item.link" class="olo-dl-link-badge" :title="t('Link')"> {{ t('&#x1F517;') }}</span></dt>
            <dd class="olo-dl-def" :style="{ ...defStyle, marginTop: '4px' }" :data-olo-editable="'items.' + i + '.definition'" data-olo-multiline>{{ item.definition }}</dd>
          </div>
        </div>
      </div>
    </template>
  </dl>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  items: [
    { term: 'Framework', definition: 'Vue.js 3 con Composition API', icon: 'code' },
    { term: 'Linguaggio', definition: 'PHP 7.4+ con WordPress', icon: 'server' },
    { term: 'Build Tool', definition: 'Vite 5', icon: 'bolt' },
  ],
  layout: 'stacked',
  show_icon: true,
  icon_color: '',
  icon_size: '20',
  term_color: '',
  term_font_size: '15',
  term_font_weight: '600',
  definition_color: '',
  definition_font_size: '14',
  separator: true,
  border_color: '',
  spacing: '16',
  striped: false,
  striped_color: 'rgba(255,255,255,0.05)',
  shadow: 'none',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const showIcon = computed(() => !!s.value.show_icon);
const iconSize = computed(() => parseInt(s.value.icon_size) || 20);
const spacing = computed(() => parseInt(s.value.spacing) || 16);

const parsedItems = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw)) {
    return raw.filter(item => item && item.term);
  }
  if (typeof raw === 'string' && raw) {
    return raw.split('\n').map(l => l.trim()).filter(Boolean).map(line => {
      const parts = line.split('|');
      if (parts.length >= 2) return { term: parts[0].trim(), definition: parts.slice(1).join('|').trim(), icon: '' };
      return null;
    }).filter(Boolean);
  }
  return [];
});

const dlStyle = computed(() => ({
  margin: 0,
  padding: 0,
  listStyle: 'none',
}));

const gridRowStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: showIcon.value ? 'auto auto 1fr' : 'auto 1fr',
  gap: '4px 24px',
  alignItems: 'baseline',
}));

const iconWrapStyle = computed(() => ({
  flexShrink: 0,
  display: 'inline-flex',
  alignItems: 'center',
  color: s.value.icon_color || 'var(--olo-color-primary, #6366F1)',
}));

const termStyle = computed(() => {
  const st = {
    fontSize: (parseInt(s.value.term_font_size) || 15) + 'px',
    fontWeight: s.value.term_font_weight || '600',
    lineHeight: '1.4',
    margin: 0,
  };
  if (s.value.term_color) st.color = s.value.term_color;
  return st;
});

const defStyle = computed(() => {
  const st = {
    fontSize: (parseInt(s.value.definition_font_size) || 14) + 'px',
    lineHeight: '1.6',
    margin: 0,
    whiteSpace: 'pre-wrap',
  };
  if (s.value.definition_color) st.color = s.value.definition_color;
  return st;
});

function itemStyle(index) {
  const st = { padding: spacing.value + 'px 16px' };
  if (s.value.separator && index > 0) {
    st.borderTop = '1px solid ' + (s.value.border_color || 'var(--olo-color-border, #E5E7EB)');
  }
  if (s.value.striped && index % 2 === 1) {
    st.background = s.value.striped_color || 'rgba(255,255,255,0.05)';
  }
  return st;
}

function renderIcon(icon) {
  if (!icon) return '';
  const sz = iconSize.value;
  const c = s.value.icon_color || 'currentColor';

  // UIkit icon from the full library
  const rawSvg = iconsSvg[icon];
  if (rawSvg) {
    // Replace width/height and inject color
    return rawSvg
      .replace(/width="20"/, `width="${sz}"`)
      .replace(/height="20"/, `height="${sz}"`)
      .replace(/currentColor/g, c);
  }

  // Emoji or text fallback
  return `<span style="font-size:${sz}px;line-height:1">${icon}</span>`;
}
</script>

<style scoped>
.olo-desclist-preview {
  list-style: none;
}
.olo-dl-link-badge {
  font-size: 12px;
  opacity: 0.5;
}
</style>
