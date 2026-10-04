<?php
/**
 * Guida «Prestazioni» (italiano). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#da-solo">Cosa fa Olobuild da solo</a></li>
		<li><a href="#impostazioni">Le impostazioni Performance &amp; Cache</a></li>
		<li><a href="#cache">La full-page cache</a></li>
		<li><a href="#esterne">Cache esterne e pulizia</a></li>
		<li><a href="#pagina">Una pagina veloce, in pratica</a></li>
		<li><a href="#misura">Misurare</a></li>
	</ol>
</nav>

<h2 id="da-solo">1. Cosa fa Olobuild da solo</h2>
<p>Alcune ottimizzazioni sono sempre attive e non hanno interruttori.</p>
<ul>
	<li><strong>Solo il necessario.</strong> Fogli di stile e script di Olobuild si caricano solo nelle pagine fatte con Olobuild, e gli script di una tile, per esempio quelli della mappa, solo se la tile è nella pagina.</li>
	<li><strong>Caratteri dal tuo server.</strong> I Google Fonts usati dal sito vengono scaricati una volta e serviti dal sito, senza chiamate a Google a ogni visita. Se lo scaricamento non riesce, la pagina usa i caratteri di sistema.</li>
	<li><strong>Contenuto a richiesta.</strong> Dalla quarta tile in poi il contenuto viene montato quando sta per entrare nello schermo. Le prime 3 tile, i form, le mappe e i menu sono sempre pronti.</li>
	<li><strong>Immagini pigre.</strong> Le immagini si caricano quando stanno per diventare visibili.</li>
	<li><strong>Video leggeri.</strong> La tile Video, con <span class="ui">Lazy Load (Facade)</span> attivo come di serie, mostra la copertina e carica il lettore di YouTube o Vimeo solo al clic.</li>
	<li><strong>Animazioni senza peso.</strong> Le animazioni d'ingresso partono quando l'elemento entra nello schermo. Chi ha chiesto al sistema operativo di ridurre il movimento vede gli elementi subito, senza animazione.</li>
</ul>

<h2 id="impostazioni">2. Le impostazioni Performance &amp; Cache</h2>
<p>Si trovano in <span class="ui">Sistema</span> › <span class="ui">Configurazione</span> › <span class="ui">Prestazioni &amp; Servizi</span> › <span class="ui">Performance &amp; Cache</span>, e dalla scheda Performance della bacheca.</p>
<table>
	<thead><tr><th>Impostazione</th><th>Cosa fa</th><th>Di serie</th></tr></thead>
	<tbody>
		<tr><td><strong>Defer JavaScript</strong></td><td>Gli script di Olobuild si scaricano senza fermare la pagina e partono quando è pronta.</td><td>Attiva</td></tr>
		<tr><td><strong>Cache CSS su file statici</strong></td><td>Mette lo stile di ogni template in un file che il browser conserva, invece di riscriverlo nella pagina.</td><td>Attiva</td></tr>
		<tr><td><strong>Minifica CSS</strong></td><td>Toglie spazi e commenti dai fogli di stile di Olobuild.</td><td>Attiva</td></tr>
		<tr><td><strong>Preload font custom</strong></td><td>Avvia subito il download dei caratteri di testo e titoli, al peso usato: il testo compare con il suo carattere, senza cambiare aspetto dopo un attimo.</td><td>Attiva</td></tr>
		<tr><td><strong>fetchpriority hero image</strong></td><td>La prima immagine della prima sezione si carica subito e con priorità alta. Le altre restano pigre.</td><td>Attiva</td></tr>
		<tr><td><strong>Lazy load video self-hosted</strong></td><td>I video caricati sul sito, in riproduzione automatica e senza audio, partono quando entrano nello schermo.</td><td>Attiva</td></tr>
		<tr><td><strong>Full-page cache</strong></td><td>Salva le pagine già pronte e le serve ai visitatori senza ricostruirle. Leggi il capitolo 3 prima di attivarla.</td><td>Spenta</td></tr>
		<tr><td><strong>UIkit subset</strong></td><td>Riduce il foglio di stile UIkit alle parti che le pagine usano davvero. Impara visitando le pagine e si corregge da solo.</td><td>Spenta</td></tr>
		<tr><td><strong>CSS per-tile</strong></td><td>Carica lo stile di alcune famiglie di tile solo dove servono, come mappe, gallerie e menu.</td><td>Spenta</td></tr>
		<tr><td><strong>Cache browser per i media (.htaccess)</strong></td><td>Dice al browser di conservare a lungo immagini, caratteri, CSS e JS. Funziona sui server Apache e LiteSpeed.</td><td>Spenta</td></tr>
		<tr><td><strong>DNS prefetch &amp; preconnect automatici</strong></td><td>Prepara in anticipo la connessione a YouTube e Vimeo nelle pagine che li contengono. I domini scritti in Domini custom escono sempre.</td><td>Attiva</td></tr>
		<tr><td><strong>Pulizia head</strong></td><td>Toglie script e stili di WordPress che molti siti non usano: jQuery Migrate, emoji, stili dei blocchi e del tema classico.</td><td>Spenta</td></tr>
		<tr><td><strong>Critical CSS</strong></td><td>Scrive nella pagina lo stile delle prime sezioni (2, in <span class="ui">Sezioni above-the-fold</span>) e carica il resto subito dopo. Si rifà dopo i giorni di <span class="ui">Durata cache</span> e quando salvi il template. <span class="ui">Rigenera critical CSS</span> lo ricostruisce subito.</td><td>Spenta</td></tr>
	</tbody>
</table>
<div class="nota attenzione"><p><strong>Rimuovi Block CSS</strong> toglie anche gli stili globali di WordPress. Con un tema a blocchi, dopo averlo attivato controlla che il sito abbia ancora i suoi colori e i suoi caratteri.</p></div>
<p>In cima alla scheda, <span class="ui">Stato cache</span> conta le pagine in full-page cache, i file di stile e le pagine con Critical CSS, e dice quando la cache è stata svuotata l'ultima volta. <span class="ui">Svuota tutto</span> li cancella tutti insieme, con il subset UIkit e i dati temporanei di Olobuild.</p>

<h2 id="cache">3. La full-page cache</h2>
<p>La full-page cache salva ogni pagina già pronta e la serve ai visitatori senza ricostruirla. È la regolazione che conta di più su un sito con molte visite.</p>
<ul>
	<li>Non vale per chi ha fatto l'accesso, per carrello e cassa di WooCommerce, per le sitemap e per gli indirizzi con parametri diversi da quelli di tracciamento.</li>
	<li>Tiene versioni separate per computer e telefono, e per lingua.</li>
	<li>Ogni pagina resta in cache per 8 ore.</li>
	<li>Si svuota da sola quando salvi nel builder o in WordPress, cambi menu, tema, header o footer attivi, colori e caratteri globali, regole di assegnazione o impostazioni Performance, e a ogni aggiornamento di Olobuild.</li>
</ul>
<div class="nota attenzione"><p>Non attivarla se il sito usa già un plugin di cache come LiteSpeed Cache o WP Rocket. In quel caso Olobuild non la installa, e accanto all'interruttore la scheda dice perché.</p></div>

<h2 id="esterne">4. Cache esterne e pulizia</h2>
<p>Olobuild non comanda le cache esterne. Se il sito usa LiteSpeed Cache, WP Rocket, la cache dell'hosting o Cloudflare, svuotala tu dopo aver cambiato colori, caratteri, header o footer: altrimenti i visitatori possono vedere ancora la versione precedente.</p>
<p><span class="ui">Sistema</span> › <span class="ui">Strumenti</span> › <span class="ui">Cache di Olobuild</span> › <span class="ui">Cancella file e dati</span> cancella i file di stile generati, la full-page cache e i dati temporanei di Olobuild, che si ricreano alla visita successiva. Serve quando una pagina mostra uno stile vecchio. Fa lo stesso di <span class="ui">Svuota tutto</span> nella scheda Performance.</p>

<h2 id="pagina">5. Una pagina veloce, in pratica</h2>
<ol class="passi">
	<li><strong>Immagini alla misura giusta.</strong> Carica immagini larghe al massimo quanto lo spazio che occupano, 2000 pixel per una foto a tutta larghezza, in formato WebP o JPEG compresso.</li>
	<li><strong>La prima immagine subito.</strong> Con <span class="ui">fetchpriority hero image</span> attivo, come di serie, la prima immagine della prima sezione parte da sola con priorità alta. Se la foto principale sta più in basso, nella sua tile Immagine apri <span class="ui">Avanzate</span> › <span class="ui">SEO &amp; Accessibilità</span> e scegli Eager in <span class="ui">Caricamento immagine</span> e High in <span class="ui">Fetch Priority</span>.</li>
	<li><strong>Pochi caratteri.</strong> Bastano 2 famiglie, con 3 o 4 pesi in tutto. Ogni peso in più è un file da scaricare.</li>
	<li><strong>Video con copertina.</strong> Usa la tile Video con Lazy Load (Facade) attivo, invece di incollare il codice di YouTube in una tile HTML.</li>
	<li><strong>Effetti dove servono.</strong> Sfondi animati, parallasse e video di sfondo pesano più di una foto. Usali nei punti in cui raccontano qualcosa.</li>
</ol>

<h2 id="misura">6. Misurare</h2>
<p>Misura la pagina con PageSpeed Insights di Google o con Lighthouse, negli strumenti per sviluppatori di Chrome. Fai la prova in una finestra anonima: da collegato vedi la pagina senza full-page cache, più lenta di quella dei visitatori.</p>
<p>Ripeti la misura 2 o 3 volte e guarda soprattutto il caricamento dell'elemento più grande (LCP) e la stabilità del layout (CLS). Il punteggio cambia da una prova all'altra, e da un telefono all'altro.</p>
