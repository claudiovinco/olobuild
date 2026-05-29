<template>
  <div style="padding:10px;background:var(--olo-color-surface-alt, #f6f7f9);border-radius:8px;min-height:60px;">
    <div style="font-size:13px;font-weight:700;color:var(--olo-color-text, #1f2937);margin-bottom:12px;">{{ t('Filtri Prodotti') }}</div>

    <!-- Prezzo -->
    <div style="margin-bottom:14px;">
      <div style="font-size:12px;font-weight:600;color:var(--olo-color-text-soft, #6b7280);margin-bottom:6px;">{{ t('Prezzo') }}</div>
      <div style="display:flex;align-items:center;gap:8px;">
        <div style="flex:1;height:4px;background:var(--olo-color-surface-alt, #f6f7f9);border-radius:2px;position:relative;">
          <div style="position:absolute;left:10%;right:30%;height:100%;background:var(--olo-color-primary, #e1474f);border-radius:2px;"></div>
        </div>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--olo-color-text-faint, #94a3b8);margin-top:4px;">
        <span>€ 0</span>
        <span>€ 500</span>
      </div>
    </div>

    <!-- Categoria -->
    <div style="margin-bottom:14px;">
      <div style="font-size:12px;font-weight:600;color:var(--olo-color-text-soft, #6b7280);margin-bottom:6px;">{{ t('Categoria') }}</div>
      <div v-for="cat in ['Abbigliamento', 'Accessori', 'Elettronica']" :key="cat" style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
        <div style="width:14px;height:14px;border:2px solid var(--olo-color-border, #e5e7eb);border-radius:3px;"></div>
        <span style="font-size:12px;color:var(--olo-color-text-soft, #6b7280);">{{ cat }}</span>
      </div>
    </div>

    <!-- Attributi -->
    <div>
      <div style="font-size:12px;font-weight:600;color:var(--olo-color-text-soft, #6b7280);margin-bottom:6px;">{{ t('Attributi') }}</div>
      <div style="display:flex;flex-wrap:wrap;gap:4px;">
        <span
          v-for="attr in ['Rosso', 'Blu', 'Verde', 'M', 'L']"
          :key="attr"
          style="font-size:11px;padding:2px 8px;background:var(--olo-color-surface-alt, #f6f7f9);border-radius:12px;color:var(--olo-color-text-soft, #6b7280);"
        >{{ attr }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});
</script>
