# Handoff: OLObuild — Pannello Impostazioni (right rail)

## Overview
Redesign del **pannello destro** dell'editor OLObuild (settings di un elemento selezionato). Sostituisce il muro di accordion impilati con un **rail icone a destra + pannello contenuto a sinistra**, specchiando la libreria elementi: ogni gruppo di campi diventa una voce di rail, evitando lo scroll verticale infinito.

L'artboard di riferimento è **02c · Impostazioni** dentro `OLObuild — Sidebar redesign.html`.

## About the Design Files
Prototipo HTML/React come riferimento di look & behavior, **non da copiare 1:1**. Reimplementare nel framework dell'editor target seguendo i pattern del codebase.

## Fidelity
**High-fidelity**. Anatomia, token, comportamenti hover, gerarchia tipografica e densità sono finali.

## Layout

Pannello destro totale **384px**, suddiviso:
- **Header** (sticky in alto, ~150–170px alti): breadcrumb + title + actions, tabs primarie, search/Hover.
- **Body**: grid `1fr 64px`.
  - **Content panel** (sinistra) — controlli della sezione attiva, scroll verticale.
  - **Rail** (destra, 64px) — icone delle sezioni del tab attivo, accent verde a destra.

### Header

1. **Breadcrumb riga** — chip `BODY` viola (bg `#faf5ff` / fg `#7e22ce`) seguito da percorso `Sezione › Riga / Colonne › Colonna › Titolo`. Ultimo nodo in bold scuro, gli altri muted 10px.
2. **Title row** — `Impostazioni Titolo` (h3 14/700) + 4 icon-button 26px (Duplica / Incolla stile / Salva preset / Chiudi).
3. **Tabs primarie** — pill container `border 1px / radius 8px / padding 2px`, 3 tab (`Contenuto`, `Stile`, `Avanzate`). Attivo: bg `#e8622a` (Olobuild brand) + testo bianco + ombra `0 1px 3px rgba(232,98,42,.3)`. Inattivo: trasparente + testo muted.
4. **Search row** — input "Cerca impostazione…" + icon-button **Hover** ambra (`bg #fef3c7 / fg #92400e`) con micro-pill "Hover" che esce in alto a destra (font 8px, bg warning, fg bianco). Lo stato hover è una modalità separata di anteprima.

### Rail (destra)

- Larghezza fissa **64px**, `border-left: 1px solid border-light` (il pattern è specchiato della rail elementi).
- Stesso pulsante della rail elementi: 56px alto, icona 18px + label 10px + counter pill in alto a destra.
- Stato attivo: bg `#fff`, **barra accent 2px arancio brand** sul **lato destro** (`border-radius: 2px 0 0 2px`), label nera, dot 6px (`primary`) come indicatore.
- Hover non-attivo: bg `rgba(0,0,0,.02)`.
- In fondo: separatore tratteggiato + bottone "Custom" (icona +) per aggiungere una sezione personalizzata.

### Sezioni per tab

Hard-coded mock; sostituire con il vero registro di campi per element-type del builder.

| Tab | Sezioni rail |
|---|---|
| **Contenuto** | Titolo · Effetti testo · Aspetto · Decorazione · Sottotitolo · Link |
| **Stile** | Tipografia · Colori · Bordo · Ombra · Sfondo · Spaziature |
| **Avanzate** | ID & Classi · Animazioni · Responsive · Visibilità · CSS custom |

Quando l'utente switcha tab, il rail aggiorna le voci e l'attiva si resetta sulla prima.

## Controlli (componenti del content panel)

Tutti i campi seguono la stessa anatomia con label muted 11px sopra e controllo sotto. Spazio verticale 10–12px tra blocchi.

- **Field block** (`.v2i-block`) — wrapper flex column con label + control. Icona ⚡ ambra accanto alla label se il campo è "dinamico".
- **Textarea / input** — `border 1px / radius 6px / padding 7×9`. Focus: bordo brand + box-shadow focus.
- **Select inline** (`.v2i-select`) — riga 32px con label valore + chevron a destra. Mock; sostituire con `<select>` o popover proprio.
- **Group card** (`.v2i-group`) — wrapper soft (bg `#f9fafb`, border light, radius 8) per raggruppare 2+ blocchi sotto un titolo eyebrow uppercase 10/700 muted. Header può ospitare una pill (es. `Hover` ambra).
- **Swatch palette** — riga di 11 swatch 20×20 + bottone "+" tratteggiato. Lo swatch attivo ha `box-shadow 0 0 0 2px primary`.
- **Color row** — preview 28×28 + input mono `#000000` (font `--ot-font-mono`).
- **Alpha slider** — track 6px `bg-muted` con fill `linear-gradient(90deg, primary-200, primary)` + label cap 11px sx + numero tabular nums dx.
- **Segmented control** (`.v2i-segment`) — pill container `bg-muted / radius 6 / padding 2`, button uguali con attivo `bg #fff + shadow-xs + testo nero`.
- **Number-with-unit** — slider + input numerico `width: 50px / text-align right / tabular-nums` + cap unità.

## Interactions

- **Tab switch** → resetta sezione attiva alla prima del tab.
- **Rail click** → scroll-to-top del pannello e rende la sezione corrispondente.
- **Search** (search row) — filtro live cross-sezione: input matcha label di tutti i campi del tab attivo, mostra solo quelli + breadcrumb della sezione di provenienza. (Mock nel prototipo: input plain.)
- **Hover toggle** (icon-button ambra in search row) — switcha l'edit mode in stato `:hover` dell'elemento; visivamente il pannello mostra una barra ambra 2px in alto e tutti i color picker editano `--hover-…`. Persistere in user prefs.
- **Actions title row**:
  - `Duplica` → clona il nodo selezionato (sibling immediato).
  - `Incolla stile` → applica clipboard di stili (Cmd+Shift+V like).
  - `Salva preset` → apre popover "Nome preset" → salva tra i preset utente.
  - `Chiudi` → deseleziona nodo, collassa pannello (la rail elementi resta).
- **Drag**: i campi non sono draggabili; il riordino si fa dal pannello Struttura.

## Stati

- `tab: 'contenuto' | 'stile' | 'avanzate'`
- `section: string` — id della sezione attiva nel rail
- `search: string`
- `hoverState: boolean`
- `selectedNodeId: string` — viene da fuori (struttura/canvas)

## Design Tokens

Riusa esattamente i token della libreria elementi (file `tokens.css`).

**Specifici del pannello impostazioni**:
- Tabs primarie active: `bg --ot-primary` (Olobuild orange) + ombra `0 1px 3px rgba(232,98,42,.3)`.
- Hover toggle: `bg --ot-warning-soft (#fef3c7) / fg --ot-warning-dark (#92400e)`, micro-pill `bg --ot-warning (#f59e0b)`.
- Group card: `bg --ot-bg-soft / border --ot-border-light / radius 8`.
- Rail accent specchiato: `border-radius: 2px 0 0 2px`, `right: 0`.

## Files
```
prototype/
├── variants/
│   ├── v2-impostazioni.jsx      # componente React
│   ├── impostazioni-styles.css  # stili dedicati
│   └── struttura-styles.css     # eredita .v2-rail base
├── tokens.css                   # token design system Olo
├── icons.jsx                    # icon set Lucide-style (heading, spark, sliders, shape, layers, sliders, etc.)
└── shell.jsx                    # rail base (.v2-rail, .v2-rail-btn) — stesso del pannello sinistro
```

I file rilevanti per implementare SOLO il pannello destro:
**`v2-impostazioni.jsx` + `impostazioni-styles.css`** + token + icone.
La classe `.v2-rail` riutilizzata sta in `variants/styles.css` (sidebar elementi) — copiare la base e aggiungere gli override `.v2i-rail` definiti in `impostazioni-styles.css`.

## Note implementative
- I campi mostrati nel prototipo (Effetto, Carattere cursore, Tipo decorazione, Bordo, ecc.) sono il **set del widget Titolo**. Ogni element-type del builder ha il proprio schema — caricare dinamicamente da un registro PHP lato WordPress (probabilmente l'attuale `Element::get_settings()`).
- Lo schema dovrebbe descrivere anche la **mappatura sezione → tab → campi**, in modo che la rail sia generata automaticamente. Suggerimento di shape:
  ```js
  {
    tab: 'contenuto' | 'stile' | 'avanzate',
    section: { id, label, icon },
    fields: [{ id, label, type, dynamic?, default, ... }]
  }
  ```
- La modalità **Hover** è uno state separato dell'editor, non un valore di campo: deve essere supportato a livello di store (Redux/Pinia/Zustand) e applicato come `--state` CSS variable sul preview.
- I `border-radius: 2px 0 0 2px` della barra accent sul rail destro **NON sono uno specchio CSS automatico** — sono una regola override (`.v2i-rail .v2-rail-btn .bar { left: auto; right: 0 }`).
- Il pannello deve restare **indipendente** dalla rail elementi a sinistra: i due rail vivono in container separati e non condividono state.
