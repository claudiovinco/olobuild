<?php
/**
 * Guida «Template» (italiano). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#cosa">Template e pagine</a></li>
		<li><a href="#tipi">I tipi di template</a></li>
		<li><a href="#elenco">Blocchi &amp; Pagine</a></li>
		<li><a href="#header-footer">Quale header vede una pagina</a></li>
		<li><a href="#regole">Regole di assegnazione</a></li>
		<li><a href="#revisioni">Revisioni</a></li>
		<li><a href="#pronti">Blocchi pronti, temi ed elementi globali</a></li>
		<li><a href="#import">Importare ed esportare</a></li>
	</ol>
</nav>

<h2 id="cosa">1. Template e pagine</h2>
<p>Un template è il contenuto costruito con Olobuild: sezioni, righe, colonne e tile. Una pagina di WordPress mostra il template collegato a lei, e il collegamento nasce da solo quando crei la pagina da Olobuild.</p>
<p>Un template si può anche inserire ovunque WordPress accetti uno shortcode. In Blocchi &amp; Pagine un clic sullo shortcode lo copia, nella forma <code>[olo_template id="12"]</code>.</p>
<div class="nota"><p>Se la pagina di WordPress finisce nel cestino, al salvataggio successivo del template Olobuild ne crea una nuova, collegata allo stesso template.</p></div>

<h2 id="tipi">2. I tipi di template</h2>
<table>
	<thead><tr><th>Tipo</th><th>A cosa serve</th></tr></thead>
	<tbody>
		<tr><td><strong>Pagina</strong></td><td>Il contenuto di una pagina del sito.</td></tr>
		<tr><td><strong>Header</strong></td><td>La testata con logo e menu, in cima alle pagine.</td></tr>
		<tr><td><strong>Footer</strong></td><td>Il piede, in fondo alle pagine.</td></tr>
		<tr><td><strong>Single</strong></td><td>Il modello comune a tutti i contenuti di un tipo, per esempio ogni articolo o ogni prodotto. Si crea da Template Single, scegliendo il tipo di contenuto.</td></tr>
		<tr><td><strong>Mega Panel</strong></td><td>Il contenuto di un pannello del megamenu.</td></tr>
		<tr><td><strong>Widget</strong></td><td>Un contenuto riusabile dentro schede, tab e slider.</td></tr>
		<tr><td><strong>404</strong></td><td>La pagina che compare quando un indirizzo non esiste.</td></tr>
	</tbody>
</table>

<h2 id="elenco">3. Blocchi &amp; Pagine</h2>
<p><span class="ui">Costruisci</span> › <span class="ui">Blocchi &amp; Pagine</span> elenca tutti i template del sito. I filtri in alto mostrano un tipo alla volta, e ogni scheda dice lo stato: Bozza, Pubblicato o Attivo. Dalle azioni di una scheda puoi modificare, duplicare, esportare ed eliminare il template.</p>
<div class="nota attenzione"><p><strong>Elimina</strong> cancella il template per sempre. La pagina di WordPress collegata resta, ma non mostra più il contenuto fatto con Olobuild.</p></div>

<h2 id="header-footer">4. Quale header vede una pagina</h2>
<p>Ogni pagina mostra un header e un footer. Olobuild li sceglie in quest'ordine, e si ferma alla prima risposta che trova.</p>
<figure class="olo-schema">
	<div class="flusso">
		<div><strong>1. La scelta della pagina</strong>Il riquadro Olobuild nell'editor di WordPress, con i menu Header e Footer.</div>
		<div class="freccia" aria-hidden="true"><?php echo Olobuild_Guida::icona( 'freccia', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico. ?></div>
		<div><strong>2. Le regole di assegnazione</strong>Per esempio un header diverso per gli articoli del blog.</div>
		<div class="freccia" aria-hidden="true"><?php echo Olobuild_Guida::icona( 'freccia', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico. ?></div>
		<div><strong>3. Header e footer attivi</strong>Quelli che valgono per tutto il sito.</div>
	</div>
	<figcaption>Con «Predefinito (globale)» nel riquadro della pagina, decidono le regole o, in mancanza, l'header e il footer attivi.</figcaption>
</figure>
<p>Per rendere attivo un header o un footer, aprilo nel builder, pubblicalo e premi <span class="ui">Attiva</span> nella barra in alto. Lo stesso vale per un template Single e per la pagina 404.</p>
<p>Nel canvas di una pagina, header e footer dicono da quale template vengono e se li usano anche altre pagine. <span class="ui">Apri il template</span> li apre nel builder, e un pulsante riporta poi alla pagina da cui sei partito.</p>

<h2 id="regole">5. Regole di assegnazione</h2>
<p>Le regole decidono quale header, footer, Single o archivio usare in una parte del sito. Si trovano in <span class="ui">Sistema</span> › <span class="ui">Configurazione</span> › <span class="ui">Contenuti &amp; Template</span> › <span class="ui">Assegnazione template</span>.</p>
<ol class="passi">
	<li><strong>Crea la regola.</strong> Premi <span class="ui">Nuova regola</span>, scegli il <span class="ui">Contesto</span> (Header, Footer, Single o Archive) e il template da usare.</li>
	<li><strong>Aggiungi le condizioni.</strong> Con <span class="ui">Aggiungi condizione</span> scegli dove vale la regola, e per ogni condizione se includere o escludere. Il valore si sceglie da un menu.</li>
	<li><strong>Combina.</strong> <span class="ui">Operatore condizioni</span> dice se devono valere tutte le condizioni o almeno una.</li>
	<li><strong>Dai la priorità.</strong> Quando due regole valgono per la stessa pagina vince quella con il numero di <span class="ui">Priorità</span> più basso. Il valore di serie è 10.</li>
</ol>
<p>Le condizioni disponibili sono: Tutto il sito, Solo homepage, Tutte le pagine singole, Pagina, Articolo, Tipo di contenuto, Archivio, Categoria, Tag, Utente loggato, Utente non loggato, Ruolo utente, Pagina 404, Pagina ricerca, e 4 condizioni per WooCommerce.</p>
<div class="nota"><p>Senza regole valgono l'header e il footer attivi. Una regola serve solo dove vuoi qualcosa di diverso.</p></div>

<h2 id="revisioni">6. Revisioni</h2>
<p>A ogni salvataggio Olobuild conserva la versione precedente del template, fino a 50 versioni. Le trovi nel builder, in <span class="ui">Cronologia revisioni</span>, con data e peso.</p>
<ol class="passi">
	<li>Apri <span class="ui">Cronologia revisioni</span> dalla barra in alto.</li>
	<li>Premi <span class="ui">Ripristina</span> sulla versione che ti serve: il canvas la carica.</li>
	<li>Controlla la pagina e premi <span class="ui">Salva</span>. Finché non salvi, il sito resta com'era.</li>
</ol>

<h2 id="pronti">7. Blocchi pronti, temi ed elementi globali</h2>
<p>Il pulsante <span class="ui">Blocchi &amp; Pagine</span> nella barra del builder apre la libreria: 89 blocchi, 58 pagine intere e le sezioni che hai salvato tu con <span class="ui">Salva sezione come template</span> dal clic destro. Una pagina intera si può usare in due modi: <span class="ui">Sostituisci tutto</span> o <span class="ui">Aggiungi in fondo</span>.</p>
<p><span class="ui">Temi sito</span> apre gli stessi temi della prima configurazione. Un tema cambia l'intero sito, e prima di confermare la finestra elenca tutto ciò che cambierà.</p>
<p>Un elemento si può rendere <strong>globale</strong> dal clic destro, con <span class="ui">Salva come globale</span>. Un elemento globale si inserisce dalla categoria Globali del pannello Elementi ed è lo stesso in tutte le pagine che lo usano. <span class="ui">Sgancia globale</span> lo trasforma in una copia indipendente.</p>

<h2 id="import">8. Importare ed esportare</h2>
<p><span class="ui">Costruisci</span> › <span class="ui">Importa / Esporta</span> sposta il lavoro fra siti diversi con file <code>.json</code>.</p>
<ul>
	<li><strong>Esporta Template</strong> salva un template, anche con le sue immagini se scegli <span class="ui">Includi media</span>.</li>
	<li><strong>Esporta Sito Completo</strong> salva template, pagine, home, menu, media, stili e opzioni globali.</li>
	<li><strong>Importa</strong> carica un file esportato da un altro sito Olobuild.</li>
</ul>
<div class="nota attenzione"><p>Importare un sito completo <strong>sostituisce</strong> colori, caratteri e opzioni globali del sito che lo riceve. Esporta prima il sito attuale, se vuoi poter tornare indietro.</p></div>
<p>In Blocchi &amp; Pagine <span class="ui">Seleziona</span> e poi <span class="ui">Esporta tema</span> mettono più template in un file solo. Nel builder, l'import di un file JSON crea un template nuovo in bozza e lascia intatto quello aperto.</p>
