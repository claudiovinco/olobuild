# Scheda Layout — spec redesign

Riferimento visivo: `reference/REFERENCE_layout.html` (confronto *Oggi → Coerente*).

## Problema (Oggi)
- Campi dimensione = **caselle vuote** senza unità né valore di riferimento.
- Icona **device ripetuta su ogni riga** (responsive per-campo).
- *Overflow* è una casella di testo anonima invece di un menù.

## Redesign (Coerente)
Stesso linguaggio di `FieldBox`: ogni dimensione è una **valbox con unità in linea**, con
segnaposto sensato. Niente device nel pannello.

| Riga | Controllo | Default / segnaposto | Unità |
|---|---|---|---|
| **Larghezza piena** | toggle (iOS, arancio) + micro-descrizione | on/off | — |
| **Larghezza** | valbox + selettore unità | `auto` | px · % · vw |
| **Larghezza massima** | valbox + selettore unità | es. `1200` | px · % · vw |
| **Altezza minima** | valbox + selettore unità | `auto` | px · vh · % |
| **Overflow** | select con icona | `Visibile` | — |

In più: **anteprima schematica** dei vincoli (width · max-width · min-height) come il peek
delle altre proprietà. Quando *Larghezza piena* è on, disabilita (greyed) il campo *Larghezza*.

## Contratto dati — INVARIATO
Mantieni le chiavi attuali del pannello Layout, qualunque siano i loro nomi reali nel repo
(es. `width`, `max_width`, `min_height`, `full_width`, `overflow`). Il selettore unità scrive
la stringa con suffisso come oggi (es. `"1200px"`, `"100%"`), **non** introdurre un campo unità
separato se oggi l'unità è inclusa nella stringa. Se invece oggi unità e valore sono già due
campi, mantieni quei due campi. **Non rinominare nulla.**

## Comportamenti da preservare
1. Selettore unità: stesse unità ammesse oggi per ciascun campo (non aggiungerne di nuove se il
   backend non le supporta).
2. *Larghezza piena*: stessa chiave booleana e stesso effetto attuale.
3. *Overflow*: stesse opzioni/`value` di oggi, solo presentate come menù con icona.
4. Responsive: dal wrapper di pagina, non dal pannello.

## Note
- Accento arancio `#e8622a` su toggle, focus valbox, selettore unità attivo.
- I segnaposto (`auto`, `Visibile`) sono **testo placeholder**, non valori salvati: se l'utente
  non tocca il campo, salva ciò che già salveresti oggi (probabilmente vuoto/`null`).
