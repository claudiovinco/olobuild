Report completo. Tutti i pattern verificati sul codice reale.

---

# TASK 4 — Pattern "nuova tile" OLObuild + runtime animazioni

## 1. TILE-PERFETTA.md — sintesi sezioni chiave

File: `D:\TECNICA\olobuild\docs\TILE-PERFETTA.md`

- **Anatomia (§0)**: una tile vive in 5 file — `src/config/elements/<type>.js` (schema inspector), `src/components/Tiles/<Name>Tile.vue` (anteprima WYSIWYG), `includes/tiles/class-<type>-tile.php` (render autoritativo), `BuilderInspector.vue` → `TILE_PRESETS` (preset apply-once), opzionale `assets/css/frontend.css`.
- **Regola n.1 di stabilità (§2)**: ogni chiave usata nel render PHP DEVE avere default identico in JS e in PHP `$defaults` (`wp_parse_args`). Gotcha noto: `text_effect_target` default `'heading'` va overridato se il dropdown espone altri target.
- **Preset (§4)**: 5 sicuri + 7 audaci + custom; apply-once via `applyTilePresetTheme` (scrivono settings, NO override CSS runtime); ogni preset deve toccare 5 dimensioni (tipografia, layout, colori, classe firma CSS `olo-{tile}-preset-{id}` in frontend.css, entrance_animation); mai `{}` vuoti.
- **Sicurezza render PHP (§12)**: UID unico per scope CSS (`wp_rand`/`wp_unique_id`); `wp_kses_post` per HTML utente; regex detect HTML `/<[a-z!\/][^>]*>/i` (mai `/^\s*</`); `safe_color_css()` su ogni colore inline; **mai `&&` negli script inline** (WP lo encoda in `&#038;` → script rotto: usare if annidati).
- **WYSIWYG (§13)**: la Vue è anteprima fedele nel canvas, il PHP è il render autoritativo (frontend + iframe). Zero placeholder.
- **Standard field**: `spacing` (4 caselle link/unlink), `border` (`borderFields()`), `border-radius` (4 angoli), `withHover()` bilaterale, `icon` (FieldIcon), separatori `{ type:'separator', label }` (mai `'heading'`), `font-family` (FieldFontFamily).
- **Anti-pattern (§19)**: range separati per spacing/border, preset vuoti, hover_X separati, hex hardcoded per il primario, duplicazione logica complessa PHP→Vue.

## 2. Tile di riferimento: chathero (3 file)

### 2a. PHP — `D:\TECNICA\olobuild\includes\tiles\class-chathero-tile.php`

`class Olo_ChatHero_Tile extends Olo_Tile_Base`. Il metodo `render($settings, $style)` ritorna una stringa bufferizzata composta da: **`<style>` scoped + markup + `<style>` finale per il kit bordi**. Tutto inline, niente enqueue.

```php
$s   = wp_parse_args( $settings, $this->defaults );
$uid = 'ocht-' . wp_rand( 10000, 99999 );          // UID per scope CSS, niente collisioni tra istanze
...
ob_start(); ?>
<style>
  .<?php echo $uid; ?>{position:relative;...background:<?php echo $bg; ?>;...}
  .<?php echo $uid; ?> .cht-glow{...filter:blur(<?php echo $gblur; ?>px);...}
</style>
<section class="olo-chathero <?php echo esc_attr( $uid ); ?>"> ... </section>
<?php ... return ob_get_clean();
```

- **CSS emesso una volta per ISTANZA** (non per pagina), scoped via `$uid` — è il pattern delle tile-hero "blueprint": ogni istanza porta il suo `<style>`.
- **Settings → runtime**: ogni valore passa per un sanitizer prima dell'interpolazione: `$this->safe_color_css()` per i colori, `intval()/floatval()` + clamp `max()/min()` per i numeri, `esc_html`/`esc_url`/`esc_attr` nel markup. Con il phpcs disable commentato che documenta perché l'inline CSS è sicuro (riga 162).
- **Token colore — pattern fallback-a-token**:
```php
$accent = $this->safe_color_css( $s['accent'] ) ?: 'var(--olo-color-primary, #a06bff)';
$acc2   = $this->safe_color_css( $s['accent2'] ?? '' ) ?: 'var(--olo-color-secondary, #ff7ad1)';
```
  Campo vuoto = segue la palette del tema. Nel config il field lo dichiara: `description: t('Vuoto = primario del tema.')`.
- **Font — chathero usa stack fissi con var ruolo** (righe 144-146):
```php
$disp = "var(--olo-font-family-heading, 'Instrument Sans',-apple-system,sans-serif)";
$sans = "var(--olo-font-family, 'Instrument Sans',-apple-system,sans-serif)";
$mono = "var(--olo-font-family-mono, 'Space Mono',ui-monospace,Menlo,monospace)";
```
  Per Clod: `--olo-font-family-heading` → Big Shoulders Display, `--olo-font-family` → Hanken Grotesk, `--olo-font-family-mono` → Space Mono (il mono è già il ruolo giusto). Se invece si espone un field famiglia all'utente, lo standard è `type:'font-family'` + `$this->resolve_font_family($v, $legacy_map) ?: $fallback` (`class-tile-base.php:148` — risolve ruoli body/sans/heading/serif/mono in `var(--olo-font-family-*)`, mappa legacy per-tile, valida CSS pronto).
- **Kit standard a coda render** (righe 224-233): `build_border_css` + `build_border_hover_css(".{$uid}", ...)` + `build_border_effect_css` emessi in un secondo `<style>`; sfondo completo via `(new Olo_CSS_Builder())->get_bg_inline_css($bg_obj)` solo se `bg.type !== 'none'`; ombra via `build_shadow_decl()` privato (preset sm/md/lg/xl + custom).
- **withHover/build_hover_css**: chathero NON lo usa (hover fissi nel CSS scoped: `.cht-btn:hover{transform:translateY(-2px)}`). Il pattern standard quando serve hover configurabile è quello di button — config: `withHover({ key:'bg_color', ... }, { hoverKey:'hover_bg_color' })` (helper in `src/config/elements/_shared.js:936`); PHP `class-button-tile.php:129`:
```php
$hover = $this->build_hover_css( $s, [
    'bg_color'   => [ 'css' => 'background-color', 'hover_key' => 'hover_bg_color',   'important' => true ],
    'text_color' => [ 'css' => 'color',            'hover_key' => 'hover_text_color', 'important' => true ],
    'border_radius' => 'border-radius',
] );
// → ['hover_decls' => "...", 'transitions' => [...]] da comporre in ".{$uid}:hover{...}"
```

### 2b. Vue — `D:\TECNICA\olobuild\src\components\Tiles\ChatHeroTile.vue`

Replica **byte-identica** del render PHP, tutta a computed style inline:

```js
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { /* COPIA INTEGRALE dei defaults del config — terza copia, deve coincidere */ };
const s = computed(() => ({ ...defaults, ...props.settings }));    // reattivo: l'inspector aggiorna live
const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #a06bff)');  // stesso fallback-a-token del PHP
const rootStyle = computed(() => ({ ..., '--cht-accent': accent.value }));  // CSS var per i pseudo-elementi
```

- Gli **pseudo-elementi/hover** che non si possono fare con style inline vanno in `<style scoped>` e leggono la CSS var passata dal computed (`--cht-accent` per il dot della pill e il focus ring).
- Le funzioni helper del PHP (shadowDecl, borderDecl, chatRadius) sono **duplicate in JS con identica logica** incluse le "stringhe byte-identiche" per i default (`'0 28px'`, `'16px 16px 0 0'`) — parità Vue↔PHP garantita anche sui default.
- Le tile più recenti (es. `StepTimelineTile.vue:68`) usano invece i token centralizzati: `import { resolveColor, resolveFontFamily, TOKENS, SHADOW } from '@/composables/oloTileDefaults'` con `resolveColor(s.value.title_color, TOKENS.text)` e `resolveFontFamily(v, FONT_LEGACY) || SERIF`. Helper in `src/composables/oloTileDefaults.js` (`resolveColor` riga 110 = `userValue || token`; `resolveFontFamily` riga 138, gemello JS di `Olo_Tile_Base::resolve_font_family`). **Per le tile nuove Clod usare questo pattern**, non la duplicazione di stack chathero.

### 2c. Registrazione PHP — le 2 righe esatte in `D:\TECNICA\olobuild\includes\class-olo-builder.php`

```php
// riga 3026
require_once OLO_PATH . 'includes/tiles/class-chathero-tile.php';
// riga 3255
$manager->register_tile( new Olo_ChatHero_Tile() );
```
NON è auto-discovery lato PHP (il playbook vault sbaglia su questo) — 2 righe manuali obbligatorie.

## 2-bis. Runtime JS frontend — chathero NON ne ha; il pattern di riferimento è smearhero

Chathero e glowgallery sono dichiaratamente "Nessun JS (pure CSS)". Per le tile Clod che richiedono animazioni JS (infografica SVG animata, REC overlay con blink/timer), il pattern reale è in `D:\TECNICA\olobuild\includes\tiles\class-smearhero-tile.php` (righe 149-181):

```php
<?php if ( $smear ) : ?>
<script>
(function(){
    var root=document.currentScript.previousElementSibling;   // ← targeting SENZA id: lo script segue la <section>
    if(!root){return;}
    var zone=root.querySelector('[data-sh-zone]');
    if(!zone){return;}
    var cols=<?php echo $cols_json; /* wp_json_encode() di valori già safe_color_css() */ ?>;
    var fine=window.matchMedia('(pointer:fine)').matches;
    var motion=!window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if(!fine){return;}      // ← if SEPARATI, MAI `a&&b`: WP encoda && in entità e rompe lo script
    if(!motion){return;}
    zone.addEventListener('pointermove', function(e){ /* crea .sh-blob, anima, remove dopo 900ms */ });
})();
</script>
<?php endif; ?>
```

Caratteristiche del pattern:
- **Script per-istanza** subito dopo la `<section>`; aggancio via `document.currentScript.previousElementSibling` → niente id da generare, ogni istanza è indipendente (l'`$uid` resta solo per il CSS).
- **Settings → JS** via `wp_json_encode()` di valori già sanitizzati, interpolati nello script.
- **Guard accessibilità**: `pointer:fine` + `prefers-reduced-motion` con `return` separati.
- **Asset condivisi una volta per pagina**: pattern `static $done` di `class-timeline-tile.php:63-72`:
```php
private function print_shared_css() {
    static $done = false;
    if ( $done ) { return; }
    $done = true;
    echo '<style id="olo-tlsuper-css">' . file_get_contents( OLO_PATH . 'assets/css/timeline-super.css' ) . '</style>';
}
```
  e per gli script condivisi `Olo_Text_Effects::print_script()` (`includes/class-text-effects.php:180`): doppia guardia `self::$script_emitted` (static PHP, per richiesta) + `window.__oloTextFxInit` (per il caso di markup iniettato due volte nel DOM).
- **Replica Vue dell'animazione**: `SmearHeroTile.vue:183-216` reimplementa la stessa identica logica in `onMounted` (stessi guard matchMedia, stessa classe `.sh-blob`, stessi timing) + cleanup `onBeforeUnmount(removeEventListener)`. Il canvas builder così mostra l'animazione vera, non un fermo-immagine.

## 3. glowgallery PHP — generazione SVG/canvas animato

`D:\TECNICA\olobuild\includes\tiles\class-glowgallery-tile.php`: **NON c'è generazione SVG/canvas** — il glow è pure-CSS (`radial-gradient` + `filter:blur` su uno `<span class="evh-glow">`), il placeholder media è `repeating-linear-gradient`. Gli unici SVG nelle tile hero sono micro-icone inline statiche (freccia CTA in chathero riga 201). Per pattern SVG generati in PHP l'unico precedente è `Olo_CSS_Builder::build_pattern_svg` (`includes/class-css-builder.php`). **Conclusione operativa per l'hero Clod con infografica SVG animata**: emettere l'SVG inline nel markup della tile (con `esc_attr` su ogni valore interpolato), animarlo con keyframes CSS scoped a `$uid` (dashoffset, transform) e/o script per-istanza stile smearhero; niente da riusare da glowgallery oltre allo scheletro generale (che è identico a chathero).

## 4. Auto-discovery: config SÌ, Vue NO, PHP NO

- **Config JS — auto-discovery confermato**: `D:\TECNICA\olobuild\src\config\elementRegistry.js:6`:
```js
const modules = import.meta.glob('./elements/*.js', { eager: true });
// salta _shared.js, indicizza per def.type
```
  Basta creare `src/config/elements/<type>.js` con `export default { type, name, icon, category, defaults, fields, styleFields }`.
- **Componenti Vue — registrazione MANUALE** in `D:\TECNICA\olobuild\src\components\Tiles\TileBase.vue`: import esplicito + voce nella mappa `tileComponents` (riga 295), chiave = il `type` del config, fallback `ExternalTilePlaceholder` se assente:
```js
import ChatHeroTile from './ChatHeroTile.vue';      // riga 41
const tileComponents = { ..., chathero: ChatHeroTile, ... };   // riga 328
const tileComponent = computed(() => tileComponents[props.tile.type] || ExternalTilePlaceholder);
```
  Convenzione naming: file `PascalCase + Tile.vue` (es. type `chathero` → `ChatHeroTile.vue`); la mappatura type→componente è esplicita nella mappa, non derivata dal nome.
- **PHP — registrazione MANUALE**: le 2 righe in `class-olo-builder.php` (punto 2c).

## 5. Checklist operativa "nuova tile" (per le tile Clod)

**File da creare (3) + da toccare (2-3):**

| # | Azione | File |
|---|---|---|
| 0 | **Anti-collisione nome classe**: `grep "class Olo_X_Tile"` su TUTTO `wp-content/plugins/` dei server, non solo il repo (gotcha `Olo_EventHero_Tile`/olo-booking → fatal) | server |
| 1 | Config inspector (auto-discovery) | `src/config/elements/<type>.js` |
| 2 | Componente anteprima WYSIWYG | `src/components/Tiles/<Name>Tile.vue` |
| 3 | Registrare il componente: import + voce mappa `tileComponents` | `src/components/Tiles/TileBase.vue` |
| 4 | Classe render PHP | `includes/tiles/class-<type>-tile.php` |
| 5 | 2 righe: `require_once` (~riga 3026) + `register_tile(new Olo_X_Tile())` (~riga 3255) | `includes/class-olo-builder.php` |
| 6 | (Opz.) `TILE_PRESETS.<type>` se la tile avrà preset stilistici | `src/components/Builder/BuilderInspector.vue` |
| 7 | (Opz.) slug in `Olo_Sandbox_Config::allowed_tiles()` se serve in sandbox | `includes/class-sandbox-config.php` |

**Coerenza obbligatoria:** defaults identici in 3 posti (config JS, oggetto `defaults` nel .vue, `$defaults` PHP); render Vue == render PHP (stesse stringhe CSS, anche sui default); chiavi salvate mai rinominate.

**Build + release:**
```
node node_modules/vite/bin/vite.js build        # npx NON funziona su questo sistema
# + bump OLO_VERSION in olobuild.php (obbligatorio dopo modifiche JS/CSS)
# + deploy automatico su tutti e 6 i server (regola permanente)
```
(Il bundle `theme-picker` con `--config vite.picker.config.js` serve SOLO se si tocca `src/theme-picker/` — non è il caso delle tile.)

**Le 10 regole regoletiles1** (`D:\TECNICA\olobuild\regoletiles1\DESIGN_LANGUAGE.md`), sintesi:
1. **Colore solo via token**: ruoli cliente via `resolveColor(userVal, GLOBAL.x)`, neutri/superfici da SYSTEM, `contrastOn()` per il testo su primario — zero hex grezzi nei componenti.
2. **Spaziatura scala 8pt** (`SPACE`): padding per taglia (12 / 16-24 / 32-48), gap 8/12/16, mai 13px/7px.
3. **Raggio scala `RADIUS`** (sm 6 / md 10 / lg 14 / pill): UNA tile = UN raggio coerente ovunque.
4. **Elevazione 4 livelli** (none/sm/md/lg), ombre morbide a bassa opacità; interattivo sm → hover md.
5. **Tipografia da scala globale**: ruoli title/subtitle/body/caption, line-height titoli 1.1-1.2 corpo 1.5-1.6, `text-wrap:balance` sui titoli, mai < 12px.
6. **Icone solo SVG dal set**, stroke uniforme 16/20/24, `currentColor` — mai emoji.
7. **Stati completi**: hover discreto (lift -1px + ombra md), **focus-visible SEMPRE** (`0 0 0 3px color-mix(primary 30%)`), transizioni uniformi.
8. **Media**: aspect-ratio espliciti, `object-fit:cover`, placeholder elegante (superficie neutra + icona, mai bianco vuoto).
9. **Default curati e realistici**: copy reale breve, colori dai token — la tile appena inserita deve sembrare progettata. Niente lorem ipsum.
10. **Accessibilità baseline**: elementi semantici (`<a>`/`<button>`/heading), contrasto AA, `aria-label` sui controlli icona, hit-area ≥ 44px.

Più la meta-regola "famiglia": tile della stessa categoria condividono scala padding/gap, raggio, lingua d'ombra, trattamento titoli, stile icone, stati hover/focus. Strumenti pronti: `src/composables/oloTileDefaults.js` (TOKENS, SPACE, RADIUS, SHADOW, resolveColor, resolveFontFamily, contrastOn) + `useBoxModel.js`.

**Nota specifica tema Clod (dark, lime #C6F24E):** seguire il pattern fallback-a-token di chathero — accento utente vuoto → `var(--olo-color-primary, #C6F24E)` lato tema; font via ruoli `--olo-font-family-heading` (Big Shoulders Display) / `--olo-font-family` (Hanken Grotesk) / `--olo-font-family-mono` (Space Mono); i default hex della tile (ink #0b0c0f, bone #ECEAE3) sono legittimi come default di tile blueprint-specifica (come `#140e22` di chathero), purché ogni colore esposto come field abbia fallback ai token quando vuoto.