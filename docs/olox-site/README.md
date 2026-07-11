# OLOX — sito olotheme.com come template olobuild

Replica pixel-perfect del sito marketing olotheme.com (design "Experience" dark:
Fraunces/Inter/JetBrains Mono, 6 colori prodotto) costruita con la famiglia di
tile `olox*` (15 tile, v1.4.315+).

## Struttura

- **Tile**: `src/config/elements/olox*.js` + `src/components/Tiles/Olox*Tile.vue`
  + `includes/tiles/class-olox*-tile.php` (base condivisa `class-olox-base.php`).
- **Asset condivisi**: `assets/css/olox.css` (design system scoped `.oloxp`,
  fixed solo con `.oloxp-live`) + `assets/js/olox.js` (behaviors per
  `data-olox="…"`: rail home, 6 minigiochi, sticky assembler/day, scene hero,
  reveal, scan/pano/xp, quiz — HAND-AUTHORED, no build).
- **Pagine** (13): home experience (composizione a sezioni: 1 `oloxrail` +
  8 fermate `oloxpanel`, montate nel binario a runtime dal modulo JS
  `railassemble`), 6 pagine prodotto, 6 manuali tecnici (`oloxmanual`).
- **Header/Footer condivisi**: template olobuild dedicati (kind `header` /
  `footer` nei JSON) con tile `oloxnav`/`oloxfoot` in modalità AUTO.

## Rigenerare i template

1. `node build-olox-templates.mjs` → genera `olox-out/*.json` (13 template
   con tutti i contenuti; modifica qui i testi).
2. Copiare i JSON sul server in `/tmp/olox-tpl/` + `olox-insert.php` in `/tmp/`.
3. `wp eval-file /tmp/olox-insert.php --allow-root --path=<wp>` — idempotente:
   crea/aggiorna template (status `published`) e pagine WP collegate via
   `_olo_template_id`; slug = nome file.
4. Loghi attesi in `/wp-content/uploads/olotheme-site/` (`<prodotto>-orizz.png`).
5. Header/footer condivisi assegnati a TUTTE le pagine (home inclusa) via
   `_olo_header_id`/`_olo_footer_id`; sulla home il chrome della tile
   `oloxrail` nasconde da solo logo/lingue/credits (CSS `:has` in olox.css).

## Gotcha noti

- Le tile `olox*` sono escluse dal lazy-render delle sezioni
  (`class-frontend-renderer.php::maybe_lazy_wrap`) — runtime al DOMContentLoaded.
- Il tema/UIkit impone `color` su heading/link e `background` su input/pre:
  le regole olox usano specificità doppia `.oloxp.oloxp` (e `!important` sugli
  input del mad-lib) per vincere.
- `html{scroll-behavior:smooth}` del tema: gli scroll programmatici del rail
  usano `behavior:'instant'`.
- Deep-link alle fermate della home: `#go-N` (N = 0..7), usato anche per QA.
- Il quiz tutor somma XP alla barra fissa via CustomEvent `olox:xp`
  (`detail.bonus`, `detail.toast`).
