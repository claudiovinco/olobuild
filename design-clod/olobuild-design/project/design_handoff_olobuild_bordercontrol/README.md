# Handoff: OLObuild — `FieldBorder`, controllo Bordo coerente

## Cos'è

Redesign del controllo **Bordo** dell'inspector, per portarlo in linea con `FieldBox`
(margine / padding / raggio) già implementato. Stesso linguaggio, stessa densità, stesso
contratto dati — cambia **solo la UI**, non il formato salvato.

**Oggi** il controllo occupa molto spazio con due blocchi distinti:
1. header *Stile + Colore*;
2. una **croce 2×2** con 4 input numerici e link al centro per lo spessore dei lati;
3. un **pannello separato "Effetti bordo"**.

**Il redesign** sta in un solo blocco compatto:

| Riga | Cosa |
|---|---|
| **Spessore** | `collega/separa + slider + valore (px)` — 4 lati on-demand, identico a `FieldBox` in `mode="sides"` |
| **Stile** | select (solid / dashed / dotted / double / groove / ridge) |
| **Colore** | swatch + campo testo, **token-first** (default `primary`) |
| **Effetto** | *opzionale* — fold-in del vecchio pannello "Effetti bordo", se la tile lo espone |
| **Peek** | anteprima del bordo reale su sfondo a scacchi |

Riferimento visivo: **`REFERENCE_redesign.html`** (confronto *Oggi → Coerente*).

## ⚠️ Natura dei file (leggere prima)

- `prototype/FieldBorder.vue` è una **bozza Vue 3 `<script setup>`** scritta nello stile del
  repo, **da adattare** — non incollare alla cieca. Riusa lo stesso `t()` da `@/i18n`,
  compone `./FieldBox.vue` per lo spessore e mantiene il contratto dati attuale.
- `REFERENCE_redesign.html` è un **mockup statico** (solo look & feel), non codice di prod.
- La logica e i token documentati qui sotto sono il riferimento autorevole; l'implementazione
  concreta resta a discrezione di chi sviluppa nel codebase.

## ⚠️ Vincolo n°1 — contratto dati INVARIATO

Il componente attuale (`src/components/Builder/fields/FieldBorder.vue`) salva **un singolo
oggetto**:

```js
{ top, right, bottom, left, linked, style, color }
// top/right/bottom/left : Number (px, 0–50)
// linked                : Boolean (default true)
// style                 : 'solid' | 'dashed' | 'dotted' | 'double' | 'groove' | 'ridge'
// color                 : String  (hex / rgba / nome-token)
```

Il redesign **legge e scrive esattamente questo oggetto**. Nessuna chiave nuova, nessun
rename. I template esistenti devono continuare a funzionare senza migrazione.

## Mappatura: attuale → redesign

| Attuale | Redesign |
|---|---|
| Croce 2×2 (4 input) + link centrale + preview box | riga **`collega/separa + slider + valore`** (4 lati on-demand) |
| Pannello separato **"Effetti bordo"** | **riga "Effetto"** dentro lo stesso blocco (opzionale) |
| Header *Stile* + *Colore* affiancati in alto | righe **Stile** e **Colore** allineate al resto |
| Colore default **vuoto** (swatch grigio, "—") | colore **token-first**, default `primary` (swatch reso col token) |
| `var(--olo-color-primary, #6366f1)` come accento | accento **CHROME fisso** del builder (vedi §Accento) |
| ~430px, 2 sezioni | ~210px, 1 blocco |

## Spessore = `FieldBox` in `mode="sides"`

Non reimplementare la riga: lo spessore è **lo stesso `FieldBox`** già in produzione.
Il suo contratto combacia 1:1 col bordo (scalare quando collegato, `{top,right,bottom,left}`
quando separato):

```vue
<FieldBox
  mode="sides"
  :units="['px']"
  :slider-max="50"
  :slider-step="1"
  preview="none"
  :model-value="linked ? top : { top, right, bottom, left }"
  @update:model-value="onWidth"
/>
```

`onWidth` ricuce il risultato dentro l'oggetto bordo, impostando `linked` di conseguenza
(vedi la bozza). Così spessore e raggio/margini/padding si comportano **identici**.

## Accento — CHROME vs CONTENUTO ⚠️ (decisione da confermare)

Regola del progetto: **i controlli dell'inspector sono CHROME del builder** → accento
**arancio fisso `#e8622a`** (identità prodotto), mentre il **colore del bordo** (contenuto
della tile) usa i **token del cliente**.

> Disallineamento noto: il `FieldBox` shippato usa `var(--olo-color-primary, #6366f1)` per
> slider/link/collegato. Per coerenza visiva nello **stesso** pannello, o:
> 1. **(consigliato)** si migra anche `FieldBox` all'accento fisso, esponendo un unico
>    `--olo-ui-accent: #e8622a` a livello pannello e sostituendo i `var(--olo-color-primary…)`
>    di chrome con `var(--olo-ui-accent)`; **oppure**
> 2. si mantiene `FieldBorder` su `--olo-color-primary` come l'attuale `FieldBox`.
>
> La bozza usa `var(--olo-ui-accent, #e8622a)`: cambiare in un punto solo per scegliere.
> NON introdurre nuovi nomi `--olo-color-*` che il `GlobalColorsPanel` non genera.

Il **colore del bordo** invece è contenuto: default token `primary`, risolto in
`var(--olo-color-primary)` per swatch e peek (vedi `TOKEN_VAR` nella bozza).

## Hover & breakpoint — NON nel field

Come `FieldBox` (vedi il suo commento d'apertura), il field **non** gestisce hover né
responsive:
- **Hover** → l'occhio Normale/Hover è chrome di `InspectorField`; il bordo usa la sua chiave
  hover esistente (es. `border_hover`) — preservarla, non duplicarla nel field.
- **Breakpoint** → `ResponsiveFieldWrap` avvolge il field e salva su chiave suffissata
  `border_<bp>` (es. `border_tablet`), con badge "Eredita" e pills DT/TL/TP/ML/MB.

Nel mockup lo switch device e il toggle Normale/Hover compaiono in alto **solo per contesto**:
nel codice arrivano dai wrapper, non da `FieldBorder`.

## Comportamenti da preservare

1. **Collega/separa** — default collegato (uno slider guida i 4 lati). Separato → 4 mini-input;
   ricollegando si allinea al massimo dei lati (logica di `FieldBox`).
2. **Range** — spessore 0–50 px, step 1 (come l'attuale `min/max/step` degli input).
3. **Stile** — stesse 6 opzioni, stessi `value`.
4. **Colore** — stringa invariata nel salvataggio; token risolti solo in resa. Picker nativo
   sullo swatch (overlay `opacity:0`, come l'attuale, per evitare il `.click()` programmatico).
5. **Peek** — mostra il bordo reale (lati + stile + colore risolto); non altera i valori.

## Files

```
design_handoff_olobuild_bordercontrol/
├── README.md                 # questo file
├── PROMPT.txt                # frase pronta per Claude Code
├── REFERENCE_redesign.html   # mockup statico: confronto Oggi → Coerente
└── prototype/
    └── FieldBorder.vue       # bozza Vue del redesign (contratto dati invariato)
```

## Come passarlo a Claude Code

1. Tieni questa cartella accessibile a Claude Code (puoi committarla nel repo, es.
   `docs/handoff/border/`, oppure lasciarla dove l'hai scaricata e indicarne il percorso).
2. Dai a Claude Code il prompt in `PROMPT.txt` (o il testo qui sotto).
3. Itera a piccoli passi: prima sostituisci **solo** il template/stili di `FieldBorder.vue`
   lasciando invariato `<script>` e il modello salvato → valida il diff sul canvas; poi
   collega lo spessore a `FieldBox`; infine valuta il fold-in dell'Effetto e l'allineamento
   dell'accento con `FieldBox`.

## Note implementative

- **Niente regressioni di salvataggio** — l'oggetto `border` resta `{top,right,bottom,left,
  linked,style,color}`. Hover/responsive sui loro meccanismi attuali (`border_hover`,
  `border_<bp>`).
- **Accessibilità** — slider come `<input type="range">` con label (eredita da `FieldBox`);
  select con `aria-label`; swatch con input `type="color"` etichettato; occhio/hover dal
  wrapper con `aria-pressed`.
- **Effetto** — la riga è opzionale (`showEffect`). Attivarla **solo** se la tile espone già
  un'opzione effetto bordo, riusando la sua chiave reale; altrimenti ometterla.
