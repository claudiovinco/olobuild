/**
 * megamenuPresets.js — ricette di token per il tile Mega Menu.
 *
 * PROBLEMA RISOLTO: oggi `megamenu.js` espone il campo `preset` con 12 voci, ma
 * `MegaMenuTile.vue` NON legge mai `s.preset` → cambiare preset non fa nulla.
 *
 * QUI: ogni preset = un bundle di CHIAVI GIA' ESISTENTI (nessuna chiave nuova,
 * nessun salvataggio rotto). Applicare al cambio preset; i valori restano
 * sovrascrivibili dal cliente nell'inspector.
 *
 * REGOLE rispettate:
 *  - Token-first: colori brand via var(--olo-color-*). Hex grezzi SOLO per
 *    superfici "scure decise" (bar dark) dove non esiste un ruolo cliente.
 *  - Chiavi invariate: usa esattamente i nomi salvati (nav_bg, text_color,
 *    active_color, hover_effect, font_weight, text_transform, letter_spacing,
 *    btn_radius {tl,tr,br,bl}, panel_open_animation, topbar_*, ecc.).
 *
 * USO (nello store builder, al cambio di settings.preset):
 *   import { MEGAMENU_PRESETS } from '@/config/megamenuPresets';
 *   function applyMegamenuPreset(settings, id){
 *     const r = MEGAMENU_PRESETS[id];
 *     if (r) Object.assign(settings, structuredClone(r));
 *   }
 *
 * NB: 'custom' è volutamente assente → lascia i valori correnti intatti.
 */

const R = (tl,tr,br,bl)=>({ tl, tr: tr ?? tl, br: br ?? tl, bl: bl ?? (tr ?? tl) });

export const MEGAMENU_PRESETS = {

  'modern-clean': {
    nav_bg: '', text_color: '', active_color: 'var(--olo-color-primary)',
    layout: 'center', font_weight: '500', text_transform: 'none', letter_spacing: '0',
    hover_effect: 'underline', btn_radius: R(7),
    panel_shadow: 'md', panel_open_animation: 'fade', panel_border_top: '3',
  },

  'minimal-line': {
    nav_bg: '', text_color: '', active_color: 'var(--olo-color-primary)',
    layout: 'center', font_weight: '600', text_transform: 'uppercase', letter_spacing: '2.5',
    hover_effect: 'underline-grow',
    button_mode: 'last', btn_bg: 'transparent', btn_border_width: '1', btn_radius: R(0),
    panel_open_animation: 'fade', panel_shadow: 'sm',
  },

  'magazine-bold': {
    nav_bg: '#ffffff', active_color: 'var(--olo-color-primary)',
    layout: 'left', font_weight: '700', text_transform: 'uppercase', letter_spacing: '0.3',
    hover_effect: 'double-line', btn_radius: R(0), btn_bg: 'var(--olo-color-dark)',
    topbar_enabled: true, topbar_left_content: 'ticker', topbar_right_social: true,
    topbar_bg: 'var(--olo-color-primary)', topbar_text_color: '#ffffff', topbar_link_color: '#ffffff',
    panel_open_animation: 'slide-down', panel_border_top: '3',
  },

  'cinema-bar': {
    nav_bg: '#12121a', text_color: '#e8e8ea', active_color: 'var(--olo-color-accent)',
    layout: 'center', letter_spacing: '0.5',
    hover_effect: 'highlight', btn_bg: 'var(--olo-color-accent)',
    panel_bg: '#15151f', panel_open_animation: 'scale-center', panel_shadow: 'lg',
  },

  'compact-bar': {
    nav_bg: '', text_color: '', active_color: 'var(--olo-color-primary)',
    nav_height: '48', item_gap: '12', font_size: '14',
    bar_padding: { top: 8, right: 16, bottom: 8, left: 16 },
    hover_effect: 'background', btn_radius: R(6),
    panel_size: 'auto', panel_open_animation: 'fade', panel_shadow: 'md',
  },

  'glass-bar': {
    // bar translucida → header_mode overlay + sticky che diventa solido on-scroll
    nav_bg: 'rgba(255,255,255,0.13)', text_color: '#ffffff', active_color: '#ffffff',
    header_mode: 'overlay', sticky: true, sticky_bg: 'var(--olo-color-dark)',
    layout: 'center', hover_effect: 'underline',
    btn_bg: '#ffffff', btn_color: 'var(--olo-color-dark)', btn_radius: R(8),
    panel_open_animation: 'fade', panel_shadow: 'lg',
  },

  'neon-strip': {
    nav_bg: '#0b1220', text_color: '#c9d4e0', active_color: 'var(--olo-color-accent)',
    font_size: '14', hover_effect: 'glitch',
    btn_bg: 'var(--olo-color-accent)', btn_radius: R(7),
    panel_bg: '#0e1726', panel_open_animation: 'blur', panel_border_top: '2',
  },

  'brutalist-block': {
    nav_bg: '#ffffff', text_color: '#000000', active_color: '#000000',
    text_transform: 'uppercase', font_weight: '600',
    hover_effect: 'framed', hover_effect_color: '#000000', hover_effect_height: '2',
    btn_bg: '#000000', btn_color: '#ffffff', btn_radius: R(0), btn_border_width: '2',
    panel_radius: R(0), panel_shadow: 'none', panel_border_top: '0',
    border: { width: { top: 3, right: 3, bottom: 3, left: 3 }, color: '#000000', style: 'solid' },
  },

  'gradient-bar': {
    nav_bg: 'linear-gradient(135deg, var(--olo-color-primary), var(--olo-color-secondary))',
    text_color: '#ffffff', active_color: '#ffffff',
    font_weight: '600', hover_effect: 'fill-up',
    btn_bg: 'rgba(255,255,255,0.18)', btn_color: '#ffffff', btn_radius: R(8),
    panel_open_animation: 'scale', panel_shadow: 'lg',
  },

  'sticker-tape': {
    nav_bg: 'var(--olo-color-accent)', text_color: '#1a1a1a', active_color: '#111111',
    font_weight: '700', hover_effect: 'background',
    btn_bg: '#111111', btn_color: '#ffffff', btn_radius: R(999),
    panel_radius: R(14), panel_open_animation: 'scale-center',
  },

  'retro-terminal': {
    nav_bg: '#06180d', text_color: '#5fe08a', active_color: '#9dff70',
    letter_spacing: '0.8', hover_effect: 'bracket',
    button_mode: 'last', btn_bg: 'transparent', btn_border_width: '1', btn_radius: R(0),
    panel_bg: '#06180d', panel_open_animation: 'reveal', panel_border_top: '2',
  },

  'tilt-bar': {
    nav_bg: '', text_color: '', active_color: 'var(--olo-color-primary)',
    font_weight: '700', text_transform: 'uppercase',
    hover_effect: 'flip', btn_radius: R(7),
    panel_open_animation: 'flip', panel_shadow: 'lg',
    // la micro-rotazione è puramente CSS frontend (transform sul .olo-mm-bar)
  },

};

export default MEGAMENU_PRESETS;
