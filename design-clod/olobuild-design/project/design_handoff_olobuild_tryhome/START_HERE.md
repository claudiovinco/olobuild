# ⛳ START HERE — Try home: 3 effetti nativi per OLObuild

Punto di ingresso unico per Claude Code. Tutti i file citati sono in questa cartella.
Obiettivo: **insegnare a OLObuild** i tre effetti della landing “Try” come capacità
riusabili — non CSS incollato in una pagina.

## 0) Ordine di lettura (prima di toccare codice)
1. `EFFECTS_SPEC.md` — la specifica dei 3 effetti, con i file reali e il codice da inserire.
2. `prototype/glowCSS.js` — il util pronto per il tipo di sfondo “Bagliori” (da portare in `src/utils/`).
3. `REFERENCE_effects.html` — il risultato atteso, *visto* (apri in un browser).

## 1) Contesto architetturale (già verificato sul repo `claudiovinco/olobuild@main`)
- Gli sfondi sono un sistema condiviso a 3 livelli, **da tenere allineati**:
  - `src/components/Builder/BackgroundControls.vue` — array `types`, `defaultBg`, blocchi `v-if`, overlay.
  - `src/composables/useBackgroundStyle.js` — `buildBgStyle()` + `bgInlineStyle` (canvas).
  - `includes/.../class-frontend-renderer.php` — copia PHP (es. `build_pattern_css()`).
- I pattern (`src/utils/patternCSS.js`) sono il **modello esatto** da imitare per i bagliori.
- Colori = ruoli globali del cliente (`var(--olo-color-primary)`, seed `#e1474f`).

## 2) Cosa costruire (in quest’ordine)
1. **Sfondo “Bagliori”** (`glow`) — vedi `EFFECTS_SPEC.md §1`. È il pezzo principale e mappa
   la richiesta “tipo di sfondo pagina globale”. Porta `glowCSS.js`, aggiungi il tipo, il
   blocco controllo, il ramo canvas e la copia PHP.
2. **Pallino “live” pulsante** — `EFFECTS_SPEC.md §2`. Feature della tile Badge/Etichetta +
   `@keyframes olo-pulse` globale.
3. **Animazione loop “Galleggiamento”** — `EFFECTS_SPEC.md §3`. Nuova famiglia loop accanto
   alle 36 d’ingresso; la sovrapposizione tile-su-immagine è già possibile (posizione assoluta).

## 3) Definition of Done (finito SOLO quando…)
- [ ] “Bagliori” disponibile come sfondo di **pagina e sezione**; preview = canvas = frontend.
- [ ] Gli aloni seguono il **primario globale** (cambi il token → cambiano i bagliori).
- [ ] Toggle “Stato live” sulla tile Badge → pallino con onda (Verde/Primario), reduced-motion ok.
- [ ] “Galleggiamento” selezionabile come animazione loop su qualsiasi tile; `anim_in` invariata.
- [ ] **Zero hex hardcoded** nei componenti (solo nei file token); **zero emoji**.

## 4) Guardrail (non rompere il prodotto)
- **Chiavi salvate: solo additive.** Nuove (`glow_*`, `badge_live*`, `anim_loop`); non
  rinominare/rimuovere `type`, `overlay_*`, `pattern_*`, `anim_in`, ecc. I template esistenti
  devono continuare a funzionare identici.
- Mantieni **allineate** le 3 rese dello sfondo (Vue preview, canvas, PHP). Una sola non basta.
- I file in `prototype/` sono riferimento/fonte: `glowCSS.js` va in `src/utils/` così com’è;
  il resto riscrivilo nello stack reale (Vue 3 + store del progetto, classi `mb-*`, `t()`),
  non incollare HTML/React.
- L’**arancio `#e8622a` del chrome** dell’inspector NON si tocca: questi effetti sono
  contenuto di pagina e usano i token del cliente.

## 5) Prompt da incollare in Claude Code
Vedi `PROMPT.txt`.
