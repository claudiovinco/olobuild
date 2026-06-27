# Inserter Elementi — spec redesign

Riferimento visivo: `reference/REFERENCE_inserter.html` (confronto *Oggi → Coerente*).
⚠️ Questo tocca il **chrome di navigazione** (rail categorie + griglia tile), **non** un field
dell'inspector. Nessun contratto dati di tile coinvolto — è UI di inserimento.

## Problema (Oggi)
1. **Etichette categoria troncate**: `Essenzi…`, `Marketi…`, `Interatti…`, `Navigaz…`. È
   navigazione primaria illeggibile.
2. **Header ambiguo**: pallino viola senza significato + un `20` che duplica il badge del rail.
3. **Tile ad altezze irregolari** (label su 1 o 2 righe) → griglia a denti di sega.
4. **Badge numerici** sul rail sembrano notifiche urgenti.
5. **Accento incoerente**: tab attiva sottolineata arancio, categoria attiva con pallino.
6. **Nessuna affordance di trascinamento** (le tile si draggano nel canvas).

## Redesign (Coerente)
| Area | Cambiamento |
|---|---|
| **Rail** | un filo più largo; **etichette intere** su max 2 righe; **niente numeri** sulle icone (si sovrapponevano) |
| **Categoria attiva** | **barretta arancio a sinistra** + icona arancio (stesso linguaggio della sottolineatura tab). Via il pallino |
| **Header** | testo parlante `Media · 20` + "trascina nel canvas". Via il dot viola e il numero fluttuante |
| **Griglia** | tile ad **altezza fissa**, icona + label centrate su 2 righe (clamp). Niente denti di sega |
| **Conteggi** | rimossi dal rail; il totale resta solo nell'header |
| **Drag** | cursore `grab`; hover con bordo+ombra arancio e translateY; stato "in trascinamento" = bordo arancio + icona piena |

## Cosa NON cambia
- Le **categorie**, i loro contenuti e i conteggi reali (solo la *presentazione* dei conteggi cambia).
- Il comportamento di **ricerca contestuale** ("Cerca in <categoria>…").
- Il **drag-and-drop** verso il canvas: si aggiunge solo l'affordance visiva, non la logica.
- Le **tab** Elementi / Struttura.

## Note implementative
- Altezza tile uniforme: riserva sempre lo spazio per 2 righe di testo, centrato, anche con 1 riga.
  Label con `-webkit-line-clamp: 2` per i nomi lunghi.
- Rail: label `font-size ~10px`, `line-height ~1.15`, `max-width` del contenitore; consenti il
  wrap su 2 righe invece del troncamento.
- Accento categoria attiva: barretta `3px` arancio a sinistra + icona arancio; nessun pallino.
- Drag: `cursor: grab` a riposo, `grabbing` durante il drag (gestito dal DnD esistente).
- Header: niente badge "notifica". Il conteggio è informativo, in linea col titolo.

## Accessibilità
- Categorie come pulsanti con `aria-current`/`aria-selected` sulla attiva (non solo colore).
- Tile draggabili con `role`/`aria-grabbed` coerenti col sistema DnD già in uso.
