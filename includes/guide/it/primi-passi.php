<?php
/**
 * Guida «Primi passi» (italiano). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#configurazione">La prima configurazione</a></li>
		<li><a href="#bacheca">La bacheca</a></li>
		<li><a href="#crea">Creare una pagina</a></li>
		<li><a href="#builder">Il builder in 4 zone</a></li>
		<li><a href="#elementi">Aggiungere e sistemare gli elementi</a></li>
		<li><a href="#pubblica">Salvare e pubblicare</a></li>
		<li><a href="#dispositivi">Computer, tablet e telefono</a></li>
		<li><a href="#scorciatoie">Scorciatoie da tastiera</a></li>
	</ol>
</nav>

<h2 id="configurazione">1. La prima configurazione</h2>
<p>Alla prima attivazione Olobuild apre la configurazione guidata, in 3 passi. Se l'hai rimandata, il pannello di benvenuto nella Bacheca di WordPress la riapre con <span class="ui">Avvia la configurazione</span>.</p>
<ol class="passi">
	<li><strong>Tema WordPress.</strong> <span class="ui">Installa e attiva</span> mette Hello Olobuild, il tema leggero incluso nel plugin. Con <span class="ui">Usa tema attuale</span> tieni il tema che hai già.</li>
	<li><strong>Scegli un design.</strong> Scegli uno dei 52 temi pronti, oppure <span class="ui">Vuoto</span>, e premi <span class="ui">Importa tema selezionato</span>. Il tema crea pagine e menu, attiva header e footer, applica i suoi colori e i suoi caratteri e imposta la home. Prima dell'import Olobuild salva un'istantanea del sito, che puoi rimettere con <span class="ui">Ripristina</span>.</li>
	<li><strong>Tutto pronto.</strong> <span class="ui">Apri Olobuild</span> porta alla bacheca, <span class="ui">Vedi il sito</span> apre il sito.</li>
</ol>
<div class="nota"><p>Con <strong>Vuoto</strong> ottieni il minimo per partire: una pagina Home impostata come home, il menu «Menu Principale», un «Header base» e un «Footer base» già attivi.</p></div>

<h2 id="bacheca">2. La bacheca</h2>
<p>La bacheca si apre dalla voce <span class="ui">Olobuild</span> del menu di WordPress. La barra in alto è uguale in tutte le pagine di Olobuild e divide il lavoro in 4 aree.</p>
<table>
	<thead><tr><th>Area</th><th>Cosa contiene</th></tr></thead>
	<tbody>
		<tr><td><strong>Costruisci</strong></td><td>Blocchi &amp; Pagine (i template del sito), Popup, Importa / Esporta</td></tr>
		<tr><td><strong>Media</strong></td><td>Ricerca di foto e video da inserire nelle pagine</td></tr>
		<tr><td><strong>Raccolta</strong></td><td>Invii dei form, Newsletter, Tracking &amp; Analytics</td></tr>
		<tr><td><strong>Sistema</strong></td><td>Configurazione, Strumenti e questa Guida</td></tr>
	</tbody>
</table>
<p>Nella bacheca trovi <span class="ui">Continua dove avevi lasciato</span>, le <span class="ui">Azioni rapide</span>, le schede di <span class="ui">Gestione</span> e, a destra, il Centro risorse con le novità e le guide. Il campo di ricerca in alto, o <kbd>Ctrl</kbd> <kbd>K</kbd>, cerca pagine, template e impostazioni da qualsiasi pagina di Olobuild.</p>

<h2 id="crea">3. Creare una pagina</h2>
<p><span class="ui">Crea pagina</span>, fra le Azioni rapide, crea in un colpo solo un template «Senza titolo» e la pagina di WordPress collegata, entrambi in bozza, e apre il builder. Lo stesso fa <span class="ui">Nuova pagina</span> in cima alla bacheca.</p>
<p>Puoi partire anche da WordPress:</p>
<ul>
	<li>nell'elenco Pagine, l'azione <span class="ui">Crea con Olobuild</span> sotto il titolo di una pagina</li>
	<li>nella barra di amministrazione, <span class="ui">Modifica con Olobuild</span> mentre guardi una pagina del sito</li>
	<li>nell'editor di WordPress, il riquadro <span class="ui">Olobuild</span>, dove scegli anche header e footer della pagina</li>
</ul>
<div class="nota"><p>Dai alla pagina il suo titolo prima di pubblicarla. Il titolo si cambia con un clic sul nome della pagina, nella barra del builder. WordPress ricava l'indirizzo della pagina dal titolo nel momento della pubblicazione.</p></div>

<h2 id="builder">4. Il builder in 4 zone</h2>
<figure class="olo-schema">
	<div class="finestra" aria-hidden="true">
		<div class="z-barra"><span class="num">1</span>Barra in alto</div>
		<div class="z-palette"><span class="num">2</span>Elementi<br>e Struttura</div>
		<div class="z-canvas"><span class="num">3</span>Canvas</div>
		<div class="z-inspector"><span class="num">4</span>Contenuto · Stile<br>· Avanzate</div>
	</div>
	<ol class="legenda">
		<li><span class="num">1</span><span><strong>Barra in alto.</strong> Titolo, Impostazioni pagina, dispositivi e zoom, Blocchi &amp; Pagine, annulla e ripeti, Anteprima, Salva e Pubblica.</span></li>
		<li><span class="num">2</span><span><strong>Pannello sinistro.</strong> <span class="ui">Elementi</span> raccoglie le tile per categoria, con Recenti, Preferiti e Globali. <span class="ui">Struttura</span> mostra l'albero della pagina.</span></li>
		<li><span class="num">3</span><span><strong>Canvas.</strong> La pagina come la vedrà il visitatore, disegnata dallo stesso motore del sito, con header e footer.</span></li>
		<li><span class="num">4</span><span><strong>Pannello destro.</strong> Compare quando selezioni un elemento: <span class="ui">Contenuto</span> per testi, immagini e link, <span class="ui">Stile</span> per l'aspetto, <span class="ui">Avanzate</span> per visibilità, effetti e posizionamento.</span></li>
	</ol>
	<figcaption>Le 4 zone del builder. Il pannello destro si apre anche con Impostazioni pagina.</figcaption>
</figure>
<p>Ogni pagina è fatta di sezioni. Una sezione contiene righe, una riga contiene colonne, e nelle colonne stanno gli elementi, che in Olobuild si chiamano tile.</p>
<figure class="olo-schema">
	<div class="albero" aria-hidden="true">
		<div class="l1">Sezione <small>fascia a tutta larghezza, con il suo sfondo</small></div>
		<div class="l2">Riga <small>divide la sezione in colonne</small></div>
		<div class="l3">Colonna <small>larghezza diversa per ogni dispositivo</small></div>
		<div class="l4">Tile <small>titolo, testo, immagine, pulsante, galleria…</small></div>
	</div>
	<figcaption>La gerarchia di una pagina, come la mostra il pannello Struttura.</figcaption>
</figure>
<p>Nello Stile di una tile i controlli sono divisi in due blocchi. <strong>Elemento</strong> agisce su ciò che la tile disegna, per esempio il pulsante. <strong>Contenitore</strong> agisce sul riquadro che la tile occupa nella griglia, con i suoi spazi e il suo sfondo.</p>

<h2 id="elementi">5. Aggiungere e sistemare gli elementi</h2>
<ol class="passi">
	<li><strong>Aggiungi.</strong> Trascina una tile dal pannello Elementi nel canvas, oppure fai clic sulla sua scheda: la tile si inserisce dopo l'elemento selezionato, o in fondo alla pagina. In una pagina vuota usa <span class="ui">Aggiungi modulo</span> o <span class="ui">Scegli layout</span>.</li>
	<li><strong>Cerca.</strong> <kbd>Ctrl</kbd> <kbd>K</kbd> apre il Finder, che trova una tile per nome senza scorrere le categorie.</li>
	<li><strong>Inserisci una sezione.</strong> Il pulsante <span class="ui">+</span> fra due sezioni apre <span class="ui">Inserisci modulo o riga</span>, con un modulo nuovo, una riga vuota o un blocco pronto dalla libreria.</li>
	<li><strong>Sposta e duplica.</strong> Passando sopra un elemento compaiono i comandi per trascinarlo, spostarlo su o giù, duplicarlo ed eliminarlo. Il clic destro apre il menu completo, con Copia, Incolla, Copia stile e Incolla stile.</li>
	<li><strong>Scrivi.</strong> Fai doppio clic su un testo per modificarlo direttamente nel canvas. <kbd>Esc</kbd> annulla la modifica in corso.</li>
</ol>
<div class="nota"><p>Accanto al titolo di un campo o di una sezione può comparire una <strong>(i)</strong>. Aprila per leggere a cosa serve il controllo: le spiegazioni stanno lì, così il pannello resta ordinato.</p></div>

<h2 id="pubblica">6. Salvare e pubblicare</h2>
<p><span class="ui">Salva</span> si attiva quando c'è qualcosa da salvare, e salva insieme pagina, header e footer se li hai modificati nel canvas. Olobuild non salva da solo: se chiudi la scheda con modifiche non salvate, il browser chiede conferma.</p>
<p><span class="ui">Pubblica</span> mette online il template e la pagina di WordPress collegata, con il suo titolo.</p>
<div class="nota attenzione"><p>Dopo la pubblicazione ogni <strong>Salva</strong> va subito online: il sito mostra sempre l'ultima versione salvata. Per provare una modifica senza toccare il sito, duplica la pagina in Blocchi &amp; Pagine e lavora sulla copia.</p></div>
<p>A ogni salvataggio Olobuild conserva la versione precedente. Le trovi in <span class="ui">Cronologia revisioni</span>, nella barra in alto. La guida <a href="<?php echo esc_url( Olobuild_Guida::url( 'template' ) . '#revisioni' ); ?>">Template</a> spiega come rimetterne una.</p>
<p><span class="ui">Anteprima</span> nasconde i pannelli e mostra la pagina come la vede il visitatore. <span class="ui">Reale</span> apre la pagina vera in una nuova scheda. <kbd>Esc</kbd> riporta alla modifica.</p>

<h2 id="dispositivi">7. Computer, tablet e telefono</h2>
<p>I pulsanti dei dispositivi nella barra in alto ridimensionano il canvas: Widescreen, Desktop, Tablet e Mobile. Tablet orizzontale e Mobile orizzontale si accendono in <span class="ui">Sistema</span> › <span class="ui">Configurazione</span> › <span class="ui">Dispositivi dell'editor</span>. Molti controlli hanno un valore per ogni dispositivo. Quando ne modifichi uno, cambi il valore del dispositivo scelto nella barra.</p>
<p>Lo zoom, accanto ai dispositivi, rimpicciolisce o ingrandisce il canvas senza cambiare la pagina.</p>

<h2 id="scorciatoie">8. Scorciatoie da tastiera</h2>
<p>Su Mac usa <kbd>Cmd</kbd> al posto di <kbd>Ctrl</kbd>. Nel builder l'elenco è sempre a portata di mano: è l'icona a forma di tastiera nella barra in alto.</p>
<table>
	<thead><tr><th>Tasti</th><th>Azione</th></tr></thead>
	<tbody>
		<tr><td><kbd>Ctrl</kbd> <kbd>S</kbd></td><td>Salva</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Z</kbd> · <kbd>Ctrl</kbd> <kbd>Shift</kbd> <kbd>Z</kbd></td><td>Annulla · Ripeti, fino a 100 passi</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>C</kbd> · <kbd>Ctrl</kbd> <kbd>V</kbd> · <kbd>Ctrl</kbd> <kbd>D</kbd></td><td>Copia · Incolla · Duplica l'elemento selezionato</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Alt</kbd> <kbd>C</kbd> · <kbd>Ctrl</kbd> <kbd>Alt</kbd> <kbd>V</kbd></td><td>Copia stile · Incolla stile</td></tr>
		<tr><td><kbd>Canc</kbd> o <kbd>Backspace</kbd></td><td>Elimina l'elemento selezionato</td></tr>
		<tr><td><kbd>Ctrl</kbd> + clic</td><td>Seleziona più elementi</td></tr>
		<tr><td><kbd>Alt</kbd> <kbd>↑</kbd> · <kbd>Alt</kbd> <kbd>↓</kbd></td><td>Sposta su · Sposta giù</td></tr>
		<tr><td><kbd>Alt</kbd> <kbd>←</kbd> · <kbd>Alt</kbd> <kbd>→</kbd></td><td>Sposta nella colonna accanto</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>K</kbd></td><td>Cerca una tile (Finder)</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Alt</kbd> <kbd>P</kbd></td><td>Anteprima. <kbd>Esc</kbd> per uscire</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Shift</kbd> <kbd>A</kbd></td><td>Assistente AI, se è configurato</td></tr>
	</tbody>
</table>
