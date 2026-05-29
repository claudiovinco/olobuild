<template>
  <div class="mb-py-4 mb-px-4">
    <div :style="containerStyle">
      <div v-if="s.title" :style="{ fontWeight: 700, fontSize: '16px', color: s.title_color || 'var(--olo-color-text, #374151)', marginBottom: '12px', borderBottom: '1px solid var(--olo-color-border, #E5E7EB)', paddingBottom: '8px' }">
        {{ s.title }}
      </div>
      <div :style="{ listStyleType: listType }">
        <div v-for="(item, i) in mockItems" :key="i"
          :style="{ paddingLeft: (item.level * indent) + 'px', marginBottom: '6px', display: 'flex', alignItems: 'baseline', gap: '6px' }">
          <span v-if="s.list_style === 'numbered'" :style="{ color: s.text_color || 'var(--olo-color-text, #374151)', fontSize: fontSize + 'px', opacity: 0.5, flexShrink: 0 }">{{ item.num }}</span>
          <span v-else-if="s.list_style === 'bullets'" :style="{ width: '5px', height: '5px', borderRadius: '50%', background: s.link_color || 'var(--olo-color-primary, #e1474f)', flexShrink: 0, marginTop: '6px' }"></span>
          <span :style="{ color: s.link_color || 'var(--olo-color-primary, #e1474f)', fontSize: fontSize + 'px', cursor: 'pointer' }">{{ item.text }}</span>
        </div>
      </div>
      <div :style="{ marginTop: '12px', fontSize: '10px', color: 'var(--olo-color-text-soft, #6b7280)', fontStyle: 'italic' }">
        {{ t('Auto-generato dagli heading della pagina') }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  title: 'Sommario',
  max_depth: '3',
  list_style: 'numbered',
  text_color: 'var(--olo-color-text, #374151)',
  link_color: 'var(--olo-color-primary, #e1474f)',
  title_color: 'var(--olo-color-text, #374151)',
  font_size: '15',
  indent: '20',
  sticky: false,
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const fontSize = computed(() => parseInt(s.value.font_size) || 15);
const indent = computed(() => parseInt(s.value.indent) || 20);
const listType = computed(() => s.value.list_style === 'numbered' ? 'decimal' : s.value.list_style === 'bullets' ? 'disc' : 'none');

const mockItems = computed(() => {
  const depth = parseInt(s.value.max_depth) || 3;
  const items = [
    { level: 0, text: 'Introduzione', num: '1.' },
    { level: 0, text: 'Come funziona', num: '2.' },
  ];
  if (depth >= 2) {
    items.push({ level: 1, text: 'Passo 1: Configurazione', num: '2.1' });
    items.push({ level: 1, text: 'Passo 2: Personalizzazione', num: '2.2' });
  }
  if (depth >= 3) {
    items.push({ level: 2, text: 'Opzioni avanzate', num: '2.2.1' });
  }
  items.push({ level: 0, text: 'Conclusioni', num: '3.' });
  return items;
});

const containerStyle = computed(() => {
  const st = { padding: '16px', background: 'var(--olo-color-muted, #F3F4F6)', borderRadius: '8px', border: '1px solid var(--olo-color-border, #E5E7EB)' };
  if (s.value.sticky) st.position = 'sticky';
  return st;
});
</script>
