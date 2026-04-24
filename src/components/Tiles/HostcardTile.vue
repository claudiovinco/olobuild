<template>
  <div class="mb-font-sans" :style="wrapStyle">
    <!-- Button variant -->
    <div v-if="s.variant === 'button'" style="text-align:center;">
      <div :style="buttonStyle">
        <span v-if="s.button_icon" style="margin-right:6px;">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" style="vertical-align:-3px;">
            <circle cx="10" cy="6" r="4"/><path d="M2 18c0-4.4 3.6-8 8-8s8 3.6 8 8"/>
          </svg>
        </span>
        <span data-olo-editable="button_text">{{ s.button_text || 'Il tuo gestore' }}</span>
      </div>
    </div>

    <!-- Card variant -->
    <div v-else-if="s.variant !== 'inline'" :style="cardStyle">
      <div style="display:flex;align-items:center;gap:14px;padding:16px;">
        <div :style="miniPhotoStyle">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="#9ca3af">
            <circle cx="10" cy="6" r="4"/><path d="M2 18c0-4.4 3.6-8 8-8s8 3.6 8 8"/>
          </svg>
        </div>
        <div style="flex:1;min-width:0;">
          <div style="font-weight:600;font-size:15px;color:var(--olo-color-text, #374151);">{{ t('Nome Gestore') }}</div>
          <div v-if="s.label_role" style="font-size:13px;color:#6b7280;margin-top:2px;" data-olo-editable="label_role">{{ s.label_role }}</div>
        </div>
        <svg width="16" height="16" viewBox="0 0 20 20" fill="#9ca3af" style="flex-shrink:0;">
          <path d="M7 4l6 6-6 6" stroke="#9ca3af" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>

    <!-- Inline variant -->
    <div v-else :style="inlineStyle">
      <div style="text-align:center;margin-bottom:16px;">
        <div :style="photoStyle">
          <svg width="32" height="32" viewBox="0 0 20 20" fill="#9ca3af">
            <circle cx="10" cy="6" r="4"/><path d="M2 18c0-4.4 3.6-8 8-8s8 3.6 8 8"/>
          </svg>
        </div>
        <div style="font-weight:700;font-size:18px;color:var(--olo-color-text, #374151);margin-top:12px;">{{ t('Nome Gestore') }}</div>
        <div v-if="s.label_role" style="font-size:14px;color:#6b7280;margin-top:4px;" data-olo-editable="label_role">{{ s.label_role }}</div>
      </div>
      <div v-if="s.show_bio" style="font-size:13px;color:#374151;line-height:1.6;margin-bottom:14px;text-align:center;">
        {{ t('Biografia del gestore della struttura...') }}
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;">
        <span v-if="s.show_phone" style="font-size:12px;color:#374151;display:flex;align-items:center;gap:4px;">
          <svg width="14" height="14" viewBox="0 0 20 20" fill="#6b7280"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
          +39 123 456
        </span>
        <span v-if="s.show_email" style="font-size:12px;color:#374151;display:flex;align-items:center;gap:4px;">
          <svg width="14" height="14" viewBox="0 0 20 20" fill="#6b7280"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
          email@example.com
        </span>
      </div>
      <div v-if="s.show_languages" style="display:flex;gap:6px;justify-content:center;margin-top:10px;flex-wrap:wrap;">
        <span v-for="lang in ['IT','EN','DE']" :key="lang" :style="pillStyle">{{ lang }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { getShadowValue } from '@/composables/useShadowMap';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  source: 'auto', manual_user_id: '', variant: 'card',
  button_text: 'Il tuo gestore', button_style: 'primary', button_icon: 'user',
  show_photo: true, show_bio: true, show_phone: true, show_email: true,
  show_languages: true, show_services: false, label_role: 'Gestore della struttura',
  photo_size: '100', photo_rounded: true, accent_color: '', card_bg: '#ffffff',
  card_radius: '12', modal_shadow: 'lg', modal_overlay: '60',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #6366F1)');

const wrapStyle = computed(() => ({
  maxWidth: '400px',
}));

const btnColorMap = {
  default: { bg: '#e5e7eb', color: '#374151' },
  primary: { bg: '#1e87f0', color: '#fff' },
  secondary: { bg: '#222', color: '#fff' },
  text: { bg: 'transparent', color: '#374151' },
  link: { bg: 'transparent', color: '#1e87f0' },
};

const buttonStyle = computed(() => {
  const c = btnColorMap[s.value.button_style] || btnColorMap.primary;
  return {
    display: 'inline-flex', alignItems: 'center', gap: '6px',
    padding: '10px 24px', borderRadius: '4px', fontSize: '14px',
    fontWeight: '600', background: c.bg, color: c.color, cursor: 'pointer',
  };
});

const cardStyle = computed(() => ({
  background: s.value.card_bg,
  borderRadius: s.value.card_radius + 'px',
  boxShadow: getShadowValue(s.value, 'modal_shadow'),
  overflow: 'hidden',
  cursor: 'pointer',
  transition: 'box-shadow 0.2s ease',
}));

const miniPhotoStyle = computed(() => ({
  width: '48px', height: '48px',
  borderRadius: s.value.photo_rounded ? '50%' : '6px',
  background: '#e5e7eb', display: 'flex', alignItems: 'center',
  justifyContent: 'center', flexShrink: '0',
}));

const inlineStyle = computed(() => ({
  background: s.value.card_bg,
  borderRadius: s.value.card_radius + 'px',
  boxShadow: getShadowValue(s.value, 'modal_shadow'),
  padding: '24px',
}));

const photoStyle = computed(() => {
  const size = Math.min(parseInt(s.value.photo_size) || 100, 120);
  return {
    width: size + 'px', height: size + 'px',
    borderRadius: s.value.photo_rounded ? '50%' : '8px',
    background: '#e5e7eb', display: 'inline-flex', alignItems: 'center',
    justifyContent: 'center',
  };
});

const pillStyle = computed(() => ({
  background: '#f3f4f6', color: '#374151', padding: '3px 10px',
  borderRadius: '12px', fontSize: '11px', fontWeight: '600',
}));
</script>
