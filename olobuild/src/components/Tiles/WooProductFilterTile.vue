<template>
  <div style="padding:10px;background:#f9fafb;border-radius:8px;min-height:60px;">
    <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:12px;">Filtri Prodotti</div>

    <!-- Prezzo -->
    <div style="margin-bottom:14px;">
      <div style="font-size:12px;font-weight:600;color:#4B5563;margin-bottom:6px;">Prezzo</div>
      <div style="display:flex;align-items:center;gap:8px;">
        <div style="flex:1;height:4px;background:#E5E7EB;border-radius:2px;position:relative;">
          <div style="position:absolute;left:10%;right:30%;height:100%;background:var(--olo-color-primary, #6366F1);border-radius:2px;"></div>
        </div>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:11px;color:#9CA3AF;margin-top:4px;">
        <span>€ 0</span>
        <span>€ 500</span>
      </div>
    </div>

    <!-- Categoria -->
    <div style="margin-bottom:14px;">
      <div style="font-size:12px;font-weight:600;color:#4B5563;margin-bottom:6px;">Categoria</div>
      <div v-for="cat in ['Abbigliamento', 'Accessori', 'Elettronica']" :key="cat" style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
        <div style="width:14px;height:14px;border:2px solid #D1D5DB;border-radius:3px;"></div>
        <span style="font-size:12px;color:#6B7280;">{{ cat }}</span>
      </div>
    </div>

    <!-- Attributi -->
    <div>
      <div style="font-size:12px;font-weight:600;color:#4B5563;margin-bottom:6px;">Attributi</div>
      <div style="display:flex;flex-wrap:wrap;gap:4px;">
        <span
          v-for="attr in ['Rosso', 'Blu', 'Verde', 'M', 'L']"
          :key="attr"
          style="font-size:11px;padding:2px 8px;background:#E5E7EB;border-radius:12px;color:#6B7280;"
        >{{ attr }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});
</script>
