# La tile perfetta — manuale di riferimento Olobuild

> Distillato dai pattern reali del codice Olobuild (v3.49.x). Utile per:
> - **Creare** un nuovo tile partendo dallo stato dell'arte
> - **Confrontare** un tile esistente con questa checklist per scovarne limiti
> - **Decidere** se una richiesta utente è già coperta o richiede estensione

---

## 0. Anatomia di un tile

Un tile Olobuild vive in **5 file** correlati:

| File | Ruolo |
|---|---|
| `src/config/elements/<type>.js` | Inspector schema (defaults JS + fields per il pannello laterale) |
| `src/components/Tiles/<Name>Tile.vue` | Anteprima Vue (canvas builder, WYSIWYG) |
| `includes/tiles/class-<type>-tile.php` | Render PHP server-side (frontend + iframe builder) |
| `src/components/Builder/BuilderInspector.vue` (`TILE_PRESETS`) | Mappa preset → settings values |
| `assets/css/frontend.css` (opzionale) | Stili condivisi tra builder e frontend |

Sorgente di verità: i defaults **devono coincidere** tra JS e PHP. Se divergono, l'utente vede una cosa nel builder e un'altra nel frontend.

---

## 1. Identità

Ogni tile dichiara 4 campi identitari:

```js
type:     'image',           // SLUG univoco, no spazi, no trattini riservati (vedi §1.1)
name:     t('Immagine'),     // Label umana, sempre tradotta con t()
icon:     'dashicons-format-image',  // Dashicons WP o emoji
category: 'media',           // essential | text | media | layout | marketing | data | …
```

### 1.1 Naming `type`

- Convenzione **kebab-case** se il nome è multi-parola: `text-block`, `imgcompare`, `icon-tabs`, ma **molti vecchi tile usano camelcase senza separatori**: `iconbox`, `iconlist`, `proslider`. Il pattern **non è coerente** in tutto il codebase — controllare i tile esistenti prima di scegliere il nome.
- ⚠️ Il `type` è usato come stringa di lookup ovunque (registry, REST, frontend, ContextMenu) — **non rinominare** un tile esistente senza migration DB.

---

## 2. Defaults coerenti JS ↔ PHP

⚠️ **Regola n°1 di stabilità**: ogni chiave usata nel render PHP deve avere un default nel JS (così il client la espone) **e** nel PHP (così wp_parse_args non lascia chiavi `undefined`).

```js
// src/config/elements/foo.js
defaults: {
  preset: 'custom',
  text: 'Hello',
  text_color: '',
  font_size: '16',
  // … sempre seguito dai mixin condivisi:
  ...textEffectsDefaults,         // 12 chiavi text_effect_*
  ...borderEffectDefaults,         // border_effect_*
  border: { ...borderDefault },
  border_hover: { ...borderHoverDefault },
  border_hover_duration: 300,
}
```

```php
// includes/tiles/class-foo-tile.php
protected $defaults = [
    'preset'              => 'custom',
    'text'                => 'Hello',
    'text_color'          => '',
    'font_size'           => '16',
    // Text effects (12 chiavi):
    'text_effect'             => 'none',
    'text_effect_target'      => 'XXX',  // ⚠ override coerente con dropdown!
    'text_effect_speed'       => '50',
    // … completare TUTTE le 12 chiavi
];
```

### 2.1 Bug noto: `text_effect_target` mismatch

Il default di `textEffectsDefaults` è `'heading'`. Se il dropdown del tile espone target diversi (es. `'text'`, `'caption'`, `'content'`), l'utente attiva un effetto ma **niente si applica** perché il target salvato resta `'heading'`. **Pattern obbligatorio**:

```js
defaults: {
  ...textEffectsDefaults,
  text_effect_target: 'caption',  // override coerente con la prima/unica opzione del dropdown
}
```

Stesso override **anche** nel PHP defaults.

---

## 3. Inspector — sezioni canoniche

Le sezioni dell'inspector seguono un ordine pensato per il workflow utente. Adottare per coerenza UX:

```
1. PRESET STILISTICO       — preset (select 12+1 opzioni)
2. CONTENUTO               — text, items, image_url, ecc.
3. EFFETTI TESTO           — textEffectsFields()
4. STILE                   — color, font_size, tipografia
5. ICONA / IMMAGINE        — icon_color, icon_size, icon_shape, ecc.
6. LAYOUT                  — alignment, max_width, gap, columns
7. SFONDO                  — bg (type unificato)
8. SPAZIATURA              — tile_padding, tile_margin (spacing)
9. BORDO                   — borderFields()
10. OMBRA                  — shadowField
11. AVANZATE               — text_effects, border_effects, custom CSS, ecc.
```

Ogni separator usa `{ type: 'separator', label: t('Nome sezione') }`. **MAI** `type: 'heading'` (non esiste nel sistema).

---

## 4. Preset stilistici — 5 sicuri + 7 audaci + 1 custom

⚠️ **Regola madre di "creatività su demand"**: ogni tile con presentazione visiva propone **13 preset**:

```js
{ key: 'preset', label: t('Stile'), type: 'select', options: [
  // 5 sicuri ◆ — pronti per agency/clienti tradizionali
  { value: 'modern-clean',    label: t('◆ Modern Clean') },
  { value: 'minimal-mono',    label: t('◆ Minimal Mono') },
  { value: 'magazine-XXX',    label: t('◆ Magazine ...') },
  { value: 'editorial-serif', label: t('◆ Editorial Serif') },
  { value: 'compact-inline',  label: t('◆ Compact Inline') },
  // 7 audaci — design di tendenza per portfolio/landing
  { value: 'glass-XXX',       label: t('✨ Glass ...') },
  { value: 'neon-XXX',        label: t('⚡ Neon ...') },
  { value: 'brutalist-XXX',   label: t('⬛ Brutalist ...') },
  { value: 'gradient-XXX',    label: t('🌊 Gradient ...') },
  { value: 'sticker-XXX',     label: t('🏷 Sticker ...') },
  { value: 'retro-XXX',       label: t('▌ Retro ...') },
  { value: 'tilt-XXX',        label: t('🃏 Tilt 3D') },
  // 1 libertà totale
  { value: 'custom',          label: t('Personalizzato') },
]}
```

### 4.1 Apply-once architecture

I preset **scrivono valori** nei settings al momento della selezione (via `applyTilePresetTheme` in `BuilderInspector.vue`), **non** override CSS runtime. Conseguenze:
- L'utente può **sovrascrivere singoli setting** dopo aver applicato un preset
- Il preset finisce "incollato" nel template salvato — niente dipendenze dinamiche
- Per modificare un preset esistente serve aggiornare `TILE_PRESETS` mappa

```js
TILE_PRESETS = {
  iconlist: {
    'modern-clean':    { icon_color: '#22c55e', icon_size: '22', text_size: '16', gap: '14', ... },
    // ⚠ NIENTE oggetti vuoti {} — deve produrre differenze visive evidenti
  }
}
```

⚠️ **Errore comune**: definire preset come `{}` vuoto. Conseguenza: l'utente seleziona "Glass Rows" e non vede nessun cambiamento → percepito come bug, valido come critica.

### 4.2 Regola Preset universale — 5 dimensioni obbligatorie

> Aggiunta v3.57.24 — fix percepito di "preset che non cambiano nulla".

Ogni voce di `TILE_PRESETS[tileType]` DEVE modificare almeno queste 5 dimensioni — altrimenti il cambio è invisibile a colpo d'occhio e l'utente lo percepisce come bug:

| # | Dimensione | Dove agire | Esempio (preset `neon-cyberpunk` hero) |
|---|------------|------------|----------------------------------------|
| 1 | **Tipografia** | `settings.title_*`, `settings.subtitle_*` | `title_font_weight: '700'`, `title_letter_spacing: '2'`, `title_text_transform: 'uppercase'` |
| 2 | **Layout** | `settings.tile_padding`, `min_height`, `*_align`, `content_max_width` | `min_height: '680px'`, `text_align: 'center'`, `tile_padding: { top: 80, ... }` |
| 3 | **Colori** | `settings.title_color`, `subtitle_color`, `text_color`, `cta_*_color`, `style.bg` | `title_color: '#ff6a2a'`, `style.bg: { type: 'solid', color: '#0a0f1c' }` |
| 4 | **Effetti CSS dedicati** | `assets/css/frontend.css` (selettore `.olo-{tile}-preset-{id}`) | `text-shadow: 0 0 20px currentColor; animation: olo-hero-neon-pulse 2.4s infinite;` |
| 5 | **Animazione entrance** | `settings.entrance_animation` | `entrance_animation: 'slide-up'` |

#### Architettura della firma visiva (dimensione #4)

Il renderer PHP della tile **deve** applicare al wrapper la classe `olo-{tile}-preset-{sanitize_key($preset)}`. Esempio: `class-hero-tile.php` riga 233:

```php
<div class="olo-hero <?php echo esc_attr( $uid ); ?>
            olo-hero-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>">
```

Poi in `frontend.css` si aggiunge una sezione dedicata con le 12 regole (una per preset). Convenzione attuale (sezione "Preset visivi per tile Hero" in frontend.css):

```css
/* Neon → glow pulsante */
.olo-hero-preset-neon-cyberpunk .olo-hero-title {
  text-shadow: 0 0 8px currentColor, 0 0 20px currentColor, 0 0 40px currentColor;
  animation: olo-hero-neon-pulse 2.4s ease-in-out infinite;
}

/* Glass → backdrop-filter */
.olo-hero-preset-glass-overlay .olo-hero-inner {
  backdrop-filter: blur(20px) saturate(180%);
  background: rgba(255,255,255,.22);
}

/* Brutalist → shadow hard offset */
.olo-hero-preset-brutalist-mega .olo-hero-title { text-shadow: 6px 6px 0 #000; }

/* Retro → scanlines overlay */
.olo-hero-preset-retro-poster::before { background: repeating-linear-gradient(0deg, rgba(0,0,0,.18) 0px 1px, transparent 1px 3px); }

/* Tilt → perspective 3D */
.olo-hero-preset-tilt-parallax:hover .olo-hero-content { transform: rotateX(2deg) rotateY(-2deg) scale(1.02); }
```

#### Checklist prima di considerare un preset "finito"

- [ ] Tipografia distintiva (almeno 2 setting tra family/weight/letter-spacing/transform)
- [ ] Layout distinto (padding o allineamento o min-height diversi dagli altri preset)
- [ ] Colori coordinati (text + bg + CTA — almeno bg con `style.bg`)
- [ ] **Classe CSS firma in frontend.css** con almeno 1 effetto (glow, blur, shadow, scanlines, parallax, gradient animato, ...)
- [ ] **`entrance_animation` impostato** (NON `'none'` di default)
- [ ] Differenza visibile a colpo d'occhio rispetto al preset successivo nell'elenco

I tile hero (v3.57.24) sono il riferimento canonico — replicare il pattern per gli altri tile con preset audaci.

---

## 5. Tipografia & testo

### 5.1 Allineamento (incluso `justify`)

```js
{ key: 'text_align', label: t('Allineamento testo'), type: 'select', options: [
  { value: 'left',    label: t('Sinistra') },
  { value: 'center',  label: t('Centrato') },
  { value: 'right',   label: t('Destra') },
  { value: 'justify', label: t('Giustificato') },  // ⚠ non dimenticare
]}
```

Sui tile con layout flex (list, iconlist), applicare `text-align` allo **span del testo** + `flex: 1` per far funzionare il `text-align: justify` su una sola riga.

### 5.2 Effetti testo

Riusare `textEffectsFields()` da `_shared.js`. Le 11 opzioni: `none, typewriter, typewriter-loop, reveal-letter, reveal-word, gradient-anim, glitch, wave, underline-grow, highlight-grow, scramble`. Render PHP via `$this->tfx_attrs($s, 'target', $plain_text)` + `$this->tfx_css($s, $scope_selector)` + `$this->tfx_print_script()`.

### 5.3 Multi-colonne (CSS columns)

Per tile con contenuto testuale lungo (text-block): esporre `columns` (range 1-4) + `column_gap` (range 0-80). Render:
```php
if ($cols > 1) {
    $style .= 'column-count:' . $cols . ';column-gap:' . $gap . 'px;';
}
```

### 5.4 Tipografia globale

Esporre sempre `typography_preset` con `optionsSource: 'globalTypography'` per ereditare i preset tipografici globali del sito. Pattern:

```php
$tp = sanitize_text_field( $s['typography_preset'] ?? '' );
if ( $tp ) {
    $style .= "font-family:var(--olo-font-{$tp}-family);";
    $style .= "font-weight:var(--olo-font-{$tp}-weight);";
    // …
}
```

---

## 6. Sfondo creativo unificato

Pattern moderno: campo `bg` di type `'background'` che riusa `BackgroundControls.vue`. Supporta **7 modalità**:

```
none | solid | gradient | pattern | image | video | gallery
```

Defaults JS:
```js
bg: { type: 'none' }
```

Render PHP via `Olo_CSS_Builder` (NIENTE duplicazione):
```php
$bg_creative_css = '';
$bg_obj = $s['bg'] ?? [ 'type' => 'none' ];
if ( is_array( $bg_obj ) && ( $bg_obj['type'] ?? 'none' ) !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
    $cssb = new Olo_CSS_Builder();
    $bg_creative_css = $cssb->get_bg_inline_css( $bg_obj );
}
```

### 6.1 Backward compat

Se un tile aveva già `bg_color` legacy, **lasciarlo**: il nuovo `bg` con type `'none'` non lo tocca. Solo se `bg.type !== 'none'` sovrascrive. Esempio button: prima esposto solo `bg_color`, ora `bg` (creativo) + `bg_color` (semplice fallback).

---

## 7. Bordo & angoli

### 7.1 Border system (4 lati indipendenti)

⚠️ **Standard obbligatorio**: usare `type: 'border'` (FieldBorder), MAI 4 range separati.

```js
defaults: {
  border:       { ...borderDefault },        // 4 lati indipendenti
  border_hover: { ...borderHoverDefault },   // override hover
  border_hover_duration: 300,
  ...borderEffectDefaults,  // 4 effetti: neon, neon-pulse, gradient, gradient-spin
}

fields: [
  ...borderFields(),  // genera tutti i field necessari
]
```

Render PHP:
```php
$border_css        = $this->build_border_css( $s['border'] ?? [] );
$border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
$border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
```

### 7.2 Border-radius 4 angoli + hover

⚠️ Usare `type: 'border-radius'` (4 angoli indipendenti tl/tr/br/bl con link/unlink). MAI un semplice `range`.

```js
{ key: 'border_radius', label: t('Border Radius'), type: 'border-radius' }
```

Render: `$this->build_border_radius_css($value)`.

⚠️ **Hover radius**: 4 valori hover sostituiscono completamente i 4 base (anche se 0). Applicare solo al wrapper, mai img+wrapper insieme.

---

## 8. Hover bilaterale dichiarativo

⚠️ **Standard obbligatorio (v3.13+)**: pattern `withHover()` JS + `build_hover_css()` PHP. MAI parsing inline o field separati `hover_X` nel config.

```js
fields: [
  withHover(
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },
    { hoverKey: 'hover_bg_color' }
  ),
]
```

L'icona occhio compare automatica accanto al label e gestisce il toggle base/hover.

---

## 9. Spaziatura (FieldSpacing)

⚠️ **Standard obbligatorio**: usare `type: 'spacing'` (4 caselle + link/unlink), MAI 4 range separati.

```js
defaults: {
  tile_padding: { top: 16, right: 16, bottom: 16, left: 16 },
  tile_margin:  { top: 0,  right: 0,  bottom: 0,  left: 0 },
}
fields: [
  { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 100 },
  { key: 'tile_margin',  label: t('Margine (px)'),  type: 'spacing', min: -50, max: 100 },
]
```

---

## 10. Ombra (`shadowField`)

Riusare `shadowField` da `_shared.js` per il pattern uniforme. Render via `Olo_Tile_Utils::shadow($key, $variant)`:

| Variant | Quando usarla | Alpha range |
|---|---|---|
| `'standard'` | Tile generici, card, sezioni | 8-15% |
| `'photo'` | Tile con immagini (avatar, gallery item) | 20-40% |
| `'panel'` | Dropdown, popover, modal | 8-18% |
| `'button'` | Pulsanti, bottoni grandi | 18-35% |

⚠️ **Errore comune**: usare `'standard'` su un button con bg colorato pieno → ombra invisibile (alpha 8%). Scegliere variant **in base al contrasto** che servirà.

---

## 11. Icone (FieldIcon)

⚠️ **Standard obbligatorio**: per qualsiasi field icona/emoji usare `type: 'icon'` (FieldIcon), MAI `type: 'text'`. Vale anche per `itemFields` dentro `content-items`.

```js
{ key: 'icon', label: t('Icona'), type: 'icon' }
```

Nessun utente digiterà mai a mano `dashicons-format-image` o `⚡`.

---

## 12. Render PHP — pattern di sicurezza

### 12.1 Content HTML inline (`<strong>`, `<em>`, ecc.)

⚠️ **Bug ricorrente**: regex `'/^\s*</'` rileva HTML solo se il content **inizia** con `<`. Un paragrafo "Testo <strong>...</strong>" viene escapato come testo letterale.

Pattern corretto:
```php
if ( preg_match( '/<[a-z!\/][^>]*>/i', $content_raw ) ) {
    $content = wp_kses_post( $content_raw );  // sanitize ma preserva tag inline
} else {
    $content = nl2br( esc_html( $content_raw ) );  // plain text, nl2br
}
```

### 12.2 UID unico per scope CSS

Ogni render genera un UID unico per scopare CSS al singolo tile (evita bleed tra istanze):
```php
$uid = 'olo-tb-' . wp_unique_id();   // o wp_rand( 10000, 99999 );
echo '<div class="olo-text-block ' . $uid . '">' . $content . '</div>';
echo '<style>.' . $uid . ' { ... }</style>';
```

### 12.3 Mai `&&` negli script inline PHP

⚠️ WordPress converte `&&` in `&#038;&#038;` (entità HTML), rompendo l'intero blocco `<script>`. Usare sempre `if` annidati:
```php
// ❌ rotto
echo "<script>if(a&&b){...}</script>";
// ✅ funziona
echo "<script>if(a){if(b){...}}</script>";
```

### 12.4 wp_kses_post per HTML user-generated

Tutto l'HTML che arriva dall'editor utente passa per `wp_kses_post()` — preserva tag formatting (strong/em/a/p/etc.) e rimuove pericolosi (script/iframe). Mai `echo $user_content` raw.

### 12.5 esc_attr per CSS inline

Tutti i valori dentro `style="..."` o attributi HTML passano per `esc_attr()`. I colori che provengono dall'utente passano prima per `$this->safe_color_css($val)` che valida hex/rgba e blocca `expression(...)`, `javascript:`, ecc.

---

## 13. Anteprima Vue WYSIWYG (canvas builder)

⚠️ **Regola madre WYSIWYG**: ogni tile DEVE avere un Vue component che rende anteprima fedele nel builder, **zero placeholder testuali tipo "Aggiungi contenuto qui"**.

```vue
<!-- src/components/Tiles/FooTile.vue -->
<template>
  <div :class="['olo-foo', presetClass]" :style="containerStyle">
    <span :style="textStyle">{{ s.text }}</span>
  </div>
</template>
<script setup>
import { computed } from 'vue';
const props = defineProps({ tile: Object });
const s = computed(() => props.tile.settings || {});
// … computed styles che ricalcolano i settings come fa il PHP render
</script>
```

L'anteprima Vue **NON è il render iframe** (quello è PHP). È un'anteprima rapida nel canvas per UX builder. Se il tile ha logica server-pesante (es. queryloop, media gallery con DB lookup), l'anteprima Vue mostra un "approssimato fedele".

---

## 14. Internazionalizzazione

⚠️ **i18n completezza**: wrappare SEMPRE tutte le stringhe con `t()` (JS) o `__('...', 'olobuild')` (PHP). **Zero hardcoded italiano** nei file `.vue`.

```js
// ✅ corretto
name: t('Avviso'),
{ value: 'left', label: t('Sinistra') }

// ❌ rotto
name: 'Avviso',
{ value: 'left', label: 'Sinistra' }
```

```php
__( 'Cookie Consent', 'olobuild' )
```

---

## 15. Animazioni & interazioni

Olobuild ha già implementate:
- **36 entrance animations** (`olo-entrance-*` con IntersectionObserver)
- **8 infinite animations** (float, pulse, spin, wiggle, bounce, shake, glow, breathe)
- **Hover transforms** (scale, translateX/Y, rotate, skewX/Y + transition)
- **Mouse tilt 3D + tracking** (data-olo-tilt, data-olo-mouse-track)
- **Parallax scroll-linked** (multi-property: opacity, blur, scale, translateY)
- **Stagger figli + item interni** (olo-stagger-parent, griglia/slider/accordion/ecc.)

⚠️ Prima di proporre una nuova animazione, **leggere `_shared.js` e BuilderInspector** per evitare duplicati.

---

## 16. Inline editing (canvas)

Per i tile con testo prominente, esporre `data-olo-editable` sull'elemento. Il doppio click attiva contenteditable + floating toolbar Tiptap (`useInlineEdit.js` + `RichTextEditor.vue`). Già implementato in headline, text-block, button, ecc.

```php
<h2 data-olo-editable="heading"><?php echo esc_html($s['heading']); ?></h2>
```

---

## 17. Globalizzazione (Global Widgets / Salva come globale)

Ogni tile può essere "salvato come globale" via ContextMenu → entra in `wp_olo_global_widgets`. Istanze multiple usano `global_id` per linkare al record condiviso. Pattern non richiede codice nel tile: è gestito a livello di store/REST. Verificare solo che il render PHP non rompa se settings ha `global_id` (key extra che ignora).

---

## 18. Checklist finale "tile perfetta"

Prima di considerare un tile **completo**, verificare la presenza di:

### Inspector / config JS
- [ ] `type`, `name`, `icon`, `category` corretti
- [ ] `t()` su tutte le stringhe (zero IT hardcoded)
- [ ] Preset (12 + custom) con valori reali, mai `{}` vuoto
- [ ] `textEffectsFields()` + override `text_effect_target` allineato al primo `value` del dropdown
- [ ] `text_align` con 4 opzioni (incluso `justify`) **se ha testo**
- [ ] `tile_padding` + `tile_margin` con `type: 'spacing'`
- [ ] `border` + `border_hover` + `borderEffectDefaults` via `borderFields()`
- [ ] `border_radius` con `type: 'border-radius'` (mai range singolo)
- [ ] `shadowField` con variant appropriata
- [ ] `withHover()` per ogni proprietà con hover (mai field separati `hover_X` nel config)
- [ ] `typography_preset` con `optionsSource: 'globalTypography'`
- [ ] Sfondo creativo `bg` di type `'background'` **se ha area visiva propria**
- [ ] Separator nuove sezioni con `type: 'separator'` (mai `'heading'`)
- [ ] Icone via `type: 'icon'` (FieldIcon), mai text

### Defaults & coerenza
- [ ] Tutti i defaults JS replicati in PHP `$defaults` con stessi valori
- [ ] Nessuna chiave usata nel render PHP senza default
- [ ] Type cast (`absint`, `floatval`, `sanitize_text_field`) su tutti i valori sospetti

### Render PHP
- [ ] UID unico per scope CSS
- [ ] `wp_kses_post` per HTML user-generated, `esc_html` per plain
- [ ] Regex HTML inline corretta: `/<[a-z!\/][^>]*>/i`
- [ ] `safe_color_css` per ogni colore inline
- [ ] `tfx_attrs` + `tfx_css` + `tfx_print_script` se ha effetti testo
- [ ] `build_border_css` + `build_border_hover_css` + `build_border_effect_css`
- [ ] `build_border_radius_css` per border-radius
- [ ] `Olo_Tile_Utils::shadow($key, $variant)` con variant giusta
- [ ] `Olo_CSS_Builder::get_bg_inline_css($bg)` se ha sfondo creativo
- [ ] Mai `&&` in script inline (entity bug)
- [ ] Nessun output diretto di `$_GET`/`$_POST` (mai usato per output, solo per logica)

### Anteprima Vue
- [ ] `<NomeTile>.vue` esiste in `src/components/Tiles/`
- [ ] Anteprima visivamente fedele al render PHP (zero placeholder testuali)
- [ ] Reattiva ai cambiamenti settings (computed properties)

### Mapping preset
- [ ] In `BuilderInspector.vue` la mappa `TILE_PRESETS.<type>` ha tutti e 12 i preset con valori distinti
- [ ] Almeno 3-4 setting cambiano tra preset adiacenti (no preset "fotocopia")

### Test
- [ ] Aggiunto da sidebar → render OK
- [ ] Cambio preset → effetto visivo evidente
- [ ] Attivazione effetto testo → animazione visibile
- [ ] Hover su elemento con `withHover` → transizione fluida
- [ ] Cambio bg da solid a gradient/pattern/image → render coerente
- [ ] Border 4 lati diversi → tutti applicati
- [ ] Border-radius 4 angoli diversi → tutti applicati
- [ ] Salvataggio → ricaricamento → settings preservati
- [ ] Frontend pubblico (no admin) → rendering identico al builder
- [ ] Responsive (mobile/tablet) → layout adattivo
- [ ] i18n: cambiando WP locale, le label cambiano (no IT hardcoded)

---

## 19. Anti-pattern noti (NON fare)

1. **Range separati per spacing/border**: rotto UX, usare `type: 'spacing'` / `type: 'border-radius'`
2. **`text_effect_target: 'heading'`** quando il dropdown offre `'text'`/`'caption'`/`'content'` → effetti non si applicano
3. **Preset vuoti `{}`** → cambia il select ma niente succede
4. **`type: 'heading'`** per i separatori → non esiste nel sistema
5. **`type: 'text'`** per icone → utente non scrive `dashicons-format-image` a mano
6. **`hover_X` field separati nel config** → spezza il pattern bilaterale `withHover`
7. **`&&` in script inline PHP** → WordPress encoda → script rotto
8. **Regex `/^\s*</`** per detect HTML → solo inizio, perde tag inline al centro
9. **`shadow($key, 'standard')`** su button con bg pieno → ombra invisibile
10. **Hardcoded `#blue`/`#3b82f6`** primary → usare `var(--olo-color-primary)`
11. **Mocking solid color quando type=image|video|gallery senza asset** → bg sparisce, fix in `Olo_CSS_Builder::get_effective_bg` (fallback automatico)
12. **Duplicazione PHP→Vue render**: la Vue è anteprima approssimata, il PHP è autoritativo per il frontend. Non duplicare logica complessa.

---

## 20. Note di estensione

### 20.1 Aggiungere un setting a un tile esistente

1. Aggiungi al `defaults` (JS + PHP)
2. Aggiungi al `fields` (JS) nella sezione coerente
3. Aggiungi al render PHP con sanitize appropriato
4. Aggiungi all'anteprima Vue (se rilevante per il canvas)
5. Aggiungi al `TILE_PRESETS` di tutti i 12 preset (almeno valori sensati)
6. Bump versione plugin (sorgente + define)
7. Vite build + deploy try + mosaic (regola permanente)

### 20.2 Aggiungere un nuovo tile

Pattern 2-file (auto-discovery via `import.meta.glob`):
- `src/config/elements/<type>.js` → registry auto
- `includes/tiles/class-<type>-tile.php` → registry auto

Aggiungere il Vue tile component come terzo file se serve anteprima ricca. Aggiornare `TILE_PRESETS` in BuilderInspector + memorizzare slug in `Olo_Sandbox_Config::allowed_tiles()` se il tile dovrà essere disponibile nella sandbox demo.

---

## 21. Riferimenti incrociati

- `src/config/elements/_shared.js` — mixin condivisi (textEffects, border, withHover, shadowField)
- `src/config/elements/_styleFieldsBase.js` — campi "tab Stile" data-driven (sezione/row/iconbox/hero)
- `src/components/Builder/BuilderInspector.vue` — `TILE_PRESETS` mappa preset→values
- `src/components/Builder/BackgroundControls.vue` — UI sfondo unificato
- `src/utils/patternCSS.js` — 33 pattern CSS-only (mirror del PHP `class-css-builder.php::build_pattern_css`)
- `includes/class-css-builder.php` — `get_effective_bg`, `get_bg_inline_css`, `build_gradient_css`, `build_pattern_css`, `build_pattern_svg`
- `includes/class-text-effects.php` — `Olo_Text_Effects::classes/data_attrs/css/print_script`
- `includes/class-tile-utils.php` — `shadow($key, $variant)` con 4 mappe
- `includes/tiles/class-tile-base.php` — base class con helper `tfx_attrs`, `build_border_css`, `build_border_radius_css`, `build_hover_css`, `safe_color_css`, `parse_border`

---

## 22. Storico bug risolti (lessons learned)

| Bug | Tile | Causa | Fix |
|---|---|---|---|
| Pattern verde su rgba | section/row (`build_pattern_css`) | `hexdec` su stringa `rgba(...)` → 0/186/2 | Parse rgba() prima di hexdec |
| Countdown sandbox enorme | banner JS | wp_localize stringifica scalar → `+` concatenava string | `Number(data.ttl)` cast esplicito |
| Effetto testo iconlist | iconlist | `text_effect_target` default 'heading' ≠ dropdown 'text' | Override `text_effect_target: 'text'` |
| `<strong>` mostrato letterale | text-block | Regex `/^\s*</` solo inizio | Regex `/<[a-z!\/][^>]*>/i` ovunque |
| Doppio render front page | seed sandbox | post_content shortcode + meta `_olo_template_id` | Vuoto post_content, lasciato solo meta |
| Cloni visibili a tutti | sandbox dashboard | `GET /templates` ritorna tutti | Filter `scope_templates_list` |
| Ombra button invisibile | button | Variant 'standard' alpha 8% | Nuova variant 'button' alpha 18-35% |
| Builder Vue colonne troppe | text-block | Niente columns esposto | Field `columns` + `column_gap` |
| Allineamento testo mancante | text-block, list, iconlist, desclist, alert | Nessun `text_align` esposto | Nuovo field `text_align` 4 opzioni + render PHP |
| `justify` non disponibile | headline, iconbox, hero, panel | Dropdown alignment con 3 opzioni | Aggiunto `'justify'` come 4ª opzione |

---

*Documento vivente. Aggiornare quando si scopre un nuovo pattern o anti-pattern. Versione: 1.0 — basato su Olobuild 3.49.2.*
