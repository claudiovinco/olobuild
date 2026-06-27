# NOVA — pacchetto per Claude Code

Contenuto:
- `NOVA - build spec.md` → la spec: contenuti, mappatura tile, e l'elenco delle
  TILE NUOVE / opzioni di stile da implementare.
- `blueprint/` → il sito di riferimento (HTML + CSS + JS). Serve a Claude Code per
  vedere la resa reale degli effetti (parallax, scramble, mouse-tilt 3D, ImgCompare,
  sticky-pin) da cui ricostruire il template OLObuild.

## Dove copiarlo
Metti questa cartella **dentro la root del repo `claudiovinco/olobuild`**, ad esempio:
`D:\TECNICA\olobuild\NOVA-per-claude\`
Così Claude Code ha sotto mano sia la spec sia il codice del plugin (da cui deve
dedurre il formato JSON di import).

## Cosa fa Claude Code
Genera il JSON importabile in OLObuild (Gestione Template → Importa). Quasi tutto è
componibile con tile ESISTENTI; servono solo 2-3 estensioni (vedi sezione
"🆕 NEW TILES" nella spec): colonna sticky-pinned, mesh background riusabile, grain
overlay + outline-text/blend-mode come opzioni di stile.

## Prompt da incollare (vedi anche il messaggio in chat)
Apri `NOVA - build spec.md` e segui "READ FIRST": deduci l'envelope JSON dal codice
del repo, conferma gli slug delle tile, e mostra PRIMA il piano (envelope + albero +
piano tile nuove) prima di generare il .json finale.
