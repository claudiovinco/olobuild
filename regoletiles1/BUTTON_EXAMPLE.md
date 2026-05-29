# Esempio guida — refactor della tile **Button**

Worked example dei tre interventi prioritari (token-first defaults + fonte unica
+ `useBoxModel`) su una sola tile, **senza cambiare le chiavi salvate**. È il
modello da replicare sulle altre tile, una categoria per volta.

> ⚠️ Pseudo-diff di riferimento: adattare import/percorsi e verificare i nomi chiave
> reali. Le chiavi salvate (`bg_color`, `text_color`, `border_radius`, `tile_padding`…)
> restano identiche: cambia solo da DOVE arrivano i default e COME si risolvono.

## 1. `button.js` — i default diventano l'unica fonte, token-first

```diff
+ import { buildDefaults } from '@/composables/oloTileDefaults';

  export default {
    type: 'button',
    name: t('Pulsante'),
    icon: 'dashicons-button',
    category: 'essential',
-   defaults: {
-     preset: 'custom',
-     text: t('Clicca qui'),
-     bg_color: '',
-     text_color: '',
-     border_radius: '6',
-     tile_padding: { top: 14, right: 32, bottom: 14, left: 32 },
-     font_size: '16',
-     font_weight: '600',
-     shadow: 'none',
-     ...
-   },
+   // Fonte UNICA dei default (curati). Estende il base condiviso.
+   defaults: {
+     preset: 'custom',
+     target: '_self',
+     alignment: 'center',
+     icon: '', icon_position: 'before', icon_spacing: '8',
+     ...buildDefaults('button'),   // text, bg_color:'', text_color:'',
+                                   // border_radius:10, tile_padding(SPACE),
+                                   // font_*, shadow:'sm', hover_effect:'lift'
+   },
    ...
  };
```

## 2. `ButtonTile.vue` — niente default ridichiarati, colori token-first, box-model via composable

```diff
  <script setup>
  import { computed } from 'vue';
  import iconsSvg from '../ProSlider/uikitIconsSvg.js';
  import { useBuilderStore } from '@/stores/builder';
  import { rv } from '@/composables/useResponsiveValue';
  import { getShadowValue } from '@/composables/useShadowMap';
+ import { useBoxModel } from '@/composables/useBoxModel';
+ import { resolveColor, TOKENS, buildDefaults } from '@/composables/oloTileDefaults';

  const props = defineProps({ settings: { type: Object, default: () => ({}) } });
  const builderStore = useBuilderStore();

- // ❌ default ridichiarati qui (divergono da button.js: bg_color '#6366F1' vs '')
- const s = computed(() => ({
-   text: 'Clicca qui',
-   bg_color: '#6366F1',
-   text_color: '#FFFFFF',
-   border_radius: '6',
-   tile_padding: { top: 14, right: 32, bottom: 14, left: 32 },
-   ...props.settings,
- }));
+ // ✓ una sola fonte: stessi default del registry
+ const s = computed(() => ({ ...buildDefaults('button'), ...props.settings }));

+ // ✓ box-model normalizzato (gestisce numero|oggetto + legacy padding_x/y)
+ const { radiusCss, paddingCss } = useBoxModel(s, {
+   radiusKey: 'border_radius', radiusFallback: 10,
+   paddingKey: 'tile_padding', paddingFallback: [12, 24, 12, 24],
+   paddingLegacy: ['padding_y', 'padding_x'],
+ });

  const btnStyle = computed(() => {
-   // ❌ parsing radius/padding a mano + fallback indaco
-   let borderRadius = typeof rad === 'object' ? `${rad.tl}px …` : `${rad||6}px`;
    const style = {
      display: 'inline-block',
-     padding: `${padTop}px ${padRight}px …`,
-     backgroundColor: hasCreative ? 'transparent' : (s.value.bg_color || '#6366F1'),
-     color: s.value.text_color || '#FFFFFF',
-     borderRadius,
+     padding: paddingCss.value,
+     backgroundColor: hasCreative ? 'transparent' : resolveColor(s.value.bg_color, TOKENS.primary),
+     color: resolveColor(s.value.text_color, TOKENS.onPrimary),
+     borderRadius: radiusCss.value,
      fontSize: `${rv(props.settings,'font_size',s.value.font_size,builderStore.viewMode)||16}px`,
      fontWeight: s.value.font_weight || '600',
    };
    const sh = getShadowValue(s.value);
    if (sh !== 'none') style.boxShadow = sh;
    return style;
  });
  </script>
```

## 3. (Opzionale, step a11y) render semantico

```diff
- <span :style="btnStyle" class="mb-relative"> … </span>
+ <a :href="s.url || '#'" :target="s.target" :style="btnStyle"
+    class="mb-relative" role="button" :aria-label="s.text"> … </a>
```

## Risultato

- Pulsante inserito → **rosso brand** (token), raggio 10, padding da scala, micro-ombra.
- **Un solo punto** definisce i default (`buildDefaults('button')`): niente più divergenze
  config↔componente.
- Radius/padding normalizzati da `useBoxModel`: il parsing sparisce dal componente.
- Chiavi salvate invariate → nessuna migrazione dati, template esistenti intatti.

## Checklist di propagazione (per categoria)

- [ ] Button (questo esempio) → validare diff
- [ ] Tile essenziali: Headline, Icon, Divider, Alert
- [ ] Layout: Hero, Grid, Column, CTA Banner
- [ ] Media: Gallery, Carousel, Chart
- [ ] Interattive: Form, Accordion, Tabs, Flipcard
- [ ] Sostituire emoji default → icone SVG del set
- [ ] Pass a11y: `<a>`/`<button>`, `aria-label`, avviso contrasto AA nell'editor
