<template>
  <div>
    <h3 style="font-size:16px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 10px">Condividi</h3>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <span v-for="btn in buttons" :key="btn.label" :style="{...btnStyle, background: btn.bg}">{{ btn.label }}</span>
    </div>
  </div>
</template>

<script setup>
defineProps({ settings: { type: Object, default: () => ({}) } });
const buttons = [
  { label: 'Facebook', bg: '#1877f2' },
  { label: 'WhatsApp', bg: '#25d366' },
  { label: 'Email', bg: '#6b7280' },
  { label: 'Copia link', bg: '#374151' },
];
const btnStyle = { color: '#fff', padding: '6px 14px', borderRadius: '6px', fontSize: '12px', fontWeight: '600', cursor: 'pointer' };
</script>
