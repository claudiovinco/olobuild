# OLOtheme — Pagina di lancio "solo tile": cosa manca a OLObuild

> 📎 **Companion:** per gli effetti "wow" speciali dei 50 temi (cursore magnetico, goo, scroll-pin,
> physics, ASCII, font variabile, water, scratch, particelle, presence/leaderboard…) e per la loro
> conversione in tile reali, vedi **`PER-CLAUDE-CODE-tile-speciali.md`** — classifica ogni effetto in
> `già coperto / da estendere / nuovo`, con schede operative e screenshot di riferimento.


La pagina `manutenzione/json/olotheme-presto-online-tiles.json` è costruita **solo con tile reali**
(`section · image · badge · headline · newsletter · icon`). Ecco cosa NON è stato possibile rendere 1:1
con i tile attuali e che varrebbe la pena implementare nel builder. Sono richieste additive — nessuna
rompe le chiavi salvate esistenti.

## 1. Versione CHIARA dei loghi ufficiali  ⭐ priorità · MANCANTE
I loghi ufficiali in `assets/logos/` sono scuri su trasparenza: su fondo scuro spariscono.
Nella cartella **non esiste** una versione bianca/chiara dei marchi principali
(`olotheme`, `olobuild`, `ololang`, `olotour`, `olotutor`): ci sono solo le varianti `-w` dei
sotto-marchi OLObooking. Nel template è stato usato il logo **ufficiale scuro immutato**
(nessun logo viene generato).
**Serve dal cliente/brand**: i file ufficiali `*-orizz-w.png` (versione chiara) di OLOtheme e dei
prodotti, da caricare in Media Library. In alternativa, un'opzione `recolor`/`filter_invert` sul
**tile Image** — ma la strada giusta è avere gli asset ufficiali chiari.

## 2. Sfondo "aurora" animato sulla Sezione
Il fondo wow della pagina (blob di colore sfumati che derivano lentamente) non esiste come opzione di
sezione. Oggi la sezione fa colore/gradiente/immagine/video statici.
**Serve**: tipo sfondo **"Aurora"** sulla sezione (2–4 colori, blur, velocità) — come il preset
`gradient-aurora` che già esiste sui tile testuali, ma a livello di **background di sezione**.

## 3. Colore/gradiente per-parola dentro un titolo
Nel design "Cinque prodotti" è in gradiente e "Nessuna catena" in lime, **nella stessa frase**.
Il tile Headline applica `gradient_text` o `heading_color` all'**intero** titolo, non a singole parole.
Ho aggirato spezzando il titolo in righe-headline separate.
**Serve**: supporto a span colorati/gradiente per-parola (es. markup `**parola**` o segmenti con colore)
nel tile Headline / Hero.

## 4. Elementi decorativi fluttuanti / parallax
I "blocchi" che galleggiano sullo sfondo dell'hero non hanno equivalente tile.
**Serve (nice-to-have)**: un tile/decoratore "shape" posizionabile in assoluto con animazione float,
oppure un layer decorativo di sezione.

## 5. Indicatore "scorri" (scroll cue)
Piccola freccia animata a fondo hero. Non incluso.
**Serve (nice-to-have)**: micro-tile "scroll indicator", o un'icona con animazione `bounce` ancorabile
in basso alla sezione.

## Già coperto bene dai tile (nessun intervento)
- **Vetro / glass**: preset `glass-frosted` (badge), `glass-floating` (newsletter), `glass-frame` (image),
  `glass-overlay` (headline/hero). 👍
- **Form email**: tile **Newsletter** completo (provider, anti-spam, privacy, colori). 👍
- **Chip "in arrivo" con pallino pulsante**: tile **Badge** con `badge_live`. 👍
- **Gradiente testo (intero titolo)**: `gradient_text` + `gradient_from/to`. 👍

> Nota: appena esistono i loghi bianchi ufficiali (punto 1), sostituire nel template le immagini base64
> con gli ID/URL della Media Library, così il template resta leggero e i marchi restano aggiornati.
