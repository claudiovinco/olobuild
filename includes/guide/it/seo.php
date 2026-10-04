<?php
/**
 * Guida «SEO e Open Graph» (italiano). Solo HTML: titolo, descrizione e minuti
 * li stampa Olobuild_Guida::render_guida() dall'elenco delle guide.
 *
 * @package Olobuild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<nav class="olo-guida-toc" aria-label="In questa guida">
	<strong>In questa guida</strong>
	<ol>
		<li><a href="#dove">Dove si imposta</a></li>
		<li><a href="#titolo">Titolo e descrizione</a></li>
		<li><a href="#social">Anteprima sui social</a></li>
		<li><a href="#robots">Indicizzazione e canonical</a></li>
		<li><a href="#schema">Dati strutturati</a></li>
		<li><a href="#redirect">Redirect e 404</a></li>
		<li><a href="#sitemap">Sitemap</a></li>
		<li><a href="#plugin">Con Yoast, Rank Math e simili</a></li>
	</ol>
</nav>

<h2 id="dove">1. Dove si imposta</h2>
<p>Olobuild scrive nella testata di ogni pagina del sito titolo, descrizione, anteprima social e dati strutturati. Li imposti pagina per pagina, in due posti che salvano gli stessi dati.</p>
<ul>
	<li><strong>Nel builder:</strong> <span class="ui">Impostazioni pagina</span> › <span class="ui">SEO</span>, con le schede Base, Social, Robots, Schema e FAQ. Il gruppo si salva da solo, senza premere Salva, e compare quando il template è collegato a una pagina.</li>
	<li><strong>Nell'editor di WordPress:</strong> il riquadro <span class="ui">Olobuild SEO</span>, presente in pagine, articoli e negli altri contenuti pubblici, con l'anteprima del risultato su Google.</li>
</ul>
<p>Negli elenchi di pagine e articoli la colonna SEO mostra con un pallino quanti dei campi principali sono compilati.</p>
<div class="nota"><p>La scheda <strong>SEO globale</strong> della Configurazione è in revisione. Oggi agiscono il Separatore del titolo e l'interruttore Sitemap XML. Titoli, descrizioni e immagini si impostano pagina per pagina, come spiega questa guida.</p></div>

<h2 id="titolo">2. Titolo e descrizione</h2>
<p><span class="ui">SEO Title</span> è il titolo che compare nella scheda del browser e nei risultati di ricerca. Il contatore indica 60 caratteri, la lunghezza che Google mostra per intero. Se lo lasci vuoto vale il titolo della pagina.</p>
<p><span class="ui">Meta Description</span> è il testo sotto il titolo nei risultati, con un contatore di 160 caratteri. Se manca, Olobuild usa le prime 30 parole del riassunto della pagina e, in mancanza anche di quello, lo slogan del sito.</p>
<p><span class="ui">Focus keyword</span> è la parola chiave principale della pagina. Nel riquadro dell'editor di WordPress Olobuild controlla se compare nel titolo SEO, nella descrizione, nell'indirizzo e nel primo paragrafo.</p>

<h2 id="social">3. Anteprima sui social</h2>
<p>Quando qualcuno condivide la pagina su Facebook, LinkedIn, WhatsApp o X, l'anteprima nasce dai dati Open Graph. Li trovi nella scheda Social.</p>
<ul>
	<li><span class="ui">OG Title</span> e <span class="ui">OG Description</span> cambiano titolo e testo solo per i social. Vuoti, valgono quelli della scheda Base.</li>
	<li><span class="ui">OG Image</span> è l'immagine dell'anteprima. La misura giusta è 1200 × 630 pixel.</li>
	<li>I campi per <span class="ui">Twitter / X</span> servono solo se vuoi un testo diverso: vuoti, X usa quelli Open Graph.</li>
</ul>
<p>Senza OG Image Olobuild usa l'immagine in evidenza della pagina e, se manca anche quella, il logo del sito. Con un'immagine l'anteprima di X è grande, senza è compatta.</p>
<div class="nota"><p>I social conservano l'anteprima per giorni. Dopo averla cambiata, aggiornala con lo strumento di debug della piattaforma, per esempio il Sharing Debugger di Facebook.</p></div>

<h2 id="robots">4. Indicizzazione e canonical</h2>
<p>Nella scheda Robots, <span class="ui">noindex</span> chiede ai motori di ricerca di non mostrare la pagina nei risultati. I link della pagina restano seguiti, a meno di attivare anche <span class="ui">nofollow</span>. Usa noindex per pagine di servizio, come una pagina di ringraziamento dopo un form.</p>
<p><span class="ui">Canonical URL</span> indica l'indirizzo principale di una pagina che esiste in più versioni. Vuoto, vale l'indirizzo della pagina stessa, ed è la scelta giusta quasi sempre.</p>

<h2 id="schema">5. Dati strutturati</h2>
<p>I dati strutturati descrivono la pagina ai motori di ricerca in un formato che leggono senza interpretare il testo. Olobuild li scrive da solo:</p>
<ul>
	<li>su tutte le pagine, chi pubblica il sito, con il logo</li>
	<li>sulla home anche il sito stesso, con la sua ricerca interna</li>
	<li>sulle altre pagine il percorso di navigazione (breadcrumb)</li>
	<li>sugli articoli il tipo Articolo, sulle pagine il tipo Pagina, sui prodotti WooCommerce il tipo Prodotto</li>
</ul>
<p>Nella scheda FAQ, <span class="ui">Aggiungi FAQ</span> raccoglie domande e risposte che diventano dati di tipo FAQ. La tile Accordion ha lo stesso interruttore, <span class="ui">Schema FAQ (SEO)</span>, e la tile Mappa può descrivere il luogo con <span class="ui">Schema.org JSON-LD (SEO)</span>.</p>
<div class="nota"><p>Scegliere un tipo in <span class="ui">Schema.org type</span>, per esempio Event o Recipe, produce solo nome, indirizzo, date e descrizione. Per i risultati arricchiti di Google servono più dati: scrivili in <span class="ui">JSON-LD personalizzato</span>, che Olobuild controlla prima di pubblicarlo.</p></div>

<h2 id="redirect">6. Redirect e 404</h2>
<p>Quando una pagina cambia indirizzo, un redirect porta chi arriva dal vecchio link alla pagina nuova. La gestione sta in <span class="ui">Sistema</span> › <span class="ui">Configurazione</span> › <span class="ui">SEO &amp; Privacy</span> › <span class="ui">Redirect &amp; 404</span>, e la bacheca segnala gli indirizzi in 404 ancora senza redirect.</p>
<ol class="passi">
	<li><strong>Aggiungi il redirect.</strong> <span class="ui">Aggiungi redirect</span>, poi l'indirizzo vecchio in <span class="ui">Da URL</span> e quello nuovo in <span class="ui">A URL</span>.</li>
	<li><strong>Scegli il tipo.</strong> 301 per uno spostamento definitivo, il caso più comune. 302 e 307 per uno temporaneo. 410 per dire che la pagina è stata tolta e non tornerà.</li>
	<li><strong>Controlla i 404.</strong> Il Log 404 elenca gli indirizzi richiesti che non esistono, fino a 500. <span class="ui">Crea redirect</span> su una riga prepara un redirect 301: correggi la destinazione prima di salvarlo.</li>
</ol>
<p>Per spostare un gruppo di indirizzi, comincia <span class="ui">Da URL</span> con <code>~</code> e scrivi un'espressione regolare. <code>$1</code> nella destinazione riprende la parte catturata.</p>
<div class="nota attenzione"><p>Un redirect vale anche quando l'indirizzo di partenza è una pagina che esiste. Prima di crearne uno su un indirizzo in uso, controlla che quella pagina non serva più.</p></div>
<p>La vista IndexNow avvisa Bing e gli altri motori che aderiscono quando pubblichi una pagina o un articolo. Oltre alla chiave serve un file di testo con lo stesso nome della chiave nella cartella principale del sito: Olobuild non lo crea da solo.</p>

<h2 id="sitemap">7. Sitemap</h2>
<p>La sitemap elenca le pagine del sito per i motori di ricerca. WordPress la pubblica all'indirizzo <code>/wp-sitemap.xml</code> e comprende tutto il sito: è quella da indicare in Google Search Console. Se usi un plugin SEO, vale la sua.</p>

<h2 id="plugin">8. Con Yoast, Rank Math e simili</h2>
<p>Con Yoast SEO, Rank Math, All in One SEO o SEOPress attivi, Olobuild si fa da parte: non scrive titolo, descrizione, Open Graph, canonical e dati strutturati, così la pagina non li riceve due volte. In questo caso compila i campi nel pannello del plugin SEO. Quelli di Olobuild restano salvati ma non vengono usati.</p>
<p>Le versioni in altre lingue della pagina e i loro collegamenti per i motori di ricerca sono compito di OLOlang.</p>
