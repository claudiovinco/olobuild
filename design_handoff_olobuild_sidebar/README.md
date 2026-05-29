# Handoff: OLObuild — Colonna laterale (Elementi + Struttura)

## Overview
Redesign della colonna laterale sinistra dell'editor **OLObuild** (page builder WordPress). Sostituisce l'attuale lista densa di ~97 elementi con una **libreria a tab verticali** (rail icone + pannello card) e un **albero della struttura** coerente nello stesso linguaggio.

Due viste, una sola colonna, switch via tabs in alto:
- **Elementi** (artboard `02 · Libreria a tab verticali`) — categorie nella rail laterale, griglia 2 colonne di card draggabili nel pannello.
- **Struttura** (artboard `02b · Struttura`) — rail con filtri di vista (Tutto / Header / Body / Footer / Avvisi / Globali), albero a 4 livelli con guide tratteggiate, breadcrumb sticky in fondo.

## About the Design Files
Il bundle è composto da **prototipi HTML/React** — un riferimento di look & behavior, **non codice di produzione da copiare**. Vanno **ricreati nell'ambiente del codebase OLObuild target** (verosimilmente JS vanilla / Vue / React per WordPress, oppure ES modules in plugin) usando i pattern e le librerie già presenti. Se il progetto OLObuild non ha ancora un framework SPA per il pannello dell'editor, scegli quello più adatto al resto del plugin (probabilmente React, dato che WP Gutenberg è React-based).

## Fidelity
**High-fidelity**. Colori, tipografia, spaziature, ombre, comportamenti hover/drag sono finali. La densità della griglia è 2 colonne con card 16×~80px su sidebar 304px (rail 64 + pannello 304 = 368 totali).

## Schermi / Viste

### 1. Tab "Elementi" (V2)
**Scopo**: l'utente cerca e trascina un elemento (Pulsante, Immagine, Hero, ecc.) dentro il canvas centrale.

**Layout sidebar (368px totali)**:
- Tab bar in alto (38px) con due tab: `Elementi` (icona grid) / `Struttura` (icona layers). Tab attiva = bordo inferiore 2px arancio brand Olobuild `#e8622a`.
- Body: grid `64px 1fr`.
- **Rail (64px)**: stack verticale di pulsanti 56px (icona 18px + label 10px + counter pill in alto a destra). Stato attivo = bg `#fff`, barra accent 2px verde brand a sinistra, label scura. Hover non-attivo = bg `rgba(0,0,0,.02)`. In fondo: separatore tratteggiato + pulsante "Personalizza".
- **Pannello (resto)**:
  - Header pannello: dot colorato categoria + titolo (es. "Essenziale", h3 14px/700) + counter pill grigio.
  - Search bar `bg-soft` con input + icona search + clear (`x`) quando ha valore.
  - Griglia 2 colonne, gap 8px, card padding 12/10/10/10. Card: icona 32px in box `bg-muted` rounded 7px + label 12px/500 troncata a 1 riga + opzionale badge categoria (per risultati di ricerca) o stella ⭐ in alto a destra (favorites).
  - Hover card: bordo arancio brand, bg `primary-50`, icon box bianco con shadow, traslate -1px, grip drag a destra.

### 2. Tab "Struttura" (V2 Struttura)
**Scopo**: navigare la gerarchia DOM della pagina (Header / Body / Footer → Sezione → Row → Column → Elemento), selezionare nodi, riordinarli via drag.

**Layout sidebar**: identico a Elementi (rail + pannello).
- **Rail**: ora mostra filtri di vista. 6 voci: Tutto, Header (info/blu), Body (brand/viola), Footer (success/verde), Avvisi (warning/ambra), Globali. Stessa anatomia di pulsante della rail Elementi, con tinte tone-specific su attivo.
- **Pannello**:
  - Header pannello: dot verde + "Struttura pagina" + counter "12 elementi".
  - Search "Cerca un blocco…" filtra l'albero in modo live, nascondendo i sotto-rami che non matchano.
  - Toolbar inline: `Comprimi tutto` / `Espandi tutto` (icone chevron) + (right) bottone `👁 Solo selezione`.
  - **Albero**: 3 blocchi macro (Header, Body, Footer) come "card" colorate con barra accent 3px a sinistra:
    - Header → bg `#eff6ff`, accent `#3b82f6`, icona blue.
    - Body → bg `#faf5ff`, accent `#a855f7`, icona viola.
    - Footer → bg `#f0fdf4`, accent `#22c55e`, icona verde.
    - Header collassabile, contatore "N sezioni" pill bianco semitrasparente a destra.
  - **Righe nodo**: padding 5px 6px, indent step 16px. Da sinistra: grip drag (mostrato solo on hover) → chevron expand (se ha figli) → icona elemento → label → tag opzionale "selezionato" → action group (eye/lock) on hover.
  - **Guide indentazione**: pseudo-elemento dashed `var(--ot-border-strong)` posizionato a 22px dell'inset di ogni gruppo `.v2s-kids`.
  - **Selezionato** (es. nodo Immagine in Body): bg `primary-50` + box-shadow `inset 3px 0 0 #e8622a` + icona arancio + tag pill `selezionato` arancio.
  - **Drop zone vuota**: riga separata con bordo dashed `primary-200`, bg `primary-50`, label "Trascina qui" centrata, icona `+`.
- **Breadcrumb sticky in basso**: bg `bg-soft`, top border. Eyebrow "Selezionato" + path `Body › Sezione › Row › Column › 🖼 Immagine` (ultimo nodo come pill arancio).

## Interactions & Behavior

### Comportamenti chiave
- **Tabs Elementi/Struttura**: switch immediato, mantiene scroll position separato per ogni tab.
- **Rail click**: cambia categoria/filtro attivo, resetta search interna.
- **Search**: debounce 0ms (filtro immediato). In Elementi, se attiva, mostra **tutti** gli elementi matching (cross-categoria) con badge di provenienza. Empty state: "Nessun elemento per <q>".
- **Drag start su card/riga elemento**: `dataTransfer.setData('text/plain', label)` + `setDragImage` con un mini ghost custom (white card + bordo arancio + label, shadow `0 8px 20px rgba(232,98,42,.25)`). Cursor `grab → grabbing`.
- **Drop zone canvas**: bordo box-shadow arancio + hint pill "Rilascia per inserire" sul fondo durante il drag. Implementato con `dragover/dragleave/drop`.
- **Espansione albero**: chevron rotate 0/-90deg con `transition: transform .15s`.
- **Hover row Struttura**: scopre grip + actions (`eye`, `lock`).

### Animazioni
- Transition globale: `all .2s cubic-bezier(.4, 0, .2, 1)` (token `--ot-transition`).
- Hover card: `translateY(-1px)` + transizione bordo/bg.
- Active rail bar: transition transform.
- Nessun bounce, nessun confetti.

### State necessario
- `tab: 'elementi' | 'struttura'`
- Elementi: `activeCategory: string`, `search: string`, `pinnedIds: string[]` (pinned/favorites — persistere in user prefs)
- Struttura: `viewFilter: 'all'|'header'|'body'|'footer'|'issues'|'global'`, `search: string`, `selectedNodeId: string`, `expandedNodes: Set<string>`
- Drag state: `draggingElementType: string | null` (per dare feedback al canvas)

## Design Tokens

Tutti i token sono in `prototype/tokens.css` (estratti dal design system Olo Tutor).

**Colori brand** (Olobuild orange — allineato a `--olo-color-primary` di `#olobuilder-app` in `src/assets/styles/main.scss`):
- Arancio primario `#e8622a` (`--ot-primary`)
- Arancio brillante `#f97316` Tailwind orange-500 (`--ot-primary-bright`)
- Tints: `primary-50 #fdf2ec`, `primary-100 #fbe1d3`, `primary-200 #f7c1a3`, `primary-300 #f29871`, `primary-dark #b04217`

**Neutri**: `bg #f8fafc`, `bg-soft #f9fafb`, `bg-muted #f1f5f9`, `card #fff`, `border #e2e8f0`, `border-light #f1f5f9`, `border-strong #d1d5db`, `text #1e293b`, `text-muted #64748b`, `text-light #94a3b8`.

**Toni macro-aree (Struttura)**:
- Header: `#3b82f6` su `#eff6ff`
- Body: `#a855f7` su `#faf5ff`
- Footer: `#22c55e` su `#f0fdf4`
- Avvisi: `#f59e0b` su `#fffbeb`

**Tipografia**: Work Sans 400/500/600/700, fallback `-apple-system, BlinkMacSystemFont, 'Segoe UI'`. Scala usata: 11/12/13/14. Eyebrow uppercase `letter-spacing: .04–.06em`.

**Spacing**: scala 4/8/12/16/20/24px.

**Radius**: 4 (interni), 6 (chip/row), 7–8 (card/icon-box), 8 (search box), 99px (pill/counter).

**Shadows**: `--ot-shadow-xs 0 1px 2px rgba(16,24,40,.05)`, `--ot-shadow-sm`, `--ot-shadow`. Hover card: `var(--ot-shadow-sm)`.

## Assets
- `assets/olobuild-horizontal.png` — logo ufficiale orizzontale (rosso `#e1474f` "olo" + grigio `#555` "build"), per topbar.
- `assets/olobuild-square.png` — logo quadrato per favicon / area collassata.
- **Icone**: tutte Lucide-style stroke 1.6, 24×24 viewBox. Usa `lucide-vue-next` o `lucide-react` nel codebase target. Il file `icons.jsx` riporta le icone usate (`grid`, `layers`, `clock`, `star`, `square`, `layout`, `text`, `image`, `megaphone`, `spark`, `form`, `cart`, `button`, `map`, `hero`, `spacer`, `panel`, `content`, `video`, `cols`, `colsInner`, `heading`, `code`, `drag`, `chevDown`, `chev`, `plus`, `eye`, `pin`, `search`, ecc.).

## Catalogo elementi
`prototype/data.js` esporta:
- `OLO_CATEGORIES`: array di 10 categorie con `{id, label, dot (color), icon, count}`.
- `OLO_ELEMENTS`: dict cat → array di `{id, label, icon, fav?}`.
- `OLO_ELEMENTS_FLAT`: appiattito per la search globale.

Sostituire con la fonte reale degli elementi del builder (probabilmente un registro PHP che espone i widget tramite REST/AJAX).

## Files

```
prototype/
├── OLObuild — Sidebar redesign.html    # entry point con design-canvas
├── tokens.css                            # design tokens Olo Tutor
├── styles.css                            # shell builder (topbar/canvas/right panel)
├── data.js                               # catalogo elementi + categorie
├── icons.jsx                             # Lucide-style icon set (window.OLOIcon)
├── shell.jsx                             # BuilderShell wrapper + drag util
├── design-canvas.jsx                     # solo per la presentazione, NON da portare
└── variants/
    ├── styles.css                        # CSS V2 elementi
    ├── struttura-styles.css              # CSS V2 struttura
    ├── v2.jsx                            # tab Elementi
    └── v2-struttura.jsx                  # tab Struttura (albero + tree data)
```

I file rilevanti per l'implementazione finale sono **`variants/v2.jsx` + `variants/v2-struttura.jsx` + i due CSS in `variants/`** + i token. Lo shell e il design-canvas sono solo scaffolding del prototipo.

## Note implementative
- Il prototipo è React 18 + Babel inline. Da convertire nel framework target.
- L'albero `V2S_TREE` in `v2-struttura.jsx` è hard-coded come demo — sostituire con il vero modello dati del DOM dell'editor (ID nodo, tipo, parent, children, hidden, locked).
- I `box-shadow inset` per l'accent sinistro del nodo selezionato sono preferibili a un `border-left` per non shiftare il contenuto.
- Le guide tratteggiate dell'indent sono ottenute con `::before` su `.v2s-kids` — verifica resa con l'effettiva profondità dell'albero target.
- Drag and drop: il prototipo usa HTML5 native; valutare libreria (`react-dnd`, `@dnd-kit/core`) per riordino albero affidabile.
