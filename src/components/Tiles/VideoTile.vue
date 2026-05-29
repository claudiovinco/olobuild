<template>
  <div>
    <!-- POSTER OVERLAY (when poster_image is set and not yet playing) -->
    <div v-if="s.poster_image && !playing" class="olo-video-poster mb-rounded-lg mb-overflow-hidden" @click="playing = true" style="cursor:pointer;position:relative;">
      <img :src="s.poster_image" :style="s.display_mode === 'cover' ? { width: '100%', height: (parseInt(s.cover_height) || 500) + 'px', objectFit: 'cover', display: 'block' } : { width: '100%', display: 'block' }" />
      <div v-if="s.show_play_icon !== false" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
        <svg :width="playSize" :height="playSize" viewBox="0 0 80 80">
          <circle cx="40" cy="40" r="38" fill="rgba(0,0,0,0.5)" stroke-width="2" :stroke="s.play_icon_color || '#fff'"/>
          <polygon points="32,24 32,56 58,40" :fill="s.play_icon_color || '#fff'"/>
        </svg>
      </div>
    </div>

    <!-- COVER MODE -->
    <div
      v-else-if="s.display_mode === 'cover'"
      class="mb-relative mb-overflow-hidden mb-rounded-lg"
      :style="{ height: (parseInt(s.cover_height) || 500) + 'px', background: '#111' }"
    >
      <video
        v-if="s.source_type === 'file' && s.file_url"
        :src="s.file_url"
        class="mb-absolute mb-inset-0 mb-w-full mb-h-full"
        style="object-fit:cover"
        muted autoplay loop
      ></video>
      <iframe
        v-else-if="embedUrl"
        :src="embedUrl"
        class="mb-absolute mb-inset-0 mb-w-full mb-h-full"
        frameborder="0" allow="autoplay" allowfullscreen
      ></iframe>
      <!-- Overlay -->
      <div
        v-if="parseFloat(s.overlay_opacity) > 0"
        class="mb-absolute mb-inset-0"
        :style="{ background: s.overlay_color, opacity: parseFloat(s.overlay_opacity) / 100 }"
      ></div>
      <!-- Overlay text -->
      <div
        v-if="s.overlay_text"
        class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center mb-text-white mb-text-2xl mb-font-bold"
        style="z-index:2; text-shadow:0 2px 8px rgba(0,0,0,.5)"
        data-olo-editable="overlay_text"
      >{{ s.overlay_text }}</div>
    </div>

    <!-- FILE MODE (native video) -->
    <div
      v-else-if="s.source_type === 'file' && s.file_url"
      class="mb-rounded-lg mb-overflow-hidden mb-bg-gray-800"
    >
      <video
        :src="s.file_url"
        class="mb-w-full mb-block"
        :controls="s.controls !== false"
        :autoplay="!!s.autoplay"
        :muted="!!s.muted"
        :loop="!!s.loop"
        :poster="s.poster_image || undefined"
        style="max-height:400px"
      ></video>
    </div>

    <!-- EMBED MODE (YouTube/Vimeo) -->
    <div
      v-else
      class="mb-relative mb-overflow-hidden mb-rounded-lg mb-bg-gray-800"
      :style="{ paddingBottom: aspectPadding }"
    >
      <iframe
        v-if="embedUrl"
        :src="embedUrl"
        class="mb-absolute mb-inset-0 mb-w-full mb-h-full"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen
      ></iframe>
      <div
        v-else
        class="mb-absolute mb-inset-0 mb-flex mb-flex-col mb-items-center mb-justify-center"
        :style="{ background: 'var(--olo-color-surface-alt, #f6f7f9)', color: 'var(--olo-color-text-faint, #94a3b8)' }"
      >
        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-mb-2">
          <rect x="2" y="5" width="14" height="14" rx="2" />
          <path d="m16 9 6-3v12l-6-3" />
        </svg>
        <span class="mb-text-sm">{{ t('Inserisci un URL YouTube/Vimeo o un file video') }}</span>
      </div>
    </div>

    <div
      v-if="s.caption"
      class="mb-text-sm mb-text-center mb-mt-2"
      :style="{ color: 'var(--olo-color-text-soft, #6b7280)' }"
    data-olo-editable="caption">{{ s.caption }}</div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  source_type: 'embed',
  video_url: '',
  file_url: '',
  display_mode: '16:9',
  cover_height: '500',
  autoplay: false,
  muted: false,
  loop: false,
  controls: true,
  start_time: '',
  end_time: '',
  poster_image: '',
  privacy_mode: false,
  show_play_icon: true,
  play_icon_size: '80',
  play_icon_color: '#ffffff',
  overlay_text: '',
  overlay_color: '#000000',
  overlay_opacity: '0',
  caption: '',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const playing = ref(false);
const playSize = computed(() => parseInt(s.value.play_icon_size) || 80);

const aspectPadding = computed(() => {
  const map = { '16:9': '56.25%', '4:3': '75%', '1:1': '100%' };
  return map[s.value.display_mode] || '56.25%';
});

const embedUrl = computed(() => {
  const url = s.value.video_url;
  if (!url) return '';

  const params = [];
  if (s.value.autoplay) params.push('autoplay=1');
  if (s.value.muted) params.push('mute=1');
  if (s.value.loop) params.push('loop=1');
  if (s.value.controls === false) params.push('controls=0');

  // Start/end time params
  if (s.value.start_time) params.push('start=' + parseInt(s.value.start_time));
  if (s.value.end_time) params.push('end=' + parseInt(s.value.end_time));

  const query = params.length ? '?' + params.join('&') : '';

  // YouTube
  const ytMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
  if (ytMatch) {
    let q = query;
    if (s.value.loop) q += (q ? '&' : '?') + 'playlist=' + ytMatch[1];
    const domain = s.value.privacy_mode ? 'www.youtube-nocookie.com' : 'www.youtube.com';
    return `https://${domain}/embed/${ytMatch[1]}${q}`;
  }

  // Vimeo
  const vimeoMatch = url.match(/vimeo\.com\/(\d+)/);
  if (vimeoMatch) return `https://player.vimeo.com/video/${vimeoMatch[1]}${query}`;

  return '';
});
</script>
