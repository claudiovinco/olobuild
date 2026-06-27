# Spec — 4 tile "Zona interattiva" (Finder · Builder · Mixer · Projector)

Quattro **nuove tile** per la categoria *Interattive* di OLObuild, estratte dai demo
OLOthemes. Sono i pattern marcati `data-olo-note="NEW tile candidate: …"` nei file theme;
oggi **36 dei 50 theme** ne montano almeno una (badge "Zones" nella vetrina).

> **Reference implementation funzionante:** `olothemes/fx.js` (dependency-free, vanilla).
> Ogni demo è il prototipo vivo di come la tile deve comportarsi. Questo doc traduce quel
> comportamento in una tile OLObuild (registry + componente Vue), nello stesso stile del
> `BUTTON_EXAMPLE.md`: **default token-first**, **una sola fonte dei default**,
> **chiavi salvate stabili**, **box-model via `useBoxModel`**, pass a11y.

---

## 0. Regole comuni a tutte e 4 (leggere prima)

**CHROME vs CONTENUTO.** Queste tile sono **CONTENUTO del sito** → si vestono con i
**token del cliente** (i 6 ruoli globali), MAI con l'arancio del builder. L'arancio
`#e8622a` resta solo nella *chrome* dell'inspector (slider/device/toggle).

**Token-first (come Button).** Nessun colore hardcoded. Ogni tile ha un gruppo di stile
"Zona" che risolve via `resolveColor(userValue, token)` dai token di `oloTileDefaults.js`:

| Variabile demo (`fx.js`) | Ruolo            | Default token              | Note |
|--------------------------|------------------|----------------------------|------|
| `--fx-zone-accent`       | colore attivo    | `TOKENS.primary`           | chip/slider/preview attivi |
| `--fx-zone-on`           | testo su accent  | `contrastOn(accent)`       | calcolato, mai chiesto |
| `--fx-zone-line`         | bordi/traccia    | `SYSTEM.border`            | hairline neutra |
| `--fx-zone-bg`           | tinta soft hover | `color-mix(accent 10%,#fff)` | derivata, non chiesta |
| `--fx-zone-thumb`        | pallino slider   | `SYSTEM.surface`           | solo Projector |

→ la tile espone **un solo controllo colore**: `zone_accent` (ruolo/colore). Gli altri
4 sono **derivati** (contrasto + color-mix), come per Alert. Curato appena inserito.

**Una sola fonte default.** `buildDefaults('finder'|'builder'|'mixer'|'projector')` in
`oloTileDefaults.js`; il registry e il componente leggono da lì, non li ridichiarano.

**Box-model condiviso.** `border_radius` + `tile_padding` via `useBoxModel` (fallback dalla
scala `RADIUS`/`SPACE`). Stesse chiavi delle altre tile → nessuna migrazione.

**Stati richiesti (tutte):** default · hover (`translateY(-2px)`) · attivo/selezionato
(`accent`) · `:focus-visible` (outline 2px su `accent`) · vuoto (placeholder) ·
`prefers-reduced-motion` (niente transizioni/entrata).

**A11y baseline:** controlli reali (`<button>` per chip/stepper, `<input type="range">`
per Projector), `aria-pressed`/`aria-selected` sulle opzioni attive, label/`aria-label`
su ogni controllo, output con `aria-live="polite"`, target ≥ 44px.

---

## 1. Finder — *consiglia con un tap*

**Scopo.** L'utente tocca un'opzione ("Per chi compri?", "Dov'è il tuo corpo oggi?") e
sotto appare la card consigliata. Zero form, una scelta. *(Demo: Maison, Terra, Contour,
Carrello, Fjordline…)*

**Anatomia.** Riga di chip (opzioni) → una card risultato visibile alla volta. Prima
opzione attiva di default.

**Campi inspector**

| Campo (label)        | Controllo            | Chiave salvata    | Default |
|----------------------|----------------------|-------------------|---------|
| Titolo / occhiello   | Text                 | `heading`, `eyebrow` | '' |
| Opzioni + risultati  | **Repeater**         | `items[]`         | 3–4 voci |
| Layout chip          | Segmented (wrap/scroll) | `chips_layout` | `wrap` |
| Allineamento         | Segmented (left/center) | `align`        | `center` |
| Colore zona          | Color/ruolo          | `zone_accent`     | `''` → primary |
| Raggio / padding     | FieldBox             | `border_radius`, `tile_padding` | scala |

**Modello dati**
```jsonc
items: [
  { key: "partner",                  // stabile, generato una volta (non da label)
    label: "Per un partner",          // testo chip
    media: { id, alt },               // immagine card (placeholder se vuota)
    kicker: "Collezione",
    title: "The Thoughtful Edit",
    body: "…",
    meta: "da €24",                   // riga prezzo/nota opz.
    cta: { text: "Vedi", url: "" }    // opz.
  }
]
```
**Comportamento (da `setupFinder`).** Click opzione → `aria-pressed`/`.on` sulla chip e
mostra la card con `key` corrispondente; tutte le altre nascoste. La **prima** voce è
attiva al load. ⚠️ La card NON deve forzare `display` da `.show` → il tema/tile la rende
`flex`/`grid` (vedi `[data-finder-res]:not(.show){display:none}`).

**Reference:** `[data-finder]` › `.fxf-opts > [data-finder-opt="key"]` + `[data-finder-res="key"]`.

---

## 2. Builder — *componi e somma dal vivo*

**Scopo.** Stepper +/- su una lista (carrello, box, menu, kit) con **totale e conteggio
live**, cap opzionale. *(Demo: Carrello, Mercato, Honeycomb, Tavola, Verde, Circuit…)*

**Campi inspector**

| Campo                | Controllo        | Chiave salvata | Default |
|----------------------|------------------|----------------|---------|
| Titolo / intro       | Text             | `heading`, `intro` | '' |
| Voci                 | **Repeater**     | `items[]`      | 4–6 |
| Valuta               | Text (1–2 char)  | `currency`     | `€` |
| Limite quantità      | Number (0 = off) | `cap`          | `0` |
| Colore zona          | Color/ruolo      | `zone_accent`  | `''` → primary |
| Raggio / padding     | FieldBox         | `border_radius`, `tile_padding` | scala |

**Modello dati**
```jsonc
items: [ { name: "Tazza", price: 32, media: {id,alt}, note: "Clayfolk", start: 0 } ]
currency: "€"   // stringa libera; "" ammesso (nessun simbolo)
cap: 6          // 0 = nessun limite
```
**Comportamento (da `setupBuilder`).** `+`/`-` cambiano `data-n` (min 0); `total = Σ n·price`
formattato `currency + total.toFixed(2)` togliendo `.00`; conteggio = Σ n. Voce con n>0 →
`.on`. Con `cap`: a quota raggiunta blocca gli `+` e setta `.bld-full`.
⚠️ Default numerici con check `== null` (lo **0** è falsy): non usare `|| default`.

**Reference:** `[data-builder data-currency data-cap]` › `[data-bld-item data-n data-price]`
con `.fxb-step` (`[data-bld-dec]`/`[data-bld-count]`/`[data-bld-inc]`); output
`[data-bld-total]`, `[data-bld-items]`.

---

## 3. Mixer — *fondi gli swatch in un'anteprima*

**Scopo.** Tocchi 2–3 swatch (colori, materiali, glaze) → anteprima fusa dal vivo + nomi
scelti. *(Demo: Kiln, Canvas, Vélour, Loft, Prisma.)*

**Campi inspector**

| Campo            | Controllo     | Chiave salvata | Default |
|------------------|---------------|----------------|---------|
| Titolo / intro   | Text          | `heading`, `intro` | '' |
| Swatch           | **Repeater**  | `swatches[]`   | 5–8 |
| Max selezionabili| Number        | `max`          | `3` |
| Testo vuoto      | Text          | `empty`        | "Tocca per fondere" |
| Forma swatch     | Segmented (cerchio/quadrato) | `swatch_shape` | `circle` |
| Colore zona      | Color/ruolo   | `zone_accent`  | `''` → primary |

**Modello dati**
```jsonc
swatches: [ { hex: "#c2724a", name: "Terracotta" } ]
max: 3
```
**Comportamento (da `setupMixer`).** Click swatch: se già scelto lo deseleziona; altrimenti
lo aggiunge (oltre `max` scarta il più vecchio, FIFO). Anteprima = **media RGB** dei
selezionati (no `color-mix` → robusto, deterministico); `[data-mix-out]` = nomi uniti con
" + ". Swatch scelti → `.on`. La preview vive **dentro** la tile.

**Reference:** `[data-mixer data-max data-empty]` › `.fxm-swatches > [data-mix="#hex" data-mix-name]`,
preview `[data-mix-preview]`, output `[data-mix-out]`.

---

## 4. Projector — *uno slider → un valore calcolato*

**Scopo.** Uno slider guida un numero che si aggiorna live: budget viaggio, costo stimato,
ore risparmiate, montante composto. *(Demo: Sterling, Capital Row, Nimbus, Ledger, Synapse,
Fjordline.)*

**Campi inspector**

| Campo               | Controllo       | Chiave salvata | Default |
|---------------------|-----------------|----------------|---------|
| Titolo / intro      | Text            | `heading`, `intro` | '' |
| Min / Max / Step    | Number ×3       | `min`,`max`,`step` | 0 / 100 / 1 |
| Valore iniziale     | Number          | `value`        | metà range |
| Tasso annuo         | Number (0 = lineare) | `rate`    | `0` |
| Moltiplicatore/anni | Number          | `years`        | `1` |
| Valuta              | Text ("" = nessun simbolo) | `currency` | `€` |
| Label slider / cap output | Text       | `input_label`, `out_caption` | '' |
| Mostra input live   | Toggle          | `show_contrib` | `false` |
| Colore zona         | Color/ruolo     | `zone_accent`  | `''` → primary |

**Comportamento (da `setupProjector`).**
`fv = rate===0 ? value*years : value*((1+rate)^years − 1)/rate`.
- `rate = 0` ⇒ **lineare** (`units × multiplier`): costo/tasse/ore/budget.
- `rate > 0` ⇒ montante (annuity FV): risparmio/investimento.
Output formattato `Intl.NumberFormat` (0 decimali) con prefisso `currency`.
⚠️ Default con check `== null` (**non** `|| default`): servono `rate=0` e `currency=""`.
Se serve un simbolo dopo un numero "nudo" (es. "8 notti"), tienilo come testo statico nel
markup e usa `currency:""` (vedi demo Fjordline). La traccia dello slider si riempie con
`--pct`.

**Reference:** `[data-project data-rate data-years data-currency]` › `[data-project-input]`
(range) + `[data-project-out]`, opz. `[data-project-contrib]`.

---

## 5. Pattern inspector condiviso — il **Repeater**

Finder/Builder/Mixer condividono un repeater di voci. Convenzioni:
- **`key`/`id` stabile** generato una volta alla creazione della voce (UUID corto),
  **non** derivato dalla label (stessa fragilità del GlobalColorsPanel — vedi `TOKEN_MAPPING.md`).
  Rinominare una label non deve rompere lo stato salvato.
- Riordino drag, duplica, elimina; min 1 voce.
- Immagini = `media:{id,alt}` con placeholder a strisce quando vuote (mai SVG disegnati).
- Le emoji NON sono default: icone dal set SVG.

## 6. Definition of Done (per ognuna delle 4)

- [ ] Default in `buildDefaults('<type>')`, **token-first**, una sola fonte (no ridichiara).
- [ ] `border_radius` + `tile_padding` via `useBoxModel`; chiavi salvate **invariate**.
- [ ] Un solo controllo colore (`zone_accent`); `on`/`bg`/`line` derivati (contrastOn/color-mix).
- [ ] Stati: hover, attivo, `:focus-visible`, vuoto, `prefers-reduced-motion`.
- [ ] A11y: controlli reali, `aria-pressed/selected`, `aria-live` sugli output, target ≥44px.
- [ ] Logica numerica con check `== null` (0 e "" validi); valuta "" ammessa.
- [ ] Bella appena inserita: palette = token cliente, raggio/padding da scala.

## 7. Aggancio alla propagazione (estende la checklist del BUTTON_EXAMPLE)

- [ ] Interattive: Form, Accordion, Tabs, Flipcard
- [ ] **+ Finder, Builder, Mixer, Projector** ← questo doc (proto live in `olothemes/fx.js`)
