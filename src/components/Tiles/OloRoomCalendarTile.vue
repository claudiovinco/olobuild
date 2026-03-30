<template>
  <div>
    <h3 style="font-size:18px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 12px">Disponibilità e Prenotazione</h3>
    <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden">
      <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f9fafb;border-bottom:1px solid #e5e7eb">
        <span style="font-size:11px;color:#6b7280;cursor:pointer">&lsaquo; Prec</span>
        <span style="font-size:14px;font-weight:600;color:var(--olo-color-text, #374151)">Febbraio 2026</span>
        <span style="font-size:11px;color:#6b7280;cursor:pointer">Succ &rsaquo;</span>
      </div>
      <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center">
        <span v-for="d in ['Lun','Mar','Mer','Gio','Ven','Sab','Dom']" :key="d" style="font-size:10px;color:#9ca3af;padding:6px 0;border-bottom:1px solid #f3f4f6">{{ d }}</span>
        <span v-for="i in 5" :key="'e'+i" style="padding:8px;font-size:11px"></span>
        <span v-for="n in 28" :key="n" :style="cellStyle(n)">{{ n }}</span>
      </div>
    </div>
    <div style="margin-top:8px;font-size:11px;color:#9ca3af;text-align:center">Clicca su un giorno per prenotare uno slot</div>
  </div>
</template>

<script setup>
defineProps({ settings: { type: Object, default: () => ({}) } });
function cellStyle(n) {
  const base = { padding: '8px 2px', fontSize: '11px', fontWeight: '500', cursor: 'pointer', borderBottom: '1px solid #f3f4f6' };
  if (n % 7 === 0) return { ...base, color: '#d1d5db', background: '#fafafa' };
  if (n === 15 || n === 16) return { ...base, background: '#dbeafe', color: '#1e40af', fontWeight: '700', borderRadius: '4px' };
  return { ...base, color: '#374151' };
}
</script>
