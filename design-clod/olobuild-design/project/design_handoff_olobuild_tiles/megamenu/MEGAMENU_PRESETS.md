# Mega Menu — preset & 30 variazioni (handoff per Claude Code)

Pacchetto per rendere il tile **Mega Menu** (`type: 'megamenu'`) coerente, bello e
finalmente *governabile dai preset*. Fonti lette: `src/config/elements/megamenu.js`
(~130 chiavi) e `src/components/Tiles/MegaMenuTile.vue` (render).

Riferimenti visivi nel progetto design:
- `OLObuild - Mega Menu deep-dive.html` — anatomia, finding, 6 preset, tabella 12, fix.
- `OLObuild - Mega Menu 30 variazioni.html` + `megamenu-gallery.js` — **30 header live**.

---

## 1. Il problema (priorità ALTA)
`megamenu.js` espone `preset` con **12 valori** (`modern-clean` … `tilt-bar`), ma
`MegaMenuTile.vue` **non legge mai `s.preset`**: cambiare preset non sposta un pixel.

**Soluzione** → `megamenuPresets.js` (in questa cartella): ogni preset è un **bundle di
chiavi già esistenti**. Da cablare nello store al cambio di `settings.preset`:

```js
import { MEGAMENU_PRESETS } from '@/config/megamenuPresets';
function applyMegamenuPreset(settings, id){
  const r = MEGAMENU_PRESETS[id];
  if (r) Object.assign(settings, structuredClone(r)); // valori sovrascrivibili
}
```

Vincoli: **chiavi salvate invariate**, **token-first** (hex grezzi solo per superfici
scure "decise"). `'custom'` resta assente = non tocca nulla.

---

## 2. Altri interventi necessari (dal deep-dive)

| # | Intervento | Severità | Nota |
|---|------------|----------|------|
| 2 | **`mega_mode`: contratto config↔componente** | bug | config offre `auto`/`css-class`; il componente controlla `'all'` (inesistente) e **non gestisce `css-class`**. Allineare: `auto` (figli) + `css-class` (`item.classes.includes('mega-menu')`). |
| 3 | **Default hardcoded → token** | coerenza | `topbar_bg:'#1F2937'`, `topbar_text_color:'#9CA3AF'`, `mobile_bg:'#1e1e2e'`, `topbar_right_cta_color:'#FFFFFF'` → legare a ruoli cliente (dark/light/text) con `resolveColor`. |
| 4 | **IA inspector (130 campi)** | UX | preset in cima; gruppi collassabili per frequenza (Brand&voci → Navbar → Pannello → Top bar → Mobile → Avanzate); badge "modificato" sui gruppi sovrascritti. |
| 5 | **Colonna promo/immagine nel pannello** | feature | oggi il `megapanel-map` rende solo colonne di link; aggiungere tipo-colonna `promo` (immagine + titolo + CTA) con placeholder elegante. |
| 6 | **Ricerca `expand`/`overlay`** | rifinitura | `search_style:'expand'` esiste nei default ma l'anteprima mostra solo l'icona statica; rendere icona→campo inline e overlay a barra. |

---

## 3. Le 30 variazioni (estensione dei preset)

`megamenu-gallery.js` contiene 40 ricette in 6 famiglie. Sono **quasi tutte ottenibili con le
chiavi esistenti** (sono "preset estesi"); la famiglia F spinge oltre e alcune voci
(`+frontend`) richiedono un filo di CSS aggiuntivo nel render del componente.

- **A · Chiari & professionali** — Modern Clean, Compact App, Corporate Navy, Tech SaaS, Pharma Trust, Editorial Hairline
- **B · Scuri & cinematici** — Cinema Amber, Neon Strip, Aurora Glass, News Dark Ticker, Dev Docs Mono, Midnight Lux
- **C · Editoriali & raffinati** — Magazine Bold, Minimal Line, Luxury Gold, Architecture, Serif Display, Kraft Eco
- **D · Espressivi & pop** — Sticker Tape, Playful Pastel, Color Block, Festival Gradient, Retro Terminal, Brutalist Block
- **E · Strutture & overlay** — Split Nav (`logo_position:split`), Stacked Center (`stacked`), Glass on Photo (`header_mode:overlay`), E-commerce (topbar+cart), Mega Panel Open (pannello dal vivo), Search Overlay (`search_style`)
- **F · Sperimentali & inattesi** — Command Palette (menu-as-search), Marquee Statement (nav scorrevole), Editorial Index (voci numerate), Departure Board (split-flap), Memphis 80s, Notebook Hand (Caveat), Bauhaus Blocks, Y2K Aqua, Tag Cloud, Mosaic Mega (pannello immagini)

La famiglia **F** è il laboratorio: dimostra fin dove può arrivare la tile reinterpretando
le stesse zone (ricerca → command palette, ticker → nav marquee, pannello → mosaico di
immagini). Le voci `+frontend` indicano dove servirebbe estendere il render — utile per
decidere quali capacità vale la pena portare nel componente.

---

## 4. Mappa anatomica (per orientarsi nel componente)

```
[ TOP BAR ]   topbar_* (ticker | testo | hamburger · social · CTA · search · cart)
[ NAV BAR ]   logo (logo_position: left|center|right|stacked|split)
              · nav items (hover_effect ×14, active_color)
              · tools (search_* · social_* · button_mode/CTA)
[ MEGA PANEL ] panel_* (colonne da menu WP · panel_open_animation ×8 · promo*)
[ MOBILE ]    mobile_* (offcanvas|dropdown|fullscreen · hamburger_style ×10 · stagger)
[ STICKY ]    sticky_* (show_on_up · shrink · bg/shadow)
```

Definition of Done per il Mega Menu: preset cablati e visibilmente diversi; `mega_mode`
allineato; default top bar/mobile a token; pannello con opzione promo; ricerca expand resa;
inspector raggruppato. Nessuna chiave salvata rinominata o rimossa.
