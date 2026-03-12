<template>
  <div class="olo-linkinbio-tile" :style="outerStyle">
    <div :style="innerStyle">
      <!-- Profile -->
      <div :style="{ textAlign: s.text_align, marginBottom: '20px' }">
        <div
          v-if="s.profile_image"
          :style="{
            width: '80px', height: '80px', borderRadius: '50%', overflow: 'hidden',
            margin: s.text_align === 'center' ? '0 auto 12px' : '0 0 12px',
          }"
        >
          <img :src="s.profile_image" alt="" style="width:100%;height:100%;object-fit:cover;" />
        </div>
        <div
          v-else
          :style="{
            width: '80px', height: '80px', borderRadius: '50%',
            background: '#e5e7eb', display: 'flex', alignItems: 'center', justifyContent: 'center',
            margin: s.text_align === 'center' ? '0 auto 12px' : '0 0 12px',
            fontSize: '32px', color: '#9ca3af',
          }"
        >&#128100;</div>
        <div :style="{ fontWeight: '700', fontSize: '1.2em', color: s.profile_name_color || 'var(--olo-color-text, #374151)' }" data-olo-editable="profile_name">
          {{ s.profile_name || 'Il tuo nome' }}
        </div>
        <div v-if="s.profile_bio" :style="{ color: s.bio_color || '#6b7280', fontSize: '0.9em', marginTop: '4px' }" data-olo-editable="profile_bio">
          {{ s.profile_bio }}
        </div>
      </div>

      <!-- Links -->
      <div :style="{ display: 'flex', flexDirection: 'column', gap: s.gap + 'px' }">
        <div
          v-for="(item, i) in links"
          :key="i"
          :style="linkBtnStyle(item)"
          class="olo-lib-link"
        >
          <span v-if="item.icon" style="margin-right:8px;opacity:0.7;">{{ item.icon }}</span>
          <span :data-olo-editable="'items.' + i + '.title'">{{ item.title || 'Link' }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  items: [
    { id: 'lib-1', title: 'Sito web', url: '#', icon: '', image_url: '', style: 'filled' },
    { id: 'lib-2', title: 'Portfolio', url: '#', icon: '', image_url: '', style: 'filled' },
    { id: 'lib-3', title: 'Instagram', url: '#', icon: '', image_url: '', style: 'filled' },
    { id: 'lib-4', title: 'Contattami', url: '#', icon: '', image_url: '', style: 'outline' },
  ],
  profile_image: '',
  profile_name: 'Il tuo nome',
  profile_bio: 'Una breve descrizione qui',
  max_width: '420',
  link_color: '#1e87f0',
  link_bg: '#ffffff',
  link_hover_bg: '#f3f4f6',
  link_border_radius: '12',
  link_padding: '14',
  gap: '12',
  text_align: 'center',
  profile_name_color: 'var(--olo-color-text, #374151)',
  bio_color: '#6b7280',
  background_color: '#f9fafb',
  background_gradient: '',
  show_social_icons: false,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const links = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw)) return raw;
  return [];
});

const outerStyle = computed(() => {
  const bg = s.value.background_gradient || s.value.background_color || '#f9fafb';
  return {
    background: bg,
    padding: '32px 16px',
    display: 'flex',
    justifyContent: 'center',
    minHeight: '200px',
  };
});

const innerStyle = computed(() => ({
  width: '100%',
  maxWidth: (parseInt(s.value.max_width) || 420) + 'px',
}));

function linkBtnStyle(item) {
  const st = item.style || 'filled';
  const radius = ((v => isNaN(v) ? 12 : v)(parseInt(s.value.link_border_radius))) + 'px';
  const padding = (parseInt(s.value.link_padding) || 14) + 'px';
  const base = {
    display: 'block',
    width: '100%',
    textAlign: s.value.text_align,
    borderRadius: radius,
    padding: padding,
    fontSize: '0.95em',
    fontWeight: '500',
    cursor: 'pointer',
    transition: 'all 0.2s ease',
    boxSizing: 'border-box',
    textDecoration: 'none',
  };
  if (st === 'filled') {
    base.background = s.value.link_bg || '#ffffff';
    base.color = s.value.link_color || '#1e87f0';
    base.border = '1px solid rgba(0,0,0,0.08)';
    base.boxShadow = '0 1px 3px rgba(0,0,0,0.06)';
  } else if (st === 'outline') {
    base.background = 'transparent';
    base.color = s.value.link_color || '#1e87f0';
    base.border = '2px solid ' + (s.value.link_color || '#1e87f0');
  } else {
    base.background = 'transparent';
    base.color = s.value.link_color || '#1e87f0';
    base.border = 'none';
    base.textDecoration = 'underline';
  }
  return base;
}
</script>

<style scoped>
.olo-lib-link:hover {
  opacity: 0.85;
  transform: translateY(-1px);
}
</style>
