<template>
  <div class="fm">
    <!-- Anteprima del media selezionato (resa per tipo) -->
    <div v-if="modelValue" class="fm-preview" :class="`fm-preview--${kind}`">
      <img v-if="kind === 'image'" :src="modelValue" alt="" class="fm-media" />
      <video
        v-else-if="kind === 'video'"
        :src="videoSrc"
        class="fm-media"
        muted playsinline preload="metadata"
      ></video>
      <div v-else class="fm-filecard">
        <span class="fm-filecard-ico" v-html="icons[iconName]"></span>
        <span class="fm-filename" :title="fileName">{{ fileName }}</span>
      </div>

      <!-- Distintivo play sul video -->
      <span v-if="kind === 'video'" class="fm-play" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
      </span>

      <button type="button" class="fm-remove" :title="t('Rimuovi media')" @click="clear">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>

    <!-- Picker: un solo pulsante, filtrato sul tipo del campo -->
    <button type="button" class="fm-pick" @click="pickMedia(detectedType)">
      <span class="fm-pick-ico" v-html="icons[iconName]"></span>
      <span>{{ modelValue ? meta.change : meta.select }}</span>
    </button>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { useToast } from '../../../composables/useToast.js';

const props = defineProps({
  modelValue: { type: String, default: '' },
  // Auto-filtro: 'image' | 'video' | 'audio' | 'application/pdf' | 'all'
  // (passato da InspectorField, derivato dalla chiave del campo).
  accept: { type: String, default: 'all' },
});
const emit = defineEmits(['update:modelValue', 'update:attachmentId']);

// Tipo atteso del campo, normalizzato ai valori noti.
const KNOWN = ['image', 'video', 'audio', 'application/pdf', 'all'];
const detectedType = computed(() => {
  const a = (props.accept || 'all').toLowerCase();
  return KNOWN.includes(a) ? a : 'all';
});

// Etichette + icona per il pulsante, per tipo di media.
const TYPE_META = {
  image:             { icon: 'image', select: t('Seleziona immagine'), change: t('Cambia immagine') },
  video:             { icon: 'video', select: t('Seleziona video'),    change: t('Cambia video') },
  audio:             { icon: 'audio', select: t('Seleziona audio'),    change: t('Cambia audio') },
  'application/pdf': { icon: 'pdf',   select: t('Seleziona PDF'),      change: t('Cambia PDF') },
  all:               { icon: 'all',   select: t('Seleziona media'),    change: t('Cambia media') },
};
const meta = computed(() => TYPE_META[detectedType.value] || TYPE_META.all);
const iconName = computed(() => (kind.value === 'file' ? 'all' : (kind.value === 'pdf' ? 'pdf' : meta.value.icon)));

// Tipo effettivo del file selezionato (dall'estensione, con il tipo atteso come ripiego).
const kind = computed(() => {
  const u = (props.modelValue || '').toLowerCase();
  const ext = (u.split('?')[0].split('#')[0].split('.').pop() || '');
  const a = detectedType.value;
  if (ext === 'pdf') return 'pdf';
  if (['mp4', 'webm', 'mov', 'm4v', 'ogv'].includes(ext)) return 'video';
  if (['mp3', 'wav', 'm4a', 'aac', 'flac', 'oga'].includes(ext)) return 'audio';
  if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'bmp'].includes(ext)) return 'image';
  if (ext === 'ogg') return a === 'audio' ? 'audio' : 'video';
  // Estensione assente/sconosciuta → ripiego sul tipo atteso del campo.
  if (a === 'video') return 'video';
  if (a === 'audio') return 'audio';
  if (a === 'application/pdf') return 'pdf';
  if (a === 'image') return 'image';
  return 'file';
});

// Aggiunge un frammento temporale per far mostrare al video un fotogramma di anteprima.
const videoSrc = computed(() => {
  const u = props.modelValue || '';
  return u && !u.includes('#') ? u + '#t=0.5' : u;
});

const fileName = computed(() => {
  if (!props.modelValue) return '';
  return props.modelValue.split('/').pop().split('?')[0].split('#')[0];
});

// Icone SVG (mai emoji), coerenti col resto dell'inspector: stroke currentColor, no dimensioni inline.
const icons = {
  image: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2.5"/><circle cx="8.5" cy="8.5" r="1.6"/><path d="M21 15l-4.5-4.5L5 21"/></svg>',
  video: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="5" width="19" height="14" rx="3"/><path d="M10.5 9.2v5.6l4.8-2.8z" fill="currentColor" stroke="none"/></svg>',
  audio: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 17V5l11-2v12"/><circle cx="6" cy="17" r="3"/><circle cx="17" cy="15" r="3"/></svg>',
  pdf: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2.5H6.5a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V8z"/><path d="M14 2.5V8h5.5"/></svg>',
  all: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="3" width="14" height="14" rx="2.5"/><path d="M4 7.5V19a2 2 0 0 0 2 2h11.5"/><path d="M11 9.2v4.6l3.8-2.3z" fill="currentColor" stroke="none"/></svg>',
};

function clear() {
  emit('update:modelValue', '');
  emit('update:attachmentId', 0);
}

function pickMedia(type = 'all') {
  const toast = useToast();
  if (!window.wp || !window.wp.media) {
    toast.error(t('Libreria Media di WordPress non disponibile.'));
    return;
  }
  const frameOpts = {
    title: meta.value.select,
    button: { text: t('Usa questo media') },
    multiple: false,
  };
  // wp.media accetta il mime ('application/pdf') o il tipo ('image','video','audio').
  if (type !== 'all') frameOpts.library = { type };
  const frame = wp.media(frameOpts);
  frame.on('select', () => {
    const attachment = frame.state().get('selection').first().toJSON();
    emit('update:modelValue', attachment.url);
    emit('update:attachmentId', attachment.id);
  });
  frame.open();
}
</script>

<style scoped>
.fm {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

/* ── Anteprima ── */
.fm-preview {
  position: relative;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
  background: #f9fafb;
}
.fm-media {
  display: block;
  width: 100%;
  height: 104px;
  object-fit: cover;
  background: #f3f4f6;
}
.fm-preview--video .fm-media { background: #111827; }

.fm-filecard {
  display: flex;
  align-items: center;
  gap: 10px;
  height: 104px;
  padding: 0 14px;
  color: #6b7280;
}
.fm-filecard-ico {
  flex: none;
  display: inline-flex;
  color: #9ca3af;
}
.fm-filecard-ico :deep(svg) { width: 30px; height: 30px; }
.fm-preview--pdf .fm-filecard-ico { color: #ef4444; }
.fm-filename {
  font-size: 12px;
  line-height: 1.3;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Distintivo play sovrapposto al video */
.fm-play {
  position: absolute;
  inset: 0;
  margin: auto;
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(17, 24, 39, 0.55);
  color: #fff;
  border-radius: 50%;
  pointer-events: none;
}
.fm-play svg { width: 20px; height: 20px; margin-left: 2px; }

/* Pulsante rimuovi (compare all'hover) */
.fm-remove {
  position: absolute;
  top: 6px;
  right: 6px;
  width: 22px;
  height: 22px;
  padding: 0;
  border: none;
  border-radius: 50%;
  background: rgba(17, 24, 39, 0.55);
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.15s, background 0.15s;
}
.fm-preview:hover .fm-remove { opacity: 1; }
.fm-remove:hover { background: #ef4444; }
.fm-remove svg { width: 13px; height: 13px; }
.fm-remove:focus-visible {
  opacity: 1;
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: 2px;
}

/* ── Pulsante picker ── */
.fm-pick {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 8px 12px;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 12.5px;
  font-weight: 600;
  color: #374151;
  cursor: pointer;
  transition: border-color 0.15s, color 0.15s, background 0.15s;
}
.fm-pick:hover {
  border-color: var(--olo-ui-accent, #e8622a);
  color: var(--olo-ui-accent, #e8622a);
}
.fm-pick:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: 1px;
}
.fm-pick-ico { flex: none; display: inline-flex; }
.fm-pick-ico :deep(svg) { width: 16px; height: 16px; }
</style>
