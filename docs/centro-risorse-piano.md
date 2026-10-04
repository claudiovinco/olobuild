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
   - **Novità**: `CHANGELOG.md` nel pacchetto, scritto per chi usa Olobuild. La bacheca mostra le ultime
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

**Fase 1 (versione 1.4.500)**

- Barra delle icone riparata: compare solo a pannello compresso, ogni icona riapre la sua sezione.
- `CHANGELOG.md` dalle versioni 1.4.481-1.4.500.
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
- Ogni lotto che cambia un'interfaccia descritta aggiorna la guida relativa e il `CHANGELOG.md`.

## 5. Difetti trovati scrivendo le guide

Le guide descrivono solo ciò che agisce. Questi difetti restano da correggere, e le guide li segnalano
come «in revisione» dove l'utente li incontrerebbe.

1. **La full-page cache non si svuota al salvataggio nel builder.** `do_action( 'olo_template_saved' )`
   non viene mai lanciato. Ne dipendono anche l'invalidazione del Critical CSS, il nuovo apprendimento
   del subset UIkit e la pulizia dei vecchi file `olo-{tpl}-*.css`. È la correzione più urgente.
2. **SEO globale**: su 11 controlli agiscono solo Separatore e Sitemap XML. Il Vue salva chiavi che il
   PHP non legge (`og_image` contro `og_default_image`, `twitter_handle` contro `twitter_user`, `card_type`
   contro `twitter_card_type`, `advanced.schema.type` contro `titles.kg_type`). Il link «Vedi sitemap»
   porta alla sitemap di WordPress, non a quella di Olobuild.
3. **Performance & Cache**: Defer JavaScript (handle sbagliati), Durata cache, Sezioni above-the-fold,
   Preload font custom, Video facade YouTube/Vimeo, fetchpriority hero image, Lazy loading immagini
   below-fold non agiscono. «Svuota tutto» svuota solo il Critical CSS. Le statistiche (pagine in
   cache, score) non misurano ciò che dicono. Se c'è già un altro drop-in di cache, la full-page cache
   non si attiva e non lo dice.
4. **White Label**: «Nascondi changelog & roadmap» e «Link Documentazione custom» non vengono salvati.
   Con la pagina Guida possono agire davvero: nascondere le Novità, puntare la Guida a un indirizzo
   proprio.
5. **Configurazione**: la scheda «Breakpoint responsive» salva un'opzione che nessuno legge. La scheda
   Popup parla di template «di tipo Popup», che non esiste.
6. **Bacheca**: «Sfoglia template · Pronti all'uso» apre i template del sito, non una libreria. Il
   template 404 non ha un'interfaccia per attivarlo. La home di partenza del wizard nomina uno «Style
   Manager» che non esiste.
7. **Redirect & 404**: IndexNow richiede il file `<chiave>.txt` nella radice, che Olobuild non crea.
8. **Stock media**: il link di aiuto per le chiavi punta a olotheme.com/docs/stock-media-keys/ (404).

## 6. olotheme.com (sito in produzione, da fare su richiesta)

- Pagine /docs, /docs/onboarding, /docs/templates, /docs/seo, /docs/performance, /support, /community,
  /roadmap: date «Q3 2026» scadute, emoji, link a github.com/olotheme (404).
- /olobuild-manuale/: aggiornare i dati tecnici o ritirarlo.
- /risorse/: i PDF promessi «al lancio» non ci sono.
