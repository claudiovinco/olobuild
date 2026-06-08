# Mega Menu — 40 template pronti per OLObuild

Template del tile **Mega Menu** (`type: 'megamenu'`) con le **chiavi reali** di
`src/config/elements/megamenu.js`. Due formati equivalenti:

- `megamenuTemplates.js` — sorgente ESM (helper `R`/`SP` + alias token). Importabile.
- `megamenu-templates.json` — valori già espansi (radius/spacing come oggetti). Import diretto.

## Applicare un template
```js
import { MEGAMENU_TEMPLATES } from '@/config/megamenuTemplates';
const tpl = MEGAMENU_TEMPLATES.find(t => t.id === 'cinema-amber');
Object.assign(elementSettings, structuredClone(tpl.settings)); // poi sovrascrivibile dal cliente
```

## Regole
- **Chiavi salvate invariate** (spacing `{top,right,bottom,left}`, border-radius `{tl,tr,br,bl}`, range come stringa).
- **Token-first**: brand via `var(--olo-color-*)`; hex solo per superfici "decise".
- **`frontend`**: estensione di render oltre l'inspector. Il template funziona comunque
  (impostato con le chiavi più vicine); l'estensione lo porta al 100%.

---

## Indice dei 40 template

### A · Chiari & professionali

| id | nome | preset base | n° chiavi | frontend |
|---|---|---|---|---|
| `modern-clean` | Modern Clean | `modern-clean` | 14 | — |
| `compact-app` | Compact App | `compact-bar` | 11 | — |
| `corporate-navy` | Corporate Navy | `custom` | 13 | — |
| `tech-saas` | Tech SaaS | `gradient-bar` | 9 | sì |
| `pharma-trust` | Pharma Trust | `custom` | 16 | — |
| `editorial-hairline` | Editorial Hairline | `minimal-line` | 11 | — |

### B · Scuri & cinematici

| id | nome | preset base | n° chiavi | frontend |
|---|---|---|---|---|
| `cinema-amber` | Cinema Amber | `cinema-bar` | 12 | — |
| `neon-strip` | Neon Strip | `neon-strip` | 12 | sì |
| `aurora-glass` | Aurora Glass | `glass-bar` | 14 | — |
| `news-dark-ticker` | News Dark Ticker | `magazine-bold` | 15 | — |
| `dev-docs-mono` | Dev Docs Mono | `retro-terminal` | 8 | sì |
| `midnight-lux` | Midnight Lux | `cinema-bar` | 16 | — |

### C · Editoriali & raffinati

| id | nome | preset base | n° chiavi | frontend |
|---|---|---|---|---|
| `magazine-bold` | Magazine Bold | `magazine-bold` | 20 | — |
| `minimal-line` | Minimal Line | `minimal-line` | 13 | — |
| `luxury-gold` | Luxury Gold | `custom` | 15 | — |
| `architecture` | Architecture | `custom` | 17 | — |
| `serif-display` | Serif Display | `custom` | 12 | — |
| `kraft-eco` | Kraft Eco | `sticker-tape` | 13 | — |

### D · Espressivi & pop

| id | nome | preset base | n° chiavi | frontend |
|---|---|---|---|---|
| `sticker-tape` | Sticker Tape | `sticker-tape` | 11 | sì |
| `playful-pastel` | Playful Pastel | `custom` | 12 | — |
| `color-block` | Color Block | `gradient-bar` | 13 | — |
| `festival-gradient` | Festival Gradient | `tilt-bar` | 12 | sì |
| `retro-terminal` | Retro Terminal | `retro-terminal` | 13 | — |
| `brutalist-block` | Brutalist Block | `brutalist-block` | 17 | sì |

### E · Strutture & overlay

| id | nome | preset base | n° chiavi | frontend |
|---|---|---|---|---|
| `split-nav` | Split Nav | `custom` | 7 | — |
| `stacked-center` | Stacked Center | `custom` | 8 | — |
| `glass-on-photo` | Glass on Photo | `glass-bar` | 15 | — |
| `ecommerce` | E-commerce | `custom` | 19 | — |
| `mega-panel-open` | Mega Panel Open | `modern-clean` | 11 | — |
| `search-overlay` | Search Overlay | `custom` | 8 | sì |

### F · Sperimentali & inattesi

| id | nome | preset base | n° chiavi | frontend |
|---|---|---|---|---|
| `command-palette` | Command Palette | `custom` | 7 | sì |
| `marquee-statement` | Marquee Statement | `custom` | 8 | sì |
| `editorial-index` | Editorial Index | `minimal-line` | 9 | sì |
| `departure-board` | Departure Board | `custom` | 8 | sì |
| `memphis-80s` | Memphis 80s | `sticker-tape` | 9 | sì |
| `notebook-hand` | Notebook Hand | `custom` | 10 | sì |
| `bauhaus-blocks` | Bauhaus Blocks | `custom` | 12 | sì |
| `y2k-aqua` | Y2K Aqua | `glass-bar` | 8 | sì |
| `tag-cloud` | Tag Cloud | `custom` | 6 | sì |
| `mosaic-mega` | Mosaic Mega | `modern-clean` | 11 | sì |

---

## Estensioni frontend (17/40)
Le restanti 23 sono ottenibili **interamente con le chiavi esistenti** (font particolari = `typography_preset` globale). Queste invece chiedono un'aggiunta al render:

- **Tech SaaS** (`tech-saas`) — Badge "NEW" su una voce: oggi non c'è una chiave; aggiungere supporto a un badge per item (es. da classe CSS della voce menu WP).
- **Neon Strip** (`neon-strip`) — CTA “glow”: box-shadow neon sul .olo-mm-btn (estensione stile).
- **Dev Docs Mono** (`dev-docs-mono`) — Logo con version-chip (es. v3.34) e ricerca search_style:expand da rendere come campo inline.
- **Sticker Tape** (`sticker-tape`) — Voce attiva “a pillola” piena (variante di hover_effect background con radius pill).
- **Festival Gradient** (`festival-gradient`) — Micro-rotazione della barra (transform: rotate) — concetto tilt-bar.
- **Brutalist Block** (`brutalist-block`) — Ombra hard (box-shadow pieno, senza blur) sul contenitore della barra.
- **Search Overlay** (`search-overlay`) — search_style “overlay” da rendere (campo a tutta barra + scorciatoia “/”).
- **Command Palette** (`command-palette`) — Pattern command-palette: input centrale + dropdown risultati (estensione del render ricerca). Probabilmente un nuovo search_style "command".
- **Marquee Statement** (`marquee-statement`) — Nav scorrevole (marquee): track animato in translateX + mask ai bordi.
- **Editorial Index** (`editorial-index`) — Numerazione 01-05 davanti alle voci (counter CSS o numero dal menu WP).
- **Departure Board** (`departure-board`) — Voci come celle flap: ogni .olo-mm-nav-link con sfondo scuro, riga centrale e ombre interne.
- **Memphis 80s** (`memphis-80s`) — Decori geometrici (cerchi/triangoli/stripe) attorno alla barra + ombra dura colorata: livello decorativo nel render.
- **Notebook Hand** (`notebook-hand`) — Righe orizzontali di sfondo + underline tratteggiato (handwriting via typography_preset).
- **Bauhaus Blocks** (`bauhaus-blocks`) — Marchio geometrico (cerchio+quadrato) accanto al logo.
- **Y2K Aqua** (`y2k-aqua`) — Riflessi gel: inset highlight su barra e CTA (box-shadow inset).
- **Tag Cloud** (`tag-cloud`) — Dimensione per-voce variabile + alcune voci “a pillola” (font-size e bg per item, da classe della voce menu WP).
- **Mosaic Mega** (`mosaic-mega`) — Tipo-colonna “immagini/promo” nel megapanel-map (griglia di thumbnail + card in evidenza). È l'implementazione #5 del deep-dive.

## Prossimo passo lato OLObuild
1. Esporre i template nel selettore preset/template del Mega Menu.
2. Cablare `preset` (`megamenuPresets.js`) così i 12 preset ufficiali applicano la ricetta.
3. Estensioni `frontend` per priorità: ricerca **command/overlay**, **colonna immagini/promo** del pannello, **badge per-voce**, poi gli effetti decorativi.
