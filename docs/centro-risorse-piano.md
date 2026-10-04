# Centro risorse: verifica e piano

Documento di lavoro del 4 ottobre 2026, sulla versione 1.4.499. Descrive lo stato del Centro risorse
della bacheca, la struttura che lo sostituisce e il piano dei materiali.

## 1. Verifica

| Elemento | Cosa mostrava | Dove portava | Stato |
|---|---|---|---|
| Barra delle 4 icone | Razzo, play, punto di domanda, campanella in riquadri grigi sotto il titolo | Nessuna azione | Guasto. Il markup usa la classe `rail-mini`, CSS e JS cercano `.olo-rail-mini`: la barra resta visibile da aperta, con lo stile grezzo del browser, e i clic non fanno niente. Da compressa non compare. |
| Cosa c'è di nuovo | «v1.4.499 · 4 Ott · Novità · Vedi changelog completo nel repository» | Nessun link | Segnaposto. Il plugin non ha un `CHANGELOG.md`, e la data è quella del giorno in cui si apre la pagina. Il repository non è raggiungibile dalla bacheca. |
| Impara Olobuild | 4 «video» con durata (1:02, 4:18, 3:45, 5:30) | olotheme.com/docs/onboarding, templates, seo, performance | Promessa falsa. I video non esistono. Le 4 pagine dicono «In arrivo · Q3 2026», data già passata, e ripetono lo stesso blocco con emoji. |
| Documentazione | Link | olotheme.com/docs | Segnaposto «In arrivo · Q3 2026» con emoji. Lo stesso link sta nell'icona (?) della barra in alto. |
| Apri ticket | Link | olotheme.com/support | Il sistema di ticket non esiste. La pagina rimanda a info@olotheme.com. |
| Community | Link | olotheme.com/community | Non esiste («Q4 2026»). Rimanda a github.com/olotheme, che dà 404. |
| Roadmap | Link | olotheme.com/roadmap | Ferma a maggio: «Q3 2026 · in corso», prezzi di lancio, tappe superate. |

Materiali che esistono già ma che il Centro risorse non usa:

- `docs/manuale-olobuild.md` nel repository: 1.009 righe, utile come traccia, in parte superato.
- olotheme.com/olobuild-manuale/ (13 luglio): manuale base in 5 capitoli, con dati tecnici non più veri (Vite 5, drag and drop Pragmatic, conteggi delle tile).
- olotheme.com/risorse/: tre schede di PDF «in finalizzazione», scaricabili «al lancio».

## 2. Struttura

Tre livelli, ognuno con un compito solo.

1. **Nel plugin**, sempre disponibile e legato alla versione installata.
   - **Novità**: `includes/guide/novita.txt` nel pacchetto (formato Markdown), scritto per chi usa Olobuild. La bacheca mostra le ultime
     2 versioni, la pagina Guida le mostra tutte.
   - **Guida**: pagina della shell (stessa barra in alto, stessa sotto-navigazione) con le guide scritte.
     Le guide sono file del pacchetto (`guide/it/*.php`), una per argomento, e dicono la versione su cui
     sono state verificate.
   - **Supporto**: un'email già compilata con versione di Olobuild, WordPress, PHP e indirizzo del sito,
     che l'utente legge e invia dal proprio programma di posta.
2. **Sul sito olotheme.com**, pubblico.
   - /docs pubblica le stesse guide del plugin, dalla stessa fonte, in modo che non divergano.
   - /support descrive il canale vero (email) finché non c'è un sistema di ticket.
   - /roadmap si aggiorna o si toglie. /community si toglie finché non esiste.
3. **Video**, solo quando esistono, girati sulla versione corrente e con la durata vera.

Nel Centro risorse compare solo ciò che esiste. Una voce senza destinazione vera non si mostra.

## 3. Piano dei materiali

**Fase 1 (versione 1.4.501)**

- Barra delle icone riparata: compare solo a pannello compresso, ogni icona riapre la sua sezione.
- Registro delle novità (`includes/guide/novita.txt`) dalle versioni 1.4.481-1.4.501.
- Pagina Guida con indice e 4 guide: Primi passi, Template, SEO e Open Graph, Prestazioni.
- Centro risorse e icona (?) della barra in alto puntano alla Guida. Community e Roadmap escono finché
  non esistono.

**Fase 2**

Guide d'uso: Palette e stili globali, Header, footer e megamenu, Contenuti dinamici, Form, Popup,
Importa ed esporta, Temi pronti, WooCommerce, Assistente AI.

**Fase 3**

Guide per sviluppatori: struttura di una tile, hook e filtri, REST `olobuild/v1`. Poi il sito /docs
alimentato dagli stessi file e i video.

## 4. Regole per i materiali

- Si scrive solo ciò che il codice fa nella versione corrente, con le etichette esatte dell'interfaccia.
- Registro professionale: frasi brevi, niente punto e virgola, numeri in cifre, niente promesse
  («punteggio 100») né date di lancio.
- Niente emoji. Le icone vengono dal set SVG della shell. Le illustrazioni sono schemi semplici in HTML
  e CSS, con i colori della shell.
- Ogni lotto che cambia un'interfaccia descritta aggiorna la guida relativa e il registro delle novità.

## 5. Difetti trovati scrivendo le guide (corretti nella 1.4.502)

Le guide descrivono solo ciò che agisce. Questi difetti sono stati corretti nella 1.4.502, e le guide
non li segnalano più come «in revisione».

1. **Full-page cache e salvataggio nel builder.** `Olobuild_Database::notifica()` lancia
   `olo_template_saved` a ogni creazione, modifica ed eliminazione (anche da import ed export): si
   svuotano full-page cache, Critical CSS, subset UIkit, cache di Autoptimize e file `olo-{tpl}-*.css`,
   e parte l'avviso IndexNow.
2. **SEO globale**: la scheda scrive le chiavi che `Olobuild_Seo_Head` legge (`Olobuild_Seo_Settings::normalizza()`
   riprende quelle vecchie). Titoli senza separatore finale, un solo canonical e un solo meta robots,
   «Robots default» = `blog_public`, sitemap di WordPress senza le pagine in noindex. Via «Lingua sito»
   e «Auto-ping», che non facevano niente.
3. **Performance & Cache**: Defer JavaScript, Durata cache, Sezioni above-the-fold, Preload font custom
   e fetchpriority hero image agiscono; Video facade e Lazy loading immagini below-fold tolti (già
   fatti dalle tile). «Svuota tutto» svuota tutto, le statistiche contano pagine, file e Critical CSS,
   e la scheda dice perché la full-page cache non parte.
4. **White Label**: ogni campo agisce da solo. Novità nascoste da bacheca, pannello e Guida;
   documentazione dell'agenzia al posto delle guide; email del supporto; commenti col nome Olobuild
   tolti dal codice del sito.
5. **Configurazione**: «Breakpoint responsive» è diventata «Dispositivi dell'editor» (sceglie i
   dispositivi della barra del builder; le larghezze sono quelle fisse del renderer). Testi della
   scheda Popup corretti.
6. **Bacheca**: «I tuoi template» al posto di «Sfoglia template · Pronti all'uso»; il template 404 si
   attiva dalla barra del builder; il wizard porta a «Palette & Stili».
7. **Redirect & 404**: Olobuild serve il file `<chiave>.txt` di IndexNow e lo indica con `keyLocation`.
8. **Stock media**: ogni servizio porta alla sua pagina per le chiavi.

## 6. olotheme.com (sito in produzione, da fare su richiesta)

- Pagine /docs, /docs/onboarding, /docs/templates, /docs/seo, /docs/performance, /support, /community,
  /roadmap: date «Q3 2026» scadute, emoji, link a github.com/olotheme (404).
- /olobuild-manuale/: aggiornare i dati tecnici o ritirarlo.
- /risorse/: i PDF promessi «al lancio» non ci sono.
