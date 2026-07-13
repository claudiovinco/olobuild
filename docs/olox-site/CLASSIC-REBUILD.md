# OLOX → tile classiche: piano di conversione (2026-07-13)

**Obiettivo** (decisione utente): le 13 pagine del sito olotheme.com replicate su mosaic
devono essere costruite con **tile classiche olobuild** — "costruisco il sito con
olobuild" dev'essere vero alla lettera. Tile dedicate SOLO per ciò che il builder
non può coprire per natura (minigiochi/scene interattive). Ogni estensione va fatta
al **motore generico** (feature riusabile da qualsiasi sito), mai come hack olox.

**Strategia di sviluppo**: la versione OLOX attuale (v1.4.328-333, pixel-perfect
verificata) resta il riferimento live su mosaic. La versione classica si sviluppa su
**template/slug separati** (suffisso `-c`, es. `/olotheme-experience-c/`); lo switch
delle pagine canoniche avviene solo dopo verifica screenshot appaiati vs
olotheme.com. Rigenerazione: `build-olox-templates.mjs` (la versione WIP nel working
tree ha GIÀ la home convertita).

## Fondamenta globali (zero custom, tutto builder)

| Cosa | Meccanismo olobuild |
|---|---|
| Font Fraunces / Inter / JetBrains Mono | Ruoli globali `typography.font_family_heading/font_family/font_family_mono` (class-style-system.php) → var `--olo-font-family*`, self-host automatico |
| Palette (ink #0C0E13, paper #FAF7F2, 6 colori prodotto) | GlobalColorsPanel → token `--olo-color-*` (option `olo_styles`, merge con `array_replace` per blocco!) |
| Scorrimento orizzontale home | Sezioni native `sticky_effect:'cover-h'` (raggruppamento automatico per adiacenza, move-not-clone) |
| Barra progresso scroll | tile `scrollprogress` (position bottom, colorabile) |

## Mappatura blocchi → tile classiche

### Home (WIP già impostato nel build script)
| Blocco design | Tile |
|---|---|
| Fermate (8 sezioni orizzontali) | sezioni `cover-h` + row 50-50 |
| Kicker mono | `badge` (variant outline, typography mono) |
| Titolo serif con em colorato | `headline` |
| Sottotitolo / testi | `text-block` |
| Tag pills | `badge` ×N |
| CTA | `button` |
| Marquee intro | `marquee` (separatore ●, border_top) |
| Progress bar | `scrollprogress` |
| Minigiochi (7 scene) | `oloxscene` (DEDICATA, concessa) |
| Pallini fermate | **GAP → nuova tile generica** (vedi sotto) |
| Halo colore per fermata | **GAP → orchestrazione** (vedi sotto) |
| Hint "scrolla in basso" | `text-block` |

### Pagine prodotto (6) — oggi tutte OLOX
| Tile OLOX | Sostituzione classica |
|---|---|
| `oloxhero` (testo) | `badge` + `headline` + `text-block` + `badge`×N + `button`×2 |
| `oloxhero` (scena wall/clock/console/term/porthole/medal) | tile scena DEDICATA (concessa: showcase interattivo) |
| `oloxmarquee` | `marquee` |
| `oloxcards` (brick/ticket/red/room/hs/dcard) | `info-cards` (card_border, kicker counter mono, hover glow) / `flipcard` dove serve flip |
| `oloxsticky` (assembler/day) | `stackscroll` (impilamento) / `scrollscrub` (attraversamento) |
| `oloxpricing` (Free/Pro + gru) | 2× `pricing` affiancate (preset dark-luxury); gru = deco della scena dedicata |
| `oloxstatement` (counter) | `counter`×N in riga (anim) o `statstrip` (multi, mono ma statico) — scegliere per resa |
| `oloxstatement` (zerozero/plain) | `headline` + `text-block` |
| `oloxstatement` (stamp) | `quotation` (preset serif) — **timbro = GAP**, valutare estensione `quotation` con campo seal |
| `oloxlist` (flip/url) | `hoverlist` (name grande, hover_indent, peek) / `list` |
| `oloxlessons` (percorso con stati) | `timeline` SUPER (tl_line scroll, stati auto) o `progresstracker`; stato "bloccato" per-item = eventuale piccola estensione |
| `oloxquiz` (quiz XP) | DEDICATA (minigioco, concessa) |
| `oloxbanner` (follow/next) | `cta-banner` |
| `oloxpagefx` (scan/pano/xp) | `particlefx` scope page dove basta; scanline/xp = DEDICATA fx (concessa) |

### Manuali (6) — oggi `oloxmanual`
| Blocco | Tile |
|---|---|
| TOC laterale sticky scrollspy | `toc` (sticky, highlight_active, preset sticky-rail) ✅ |
| Capitoli | `headline` + `text-block` + `code` + `table` |
| Spec tecnica | `table` / `desclist` |
| Pill "← scheda prodotto" | `button` nel template header (già AUTO) |

### Header (template #390) e Footer (#389)
| Blocco | Tile |
|---|---|
| Logo | `sitelogo` |
| Menu prodotti mono con attiva colorata | `navmenu` (typography mono, active_color, pointer) |
| Lingue IT EN FR DE ES | `langswitcher` (style codes, compact, inline) |
| Pill "← il viaggio" | `navmenu` button_items o `button` |
| **Pallini fermate** | **GAP → nuova tile generica** |
| Footer: logo + link + fine | `sitelogo` + `list`/`iconlist` + `text-block` |
| **Credits fissi bottom viewport** | **GAP → nuova tile generica** |

## GAP: cosa va creato/esteso (tutto GENERICO, riusabile)

1. **Estensione motore `cover-h`** (class-frontend-renderer.php::initHGroups):
   esporre lo stato — CSS var `--olo-hp` (progresso gruppo 0..1) e `--olo-pp`
   per-sezione, `data-olo-active` sul gruppo, CustomEvent `olo:hgroup`
   `{index,count,progress,el}`. Serve a pallini, halo, testi; utile a chiunque.
2. **Tile `coverdots`** (nome da confermare): pallini di navigazione per i gruppi
   cover-h della pagina — AUTO-discovery del gruppo, un pallino per sezione
   (label/colore da settings di sezione o items), attivo via `olo:hgroup`,
   click→scroll instant. In un template header si nasconde da sola se la pagina
   non ha gruppi. Sostituisce l'overlay CSS del chrome rail (v1.4.331-332).
3. **Tile `bottombar`**: barra fissa in fondo alla viewport (testo/link HTML,
   colori, altezza, z-index) — per i credits sempre visibili; caso d'uso comune.
4. **Halo dinamico**: orchestrazione colore-per-sezione-attiva. Preferenza:
   estendere il background `glow` di sezione con modalità "segui sezione attiva
   del gruppo cover-h" oppure tile decoratrice zero-dimensione che interpola
   `glow_color` sull'evento `olo:hgroup`. Decidere in implementazione.
5. **Scene showcase prodotto** (wall/clock/console/term/porthole/medal):
   restano dedicate (estendere `oloxscene` con le 6 scene hero o tile gemella).
6. Eventuali micro-estensioni se la resa lo richiede: campo seal su `quotation`,
   stato per-item su `timeline`/`progresstracker`, label mono su `counter`.

## Fasi
1. ✅ Mappatura (questo documento)
2. Estensione cover-h + tile `coverdots` + `bottombar`
3. Home classica pixel-perfect su `/olotheme-experience-c/`
4. Prototipo `/olobuild-c/` → replica sulle altre 5 prodotto
5. Manuali (prototipo + replica)
6. Header/footer classici + switch canonico + pulizia

Regole sempre valide: regoletiles1 (token, SPACE/RADIUS, focus-visible, niente
emoji), chiavi salvate invariate, build + bump OLOBUILD_VERSION, verifica
screenshot appaiati vs live PRIMA di dichiarare "fatto".
