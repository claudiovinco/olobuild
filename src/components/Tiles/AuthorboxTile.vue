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

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

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
  name_color: 'var(--olo-color-text, #374151)',
  name_size: '20',
  name_weight: '700',
  bio_color: 'var(--olo-color-text, #374151)',
  bio_size: '14',
  role_color: 'var(--olo-color-primary, #6366F1)',
  role_size: '13',
  link_color: 'var(--olo-color-primary, #6366F1)',
  count_color: 'var(--olo-color-text, #374151)',
  background_color: 'var(--olo-color-muted, #F3F4F6)',
  border_radius: '8',
  padding: '20',
  avatar_border_radius: '50',
  avatar_border_width: '0',
  avatar_border_color: 'var(--olo-color-primary, #6366F1)',
  border_width: '0',
  border_color: 'var(--olo-color-border, #E5E7EB)',
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
    background: s.value.background_color || 'var(--olo-color-muted, #F3F4F6)',
    borderRadius: radius.value + 'px',
    padding: padding.value + 'px',
  };
  const bw = parseInt(s.value.border_width);
  if (bw > 0) {
    st.border = bw + 'px solid ' + (s.value.border_color || 'var(--olo-color-border, #E5E7EB)');
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
    background: 'var(--olo-color-border, #E5E7EB)',
    flexShrink: '0',
  };
  const abw = parseInt(s.value.avatar_border_width);
  if (abw > 0) {
    st.border = abw + 'px solid ' + (s.value.avatar_border_color || 'var(--olo-color-primary, #6366F1)');
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
  color: s.value.name_color || 'var(--olo-color-text, #374151)',
  lineHeight: '1.3',
}));

const roleStyle = computed(() => ({
  fontSize: (parseInt(s.value.role_size) || 13) + 'px',
  color: s.value.role_color || 'var(--olo-color-primary, #6366F1)',
  marginBottom: '8px',
  fontWeight: '500',
}));

const bioStyle = computed(() => ({
  margin: '8px 0',
  fontSize: (parseInt(s.value.bio_size) || 14) + 'px',
  color: s.value.bio_color || 'var(--olo-color-text, #374151)',
  lineHeight: '1.5',
}));

const countStyle = computed(() => ({
  fontSize: '13px',
  color: s.value.count_color || 'var(--olo-color-text, #374151)',
  marginTop: '6px',
}));

const linkStyle = computed(() => ({
  fontSize: '13px',
  color: s.value.link_color || 'var(--olo-color-primary, #6366F1)',
  marginTop: '4px',
  textDecoration: 'underline',
}));
</script>
