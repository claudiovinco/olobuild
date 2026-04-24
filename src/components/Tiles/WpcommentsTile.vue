<template>
  <div class="olo-wpcomments">
    <!-- Title -->
    <component
      v-if="s.show_title"
      :is="s.title_tag"
      class="mb-font-bold mb-mb-4"
      :style="titleStyle"
      data-olo-editable="title_text"
    >
      {{ s.title_text }} (2)
    </component>

    <!-- Comment list -->
    <div class="mb-space-y-0">
      <!-- Comment 1 -->
      <div class="mb-py-4" :style="commentBorderStyle">
        <div class="mb-flex mb-gap-3">
          <div
            v-if="s.show_avatar"
            class="mb-flex-shrink-0 mb-bg-blue-500 mb-flex mb-items-center mb-justify-center mb-text-white mb-font-bold mb-text-sm"
            :style="avatarStyle"
          >{{ t('MR') }}</div>
          <div class="mb-flex-1 mb-min-w-0">
            <div class="mb-flex mb-items-center mb-gap-2 mb-mb-1">
              <span class="mb-font-semibold mb-text-sm" :style="authorStyle">{{ t('Mario Rossi') }}</span>
              <span v-if="s.show_date" class="mb-text-xs" :style="dateStyle">{{ t('2 ore fa') }}</span>
            </div>
            <p class="mb-text-sm mb-leading-relaxed mb-mb-1" :style="textStyle">
              {{ t('Ottimo articolo, molto utile e ben scritto. Complimenti per la chiarezza espositiva.') }}
            </p>
            <a v-if="s.show_reply_link" href="#" class="mb-text-xs mb-no-underline" :style="linkStyle">{{ t('Rispondi') }}</a>
          </div>
        </div>
      </div>

      <!-- Comment 2 (threaded reply) -->
      <div class="mb-py-4 mb-pl-12" :style="commentBorderStyle">
        <div class="mb-flex mb-gap-3">
          <div
            v-if="s.show_avatar"
            class="mb-flex-shrink-0 mb-bg-green-500 mb-flex mb-items-center mb-justify-center mb-text-white mb-font-bold mb-text-sm"
            :style="avatarStyle"
          >{{ t('LB') }}</div>
          <div class="mb-flex-1 mb-min-w-0">
            <div class="mb-flex mb-items-center mb-gap-2 mb-mb-1">
              <span class="mb-font-semibold mb-text-sm" :style="authorStyle">{{ t('Laura Bianchi') }}</span>
              <span v-if="s.show_date" class="mb-text-xs" :style="dateStyle">{{ t('1 ora fa') }}</span>
            </div>
            <p class="mb-text-sm mb-leading-relaxed mb-mb-1" :style="textStyle">
              {{ t('Concordo pienamente! Avrei una domanda riguardo al terzo punto.') }}
            </p>
            <a v-if="s.show_reply_link" href="#" class="mb-text-xs mb-no-underline" :style="linkStyle">{{ t('Rispondi') }}</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Comment form preview -->
    <div v-if="s.show_form" class="mb-mt-6 mb-p-4 mb-rounded" :style="formStyle">
      <h4 class="mb-font-semibold mb-mb-3 mb-text-sm" :style="titleStyle">{{ t('Lascia un commento') }}</h4>
      <div class="mb-space-y-3">
        <div class="mb-h-20 mb-rounded mb-border mb-border-gray-600 mb-bg-gray-800/50"></div>
        <div class="mb-flex mb-gap-3">
          <div class="mb-flex-1 mb-h-9 mb-rounded mb-border mb-border-gray-600 mb-bg-gray-800/50"></div>
          <div class="mb-flex-1 mb-h-9 mb-rounded mb-border mb-border-gray-600 mb-bg-gray-800/50"></div>
        </div>
        <button
          class="mb-px-4 mb-py-2 mb-rounded mb-text-sm mb-font-medium mb-text-white mb-bg-blue-600 mb-cursor-default"
        >{{ t('Invia commento') }}</button>
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
  show_title: true,
  title_text: 'Commenti',
  title_tag: 'h3',
  show_avatar: true,
  avatar_size: '48',
  show_date: true,
  show_reply_link: true,
  show_form: true,
  comments_per_page: '10',
  order: 'desc',
  title_color: '',
  text_color: '',
  author_color: '',
  date_color: '',
  link_color: '',
  form_background: '',
  border_color: '#e5e7eb',
  avatar_border_radius: '50',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const avatarStyle = computed(() => {
  const size = parseInt(s.value.avatar_size) || 48;
  return {
    width: size + 'px',
    height: size + 'px',
    borderRadius: ((v => isNaN(v) ? 50 : v)(parseInt(s.value.avatar_border_radius))) + '%',
  };
});

const titleStyle = computed(() => {
  const st = {};
  if (s.value.title_color) st.color = s.value.title_color;
  return st;
});

const textStyle = computed(() => {
  const st = {};
  if (s.value.text_color) st.color = s.value.text_color;
  return st;
});

const authorStyle = computed(() => {
  const st = {};
  if (s.value.author_color) st.color = s.value.author_color;
  return st;
});

const dateStyle = computed(() => {
  const st = { color: s.value.date_color || '#9CA3AF' };
  return st;
});

const linkStyle = computed(() => {
  const st = { color: s.value.link_color || '#60A5FA' };
  return st;
});

const commentBorderStyle = computed(() => {
  return {
    borderBottom: '1px solid ' + (s.value.border_color || '#e5e7eb'),
  };
});

const formStyle = computed(() => {
  const st = {};
  if (s.value.form_background) st.background = s.value.form_background;
  if (s.value.border_color) st.border = '1px solid ' + s.value.border_color;
  return st;
});
</script>

<style scoped>
.olo-wpcomments {
  padding: 8px 0;
  min-height: 80px;
}
</style>
