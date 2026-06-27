# REPORT — Inspector, "Impostazioni avanzate" e collocazione "Effetti mouse"

## 1. Organizzazione dell'inspector (`D:\TECNICA\olobuild\src\components\Builder\BuilderInspector.vue`)

### Tab (3, fissi, hard-coded)
```js
// BuilderInspector.vue:1610
const tabs = ['Contenuto', 'Stile', 'Avanzate'];
```
Pill segmented (riga 144-161), tab attivo persistito in localStorage (`ACTIVE_TAB_KEY`, riga 1725). Ogni tab ha una "rail" laterale di sezioni (scroll-spy).

### Tab "Contenuto" — data-driven dai config elementi
- Renderizza `elementDef.fields` (i config in `src/config/elements/*.js`), scrive su `tile.settings.*` (o `tile.style.*` se il field ha `scope: 'style'`, riga 227/231).
- **Il raggruppamento avviene ESCLUSIVAMENTE tramite separator**: il computed `groupedSections` (righe 2305-2322) spezza l'array `fields` su ogni `{ type: 'separator', label: '...' }` e crea una `CollapseSection` per ogni gruppo:
```js
// BuilderInspector.vue:2305
const groupedSections = computed(() => {
  const sections = [];
  let current = { label: null, fields: [] };
  for (const field of elementFields.value) {
    if (field.type === 'separator') { ... current = { label: field.label, fields: [] }; }
    else { current.fields.push(field); }
  }
  ...
});
```
- I field PRIMA del primo separator stanno fuori da ogni sezione (sempre aperti). Le sezioni con label sono collassabili e **`:defaultOpen="sIdx <= 1"`** (riga 247): solo le prime 2 sezioni partono aperte → le sezioni in coda al config partono chiuse (è la convenzione de-facto per i gruppi "avanzati" di una tile).
- **NON esiste alcun campo `tab:`, `group:` o `advanced:true` nei config**: un field di config NON può essere indirizzato al tab Avanzate. L'unica traccia è `section: 'Avanzato'` su `customCssField` (`_shared.js:374`) ma è **INERTE** — nessun componente legge `field.section` (grep: unica occorrenza è la definizione stessa).

### Tab "Stile"
- `StyleFieldsRenderer` (riga 315) con `elementDef.styleFields || []` + la base dichiarativa `styleFieldsBase(tileType)` di `src/config/elements/_styleFieldsBase.js` (pseudo-field `layout-stack`, `box-stack`, `bg`, `borderEffectFields()`, `effects-stack`, `blend_mode`, `transition.*`). Scrive su `tile.style.*`.

### Tab "Avanzate" — template HARD-CODED (non data-driven)
Scrive su **`tile.advanced.*`** tramite:
```js
// BuilderInspector.vue:5839
function updateAdvanced(key, value) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileAdvanced(builderStore.selectedTileId, { [key]: value });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}
```
Macro-sezioni (rail fissa, righe 1714-1721):
```js
const _advRailFixed = [
  { id: 'v2i-sec-adv-id',         label: 'Identificatori' },
  { id: 'v2i-sec-adv-visibility', label: 'Visibilità' },
  { id: 'v2i-sec-adv-seo',        label: 'SEO' },
  { id: 'v2i-sec-adv-effects',    label: 'Effetti' },
  { id: 'v2i-sec-adv-position',   label: 'Posizionamento' },
  { id: 'v2i-sec-adv-dev',        label: 'Sviluppatore' },
];
```
Dentro `<CollapseSection id="v2i-sec-adv-effects" title="Effetti & Animazioni" :macro="true">` (riga 683) ci sono le sotto-sezioni annidate: **Animazione di ingresso** (entrance_* — NB: salva su `settings` via `updateSetting`!), **Animazione allo scroll** (scrollspy_*), **Parallax allo scroll** (`advanced.parallax` + ParallaxEditor), **Percorso Bezier allo scroll** (`advanced.bezier_path`), **Scroll fisso (sticky)**, **→ "Effetti mouse" (riga 952)**, **Animazione continua** (infinite_*), **Maschera forma** (mask_*).

## 2. ⭐ SCOPERTA CHIAVE: la sezione "Effetti mouse" ESISTE GIÀ nel tab Avanzate

`<CollapseSection title="Effetti mouse">` — **BuilderInspector.vue righe 952-1071**, dentro la macro `v2i-sec-adv-effects`. Contiene già, tutte su `tile.advanced.*`:

| Chiave | Controllo | Range/Opzioni | Default |
|---|---|---|---|
| `mouse_tilt` | checkbox | — | false |
| `mouse_tilt_intensity` | range | 5-30 step 1 | 15 |
| `mouse_tilt_target` | `FieldSelect ui="segmented"` | `TILT_TARGET_OPTIONS` (riga 1463): `block` "Blocco intero" / `items` "Foto interne" | block |
| `mouse_track` | checkbox | — | false |
| `mouse_track_speed` | range | 1-10 | 3 |
| `cursor_spotlight` | checkbox (torcia) | — | false |
| `cursor_spotlight_blend` | dropdown `SPOTLIGHT_BLEND_OPTIONS` (1455) | difference/exclusion/screen/overlay/hard-light | difference |
| `cursor_spotlight_color` / `_size` (80-600) / `_softness` (0-100) / `_easing` (5-100) | color/range | | #ffffff/300/40/22 |
| **Cursore magnetico** | checkbox + sotto-controlli | **GLOBALE sito**, NON per-tile: option `olo_magnetic_cursor` via REST `olo/v1/magnetic-cursor` (class-rest-api.php:212), auto-save (`magCursor` ref riga 1522, `updateMag`) | — |

Quindi la richiesta dell'utente ("effetti puntatore sempre in 'Effetti mouse' dentro le avanzate") è **già il pattern del codebase**: tilt 3D, segui-cursore (magnetico per-elemento), spotlight e cursore magnetico globale vivono lì. I NUOVI effetti del tema Clod (onda lettere sotto cursore, HUD/monitor che segue il puntatore) vanno **aggiunti a questa stessa CollapseSection** con chiavi `advanced.*` nuove.

## 3. Helper condivisi

### `src/config/elements/_shared.js` (1029 righe) — firme esatte
- `linkFields` (array), `targetField`, `alignmentField`, `columnWidthOptions`, `overflowField`, `widgetTemplateField` — costanti
- `shadowField` / `shadowDefaults`; `filterFields` / `filterDefaults`; `textShadowFields`; `backdropFilterFields`; `transformFields`; `maskFields`; `infiniteAnimationFields`; `stickyFields`; `popupTriggerFields`; `formStepFields`; `flexContainerFields`; `cssGridFields` — array + relativi `*Defaults`
- `wowEffectsFields = () => [...]` + `wowEffectsDefaults` (riga 114) — separator "Effetti avanzati (preset wow)"
- `entranceAnimationField`, `entranceStaggerFields`, `entranceAnimationDefault`
- `conditionFields`/`conditionDefaults` + `conditionFieldsEnhanced`/`conditionDefaultsEnhanced`
- **`mouseFields` (riga 275) + `mouseDefaults` (riga 285)** — GIÀ ESISTENTE ma **mai importato da nessun config** (grep: solo la definizione). Contiene separator `'Effetti mouse'` + `mouse_tilt`, `mouse_tilt_intensity`, `mouse_track`, `mouse_track_speed` (manca `mouse_tilt_target` rispetto al pannello Avanzate)
- `scrollEffectFields`/`scrollEffectDefaults` + `scrollEffectFieldsEnhanced`/`DefaultsEnhanced`
- `textEffectsFields = (targetOptions = [...]) => [...]` (riga 389) + `textEffectsDefaults` — separator "Effetti testo"
- `customCssField` / `customCssDefault`
- `makeResponsive(field)` → array di 6 field `key`, `key_widescreen`, `key_tablet_landscape`, `key_tablet`, `key_mobile_landscape`, `key_mobile`
- `withHover(field, opts = {})` (riga 936) → `{ ...field, hoverable: true, hoverKey: opts.hoverKey || \`${field.key}_hover\`, hoverDurationKey, hoverDefaultDuration }`
- `borderDefault`, `borderHoverDefault`, `borderEffectDefaults`; `borderEffectFields()` (riga 976); `borderFields(opts = {})` con `{ key='border', hoverKey='border_hover', durationKey='border_hover_duration' }` (riga 1004)

### `_styleFieldsBase.js`: `styleFieldsBase(tileType)` (riga 29) — field del tab Stile del wrapper, scrivono su `tile.style.*`. Niente di legato al mouse qui (gli hover sono `withHover`/`style.hover.*`, cosa diversa dagli effetti puntatore).

## 4. Runtime frontend esistente per effetti mouse

- **Emissione attributi (PHP)**: `includes/class-frontend-renderer.php:2960-2977` — legge **da `settings` OPPURE `advanced`** (commento esplicito: "il pannello Effetti mouse del builder salva in advanced, lo styleField _shared in settings") ed emette `data-olo-tilt="15"` / `data-olo-tilt-items="15"` / `data-olo-track="3"` + spotlight. Versione riusabile: `Olo_Animation_Builder::build_mouse_attrs($advanced)` e `build_spotlight_attr($advanced)` in `includes/class-animation-builder.php:240-283` (spotlight → `data-olo-spotlight='{json}'` con clamp/whitelist).
- **Runtime JS**: NON è un file enqueued — sono **`<script>` INLINE emessi dal renderer** in coda al template: `class-frontend-renderer.php:4527-4589` (tilt+track: un solo listener `mousemove` document-level, guard `matchMedia('(min-width:960px)')`, propagazione `data-olo-tilt-items` → img/video figli) e 4590+ (spotlight, con falloff radiale e rAF). Pattern alternativo per runtime pesanti: file dedicati `assets/js/olo-*.js` con `wp_enqueue_script` condizionale per tipo tile (es. righe 3992-4106 dello stesso file: leaflet, pdfviewer, ecc.).
- **Cursore magnetico globale**: `includes/class-magnetic-cursor.php` — option `olo_magnetic_cursor`, runtime inline in `wp_footer` (priorità 60), dormiente finché `enabled` è false, soppresso nell'iframe builder (`OLO_BUILDER_PREVIEW`), reduced-motion/touch-safe.
- **Anteprima nel canvas Vue**: `src/components/Grid/GridCell.vue:156-206` replica tilt/track nel builder leggendo `adv.mouse_* ?? settings.mouse_*` (WYSIWYG già garantito).
- Altri sistemi cursor-correlati ma distinti: `text_effect: 'wave'` (time-based, non cursor-based), parallax/bezier (scroll-based).

## 5. Proposta concreta

**Raccomandazione primaria**: per gli effetti del tema Clod NON serve un nuovo helper config — vanno aggiunti come nuove chiavi `advanced.*` nella CollapseSection "Effetti mouse" esistente (BuilderInspector.vue:952), perché lì valgono per OGNI tile automaticamente, e il salvataggio è già cablato (`updateAdvanced`). Esempio per "onda lettere" e "HUD segui-puntatore": chiavi `mouse_wave`, `mouse_wave_radius`, `mouse_hud`, `mouse_hud_content` + estensione di `build_mouse_attrs()` in `class-animation-builder.php` + blocco runtime nello script inline `class-frontend-renderer.php:~4527` (stesso pattern guard desktop + reduced-motion) + replica anteprima in `GridCell.vue`.

**Se si vuole comunque l'helper config-level** (per esporre i controlli anche nel tab Contenuto di tile specifiche): l'esistente `mouseFields` (array, incompleto) va promosso a funzione, in `src/config/elements/_shared.js` SOSTITUENDO le righe 274-290 (mantenendo l'export legacy):

```js
// ─── Mouse effects fields preset (sezione "Effetti mouse") ───
// Pannello canonico per-tile: tab Avanzate (BuilderInspector.vue:952, chiavi su
// tile.advanced). Questo helper espone le STESSE chiavi nei config delle tile che
// vogliono i controlli anche nel tab Contenuto: il renderer PHP legge già entrambe
// le fonti (settings ?? advanced — class-frontend-renderer.php:2960).
// Convenzione: spreddare in FONDO all'array fields (le sezioni oltre la 2ª
// partono chiuse → comportamento "avanzato").
export const mouseEffectsFields = (opts = {}) => [
  { type: 'separator', label: t('Effetti mouse') },
  { key: 'mouse_tilt', label: t('Tilt 3D al mouse'), type: 'toggle' },
  { key: 'mouse_tilt_intensity', label: t('Intensità tilt'), type: 'range', min: 5, max: 30, step: 1,
    condition: { field: 'mouse_tilt', op: 'eq', value: true } },
  { key: 'mouse_tilt_target', label: t('Applica a'), type: 'select', options: [
    { value: 'block', label: t('Blocco intero') },
    { value: 'items', label: t('Foto interne') },   // 2 opzioni corte → segmented automatico (FieldSelect)
  ], condition: { field: 'mouse_tilt', op: 'eq', value: true } },
  { key: 'mouse_track', label: t('Segui cursore (magnetico)'), type: 'toggle' },
  { key: 'mouse_track_speed', label: t('Velocità tracking'), type: 'range', min: 1, max: 10, step: 1,
    condition: { field: 'mouse_track', op: 'eq', value: true } },
  ...(opts.wave ? [
    { key: 'mouse_wave', label: t('Onda lettere sotto il cursore'), type: 'toggle' },
    { key: 'mouse_wave_radius', label: t('Raggio onda (px)'), type: 'range', min: 40, max: 300, step: 10,
      condition: { field: 'mouse_wave', op: 'eq', value: true } },
    { key: 'mouse_wave_lift', label: t('Sollevamento (px)'), type: 'range', min: 2, max: 30, step: 1,
      condition: { field: 'mouse_wave', op: 'eq', value: true } },
  ] : []),
  ...(opts.hud ? [
    { key: 'mouse_hud', label: t('Monitor HUD segui-puntatore'), type: 'toggle' },
    { key: 'mouse_hud_label', label: t('Etichetta HUD'), type: 'text',
      condition: { field: 'mouse_hud', op: 'eq', value: true } },
  ] : []),
];

export const mouseEffectsDefaults = (opts = {}) => ({
  mouse_tilt: false, mouse_tilt_intensity: '15', mouse_tilt_target: 'block',
  mouse_track: false, mouse_track_speed: '3',
  ...(opts.wave ? { mouse_wave: false, mouse_wave_radius: '120', mouse_wave_lift: '10' } : {}),
  ...(opts.hud  ? { mouse_hud: false, mouse_hud_label: '' } : {}),
});
```
Uso nel config tile: `...mouseEffectsFields({ wave: true })` in fondo a `fields`, `...mouseEffectsDefaults({ wave: true })` nei `defaults`.

**Vincoli/convenzioni da rispettare** (standard del repo): condition syntax `{ field, op: 'eq'|'neq'|'in', value }`; select 2-4 opzioni corte = segmented automatico (opt-out `ui:'dropdown'`); chiavi salvate `mouse_*` INVARIATE (il renderer PHP le legge già); niente `tab:`/`advanced:` nei config (non esistono); nuovi effetti = anche runtime inline PHP + anteprima GridCell.vue + build (`node node_modules/vite/bin/vite.js build`) + bump `OLO_VERSION`.

**File chiave**: `D:\TECNICA\olobuild\src\components\Builder\BuilderInspector.vue` (tab:1610, advanced rail:1714, Effetti mouse:952-1071, updateAdvanced:5839, groupedSections:2305) · `D:\TECNICA\olobuild\src\config\elements\_shared.js` (mouseFields:275, withHover:936, borderFields:1004) · `D:\TECNICA\olobuild\src\config\elements\_styleFieldsBase.js` · `D:\TECNICA\olobuild\includes\class-animation-builder.php` (build_mouse_attrs:240, build_spotlight_attr:263) · `D:\TECNICA\olobuild\includes\class-frontend-renderer.php` (attrs:2960-2977, runtime inline:4527-4589+) · `D:\TECNICA\olobuild\includes\class-magnetic-cursor.php` · `D:\TECNICA\olobuild\src\components\Grid\GridCell.vue` (anteprima canvas:156-206).