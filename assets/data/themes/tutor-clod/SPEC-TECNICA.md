# SPEC TECNICA — Template "OLOtutor Vetrina" per tutor.clod.eu

Pacchetto tema importabile OLObuild. Cinque pagine + header + footer, composti **solo** con tile
native del plugin (`src/config/elements/`). Riferimento pixel: **desktop 1280**.

Sorgente testi: `uploads/vetrina-testi.md` — **verbatim, non riscritti**.
Sorgente visivo: Olo Tutor Design System (Work Sans + Playfair Display, verde #3fa23f, navy #0f172a).

---

## 1. File del pacchetto

```
assets/data/themes/tutor-clod/
├─ theme.json            id: tutor-clod  (= nome cartella, obbligatorio)
├─ header.json           megamenu + scrollprogress
├─ footer.json           logo + testo proprietà + 2 colonne nav + divider
├─ home.json             pagina d'ingresso
├─ in-aula.json          L'ora di lezione
├─ lo-studio.json        I giorni fra le lezioni
├─ la-scuola.json        Una scuola intera
├─ come-si-prova.json    Come si prova
├─ logo.png              → copiato in uploads/olobuild-logo.png
├─ logo-light.png        → copiato in uploads/olobuild-logo-light.png (versione bianca per il footer navy)
└─ screenshot.jpg        card del modale "Importa Temi" (1280×800)
```

I template JSON sono **array di sezioni**; ogni nodo ha `{id, type, settings, style, advanced, children}`
con gerarchia fissa **section → row → column → tile**. Gli `id` vengono rigenerati all'import
(`regenerate_ids()`), quelli nel pacchetto servono solo alla leggibilità.

Placeholder risolti dall'importer:
- `LOGO_PLACEHOLDER` → URL del logo caricato (header, footer);
- `"menu_id":"auto"` → id del menu WP creato da `theme.json.menu` (scritto **senza spazi**, come richiede la regex dell'importer).

---

## 2. Token — nessun hex nei template

`theme.json → styles.colors` (ruoli core, emessi come `--olo-color-*` dentro `.olo-template`):

| ruolo | valore | uso nel template |
|---|---|---|
| primary | `#3fa23f` | CTA, link, accenti, indicatori, numeri step/pila |
| primary_contrast | `#ffffff` | testo sui CTA |
| secondary | `#0f172a` | band navy (scena due, footer, citazione) |
| secondary_contrast | `#ffffff` | testo sul navy |
| muted | `#f8fafc` | fondo hero, page title bar, band alterne |
| muted_contrast | `#475569` | — |
| text | `#1e293b` | titoli e corpo |
| text_muted | `#64748b` | lead, descrizioni |
| background | `#ffffff` | fondo pagina |
| border | `#e2e8f0` | bordi card, righe tabella, bordi nastro |
| link | `#2d722d` | link inline |

Alias disponibili di serie: `--olo-color-surface`, `-surface-alt`, `-text-soft`, `-text-faint`, `-on-primary`.
Le tinte intermedie sono scritte come `color-mix(in srgb, var(--olo-color-…) N%, …)` — **zero hex nuovi**.

⚠️ **Gotcha da verificare al primo import**: `import_theme` scrive `olo_styles` ma **non** sincronizza
`olo_global_colors`, che vince sui ruoli core. Se tutor.clod.eu ha già una palette globale con id
`primary/secondary/text/…`, riallineare dal pannello Palette (o PUT `/global-colors`) dopo l'import,
altrimenti i colori del tema non si vedono.

## 3. Tipografia (1280)

| ruolo | famiglia | px | peso | line-height |
|---|---|---|---|---|
| H1 pagina | Playfair Display | 56 | 700 | 1.06 |
| H2 sezione | Playfair Display | 40 | 700 | 1.12 |
| H3 scena / card | Playfair Display | 44 / 28 | 700 | 1.10 |
| Titolo card (iconbox, stack) | Work Sans | 22 / 19 / 18 | 600 | 1.3 |
| Lead | Work Sans | 18 | 400 | 1.6 |
| Corpo | Work Sans | 17 | 400 | 1.62 |
| Occhiello (eyebrow) | Work Sans | 12 | 600 | 1.4 · uppercase, tracking .09em |
| Didascalia | Work Sans | 14 | 400 | 1.65 · colore text-faint |
| Nastro | Playfair Display | 22 | 600 | — |

Tablet/mobile: `font_size_h1_tablet 42`, `h1_mobile 32` (già in `theme.json`).

⚠️ Il font-host self-hosta i pesi `300;400;500;600;700`. Qui non serve niente sopra 700.

## 4. Ritmo verticale (1280)

- Sezione standard: `padding: "custom"`, **110–120px** sopra/sotto.
- Sezione con band colorata: 100px.
- Larghezze: hero e griglie `width: "large"` (max 1400); testi lunghi `"default"` (1200); manifesto e citazioni `"small"` (900); nastro e header `"fullbleed"` + `padding: "remove-vertical"`.
- Gap griglie: **20px** (card), 24px (fila attività), 48px (split hero/scene).
- Radius: card 14, band/CTA 24, contatori 12, bottoni 8.
- Ombra card: `sm`; pila: custom `0 8px 32px rgba(15,23,42,.08)`.

---

## 5. Mappa blocco → tile

### header.json
| blocco | tile | chiavi che contano |
|---|---|---|
| barra | `megamenu` | `menu_id:"auto"`, `header_mode:"classic"`, `sticky:true`, `sticky_bg` color-mix 88% del background, `logo_image:"LOGO_PLACEHOLDER"`, `logo_width:130`, `extra_link_1_*` = CTA "Contatta per una demo", `mobile_style:"fullscreen"` |
| barra di avanzamento | `scrollprogress` | `position:"top"`, `bar_height:"2"`, `bar_color` = primary, `z_index:9999` |

### footer.json
| blocco | tile | chiavi che contano |
|---|---|---|
| logo | `image` | `image_url: "/wp-content/uploads/olobuild-logo-light.png"` (la **variante bianca**, copiata lì dall'importer: `LOGO_PLACEHOLDER` risolve solo al logo scuro, invisibile sul navy), `image_width/max_width: "140px"`, `height:"auto"`, `object_fit:"contain"`, `image_alignment:"left"`, `link_url:"/"`, `alt_text:"OLOtutor"`. ⚠️ Se il sito usa un percorso uploads non standard, correggere l'URL dopo l'import. |
| proprietà dei contenuti | `text-block` | 14/1.65, colore `color-mix(secondary_contrast 60%)`, max 420 |
| due colonne link | `headline` (12/600 uppercase) + `nav` verticale | link `color-mix(secondary_contrast 65%)`, hover pieno |
| chiusura | `divider` + `text-block` | divider 1px `color-mix(secondary_contrast 8%)`, spacing 24; riga finale 13px |

### home.json
| blocco | tile | note |
|---|---|---|
| Hero | `text-block` (occhiello) + `animatedheading` + `text-block` (lead) + 2 × `button` + `content` (mockup) | `animatedheading.animation:"rotating"`, `animated_words: studia/ricorda/ripassa`, `font_size:56` |
| Onda | `shapedivider` | `shape:"wave"`, `height:80`, `color` = background, tablet 60 / mobile 40 |
| Manifesto | `scrubtext` | `scroll_reveal:true`, `dim_opacity:14`, `size_min 26 / size_max 46`, `max_width_ch:28`; gli `<em>` sono le parole accento |
| Due momenti | 2 × `section` con `sticky_effect:"cover"`, `sticky_top:64` | scena 1 su background, scena 2 su navy: la seconda **sale sopra** la prima |
| Contatori | 4 × `counter` in row `25-25-25-25` | `number_font_size:46`, `media_bg` = muted, radius 12, nessun bordo colorato |
| Nastro metodi | `marquee` | `speed:40`, `pause_hover:true`, `full_width:true`, `height:84`, bordi 1px `border` |
| Invito | `cta-banner` | `layout:"split-2"`, `banner_padding:56`, fondo `color-mix(primary 12%, #fff)` |

### in-aula.json
| blocco | tile | note |
|---|---|---|
| Titolo | `pagetitlebar` | il **titolo è il titolo della pagina WP** (`L'ora di lezione`); qui si imposta solo `subtitle`, `bg_color` = muted, `bg_parallax:true`, `min_height:300`, padding 140/80 |
| Innesto | 2 × `text-block` | 20px, larghezza sezione `small` |
| Attività | `section` `sticky_effect:"cover-h"` + fila di 5 `iconbox` (row custom 20,20,20,20,20) | la quinta card è navy |
| Peer Instruction | `step-timeline` | `columns:4`, `show_timeline:true`, `timeline_height:1`, `counter_size:56`, mockup nascosti (`show_media_label:false`) |
| Cartoncini | `text-block` | 19px |
| Dove | 3 × `iconbox` | icone Lucide `monitor`, `building-2`, `map-pin`, box icona 14% primary |
| Passaggio | `menuanchor` + `headline` + `button` | ancora `campanella`, offset 64 |

### lo-studio.json
| blocco | tile | note |
|---|---|---|
| Titolo | `pagetitlebar` | come sopra |
| Pila | `stackscroll` | `top_offset:100`, `top_step:20`, `media_position:"none"`, `show_number:true`, 4 card, la quarta navy |
| Certezza / esito | 4 × `flipcard` | `flip_trigger:"hover"`, `card_height:230`; **card 2 e 3 hanno il testo già sul fronte** (equivalente a "mostrate dal retro": olobuild non ha un flag "parti girata") |
| Citazione | `quotation` preset `big-mark` + `text-block` | |
| Chi ha misurato | `table` | `has_header:false`, `first_col_bold:true`, preset `minimal-line`, `responsive_mode:"stack"` |

### la-scuola.json
| blocco | tile | note |
|---|---|---|
| Titolo | `pagetitlebar` | |
| Linguette | `switcher` | `preset:"underline-animated"`, `indicator_type:"underline"`, 4 schede (Registro / Presenze a ore / Famiglie / Tutor) |
| Sistema | 4 × `iconbox` | `calendar`, `copy`, `message-square`, `bar-chart-3` |
| Standard | 3 × `counter` + `text-block` badge | band muted |
| Proprietà | `headline` + `text-block` | |

### come-si-prova.json
| blocco | tile | note |
|---|---|---|
| Titolo | `pagetitlebar` | |
| Tre passi | `step-timeline` | `columns:3`, `counter_size:64`, titolo Playfair 28 |
| Punto preciso | `section` navy + `quotation` `big-mark` | |
| Riferimenti | `table` 10 righe | `first_col_bold:true`, senza header |
| Invito | `cta-banner` | CTA `Scrivici` (mailto) + CTA2 `Scarica la nota di sintesi` |

---

## 6. Motion — cosa resta e cosa cade

Tenuti (nativi in olobuild):
- **scrollprogress** in header;
- **parallasse sfondo** del `pagetitlebar` (`bg_parallax`);
- **sticky cover** fra le due scene della home (`section.sticky_effect:"cover"`);
- **scrub testo** del manifesto (`scrubtext.scroll_reveal`);
- **pila di card** (`stackscroll`);
- **flip** delle card certezza/esito (`flipcard`, trigger hover);
- **parola che ruota** nel titolo (`animatedheading.animation:"rotating"`);
- **nastro** con pausa al passaggio (`marquee.pause_hover`).

Caduti o sostituiti — da confermare:
1. **Fila orizzontale con pin verticale** (In aula). Reso con `section.sticky_effect:"cover-h"` + fila di 5 card. Se l'effetto non è il pin desiderato, l'alternativa nativa è un `carousel` o un `marquee` di card; da decidere guardando il render.
2. **Timeline verticale alternata** (Come si prova). `step-timeline` è orizzontale: resa a `columns:3`. Per la verticale alternata serve `columns:1` (impilata, non alternata) oppure una nuova variante della tile.
3. **Ingresso a scaletta 180 ms** dei contatori. `counter` non espone lo stagger; disponibile via le animazioni di ingresso con stagger figli sulla `row` (da impostare nel builder, non nel JSON).
4. **Sondaggio a due giri giocabile** (prototipo Design Component). Non c'è una tile che lo faccia: serve la tile live-poll del portale docente o una tile nuova. Nel template il blocco non c'è.
5. **Due carte "mostrate dal retro"**: reso spostando il testo sul fronte (vedi sopra).

## 7. Import

1. Copiare la cartella in `assets/data/themes/tutor-clod/` (con `logo.png`, `logo-light.png`, `screenshot.jpg`).
2. Builder → **Importa Temi** → OLOtutor — Vetrina, oppure REST `Olo_Rest_Api::import_theme`.
3. L'importer: carica i loghi → crea il menu → crea i 7 template → attiva header/footer → fonde `styles` in `olo_styles` → crea/riusa le 5 pagine (`home` diventa la front page).
4. Riallineare `olo_global_colors` (§2) e verificare i font (Work Sans + Playfair self-hostati).
5. Caricare le due immagini segnaposto della home: screenshot portale docente (340px di altezza) e foto d'aula.

⚠️ **Durante l'iterazione non re-importare**: ogni import crea template nuovi e orfana i precedenti.
Aggiornare in-place con `(new Olo_Database())->update_template(...)`.

## 8. Checklist pixel a 1280

- [ ] Header sticky alto 64px, logo 130px, CTA pill radius 8.
- [ ] H1 56/1.06, occhiello 12 uppercase tracking .09em.
- [ ] Hero: colonne 54/46, gap 48, padding sopra 120.
- [ ] Contatori: 4 colonne, gap 20, numero 46, card radius 12 **senza bordo colorato**.
- [ ] Nastro: altezza 84, bordi 1px #e2e8f0, pausa al passaggio.
- [ ] Band navy: contrasto testo ≥ 4.5:1 (bianco su #0f172a = 15.9:1).
- [ ] Nessun gradiente decorativo, nessuna emoji.
