<template>
  <div
    class="mb-flex mb-flex-wrap mb-py-4 mb-px-4"
    :style="{ gap: rv(settings, 'gap', s.gap, builderStore.viewMode) + 'px', justifyContent: justifyMap[rv(settings, 'alignment', s.alignment, builderStore.viewMode)] || 'center', minHeight: '48px' }"
  >
    <div
      v-for="(btn, i) in activeButtons"
      :key="i"
      class="mb-flex mb-items-center mb-rounded-lg mb-cursor-pointer"
      :style="btnStyle(btn.platform)"
      :title="getLabel(btn)"
    >
      <span
        v-if="s.style !== 'text-only'"
        v-html="getIconSvg(btn.platform)"
        :style="{ width: iconSize + 'px', height: iconSize + 'px', display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }"
      ></span>
      <span
        v-if="s.style !== 'icon-only'"
        :style="{ marginLeft: s.style === 'icon-text' ? '6px' : '0', fontSize: '13px', whiteSpace: 'nowrap' }"
      >{{ getLabel(btn) }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { rv } from '@/composables/useResponsiveValue';

const builderStore = useBuilderStore();

const defaults = {
  buttons: [
    { id: 'sh-1', platform: 'facebook',  custom_label: '' },
    { id: 'sh-2', platform: 'twitter',   custom_label: '' },
    { id: 'sh-3', platform: 'whatsapp',  custom_label: '' },
    { id: 'sh-4', platform: 'linkedin',  custom_label: '' },
    { id: 'sh-5', platform: 'email',     custom_label: '' },
  ],
  style: 'icon-only',
  size: '36',
  gap: '10',
  alignment: 'center',
  icon_color: '#ffffff',
  icon_hover_color: '#ffffff',
  bg_color: 'var(--olo-color-primary, #e1474f)',
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({ ...defaults, ...props.settings }));

const justifyMap = { left: 'flex-start', center: 'center', right: 'flex-end' };

const platformLabels = {
  facebook: 'Facebook',
  twitter: 'X',
  whatsapp: 'WhatsApp',
  linkedin: 'LinkedIn',
  email: 'Email',
  copylink: 'Copia link',
  telegram: 'Telegram',
  pinterest: 'Pinterest',
};

const iconPaths = {
  facebook: 'M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z',
  twitter: 'M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z',
  whatsapp: 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z',
  linkedin: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
  email: 'M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z',
  copylink: 'M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z',
  telegram: 'M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z',
  pinterest: 'M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z',
};

function getIconSvg(platform) {
  const d = iconPaths[platform];
  if (!d) return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="1em" height="1em"><circle cx="12" cy="12" r="10"/></svg>';
  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="1em" height="1em"><path d="${d}"/></svg>`;
}

function getLabel(btn) {
  if (btn.custom_label) return btn.custom_label;
  return platformLabels[btn.platform] || btn.platform;
}

const iconSize = computed(() => Math.round((parseInt(s.value.size) || 36) * 0.5));

const activeButtons = computed(() => {
  const raw = s.value.buttons;
  if (Array.isArray(raw)) return raw.filter(item => item.platform);
  return [];
});

function btnStyle(platform) {
  return {
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    padding: s.value.style === 'text-only' ? '8px 14px' : (s.value.style === 'icon-text' ? '8px 14px' : '8px'),
    width: s.value.style === 'icon-only' ? (parseInt(s.value.size) || 36) + 'px' : 'auto',
    height: s.value.style === 'icon-only' ? (parseInt(s.value.size) || 36) + 'px' : 'auto',
    backgroundColor: s.value.bg_color || 'var(--olo-color-primary, #e1474f)',
    color: s.value.icon_color || '#ffffff',
    borderRadius: '8px',
    fontSize: iconSize.value + 'px',
    lineHeight: '1',
    transition: 'all 0.2s ease',
  };
}
</script>
