# Handoff: OLObuild — Rendere le Tile belle & coerenti (tutte le 240)

**Obiettivo:** le 240 `*Tile.vue` devono sembrare **una sola famiglia** — belle appena
inserite e visivamente coerenti tra loro. Questo pacchetto dà a Claude Code lo standard
visivo + il processo per agire e ragionare su **ogni** tile, più i composable/utility per
i problemi trasversali. Campione letto a fondo: Button, Alert, Hero, Divider (render) +
`button.js` e `_styleFieldsBase.js` (editor) — i pattern si ripetono nell'intero sistema.

**Da dove parte Claude Code:**
0. **`START_HERE.md`** — ⛳ punto di ingresso: protocollo operativo + prompt da incollare + Definition of Done.
1. `DESIGN_LANGUAGE.md` — le 10 regole di "bello & coerente" che ogni tile deve seguire.
2. `TILE_AUDIT_CHECKLIST.md` — il processo ripetibile su tutte le 240, per categoria.
3. Composable/token (`prototype/`) — gli strumenti per applicarle senza reinventare.
4. `BUTTON_EXAMPLE.md` — un refactor completo come modello.
5. `CLAUDE.md` — da copiare nella root del repo: regole sempre attive in ogni sessione.

## ⚠️ Natura dei file

I file in `prototype/` (`useBoxModel.js`, `oloTileDefaults.js`) sono **implementazioni
di riferimento**, vicine alla produzione perché sono *logica*, non UI — ma vanno
**verificate contro lo schema reale delle chiavi** e adattate ai percorsi/store del
progetto prima dell'adozione. Non cambiano il formato dei dati salvati: leggono i
formati esistenti.

---

## Sintesi: anatomia attuale di una tile

- **Render:** `*Tile.vue` riceve `settings`, calcola stili in `computed`, renderizza con
  un mix di utility `mb-*` + grossi oggetti `:style` inline. Default dichiarati inline.
- **Editor:** `src/config/elements/<type>.js` definisce `defaults`, `fields[]`
  (CONTENUTO) e `styleFields[]` (STILE), su una base condivisa `styleFieldsBase()`.
  Lo split CONTENUTO/STILE/AVANZATE è una buona regola universale già presente.
- **Punti di forza da preservare:** lo split semantico, i field dichiarativi, gli helper
  `withHover()`/`responsive`, l'integrazione di `FieldBox`/`box-stack`, i preset.

---

## I 8 problemi sistemici (in ordine di impatto)

### 1. Default off-brand e non centrati sui token
`ButtonTile` nasce `#6366F1` (indigo) hardcoded; `Alert` ha palette fisse
(`#DBEAFE`…); `Divider` grigi fissi. Esiste `--olo-color-primary` ma i **fallback non
lo usano**: `Hero` fa `var(--olo-color-primary, #6366F1)` (token-first, ✅) mentre
`Button` mette `#6366F1` diretto (❌). Risultato: tile appena inserite sembrano indaco,
non del brand.
→ **Fix:** `resolveColor(userVal, TOKEN)` token-first ovunque (vedi `oloTileDefaults.js`).

### 2. Default non "belli appena inseriti" (richiesta esplicita)
I default sono validi ma generici (radius 6, padding arbitrari per tile, `shadow:'none'`).
Inserire una tile dovrebbe dare subito qualcosa di **curato**.
→ **Fix:** `TILE_DEFAULTS` curati su scala condivisa `SPACE`/`RADIUS` + micro-ombre
eleganti, colore brand, contrasto leggibile.

### 3. Default duplicati in DUE posti, e divergenti
`button.js` ha `defaults.bg_color: ''` ma `ButtonTile.vue` ha `bg_color: '#6366F1'`:
due fonti di verità che **non concordano**. Stesso per padding/radius su molte tile.
→ **Fix:** **unica fonte** (`buildDefaults(type)`); il Tile legge i default dal registry,
non li ridichiara.

### 4. Parsing box-model ripetuto in ogni tile (render)
`border_radius` numero|oggetto e `tile_padding` oggetto|legacy `padding_x/y` vengono
ri-parsati a mano in `ButtonTile`, `HeroTile` (`radiusCss`), `StyleBoxStack`
(`radiusPreviewCss`)… È il gemello lato render di ciò che `FieldBox` ha già unificato
lato input.
→ **Fix:** `useBoxModel(settings)` / `toRadiusCss` / `toSpacingCss` (vedi `useBoxModel.js`).

### 5. Due linguaggi di stile mescolati
Utility `mb-*` + `:style` inline giganti + perfino `style="..."` statici nello stesso
template (es. `Hero`). Difficile da temizzare e mantenere coerente.
→ **Fix:** convergere su un layer (utility o CSS scoped con var dei token); estrarre gli
oggetti stile ripetuti in composable.

### 6. Icone emoji come default
`Alert` usa `ℹ️ ✅ ⚠️ ❌`; `Divider` accetta emoji libere. Resa incoerente tra OS,
non accessibile, "cheap". Esiste già `iconsSvg`.
→ **Fix:** default su icone SVG del set (`icon: 'info'`…), emoji solo come scelta esplicita.

### 7. Accessibilità del render
`Button` e le CTA di `Hero` sono `<span>` con `cursor:pointer` — niente focus da
tastiera, niente ruolo/semantica, l'URL non è un vero link. Il dismiss dell'`Alert`
non ha label accessibile. Nessun controllo di contrasto sui colori custom.
→ **Fix:** elementi semantici (`<a>`/`<button>`) nel render front-end, `aria-label` sui
controlli icona, avviso di contrasto AA nell'editor quando i colori falliscono.

### 8. Editor: concetti duplicati e tab lunghi
`button.js` espone **sia** `bg_color` (semplice) **sia** `bg` (oggetto background);
**sia** il bordo legacy (`border_width/style/color`) **sia** `border` (oggetto). Due
sistemi paralleli per lo stesso scopo → l'utente non sa quale vince. L'hover è sparso
su `hover_bg_color/hover_text_color/hover_shadow/hover_effect/...` + i `withHover()`.
L'ombra custom hover apre **7 campi condizionali** inline.
→ **Fix:** un solo sistema per concetto (preferire gli oggetti `bg`/`border`/shadow-block),
deprecare i gemelli legacy dietro un layer di migrazione; raccogliere l'hover in un unico
modello (come fa `StyleBoxStack` col toggle Normale/Hover).

---

## Raccomandazioni prioritarie

| # | Azione | Effort | Impatto |
|---|--------|--------|---------|
| 1 | `resolveColor()` token-first + `TILE_DEFAULTS` curati | M | ⭐⭐⭐ estetica/brand immediati |
| 2 | Unica fonte dei default (`buildDefaults`) | M | ⭐⭐⭐ elimina divergenze |
| 3 | `useBoxModel()` lato render | S | ⭐⭐ DRY, meno bug |
| 4 | Default icone SVG (no emoji) | S | ⭐⭐ coerenza |
| 5 | Render semantico + a11y (`<a>`/`<button>`, aria, contrasto) | M | ⭐⭐ accessibilità |
| 6 | Deprecare i gemelli legacy bg/border nell'editor | L | ⭐⭐ chiarezza editing |
| 7 | Convergere il linguaggio di stile | L | ⭐ manutenibilità |

Suggerito partire da 1+2+3 insieme (si rinforzano) su **una tile pilota** (Button),
validare il diff, poi propagare per categoria.

## Come passarlo a Claude Code

1. Copia `design_handoff_olobuild_tiles/` nel repo (es. `docs/handoff/tiles/`), committa.
2. Prompt esempio:
   > Leggi `docs/handoff/tiles/README.md` e `prototype/`. Adotta `useBoxModel`,
   > `oloTileDefaults` (resolveColor + TILE_DEFAULTS) come da analisi. Applica PRIMA
   > alla sola tile **Button**: default token-first + unica fonte dei default + box-model
   > via composable, SENZA cambiare le chiavi salvate. Mostrami il diff di
   > `ButtonTile.vue` e `button.js`. Poi propaghiamo.
3. Ribadisci: *"i file prototype sono riferimento; verifica i nomi chiave reali e i token
   del nostro store prima di adottarli"*.

## Files

```
DESIGN_LANGUAGE.md       # ⭐ le 10 regole "bello & coerente" per ogni tile
TILE_AUDIT_CHECKLIST.md  # ⭐ processo ripetibile su tutte le 240, per categoria
README.md                # questo: obiettivo + analisi sistemica
TOKEN_MAPPING.md         # ⚠️ come i default colore si legano ai colori globali del cliente
BUTTON_EXAMPLE.md        # worked example: refactor della tile Button
REFERENCE_card-category.html  # riferimento VISIVO: categoria Card, oggi (incoerente) vs famiglia coerente
REFERENCE_data-category.html        # riferimento VISIVO: Dati/Numeri (Counter/Countercircle/Countdown)
REFERENCE_interactive-category.html # riferimento VISIVO: Interattive (Accordion/Tabs/Form) + focus a11y
prototype/
├── useBoxModel.js       # normalizzazione radius/padding/margin (render side)
├── oloTileDefaults.js   # GLOBAL/SYSTEM token + resolveColor + contrastOn + SPACE/RADIUS + TILE_DEFAULTS
└── tokens-brand.css     # seed dei ruoli globali + token di sistema (fallback)
```

> **Ordine di lettura per Claude Code:** `DESIGN_LANGUAGE.md` → `TILE_AUDIT_CHECKLIST.md`
> → `TOKEN_MAPPING.md` → `BUTTON_EXAMPLE.md`. Poi lavorare per categoria. Non inventare
> nomi di variabile che il GlobalColorsPanel non genera; non cambiare le chiavi salvate.

Vedi anche il report visuale `OLObuild - Analisi sistemica Tile.html` (stesso contenuto,
formato sfogliabile) nella root del progetto di design.
