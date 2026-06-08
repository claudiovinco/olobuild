# PLAYBOOK — Ricomposizione OLOtheme TILE-PURE (per agenti)

Ricomponi **un** tema OLObuild fedele al blueprint, usando **solo tile vere** (zero HTML
strutturale inline). Output = una cartella `assets/data/themes/<slug>/` con
`homepage.json` + `theme.json` + `header.json` + `footer.json` + loghi, generata da uno
script `.cjs` che usa il KIT condiviso.

## Regole ferree
1. **TILE-PURE**: `homepage.json` deve avere **0 `text-block`** (lo script lo verifica e lo
   stampa). Ogni sezione = una tile vera. (Header/footer li genera il kit, non toccarli.)
2. **IMAGE-FREE** (come il canonico Ledger): NON usare il tile `image` né foto. Dove il
   blueprint ha un "media/placeholder", rendi quel lato con un **pannello astratto**
   (showcase di `hero-split`, oppure ometti il media e centra il contenuto). Niente URL esterni.
3. **Palette esatta** dal `:root` del CSS del tema (variabili `--bg/--accent/--txt/...`).
   Colori = hex espliciti nei settings dei tile. Niente colori inventati.
4. **Font del tema**: leggi `@import ... family=` nel CSS. Nei tile a titolo **serif** usa
   `headline_font_family:'serif'` (eredita il display font); a titolo **sans** usa `'sans-serif'`.
5. **Copy esatta** dal blueprint HTML (titoli, sottotitoli, voci, prezzi, citazioni).
6. **Icone solo-Lucide** (rese inline SVG): `check, arrow-right, book, shield-check,
   trending-up, calculator, wallet, coins, percent, star, heart, leaf, zap, clock, map-pin,
   coffee, sparkles, scissors, palette, camera, music, code, terminal, cpu, database, globe,
   compass, mountain, waves, sun, flame, gift, award, users, calendar, phone, mail`.
   Evita nomi presenti anche in UIkit (`file-text, menu, search`) → render JS-dipendente.
7. **NON** bumpare versione, **NON** deployare, **NON** creare harness/preview. Solo i file + run.
8. **Nomi-tipo ESATTI** (= basename in `src/config/elements/`). Trappole comuni: il tile lista
   è **`iconlist`** (NON `list-tile`/`list-icons`); titolo sezione **`section-header`**; CTA
   **`cta-banner`**; striscia loghi **`trust-strip`**. Se inventi un tipo inesistente, lo script
   passa ma il tile **non renderizza**. In dubbio: verifica che `src/config/elements/<type>.js` esista.

## Come si usa il KIT (`tmp_theme_kit.cjs`)
Copia la struttura di **`tmp_gen_capital-row.cjs`** (esempio canonico completo: hero, logo-cloud,
stat-strip, thesis, portfolio, process-steps, quote, projector, cta) e/o **`tmp_gen_ledger_v2.cjs`**.
Scheletro minimo:
```js
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('xx'); // 'xx' = prefisso corto del tema
const home = [ /* sezioni: sec(bgColor,'large'|'small',[ row([ col('1-1',[ tile('...',{...}) ]) ]) ]) */ ];
K.emit({ slug, name, tags, description, colors:{...}, css_disp, css_sans, heading_weight,
         heading_line_height, google_fonts:[...], logo_variant:'light'|'dark',
         menu:[{title,url}], header:{bg,text_color,sticky_bg,logo_width},
         footer:{bg,headColor,brand:{name,tagline},columns:[{title,links:[]}],bottom:{left,right}},
         cursor:{blend_mode:'exclusion',ring_color,dot_color} /* o cursor:false per temi chiari */ }, home);
```
- `sec(color,padding,children,extra)` → padding `'large'` (sezioni) o `'small'` (strisce); alterna
  `bg`/`bg-2` del CSS per ritmo. `R(n)` = border-radius 4 angoli linkati.
- `col` width: `'1-1'`, `'1-2'`, `'1-3'`, `'1-4'`, `'2-5'`, `'3-5'`, `'2-3'`. Row `settings:{gap:NN, vertical_align:'stretch'}`.
- `K.emit(...)` scrive i 4 file + copia loghi + stampa diagnostica. `logo_variant:'light'` = logo bianco (temi scuri), `'dark'` = colorato (temi chiari).

## theme.json colors (il kit mappa alle chiavi ESATTE — passa questi in `colors`)
`primary`(accento) · `primary_contrast`(testo su accento, spesso `#fff` o ink) · `secondary` ·
`secondary_contrast` · `muted`(=bg-2/surface-alt) · `muted_contrast` · `text` · `text_muted`(dim) ·
`background`(**sfondo pagina!**) · `border`(=line) · `link`. NON inventare altre chiavi.

## MAPPATURA pattern blueprint → tile (verificata)
| Blueprint (`data-olo-tile` / sezione) | Tile OLObuild | Note chiave |
|---|---|---|
| Hero / statement + pannello | `hero-split` | `headline_lines[]` (1 colore/riga), `subhead`, `cta1/cta2`, `showcase_*` per il pannello laterale (badge + `showcase_items[{number:ETICHETTA, text:VALORE, text_color, bg}]`). `split_ratio` accetta valori liberi (es. `'1.25fr .75fr'`). |
| Logo cloud / clienti | `trust-strip` (`variant:'pill'`) | caption sopra = `section-header` solo-headline piccola (vedi helper `caption()` in capital-row). |
| Stat strip / numeri | `counter` ×N in row | **`text_color`=numero**, **`number_color`=suffisso/unità**, `prefix`, `suffix`, `label`, `number_font_size`, `bg_color:'transparent'`. |
| Titolo sezione | `section-header` | `eyebrow_*`, `headline_lines[]`, `headline_inline:true` (1 riga + accento inline), `layout:'center'|'stack'`, `tagline_show/tagline_text` per il paragrafo intro. |
| Feature/thesis split (testo+lista+media) | `section-header` + `info-cards` o `iconlist` | image-free: header centrato + 3 `info-cards` (icona `check`) **oppure** `iconlist`. |
| Card servizi/feature | `info-cards` | icona-quadrato: `show_icon`+`icon_color`+`icon_bg_color`; titolo `title_color`; `card_bg`,`card_radius`,`card_padding`,`columns`,`card_hover_effect:'lift'`. |
| Step / processo numerato | `info-cards` | `show_counter`+`counter_shape:'plain'|'circle'`+`counter_color`(+`counter_bg` se circle)+`counter_size`. |
| Portfolio/company cards | `info-cards` | monogramma=`counter`+`counter_shape:'square'`+`counter_bg`; badge=`counter_label`+`show_counter_label`; footer=`show_footer`+`footer_text`+`footer_dot_color`. |
| Prezzi | `pricing` ×N in row `{gap:32,vertical_align:'stretch'}` | `plan_name,price,currency,period,features('\n'-separati),is_popular,badge_text,badge_bg_color,bg_color,price_color,accent_color,cta_*,border_radius`. |
| Citazione / testimonial | `testimonial` | `quote` con **una coppia** “ ”, `author_name`,`author_role`,`rating:'0'`,`layout:'single'`,`bg_color:'transparent'`,`text_color`. |
| CTA finale | `cta-banner` (`layout:'stack'`) | `headline`+`headline_accent`(+`_italic`),`subtitle`,`cta_text/url`,`bg:{type,color}`,`accent_color`,`cta_bg/color`,`banner_radius`,`banner_padding`. |
| Slider/calcolatore (zona "Projector") | `projector` | `eyebrow,heading(<em> ok),intro,min,max,step,value,rate,years,currency,input_label,out_caption,note,show_contrib,zone_accent`. `rate:'0'`=lineare. |
| Marquee / ticker | `marquee` o `newsticker` | leggi config se serve. |
| Accordion / FAQ | `accordion` | leggi config se serve. |
| Slider contenuti / gallery astratta | `projector`/`info-cards`/`counter` | image-free: preferisci pannelli astratti. |
| Zona "Finder/Builder/Mixer" (non esiste tile) | approssima con tile statico | Finder→`info-cards` di opzioni; Builder→`pricing`/`list`; Mixer→`info-cards`/swatch. Segnala nel report. |

> Se un pattern non è coperto qui, **leggi `src/config/elements/<type>.js`** per i campi esatti
> di quel tile (i `defaults` + `fields`/`styleFields`). Alcuni campi "potenziati"
> (`title_color`, `counter_color`, `counter_shape`, `counter_bg`, `icon_bg_color`,
> `headline_inline`, `showcase_badge_color`) **non sono nell'inspector ma sono resi dal PHP** —
> sono usati negli esempi e funzionano: fidati degli esempi.

## Procedura
1. Leggi il CSS del tema (`OLOthemes_reference/olothemes/<css>`): estrai `:root` (palette) + `@import` (font).
2. Leggi l'HTML home del tema: estrai ordine sezioni, `data-olo-tile`, e **tutta la copy**.
3. Scrivi `tmp_gen_<slug>.cjs` (copia capital-row, adatta palette/font/sezioni/copy).
4. Esegui: `node tmp_gen_<slug>.cjs` dalla root `D:/TECNICA/olobuild`. Deve stampare `text-block 0 ✓`.
   Se stampa text-block > 0, correggi (hai usato `text-block`: sostituiscilo con un tile vero).
5. **Gotcha .cjs**: apostrofi dentro stringhe single-quote rompono → usa **backtick** per stringhe
   con apostrofi/virgolette curve. Mai `&&` dentro stringhe di contenuto.
6. Report finale (testo): slug, n. sezioni, riga diagnostica, mappatura sezione→tile, e qualsiasi
   pattern approssimato o tile mancante.

Esempi canonici da imitare: `tmp_gen_capital-row.cjs` (completo) e `tmp_gen_ledger_v2.cjs`.
