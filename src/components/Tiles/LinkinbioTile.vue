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
          class="olo-lib-avatar-ph"
          :style="{
            width: '80px', height: '80px', borderRadius: '50%',
            background: TOKENS.surfaceAlt, display: 'flex', alignItems: 'center', justifyContent: 'center',
            margin: s.text_align === 'center' ? '0 auto 12px' : '0 0 12px',
            color: TOKENS.textFaint,
          }"
        >
          <span style="width:38px;height:38px;display:inline-flex" v-html="avatarPlaceholderSvg"></span>
        </div>
        <div :style="{ fontWeight: '700', fontSize: '1.2em', color: resolveColor(s.profile_name_color, TOKENS.text) }" data-olo-editable="profile_name">
          {{ s.profile_name || 'Il tuo nome' }}
        </div>
        <div v-if="s.profile_bio" :style="{ color: resolveColor(s.bio_color, TOKENS.textSoft), fontSize: '0.9em', marginTop: '4px' }" data-olo-editable="profile_bio">
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
          <span v-if="item.icon" class="olo-lib-icon" style="margin-right:8px;opacity:0.7;display:inline-flex;width:1em;height:1em;vertical-align:-0.1em;">
            <span v-if="iconSvg(item.icon)" v-html="iconSvg(item.icon)" style="width:100%;height:100%;display:inline-flex"></span>
            <template v-else>{{ item.icon }}</template>
          </span>
          <span :data-olo-editable="'items.' + i + '.title'">{{ item.title || 'Link' }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

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
  link_color: '',
  link_bg: '',
  link_hover_bg: '',
  link_border_radius: '12',
  link_padding: '14',
  gap: '12',
  text_align: 'center',
  profile_name_color: '',
  bio_color: '',
  background_color: '',
  background_gradient: '',
  show_social_icons: false,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const links = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw)) return raw;
  return [];
});

function iconSvg(name) {
  return (name && iconsSvg[name]) ? iconsSvg[name] : '';
}

// Placeholder avatar = icona SVG "user" (no più emoji)
const avatarPlaceholderSvg = computed(() => iconsSvg['user'] || iconsSvg['users'] || '');

const outerStyle = computed(() => {
  const bg = s.value.background_gradient || resolveColor(s.value.background_color, TOKENS.surfaceAlt);
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
  const linkColor = resolveColor(s.value.link_color, TOKENS.primary);
  if (st === 'filled') {
    base.background = resolveColor(s.value.link_bg, TOKENS.surface);
    base.color = linkColor;
    base.border = '1px solid rgba(0,0,0,0.08)';
    base.boxShadow = '0 1px 3px rgba(0,0,0,0.06)';
  } else if (st === 'outline') {
    base.background = 'transparent';
    base.color = linkColor;
    base.border = '2px solid ' + linkColor;
  } else {
    base.background = 'transparent';
    base.color = linkColor;
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
.olo-lib-link:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
.olo-lib-icon :deep(svg),
.olo-lib-avatar-ph :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
</style>
