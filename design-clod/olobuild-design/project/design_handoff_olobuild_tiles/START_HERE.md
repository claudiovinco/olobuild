# ⛳ START HERE — Istruzioni operative per Claude Code

Questo file vive in `D:\TECNICA\olobuild\regoletiles1\` ed è il **punto di ingresso
unico**. Tutti i file citati qui sotto sono nella stessa cartella. Leggilo per intero,
poi esegui il protocollo. Obiettivo: rendere **tutte** le tile di OLObuild **belle e coerenti** come
una sola famiglia, applicando le regole a **ognuna delle 240**, senza eccezioni.

## 0) Ordine di lettura (obbligatorio, prima di toccare codice)
1. `DESIGN_LANGUAGE.md` — le 10 regole non negoziabili
2. `TILE_AUDIT_CHECKLIST.md` — la checklist a 10 punti + categorie
3. `TOKEN_MAPPING.md` — come i colori si legano ai globali del cliente (NON inventare nomi var)
4. `BUTTON_EXAMPLE.md` — un refactor completo, da imitare
5. `REFERENCE_card-category.html`, `REFERENCE_data-category.html`,
   `REFERENCE_interactive-category.html` — il risultato atteso, *visto*
6. `prototype/` — `oloTileDefaults.js`, `useBoxModel.js`, `tokens-brand.css` (gli strumenti)

## 1) Setup una-tantum (fondamenta condivise)
- Porta in `src/` gli strumenti: `oloTileDefaults.js`, `useBoxModel.js` (in `@/composables`),
  e includi i token di `tokens-brand.css` globalmente.
- `GlobalColorsPanel`: **primario seed = `#e1474f`**; **id dei 6 ruoli stabili** (non
  rigenerati dalla label); aggiungi i 4 ruoli **semantici** (info/success/warning/error);
  lo store calcola **`--olo-color-on-primary`** con `contrastOn(primary)`.

## 2) Enumerazione COMPLETA (non saltare nulla)
- Elenca **tutti** i file `src/components/Tiles/*Tile.vue` (sono ~240) e i relativi
  config `src/config/elements/*.js`.
- Crea/aggiorna un file di avanzamento **`TILE_PROGRESS.md`** con UNA riga per tile e una
  casella per ciascuno dei 10 punti della checklist. Nessuna tile può restare senza riga.

## 3) Loop per OGNI tile (per categoria, vedi checklist)
Per ciascuna tile applica i 10 punti di `TILE_AUDIT_CHECKLIST.md`:
colore→token, spazi→SPACE, raggio→RADIUS, ombra, type, icone SVG (no emoji), stati
(hover+focus-visible), media, default curati, a11y. Inoltre: box-model via `useBoxModel`,
default da **fonte unica**, **chiavi salvate INVARIATE**.
- Aggiorna la riga della tile in `TILE_PROGRESS.md` (✅/severità per punto) + 1 nota prima/dopo.
- Procedi a **batch per categoria** così emergono e si correggono le incoerenze tra sorelle.

## 4) Definition of Done (l'intero lavoro è finito SOLO quando…)
- [ ] Ogni `*Tile.vue` ha la sua riga in `TILE_PROGRESS.md` con tutti i punti spuntati o
      giustificati (severità 🟢 motivata).
- [ ] **Zero hex hardcoded** di colore nei componenti/config (grep: `#[0-9a-fA-F]{3,6}` →
      solo nei file token). Nessun `#6366F1` / `#1e87f0` / `#e8622a` residuo.
- [ ] **Zero emoji** come icona di default (grep emoji negli `*Tile.vue`/config).
- [ ] Ogni elemento interattivo ha **focus-visible**.
- [ ] Box-model via composable; default da fonte unica; **nessuna chiave salvata cambiata**
      (diff sulle option/meta = vuoto).
- [ ] Tile della stessa categoria condividono raggio, ombra, superficie, accento, scala type.

## 5) Guardrail (non rompere il prodotto)
- NON cambiare i nomi/chiavi salvati (margin_*, padding_*, border_radius, hover.*, ecc.):
  cambia solo la UI/resa. I template esistenti devono continuare a funzionare.
- NON introdurre `var(--olo-color-…)` con nomi che il `GlobalColorsPanel` non produce
  (vedi `TOKEN_MAPPING.md`): usa i ruoli reali o i token di SYSTEM.
- I file `prototype/` sono **riferimento**: riscrivi nello stack reale (Vue 3 + store del
  progetto), riusando i componenti UI esistenti — non incollare React/HTML.

## 6) Prompt da incollare in Claude Code
> Leggi `START_HERE.md` e segui il protocollo. Fai prima il setup (§1), poi enumera TUTTE
> le tile e crea `TILE_PROGRESS.md` (§2). Lavora per categoria applicando la checklist a
> OGNI tile (§3), aggiornando il tracker. Non considerare il lavoro finito finché la
> Definition of Done (§4) non è interamente verde. Rispetta i guardrail (§5): chiavi
> salvate invariate, nessun colore hardcoded, nessun nome di variabile inventato.
> Procedi a piccoli commit per categoria e mostrami il diff del primo batch (Button +
> tile essenziali) prima di continuare.
