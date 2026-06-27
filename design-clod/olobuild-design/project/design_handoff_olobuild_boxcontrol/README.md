# Handoff: OLObuild — `BoxField`, controllo box-model compatto

## Cos'è

Un **singolo componente riutilizzabile** che sostituisce i controlli "4 lati / 4 angoli" del pannello OLObuild (margine, padding, raggio bordi, spessore bordo) con una versione molto più compatta. Nasce dall'analisi del controllo **Border Radius** attuale, che occupava ~500px di altezza per una proprietà quasi sempre uniforme.

Un solo `BoxField` copre **tre assi** restando in poche righe:

1. **Lati o angoli** — `mode="sides"` (T/R/B/L) o `mode="corners"` (TL/TR/BR/BL)
2. **Stato** — toggle `Normale / Hover` opzionale
3. **Breakpoint** — switch `desktop / tablet / mobile` con ereditarietà

Più: selettore unità (px/%/em/rem/vw/vh), collega/separa on-demand, e un "occhio" neutro che sbircia un'anteprima live.

## ⚠️ C'è UN SOLO componente da implementare

Per evitare equivoci: **l'unico componente è `BoxField`.** Tutto il resto nel prototipo sono *esempi d'uso dello stesso componente*, NON alternative tra cui scegliere:

| Nel prototipo vedi… | Cos'è davvero |
|---|---|
| `MarginDemo`, `PaddingDemo`, `BorderRadiusDemo`, `BorderWidthDemo` | lo stesso `BoxField` con props diverse, ciascuno in un pannello isolato — servono solo a mostrare i singoli casi |
| `StackedPanel` ("Spazi & Bordi") | **la composizione reale da replicare nel pannello**: più `BoxField` impilati in un frame, con UNO switch device globale in cima |
| `ExtendNotes` | una scheda di documentazione, non UI di prodotto |

Quindi: implementa **`BoxField`** una volta, poi comporne istanze. La disposizione finale nel pannello dell'editor è quella di `StackedPanel` (controlli impilati + switch device condiviso). Le demo singole esistono solo perché è più facile validare un controllo per volta.

## ⚠️ Natura dei file (leggere prima)

I file in `prototype/` sono un **mockup React + Babel inline** che gira in browser senza build, pensato per **mostrare comportamento e look finali** — NON è codice di produzione da incollare. Vanno **ricostruiti nello stack reale di OLObuild** (il framework JS che il pannello dell'editor già usa) seguendo i pattern, lo state management e la libreria di componenti esistenti. La logica (modello dati, ereditarietà, range unità) e i token visivi documentati qui sotto sono il riferimento autorevole; l'implementazione concreta è a discrezione di chi sviluppa nel codebase target.

## Come passarlo a Claude Code

Claude Code legge i file del tuo repository. Il modo più efficace:

1. **Copia la cartella** `design_handoff_olobuild_boxcontrol/` nel repo del plugin (es. in `docs/handoff/boxfield/`), committala, e apri il repo in Claude Code.
2. **Dai a Claude Code un prompt** che punta a questo README + prototipo e descrive il target. Esempio:

   > Leggi `docs/handoff/boxfield/README.md` e il prototipo in `docs/handoff/boxfield/prototype/`.
   > Implementa il componente `BoxField` descritto, nel nostro stack (`<dì qui: React/Vue/PHP+Alpine/Web Components…>`),
   > usando i nostri componenti UI esistenti in `<percorso>` invece di ricreare input/slider/dropdown da zero.
   > Rispetta l'API delle props e i comportamenti (ereditarietà breakpoint, override+reset, range per unità).
   > Sostituisci prima il solo controllo Border Radius in `<file>`, lasciando invariato il resto, così possiamo validare il diff.

3. **Itera a piccoli passi**: prima un controllo (Border Radius), poi — validato quello — margine/padding/bordo, infine il pannello impilato con lo switch device globale.

Note utili da dare a Claude Code:
- Le **chiavi delle option WordPress non devono cambiare**: il nuovo controllo legge/scrive gli stessi valori salvati di oggi (top/right/bottom/left, i 4 angoli, hover, durata) — cambia solo la UI.
- Il prototipo usa icone Lucide inline e CSS plain con prefisso `rc-`/`rad-`: nel codebase usate la vostra libreria icone e il vostro sistema di stili.

## API del componente

```jsx
<BoxField
  mode="corners"          // "corners" (TL/TR/BR/BL) | "sides" (T/R/B/L)
  units={["px","%","em","rem","vw","vh"]}  // unità nel dropdown; se 1 sola, niente dropdown
  defaultUnit="px"
  hasHover={true}         // mostra il toggle Normale/Hover + campo Durata transizione
  initial={20}            // valore iniziale per tutti i lati/angoli
  preview="auto"          // "auto" | "radius" | "border" | "spacing" | "none"
  defaultPeek={false}     // anteprima ("occhio") aperta di default
  variant="standalone"    // "standalone" (un pannello) | "stacked" (riga di un pannello multiplo)
  name="Raggio angoli"    // etichetta proprietà (header)
  icon="Radius"           // chiave icona: Radius | Margin | Padding | Border
  responsive={true}       // mostra lo switch device per-campo (se non controllato globalmente)
  bp={bp}                 // OPZIONALE: breakpoint controllato dall'esterno (switch globale)
  onBp={setBp}            // setter del breakpoint globale
/>
```

### Regola `preview`
- `auto` → `radius` se `mode="corners"`, altrimenti `spacing`
- `radius` → chip arrotondata che riflette i 4 angoli
- `border` → chip con lo spessore bordo sui 4 lati
- `spacing` → box interno/esterno che visualizza margine/padding
- `none` → nessuna anteprima

### Switch device: locale vs globale
- **Globale (consigliato a livello pannello):** il genitore tiene `const [bp,setBp]=useState("desktop")`, rende UNO switch in cima al pannello, e passa `bp`/`onBp` a ogni `BoxField`. I campi così **non** mostrano uno switch proprio. È il pattern di Elementor.
- **Locale:** se non passi `bp`/`onBp` e `responsive={true}`, ogni campo mostra il suo switch (utile per controlli isolati).

## Modello dati & comportamenti

```ts
// stato interno di un BoxField
type Vals = {
  [breakpoint in "desktop"|"tablet"|"mobile"]: {
    normal: { all: number; /* + tl,tr,br,bl  OPPURE  t,r,b,l */ };
    hover:  { all: number; /* idem */ };
  }
};
```

Comportamenti da preservare nell'implementazione:

1. **Collega/separa** — di default tutti i lati/angoli sono collegati: uno slider + un campo valore guidano tutti. Il pulsante a sinistra li separa → compaiono 4 mini-input; ricollegandoli tornano a un valore unico.
2. **Range per unità** — lo slider riscala in base all'unità: `px/%/vw/vh` → 0–100 step 1; `em/rem` → 0–10 step 0.1. (Vedi `UNIT_CFG` nel prototipo.)
3. **Ereditarietà breakpoint** — tablet e mobile partono identici a desktop. Finché non li modifichi mostrano l'hint *"Eredita da Desktop"*. Appena cambi un valore diventano un **override**: puntino blu sull'icona del device + chip **↺ reset** che riallinea il breakpoint a desktop.
4. **Hover** — `hasHover` aggiunge il toggle Normale/Hover (condivide lo stesso controllo) e, in modalità Hover, il campo **Durata** (ms) della transizione. Un puntino sul tab "Hover" segnala che i valori hover differiscono dal normale.
5. **Peek (occhio)** — neutro, due stati (aperto/chiuso). Mostra/nasconde l'anteprima live; non altera i valori.

## Design tokens (estratto — vedi `radius.css`, scope `.rad`)

```css
--blue: #3b82f6;  --blue-bright: #2563eb;  --blue-soft: #eff4ff;  --blue-line: #bcd3fb;
--orange: #f97316;            /* solo stato Hover dello slider */
--ink: #374151;  --ink-soft: #6b7280;  --muted: #9ca3af;  --faint: #c4c9d1;
--panel: #f6f7f9;  --line: #e5e7eb;  --field: #fff;
--sans: "Work Sans", system-ui, sans-serif;
--mono: "JetBrains Mono", ui-monospace, monospace;

/* misure chiave */
input/slider row height: 38px;  border-radius input: 8–9px;
slider track: 5px;  knob: 16px (bordo 2px, blu / arancio in hover);
device switch: pill 28×26px per icona, dot override 5px;
```

Lo slider in stato **Hover** usa l'arancione del brand (`--orange`); in stato Normale il blu (`--blue`). L'override e il reset usano il blu.

## Mappatura sul controllo attuale

| Attuale (Border Radius) | Nuovo `BoxField` |
|---|---|
| Griglia 2×2 + link centrale | riga `collega/separa + slider + valore` |
| Blocco HOVER duplicato | toggle `Normale/Hover` sullo stesso controllo |
| Campo Durata sempre visibile | Durata solo in modalità Hover |
| Unità fissa "(px)" nel label | dropdown unità nel chip valore |
| (nessun breakpoint nel singolo controllo) | switch device con ereditarietà |
| ~500px altezza | ~150px stato base |

## Files

```
prototype/
├── index.html        # apri in browser: demo Margine, Raggio, pannello impilato, note
├── radius.css        # stili (scope .rad / classi rc-*); include anche la ricreazione "attuale"
└── box-control.jsx   # il componente: BoxField + DeviceSwitch + UnitSelect + BPanel + demo
```

`box-control.jsx` è autosufficiente (icone e helper sono definiti al suo interno). Per l'implementazione servono concettualmente solo `BoxField` e i suoi sotto-componenti; le funzioni `*Demo`/`StackedPanel`/`ExtendNotes` sono esempi d'uso.

## Note implementative

1. **Niente regressioni di salvataggio** — mappare i valori del nuovo controllo sulle stesse option/meta già usate dal pannello (i 4 lati/angoli, hover, durata). La UI cambia, il formato salvato no.
2. **Accessibilità** — slider come `<input type="range">` con label; lo switch device e il toggle stato come gruppi di radio/`aria-pressed`; il dropdown unità con `role="listbox"`; l'occhio con `aria-pressed` + label "Mostra/Nascondi anteprima".
3. **Tastiera** — frecce ↑/↓ sul campo valore (e shift = passo ×10); Esc chiude il dropdown unità.
4. **Performance** — con uno switch device globale, evitare ri-render dell'intero pannello: ogni `BoxField` mantiene i propri valori, riceve solo `bp` come prop.
5. **Estensioni naturali** — lo stesso schema si applica a **box-shadow** (offset X/Y, blur, spread → 4 numeri + colore), **posizione/offset**, **dimensioni** (min/max width/height). Vale la pena standardizzare un `<NumericMultiField>` di base.
