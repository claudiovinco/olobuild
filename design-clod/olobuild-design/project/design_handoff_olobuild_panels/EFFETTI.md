# Sezione Effetti — spec redesign

Riferimento visivo: `reference/REFERENCE_effetti.html` (confronto *Oggi → Coerente*).
È il pannello più denso del builder.

## Problema (Oggi)
- **Slider di colori diversi**: rossi (opacità) e blu (trasformazione, alfa, filtro). Incoerenti.
- Valori a volte in box, a volte inline; **unità mancanti**; campi vuoti.
- **Due "occhi"** di visibilità con stili/posizioni diverse.
- *Transizione hover* mescolata al resto.

## Redesign (Coerente)
Un solo sistema. In cima al pannello: **selettore Normale/Hover** (segmentato navy) + eventuale
occhio di sezione. Niente switch device (sta a livello pagina).

### Sotto-sezioni (ognuna con occhio di visibilità allineato a destra)
| Sezione | Righe | Unità |
|---|---|---|
| **Ombra** | preset (select) | — |
| **Opacità** | slider + valbox | `%` (default 100) |
| **Trasformazione** | Rotazione, Scala (slider+valbox); Sposta X/Y, Skew X/Y (valbox grid); Origine (select) | `°`, `%`, `px` |
| **Ombra testo** | Oriz./Vert./Sfoc. (valbox grid) + colore (swatch+hex+token) + Alfa (slider) | `px`, `%` |
| **Filtro sfondo** (glassmorphism) | Sfocatura, Luminosità, Saturazione (slider+valbox) | `px`, `%` |
| **Maschera** | Forma (select) | — |

Anteprima effetti in fondo (card schematica).

### Pannello separato: Transizione hover (globale)
Resta un **pannello a sé**, non una sotto-sezione di Effetti:
- Durata → slider + valbox `ms` (default 300)
- Easing → select (default `Ease`)

## Regole UI chiave
1. **Uno slider solo**: tutti gli slider usano lo stile arancio chrome. Eliminare il mix rosso/blu.
2. **Unità ovunque** in valbox: `°` (rotazione/skew), `%` (opacità/scala/alfa/luminosità/
   saturazione), `px` (sposta/ombra-testo/sfocatura), `ms` (durata).
3. **Segnaposto** su X/Y, Skew, Ombra testo, Maschera, Easing → `0 / Nessuna / Ease`.
4. **Un solo stile di occhio**, allineato a destra di ogni sotto-sezione attivabile, con
   `aria-pressed`.
5. **Default sensati**: Opacità 100%, Scala 100%, Luminosità/Saturazione 100%.

## Contratto dati — INVARIATO
Mantieni TUTTE le chiavi attuali degli effetti (opacity, transform rotate/scale/translateX/
translateY/skewX/skewY/origin, text-shadow h/v/blur/color/alpha, backdrop blur/brightness/
saturation, mask, e per la transizione: durata/easing) **con i loro nomi reali nel repo**.
Il redesign è puramente di presentazione. **Nessun rename.**

> ⚠️ Etichette: nel mockup ho usato nomi leggibili (`Sposta X/Y`, `Oriz./Vert./Sfoc.`). Sono
> **solo label UI** (via `t()`), non chiavi. Se preferite le sigle originali, cambiate solo la
> stringa visualizzata — la chiave salvata resta quella di oggi.

## Comportamenti da preservare
- Le sezioni attivabili con l'occhio mantengono il loro flag di visibilità esistente.
- Hover: il selettore Normale/Hover è chrome; scrive sulle chiavi hover già presenti, non ne crea.
- Range/step degli slider invariati rispetto a oggi.
