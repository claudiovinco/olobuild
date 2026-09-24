import { defineStore } from 'pinia';
import { contrastOn } from '@/composables/oloTileDefaults';
import { t } from '@/i18n';
import { okOrThrow } from '@/components/Admin/cfgSave';
import { rinnovaNonce, nonceScaduto } from '@/utils/restNonce';

const oloData = window.oloData || {};

// Il nonce si legge a ogni richiesta: rinnovaNonce lo aggiorna dentro window.oloData.
function nonceAttuale() {
  return (typeof window !== 'undefined' && window.oloData ? window.oloData : oloData).nonce;
}

// ── Esito dei salvataggi ──
// Le azioni che scrivono sul server restituiscono { ok: true } oppure
// { ok: false, motivo } (e inCorso: true se un salvataggio uguale è già in
// volo). Prima l'errore finiva solo in console e il chiamante annunciava
// «salvato» comunque: alla ricarica il lavoro era perso.
class ErroreRest extends Error {}

// fetch che non inghiotte niente: nessuna risposta → «errore di rete»,
// risposta non 2xx → okOrThrow («500 …»). Il nonce lo mette chiedi: scaduto
// (403 rest_cookie_invalid_nonce, arriva prima dell'handler e il server non ha
// scritto niente) se ne chiede uno nuovo e si ripete UNA volta, come il
// salvataggio della pagina; senza, ogni «Riprova» falliva di nuovo.
async function chiedi(url, opzioni = {}) {
  const invia = async () => {
    try {
      return await fetch(url, { ...opzioni, headers: { ...(opzioni.headers || {}), 'X-WP-Nonce': nonceAttuale() } });
    } catch (e) {
      throw new ErroreRest(t('errore di rete'));
    }
  };
  let res = await invia();
  if (await nonceScaduto(res)) {
    // WordPress non ne dà uno nuovo: la sessione è chiusa davvero.
    if (!(await rinnovaNonce())) throw new ErroreRest(t('sessione scaduta: accedi di nuovo e riprova'));
    res = await invia();
  }
  try {
    return await okOrThrow(res);
  } catch (e) {
    throw new ErroreRest(e.message);
  }
}

function motivoErrore(err) {
  if (err instanceof ErroreRest) return err.message;
  if (err instanceof SyntaxError) return t('risposta non valida del server');
  return t('errore imprevisto');
}

function inCorso() {
  return { ok: false, inCorso: true, motivo: t('salvataggio già in corso') };
}

// Il valore di un set tipografico può essere un var() di ruolo, uno stack
// web-safe o il nome di un solo font: virgolettare sempre trasformava le prime
// due forme in un nome di font inesistente, e la famiglia non si applicava.
function famigliaCss(family) {
  const v = String(family || '').trim();
  if (!v) return '';
  if (v.startsWith('var(') || v.includes(',') || v.includes("'") || v.includes('"')) return v;
  return `'${v}', sans-serif`;
}

// Gli helper restituiscono già l'unità: "0.5px" + "px" = dichiarazione scartata.
function lunghezzaCss(v) {
  const s = String(v ?? '0').trim() || '0';
  return /^-?[\d.]+$/.test(s) ? s + 'px' : s;
}

function cssRadius(val, fallback = '4px') {
  if (typeof val === 'object' && val !== null) {
    return `${val.tl || 0}px ${val.tr || 0}px ${val.br || 0}px ${val.bl || 0}px`;
  }
  const s = String(val || '');
  if (s.includes('px')) return s;
  if (s) return `${s}px`;
  return fallback;
}

export const useStylesStore = defineStore('styles', {
  state: () => ({
    styles: JSON.parse(JSON.stringify(oloData.styles || {})),
    presets: oloData.presets || {},
    customPresets: [],
    generatedCss: oloData.stylesCss || '',
    isDirty: false,
    isSaving: false,
    savingColors: false, // flag dedicato al save dei global colors (non condiviso con isSaving)
    savingTypography: false, // idem per i set tipografici
    globalColors: JSON.parse(JSON.stringify(oloData.globalColors || [])),
    globalTypography: JSON.parse(JSON.stringify(oloData.globalTypography || [])),
    // Set tipografici mandati a salvare e non ancora scritti (salvataggio fallito o
    // in volo): li lascia il pannello quando si chiude, e alla riapertura li
    // riprende (restano «da salvare»). Non stanno in globalTypography, che offre
    // i set alle tile: un set mai salvato darebbe loro un var(--olo-font-<id>-family)
    // che sul sito non esiste. Solo in memoria, come ogni modifica non salvata.
    globalTypographyDraft: null,
    globalColorsDirty: false,
    globalTypographyDirty: false,
  }),

  getters: {
    colors: (state) => state.styles.colors || {},
    darkColors: (state) => state.styles.dark_colors || {},
    typography: (state) => state.styles.typography || {},
    layout: (state) => state.styles.layout || {},
    googleFonts: (state) => state.styles.google_fonts || [],
    grain: (state) => state.styles.grain || { enabled: false, opacity: 6, scale: 180 },

    /**
     * Generate CSS custom properties client-side for live preview.
     */
    cssVariables(state) {
      const s = state.styles;
      const c = s.colors || {};
      const t = s.typography || {};
      const l = s.layout || {};
      const fonts = s.google_fonts || [];

      let css = '';

      // Google Fonts import
      if (fonts.length > 0) {
        const families = fonts.map(f => f.replace(/ /g, '+') + ':wght@300;400;500;600;700');
        css += `@import url("https://fonts.googleapis.com/css2?family=${families.join('&family=')}&display=swap");\n`;
      }

      css += '.olo-template {\n';
      // Colors
      for (const [key, value] of Object.entries(c)) {
        const prop = key.replace(/_/g, '-');
        css += `  --olo-color-${prop}: ${value};\n`;
      }
      // Typography
      if (t.font_family) css += `  --olo-font-family: ${t.font_family};\n`;
      if (t.font_family_heading) css += `  --olo-font-family-heading: ${t.font_family_heading};\n`;
      css += `  --olo-font-size-base: ${t.font_size_base || '16px'};\n`;
      for (let i = 1; i <= 6; i++) {
        css += `  --olo-font-size-h${i}: ${t[`font_size_h${i}`] || '1rem'};\n`;
      }
      css += `  --olo-line-height: ${t.line_height || '1.6'};\n`;
      css += `  --olo-font-weight-heading: ${t.font_weight_heading || '700'};\n`;
      // Layout
      css += `  --olo-border-radius: ${cssRadius(l.border_radius, '4px')};\n`;
      css += `  --olo-border-radius-large: ${cssRadius(l.border_radius_large, '8px')};\n`;
      css += `  --olo-container-max-width: ${l.container_max_width || '1200px'};\n`;
      css += `  --olo-container-narrow: ${l.container_narrow || '720px'};\n`;
      css += `  --olo-container-wide: ${l.container_wide || '1440px'};\n`;
      // Spacing scale
      const sp = s.spacing || {};
      css += `  --olo-space-xs: ${sp.xs || '4px'};\n`;
      css += `  --olo-space-sm: ${sp.sm || '8px'};\n`;
      css += `  --olo-space-md: ${sp.md || '16px'};\n`;
      css += `  --olo-space-lg: ${sp.lg || '24px'};\n`;
      css += `  --olo-space-xl: ${sp.xl || '32px'};\n`;
      css += `  --olo-space-2xl: ${sp['2xl'] || '48px'};\n`;
      css += `  --olo-space-3xl: ${sp['3xl'] || '64px'};\n`;
      css += `  --olo-space-4xl: ${sp['4xl'] || '96px'};\n`;
      // Border radius scale
      const br = s.border_radius_scale || {};
      css += `  --olo-radius-none: 0;\n`;
      css += `  --olo-radius-sm: ${br.sm || '4px'};\n`;
      css += `  --olo-radius-md: ${br.md || '8px'};\n`;
      css += `  --olo-radius-lg: ${br.lg || '16px'};\n`;
      css += `  --olo-radius-full: ${br.full || '9999px'};\n`;
      // Global shadows
      const sh = s.shadows || {};
      css += `  --olo-shadow-none: none;\n`;
      css += `  --olo-shadow-sm: ${sh.sm || '0 1px 2px 0 rgba(0,0,0,0.05)'};\n`;
      css += `  --olo-shadow-md: ${sh.md || '0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1)'};\n`;
      css += `  --olo-shadow-lg: ${sh.lg || '0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1)'};\n`;
      css += `  --olo-shadow-xl: ${sh.xl || '0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1)'};\n`;
      // Legacy aliases
      css += '  --olo-shadow-small: var(--olo-shadow-sm);\n';
      css += '  --olo-shadow-medium: var(--olo-shadow-md);\n';
      css += '  --olo-shadow-large: var(--olo-shadow-lg);\n';

      // Section padding (token alias)
      const secp = s.section_padding || {};
      css += `  --olo-section-pad-y-compact:  var(--olo-space-${secp.compact  || 'lg'});\n`;
      css += `  --olo-section-pad-y-default:  var(--olo-space-${secp.default  || 'xl'});\n`;
      css += `  --olo-section-pad-y-spacious: var(--olo-space-${secp.spacious || '2xl'});\n`;
      css += `  --olo-section-pad-y-between:  var(--olo-space-${secp.between  || 'md'});\n`;

      // Gutter
      const g = s.gutter || {};
      css += `  --olo-gutter: ${parseInt(g.desktop ?? 32, 10)}px;\n`;
      css += `  --olo-gutter-side: ${parseInt(g.side_desktop ?? 32, 10)}px;\n`;

      // Global Colors
      if (state.globalColors && state.globalColors.length > 0) {
        css += '  /* Global Color Palette */\n';
        for (const gc of state.globalColors) {
          if (gc.id && gc.value) {
            css += `  --olo-color-${gc.id}: ${gc.value};\n`;
          }
        }
      }

      // on-primary: testo leggibile sul primario corrente (contrasto sRGB).
      // Fonte del primario: palette globale → colors.primary → seed brand.
      const gcPrimary = (state.globalColors || []).find(g => g.id === 'primary');
      const primaryHex = (gcPrimary && gcPrimary.value) || c.primary || '#e1474f';
      css += `  --olo-color-on-primary: ${contrastOn(primaryHex)};\n`;

      // Alias di compatibilità: i nomi-pacchetto usati dalle tile mappano sui
      // token del tema (text_muted/muted/background/danger), così seguono la
      // palette del cliente. Vedi _olo-tokens.scss.
      css += '  --olo-color-text-soft: var(--olo-color-text-muted, #6b7280);\n';
      css += '  --olo-color-text-faint: var(--olo-color-text-muted, #94a3b8);\n';
      css += '  --olo-color-surface: var(--olo-color-background, #ffffff);\n';
      css += '  --olo-color-surface-alt: var(--olo-color-muted, #f6f7f9);\n';
      css += '  --olo-color-error: var(--olo-color-danger, #b42318);\n';
      css += '  --olo-color-info: #2563eb;\n';

      // Global Typography
      if (state.globalTypography && state.globalTypography.length > 0) {
        css += '  /* Global Typography Sets */\n';
        for (const gt of state.globalTypography) {
          if (!gt.id) continue;
          if (gt.family) css += `  --olo-font-${gt.id}-family: ${famigliaCss(gt.family)};\n`;
          css += `  --olo-font-${gt.id}-weight: ${gt.weight || '400'};\n`;
          css += `  --olo-font-${gt.id}-transform: ${gt.transform || 'none'};\n`;
          css += `  --olo-font-${gt.id}-line-height: ${gt.line_height || '1.5'};\n`;
          css += `  --olo-font-${gt.id}-letter-spacing: ${lunghezzaCss(gt.letter_spacing)};\n`;
        }
      }

      css += '}\n\n';

      // Regole del preset tipografico — gemello di Olobuild_Style_System.
      // La classe sul wrapper porta lo stile ai discendenti: l'eredita' da sola
      // perdeva contro le regole del tema e il comando non faceva niente.
      if (state.globalTypography && state.globalTypography.length > 0) {
        css += '/* Preset tipografici - applicazione */\n';
        for (const gt of state.globalTypography) {
          if (!gt.id) continue;
          const sel = `.olo-typo-${gt.id}.olo-typo-${gt.id}`;
          css += `${sel},\n${sel} :is(h1,h2,h3,h4,h5,h6,p,li,a,span,em,i,blockquote,figcaption,label,dt,dd,td,button,input,textarea,select) {\n`;
          css += `  font-family: var(--olo-font-${gt.id}-family, inherit);\n`;
          css += `  font-weight: var(--olo-font-${gt.id}-weight, inherit);\n`;
          css += `  text-transform: var(--olo-font-${gt.id}-transform, none);\n`;
          css += `  line-height: var(--olo-font-${gt.id}-line-height, inherit);\n`;
          css += `  letter-spacing: var(--olo-font-${gt.id}-letter-spacing, normal);\n`;
          css += '}\n\n';
          // Le tile scrivono la tipografia del titolo inline attraverso le
          // variabili di ruolo: ridefinirle sul wrapper fa risolvere quella
          // dichiarazione nel font del preset. Il ruolo mono resta fuori.
          css += `${sel} {\n`;
          css += `  --olo-font-family: var(--olo-font-${gt.id}-family, var(--olo-font-family));\n`;
          css += `  --olo-font-family-heading: var(--olo-font-${gt.id}-family, var(--olo-font-family-heading));\n`;
          css += '}\n\n';
        }
      }

      // UIkit overrides – bg WITHOUT !important so inline styles (custom bg) can win
      css += '.olo-template .uk-section-primary { background-color: var(--olo-color-primary); color: var(--olo-color-primary-contrast) !important; }\n';
      css += '.olo-template .uk-section-primary :where(a) { color: var(--olo-color-primary-contrast) !important; }\n';
      css += '.olo-template .uk-section-secondary { background-color: var(--olo-color-secondary); color: var(--olo-color-secondary-contrast) !important; }\n';
      css += '.olo-template .uk-section-secondary :where(a) { color: var(--olo-color-secondary-contrast) !important; }\n';
      css += '.olo-template .uk-section-muted { background-color: var(--olo-color-muted); color: var(--olo-color-muted-contrast) !important; }\n';

      // Typography overrides — fallback to UIkit's font stack when no custom font
      const uikitFontStack = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif';
      css += `.olo-template { background-color: var(--olo-color-background); font-size: var(--olo-font-size-base); line-height: var(--olo-line-height); color: var(--olo-color-text); font-family: var(--olo-font-family, ${uikitFontStack}); }\n`;
      for (let i = 1; i <= 6; i++) {
        css += `.olo-template h${i}, .olo-template .uk-h${i} { font-size: var(--olo-font-size-h${i}); }\n`;
      }
      css += `.olo-template h1, .olo-template h2, .olo-template h3, .olo-template h4, .olo-template h5, .olo-template h6 { font-weight: var(--olo-font-weight-heading); font-family: var(--olo-font-family-heading, var(--olo-font-family, ${uikitFontStack})); }\n`;

      // Links, buttons, etc.
      css += '.olo-template a { color: var(--olo-color-link); }\n';
      css += '.olo-template .uk-text-muted { color: var(--olo-color-text-muted) !important; }\n';
      css += '.olo-template .uk-button-primary { background-color: var(--olo-color-primary) !important; color: var(--olo-color-primary-contrast) !important; border-radius: var(--olo-border-radius); }\n';
      css += '.olo-template .uk-button-secondary { background-color: var(--olo-color-secondary) !important; color: var(--olo-color-secondary-contrast) !important; border-radius: var(--olo-border-radius); }\n';
      css += '.olo-template .uk-button-danger { background-color: var(--olo-color-danger) !important; color: #fff !important; border-radius: var(--olo-border-radius); }\n';
      css += '.olo-template .uk-button-default { border-radius: var(--olo-border-radius); }\n';
      css += '.olo-template .uk-alert-success { color: var(--olo-color-success); }\n';
      css += '.olo-template .uk-alert-warning { color: var(--olo-color-warning); }\n';
      css += '.olo-template .uk-alert-danger { color: var(--olo-color-danger); }\n';
      css += '.olo-template .uk-card { border-radius: var(--olo-border-radius-large); }\n';
      css += '.olo-template .uk-card-default { border-color: var(--olo-color-border); }\n';
      css += '.olo-template .uk-box-shadow-small { box-shadow: var(--olo-shadow-small) !important; }\n';
      css += '.olo-template .uk-box-shadow-medium { box-shadow: var(--olo-shadow-medium) !important; }\n';
      css += '.olo-template .uk-box-shadow-large { box-shadow: var(--olo-shadow-large) !important; }\n';
      css += '.olo-template .uk-container:not(.uk-container-expand) { max-width: var(--olo-container-max-width); padding-left: var(--olo-gutter-side); padding-right: var(--olo-gutter-side); }\n';
      css += '.olo-template .olo-container-narrow { max-width: var(--olo-container-narrow); margin-left: auto; margin-right: auto; }\n';
      css += '.olo-template .olo-container-wide   { max-width: var(--olo-container-wide); margin-left: auto; margin-right: auto; }\n';
      css += '.olo-template .olo-container-full   { max-width: 100%; }\n';
      css += '.olo-template .olo-section-pad-compact  { padding-top: var(--olo-section-pad-y-compact);  padding-bottom: var(--olo-section-pad-y-compact); }\n';
      css += '.olo-template .olo-section-pad-default  { padding-top: var(--olo-section-pad-y-default);  padding-bottom: var(--olo-section-pad-y-default); }\n';
      css += '.olo-template .olo-section-pad-spacious { padding-top: var(--olo-section-pad-y-spacious); padding-bottom: var(--olo-section-pad-y-spacious); }\n';

      // Gutter responsive
      const gDesk = parseInt(g.desktop ?? 32, 10);
      const gTab  = parseInt(g.tablet  ?? 24, 10);
      const gMob  = parseInt(g.mobile  ?? 16, 10);
      const gSideDesk = parseInt(g.side_desktop ?? 32, 10);
      const gSideMob  = parseInt(g.side_mobile  ?? 16, 10);
      if (gTab !== gDesk) {
        css += `@media (max-width: 960px) { .olo-template { --olo-gutter: ${gTab}px; } }\n`;
      }
      if (gMob !== gDesk || gSideMob !== gSideDesk) {
        css += `@media (max-width: 640px) { .olo-template { --olo-gutter: ${gMob}px; --olo-gutter-side: ${gSideMob}px; } }\n`;
      }

      // Fluid scaling
      const fs = s.fluid_scaling || {};
      if (fs.enabled) {
        const tabF = Math.max(0.3, Math.min(1.0, parseFloat(fs.tablet ?? 0.85)));
        const mobF = Math.max(0.3, Math.min(1.0, parseFloat(fs.mobile ?? 0.65)));
        const spDefaults = { xs: 4, sm: 8, md: 16, lg: 24, xl: 32, '2xl': 48, '3xl': 64, '4xl': 96 };
        const writeScaled = (factor) => {
          let out = '';
          for (const [k, def] of Object.entries(spDefaults)) {
            const raw = (sp[k] || `${def}px`).toString();
            const n = parseFloat(raw) || def;
            out += `    --olo-space-${k}: ${Math.round(n * factor * 100) / 100}px;\n`;
          }
          return out;
        };
        css += `\n@media (max-width: 960px) {\n  .olo-template {\n${writeScaled(tabF)}  }\n}\n`;
        css += `@media (max-width: 640px) {\n  .olo-template {\n${writeScaled(mobF)}  }\n}\n`;
      }

      // Dark Mode overrides
      const dc = s.dark_colors || {};
      const hasDark = Object.values(dc).some(v => v);
      if (hasDark) {
        css += '\n/* Dark Mode */\nhtml.olo-dark-mode .olo-template {\n';
        for (const [key, value] of Object.entries(dc)) {
          if (!value) continue;
          const prop = key.replace(/_/g, '-');
          css += `  --olo-color-${prop}: ${value};\n`;
        }
        css += '}\n';
      }

      // Grain / noise overlay (mirror del PHP Olo_Style_System::generate_css).
      const grain = s.grain || {};
      if (grain.enabled) {
        const gOp = Math.max(0, Math.min(30, parseInt(grain.opacity ?? 6, 10))) / 100;
        const gScale = Math.max(60, Math.min(400, parseInt(grain.scale ?? 180, 10)));
        const noise = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E";
        css += '\n/* Grain / noise overlay */\n';
        css += '.olo-template { position: relative; }\n';
        css += `.olo-template::after { content: ""; position: fixed; inset: 0; z-index: 9999; pointer-events: none; mix-blend-mode: overlay; opacity: ${gOp}; background-image: url("${noise}"); background-size: ${gScale}px ${gScale}px; }\n`;
      }

      return css;
    },
  },

  actions: {
    updateColor(key, value) {
      if (!this.styles.colors) this.styles.colors = {};
      this.styles.colors[key] = value;
      this.isDirty = true;
    },

    updateDarkColor(key, value) {
      if (!this.styles.dark_colors) this.styles.dark_colors = {};
      this.styles.dark_colors[key] = value;
      this.isDirty = true;
    },

    updateTypography(key, value) {
      if (!this.styles.typography) this.styles.typography = {};
      this.styles.typography[key] = value;
      this.isDirty = true;
    },

    updateLayout(key, value) {
      if (!this.styles.layout) this.styles.layout = {};
      this.styles.layout[key] = value;
      this.isDirty = true;
    },

    updateSpacing(key, value) {
      if (!this.styles.spacing) this.styles.spacing = {};
      this.styles.spacing[key] = value;
      this.isDirty = true;
    },

    updateRadiusScale(key, value) {
      if (!this.styles.border_radius_scale) this.styles.border_radius_scale = {};
      this.styles.border_radius_scale[key] = value;
      this.isDirty = true;
    },

    updateShadow(key, value) {
      if (!this.styles.shadows) this.styles.shadows = {};
      this.styles.shadows[key] = value;
      this.isDirty = true;
    },

    updateSectionPadding(key, value) {
      if (!this.styles.section_padding) this.styles.section_padding = {};
      this.styles.section_padding[key] = value;
      this.isDirty = true;
    },

    updateGutter(key, value) {
      if (!this.styles.gutter) this.styles.gutter = {};
      this.styles.gutter[key] = value;
      this.isDirty = true;
    },

    updateFluidScaling(key, value) {
      if (!this.styles.fluid_scaling) this.styles.fluid_scaling = {};
      this.styles.fluid_scaling[key] = value;
      this.isDirty = true;
    },

    updateGrain(key, value) {
      if (!this.styles.grain) this.styles.grain = {};
      this.styles.grain[key] = value;
      this.isDirty = true;
    },

    exportDesignTokens() {
      const tokens = {
        colors: this.styles.colors || {},
        dark_colors: this.styles.dark_colors || {},
        typography: this.styles.typography || {},
        layout: this.styles.layout || {},
        spacing: this.styles.spacing || {},
        border_radius_scale: this.styles.border_radius_scale || {},
        shadows: this.styles.shadows || {},
        google_fonts: this.styles.google_fonts || [],
        global_colors: this.globalColors || [],
        global_typography: this.globalTypography || [],
      };
      const blob = new Blob([JSON.stringify(tokens, null, 2)], { type: 'application/json' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'olobuild-design-tokens.json';
      a.click();
      URL.revokeObjectURL(url);
    },

    importDesignTokens(tokens) {
      if (tokens.colors) this.styles.colors = { ...tokens.colors };
      if (tokens.dark_colors) this.styles.dark_colors = { ...tokens.dark_colors };
      if (tokens.typography) this.styles.typography = { ...tokens.typography };
      if (tokens.layout) this.styles.layout = { ...tokens.layout };
      if (tokens.spacing) this.styles.spacing = { ...tokens.spacing };
      if (tokens.border_radius_scale) this.styles.border_radius_scale = { ...tokens.border_radius_scale };
      if (tokens.shadows) this.styles.shadows = { ...tokens.shadows };
      if (tokens.google_fonts) this.styles.google_fonts = [...tokens.google_fonts];
      if (tokens.global_colors) this.globalColors = [...tokens.global_colors];
      if (tokens.global_typography) this.globalTypography = [...tokens.global_typography];
      this.isDirty = true;
      this.globalColorsDirty = true;
      this.globalTypographyDirty = true;
    },

    applyPreset(presetKey) {
      const preset = this.presets[presetKey];
      if (!preset) return;
      this.styles.colors = { ...preset.colors };
      this.styles.typography = { ...preset.typography };
      this.styles.layout = { ...preset.layout };
      if (preset.spacing) this.styles.spacing = { ...preset.spacing };
      if (preset.border_radius_scale) this.styles.border_radius_scale = { ...preset.border_radius_scale };
      if (preset.shadows) this.styles.shadows = { ...preset.shadows };
      this.isDirty = true;
    },

    addGoogleFont(fontName) {
      if (!fontName) return;
      if (!this.styles.google_fonts) this.styles.google_fonts = [];
      if (!this.styles.google_fonts.includes(fontName)) {
        this.styles.google_fonts.push(fontName);
        this.isDirty = true;
      }
    },

    removeGoogleFont(fontName) {
      if (!this.styles.google_fonts) return;
      this.styles.google_fonts = this.styles.google_fonts.filter(f => f !== fontName);
      this.isDirty = true;
    },

    async saveStyles() {
      if (this.isSaving) return inCorso();
      this.isSaving = true;
      try {
        const res = await chiedi(`${oloData.restUrl}styles`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.styles),
        });
        const data = await res.json();
        this.generatedCss = data.css;
        this.isDirty = false;
        return { ok: true };
      } catch (err) {
        console.error('saveStyles error:', err);
        return { ok: false, motivo: motivoErrore(err) };
      } finally {
        this.isSaving = false;
      }
    },

    async resetStyles() {
      if (this.isSaving) return inCorso();
      this.isSaving = true;
      try {
        const res = await chiedi(`${oloData.restUrl}styles/reset`, { method: 'POST' });
        const data = await res.json();
        this.styles = data.styles;
        this.generatedCss = data.css;
        this.isDirty = false;
        return { ok: true };
      } catch (err) {
        console.error('resetStyles error:', err);
        return { ok: false, motivo: motivoErrore(err) };
      } finally {
        this.isSaving = false;
      }
    },

    // === Global Colors ===

    setGlobalColors(colors) {
      this.globalColors = colors;
      this.globalColorsDirty = true;
    },

    // `lista` è la palette da scrivere: diventa quella dello store (e delle swatch)
    // SOLO se il server l'ha scritta. Il «+» la passava allo store prima dell'esito,
    // e la swatch nuova, cliccata durante il volo, legava la tile a un token che
    // un salvataggio fallito lasciava inesistente.
    // La fusione riprende dal server ogni colore che `lista` non ha, anche quello
    // tolto con la «×»: cancellarlo davvero farebbe perdere il colore, in silenzio,
    // alle tile che usano var(--olo-color-<id>) in altre pagine, in header e footer.
    // La cancellazione vera spetta alla scheda globali-colore-eliminato-rompe
    // (eliminazione morbida, riserva nel token, conteggio d'uso).
    async saveGlobalColors(lista = this.globalColors) {
      // Flag DEDICATO (non this.isSaving, condiviso con saveStyles): aggiungere un colore
      // globale non deve essere saltato durante un salvataggio stili/autosave.
      if (this.savingColors) return inCorso();
      this.savingColors = true;
      try {
        // Merge-safe: rileggi dal server e unisci, per non perdere colori aggiunti altrove
        // (es. dal pannello admin) con uno store stale del builder.
        // Se la rilettura non riesce, o non restituisce una lista, non si scrive: la
        // lista locale, forse vecchia, sostituirebbe quella del server cancellando i
        // colori aggiunti altrove.
        const rr = await chiedi(`${oloData.restUrl}global-colors`);
        const server = await rr.json();
        if (!Array.isArray(server)) throw new ErroreRest(t('risposta non valida del server'));
        const byId = new Map();
        for (const g of (lista || [])) { if (g && g.id) byId.set(g.id, g); }
        for (const sg of server) { if (sg && sg.id && !byId.has(sg.id)) byId.set(sg.id, sg); }
        const merged = Array.from(byId.values());
        const res = await chiedi(`${oloData.restUrl}global-colors`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(merged),
        });
        const data = await res.json();
        this.globalColors = data;
        this.globalColorsDirty = false;
        return { ok: true };
      } catch (err) {
        console.error('saveGlobalColors error:', err);
        return { ok: false, motivo: motivoErrore(err) };
      } finally {
        this.savingColors = false;
      }
    },

    async loadGlobalColors() {
      try {
        const res = await fetch(`${oloData.restUrl}global-colors`, {
          headers: { 'X-WP-Nonce': oloData.nonce },
        });
        if (res.ok) {
          this.globalColors = await res.json();
        }
      } catch (err) {
        console.error('loadGlobalColors error:', err);
      }
    },

    // === Global Typography ===

    setGlobalTypography(sets) {
      this.globalTypography = sets;
      this.globalTypographyDirty = true;
    },

    // I set passati diventano quelli dello store (offerti alle tile) SOLO se il
    // server li ha scritti. La bozza (globalTypographyDraft) la scrive il pannello
    // quando si chiude; qui si toglie solo se è proprio ciò che si è scritto: una
    // bozza diversa l'ha lasciata un pannello chiuso DOPO l'invio, con modifiche
    // successive, e resta da salvare. Un errore non la tocca.
    async saveGlobalTypography(sets = this.globalTypography) {
      if (this.savingTypography) return inCorso();
      this.savingTypography = true;
      const inviato = JSON.stringify(sets || []);
      try {
        const res = await chiedi(`${oloData.restUrl}global-typography`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: inviato,
        });
        const data = await res.json();
        this.globalTypography = data;
        this.globalTypographyDirty = false;
        if (this.globalTypographyDraft && JSON.stringify(this.globalTypographyDraft) === inviato) {
          this.globalTypographyDraft = null;
        }
        return { ok: true };
      } catch (err) {
        console.error('saveGlobalTypography error:', err);
        return { ok: false, motivo: motivoErrore(err) };
      } finally {
        this.savingTypography = false;
      }
    },

    // === Custom Presets ===

    async loadCustomPresets() {
      try {
        const res = await fetch(`${oloData.restUrl}design-presets`, {
          headers: { 'X-WP-Nonce': oloData.nonce },
        });
        if (res.ok) {
          this.customPresets = await res.json();
        }
      } catch (err) {
        console.error('loadCustomPresets error:', err);
      }
    },

    async saveCurrentAsPreset(name) {
      try {
        const style = {
          colors: { ...(this.styles.colors || {}) },
          typography: { ...(this.styles.typography || {}) },
          layout: { ...(this.styles.layout || {}) },
          spacing: { ...(this.styles.spacing || {}) },
          border_radius_scale: { ...(this.styles.border_radius_scale || {}) },
          shadows: { ...(this.styles.shadows || {}) },
        };
        const res = await fetch(`${oloData.restUrl}design-presets`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': oloData.nonce,
          },
          body: JSON.stringify({ name, style }),
        });
        if (!res.ok) throw new Error('Failed to save preset');
        const newPreset = await res.json();
        this.customPresets.push(newPreset);
        return newPreset;
      } catch (err) {
        console.error('saveCurrentAsPreset error:', err);
        throw err;
      }
    },

    async deleteCustomPreset(id) {
      try {
        const res = await fetch(`${oloData.restUrl}design-presets/${id}`, {
          method: 'DELETE',
          headers: { 'X-WP-Nonce': oloData.nonce },
        });
        if (!res.ok) throw new Error('Failed to delete preset');
        this.customPresets = this.customPresets.filter(p => p.id !== id);
      } catch (err) {
        console.error('deleteCustomPreset error:', err);
        throw err;
      }
    },

    applyCustomPreset(preset) {
      const s = preset.style || {};
      if (s.colors) this.styles.colors = { ...s.colors };
      if (s.typography) this.styles.typography = { ...s.typography };
      if (s.layout) this.styles.layout = { ...s.layout };
      if (s.spacing) this.styles.spacing = { ...s.spacing };
      if (s.border_radius_scale) this.styles.border_radius_scale = { ...s.border_radius_scale };
      if (s.shadows) this.styles.shadows = { ...s.shadows };
      this.isDirty = true;
    },

    async loadGlobalTypography() {
      try {
        const res = await fetch(`${oloData.restUrl}global-typography`, {
          headers: { 'X-WP-Nonce': oloData.nonce },
        });
        if (res.ok) {
          this.globalTypography = await res.json();
        }
      } catch (err) {
        console.error('loadGlobalTypography error:', err);
      }
    },
  },
});
