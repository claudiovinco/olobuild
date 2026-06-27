# Token mapping & caveats — come i colori delle tile si legano ai colori globali

Documento critico per evitare fraintendimenti. Riassume come il **GlobalColorsPanel**
genera realmente le variabili e come i default delle tile devono agganciarsi.

## Come funziona oggi (verificato su `GlobalColorsPanel.vue`)

- Il pannello salva una lista di colori; ognuno espone una variabile CSS
  **`var(--olo-color-<id>)`**.
- L'`<id>` è generato dalla **label** via `generateId(label)` (slug minuscolo).
- Palette **seed** di default (nuovo sito):

  | ruolo | id / variabile | valore seed |
  |------|----------------|-------------|
  | Primario | `--olo-color-primary` | `#e1474f` (ROSSO brand) |
  | Secondario | `--olo-color-secondary` | `#32d296` |
  | Accento | `--olo-color-accent` | `#faa05a` |
  | Scuro | `--olo-color-dark` | `#1a1a2e` |
  | Chiaro | `--olo-color-light` | `#f8f9fa` |
  | Testo | `--olo-color-text` | `#333333` |

  > **Decisione presa:** il primario seed è il **rosso brand OLObuild `#e1474f`** (non più
  > il blu `#1e87f0`). Va cambiato il default in `GlobalColorsPanel` (l'array seed di
  > `localColors`) e allineato il fallback di `GLOBAL.primary`. Secondario/accento seed
  > restano `#32d296`/`#faa05a` per ora — da rivedere se si vuole una palette seed
  > pienamente coordinata col rosso.

- Esistono **solo** questi 6 ruoli + eventuali colori custom (`--olo-color-colore-2`…).
  **NON esistono** `info/success/warning/error`, `surface`, `border`, `on-primary`,
  `primary-hover`, `surface-alt`, `text-soft`.

## La risposta alla domanda "i default non dovrebbero seguire i colori globali?"

**Sì, e lo fanno** — ma solo per i ruoli che il pannello produce davvero:

- ✅ **Legati al cliente:** `primary`, `secondary`, `accent`, `text`, `dark`, `light`
  → in `oloTileDefaults.js` sono l'oggetto **`GLOBAL`** (`var(--olo-color-…)`).
  Un Button su un sito con primario verde nasce **verde**, non rosso/blu.
- 🔒 **Di sistema (fissi):** neutri fini (`surface`, `border`, `text-soft`) → oggetto
  **`SYSTEM`**, costanti. I **semantici** (`info/success/warning/error`) sono ora **ruoli
  globali personalizzabili** (il cliente sceglie i fg; soft derivata via color-mix).

`resolveColor(userValue, token)` resta corretto: se l'utente non sceglie sulla tile,
eredita il token (globale o di sistema). Il meccanismo NON cambia — cambia solo *quali*
nomi di variabile sono reali.

## ⚠️ Fragilità da sistemare (id derivato dalla label)

`updateColorLabel()` rigenera l'`id` dalla label ad ogni modifica. Se l'utente rinomina
"Primario" → "Brand", la variabile passa da `--olo-color-primary` a `--olo-color-brand`
e **tutti i binding delle tile si rompono** (ricadono sul fallback).

**Raccomandazione:** disaccoppiare i **6 ruoli di sistema** dalla label — id stabile e
fisso (`primary`, `secondary`, …), label libera solo come etichetta visibile. Solo i
colori **custom** ottengono un id generato. Così `var(--olo-color-primary)` è garantito.

## Decisioni aperte (da confermare col cliente)

1. **§seed — RISOLTO: primario di default = rosso brand `#e1474f`.** Modificare l'array
   seed di `localColors` in `GlobalColorsPanel.vue` (primary → `#e1474f`) e tenere
   allineato il fallback di `GLOBAL.primary` in `oloTileDefaults.js`. Il cliente potrà
   comunque sovrascriverlo: i default tile seguiranno la sua scelta.
2. **§semantici — RISOLTO: personalizzabili dal cliente.** Aggiungere 4 ruoli
   (`info/success/warning/error`) al pannello + store. Il cliente sceglie solo i 4 **fg**;
   la tinta **soft** è DERIVATA via `color-mix(in srgb, var(--olo-color-X) 12%, #fff)` nei
   consumer (vedi `SYSTEM` in `oloTileDefaults.js`) — niente 8 colori da gestire.
3. **§on-primary — RISOLTO: calcolato per contrasto.** Lo store, quando il cliente cambia
   il primario, chiama `contrastOn(primary)` (luminanza relativa sRGB) e scrive
   `--olo-color-on-primary` = bianco o testo scuro. Così il testo dei Button resta
   leggibile su qualsiasi primario (rosso → bianco, giallo chiaro → scuro).
4. **Secondario/accento seed** coordinati col rosso: navy profondo `#16263d` + ambra
   `#f4a23b` (palette calda editoriale). Allineare l'array seed in `GlobalColorsPanel.vue`.

## Istruzioni per Claude Code

- Lega i default colore delle tile **solo** ai 6 ruoli globali (`GLOBAL` in
  `oloTileDefaults.js`). **Non** introdurre `var(--olo-color-info/surface/border/…)`:
  quei nomi non sono prodotti dal pannello → usa le costanti `SYSTEM`.
- Mantieni i fallback di `GLOBAL.*` allineati al **seed reale** del pannello.
- Prima di implementare, valuta la fix di **stabilità degli id** (§fragilità): senza,
  qualsiasi rinomina nel pannello rompe i binding.
