Audit completato. Ecco il report.

---

# AUDIT TILE vs blueprint "Clod — Evoluzione v2"

Blueprint: `D:\TECNICA\olobuild\design-clod\olobuild-design\project\Clod - Evoluzione (supertemplate) v2.html` + `clod/clod.css` + `clod/evo-fx.css` + `clod/evo-fx.js` + `clod/olomap.js`.
Sezioni: NAV (timecode+hairline) · HERO (meta-row + "Visual/STUDIO" outline-fill + olomap tilt 3D) · STRIP marquee · MANIFESTO (scrub parole) · SERVIZI (lista 01-05 + monitor cursore) · REEL Lavori (drag/rotella/snap/REC/skew) · R&S (4 demo-card temi) · CONTATTO (mail display) · PARTNER · FOOTER.

---

## 1. `marquee.js` — strip servizi → **USABILE COSÌ** (riserva micro)

File: `D:\TECNICA\olobuild\src\config\elements\marquee.js` + `includes\tiles\class-marquee-tile.php`.
Settings rilevanti già presenti:
- `text_items` (stringa) + `separator` con **`separator_color` + `separator_size`** → il puntino lime si ottiene con `separator: '●'`, `separator_color: lime` (PHP riga 111-114, 225-227: `.olo-mq-sep { color: …; font-size: …px }`)
- `font_family` (type `font-family` → Big Shoulders), `font_size` (max 48), `font_weight`, `text_transform: uppercase`, `height`, `full_width`, `bg_color`, `border_top`/`border_bottom`/`border_color` (il blueprint ha `border-block:1px`)
- **`velocity_skew` ESISTE GIÀ** (config righe 27-32, PHP righe 26-30/72-76/195/252): `vskew_base_speed`, `vskew_scroll_boost`, `vskew_max_skew`, `vskew_damping` — copre ESATTAMENTE "velocità reattiva allo scroll + skew wobble" del blueprint (evo-fx [7a] + script inline #9)

Manca (solo finezza pixel-perfect): il blueprint disegna il puntino come **cerchio CSS 7px** (`span::after{width:7px;height:7px;border-radius:50%}`), non un glifo. Il glifo '●' a `separator_size` calibrato è quasi indistinguibile; se in verifica visiva non regge → estensione minima `separator_style: 'dot'` (span cerchio).

## 2. `hoverlist.js` — lista servizi 01-05 + monitor → **ESTENDERE**

File: `src\config\elements\hoverlist.js` + `includes\tiles\class-hoverlist-tile.php`.
Già presenti: righe con hover indent (`hover_indent`) + `hover_bg` + `line_color`; **il peek che segue il cursore ESISTE** (PHP righe 100-133: `position:fixed`, appendTo body, pointermove) ma è SOLO immagine (`peek`, `peek_width`, `peek_ratio`, item `image`).
Layout attuale: `swatch colore + name + sub` in flex (riga 78 PHP) — il blueprint vuole `grid 64px | 1fr | auto`: **numero mono** (faint → lime in hover) | **nome display** clamp(26-44px) uppercase | **descrizione a destra** (14px, max 30ch, right, nascosta mobile).
Opzioni da aggiungere:
- `lead_mode: 'swatch' | 'number'` (+ item `number` o auto 01..) con `number_color` / `number_hover_color`
- item `desc` + `desc_color` / `desc_size` / desc a destra (hide su mobile)
- `name_size` max da 36 → ~56 + `name_uppercase` toggle
- `peek_mode: 'image' | 'monitor'`: variante monitor = box ~200px con 4 cornici viewfinder lime + "● STILL" + barra label `numero (lime) + nome` (riproduce `.fx-mon` di evo-fx.css [6]) — riusa il runtime peek esistente

## 3. sec__head + manifesto

- **`section-header.js`** → **USABILE COSÌ** per i `sec__head`: ha eyebrow, `headline_lines` (multi-riga con colore/italic), `headline_font_family/size(max 160)/weight(800)/align`, **tagline destra con `tagline_caption` mono** e `vertical_align: 'baseline'` + `layout: 'split'`. Il "/ cosa faccio — 05" = `tagline_caption`; maiuscolo del titolo scrivendo il testo in maiuscolo (non c'è toggle uppercase — eventuale micro-estensione). ⚠️ da verificare in resa: `tagline_text` vuoto con solo caption (PHP riga 93: `show_tagline` dipende solo da `tagline_show`+split, ok).
- **`headline.js`** → USABILE per h2 singoli (ha `text_stroke` outline, font-family, uppercase, typography size fino 120), ma non ha lo slot mono a destra: per i sec__head usare section-header.
- **`animatedheading.js`** → **NON ADATTA** al manifesto: animazioni typing/rotating/fade/slide/highlight/clip su parole CICLICHE, nessun legame con lo scroll.
- **`scrollscrub.js`** → è un'altra cosa: sezione PIN sticky N×100vh che rimappa lo scroll verticale su translateX di una traccia card (con scrubbar). **NON ADATTA** al manifesto (e nemmeno ideale per il reel, vedi §9).
- **`blendtext.js`** → testo gigante con blend-mode o "torcia" spotlight che segue il cursore. **NON ADATTA**: niente scrub parola-per-parola.

**GAP manifesto**: l'effetto "parole che si accendono allo scroll" (evo-fx [5]: split in `.fx-w`, opacity .13→1 con `p=clamp((vh*.9-top)/(vh*.6))`) **non esiste in nessuna tile**. Serve estensione (opzione `scroll_reveal_words` su una tile testo display dedicata o nuova tile `scrubtext` con: testo con `<em>` accento lime, font display, size clamp, lead sotto).

## 4. `megamenu.js` — header → **ESTENDERE**

File: `src\config\elements\megamenu.js` (787 righe) + `includes\tiles\class-megamenu-tile.php`.
Già presenti: `logo_text` wordmark + `logo_dot` (+`logo_text_color/size`), `extra_link_1..4` (+ `extra_link_1_button`/`extra_link_2_button` stile CTA, `extra_links_right`), sticky completo, mobile fullscreen (numeri, footer CTA), topbar CTA.
Mancano per il blueprint:
- **Timecode scroll** ("TC 00:00:00:00" mono che avanza col progresso pagina, 25fps — evo-fx [3]): NIENTE di simile esiste → nuove opzioni `show_timecode` + `timecode_color` (+ durata virtuale)
- **Hairline progresso scroll** (2px lime sul bordo inferiore della nav, `width:calc(var(--p)*100%)`): → `scroll_progress` + `progress_color`/`progress_height`. (`scrollprogress.js` esiste ma è una barra **fixed top/bottom del viewport** con z-index proprio — non ancorata al bordo della nav sticky: solo surrogato, non pixel-perfect.)
- **Dot inline nel wordmark**: il blueprint è "clod<span lime>.</span>eu" — `logo_dot` attuale è un cerchio PRIMA del testo con `background:currentColor` (PHP riga 631), non colorabile né inline → estensione `logo_dot_position: 'before'|'inline'` (marker nel testo, es. `|` → span) + `logo_dot_color`

## 5. Contatto — `cta-banner.js` / `mediacta.js` → **ESTENDERE mediacta**

- `cta-banner.js`: banner editoriale 3-colonne/stack con CTA **pillola** (headline max 80px). Manca tutto il linguaggio "mail display".
- `mediacta.js`: più vicina — `align: center`, `eyebrow`, titolo gigante (`headline_size` fino 160), `accent_text`, `subhead`, `pad_y`, media_bg può essere none. MA le CTA sono **bottoni pill** (`btn_radius` 999).
Blueprint: eyebrow mono lime · h2 display clamp(52-148) · sub · **link mail display 22-34px con freccia SVG inline + border-bottom 2px lime + hover gap 12→18px**.
Opzioni da aggiungere a mediacta: `cta_style: 'button' | 'maillink'` (testo display + freccia + underline accent + hover gap), `eyebrow_mono` (font mono + tracking .18em). L'onda di lettere + mail magnetica (evo-fx [8]) è un di-più non esistente: eventuale opzione `letters_wave` (bassa priorità, pointer:fine only).

## 6. Demo card temi R&S — showcasegrid/productgrid/workgrid/worklist → **NON ADATTE → tile nuova (o estensione showcasegrid)**

Blueprint `.demo`: card 250-320px in riga scroll-snap; **mini-preview del tema GENERATA via CSS** da 4 variabili per card (`--c-bg, --c-ink, --c-acc, --c-font`): quadratino logo accent + headline nel font del tema + bottone-pillola accent + zone-tag mono pill; footer `nome display + categoria`; hover translateY(-4px)+border.
- `showcasegrid.js`: card media(immagine/bg)+kicker+titolo+freccia — la preview resta un'immagine. NON ADATTA così.
- `productgrid.js`: footer categoria+titolo+prezzo c'è, ma media = immagine. NON ADATTA.
- `workgrid.js` / `worklist.js`: griglia/lista lavori classiche. NON ADATTE.
La preview "mini-sito" parametrica non esiste da nessuna parte → **nuova tile dedicata** (es. `themedemos`: items con `c_bg/c_ink/c_acc/c_font/zone_tag/light` + nome/categoria/link, scroll-snap orizzontale) oppure estendere showcasegrid con `media_mode: 'theme-preview'` + 5 itemFields. Consiglio tile nuova: il render è troppo specifico per infilarlo nella showcasegrid.

## 7. Riga Partner — trust-strip/statstrip/hoursstrip → **ESTENDERE statstrip** (le altre NON ADATTE)

Blueprint: `label mono uppercase faint + 2 link display 22-34px uppercase` in flex-row, hover color, border-top.
- `trust-strip.js`: icone+testo max 24px o pill glass — testi piccoli, niente label+link display. NON ADATTA.
- `hoursstrip.js`: giorno/orario/nota. NON ADATTA.
- `statstrip.js`: la più vicina (valore grande font heading fino 96px + label mono) ma: niente **link per item**, layout a colonne con divisori ≠ riga inline con label a sinistra, niente hover color. Estensioni: item `link_url`, `variant: 'inline-row'` (label strip a sinistra + valori in flex), `value_hover_color`, `value_uppercase`.
- Alternativa compositiva senza estensioni: row con text-block mono + tile `nav` (usata nei footer) — ma nav non ha font display/size grande: comunque estensione.

## 8. Footer → **USABILE COSÌ** (pattern collaudato)

Da `assets/data/themes/cadence/footer.json` e `atelier/footer.json` la composizione standard è:
```
section → row1: 4 colonne [headline brand + text-block descr.] [headline h4 + nav]×3
        → row2: divider
        → row3: 2 text-block (copyright | nota)
```
Tile usate: `headline` (heading_font/size/uppercase/color), `text-block`, `nav` (items, direction, link_color, link_hover_color), `divider`. Il footer Clod (brand 46px + dot lime, h4 mono, 2 colonne liste, bottom-bar mono) si compone identico. Unica finezza: il **dot lime dentro "clod.eu"** — headline fa esc_html del testo; soluzione in composizione (text-block HTML con span o micro-estensione `accent_text` su headline, da decidere in fase compose).

## 9. Reel orizzontale "Lavori" → **NUOVA TILE dedicata** (base codice: categoryrail + scrollscrub)

Cosa fa ognuna:
- `carousel.js`: carosello a N slide visibili, frecce/dots/autoplay/loop — niente drag libero, rotella, snap proximity, progress. NON ADATTA.
- `categoryrail.js`: rail **drag-scroll nativo** (runtime inline scoped) di tessere uniformi (card_width/aspect) con overlay+titolo+sub + hint "← drag →". È il pezzo "drag" giusto ma mancano: wheel verticale→orizzontale, progress bar, **altezze sfalsate tall/short/normal**, overlay REC+timecode in hover, skew da velocità, blocco caption iniziale (`reel__pcap`), meta name+tag stile work.
- `proslider.js`: slider full-canvas con editor a layer (stile Slider Revolution). NON ADATTA.
- `overlayslider.js`: slider a colonne immagine+overlay testo, frecce/dots. NON ADATTA.
- `panelslider.js`: card slider con frecce/autoplay. NON ADATTA.
- `scrollscrub.js`: **pin sticky** N×100vh → translateX guidato dallo scroll verticale, con scrubbar e fallback scroll nativo. Modello d'interazione DIVERSO dal blueprint (che è uno scroller libero nel flusso: drag + rotella + snap + progress, la pagina continua a scorrere normalmente).

Verdetto: il reel del blueprint (item `flex 0 0 clamp(260-420px)` con 3 altezze `tall/short/normal` self-aligned, `work__meta` gradiente nome+tag lime, REC overlay con viewfinder+timecode 25fps all'hover, skew ±7° da velocità drag, caption block iniziale, progress 2px lime, wheel-to-horizontal con release ai bordi) = **archetipo non coperto** → nuova tile (es. `reel` / `filmreel`), riusando il runtime drag di categoryrail + la scrubbar di scrollscrub + il pattern REC della futura hoverlist-monitor. Estendere categoryrail è possibile ma la snaturerebbe (6+ feature nuove su una tile "categorie e-commerce").

## 10. Hero — archetipi esistenti (conferma gap)

- `hero` — banner generico centrato (titolo/sub/CTA su sfondo).
- `hero-split` — split testo|media classico (⚠️ VIETATO come surrogato — CREDO).
- `imagehero` — full-bleed foto + velo gradiente + testo sovrapposto (Atelier/Fiori/Loft).
- `masthead` — testata di giornale serif con hairline rules (Dispatch).
- `smearhero` — galleria d'artista con paint-smear che segue il cursore (Canvas).
- `glowhero` — statement centrato su glow radiale, righe normal/accento/**outline**/gradiente (Vela/Prisma) — ha l'outline text-stroke ma NON il fill progressivo allo scroll.
- `audiohero` — musicista: equalizer CSS + cover + mini-player (Soundwave).
- `photocover` — copertina editoriale full-bleed incorniciata + kicker mono (Frame).
- `searchhero` — e-commerce centrato con barra di ricerca + chip (Carrello).
- `producthero` — SaaS centrato con mockup browser/dashboard KPI (tech).
- `featuredstory` — lead editoriale 2 colonne foto+colonna serif (Gazette/Voyage).
- `glowgallery` — eventi: glow + striscia tessere sfalsate sotto il testo (Aurora).
- `chathero` — SaaS: glow + finestra chat con bolle you/ai (Synapse).
- `maskedvideohero` — full-bleed video con maschera ad arco + watermark ghost (Verdano, modello approvato).
- `olo_room_hero` — hero camere OLObooking (dominio booking).
(`introsplit` = sezione feature split, non hero.)

**CONFERMATO: nessuno copre l'archetipo "studio editoriale"** del blueprint: meta-row mono 4 voci con border-bottom · grid 1.15fr/.85fr align end · H1 display clamp(74-210px) su 2 righe con **riga 2 outline (`-webkit-text-stroke`) + fill lime progressivo allo scroll via `background-clip:text` + `--fill`** · sub 30ch con `<b>` accenti · 2 CTA (fill lime + ghost) · **media a destra con cap mono + tilt 3D pointer-follow + parallax** (slot per immagine o infografica olomap) · entrata lettere sfalsate. → **TILE HERO NUOVA dedicata** (es. `studiohero`), pattern 2-file + registrazione manuale PHP.

---

## Tabella verdetti

| Tile | Verdetto |
|---|---|
| marquee | **USABILE COSÌ** (velocity_skew già esiste; separatore '●' colorato; opz. micro `separator_style:'dot'`) |
| hoverlist | **ESTENDERE**: lead_mode number, desc destra, name 56px+uppercase, peek_mode 'monitor' |
| section-header | **USABILE COSÌ** per sec__head (tagline_caption mono dx, baseline) |
| headline | USABILE per titoli singoli; non per sec__head split |
| animatedheading / blendtext / scrollscrub (per manifesto) | **NON ADATTE** — scrub parole allo scroll = GAP, serve estensione/tile |
| megamenu | **ESTENDERE**: show_timecode, scroll_progress hairline, logo_dot inline+colore |
| mediacta | **ESTENDERE**: cta_style 'maillink' (freccia+underline+hover gap), eyebrow mono |
| cta-banner | NON ADATTA (CTA pill, headline max 80) |
| showcasegrid/productgrid/workgrid/worklist (demo R&S) | **NON ADATTE** → nuova tile `themedemos` (mini-preview CSS via c_bg/c_ink/c_acc/c_font) |
| statstrip | **ESTENDERE** per Partner: item link_url + variant inline-row + hover |
| trust-strip / hoursstrip | NON ADATTE |
| footer (headline+text-block+nav+divider) | **USABILE COSÌ** (pattern cadence/atelier) |
| carousel/proslider/overlayslider/panelslider/scrollscrub/categoryrail (reel) | **NON ADATTE** → nuova tile `reel` (drag categoryrail + scrubbar scrollscrub + REC/skew/heights) |
| hero esistenti (15) | nessuno copre "studio editoriale" → **nuova tile `studiohero`** |

Note trasversali: grana pellicola + HUD mirino (evo-fx [1][2]) = chrome di tema, non tile (script/CSS di tema o tile "effects" separata); i layer evo-mark/evo-card sono annotazioni del blueprint, NON parte del tema da riprodurre. Ogni nuova tile PHP richiede registrazione manuale in `includes/class-olo-builder.php` (require_once + register_tile) e prima del naming va fatto `grep "class Olo_X_Tile"` su tutto wp-content/plugins dei server (gotcha collisione cross-plugin).