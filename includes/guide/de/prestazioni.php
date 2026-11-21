<?php
/**
 * Guida «Prestazioni» (tedesco). Solo HTML: titolo, descrizione e minuti
 * li stampa Olobuild_Guida::render_guida() dall'elenco delle guide.
 *
 * @package Olobuild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<nav class="olo-guida-toc" aria-label="In dieser Anleitung">
	<strong>In dieser Anleitung</strong>
	<ol>
		<li><a href="#da-solo">Was Olobuild von selbst macht</a></li>
		<li><a href="#impostazioni">Die Einstellungen Performance &amp; Cache</a></li>
		<li><a href="#cache">Der Ganzseiten-Cache</a></li>
		<li><a href="#esterne">Externe Caches und Leeren</a></li>
		<li><a href="#pagina">Eine schnelle Seite, in der Praxis</a></li>
		<li><a href="#misura">Messen</a></li>
	</ol>
</nav>

<h2 id="da-solo">1. Was Olobuild von selbst macht</h2>
<p>Einige Optimierungen sind immer aktiv und haben keinen Schalter.</p>
<ul>
	<li><strong>Nur das Nötige.</strong> Stylesheets und Skripte von Olobuild werden nur auf Seiten geladen, die mit Olobuild gemacht sind, und die Skripte einer Tile, zum Beispiel die der Karte, nur dann, wenn die Tile auf der Seite ist.</li>
	<li><strong>Schriften von deinem Server.</strong> Die Google Fonts, die die Website benutzt, werden einmal heruntergeladen und von der Website ausgeliefert, ohne bei jedem Besuch Google aufzurufen. Klappt der Download nicht, benutzt die Seite die Systemschriften.</li>
	<li><strong>Inhalt bei Bedarf.</strong> Nach dem ersten Abschnitt wird der Inhalt eingebaut, wenn er gleich in den Bildschirm kommt. Die Tiles des ersten Abschnitts, auf jeden Fall die ersten 3, Formulare, Karten und Menüs sind immer bereit. Suchmaschinen und KI-Crawler bekommen die ganze Seite: Viele führen kein JavaScript aus und würden den Inhalt bei Bedarf nicht sehen.</li>
	<li><strong>Faule Bilder.</strong> Die Bilder laden, wenn sie gleich sichtbar werden.</li>
	<li><strong>Leichte Videos.</strong> Die Tile Video zeigt mit aktivem <span class="ui">Lazy Load (Facade)</span>, wie voreingestellt, das Titelbild und lädt den Player von YouTube oder Vimeo erst beim Klick.</li>
	<li><strong>Animationen ohne Gewicht.</strong> Die Einblendanimationen starten, wenn das Element in den Bildschirm kommt. Wer das Betriebssystem gebeten hat, Bewegung zu reduzieren, sieht die Elemente sofort, ohne Animation.</li>
</ul>

<h2 id="impostazioni">2. Die Einstellungen Performance &amp; Cache</h2>
<p>Du findest sie unter <span class="ui">System</span> › <span class="ui">Einrichtung</span> › <span class="ui">Leistung &amp; Dienste</span> › <span class="ui">Performance &amp; Cache</span> und über die Karte Performance im Dashboard.</p>
<table>
	<thead><tr><th>Einstellung</th><th>Was sie macht</th><th>Voreinstellung</th></tr></thead>
	<tbody>
		<tr><td><strong>Defer JavaScript</strong></td><td>Die Skripte von Olobuild laden, ohne die Seite aufzuhalten, und starten, wenn sie fertig ist.</td><td>An</td></tr>
		<tr><td><strong>CSS-Cache in statischen Dateien</strong></td><td>Legt den Stil jedes Templates in eine Datei, die der Browser behält, statt ihn in die Seite zu schreiben.</td><td>An</td></tr>
		<tr><td><strong>CSS minifizieren</strong></td><td>Entfernt Leerzeichen und Kommentare aus den Stylesheets von Olobuild.</td><td>An</td></tr>
		<tr><td><strong>Preload eigener Schriften</strong></td><td>Startet sofort den Download der Schriften für Text und Überschriften, in der benutzten Stärke: Der Text erscheint in seiner Schrift, ohne einen Moment später das Aussehen zu wechseln.</td><td>An</td></tr>
		<tr><td><strong>fetchpriority hero image</strong></td><td>Das erste Bild des ersten Abschnitts lädt sofort und mit hoher Priorität. Die anderen bleiben faul.</td><td>An</td></tr>
		<tr><td><strong>Lazy Load selbst gehosteter Videos</strong></td><td>Auf die Website hochgeladene Videos, die automatisch und ohne Ton laufen, starten, wenn sie in den Bildschirm kommen.</td><td>An</td></tr>
		<tr><td><strong>Ganzseiten-Cache</strong></td><td>Speichert die Seiten fertig und liefert sie den Besuchern aus, ohne sie neu zu bauen. Lies Kapitel 3, bevor du ihn einschaltest.</td><td>Aus</td></tr>
		<tr><td><strong>UIkit subset</strong></td><td>Verkleinert das Stylesheet von UIkit auf die Teile, die die Seiten wirklich benutzen. Es lernt beim Besuch der Seiten und korrigiert sich selbst.</td><td>Aus</td></tr>
		<tr><td><strong>CSS pro Tile</strong></td><td>Lädt den Stil einiger Tile-Familien nur dort, wo er gebraucht wird, etwa bei Karten, Galerien und Menüs.</td><td>Aus</td></tr>
		<tr><td><strong>Browser-Cache für Medien (.htaccess)</strong></td><td>Sagt dem Browser, Bilder, Schriften, CSS und JS lange aufzubewahren. Das funktioniert auf Apache- und LiteSpeed-Servern.</td><td>Aus</td></tr>
		<tr><td><strong>Automatisches DNS prefetch &amp; preconnect</strong></td><td>Bereitet auf den Seiten, die YouTube und Vimeo enthalten, die Verbindung dorthin schon vor. Die unter Eigene Domains eingetragenen Domains kommen immer dazu.</td><td>An</td></tr>
		<tr><td><strong>Head aufräumen</strong></td><td>Entfernt Skripte und Stile von WordPress, die viele Websites nicht benutzen: jQuery Migrate, Emoji, Block-Stile und die Stile des klassischen Themes.</td><td>Aus</td></tr>
		<tr><td><strong>Critical CSS</strong></td><td>Schreibt den Stil der ersten Abschnitte in die Seite (2, unter <span class="ui">Abschnitte above-the-fold</span>) und lädt den Rest gleich danach. Er wird nach den Tagen unter <span class="ui">Cache-Dauer</span> neu erstellt und wenn du das Template speicherst. <span class="ui">Critical CSS neu erzeugen</span> baut ihn sofort neu.</td><td>Aus</td></tr>
	</tbody>
</table>
<div class="nota attenzione"><p><strong>Block-CSS entfernen</strong> entfernt auch die globalen Stile von WordPress. Prüfe bei einem Block-Theme nach dem Einschalten, ob die Website noch ihre Farben und Schriften hat.</p></div>
<p>Oben auf der Karte zählt <span class="ui">Cache-Status</span> die Seiten im Ganzseiten-Cache, die Stildateien und die Seiten mit Critical CSS und sagt, wann der Cache zuletzt geleert wurde. <span class="ui">Alles leeren</span> löscht sie alle zusammen, mit dem UIkit-Subset und den temporären Daten von Olobuild.</p>

<h2 id="cache">3. Der Ganzseiten-Cache</h2>
<p>Der Ganzseiten-Cache speichert jede Seite fertig und liefert sie den Besuchern aus, ohne sie neu zu bauen. Auf einer Website mit vielen Besuchen ist das die Einstellung, die am meisten zählt.</p>
<ul>
	<li>Er gilt nicht für angemeldete Benutzer, für Warenkorb und Kasse von WooCommerce, für Sitemaps und für Adressen mit anderen Parametern als denen zur Nachverfolgung.</li>
	<li>Er hält getrennte Versionen für Computer und Handy und je Sprache.</li>
	<li>Jede Seite bleibt 8 Stunden im Cache.</li>
	<li>Er leert sich von selbst, wenn du im Builder oder in WordPress speicherst, Menüs, Theme, aktiven Header oder Footer, globale Farben und Schriften, Zuweisungsregeln oder Performance-Einstellungen änderst, und bei jedem Update von Olobuild.</li>
</ul>
<div class="nota attenzione"><p>Schalte ihn nicht ein, wenn die Website schon ein Cache-Plugin wie LiteSpeed Cache oder WP Rocket benutzt. In diesem Fall installiert Olobuild ihn nicht, und neben dem Schalter sagt die Karte, warum.</p></div>

<h2 id="esterne">4. Externe Caches und Leeren</h2>
<p>Olobuild steuert keine externen Caches. Benutzt die Website LiteSpeed Cache, WP Rocket, den Cache des Hostings oder Cloudflare, leere ihn selbst, nachdem du Farben, Schriften, Header oder Footer geändert hast: Sonst sehen die Besucher womöglich noch die vorige Version.</p>
<p><span class="ui">System</span> › <span class="ui">Werkzeuge</span> › <span class="ui">Cache von Olobuild</span> › <span class="ui">Dateien und Daten löschen</span> löscht die erzeugten Stildateien, den Ganzseiten-Cache und die temporären Daten von Olobuild, die beim nächsten Besuch neu entstehen. Das hilft, wenn eine Seite einen alten Stil zeigt. Es macht dasselbe wie <span class="ui">Alles leeren</span> auf der Karte Performance.</p>

<h2 id="pagina">5. Eine schnelle Seite, in der Praxis</h2>
<ol class="passi">
	<li><strong>Bilder in der richtigen Größe.</strong> Lade Bilder hoch, die höchstens so breit sind wie der Platz, den sie einnehmen, 2000 Pixel für ein Foto über die ganze Breite, als WebP oder komprimiertes JPEG.</li>
	<li><strong>Das erste Bild sofort.</strong> Mit aktivem <span class="ui">fetchpriority hero image</span>, wie voreingestellt, startet das erste Bild des ersten Abschnitts von selbst mit hoher Priorität. Steht das Hauptfoto weiter unten, öffne in seiner Tile Bild <span class="ui">Erweitert</span> › <span class="ui">SEO &amp; Barrierefreiheit</span> und wähle Eager bei <span class="ui">Laden des Bildes</span> und High bei <span class="ui">Fetch Priority</span>.</li>
	<li><strong>Wenige Schriften.</strong> 2 Familien reichen, mit 3 oder 4 Stärken insgesamt. Jede Stärke mehr ist eine Datei mehr zum Herunterladen.</li>
	<li><strong>Videos mit Titelbild.</strong> Benutze die Tile Video mit aktivem Lazy Load (Facade), statt den Code von YouTube in eine HTML-Tile zu kleben.</li>
	<li><strong>Effekte, wo sie zählen.</strong> Animierte Hintergründe, Parallaxe und Hintergrundvideos wiegen mehr als ein Foto. Setz sie dort ein, wo sie etwas erzählen.</li>
</ol>

<h2 id="misura">6. Messen</h2>
<p>Miss die Seite mit PageSpeed Insights von Google oder mit Lighthouse in den Entwicklertools von Chrome. Mach den Test in einem privaten Fenster: Angemeldet siehst du die Seite ohne Ganzseiten-Cache, langsamer als die der Besucher.</p>
<p>Wiederhole die Messung 2- oder 3-mal und schau vor allem auf das Laden des größten Elements (LCP) und die Stabilität des Layouts (CLS). Die Punktzahl ändert sich von einem Test zum nächsten und von einem Handy zum anderen.</p>
