# NOVA — Punch-list correttiva (mosaic.clod.eu)

Per Claude Code. Il template NOVA è **già importato e completo** (10 sezioni, contenuti
corretti). NON ricostruirlo: questa è una **passata di rifinitura** per riportare il
risultato al blueprint `NOVA - Creative studio.html` + `NOVA handoff/NOVA - build spec.md`.
Repo: `claudiovinco/olobuild@main`. Lavora sul template del sito `mosaic.clod.eu`.

Ordine: fai P1 (cambiano il 90% della percezione), poi P2, poi P3.

---

## P1 — Tipografia globale (la causa principale)
Online il testo è un sans sottile e piccolo: l'identità "editoriale oversized" è sparita.
- Imposta i **font globali** del tema (GlobalTypography / impostazioni pagina):
  display **Syne** (800), body **Hanken Grotesk**, label/mono **Space Mono**.
  Verifica che i `@font-face`/Google Fonts siano effettivamente caricati nel frontend.
- Riporta la **scala grande**:
  - Hero H1 ~ `clamp(64px, 12vw, 220px)`, peso 800, line-height ~0.95, uppercase.
  - Heading di sezione ("Selected work", "How we work", "What we do") ~ `clamp(40px, 6vw, 96px)`.
  - "DESIGN IN MOTION" è già grande: usalo come riferimento di scala per le altre.
- Le label eyebrow ("✦ Creative studio · Est. 2014…", "The method", "Rebrand case · Helios")
  in **Space Mono**, maiuscolo, tracking ampio, piccole.

**Done quando:** il titolo hero riempie quasi la larghezza dello schermo e il colpo d'occhio
è "rivista", non "documento".

## P1 — Parola scramble reintegrata nell'hero
Adesso "SHINE" è un blocco gigante **isolato** in una banda vuota sotto l'hero: è un errore di
layout.
- Il titolo hero deve essere **3 righe in un solo blocco**: `We make` / `brands` /
  **`MOVE`** dove l'ultima riga è il Text-FX **scramble** che cicla `MOVE, SHINE, SPEAK,
  SELL, GLOW`, colore **accent-2 indigo `#7c6cff`**, stessa dimensione del resto del titolo.
- Rimuovi il blocco "SHINE" autonomo e lo spazio vuoto che occupava.

**Done quando:** la parola che cambia è l'ultima riga del titolo, allineata e della stessa
scala, senza gap morto.

## P1 — Immagini: niente voragini nere
`blendtext` ("DESIGN IN MOTION", full-bleed) e le 4 card della gallery hanno `src` vuoto →
diventano grandi bande nere e l'`alt` spunta come testo.
- **Rilinka immagini reali** (anche temporanee/placeholder di qualità) su:
  - blendtext: still astratto motion, full-bleed dietro al testo (il mix-blend del titolo
    ha senso solo sopra un'immagine).
  - gallery: Helios / Pulse / Atlas / Forma.
  - before/after Helios (se non già).
- Finché un'immagine manca, mostra un **placeholder** (superficie neutra + icona), mai un
  vuoto che collassa. Dai a quelle sezioni un `min-height` sensato.

**Done quando:** sparite le bande nere vuote; "DESIGN IN MOTION" sta sopra un'immagine.

---

## P2 — Gallery espressiva
Online è una lista 2×2 di soli testi. Lo spec chiede **layout scattered + Mouse-Tilt 3D**
hover su ogni item (nativi).
- Attiva il layout *scattered* della ProGallery e l'hover **Mouse-Tilt / cursor tracking**.
- Card con immagine + titolo + anno + categoria + tag, non solo testo incolonnato.

## P2 — Nav
- Sparisci *"Select a menu in the Inspector panel"*: assegna il menu (logo ✦ NOVA · Work ·
  Studio · Services · CTA "Let's talk") oppure usa link inline nella navmenu.
- Applica `mix-blend-mode: difference` al wrapper della nav (style option) così inverte sopra
  l'hero chiaro/scuro.

## P2 — Ritmo verticale
- Riduci le bande vuote tra hero→marquee e attorno al blendtext (derivano da scramble
  staccato + immagini vuote: una volta risolti P1, ricontrolla i padding di sezione).

---

## P3 — Dettagli da blueprint
- **Outline text**: il ghost "NOVA" dietro l'hero e alcune parole della marquee in
  `fill:none` + `text-stroke` (già previsto nelle NEW TILES note 4).
- **Grana** film leggera su tutta la pagina (`mix-blend: overlay`, opacità bassa).
- **Aurora/mesh** come sfondo riutilizzabile su CTA finale e footer (oltre all'hero).
  → coincide col tipo di sfondo "Bagliori" del pacchetto `design_handoff_olobuild_tryhome/`:
  se lo implementi lì, riusalo qui.
- **How we work**: verifica che la colonna sinistra sia davvero **sticky-pinned** mentre i 4
  step scorrono (lo spec lo chiede esplicitamente).

---

## Guardrail (invariati)
- Nessun hex hardcoded nelle tile: colori dai ruoli globali (ink `#f1ece2`, bg `#0b0a0d`,
  accent `#ff5436`, accent-2 `#7c6cff`, accent-3 `#ffd23f`).
- Non rinominare chiavi salvate; Text FX e hover via gli helper condivisi.
- Non toccare l'arancio `#e8622a` del chrome dell'inspector (questo è contenuto di pagina).

## Riferimenti
- Look atteso: `NOVA - Creative studio.html` (blueprint).
- Intento per sezione: `NOVA handoff/NOVA - build spec.md`.
