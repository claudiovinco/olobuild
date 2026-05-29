<template>
  <div class="olo-authorbox" :style="containerStyle">
    <div :style="layoutStyle">
      <!-- Avatar -->
      <div v-if="s.show_avatar" :style="avatarWrapStyle">
        <div :style="avatarStyle"></div>
      </div>

      <!-- Info -->
      <div :style="infoStyle">
        <!-- Name -->
        <component
          v-if="s.show_name"
          :is="nameTag"
          :style="nameStyle"
        >
          Nome Autore
        </component>

        <!-- Role -->
        <div v-if="s.show_role" :style="roleStyle">
          {{ t('Amministratore') }}
        </div>

        <!-- Bio -->
        <p v-if="s.show_bio" :style="bioStyle">
          {{ t('Breve biografia dell\'autore del post. Questo testo viene estratto automaticamente dal profilo WordPress dell\'autore.') }}
        </p>

        <!-- Post count -->
        <div v-if="s.show_post_count" :style="countStyle">
          {{ t('12 articoli pubblicati') }}
        </div>

        <!-- Website -->
        <div v-if="s.show_website" :style="linkStyle">
          {{ t('www.esempio.it') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

// Default colore = '' ⇒ risolti token-first a runtime (allineato a config authorbox.js)
const defaults = {
  layout: 'horizontal',
  avatar_size: '80',
  show_avatar: true,
  show_name: true,
  show_bio: true,
  show_role: false,
  show_post_count: false,
  show_website: false,
  name_tag: 'h3',
  name_color: '',
  name_size: '20',
  name_weight: '700',
  bio_color: '',
  bio_size: '14',
  role_color: '',
  role_size: '13',
  link_color: '',
  count_color: '',
  background_color: '',
  border_radius: '8',
  padding: '20',
  avatar_border_radius: '50',
  avatar_border_width: '0',
  avatar_border_color: '',
  border_width: '0',
  border_color: '',
  gap: '16',
  text_align: 'left',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const isVertical = computed(() => s.value.layout === 'vertical');
const nameTag = computed(() => {
  const valid = ['h3', 'h4', 'h5', 'div'];
  return valid.includes(s.value.name_tag) ? s.value.name_tag : 'h3';
});

const avSize = computed(() => parseInt(s.value.avatar_size) || 80);
const gap = computed(() => parseInt(s.value.gap) || 16);
const padding = computed(() => parseInt(s.value.padding) || 20);
const radius = computed(() => (v => isNaN(v) ? 8 : v)(parseInt(s.value.border_radius)));

const containerStyle = computed(() => {
  const st = {
    background: resolveColor(s.value.background_color, TOKENS.surfaceAlt),
    borderRadius: radius.value + 'px',
    padding: padding.value + 'px',
  };
  const bw = parseInt(s.value.border_width);
  if (bw > 0) {
    st.border = bw + 'px solid ' + resolveColor(s.value.border_color, TOKENS.border);
  }
  return st;
});

const layoutStyle = computed(() => {
  if (isVertical.value) {
    return {
      display: 'flex',
      flexDirection: 'column',
      alignItems: 'center',
      gap: gap.value + 'px',
      textAlign: 'center',
    };
  }
  return {
    display: 'flex',
    alignItems: 'flex-start',
    gap: gap.value + 'px',
    textAlign: s.value.text_align || 'left',
  };
});

const avatarWrapStyle = computed(() => ({
  flexShrink: '0',
}));

const avatarStyle = computed(() => {
  const st = {
    width: avSize.value + 'px',
    height: avSize.value + 'px',
    borderRadius: ((v => isNaN(v) ? 50 : v)(parseInt(s.value.avatar_border_radius))) + '%',
    background: TOKENS.border,
    flexShrink: '0',
  };
  const abw = parseInt(s.value.avatar_border_width);
  if (abw > 0) {
    st.border = abw + 'px solid ' + resolveColor(s.value.avatar_border_color, TOKENS.primary);
  }
  return st;
});

const infoStyle = computed(() => ({
  flex: '1',
  minWidth: '0',
}));

const nameStyle = computed(() => ({
  margin: '0 0 4px 0',
  padding: '0',
  fontSize: (parseInt(s.value.name_size) || 20) + 'px',
  fontWeight: s.value.name_weight || '700',
  color: resolveColor(s.value.name_color, TOKENS.text),
  lineHeight: '1.3',
}));

const roleStyle = computed(() => ({
  fontSize: (parseInt(s.value.role_size) || 13) + 'px',
  color: resolveColor(s.value.role_color, TOKENS.primary),
  marginBottom: '8px',
  fontWeight: '500',
}));

const bioStyle = computed(() => ({
  margin: '8px 0',
  fontSize: (parseInt(s.value.bio_size) || 14) + 'px',
  color: resolveColor(s.value.bio_color, TOKENS.text),
  lineHeight: '1.5',
}));

const countStyle = computed(() => ({
  fontSize: '13px',
  color: resolveColor(s.value.count_color, TOKENS.text),
  marginTop: '6px',
}));

const linkStyle = computed(() => ({
  fontSize: '13px',
  color: resolveColor(s.value.link_color, TOKENS.primary),
  marginTop: '4px',
  textDecoration: 'underline',
}));
</script>
