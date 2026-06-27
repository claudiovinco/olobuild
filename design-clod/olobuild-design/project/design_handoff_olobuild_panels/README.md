# Handoff: OLObuild — pannelli inspector & inserter (Layout · Effetti · Inserter Elementi)

## Cos'è

Tre redesign che continuano il lavoro già consegnato su `FieldBox` (margine/padding/raggio/
spessore) e `FieldBorder`. Stesso linguaggio, stessa densità, **stesso contratto dati** — cambia
**solo la UI**.

| Pezzo | File spec | Riferimento visivo | Componente repo (presunto) |
|---|---|---|---|
| **Scheda Layout** | `LAYOUT.md` | `reference/REFERENCE_layout.html` | pannello *Layout* dell'inspector |
| **Sezione Effetti** | `EFFETTI.md` | `reference/REFERENCE_effetti.html` | pannello *Effetti* + *Transizione hover* |
| **Inserter Elementi** | `INSERTER.md` | `reference/REFERENCE_inserter.html` | pannello inserimento tile (rail + griglia) |

> Il controllo **Bordo** ha il suo handoff dedicato in
> `design_handoff_olobuild_bordercontrol/` — qui non si tocca.

Ogni `.md` è autonomo: vincoli, mappatura Oggi→Coerente, comportamenti da preservare.
Ogni `REFERENCE_*.html` è un **mockup statico** (look & feel, confronto *Oggi → Coerente*),
**non** codice di produzione.

## ⚠️ Regole comuni a tutti e tre (leggere prima)

### 1. Contratto dati INVARIATO
Le chiavi salvate restano **identiche**. Nessun rename, nessuna chiave nuova, nessuna
migrazione. La UI legge/scrive esattamente i campi attuali (elencati in ogni spec). I template
esistenti devono continuare a funzionare.

### 2. CHROME vs CONTENUTO
- I **controlli dell'inspector sono CHROME del builder** → accento **arancio fisso `#e8622a`**
  (+ navy `#16263d`). È identità di prodotto: slider, knob, toggle, selettore Normale/Hover,
  barretta di categoria attiva, focus dei campi → tutti arancio.
- Il **colore del contenuto** (bordo, ombra-testo, ecc.) usa i **token del cliente**
  (`var(--olo-color-*)`), default token `primary`. Vedi `TOKEN_MAPPING.md` del pacchetto tiles.
- Usa un unico `--olo-ui-accent: #e8622a` a livello pannello per il chrome; **non** introdurre
  nomi `--olo-color-*` che `GlobalColorsPanel` non genera.

### 3. Responsive NON nel pannello
Lo switch device (Desktop/Tablet/Mobile) **non** va ripetuto dentro i pannelli: vive **a
livello di pagina/toolbar**. Nei mockup non c'è più. Il salvataggio per-breakpoint resta
affidato al wrapper esistente (`ResponsiveFieldWrap`, chiavi suffissate `_<bp>`), non al field.

### 4. Stato Normale/Hover = nuovo selettore
Dove serve lo stato hover, usa il **selettore segmentato** di produzione (pillola navy,
segmento attivo bianco in rilievo) — vedi `reference/REFERENCE_effetti.html`. È chrome di
`InspectorField`, non logica del singolo field. La chiave hover esistente va **preservata**,
non duplicata.

### 5. Niente campi vuoti
Ogni input mostra un **segnaposto sensato** (`auto`, `0`, `Nessuna`, `Ease`…) e ogni dimensione
ha la sua **unità in valbox** (`px · % · em · rem · vw · vh · ° · ms`). Mai una casella vuota o
un numero nudo senza unità.

## Ordine consigliato

1. **Layout** — il più semplice (segnaposto + unità + select Overflow). Vale come "warm-up"
   per validare il pattern valbox/unità su un pannello piccolo.
2. **Effetti** — il più denso: unifica gli slider su un unico stile arancio, normalizza tutte
   le unità, allinea gli "occhi" di visibilità, separa la *Transizione hover* come pannello a sé.
3. **Inserter Elementi** — tocca il chrome di navigazione (rail + griglia tile), non un field.
   Procedi solo dopo aver allineato l'accento.

## Files

```
design_handoff_olobuild_panels/
├── README.md                       # questo file
├── PROMPT.txt                      # prompt pronto per Claude Code
├── LAYOUT.md                       # spec scheda Layout
├── EFFETTI.md                      # spec sezione Effetti
├── INSERTER.md                     # spec inserter Elementi
└── reference/
    ├── REFERENCE_layout.html
    ├── REFERENCE_effetti.html
    └── REFERENCE_inserter.html
```

## Come passarlo a Claude Code

1. Tieni la cartella accessibile (committala es. in `docs/handoff/panels/` o indica il percorso).
2. Dai il prompt in `PROMPT.txt`.
3. Itera **un pannello alla volta** e **a piccoli passi**: prima `<template>` + `<style>`
   lasciando `<script>`/modello invariato → valida il diff sul canvas → poi i collegamenti
   (es. valbox riusabile, select). Mai un big-bang sui tre insieme.
