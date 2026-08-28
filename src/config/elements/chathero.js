import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Hero — Chat (Glow + Conversation Cards) : hero SaaS centrato su fondo scuro con GLOW
 * radiale sfocato dietro un eyebrow PILL, un H1 multi-riga (parola/coda finale a gradiente
 * accento), sub e fino a 2 CTA — seguito SOTTO da una finestra "workspace" stile chat con
 * barra finestrata e una pila di BOLLE messaggio (you / ai) ripetibili. Meccanica firma =
 * glow radiale + le card-conversazione impilate. Estratta dal blueprint OLOthemes "Synapse".
 * Render Vue == PHP (ChatHeroTile.vue). Runtime: nessuno (pure CSS).
 */
export default {
  type: 'chathero',
  name: t('Hero — Chat (Glow + Conversation Cards)'),
  icon: 'dashicons-format-chat',
  category: 'marketing',
  // Ritirata dalla palette (unificazione hero, Fase 2): assorbita da `hero`
  // (scena glow + modulo "finestra chat"). I template salvati continuano a renderizzare.
  hidden: true,

  defaults: {
    pill_text: 'Synapse 3 · now with long-term memory',
    pill_dot: true,
    headline_text: 'The AI workspace that ',
    accent_text: 'remembers.',
    subhead: "Chat, agents and your company's knowledge in one place — grounded in your docs, your data and every conversation you've had before.",
    cta1_text: 'Try free',
    cta1_url: '#pricing',
    cta2_text: 'See how it works',
    cta2_url: '#features',
    chat_enabled: true,
    chat_label: 'synapse · workspace',
    messages: [
      { side: 'you', text: "Summarise last week's customer calls and flag anything about pricing." },
      { side: 'ai', text: 'Across 9 calls: 3 flagged pricing — two want annual billing, one found the Team tier "a jump". Drafted a follow-up for each. Want me to send?' },
      { side: 'you', text: 'Yes, and add them to the CRM.' },
      { side: 'ai', text: '…' },
    ],
    bg_color: 'var(--olo-color-dark, #16263d)',
    panel_color: 'var(--olo-color-dark, #16263d)',
    panel2_color: 'var(--olo-color-dark, #16263d)',
    accent: '',
    accent2: '',
    accent_on: 'var(--olo-color-light, #f8f9fa)',
    text_color: 'var(--olo-color-light, #f8f9fa)',
    sub_color: 'var(--olo-color-text-soft, #6b7280)',
    msg_text_color: 'var(--olo-color-text-faint, #94a3b8)',
    pill_color: '',
    glow_color: 'rgba(160,107,255,0.3)',
    glow_w: 820,
    glow_h: 560,
    glow_blur: 110,
    glow_x: 50,
    glow_y: -220,
    h_size_min: 40,
    h_size_vw: 6.6,
    h_size_max: 82,
    max_width: 840,
    chat_max_width: 760,

    // Spaziatura / Raggio (additivi, default = resa attuale invariata)
    content_padding: { top: 0, right: 28, bottom: 0, left: 28 },
    chat_radius: { tl: 16, tr: 16, br: 0, bl: 0 },

    // KIT standard OLObuild — sfondo completo + ombra + bordo (default no-op)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'pill_text', label: t('Pill (eyebrow)'), type: 'text' },
    { key: 'pill_dot', label: t('Dot accento nella pill'), type: 'toggle' },
    { key: 'headline_text', label: t('Titolo (base)'), type: 'text' },
    { key: 'accent_text', label: t('Coda titolo (gradiente accento)'), type: 'text' },
    { key: 'subhead', label: t('Sottotitolo'), type: 'textarea' },

    { type: 'separator', label: t('CTA') },
    { key: 'cta1_text', label: t('CTA 1 — testo'), type: 'text' },
    { key: 'cta1_url', label: t('CTA 1 — link'), type: 'link' },
    { key: 'cta2_text', label: t('CTA 2 — testo'), type: 'text' },
    { key: 'cta2_url', label: t('CTA 2 — link'), type: 'link' },

    { type: 'separator', label: t('Finestra chat') },
    { key: 'chat_enabled', label: t('Mostra finestra chat'), type: 'toggle' },
    { key: 'chat_label', label: t('Etichetta barra finestra'), type: 'text' },
    { key: 'messages', label: t('Messaggi (bolle conversazione)'), type: 'content-items',
      itemLabel: t('Messaggio'),
      defaults: { side: 'ai', text: 'Nuovo messaggio' },
      itemFields: [
        { key: 'side', label: t('Lato'), type: 'select', options: [
          { value: 'you', label: t('Utente (destra)') },
          { value: 'ai', label: t('Assistente (sinistra)') },
        ]},
        { key: 'text', label: t('Testo'), type: 'textarea' },
      ],
    },
  ],

  styleFields: [
    { type: 'separator', label: t('Titolo') },
    { key: 'h_size_min', label: t('Dimensione min (px)'), type: 'range', min: 20, max: 70, step: 1 },
    { key: 'h_size_vw', label: t('Dimensione fluida (vw)'), type: 'range', min: 3, max: 12, step: 0.1 },
    { key: 'h_size_max', label: t('Dimensione max (px)'), type: 'range', min: 50, max: 140, step: 2 },
    { key: 'max_width', label: t('Larghezza max testo (px)'), type: 'range', min: 480, max: 1200, step: 20 },

    { type: 'separator', label: t('Glow radiale') },
    { key: 'glow_color', label: t('Colore glow'), type: 'color' },
    { key: 'glow_w', label: t('Larghezza glow (px)'), type: 'range', min: 200, max: 1400, step: 20 },
    { key: 'glow_h', label: t('Altezza glow (px)'), type: 'range', min: 200, max: 1000, step: 20 },
    { key: 'glow_blur', label: t('Sfocatura glow (px)'), type: 'range', min: 0, max: 200, step: 5 },
    { key: 'glow_x', label: t('Posizione X glow (%)'), type: 'range', min: 0, max: 100, step: 1 },
    { key: 'glow_y', label: t('Posizione Y glow (px)'), type: 'range', min: -400, max: 200, step: 10 },

    { type: 'separator', label: t('Finestra chat') },
    { key: 'chat_max_width', label: t('Larghezza max finestra (px)'), type: 'range', min: 360, max: 1000, step: 20 },
    { key: 'panel_color', label: t('Sfondo finestra'), type: 'color' },
    { key: 'panel2_color', label: t('Sfondo barra / bolla AI'), type: 'color' },
    { key: 'msg_text_color', label: t('Colore testo bolla AI'), type: 'color' },

    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Sfondo'), type: 'color' },
    { key: 'accent', label: t('Accento (gradiente + pill + CTA + bolla utente)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'accent2', label: t('Accento 2 (fine gradiente)'), type: 'color',
      description: t('Vuoto = secondario del tema.') },
    { key: 'accent_on', label: t('Testo su accento (CTA)'), type: 'color' },
    { key: 'text_color', label: t('Colore titolo'), type: 'color' },
    { key: 'sub_color', label: t('Colore sottotitolo / etichetta'), type: 'color' },
    { key: 'pill_color', label: t('Colore testo pill (vuoto = accento)'), type: 'color' },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'content_padding', label: t('Padding contenuto (px)'), type: 'spacing', max: 200,
      description: t('Spaziatura interna del blocco testo (eyebrow + titolo + sub + CTA).') },

    { type: 'separator', label: t('Forma') },
    { key: 'chat_radius', label: t('Raggio finestra chat (px)'), type: 'border-radius' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },
    { type: 'separator', label: t('Ombra') },
    ...shadowField,
    ...borderFields(),
  ],
};
