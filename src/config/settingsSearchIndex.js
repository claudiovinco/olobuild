// ─────────────────────────────────────────────────────────────────────
// GENERATO da scripts/build-settings-search-index.cjs (hook buildStart di
// vite.config.admin.js). NON modificare a mano: rigenerare con
//   node scripts/build-settings-search-index.cjs
// Indice dei campi della Configurazione per la ricerca a livello di campo.
// ─────────────────────────────────────────────────────────────────────
export const SETTINGS_FIELD_INDEX = {
	"colori": [
		{
			"kind": "section",
			"label": "Preset di stile",
			"hint": "Un punto di partenza con un click. Applica la palette completa del preset ai ruoli qui sotto."
		},
		{
			"kind": "section",
			"label": "Genera palette",
			"hint": "Parti da uno o due colori e ottieni una palette armonica con le regole della teoria del colore."
		},
		{
			"kind": "section",
			"label": "Colori del brand",
			"hint": "Ogni colore ha un ruolo. Cambialo e ovunque sul sito si aggiorna di conseguenza."
		},
		{
			"kind": "section",
			"label": "Neutri & superfici",
			"hint": "Testo, sfondi, sezioni muted e bordi. La base neutra di tutto il sito."
		},
		{
			"kind": "section",
			"label": "Colori globali extra",
			"hint": "Swatch riutilizzabili oltre ai ruoli (accenti, colori secondari del brand). Disponibili come token --olo-color-{id} e nel selettore colore delle tile."
		},
		{
			"kind": "section",
			"label": "Scala neutri",
			"hint": "Sfumature grigie utilizzate per testi, bordi, sfondi e stati disabilitati."
		},
		{
			"kind": "field",
			"label": "Tinta neutri",
			"hint": "Scegli la sfumatura di base. I 7 livelli vengono generati automaticamente.",
			"section": "Scala neutri"
		},
		{
			"kind": "section",
			"label": "Modalità dark",
			"hint": "Configura come la palette si adatta automaticamente al tema scuro."
		},
		{
			"kind": "field",
			"label": "Abilita modalità dark",
			"hint": "Mostra il selettore dark/light nell'header del sito.",
			"section": "Modalità dark"
		},
		{
			"kind": "field",
			"label": "Strategia di inversione",
			"hint": "Come generare i colori scuri.",
			"section": "Modalità dark"
		},
		{
			"kind": "section",
			"label": "Versioni dello stile",
			"hint": "Com'era lo stile del sito prima di ogni import di tema o del sito, salvataggio degli stili e ripristino dei predefiniti: colori, tipografia, set tipografici e font caricati, header, footer e pagina 404 attivi, cursore e mirino, pagina iniziale e il template delle pagine che l'import ha riusato. Non tornano i template e le pagine creati dall'import, né il testo delle pagine riusate dall'import del sito. Restano le ultime 10, e sempre quella di prima dell'ultimo import."
		}
	],
	"tipografia": [
		{
			"kind": "section",
			"label": "Coppia di font",
			"hint": "Display per titoli, body per testo corrente. Tieni 2 famiglie max per buona leggibilità."
		},
		{
			"kind": "section",
			"label": "Scala tipografica",
			"hint": "Ratio matematico tra le grandezze. Anteprima live a destra."
		},
		{
			"kind": "field",
			"label": "Dimensione base",
			"hint": "Punto di partenza, di solito 16px per il corpo del testo.",
			"section": "Scala tipografica"
		},
		{
			"kind": "field",
			"label": "Ratio scala",
			"hint": "Più alto = differenza più marcata fra body e h1.",
			"section": "Scala tipografica"
		},
		{
			"kind": "field",
			"label": "Interlinea body",
			"hint": "Altezza riga del testo corrente.",
			"section": "Scala tipografica"
		},
		{
			"kind": "field",
			"label": "Anteprima scala",
			"hint": "",
			"section": "Scala tipografica"
		}
	],
	"spaziature": [
		{
			"kind": "section",
			"label": "Scala di spacing",
			"hint": "8 token globali usati ovunque (--olo-space-*). Tutti i padding e i gap del builder pescano da qui."
		},
		{
			"kind": "section",
			"label": "Larghezze container",
			"hint": "Tre preset di larghezza massima del contenuto. Il valore Default è il container principale del sito."
		},
		{
			"kind": "field",
			"label": "Narrow",
			"hint": "Per testi lunghi e blog, leggibilità ottimale.",
			"section": "Larghezze container"
		},
		{
			"kind": "field",
			"label": "Default",
			"hint": "Larghezza standard del contenuto del sito.",
			"section": "Larghezze container"
		},
		{
			"kind": "field",
			"label": "Wide",
			"hint": "Per hero, gallerie ampie, layout immersivi.",
			"section": "Larghezze container"
		},
		{
			"kind": "section",
			"label": "Ritmo verticale sezioni",
			"hint": "Padding-top/bottom di default per le sezioni. I valori sono token della scala spacing — cambiando lì cambia tutto."
		},
		{
			"kind": "section",
			"label": "Gutter responsive",
			"hint": "Gap tra colonne griglia e padding laterale del container, per ciascun breakpoint."
		},
		{
			"kind": "field",
			"label": "Gap colonne",
			"hint": "",
			"section": "Gutter responsive"
		},
		{
			"kind": "field",
			"label": "Padding laterale",
			"hint": "",
			"section": "Gutter responsive"
		},
		{
			"kind": "section",
			"label": "Scaling fluido responsive",
			"hint": "Rimpicciolisce automaticamente tutti i token spacing su tablet e mobile, senza dover ritoccare ogni tile."
		},
		{
			"kind": "field",
			"label": "Fattore tablet",
			"hint": "Es. 0.85 → spacing al 85% sotto 960px.",
			"section": "Scaling fluido responsive"
		},
		{
			"kind": "field",
			"label": "Fattore mobile",
			"hint": "Es. 0.65 → spacing al 65% sotto 640px.",
			"section": "Scaling fluido responsive"
		}
	],
	"responsive": [
		{
			"kind": "section",
			"label": "Dispositivi accesi",
			"hint": "Desktop c'è sempre."
		}
	],
	"tipicontenuto": [
		{
			"kind": "section",
			"label": "Redazione",
			"hint": "Chi aggiorna il sito scrive, traduce e mette in ordine le categorie da un posto solo."
		},
		{
			"kind": "field",
			"label": "Aggiungi e Modifica",
			"hint": "Nei menu di WordPress aprono la Redazione. I testi scritti a blocchi si aprono ancora nella scheda di WordPress.",
			"section": "Redazione"
		},
		{
			"kind": "field",
			"label": "Nome nel menu",
			"hint": "Vuoto: Articoli. Per esempio Notizie e Notizia.",
			"section": "Redazione"
		},
		{
			"kind": "field",
			"label": "Editor",
			"hint": "Semplice: titoli, grassetto, corsivo, link, elenchi e citazioni, come in un programma di scrittura.",
			"section": "Redazione"
		},
		{
			"kind": "field",
			"label": "Campi",
			"hint": "Compaiono nella scheda di ogni articolo, nel riquadro Dettagli.",
			"section": "Redazione"
		},
		{
			"kind": "field",
			"label": "Nome nel menu",
			"hint": "Vuoto: Spettacoli. Per esempio Concerti e Concerto.",
			"section": "Redazione"
		},
		{
			"kind": "field",
			"label": "Indirizzo",
			"hint": "Le pagine degli spettacoli stanno qui. Vuoto: spettacoli.",
			"section": "Redazione"
		},
		{
			"kind": "field",
			"label": "Nome",
			"hint": "Plurale e singolare: Notizie e Notizia, Comunicati e Comunicato.",
			"section": "Redazione"
		},
		{
			"kind": "field",
			"label": "Chiave e indirizzo",
			"hint": "La chiave non cambia più dopo il salvataggio: i contenuti sono legati a lei. L'indirizzo è la parte dopo il dominio.",
			"section": "Redazione"
		},
		{
			"kind": "field",
			"label": "Icona e posizione",
			"hint": "La posizione nel menu: 5 sotto la Bacheca, 20 sotto le Pagine, 25 sotto i Commenti.",
			"section": "Redazione"
		},
		{
			"kind": "field",
			"label": "Organizzazione",
			"hint": "",
			"section": "Redazione"
		},
		{
			"kind": "field",
			"label": "Nella scheda",
			"hint": "",
			"section": "Redazione"
		},
		{
			"kind": "field",
			"label": "Editor",
			"hint": "",
			"section": "Redazione"
		},
		{
			"kind": "field",
			"label": "Permessi",
			"hint": "",
			"section": "Redazione"
		},
		{
			"kind": "field",
			"label": "Campi",
			"hint": "Compaiono nella scheda di ogni contenuto, nel riquadro Dettagli, e nella Sorgente dinamica come «Campi dei contenuti».",
			"section": "Redazione"
		}
	],
	"tplconditions": [
		{
			"kind": "field",
			"label": "Nome regola",
			"hint": "Dove si applica la regola.",
			"section": ""
		},
		{
			"kind": "field",
			"label": "Contesto",
			"hint": "Dove si applica la regola.",
			"section": ""
		},
		{
			"kind": "field",
			"label": "Template",
			"hint": "",
			"section": ""
		},
		{
			"kind": "field",
			"label": "Priorità",
			"hint": "1-999. Più basso = vince. Default 10.",
			"section": ""
		},
		{
			"kind": "field",
			"label": "Operatore condizioni",
			"hint": "",
			"section": ""
		},
		{
			"kind": "field",
			"label": "Condizioni di match",
			"hint": "Aggiungi condizioni per cui la regola si applica.",
			"section": ""
		}
	],
	"wootemplates": [
		{
			"kind": "section",
			"label": "Mappa template",
			"hint": "Lascia \"Default WooCommerce\" per usare il rendering nativo di Woo."
		},
		{
			"kind": "field",
			"label": "Prodotti senza prezzo",
			"hint": "Al posto del prezzo, quando un prodotto non ne ha: «Prezzo su richiesta», «Prezzo riservato». Vuoto: non compare niente.",
			"section": "Mappa template"
		}
	],
	"popups": [
		{
			"kind": "field",
			"label": "Nome interno",
			"hint": "Quale template Olobuild usare come contenuto del popup.",
			"section": ""
		},
		{
			"kind": "field",
			"label": "Template",
			"hint": "Quale template Olobuild usare come contenuto del popup.",
			"section": ""
		},
		{
			"kind": "field",
			"label": "Trigger",
			"hint": "",
			"section": ""
		},
		{
			"kind": "field",
			"label": "Ritardo (secondi)",
			"hint": "",
			"section": ""
		},
		{
			"kind": "field",
			"label": "% scroll",
			"hint": "",
			"section": ""
		},
		{
			"kind": "field",
			"label": "Secondi di inattività",
			"hint": "",
			"section": ""
		},
		{
			"kind": "field",
			"label": "Frequenza",
			"hint": "",
			"section": ""
		},
		{
			"kind": "field",
			"label": "Overlay opacità",
			"hint": "",
			"section": ""
		},
		{
			"kind": "field",
			"label": "Larghezza massima (px)",
			"hint": "",
			"section": ""
		}
	],
	"seo": [
		{
			"kind": "section",
			"label": "La SEO in dieci passi",
			"hint": ""
		},
		{
			"kind": "section",
			"label": "Il sito si vede sui motori",
			"hint": "Mentre costruisci il sito puoi tenerlo nascosto. Quando è pronto, aprilo."
		},
		{
			"kind": "field",
			"label": "Visibile ai motori",
			"hint": "Spento, il sito chiede a Google e agli altri motori di non mostrarlo. È la stessa scelta di Impostazioni › Lettura di WordPress.",
			"section": "Il sito si vede sui motori"
		},
		{
			"kind": "section",
			"label": "Chi pubblica il sito",
			"hint": "Chi sei, dove sei e come ti si trova. Google e le intelligenze artificiali lo leggono in ogni pagina e collegano il sito a te."
		},
		{
			"kind": "field",
			"label": "Chi sei",
			"hint": "",
			"section": "Chi pubblica il sito"
		},
		{
			"kind": "field",
			"label": "Nome",
			"hint": "Come ti chiamano le persone. Se resta vuoto, il nome del sito.",
			"section": "Chi pubblica il sito"
		},
		{
			"kind": "field",
			"label": "Logo",
			"hint": "Quadrato o orizzontale, largo almeno 112 px, su fondo chiaro.",
			"section": "Chi pubblica il sito"
		},
		{
			"kind": "field",
			"label": "Telefono ed email",
			"hint": "I contatti che vuoi mostrare. Il telefono col prefisso internazionale: +39 …",
			"section": "Chi pubblica il sito"
		},
		{
			"kind": "field",
			"label": "Indirizzo",
			"hint": "Comincia a scrivere la via: scegli l'indirizzo giusto fra quelli proposti e CAP, città e posizione sulla mappa si compilano da soli.",
			"section": "Chi pubblica il sito"
		},
		{
			"kind": "field",
			"label": "Orari",
			"hint": "Quando la sede è aperta. Lascia vuoti i giorni di chiusura.",
			"section": "Chi pubblica il sito"
		},
		{
			"kind": "field",
			"label": "Posizione sulla mappa",
			"hint": "Si compila da sola quando scegli l'indirizzo. Se il punto non è giusto, su Google Maps fai clic destro sul punto e incolla qui i due numeri.",
			"section": "Chi pubblica il sito"
		},
		{
			"kind": "field",
			"label": "Fascia di prezzo",
			"hint": "Quanto si spende, da € a €€€€.",
			"section": "Chi pubblica il sito"
		},
		{
			"kind": "field",
			"label": "Profili",
			"hint": "L'indirizzo intero di ogni profilo. Servono a dire che il sito e i profili sono della stessa persona o attività.",
			"section": "Chi pubblica il sito"
		},
		{
			"kind": "field",
			"label": "Altri profili",
			"hint": "Uno per riga: la pagina di Wikipedia, Wikidata, un albo professionale, una guida. Più legami, più i motori sono sicuri di chi sei.",
			"section": "Chi pubblica il sito"
		},
		{
			"kind": "field",
			"label": "Come ti vedono",
			"hint": "Quello che Google e le intelligenze artificiali leggono di te in ogni pagina del sito.",
			"section": "Chi pubblica il sito"
		},
		{
			"kind": "section",
			"label": "Come appare su Google",
			"hint": "Il titolo e la descrizione che compaiono nei risultati della ricerca, quando una pagina non ne ha di suoi."
		},
		{
			"kind": "field",
			"label": "Schema del titolo",
			"hint": "{page} è il titolo della pagina, {sep} il separatore, {site} il nome del sito. La home usa il nome del sito e il motto.",
			"section": "Come appare su Google"
		},
		{
			"kind": "field",
			"label": "Separatore",
			"hint": "Il segno fra il titolo della pagina e il nome del sito.",
			"section": "Come appare su Google"
		},
		{
			"kind": "field",
			"label": "Descrizione del sito",
			"hint": "Due frasi su chi sei e cosa offri, fino a 160 caratteri. Vale per la home e per le pagine senza una descrizione loro.",
			"section": "Come appare su Google"
		},
		{
			"kind": "field",
			"label": "Anteprima della home",
			"hint": "Così la home compare fra i risultati di Google.",
			"section": "Come appare su Google"
		},
		{
			"kind": "section",
			"label": "Come appare quando lo condividi",
			"hint": "L'immagine e il titolo che compaiono quando qualcuno manda un link del sito su WhatsApp, Facebook, LinkedIn o X."
		},
		{
			"kind": "field",
			"label": "Immagine disegnata",
			"hint": "Per ogni pagina senza un'immagine sua: il titolo, il nome del sito e la foto in evidenza, nel colore e nel carattere del sito.",
			"section": "Come appare quando lo condividi"
		},
		{
			"kind": "field",
			"label": "Immagine di riserva",
			"hint": "1200 × 630 px. Esce quando una pagina non ha un'immagine sua e l'immagine disegnata è spenta.",
			"section": "Come appare quando lo condividi"
		},
		{
			"kind": "field",
			"label": "Formato su X",
			"hint": "Grande: l'immagine occupa tutta la larghezza. Piccola: un quadratino accanto al titolo.",
			"section": "Come appare quando lo condividi"
		},
		{
			"kind": "section",
			"label": "Cosa leggono i motori",
			"hint": "La mappa del sito che i motori leggono, e le parti che non vale la pena mostrare."
		},
		{
			"kind": "field",
			"label": "Sitemap",
			"hint": "L'elenco di tutte le pagine da mostrare, aggiornato da solo. Le pagine escluse non ci sono.",
			"section": "Cosa leggono i motori"
		},
		{
			"kind": "field",
			"label": "Tipi di contenuto",
			"hint": "Spegni un tipo per toglierlo dai motori con tutte le sue schede, per esempio un tipo che serve solo dentro altre pagine.",
			"section": "Cosa leggono i motori"
		},
		{
			"kind": "field",
			"label": "Archivi",
			"hint": "Le pagine che WordPress crea da sé per raccogliere i contenuti. Accese, i motori le mostrano.",
			"section": "Cosa leggono i motori"
		},
		{
			"kind": "field",
			"label": "Righe in più nel robots.txt",
			"hint": "Solo se sai cosa scrivere: le righe vanno in cima al file che dice ai robot cosa leggere.",
			"section": "Cosa leggono i motori"
		},
		{
			"kind": "section",
			"label": "Le intelligenze artificiali",
			"hint": "ChatGPT, Claude, Perplexity e gli altri leggono i siti per rispondere a chi chiede. Qui scegli chi può farlo e gli prepari un riassunto."
		},
		{
			"kind": "field",
			"label": "Chi può leggere il sito",
			"hint": "«Solo per rispondere» lascia entrare le IA quando cercano una risposta e tiene fuori chi copia i siti per addestrare i modelli.",
			"section": "Le intelligenze artificiali"
		},
		{
			"kind": "field",
			"label": "Riassunto per le IA",
			"hint": "Il file llms.txt: chi sei, i contatti e le pagine del sito con la loro descrizione, scritti in modo che un'IA li legga in un colpo.",
			"section": "Le intelligenze artificiali"
		},
		{
			"kind": "section",
			"label": "Verifica dei motori",
			"hint": "Dimostri a Google e a Bing che il sito è tuo. Da lì vedi con quali ricerche arrivano le persone e cosa non va."
		},
		{
			"kind": "field",
			"label": "Google Search Console",
			"hint": "Il modo migliore è la proprietà «Dominio» col record TXT nel DNS: qui non serve niente. Se non puoi toccare il DNS, scegli «Prefisso URL» e la verifica «Tag HTML», e incolla qui il codice del meta tag.",
			"section": "Verifica dei motori"
		},
		{
			"kind": "field",
			"label": "Bing Webmaster Tools",
			"hint": "Vale anche per Copilot. In Bing Webmaster Tools scegli la verifica col meta tag; puoi anche importare il sito dalla Search Console.",
			"section": "Verifica dei motori"
		},
		{
			"kind": "section",
			"label": "Avvisa i motori quando cambi qualcosa",
			"hint": "Con IndexNow, ogni volta che pubblichi o aggiorni una pagina, Bing, Yandex e gli altri motori aderenti lo sanno subito. Google legge la sitemap."
		},
		{
			"kind": "field",
			"label": "Chiave IndexNow",
			"hint": "Premi «Genera»: la chiave è solo tua e non va registrata da nessuna parte.",
			"section": "Avvisa i motori quando cambi qualcosa"
		},
		{
			"kind": "field",
			"label": "File della chiave",
			"hint": "OLObuild lo pubblica da sé. Dopo aver salvato, aprilo: deve mostrare la chiave.",
			"section": "Avvisa i motori quando cambi qualcosa"
		},
		{
			"kind": "section",
			"label": "Pagina per pagina",
			"hint": "Ogni pagina del sito col titolo e la descrizione che escono su Google. Apri una riga per scriverli; per l'analisi del testo apri la pagina nel builder."
		},
		{
			"kind": "section",
			"label": "Indirizzi cambiati e pagine non trovate",
			"hint": "Quando una pagina cambia indirizzo, chi arriva dal vecchio deve trovarla lo stesso: le persone e i motori."
		},
		{
			"kind": "field",
			"label": "Redirect automatico",
			"hint": "Se cambi l'indirizzo di una pagina pubblicata, il vecchio porta al nuovo da solo.",
			"section": "Indirizzi cambiati e pagine non trovate"
		},
		{
			"kind": "field",
			"label": "Redirect e pagine non trovate",
			"hint": "I redirect scritti a mano e gli indirizzi che qualcuno ha cercato senza trovare niente.",
			"section": "Indirizzi cambiati e pagine non trovate"
		}
	],
	"redirects": [
		{
			"kind": "section",
			"label": "Redirect configurate",
			"hint": "Prefisso \"~\" su \"Da URL\" attiva il match regex."
		},
		{
			"kind": "section",
			"label": "Pagine non trovate",
			"hint": "URL richieste che hanno restituito 404. Promuovi a redirect quelle ricorrenti."
		}
	],
	"cookie": [
		{
			"kind": "section",
			"label": "Stato e modalità",
			"hint": "Il banner viene mostrato al primo accesso e ai visitatori che non hanno ancora scelto."
		},
		{
			"kind": "field",
			"label": "Cookie banner",
			"hint": "Disattiva solo se il sito non usa cookie non-essenziali.",
			"section": "Stato e modalità"
		},
		{
			"kind": "field",
			"label": "Modalità",
			"hint": "Opt-in è richiesto in UE. Opt-out solo dove ammesso.",
			"section": "Stato e modalità"
		},
		{
			"kind": "field",
			"label": "Blocca script fino al consenso",
			"hint": "Google Analytics, Meta Pixel, ecc. non partono finché l'utente non accetta.",
			"section": "Stato e modalità"
		},
		{
			"kind": "field",
			"label": "Re-richiedi consenso dopo",
			"hint": "Mesi dopo i quali il banner ricompare.",
			"section": "Stato e modalità"
		},
		{
			"kind": "section",
			"label": "Categorie di cookie",
			"hint": "Le categorie mostrate nel pannello di preferenze."
		},
		{
			"kind": "section",
			"label": "Testi del banner",
			"hint": "Testi mostrati al visitatore, in ogni lingua del sito."
		},
		{
			"kind": "field",
			"label": "Lingua",
			"hint": "Nelle altre lingue, un testo vuoto resta quello della lingua principale.",
			"section": "Testi del banner"
		},
		{
			"kind": "field",
			"label": "Posizione",
			"hint": "",
			"section": "Testi del banner"
		}
	],
	"analytics": [
		{
			"kind": "section",
			"label": "ID dei tracker",
			"hint": "Lascia vuoto per disattivare un tracker."
		},
		{
			"kind": "field",
			"label": "Google Analytics 4",
			"hint": "Measurement ID (es. G-XXXXXXXX).",
			"section": "ID dei tracker"
		},
		{
			"kind": "field",
			"label": "Facebook Pixel",
			"hint": "",
			"section": "ID dei tracker"
		},
		{
			"kind": "field",
			"label": "Google Tag Manager",
			"hint": "",
			"section": "ID dei tracker"
		},
		{
			"kind": "field",
			"label": "Microsoft Clarity",
			"hint": "",
			"section": "ID dei tracker"
		},
		{
			"kind": "field",
			"label": "Hotjar Site ID",
			"hint": "",
			"section": "ID dei tracker"
		},
		{
			"kind": "section",
			"label": "Eventi automatici",
			"hint": "Olobuild emette eventi GA4 / FB Pixel quando un utente interagisce con i tile."
		},
		{
			"kind": "field",
			"label": "Percentuali scroll (CSV)",
			"hint": "Quando tracciare scroll depth, es. 25,50,75,100.",
			"section": "Eventi automatici"
		},
		{
			"kind": "field",
			"label": "Estensioni download (CSV)",
			"hint": "",
			"section": "Eventi automatici"
		},
		{
			"kind": "section",
			"label": "Privacy & Consenso",
			"hint": ""
		},
		{
			"kind": "field",
			"label": "Anonimizza IP (GA4)",
			"hint": "",
			"section": "Privacy & Consenso"
		},
		{
			"kind": "field",
			"label": "Rispetta Do-Not-Track",
			"hint": "Non tracciare le visite degli utenti loggati come admin.",
			"section": "Privacy & Consenso"
		},
		{
			"kind": "field",
			"label": "Escludi amministratori",
			"hint": "Non tracciare le visite degli utenti loggati come admin.",
			"section": "Privacy & Consenso"
		},
		{
			"kind": "field",
			"label": "Richiedi consenso cookie",
			"hint": "Carica i tracker solo dopo il consenso del banner.",
			"section": "Privacy & Consenso"
		},
		{
			"kind": "section",
			"label": "Script custom",
			"hint": "Codice arbitrario da iniettare nel sito. Usa con attenzione — possono rompere la pagina o esporre dati sensibili."
		},
		{
			"kind": "field",
			"label": "In <head>",
			"hint": "Meta tag, fonts, link prefetch, codice di verifica.",
			"section": "Script custom"
		}
	],
	"performance": [
		{
			"kind": "section",
			"label": "Stato cache",
			"hint": "Ultimo svuotamento"
		},
		{
			"kind": "section",
			"label": "Critical CSS",
			"hint": "Inietta inline il CSS above-the-fold per ogni pagina, defera il resto. Boost diretto su FCP e LCP."
		},
		{
			"kind": "field",
			"label": "Abilita Critical CSS",
			"hint": "Genera e inietta il CSS critico per ogni pagina visitata.",
			"section": "Critical CSS"
		},
		{
			"kind": "field",
			"label": "Durata cache (giorni)",
			"hint": "Dopo quanti giorni il critical CSS viene rigenerato. Invalidato comunque al save di un template.",
			"section": "Critical CSS"
		},
		{
			"kind": "field",
			"label": "Sezioni above-the-fold",
			"hint": "Quante sezioni iniziali analizzare per estrarre il CSS critico.",
			"section": "Critical CSS"
		},
		{
			"kind": "section",
			"label": "Asset optimizer",
			"hint": "Defer JS, cache file CSS, minify del CSS generato dai template."
		},
		{
			"kind": "field",
			"label": "Full-page cache",
			"hint": "Salva l'HTML generato e lo serve prima di WordPress: abbatte il tempo di risposta (TTFB). Esclude automaticamente utenti loggati, carrello/checkout WooCommerce e richieste POST. Installa il drop-in advanced-cache.php e attiva WP_CACHE; si svuota a ogni modifica di contenuto.",
			"section": "Asset optimizer"
		},
		{
			"kind": "field",
			"label": "Defer JavaScript",
			"hint": "Aggiunge defer agli script frontend di Olobuild. Non blocca il rendering della pagina.",
			"section": "Asset optimizer"
		},
		{
			"kind": "field",
			"label": "Cache CSS su file statici",
			"hint": "Salva il CSS dei template come file invece di iniettarlo inline. Migliora il caching del browser.",
			"section": "Asset optimizer"
		},
		{
			"kind": "field",
			"label": "Minifica CSS",
			"hint": "Rimuove commenti, spazi e newline dal CSS generato dai template e dal CSS frontend del plugin.",
			"section": "Asset optimizer"
		},
		{
			"kind": "field",
			"label": "CSS per-tile",
			"hint": "Serve solo le porzioni di CSS dei tile presenti in pagina (mappa, gallery, hero, postgrid…) invece dell'intero foglio. Riduce il CSS inutilizzato; fallback automatico al CSS completo in caso di dubbio.",
			"section": "Asset optimizer"
		},
		{
			"kind": "field",
			"label": "UIkit subset",
			"hint": "Serve solo i componenti UIkit usati dal sito, appresi automaticamente dalle pagine. Prima visita col CSS completo, poi subset; si ri-apprende al salvataggio dei template.",
			"section": "Asset optimizer"
		},
		{
			"kind": "field",
			"label": "Cache browser per i media (.htaccess)",
			"hint": "Scrive header Expires/Cache-Control per immagini, video e font (6 mesi) e CSS/JS (1 mese). Solo server Apache/LiteSpeed.",
			"section": "Asset optimizer"
		},
		{
			"kind": "section",
			"label": "Resource hints & loading",
			"hint": "Suggerimenti al browser per precaricare risorse e ottimizzare il caricamento immagini."
		},
		{
			"kind": "field",
			"label": "DNS prefetch & preconnect automatici",
			"hint": "dns-prefetch verso YouTube e Vimeo nelle pagine che li contengono. I domini scritti più sotto escono sempre.",
			"section": "Resource hints & loading"
		},
		{
			"kind": "field",
			"label": "Preload font custom",
			"hint": "Precarica i file dei caratteri di testo e titoli, al peso usato: il testo compare subito con il suo carattere.",
			"section": "Resource hints & loading"
		},
		{
			"kind": "field",
			"label": "Lazy load video self-hosted",
			"hint": "I video autoplay (sfondi, filmreel, decorativi) si scaricano e partono solo quando entrano nel viewport. Risparmia decine di MB su pagine ricche di video.",
			"section": "Resource hints & loading"
		},
		{
			"kind": "field",
			"label": "fetchpriority hero image",
			"hint": "La prima immagine della prima sezione parte subito e con priorità alta. Le altre restano pigre.",
			"section": "Resource hints & loading"
		},
		{
			"kind": "section",
			"label": "Domini custom",
			"hint": "Aggiungi domini per dns-prefetch e preconnect personalizzati (uno per riga)."
		},
		{
			"kind": "field",
			"label": "DNS prefetch",
			"hint": "Risolve il DNS in anticipo. Utile per CDN e servizi esterni.",
			"section": "Domini custom"
		},
		{
			"kind": "field",
			"label": "Preconnect",
			"hint": "Apre connessione completa (DNS + TCP + TLS). Usare solo per risorse critiche.",
			"section": "Domini custom"
		},
		{
			"kind": "section",
			"label": "Pulizia head",
			"hint": "Rimuovi script e CSS non necessari dal <head> per ridurre richieste e peso."
		},
		{
			"kind": "field",
			"label": "Rimuovi jQuery Migrate",
			"hint": "jquery-migrate.js (~10 KB). Necessario solo per plugin molto vecchi.",
			"section": "Pulizia head"
		},
		{
			"kind": "field",
			"label": "Rimuovi emoji scripts",
			"hint": "wp-emoji-release.min.js — i browser moderni gestiscono emoji nativamente.",
			"section": "Pulizia head"
		},
		{
			"kind": "field",
			"label": "Rimuovi Block CSS",
			"hint": "wp-block-library-css (~30 KB). Solo se non usi blocchi Gutenberg nel frontend.",
			"section": "Pulizia head"
		},
		{
			"kind": "field",
			"label": "Rimuovi Classic Theme CSS",
			"hint": "classic-theme-styles-css. Inutile se non usi un tema classico WordPress.",
			"section": "Pulizia head"
		}
	],
	"maintenance": [
		{
			"kind": "section",
			"label": "Modalità",
			"hint": ""
		},
		{
			"kind": "field",
			"label": "Stato sito",
			"hint": "",
			"section": "Modalità"
		},
		{
			"kind": "field",
			"label": "Template Coming Soon",
			"hint": "Quale template Olobuild mostrare come \"Coming Soon\".",
			"section": "Modalità"
		},
		{
			"kind": "field",
			"label": "Template Manutenzione",
			"hint": "Quale template Olobuild mostrare durante la manutenzione (HTTP 503).",
			"section": "Modalità"
		},
		{
			"kind": "section",
			"label": "Chi può accedere comunque",
			"hint": "Anche con il sito offline, alcuni ruoli e un URL segreto possono bypassare la pagina di manutenzione."
		},
		{
			"kind": "field",
			"label": "Ruoli che bypassano",
			"hint": "Possono navigare il sito normalmente.",
			"section": "Chi può accedere comunque"
		},
		{
			"kind": "field",
			"label": "URL segreta di bypass",
			"hint": "Aggiungi ?bypass=SECRET all'URL del sito per accedere. Lascia vuoto per disabilitare.",
			"section": "Chi può accedere comunque"
		}
	],
	"ai": [
		{
			"kind": "section",
			"label": "Provider",
			"hint": "Scegli il modello che muove le funzioni AI dentro l'editor."
		},
		{
			"kind": "field",
			"label": "Provider AI",
			"hint": "Puoi cambiarlo in qualsiasi momento — la chiave API è salvata per provider.",
			"section": "Provider"
		},
		{
			"kind": "field",
			"label": "Modello",
			"hint": "Modelli più potenti = risposte migliori ma costo maggiore per chiamata.",
			"section": "Provider"
		},
		{
			"kind": "field",
			"label": "Modello immagini",
			"hint": "Le immagini si generano con OpenAI: serve la sua chiave (scegli OpenAI qui sopra per inserirla).",
			"section": "Provider"
		},
		{
			"kind": "field",
			"label": "Budget mensile",
			"hint": "Spesa stimata dal 1° del mese oltre la quale le funzioni AI si fermano fino al mese dopo. 0 = nessun limite.",
			"section": "Provider"
		},
		{
			"kind": "section",
			"label": "Comportamento",
			"hint": "Tono, lingua e creatività del modello."
		},
		{
			"kind": "field",
			"label": "Lingua di default",
			"hint": "Il modello risponderà in questa lingua se non specifichi altrimenti.",
			"section": "Comportamento"
		},
		{
			"kind": "field",
			"label": "Temperatura",
			"hint": "0 = preciso e ripetibile · 1 = creativo e variabile.",
			"section": "Comportamento"
		},
		{
			"kind": "field",
			"label": "Tono di voce",
			"hint": "Personalità di base che il modello adotta in tutti i task.",
			"section": "Comportamento"
		},
		{
			"kind": "field",
			"label": "Istruzioni di sistema",
			"hint": "Contesto del brand che viene incluso in ogni chiamata. Max 500 caratteri.",
			"section": "Comportamento"
		},
		{
			"kind": "section",
			"label": "Utilizzo questo mese",
			"hint": "Statistiche delle chiamate API dal 1° del mese (lo stesso periodo del budget)."
		}
	],
	"stockmedia": [
		{
			"kind": "section",
			"label": "Provider connessi",
			"hint": ""
		},
		{
			"kind": "section",
			"label": "Comportamento default",
			"hint": ""
		},
		{
			"kind": "field",
			"label": "Provider preferito",
			"hint": "Quello che si apre per primo dalla ricerca media nell'editor.",
			"section": "Comportamento default"
		},
		{
			"kind": "field",
			"label": "Scarica in locale",
			"hint": "Quando inserisci un'immagine, viene scaricata nella Libreria media di WordPress. Disattivato = hotlink al provider.",
			"section": "Comportamento default"
		},
		{
			"kind": "field",
			"label": "Ottimizza al download",
			"hint": "Comprime e converte in WebP automaticamente. Richiede modulo Performance attivo.",
			"section": "Comportamento default"
		}
	],
	"integrazioni": [
		{
			"kind": "section",
			"label": "reCAPTCHA v3",
			"hint": "Anti-spam di Google, senza caselle da spuntare. Nel form si accende da Anti-spam; servono entrambe le chiavi."
		},
		{
			"kind": "field",
			"label": "Site key",
			"hint": "Pubblica: viene stampata nella pagina del form.",
			"section": "reCAPTCHA v3"
		},
		{
			"kind": "field",
			"label": "Secret key",
			"hint": "Resta sul server: verifica ogni invio.",
			"section": "reCAPTCHA v3"
		},
		{
			"kind": "section",
			"label": "Liste e CRM",
			"hint": "Chi compila il form viene aggiunto alla lista del servizio. Senza la chiave il servizio non riceve niente, anche se nel form è acceso."
		},
		{
			"kind": "field",
			"label": "Mailchimp — API key",
			"hint": "Profilo → Extras → API keys. Finisce con il datacenter, per esempio -us21.",
			"section": "Liste e CRM"
		},
		{
			"kind": "field",
			"label": "ActiveCampaign — URL dell'account",
			"hint": "Impostazioni → Sviluppatore. Senza https:// lo aggiunge Olobuild.",
			"section": "Liste e CRM"
		},
		{
			"kind": "field",
			"label": "ActiveCampaign — API key",
			"hint": "Nella stessa pagina dell'URL. Servono tutti e due.",
			"section": "Liste e CRM"
		},
		{
			"kind": "field",
			"label": "ConvertKit (Kit) — API key v3",
			"hint": "Impostazioni → Sviluppatore: la API key v3, non la API secret.",
			"section": "Liste e CRM"
		},
		{
			"kind": "field",
			"label": "Brevo — API key",
			"hint": "SMTP e API → Chiavi API: una chiave v3 (inizia con xkeysib-).",
			"section": "Liste e CRM"
		},
		{
			"kind": "field",
			"label": "HubSpot e webhook",
			"hint": "Nessuna chiave: si impostano nel form. Per HubSpot Portal ID, Form GUID e quali campi mandare, con il nome della proprietà HubSpot: un campo che il modulo HubSpot non ha fa scartare l'intero invio.",
			"section": "Liste e CRM"
		}
	],
	"whitelabel": [
		{
			"kind": "section",
			"label": "Identità del plugin",
			"hint": "Come appare nel menu di WordPress e nel builder."
		},
		{
			"kind": "field",
			"label": "Nome plugin",
			"hint": "Sostituisce \"Olobuild\" nel menu di WordPress, nell'elenco dei plugin, nell'editor, nella Guida e nel wizard.",
			"section": "Identità del plugin"
		},
		{
			"kind": "field",
			"label": "Nome agenzia",
			"hint": "Autore del plugin nell'elenco dei plugin di WordPress.",
			"section": "Identità del plugin"
		},
		{
			"kind": "field",
			"label": "Logo (chiaro)",
			"hint": "Icona della voce nel menu di WordPress. Quadrata, almeno 32×32 px.",
			"section": "Identità del plugin"
		},
		{
			"kind": "field",
			"label": "Logo (scuro)",
			"hint": "Al posto del logo Olobuild nella barra scura della bacheca e nel wizard.",
			"section": "Identità del plugin"
		},
		{
			"kind": "field",
			"label": "URL sito agenzia",
			"hint": "Link dell'autore nell'elenco dei plugin di WordPress.",
			"section": "Identità del plugin"
		},
		{
			"kind": "section",
			"label": "Visibilità",
			"hint": ""
		},
		{
			"kind": "field",
			"label": "Nascondi \"Olobuild\" nel codice del sito",
			"hint": "Toglie i commenti HTML con il nome Olobuild dal codice personalizzato e dal banner cookie.",
			"section": "Visibilità"
		},
		{
			"kind": "field",
			"label": "Nascondi le novità",
			"hint": "Toglie le note di versione dalla bacheca e dalla Guida.",
			"section": "Visibilità"
		},
		{
			"kind": "field",
			"label": "Nascondi a non-admin",
			"hint": "Chi non è amministratore non vede la voce Olobuild nel menu di WordPress.",
			"section": "Visibilità"
		},
		{
			"kind": "field",
			"label": "Documentazione tua",
			"hint": "Il pulsante Guida e il pannello Centro risorse aprono il tuo indirizzo al posto delle guide di Olobuild.",
			"section": "Visibilità"
		},
		{
			"kind": "field",
			"label": "Email del supporto",
			"hint": "«Scrivi al supporto» apre un'email a questo indirizzo. Vuoto: il supporto Olobuild.",
			"section": "Visibilità"
		}
	],
	"permessi": [
		{
			"kind": "section",
			"label": "Ruoli del sito",
			"hint": "L'editor visuale si apre solo con il permesso «Gestire le opzioni» (amministratori). I livelli per ruolo (solo contenuti, solo design) non sono ancora applicati nel builder."
		}
	]
};
