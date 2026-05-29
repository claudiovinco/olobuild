# Audit TILE-PERFETTA — 2026-05-11

Verifica sistematica della conformità di tutti i tile OLObuild (core + esterni booking/real-estate/lang/vtour) rispetto al manuale [`TILE-PERFETTA.md`](TILE-PERFETTA.md).

Riferimento: Olobuild 3.49.x. Sample: 152 tile core (`includes/tiles/`) + 84 tile esterni (`olo-booking/includes/modules/*/tiles/`) = **236 tile totali**.

---

## 1. Sintesi numerica

| Indicatore | Core (152) | Booking esterni (84) | Manuale §  |
|---|---:|---:|---|
| Anteprima Vue presente | 152 / 152 (100%) | 80 / 84 (95%) | §13 |
| Usa `build_border_css` (helper PHP) | 135 / 152 (89%) | 28 / 84 (33%) | §7.1 |
| Usa `build_border_radius_css` | 20 / 152 (13%) | 13 / 84 (15%) | §7.2 |
| Usa `build_hover_css` dichiarativo | 2 / 152 (1%) | 0 / 84 (0%) | §8 |
| Usa `safe_color_css` | 119 / 152 (78%) | 32 / 84 (38%) | §12.5 |
| Usa `get_bg_inline_css` (bg creativo) | 1 / 152 (<1%) | 0 / 84 (0%) | §6 |
| UID univoco scope CSS | 137 / 152 (90%) | n/d | §12.2 |
| Espone preset stilistici (12+1) | 55 / 163 (34%) | 0 / 84 (0%) | §4 |
| Espone `typography_preset` | 3 / 163 (2%) | 0 / 84 (0%) | §5.4 |
| Espone bg creativo (`type: 'background'`) | 1 / 163 (1%) | 0 / 84 (0%) | §6 |
| Inspector con almeno 1 control | 163 / 163 (100%) | 12 / 84 (14%) | §3 |

> **Lettura rapida**: i tile core di Olobuild rispettano il pilastro Vue/border/safe-color, ma sono drammaticamente indietro su preset stilistici, typography_preset, bg creativo e `build_hover_css` dichiarativo. I tile esterni booking sono molto più lontani dallo standard — la maggior parte non ha nemmeno l'inspector.

---

## 2. Anti-pattern critici (rotture del manuale)

### 2.1 Preset stilistici **VUOTI `{}`** — anti-pattern §4.1 grave

Tile con preset mappati ma valori vuoti `{}`: la selezione dropdown non produce alcun cambiamento visivo. L'utente percepisce come bug.

**28 tile core** hanno almeno un preset vuoto, di cui la maggior parte ha **tutti i 12 preset vuoti**:

| Tile | Preset vuoti |
|---|---|
| alert | 12 / 12 |
| breadcrumbs | 12 / 12 |
| countdown | 12 / 12 |
| counter | 12 / 12 |
| countercircle | 12 / 12 |
| flipcard | 12 / 12 |
| form | 12 / 12 |
| hotspot | 12 / 12 |
| imgcompare | 12 / 12 |
| lightbox | 12 / 12 |
| list | 12 / 12 |
| loginform | 12 / 12 |
| newsletter | 12 / 12 |
| pagination | 12 / 12 |
| postnavigation | 12 / 12 |
| progress | 12 / 12 |
| progresstracker | 12 / 12 |
| proslider | 12 / 12 |
| slideshow | 12 / 12 |
| starrating | 12 / 12 |
| table | 12 / 12 |
| tagcloud | 12 / 12 |
| toc | 12 / 12 |
| viewer360 | 12 / 12 |
| viewscounter | 12 / 12 |
| sharebuttons | 10 / 12 |
| search | 11 / 12 |
| livesearch | parz. |

Riferimento codice: [BuilderInspector.vue](../src/components/Builder/BuilderInspector.vue) attorno a riga 1630-3940 (`const TILE_PRESETS`).

### 2.2 `text_effect_target` mismatch — bug §2.1

Tile che importano `textEffectsDefaults` (default `text_effect_target: 'heading'`) ma il loro dropdown espone target diversi (es. `'text'`, `'name'`, `'title'`) senza override esplicito. L'utente attiva un effetto e **niente si applica**.

**~30 tile core** con il bug, individuati: `audio`, `authorbox`, `blendtext`, `chart`, `form`, `grid`, `icontabs`, `linkinbio`, `loginform`, `menuanchor`, `nav`, `newsletter`, `newsticker`, `olo_room_contacts`, `overlay`, `overlaygrid`, `overlayslider`, `pagetitlebar`, `panelslider`, `paymentbuttons`, `pdfpro`, `popover`, `popup`, `portfolio`, `postgrid`, `pricelist`, `pricing`, `progresstracker`, `quotation`, `slideshow` …

**Fix**: aggiungere ai `defaults` JS e PHP `text_effect_target: '<primo_value_dropdown>'`.

### 2.3 Hover separati invece di `withHover()` — anti-pattern §8

In alcuni tile esistono coppie `bg` + `hover_bg` (oppure `caption_*` + `hover_caption_*`) dove la versione hover è un field separato. Standard v3.13+ richiede `withHover(...)` bilaterale.

| Tile | Field violanti |
|---|---|
| `queryloop` | `hover_bg` (con `bg_color` base) — anti-pattern certo |
| `overlaygrid` | `hover_image` (con `image` base) — anti-pattern certo |
| `progallery` | `hover_caption`, `hover_caption_bg`, `hover_caption_color`, `hover_caption_weight`, `hover_frame_inset`, `hover_glow_color`, `hover_glow_spread`, `hover_magnetic_strength`, `hover_tilt_angle`, `hover_zoom_scale` (numerosi override) |
| `pagination` | `hover_background` |
| `panelslider` | `hover_lift`, `hover_scale`, `hover_shadow` |
| `pricelist` | `hover_lift` |
| `sitelogo` | `hover_opacity` |
| `switcher` | `hover_bg` |
| `button` | `hover_shadow_*` (h/v/blur/spread/color/inset) — override completo dell'ombra base senza `withHover` |

Tile esterni booking (es. `Olo_Ac_Tile_Base::common_defaults()`) usano `hover_border_radius`, `hover_border_width`, `hover_border_color`, `hover_lift` come field separati — anti-pattern §8.

### 2.4 `border_radius` scalare invece di `{ tl, tr, br, bl }`

- [`audio.js:21`](../src/config/elements/audio.js#L21): `border_radius: '8'` (scalar) ma il field NON è esposto in `fields[]` → setting morto.
- [`olo-booking class-ac-tile-base.php:62`](D:/TECNICA/olo-booking/includes/modules/accommodation/tiles/class-ac-tile-base.php): `'border_radius' => '12px'` come scalar.

### 2.5 Regex HTML inline rotta `/^\s*</`

[`includes/tiles/class-content-tile.php:91`](../includes/tiles/class-content-tile.php#L91) usa la regex anti-pattern §12.1 → tag inline (`<strong>`, `<em>`) al centro del testo vengono mostrati come testo letterale.

```php
if ( preg_match( '/^\s*</', $text_raw ) ) {  // ❌ solo inizio
```

**Fix**: `preg_match( '/<[a-z!\/][^>]*>/i', $text_raw )`.

### 2.6 `&&` in `<script>` PHP

Nessun tile core attivo presenta il bug `&&` → `&#038;&#038;` nei `<script>` inline. Verifica completata, tutti i tile usano correttamente `if` annidati o JS esterno. ✅

### 2.7 HTML user-generated senza `wp_kses_post`

[`includes/tiles/class-html-tile.php:48`](../includes/tiles/class-html-tile.php#L48):

```php
echo $s['html_content'];   // ❌ raw echo
```

Anche se il tile è "html arbitrario" by design, il manuale §12.4 prescrive sempre `wp_kses_post` per HTML utente.

---

## 3. Coperture standard mancanti

### 3.1 Sistema preset 12+1 — §4

**108 tile core (66%) NON espongono il field `preset`**: animatedheading, audio, authorbox, blendtext, button, carousel, chart, code, column, content, darkmode, divider, facebookpage, floatingpanel, fragment, grid, headline, hiddenpop, html, icon, image, inner-columns, instagram, killnextprev, langswitcher, linkinbio, lottie, map, marquee, megamenu, menuanchor, mobilebar, nav, navmenu, newsticker, offcanvas, osmmap, overlay, pagetitlebar, paymentbuttons, pdfpro, pdfviewer, popover, pricelist, readingtime, revealbox, row, scrollprogress, section, shapedivider, shortcode, sitelogo, social, soundcloud, spacer, subnav, svganimator, templateembed, text-block, textmask, textpath, togglebtn, totop, twitterfeed, video, videoplaylist + **tutti i `woo_*` (40)** + **tutti gli `olo_room_*` (10)**.

Alcuni sono giustificati (spacer, divider, html, code, shortcode = utility), molti **no** (button, image, headline, hero, megamenu, social, lottie, video sono candidati ovvi per i preset stilistici).

**Tile con preset mancante nella mappa `TILE_PRESETS`** (config dichiara `preset` ma mapping assente): `shatteredimage`, `viewer360`.

### 3.2 Typography preset — §5.4

Standard: "Esporre **sempre** `typography_preset`". Reale: **3 tile su 163** (button, headline, text-block).

Tile candidati ovvi che non lo espongono: alert, accordion, hero, headline alias (animatedheading), iconbox, iconlist, panel, panelslider, postgrid, content, ecc. → ~160 tile.

### 3.3 Sfondo creativo unificato — §6

Standard: "campo `bg` di type `'background'` per tile con area visiva propria". Reale: solo **button** (1 tile) lo espone. `_styleFieldsBase.js` non lo integra ai tile data-driven (section/row/iconbox/hero).

Conseguenza: la maggior parte dei tile usa ancora `bg_color` solid-only, perdendo gradient/pattern/image/video/gallery.

### 3.4 `tile_padding` / `tile_margin` con `type: 'spacing'` — §9

**130+ tile core** non espongono spacing del tile-wrapper. Alcuni hanno `padding`/`margin` su sotto-componenti (es. `header_padding`, `content_padding`) ma manca la coppia canonica raccomandata dal manuale.

### 3.5 `build_hover_css` PHP — §8

Solo **2 tile su 152** usano l'helper PHP `build_hover_css`. La transizione al pattern dichiarativo v3.13+ è ancora **all'inizio**: 99% dei tile gestisce hover via CSS manuali o ranging inline.

### 3.6 `build_border_radius_css` PHP — §7.2

Solo **20 tile su 152** lo usano. Significa che molti border-radius sono ancora gestiti come scalari `border-radius: Xpx` inline anziché come oggetto `{tl,tr,br,bl}` con sintesi automatica.

---

## 4. Tile esterni — stato critico

### 4.1 OLObooking — 84 tile, 6 moduli

| Modulo | Tile | `build_border_css` | `build_hover_css` | `safe_color_css` | Inspector vuoto |
|---|---:|---:|---:|---:|---:|
| accommodation | 28 | 28 (100%) | 0 | 6 | 28 (100%) |
| appointments | 5 | 0 | 0 | 1 | 5 |
| events | 3 | 0 | 0 | 1 | 3 |
| real-estate | 22 | 0 | 0 | 22 | 17 |
| rentals | 3 | 0 | 0 | 1 | 3 |
| restaurants | 4 | 0 | 0 | 1 | 4 |

**72 / 84 (86%) tile booking** hanno `get_controls() { return []; }` — ZERO controlli inspector. L'utente non può configurarli dal builder; default sono PHP-only.

**0 / 84 tile booking** hanno un config JS in olobuild `src/config/elements/`. La configurazione lato Vue per i tile booking è assente.

**Issue specifici accommodation** (modulo più maturo):
- [`class-ac-tile-base.php`](D:/TECNICA/olo-booking/includes/modules/accommodation/tiles/class-ac-tile-base.php) `common_defaults()` espone `border_radius: '12px'` scalar, `hover_border_radius` / `hover_border_width` / `hover_border_color` / `hover_lift` come field separati (anti-pattern §8)
- Nessun preset stilistico (12+1)
- Nessun typography_preset
- Stringhe `name` non internazionalizzate (`'Ac — Hero'` invece di `__('Ac — Hero', 'olo-booking')`)

**Issue specifico appointments** ([`class-appointmentform-tile.php`](D:/TECNICA/olo-booking/includes/modules/appointments/tiles/class-appointmentform-tile.php)):
- Solo 1 default (`service_id`)
- Nessun border, padding, color, font
- Nessun helper PHP
- Stringa hardcoded IT non i18n: `'Nessun servizio appuntamento disponibile.'`

### 4.2 OLOlang — `langswitcher` (core)

Presente come tile core di olobuild stesso (`langswitcher.js`). Conforme al pattern base ma:
- Nessun preset stilistico
- Niente typography_preset
- Niente bg creativo

### 4.3 OLOvtour — `viewer360` (core)

Presente come tile core (`viewer360.js`). Ha preset 12+1 nel config, ma:
- Tutti 12 i preset sono **vuoti `{}`** in `TILE_PRESETS` → bug §4.1
- Manca dal mapping `TILE_PRESETS` come chiave principale (verifica manuale richiesta)

### 4.4 Tile `olo_room_*` (10) — Vue mock non reattive

Tutti i 10 Vue `OloRoom*Tile.vue` (booking, dashboard, gallery, hero, hours, info, location, map, pricing, related, rules, search, share, stats, status) hanno `0 riferimenti ai settings` → anteprime mock statiche, non reagiscono ai cambi inspector → violano §13 (WYSIWYG).

Esempio: [`OloRoomBookingTile.vue`](../src/components/Tiles/OloRoomBookingTile.vue) mostra "€ 25,00 / ora" hardcoded.

---

## 5. WYSIWYG / Vue components

✅ Tutti i 152 tile core hanno un Vue component.

❌ **Vue non-reattive** (settings refs = 0):
- Core: `FragmentTile`, `KillNextPrevTile`, `WooProductFilterTile` (Column è giustificato, è un wrapper)
- Esterni: tutti gli `OloRoom*Tile.vue` (28), molti `Property*Tile.vue` (15+)

❌ **Stringhe IT hardcoded** in Vue (no `t()`):
- `AppointmentGridTile.vue`: `btn_text: 'Prenota'`
- `BookingPickerTile.vue`, `BookingTile.vue`: `'Servizio #...'`, `'Servizio corrente'`, `'Seleziona servizio'`, `'Consulenza Fiscale'`
- `BreadcrumbsTile.vue:8,11`: `Category`, `Current Page`
- `LoginformTile.vue:219`: `Hai già un account? Accedi`
- `MapTile.vue`: `'Mappa Servizio Dinamico'`, `'Prezzo'`
- `OloRoomAvailabilityTile.vue`: `Libero`, `Parziale`, `Occupato`
- `OloRoomHeroTile.vue`: `'Prenota questa sala'`
- `OloRoomStatsPublicTile.vue`: `'Prenotazioni'`
- `PostGridTile.vue:70`: `Carica altri`
- `PropertyFeaturesTile.vue`, `PropertyInfoTile.vue`, `PropertySearchTile.vue`: dati mock italiani hardcoded
- `QueryloopTile.vue`: `Tutti`, `Tutorial`, `News`, `Guide`, `Carica altro`
- `RevealboxTile.vue:129`: `<h3>Titolo</h3>` come default
- `ServiceResultsTile.vue`: `Carica altro`
- `WooProductBundleTile.vue`: `Aggiungi bundle`
- `WooProductMetaTile.vue`: `Magliette`, `Nuovo`, `Cotone`

---

## 6. Score per categoria (priorità intervento)

| Score | Categoria | Tile | Stato |
|---:|---|---|---|
| 🟢 90-100% | Vue presence + safe_color_css | core | Conforme |
| 🟡 70-89% | build_border_css | core | Buona ma non universale |
| 🟠 30-69% | Preset config (34%) | core | Lacunosa |
| 🔴 1-29% | `build_hover_css` (1%), `build_border_radius_css` (13%), typography_preset (2%), bg creativo (1%) | core | Critico |
| 🔴 0% | Tutto sopra | booking esterni | Critico/assente |

---

## 7. Priorità di intervento

### P0 — bug funzionali utente
1. **Fix preset vuoti** in 28 tile core (alert, flipcard, slideshow, ecc.): popolare `TILE_PRESETS` con valori reali.
2. **Fix `text_effect_target` mismatch** in ~30 tile: aggiungere override nei defaults JS + PHP.
3. **Fix regex HTML** in `class-content-tile.php:91`.
4. **Fix booking inspector vuoto**: i 72 tile booking senza `get_controls()` sono inconfigurabili da builder.

### P1 — anti-pattern §8 (hover dichiarativo)
1. Rinominare `hover_X` separati a `withHover()` bilaterale in 9 tile core identificati.
2. Refactor `Olo_Ac_Tile_Base::common_defaults()` per spostare `hover_border_*` / `hover_lift` su `border_hover` esistente.

### P2 — adozione standard universale
1. **typography_preset**: aggiungere agli ~30 tile testuali più usati (alert, accordion, hero, iconbox, iconlist, panel, postgrid, content, ecc.).
2. **bg creativo**: aggiungere `bg: { type: 'none' }` ai tile con area visiva (button è già OK; aggiungere a section, row, hero, panel, iconbox, card, gallery).
3. **`build_hover_css` PHP**: migrare i 9 tile §8 sopra al pattern dichiarativo (è già coperto dal lato JS via `withHover` ma il PHP esegue ancora parsing manuale in alcuni casi).
4. **`build_border_radius_css` PHP**: portare al 100% dei tile con border-radius esposto.

### P3 — Vue WYSIWYG
1. Refactor 28 `OloRoomTile.vue` + 15 `PropertyTile.vue` per essere data-driven dai settings (eliminare mock statici).
2. Wrappare tutte le stringhe IT hardcoded in Vue con `t()`.

### P4 — preset stilistici espansione
1. Aggiungere `preset` (12+1) ai tile candidati: button, image, hero, lottie, video, social, megamenu, panel, panelslider (~10 tile con identità visiva propria).

---

## 8. File scansionati

- `D:/TECNICA/olobuild/includes/tiles/class-*-tile.php` (152 file)
- `D:/TECNICA/olobuild/src/config/elements/*.js` (163 file + 2 shared)
- `D:/TECNICA/olobuild/src/components/Tiles/*Tile.vue` (232 file)
- `D:/TECNICA/olobuild/src/components/Builder/BuilderInspector.vue` (mappa `TILE_PRESETS`)
- `D:/TECNICA/olo-booking/includes/modules/{accommodation,appointments,events,real-estate,rentals,restaurants}/tiles/class-*-tile.php` (84 file)

Metodologia: scansione programmatica via grep/regex + ispezione manuale a campione su tile rappresentativi di ogni categoria (audio, button, content, querylo­op, flipcard, achero, appointmentform, OloRoomBooking).

---

*Audit eseguito da Claude (Olobuild 3.49.x baseline) — 2026-05-11.*
