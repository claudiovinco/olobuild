import { defineStore } from 'pinia';

const oloData = window.oloData || {};

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
    generatedCss: oloData.stylesCss || '',
    isDirty: false,
    isSaving: false,
  }),

  getters: {
    colors: (state) => state.styles.colors || {},
    typography: (state) => state.styles.typography || {},
    layout: (state) => state.styles.layout || {},
    googleFonts: (state) => state.styles.google_fonts || [],

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
      // Shadows
      css += '  --olo-shadow-small: 0 1px 2px 0 rgba(0,0,0,0.05);\n';
      css += '  --olo-shadow-medium: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);\n';
      css += '  --olo-shadow-large: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);\n';
      css += '}\n\n';

      // UIkit overrides – bg WITHOUT !important so inline styles (custom bg) can win
      css += '.olo-template .uk-section-primary { background-color: var(--olo-color-primary); color: var(--olo-color-primary-contrast) !important; }\n';
      css += '.olo-template .uk-section-primary :where(a) { color: var(--olo-color-primary-contrast) !important; }\n';
      css += '.olo-template .uk-section-secondary { background-color: var(--olo-color-secondary); color: var(--olo-color-secondary-contrast) !important; }\n';
      css += '.olo-template .uk-section-secondary :where(a) { color: var(--olo-color-secondary-contrast) !important; }\n';
      css += '.olo-template .uk-section-muted { background-color: var(--olo-color-muted); color: var(--olo-color-muted-contrast) !important; }\n';

      // Typography overrides
      css += '.olo-template { background-color: var(--olo-color-background); font-size: var(--olo-font-size-base); line-height: var(--olo-line-height); color: var(--olo-color-text); }\n';
      if (t.font_family) css += '.olo-template { font-family: var(--olo-font-family); }\n';
      for (let i = 1; i <= 6; i++) {
        css += `.olo-template h${i}, .olo-template .uk-h${i} { font-size: var(--olo-font-size-h${i}); }\n`;
      }
      css += '.olo-template h1, .olo-template h2, .olo-template h3, .olo-template h4, .olo-template h5, .olo-template h6 { font-weight: var(--olo-font-weight-heading); }\n';
      if (t.font_family_heading) {
        css += '.olo-template h1, .olo-template h2, .olo-template h3, .olo-template h4, .olo-template h5, .olo-template h6 { font-family: var(--olo-font-family-heading); }\n';
      }

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
      css += '.olo-template .uk-container { max-width: var(--olo-container-max-width); }\n';

      return css;
    },
  },

  actions: {
    updateColor(key, value) {
      if (!this.styles.colors) this.styles.colors = {};
      this.styles.colors[key] = value;
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

    applyPreset(presetKey) {
      const preset = this.presets[presetKey];
      if (!preset) return;
      this.styles.colors = { ...preset.colors };
      this.styles.typography = { ...preset.typography };
      this.styles.layout = { ...preset.layout };
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
      if (this.isSaving) return;
      this.isSaving = true;
      try {
        const res = await fetch(`${oloData.restUrl}/styles`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': oloData.nonce,
          },
          body: JSON.stringify(this.styles),
        });
        if (!res.ok) throw new Error('Failed to save styles');
        const data = await res.json();
        this.generatedCss = data.css;
        this.isDirty = false;
      } catch (err) {
        console.error('saveStyles error:', err);
      } finally {
        this.isSaving = false;
      }
    },

    async resetStyles() {
      if (this.isSaving) return;
      this.isSaving = true;
      try {
        const res = await fetch(`${oloData.restUrl}/styles/reset`, {
          method: 'POST',
          headers: {
            'X-WP-Nonce': oloData.nonce,
          },
        });
        if (!res.ok) throw new Error('Failed to reset styles');
        const data = await res.json();
        this.styles = data.styles;
        this.generatedCss = data.css;
        this.isDirty = false;
      } catch (err) {
        console.error('resetStyles error:', err);
      } finally {
        this.isSaving = false;
      }
    },
  },
});
