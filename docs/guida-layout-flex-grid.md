# Guida al Layout in Olobuild

> Come disporre tile, colonne e sezioni usando **Flex** e **Grid** — con esempi visivi.

---

## La gerarchia di Olobuild

Prima di parlare di layout, devi avere chiara la struttura della pagina:

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 360" width="600" height="360" style="max-width:100%;font-family:system-ui,sans-serif">
  <rect x="10" y="10" width="580" height="340" rx="8" fill="#f8fafc" stroke="#cbd5e1" stroke-width="1.5"/>
  <text x="22" y="32" font-size="12" font-weight="700" fill="#475569" letter-spacing="0.5">SECTION</text>
  <rect x="22" y="48" width="556" height="290" rx="6" fill="#fef3c7" stroke="#f59e0b" stroke-width="1.5"/>
  <text x="34" y="68" font-size="11" font-weight="700" fill="#92400e" letter-spacing="0.5">ROW</text>
  <rect x="34" y="80" width="265" height="248" rx="4" fill="#dbeafe" stroke="#3b82f6" stroke-width="1.5"/>
  <text x="46" y="100" font-size="10" font-weight="700" fill="#1e40af" letter-spacing="0.5">COLUMN</text>
  <rect x="46" y="112" width="241" height="50" rx="3" fill="#fff" stroke="#6366f1" stroke-width="1.2"/>
  <text x="58" y="142" font-size="10" font-weight="600" fill="#3730a3">tile (headline)</text>
  <rect x="46" y="172" width="241" height="50" rx="3" fill="#fff" stroke="#6366f1" stroke-width="1.2"/>
  <text x="58" y="202" font-size="10" font-weight="600" fill="#3730a3">tile (content)</text>
  <rect x="46" y="232" width="241" height="50" rx="3" fill="#fff" stroke="#6366f1" stroke-width="1.2"/>
  <text x="58" y="262" font-size="10" font-weight="600" fill="#3730a3">tile (button)</text>
  <rect x="313" y="80" width="265" height="248" rx="4" fill="#dbeafe" stroke="#3b82f6" stroke-width="1.5"/>
  <text x="325" y="100" font-size="10" font-weight="700" fill="#1e40af" letter-spacing="0.5">COLUMN</text>
  <rect x="325" y="112" width="241" height="170" rx="3" fill="#fff" stroke="#6366f1" stroke-width="1.2"/>
  <text x="337" y="202" font-size="10" font-weight="600" fill="#3730a3">tile (image)</text>
</svg>

**Regola d'oro**: ogni livello controlla la disposizione dei propri **figli diretti**.

| Livello | Controlla la disposizione di… |
|---|---|
| Section | le righe al suo interno |
| Row     | le colonne al suo interno |
| Column  | i tile al suo interno |
| Tile    | i propri elementi interni (slide, voci, item) |

> **Errore tipico**: vuoi mettere due tile affianco e imposti "Riga inversa" sulla **Row**. La Row ha solo *colonne* come figli — se la colonna è una sola, niente cambia. Devi salire o scendere di un livello finché trovi il container che ha **i due tile** come figli diretti.

---

## Flex vs Grid in un colpo d'occhio

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 220" width="600" height="220" style="max-width:100%;font-family:system-ui,sans-serif">
  <text x="20" y="28" font-size="14" font-weight="700" fill="#1e40af">FLEX</text>
  <text x="20" y="44" font-size="11" fill="#64748b">una dimensione: riga oppure colonna</text>
  <rect x="20" y="58" width="50" height="140" rx="4" fill="#dbeafe" stroke="#3b82f6"/>
  <rect x="80" y="58" width="50" height="140" rx="4" fill="#dbeafe" stroke="#3b82f6"/>
  <rect x="140" y="58" width="50" height="140" rx="4" fill="#dbeafe" stroke="#3b82f6"/>
  <rect x="200" y="58" width="50" height="140" rx="4" fill="#dbeafe" stroke="#3b82f6"/>
  <text x="36" y="135" font-size="11" font-weight="600" fill="#1e40af">1</text>
  <text x="96" y="135" font-size="11" font-weight="600" fill="#1e40af">2</text>
  <text x="156" y="135" font-size="11" font-weight="600" fill="#1e40af">3</text>
  <text x="216" y="135" font-size="11" font-weight="600" fill="#1e40af">4</text>
  <text x="340" y="28" font-size="14" font-weight="700" fill="#92400e">GRID</text>
  <text x="340" y="44" font-size="11" fill="#64748b">due dimensioni: righe ed colonne</text>
  <rect x="340" y="58" width="80" height="65" rx="4" fill="#fef3c7" stroke="#f59e0b"/>
  <rect x="430" y="58" width="80" height="65" rx="4" fill="#fef3c7" stroke="#f59e0b"/>
  <rect x="520" y="58" width="60" height="65" rx="4" fill="#fef3c7" stroke="#f59e0b"/>
  <rect x="340" y="133" width="60" height="65" rx="4" fill="#fef3c7" stroke="#f59e0b"/>
  <rect x="410" y="133" width="80" height="65" rx="4" fill="#fef3c7" stroke="#f59e0b"/>
  <rect x="500" y="133" width="80" height="65" rx="4" fill="#fef3c7" stroke="#f59e0b"/>
  <text x="372" y="98" font-size="11" font-weight="600" fill="#92400e">A</text>
  <text x="462" y="98" font-size="11" font-weight="600" fill="#92400e">B</text>
  <text x="544" y="98" font-size="11" font-weight="600" fill="#92400e">C</text>
  <text x="362" y="173" font-size="11" font-weight="600" fill="#92400e">D</text>
  <text x="442" y="173" font-size="11" font-weight="600" fill="#92400e">E</text>
  <text x="532" y="173" font-size="11" font-weight="600" fill="#92400e">F</text>
</svg>

- **Flex** è pensato per disporre N elementi *in fila* (o in colonna). Bravo a gestire allineamenti, spazi tra elementi, ordinamento.
- **Grid** è pensato per disegnare una *tabella* dove ogni elemento occupa una o più celle. Bravo per layout complessi (dashboard, gallery non uniformi).

> **Si possono mixare?** Sullo stesso elemento no — `display` ha un solo valore. Ma un grid item può a sua volta essere un flex container, e viceversa.

---

## Flex Direction — come orientare la fila

Sul pannello "Layout Flex" di Olobuild il campo **Direzione** ha 4 opzioni:

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 350" width="600" height="350" style="max-width:100%;font-family:system-ui,sans-serif">
  <text x="20" y="24" font-size="12" font-weight="700" fill="#475569">row (default)</text>
  <rect x="20" y="34" width="260" height="60" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="30" y="44" width="60" height="40" rx="3" fill="#dbeafe" stroke="#3b82f6"/>
  <rect x="100" y="44" width="60" height="40" rx="3" fill="#dbeafe" stroke="#3b82f6"/>
  <rect x="170" y="44" width="60" height="40" rx="3" fill="#dbeafe" stroke="#3b82f6"/>
  <text x="54" y="69" font-size="11" font-weight="600" fill="#1e40af">1</text>
  <text x="124" y="69" font-size="11" font-weight="600" fill="#1e40af">2</text>
  <text x="194" y="69" font-size="11" font-weight="600" fill="#1e40af">3</text>
  <text x="240" y="69" font-size="14" fill="#3b82f6">→</text>
  <text x="320" y="24" font-size="12" font-weight="700" fill="#475569">row-reverse</text>
  <rect x="320" y="34" width="260" height="60" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="330" y="44" width="60" height="40" rx="3" fill="#fef3c7" stroke="#f59e0b"/>
  <rect x="400" y="44" width="60" height="40" rx="3" fill="#fef3c7" stroke="#f59e0b"/>
  <rect x="470" y="44" width="60" height="40" rx="3" fill="#fef3c7" stroke="#f59e0b"/>
  <text x="354" y="69" font-size="11" font-weight="600" fill="#92400e">3</text>
  <text x="424" y="69" font-size="11" font-weight="600" fill="#92400e">2</text>
  <text x="494" y="69" font-size="11" font-weight="600" fill="#92400e">1</text>
  <text x="540" y="69" font-size="14" fill="#f59e0b">←</text>
  <text x="20" y="124" font-size="12" font-weight="700" fill="#475569">column</text>
  <rect x="20" y="134" width="120" height="200" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="30" y="144" width="100" height="50" rx="3" fill="#dbeafe" stroke="#3b82f6"/>
  <rect x="30" y="200" width="100" height="50" rx="3" fill="#dbeafe" stroke="#3b82f6"/>
  <rect x="30" y="256" width="100" height="50" rx="3" fill="#dbeafe" stroke="#3b82f6"/>
  <text x="74" y="174" font-size="11" font-weight="600" fill="#1e40af">1</text>
  <text x="74" y="230" font-size="11" font-weight="600" fill="#1e40af">2</text>
  <text x="74" y="286" font-size="11" font-weight="600" fill="#1e40af">3</text>
  <text x="74" y="324" font-size="14" fill="#3b82f6">↓</text>
  <text x="180" y="124" font-size="12" font-weight="700" fill="#475569">column-reverse</text>
  <rect x="180" y="134" width="120" height="200" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="190" y="144" width="100" height="50" rx="3" fill="#fef3c7" stroke="#f59e0b"/>
  <rect x="190" y="200" width="100" height="50" rx="3" fill="#fef3c7" stroke="#f59e0b"/>
  <rect x="190" y="256" width="100" height="50" rx="3" fill="#fef3c7" stroke="#f59e0b"/>
  <text x="234" y="174" font-size="11" font-weight="600" fill="#92400e">3</text>
  <text x="234" y="230" font-size="11" font-weight="600" fill="#92400e">2</text>
  <text x="234" y="286" font-size="11" font-weight="600" fill="#92400e">1</text>
  <text x="234" y="324" font-size="14" fill="#f59e0b">↑</text>
  <text x="340" y="124" font-size="12" font-weight="700" fill="#475569">Promemoria</text>
  <text x="340" y="148" font-size="11" fill="#64748b">• reverse inverte solo l'ORDINE</text>
  <text x="340" y="166" font-size="11" fill="#64748b">  visivo, non il dato salvato</text>
  <text x="340" y="190" font-size="11" fill="#64748b">• row → leggi sinistra→destra</text>
  <text x="340" y="208" font-size="11" fill="#64748b">• column → leggi alto→basso</text>
  <text x="340" y="232" font-size="11" fill="#64748b">• su mobile, row potrebbe</text>
  <text x="340" y="250" font-size="11" fill="#64748b">  diventare column (responsive)</text>
</svg>

---

## Allineamento — giustificazione e allineamento verticale

Quando il container ha `display: flex`, due assi controllano dove vanno gli elementi:

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 260" width="600" height="260" style="max-width:100%;font-family:system-ui,sans-serif">
  <text x="20" y="24" font-size="13" font-weight="700" fill="#1e40af">Giustificazione (asse principale, orizzontale per row)</text>
  <text x="20" y="50" font-size="11" fill="#475569">flex-start</text>
  <rect x="20" y="58" width="160" height="40" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="28" y="66" width="20" height="24" fill="#3b82f6"/>
  <rect x="52" y="66" width="20" height="24" fill="#3b82f6"/>
  <rect x="76" y="66" width="20" height="24" fill="#3b82f6"/>
  <text x="220" y="50" font-size="11" fill="#475569">center</text>
  <rect x="220" y="58" width="160" height="40" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="265" y="66" width="20" height="24" fill="#3b82f6"/>
  <rect x="289" y="66" width="20" height="24" fill="#3b82f6"/>
  <rect x="313" y="66" width="20" height="24" fill="#3b82f6"/>
  <text x="420" y="50" font-size="11" fill="#475569">flex-end</text>
  <rect x="420" y="58" width="160" height="40" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="500" y="66" width="20" height="24" fill="#3b82f6"/>
  <rect x="524" y="66" width="20" height="24" fill="#3b82f6"/>
  <rect x="548" y="66" width="20" height="24" fill="#3b82f6"/>
  <text x="20" y="128" font-size="11" fill="#475569">space-between</text>
  <rect x="20" y="138" width="160" height="40" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="28" y="146" width="20" height="24" fill="#3b82f6"/>
  <rect x="90" y="146" width="20" height="24" fill="#3b82f6"/>
  <rect x="152" y="146" width="20" height="24" fill="#3b82f6"/>
  <text x="220" y="128" font-size="11" fill="#475569">space-around</text>
  <rect x="220" y="138" width="160" height="40" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="235" y="146" width="20" height="24" fill="#3b82f6"/>
  <rect x="290" y="146" width="20" height="24" fill="#3b82f6"/>
  <rect x="345" y="146" width="20" height="24" fill="#3b82f6"/>
  <text x="420" y="128" font-size="11" fill="#475569">space-evenly</text>
  <rect x="420" y="138" width="160" height="40" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="445" y="146" width="20" height="24" fill="#3b82f6"/>
  <rect x="490" y="146" width="20" height="24" fill="#3b82f6"/>
  <rect x="535" y="146" width="20" height="24" fill="#3b82f6"/>
  <text x="20" y="208" font-size="13" font-weight="700" fill="#92400e">Allineamento verticale (asse trasverso)</text>
  <text x="20" y="230" font-size="11" fill="#475569">flex-start (top)</text>
  <rect x="20" y="238" width="80" height="50" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="28" y="246" width="18" height="14" fill="#f59e0b"/>
  <rect x="50" y="246" width="18" height="22" fill="#f59e0b"/>
  <rect x="72" y="246" width="18" height="18" fill="#f59e0b"/>
  <text x="120" y="230" font-size="11" fill="#475569">center</text>
  <rect x="120" y="238" width="80" height="50" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="128" y="256" width="18" height="14" fill="#f59e0b"/>
  <rect x="150" y="252" width="18" height="22" fill="#f59e0b"/>
  <rect x="172" y="254" width="18" height="18" fill="#f59e0b"/>
  <text x="220" y="230" font-size="11" fill="#475569">flex-end (bottom)</text>
  <rect x="220" y="238" width="80" height="50" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="228" y="274" width="18" height="14" fill="#f59e0b"/>
  <rect x="250" y="266" width="18" height="22" fill="#f59e0b"/>
  <rect x="272" y="270" width="18" height="18" fill="#f59e0b"/>
  <text x="320" y="230" font-size="11" fill="#475569">stretch (default)</text>
  <rect x="320" y="238" width="80" height="50" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="328" y="246" width="18" height="42" fill="#f59e0b"/>
  <rect x="350" y="246" width="18" height="42" fill="#f59e0b"/>
  <rect x="372" y="246" width="18" height="42" fill="#f59e0b"/>
</svg>

**Nota su `column` direction**: gli assi si invertono. La *giustificazione* diventa verticale e l'*allineamento* diventa orizzontale.

---

## A capo (wrap) — quando i figli non ci stanno

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 200" width="600" height="200" style="max-width:100%;font-family:system-ui,sans-serif">
  <text x="20" y="24" font-size="13" font-weight="700" fill="#475569">nowrap (default) — stringono</text>
  <rect x="20" y="34" width="260" height="60" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="28" y="44" width="50" height="40" rx="3" fill="#dbeafe" stroke="#3b82f6"/>
  <rect x="82" y="44" width="50" height="40" rx="3" fill="#dbeafe" stroke="#3b82f6"/>
  <rect x="136" y="44" width="50" height="40" rx="3" fill="#dbeafe" stroke="#3b82f6"/>
  <rect x="190" y="44" width="50" height="40" rx="3" fill="#dbeafe" stroke="#3b82f6"/>
  <rect x="244" y="44" width="30" height="40" rx="3" fill="#fecaca" stroke="#ef4444"/>
  <text x="252" y="69" font-size="9" fill="#991b1b">!</text>
  <text x="320" y="24" font-size="13" font-weight="700" fill="#475569">wrap — vanno a capo</text>
  <rect x="320" y="34" width="260" height="140" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
  <rect x="328" y="44" width="56" height="40" rx="3" fill="#fef3c7" stroke="#f59e0b"/>
  <rect x="388" y="44" width="56" height="40" rx="3" fill="#fef3c7" stroke="#f59e0b"/>
  <rect x="448" y="44" width="56" height="40" rx="3" fill="#fef3c7" stroke="#f59e0b"/>
  <rect x="508" y="44" width="56" height="40" rx="3" fill="#fef3c7" stroke="#f59e0b"/>
  <rect x="328" y="92" width="56" height="40" rx="3" fill="#fef3c7" stroke="#f59e0b"/>
</svg>

> **Quando attivare wrap**: ogni volta che usi `max-width: 49%` (o simili) sui figli. Senza wrap, i figli si stringono per stare tutti sulla stessa riga.

---

## Casi pratici risolti

### Caso 1: due tile affiancate al 50%

**Più semplice**: usa il layout della Row.

1. Seleziona la **Row** che contiene i due tile
2. Tab Contenuto → **Layout** → `50 / 50`
3. Sposta i due tile, uno per colonna (drag&drop)

Nessun flex da configurare a mano: ogni colonna è già al 50%.

### Caso 2: tile affiancate dentro la stessa colonna

Quando i tile devono stare nella **stessa** colonna (es. li gestisci come un gruppo):

1. Seleziona la **Column** (parent dei tile)
2. Tab Contenuto → blocco **Layout Flex**:
   - Direzione: `Riga`
   - A capo: `Sì`
   - Gap orizzontale: `12` (a piacere)
3. Su ciascun tile → tab Stile → **Larghezza massima**: `49%`

Risultato: tile in fila, vanno a capo automaticamente se l'utente restringe la finestra.

### Caso 3: pulsante sempre a destra di un testo

1. Seleziona la **Column** che contiene `[testo, pulsante]`
2. Layout Flex:
   - Direzione: `Riga`
   - Giustificazione: `Spazio tra` *(così testo a sx, bottone a dx)*
   - Allineamento verticale: `Centro`

### Caso 4: card 3×3 sempre allineate

Quando vuoi una griglia 2D ordinata (3 colonne × 3 righe), usa la **Riga in modalità Grid**:

1. Seleziona la **Row**
2. Tab Contenuto → **Modalità**: `Grid`
3. **grid-template-columns**: `repeat(3, 1fr)`
4. Aggiungi colonne come al solito: ognuna occupa una cella.

Con questo approccio, **non** devi configurare il flex — il grid gestisce tutto.

---

## Errori comuni

### "Ho messo row-reverse ma non cambia nulla"
La direzione `row-reverse` inverte l'ordine **dei figli diretti**. Se il container ha un solo figlio, niente da invertire. Verifica di aver selezionato il livello giusto (es. la Column, non la Row, se vuoi invertire i tile).

### "max-width: 49% e le tile sono ancora una sotto l'altra"
`max-width` da solo non affianca: serve un parent **flex container**. Configura il "Layout Flex" della colonna parent con `Direzione: Riga`.

### "Su mobile tutto si rompe"
- Per le Row: attiva **Impila su mobile** (così le colonne diventano 100% sotto i 480px).
- Per le Column con flex orizzontale: aggiungi `A capo: Sì` così i figli vanno a capo invece di stringersi.

### "Vorrei spazi tra le card ma il gap non funziona"
Controlla se il container è in modalità grid: in quel caso usa `grid_gap`, non il flex gap.

---

## Cheat-sheet finale

| Voglio… | Imposta su… | Setting |
|---|---|---|
| 2 colonne 50/50 | Row | Layout: 50/50 |
| 3 card affiancate, larghezza fissa | Column con 3 tile | Flex direction: row, A capo: sì |
| Pulsante allineato a destra | Column | Justify: flex-end |
| Centrare verticalmente un tile | Column | Align items: center |
| Inversione ordine su mobile | Column | column-reverse (responsive) |
| Layout dashboard complesso | Row | Modalità: Grid |
| Card uguali di altezza | Column | Align items: stretch (default) |

---

**Versione plugin di riferimento**: Olobuild ≥ 3.52.17
