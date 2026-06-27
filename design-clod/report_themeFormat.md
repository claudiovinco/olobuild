# REPORT — Formato tema importabile OLObuild (per scrivere il tema "clod-evoluzione")

## 1. `theme.json` di Cadence (riferimento completo)

Path: `D:\TECNICA\olobuild\assets\data\themes\cadence\theme.json` (un'unica riga, qui riformattato):

```json
{
  "id": "cadence",
  "name": "Cadence",
  "author": "Olobuild",
  "version": "1.0.0",
  "tags": ["health","fitness","coaching","personal-trainer"],
  "description": "Cadence — Health & Fitness personal coach. …",
  "menu": {
    "name": "Cadence Menu",
    "items": [
      {"title":"Programmes","url":"#programmes"},
      {"title":"Results","url":"#results"},
      {"title":"How it works","url":"#process"},
      {"title":"Pricing","url":"#pricing"}
    ]
  },
  "templates": {
    "header":     {"file":"header.json",     "type":"header", "title":"Cadence — Header"},
    "footer":     {"file":"footer.json",     "type":"footer", "title":"Cadence — Footer"},
    "homepage":   {"file":"homepage.json",   "type":"page",   "title":"Cadence — Homepage"},
    "programmes": {"file":"programmes.json", "type":"page",   "title":"Cadence — Programmes"}
  },
  "activate": { "header": "header", "footer": "footer" },
  "pages": {
    "home":       {"title":"Home",           "template":"homepage",  "set_as_homepage":true},
    "programmes": {"title":"The programmes", "template":"programmes"}
  },
  "styles": {
    "colors": {
      "primary":"#ff6b54","primary_contrast":"#ffffff",
      "secondary":"#5ec8d8","secondary_contrast":"#101218",
      "muted":"#1e2129","muted_contrast":"#aab1be",
      "text":"#aab1be","text_muted":"#6f7785",
      "background":"#1a1d24","border":"rgba(255,255,255,.09)","link":"#ff6b54"
    },
    "typography": {
      "font_family":"\"Figtree\", -apple-system, sans-serif",
      "font_family_heading":"\"Big Shoulders Display\", sans-serif",
      "font_size_base":"16px","line_height":"1.6",
      "font_weight_heading":"800","heading_line_height":"0.96"
    },
    "google_fonts": ["Big Shoulders Display","Figtree"],
    "spacing": {}
  }
}
```

Spiegazione chiavi:
- **`id`** — deve coincidere con il nome della cartella `assets/data/themes/<id>/` (il lookup usa `basename(dir)` per screenshot e l'`id` interno per il match in `import_theme`).
- **`name` / `author` / `version` / `tags` / `description`** — metadati card nel modale Importa Temi.
- **`menu`** — `{name, items:[{title,url}]}`: viene creato un nav menu WP vero; le `url` sono custom link (anchor `#sezione` o path relativi).
- **`templates`** — mappa `chiave → {file, type, title}`. `type` ∈ `header` | `footer` | `page` (e qualunque type supportato da `olo_templates`). La chiave è il riferimento usato da `activate` e `pages`.
- **`activate`** — `{header: <chiave>, footer: <chiave>, "404": <chiave>}` → setta `olo_active_header` / `olo_active_footer` / `olo_active_404`.
- **`pages`** — mappa `chiave → {title, template:<chiave templates>, set_as_homepage?:bool, set_as_blog?:bool}` → crea/riusa pagine WP e le collega al template via post meta `_olo_template_id`.
- **`styles`** — merged in `olo_styles` (vedi §5).

Chiavi usate da TUTTI i 50 temi esistenti (verificato programmaticamente): `id, name, author, version, tags, description, menu, templates, activate, pages, styles` con `styles.{colors, typography, google_fonts, spacing}`.

File nella cartella tema di Cadence: `theme.json, header.json, footer.json, homepage.json, programmes.json, logo.png, logo-light.png, screenshot.jpg`.

## 2. `includes/class-theme-importer.php` (370 righe) — flusso completo

Path: `D:\TECNICA\olobuild\includes\class-theme-importer.php`. Classe `Olo_Theme_Importer`, tutta statica.

**`get_themes()`** (r.6-31): scandisce `OLO_PATH . 'assets/data/themes/*/theme.json'`; per ogni tema cerca lo **screenshot** in quest'ordine: `screenshot.jpg`, `screenshot.png`, `screenshot.webp` (r.17). Aggiunge i campi preview (`build_preview`, r.39-83): `category` (override `theme.json.category`, fallback mappa hardcoded r.118-135, poi primo tag), `zone` (override `theme.json.zone`), `accent/bg/ink/font/light/pal/google_fonts/url` derivati da `styles.colors`/`styles.typography`. `preview.ink` in theme.json può forzare il colore titolo anteprima (r.52).

**`import_theme($theme_id)`** (r.160-355), nell'ordine:

1. **Loghi** (r.171-185): copia `logo.png` → `uploads/olobuild-logo.png` e `logo-light.png` → `uploads/olobuild-logo-light.png`. ⚠️ Nome destinazione FISSO cross-tema: ogni import sovrascrive il logo del tema precedente. Se manca `logo-light.png`, fallback a `logo.png`.
2. **Menu** (r.187-215): `wp_create_nav_menu(menu.name)`; se esiste già riusa il `term_id` e **svuota le voci esistenti** (anti-duplicazione al re-import, r.198-204), poi crea le voci come custom link.
3. **Template** (r.217-261): per ogni voce di `templates` carica il file JSON **come stringa** e fa 2 sostituzioni placeholder:
   - `LOGO_PLACEHOLDER` → URL logo caricato (str_replace, r.230);
   - `"menu_id":"auto"` → id menu reale, con **regex tollerante agli spazi**: `preg_replace('/"menu_id"\s*:\s*"auto"/', '"menu_id":'.intval($menu_id), $json_str)` (r.238) — ⚠️ gotcha storico Atelier: i JSON pretty-printed hanno `"menu_id": "auto"` con spazio e lo str_replace falliva → megamenu "Seleziona un menu".
   
   Poi `regenerate_ids()` (r.357-369: nuovo `wp_generate_uuid4()` per ogni nodo, ricorsivo su `children` e su `settings.columns_data[].tiles`), e `Olo_Database::create_template(['title','type','content','status'=>'published'])`. Costruisce `$id_map[chiave] = nuovo id`.
4. **Attivazione** (r.263-278): `activate.header/footer/404` → `update_option('olo_active_header'|'olo_active_footer'|'olo_active_404', $id_map[...])`.
5. **Stili** (r.280-286): `wp_parse_args($theme_json['styles'], $current)` → `update_option('olo_styles', $merged)`. ⚠️ Merge **solo top-level e NON ricorsivo**: il blocco `colors` del tema sostituisce per intero il blocco salvato (le chiavi colore mancanti ricadono poi sui default in `get_styles()`); i blocchi non dichiarati dal tema restano quelli esistenti.
5b. **Cursore** (r.288-297): chiave opzionale `cursor` (oggetto) → `update_option('olo_magnetic_cursor', Olo_Magnetic_Cursor::sanitize(...))` (cursore neon anello+dot).
6. **Pagine** (r.299-349): per ogni `pages.*`, se `set_as_homepage` **riusa** la `page_on_front` esistente (idempotente, niente duplicati "Home"); idem `set_as_blog` con `page_for_posts`. Altrimenti `wp_insert_post` nuova pagina. Poi `update_post_meta($page_id,'_olo_template_id',$id_map[tpl])` + `update_option('page_on_front'/'show_on_front','page')`.
7. `update_option('olo_setup_complete', true)`.

**Elenco COMPLETO chiavi theme.json supportate dall'importer/get_themes**: `id`, `name`, `author`, `version`, `tags`, `description`, `category`, `zone`, `preview.ink`, `url`, `menu{name,items[{title,url}]}`, `templates{key:{file,type,title}}`, `activate{header,footer,404}`, `styles{…}` (qualsiasi blocco di olo_styles), `cursor{…}`, `pages{key:{title,template,set_as_homepage,set_as_blog}}`.

**⚠️ Cosa l'importer NON fa**: non tocca `olo_global_colors` né `olo_global_typography` (REST `import_theme` in `class-rest-api.php:2214` chiama solo `Olo_Theme_Importer::import_theme`). Vedi gotcha al §5.

## 3. Schema dei file template (homepage.json / header.json)

Path: `D:\TECNICA\olobuild\assets\data\themes\cadence\homepage.json` (1161 righe) e `header.json` (63 righe). Il file è **un array JSON di sezioni** (no wrapper). Schema nodo, identico a ogni livello:

```
[                                  ← array root = sezioni
  {
    "id": "cd-se-4",               ← qualsiasi stringa: rigenerata all'import
    "type": "section",
    "settings": { "style":"default", "width":"large", "padding":"large" },
    "style":    { "bg": { "type":"solid", "color":"#1a1d24" } },
    "advanced": {},
    "children": [
      { "id":"cd-ro-3", "type":"row",
        "settings": { "layout":"100", "gap":0, "vertical_align":"center" },
        "style":{}, "advanced":{}, "children": [
          { "id":"cd-co-2", "type":"column",
            "settings": { "width":"1-1", "width_medium":"1-1" },
            "style":{}, "advanced":{}, "children": [
              { "id":"cd-hero-intro", "type":"introsplit",
                "settings": { …tutte le chiavi del config elements/introsplit.js… },
                "style":{}, "advanced":{}, "children":[] }
            ] }
        ] }
    ]
  }
]
```

- Gerarchia fissa: **section → row → column → tile**. Ogni nodo ha sempre i 5 campi `id, type, settings, style, advanced, children`.
- `settings` sezione (da `src/config/elements/section.js`): `width` ∈ `small(900)|default(1200)|large(1400)|xlarge(1600)|expand|fullbleed`; `padding` ∈ `remove-vertical|small|default|large|xlarge|custom` (+`padding_top_custom`/`padding_bottom_custom`); `style` ∈ `default|muted|primary|secondary`; `bg_scope` ∈ `container|section`. ⚠️ Regola memoria: full-bleed = `padding:"remove-vertical"` + `width:"fullbleed"` ("none"/"full" NON esistono).
- `style.bg` sezione: `{type:"solid"|"none"|…, color:"#…"}`.
- Row: `settings.layout` ("100", …), `gap`, `vertical_align`. Column: `width` "1-1","1-2",… + `width_medium`.
- Tile: `settings` = chiavi ESATTE del config inspector (es. introsplit: `eyebrow, headline, accent, headline_tail, lead, cta_text/cta_url/cta_bg, cta_radius:{tl,tr,br,bl}, media_image, media_bg:{type}, media_aspect, badge_*…`). Spacing = oggetti `{top,right,bottom,left}`, radius = `{tl,tr,br,bl}`.
- Header (`header.json`): stessa struttura, 1 sezione `width:"fullbleed"` + `padding:"remove-vertical"`, 1 row, 1 column, 1 tile `megamenu` con `"menu_id":"auto"` (sostituito all'import) e tutto il set mobile fullscreen (`mobile_style:"fullscreen"`, `mobile_link_font:"heading"`, `mobile_link_size`, `mobile_footer_*`, `extra_link_1_*`).

## 4. FONT — dichiarazione, self-host, frontend + canvas

**Dichiarazione nel tema**: 2 punti di `theme.json → styles`:
- `google_fonts: ["Big Shoulders Display","Hanken Grotesk","Space Mono"]` — elenco famiglie da scaricare;
- `typography.font_family` (body), `typography.font_family_heading`, `typography.font_family_mono` — stack CSS completi (es. `"\"Space Mono\", monospace"`).

**Big Shoulders Display è già in uso**: Cadence lo usa (`google_fonts` + `font_family_heading`). "Space Mono" è già usato dal tema `mono` (`google_fonts:["DM Sans","Space Mono"]`). `font_family_mono` esiste nei default (`class-style-system.php:44`) ma nessun theme.json lo setta ancora — è supportato: emesso come `--olo-font-family-mono` solo se non vuoto (r.601-603).

**Olo_Font_Host** (`includes/class-font-host.php`): `get_font_face_css($families, $weights='300;400;500;600;700')` scarica il CSS da `fonts.googleapis.com/css2` con UA Chrome (per ottenere woff2), salva i file in `/uploads/olo-fonts/<md5>.woff2`, riscrive gli URL in locali e cacha il CSS in transient `olo_fonthost_<md5>` (1 mese; 5 min se fallisce). Se anche un solo download fallisce → stringa vuota = fallback font di sistema, MAI URL Google residui. `Olo_Font_Host::flush()` svuota la cache transient.

**Pipeline frontend** (`class-style-system.php::generate_css`, r.506-567): self-hosta (1) le famiglie in `google_fonts`, (2) le famiglie di `olo_global_typography` non già incluse, (3) le famiglie trovate in `typography.font_family/_heading/_mono` non già incluse (salta i generici di sistema). Quindi basta `google_fonts` O la typography per avere il @font-face. Il CSS è iniettato da `class-frontend-renderer.php:483` (e header/footer/404/archive integration).

**Canvas builder**: l'iframe del canvas è `?olo_builder_iframe=1` (`BuilderCanvas.vue:298-308`) = **render frontend reale** → riceve lo stesso `generate_css()` con @font-face self-hosted. Il documento padre (UI builder, anteprime inspector) usa invece `stylesStore.cssVariables` (`src/stores/styles.js:42-55`) che fa `@import` **diretto** da `fonts.googleapis.com` con le famiglie di `google_fonts`. `oloData.stylesCss` = `generate_css()` è passato al builder (`class-olo-builder.php:761`).

**Quindi per Big Shoulders Display + Hanken Grotesk + Space Mono servono solo**: le 3 famiglie in `styles.google_fonts` + le 3 chiavi typography settate. Frontend, canvas iframe e UI builder coperti.

**⚠️ Gotcha pesi**: sia `Olo_Font_Host` sia l'@import client-side scaricano pesi `300;400;500;600;700`. Il blueprint clod usa Big Shoulders **800/900** (h1-h3 weight 800, brand 900): il browser farà faux-bold dal 700. Per fedeltà pixel-perfect valutare di estendere i pesi (come fa `class-proslider-tile.php:217` che passa `'300;400;500;600;700;800;900'`) — oggi `generate_google_fonts_import()` (r.955) non passa weights custom.

**Blocco `olo_styles.typography` — chiavi esatte** (default r.41-65): `font_family, font_family_heading, font_family_mono, font_size_base, font_size_h1..h6, line_height, font_weight_heading, letter_spacing, font_weight_body, font_size_h1/h2/h3_tablet, font_size_h1/h2/h3_mobile, heading_line_height, heading_letter_spacing, heading_text_transform`. Le tile leggono i ruoli via `resolveFontFamily()` (`src/composables/oloTileDefaults.js:119-144`): `body|sans → var(--olo-font-family)`, `heading|serif → var(--olo-font-family-heading)`, `mono → var(--olo-font-family-mono, ui-monospace,…)`.

## 5. Colori — `olo_styles.colors` + `olo_global_colors`

**Ruoli core** (`get_defaults()` r.25-40): `primary, primary_contrast, secondary, secondary_contrast, muted, muted_contrast, success, warning, danger, text, text_muted, background, border, link`. Ognuno emesso come `--olo-color-<ruolo con _ → ->` dentro `.olo-template` (r.572-575). Alias sempre emessi (r.585-591): `--olo-color-on-primary, -text-soft, -text-faint, -surface, -surface-alt, -error, -info`. Blocchi correlati: `neutrals` `{mode,tint,scale[7]}`, `dark_colors` (override su `html.olo-dark-mode`), `dark_mode {enabled,strategy}`.

**`olo_global_colors`** (option separata): array `[{id, value, name?}]` — swatch della palette globale, emessi come `--olo-color-<id>` **DOPO** `olo_styles.colors` (r.700-711) → **VINCONO** (gotcha v1.4.107). `save_styles()` (r.210-241) sincronizza i global core quando si salva dal pannello, **ma `import_theme` usa `update_option` diretto e NON sincronizza**: se il sito ha già `olo_global_colors` con id core (primary/…) di un tema precedente, i colori del nuovo tema **non si vedranno** finché non si riallineano i global (PUT `/global-colors` o un save dal pannello Palette). Da tenere presente quando testeremo clod-evoluzione su un sito già usato.

**Le tile** usano i token via `resolveColor(userValue, 'var(--olo-color-…)')` (JS `oloTileDefaults.js:110` / PHP `Olo_Tile_Base`): valore '' nel settings ⇒ token del tema.

**Mapping proposto per clod-evoluzione** (dal blueprint `clod.css` r.8-13: `--ink:#0b0c0f --ink-2:#101218 --ink-3:#161922 --bone:#ECEAE3 --bone-dim:#a0a298 --faint:#6a6c64 --line:rgba(236,234,227,.10) --signal:#C6F24E --signal-d:#a9da2c`):

```json
"colors": {
  "primary": "#C6F24E",            ← signal (lime): CTA, link, accenti
  "primary_contrast": "#0b0c0f",   ← testo ink su lime (come .nav__cta del blueprint)
  "secondary": "#ECEAE3",          ← bone: bottoni/elementi invertiti
  "secondary_contrast": "#0b0c0f",
  "muted": "#161922",              ← ink-3: superfici/card rialzate (surface-alt)
  "muted_contrast": "#a0a298",     ← bone-dim
  "text": "#ECEAE3",               ← bone: body (il blueprint usa bone come color del body)
  "text_muted": "#a0a298",         ← bone-dim (--faint #6a6c64 resta valore puntuale nelle tile)
  "background": "#0b0c0f",         ← ink
  "border": "rgba(236,234,227,.10)",  ← line
  "link": "#C6F24E"
}
```
Così le tile ereditano gratis: `--olo-color-surface`=ink, `--olo-color-surface-alt`=ink-3, `--olo-color-text-soft/faint`=bone-dim, `--olo-color-on-primary`=ink. `--ink-2 #101218` e `--faint #6a6c64` non hanno ruolo core: usarli come valori espliciti nelle tile o aggiungerli come global colors extra (id es. `ink-2`, `faint`) — solo id che il GlobalColorsPanel genera/conosce (regola TOKEN_MAPPING). Typography: `font_family:"\"Hanken Grotesk\", -apple-system, sans-serif"`, `font_family_heading:"\"Big Shoulders Display\", sans-serif"`, `font_family_mono:"\"Space Mono\", monospace"`, `font_weight_heading:"800"`, `heading_line_height:"0.92"`, `heading_text_transform:"uppercase"` (h1-h3 del blueprint sono uppercase weight 800 lh .92), `line_height:"1.55"`.

## 6. Front page all'import + gotcha "template orfano"

- Step 6 dell'importer (r.299-349): la pagina con `set_as_homepage:true` **riusa** la static front page esistente (`page_on_front`) se valida — aggiorna titolo/status e ripunta `_olo_template_id` al template appena creato; altrimenti crea la pagina e setta `page_on_front` + `show_on_front='page'`. Stessa logica per `set_as_blog` con `page_for_posts`. Il collegamento pagina→template è SOLO il post meta `_olo_template_id`.
- **Gotcha "import_theme orfana il template linkato"** (storico Atelier): ogni import crea **template NUOVI** (`create_template`) e ripunta `_olo_template_id` ai nuovi id. Il template della tornata precedente resta in `olo_templates` ma scollegato (orfano) — e qualunque modifica manuale fatta su quel template è persa dal punto di vista della pagina. Workflow corretto durante l'iterazione su un tema: **aggiornare il template esistente in-place** con `Olo_Database::update_template` (⚠️ metodo D'ISTANZA, non statico: `(new Olo_Database())->update_template(...)`), NON re-importare. Il re-import si usa solo per il test finale "da zero".

## Schema riassuntivo per l'autore del tema clod-evoluzione

Cartella: `D:\TECNICA\olobuild\assets\data\themes\clod-evoluzione\` con:
- `theme.json` — `id:"clod-evoluzione"` (= nome cartella), `menu`, `templates{header,footer,homepage,…}`, `activate{header,footer}`, `pages{home:{set_as_homepage:true},…}`, `styles{colors,typography,google_fonts:["Big Shoulders Display","Hanken Grotesk","Space Mono"],spacing:{}}`; opzionali `category`, `zone`, `cursor`, `preview.ink`.
- `header.json` / `footer.json` / `homepage.json` — array di sezioni `{id,type,settings,style,advanced,children}` (section→row→column→tile); nel megamenu `"menu_id":"auto"`; per il logo usare la stringa `LOGO_PLACEHOLDER` se serve immagine.
- `logo.png`, `logo-light.png` (copiati in uploads come `olobuild-logo[-light].png`), `screenshot.jpg|png|webp`.
- Import: REST `POST olo/v1/...` → `Olo_Rest_Api::import_theme` (`class-rest-api.php:2214`) → `Olo_Theme_Importer::import_theme`.
- Attenzioni: pesi font >700 non self-hostati di default; `olo_global_colors` non sincronizzato dall'import; re-import = template nuovi (orfani i vecchi); full-bleed = `width:"fullbleed"` + `padding:"remove-vertical"`.