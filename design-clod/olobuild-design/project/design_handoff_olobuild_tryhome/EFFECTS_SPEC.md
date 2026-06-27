# EFFECTS_SPEC — insegnare a OLObuild i 3 effetti della Try home

Obiettivo: rendere **nativi** in OLObuild i tre effetti della landing “Try”, ciascuno al
livello giusto del prodotto. NON sono CSS una-tantum incollati in una pagina: sono capacità
riusabili del builder.

| # | Effetto | Dove vive | Tipo di intervento |
|---|---------|-----------|--------------------|
| 1 | Bagliori rossastri | `BackgroundControls` (sezione **e** pagina) | **Nuovo tipo di sfondo** `glow` |
| 2 | Pallino “live” che pulsa | Tile **Badge/Etichetta** (+ opz. Icona) | **Feature di tile** + keyframe globale |
| 3 | Tile che galleggia su un’immagine | Sistema **animazioni** + posizione | **Nuova animazione loop** “Galleggiamento” |

Regole trasversali (dal pacchetto tile, valgono anche qui):
- **Colore → ruoli globali del cliente.** Il default dei bagliori e del pallino-brand è
  `var(--olo-color-primary)` (seed `#e1474f`). NON l’arancio `#e8622a` del chrome: questo è
  **contenuto di pagina**, non UI dell’inspector (regola CHROME vs CONTENUTO).
- **Chiavi salvate nuove, mai rinominate.** Aggiungi chiavi (`glow_*`, `badge_live*`,
  `anim_loop`), non toccare quelle esistenti (`type`, `overlay_*`, `pattern_*`…).
- **Doppia resa identica:** preview Vue (`BackgroundControls`) + canvas
  (`useBackgroundStyle`) + frontend (PHP `class-frontend-renderer.php`). Se tocchi una,
  aggiorna le altre due, come già fatto per i pattern.

---

## 1) Tipo di sfondo “Bagliori” (`glow`)

Gemello esatto del tipo `pattern`. File coinvolti (gli stessi 3 del pattern):

**A. `src/utils/glowCSS.js`** — porta il file da `prototype/glowCSS.js` (in questo pacchetto).
Espone `getGlowCSS(bg)` → `{ backgroundColor, backgroundImage, backgroundRepeat, backgroundSize }`
e `glowPresets`. Resa: aloni `radial-gradient` morbidi (niente `filter:blur`) + grana SVG
opzionale. Supporta colori **token** via `color-mix()` e hex/rgba via `rgba()`.

**B. `src/components/Builder/BackgroundControls.vue`**
1. Aggiungi il tipo nell’array `types` (dopo `pattern`):
   ```js
   { value: 'glow', label: 'Bagliori' },
   ```
2. Aggiungi le chiavi a `defaultBg`:
   ```js
   glow_base: '#0b0d12',
   glow_color: 'var(--olo-color-primary)',
   glow_color2: '',
   glow_preset: 'spread',
   glow_intensity: 62,
   glow_size: 78,
   glow_grain: true,
   ```
3. Aggiungi il blocco controllo (stesso stile dei `v-if` esistenti, classi `mb-*`):
   ```vue
   <div v-if="bg.type === 'glow'" class="mb-space-y-3">
     <!-- Preset posizione -->
     <div>
       <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Disposizione') }}</label>
       <select :value="bg.glow_preset || 'spread'" @change="updateField('glow_preset', $event.target.value)"
         class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-2 mb-py-1.5 mb-text-xs mb-text-white">
         <option v-for="p in glowPresets" :key="p.value" :value="p.value">{{ t(p.label) }}</option>
       </select>
     </div>
     <!-- Anteprima -->
     <div class="mb-h-16 mb-rounded mb-border mb-border-gray-600" :style="glowPreviewStyle"></div>
     <div class="mb-grid mb-grid-cols-2 mb-gap-2">
       <div>
         <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Colore alone') }}</label>
         <FieldColor :modelValue="bg.glow_color || 'var(--olo-color-primary)'" @update:modelValue="updateField('glow_color', $event)" />
       </div>
       <div>
         <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Colore base') }}</label>
         <FieldColor :modelValue="bg.glow_base || '#0b0d12'" @update:modelValue="updateField('glow_base', $event)" />
       </div>
     </div>
     <div>
       <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Intensità') }} ({{ bg.glow_intensity ?? 62 }}%)</label>
       <input type="range" :value="bg.glow_intensity ?? 62" @input="updateField('glow_intensity', parseInt($event.target.value))" min="10" max="100" step="2" class="mb-w-full" />
     </div>
     <div>
       <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Ampiezza') }} ({{ bg.glow_size ?? 78 }}%)</label>
       <input type="range" :value="bg.glow_size ?? 78" @input="updateField('glow_size', parseInt($event.target.value))" min="30" max="120" step="2" class="mb-w-full" />
     </div>
     <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
       <button @click="updateField('glow_grain', !(bg.glow_grain !== false))"
         :class="['mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-shrink-0', (bg.glow_grain !== false) ? 'mb-bg-primary-600' : 'mb-bg-gray-600']">
         <span :class="['mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform', (bg.glow_grain !== false) ? 'mb-left-5' : 'mb-left-0.5']"></span>
       </button>
       <span class="mb-text-xs mb-text-gray-300">{{ t('Grana film') }}</span>
     </label>
   </div>
   ```
4. Nello `<script setup>`: importa il util e aggiungi la preview computed:
   ```js
   import { getGlowCSS, glowPresets } from '@/utils/glowCSS';
   const glowPreviewStyle = computed(() => bg.value.type === 'glow' ? getGlowCSS(bg.value) : {});
   ```
   L’**overlay** esistente (`bg.type !== 'none'`) funziona già con `glow`: nessuna modifica.

**C. `src/composables/useBackgroundStyle.js`**
- In `buildBgStyle(bg)` aggiungi il ramo (vicino al ramo `pattern`):
  ```js
  if (bg.type === 'glow') {
    return getGlowCSS(bg);
  }
  ```
  e in cima: `import { getGlowCSS } from '@/utils/glowCSS';`
- In `bgInlineStyle` aggiungi `glow` agli inline (come `pattern`):
  ```js
  if (bg.type === 'glow') return buildBgStyle(bg);
  ```

**D. PHP `includes/.../class-frontend-renderer.php`**
- Esiste già `build_pattern_css()` (vedi commento in `patternCSS.js`). Crea il gemello
  `build_glow_css( $bg )` che produce **la stessa** stringa `background-color` +
  `background-image` (radial-gradient + grana). Richiamalo nello switch del background dove
  oggi gestisci `pattern`. Stesso supporto colore: se il valore inizia con `var(` o
  `color-mix(` → usa `color-mix(in srgb, … X%, transparent)`; altrimenti `rgba()`.

**DoD effetto 1**
- [ ] Selezionando “Bagliori” come sfondo di **pagina** (PageSettings) e di **sezione** la
      preview, il canvas e il frontend mostrano lo **stesso** risultato.
- [ ] Cambiando il **primario globale**, gli aloni cambiano colore (token, non hex fisso).
- [ ] Overlay e altri tipi di sfondo continuano a funzionare (nessuna regressione).

---

## 2) Pallino “live” che pulsa — feature di tile

Non esiste in OLObuild: va aggiunto come **opzione** della tile Badge/Etichetta (e, se utile,
Icona/Bottone), più un keyframe registrato **una volta**.

1. **Individua la tile** Badge/Etichetta in `src/components/Tiles/` (es. `BadgeTile.vue` /
   `LabelTile.vue`) e il suo config in `src/config/elements/`.
2. **Nuove chiavi** (additive): `badge_live` (bool, default `false`),
   `badge_live_color` (`'success' | 'primary'`, default `'success'`).
3. **Markup**: quando `badge_live` è on, anteponi al testo:
   ```html
   <span class="olo-live-dot" :class="{ 'is-brand': badge_live_color === 'primary' }" aria-hidden="true"></span>
   ```
4. **CSS** (registralo una volta nel foglio frontend **e** nel CSS del canvas builder, non
   per-tile):
   ```css
   .olo-live-dot{width:8px;height:8px;border-radius:50%;background:var(--olo-color-success,#22c55e);
     position:relative;flex:none;display:inline-block}
   .olo-live-dot.is-brand{background:var(--olo-color-primary)}
   .olo-live-dot::after{content:"";position:absolute;inset:-4px;border-radius:50%;
     border:1px solid currentColor;color:var(--olo-color-success,#22c55e);opacity:.6;
     animation:olo-pulse 1.8s ease-out infinite}
   .olo-live-dot.is-brand::after{color:var(--olo-color-primary)}
   @keyframes olo-pulse{0%{transform:scale(.6);opacity:.7}100%{transform:scale(2);opacity:0}}
   @media (prefers-reduced-motion: reduce){ .olo-live-dot::after{animation:none} }
   ```
5. **Inspector**: aggiungi un toggle “Stato live” + select colore (Verde/Primario) nel
   pannello Contenuto della tile, con `t()` sulle label.

**DoD effetto 2**
- [ ] Toggle “Stato live” mostra il pallino con onda, in preview e frontend.
- [ ] Colore onda commutabile Verde↔Primario via token.
- [ ] `prefers-reduced-motion` rispettato. Nessuna emoji.

---

## 3) Tile che galleggia su un’immagine — animazione loop “Galleggiamento”

Si compone di due capacità, una **nuova** e una **già esistente**:

**3a — Nuova animazione LOOP (la parte da aggiungere)**
Oggi OLObuild ha **36 animazioni d’ingresso** (one-shot all’entrata in viewport). Il
galleggiamento è invece **continuo/loop**. Individua il registro delle animazioni (l’elenco
usato dalla sezione “Animazioni” del `BuilderInspector` e applicato dal renderer) e aggiungi
una piccola famiglia **“Loop”** con almeno:
```css
@keyframes olo-float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
@keyframes olo-float-rot { 0%,100%{transform:translateY(0) rotate(-4deg)} 50%{transform:translateY(-12px) rotate(-4deg)} }
```
Applicazione: `animation: olo-float 4.5s ease-in-out infinite;` con parametri esposti
(durata, ampiezza px, ritardo). Rispetta `prefers-reduced-motion: reduce` (anima → none).
Chiave salvata nuova, es. `anim_loop: 'float' | 'none'` (separata da `anim_in` esistente, che
NON va toccata).

**3b — Sovrapposizione (già possibile, da documentare)**
La “tile flottante sopra il builder” = una tile **Immagine** (screenshot) con sopra una tile
**Badge/Icona** posizionata in `absolute` tramite i controlli **Posizione** (Avanzate). Niente
codice nuovo: è composizione. La tile in overlay riceve l’animazione loop “Galleggiamento”.

**DoD effetto 3**
- [ ] “Galleggiamento” selezionabile come animazione loop su qualsiasi tile.
- [ ] Una tile in overlay assoluto su un’immagine galleggia in modo fluido e in loop.
- [ ] `anim_in` (ingresso) resta invariata e indipendente; reduced-motion rispettato.

---

## Riferimento visivo
Apri `REFERENCE_effects.html` in un browser: mostra i 3 effetti col primario del cliente.
Cambia `--olo-color-primary` in cima al file per verificare che tutto segua il token.
`prototype/glowCSS.js` è la **fonte** del tipo di sfondo: portalo in `src/utils/` così com’è.
