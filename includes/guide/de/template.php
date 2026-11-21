<?php
/**
 * Guida «Template» (tedesco). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#cosa">Templates und Seiten</a></li>
		<li><a href="#tipi">Die Template-Typen</a></li>
		<li><a href="#elenco">Blöcke &amp; Seiten</a></li>
		<li><a href="#header-footer">Welchen Header eine Seite bekommt</a></li>
		<li><a href="#regole">Zuweisungsregeln</a></li>
		<li><a href="#revisioni">Revisionen</a></li>
		<li><a href="#pronti">Fertige Blöcke, Themes und globale Elemente</a></li>
		<li><a href="#import">Importieren und exportieren</a></li>
	</ol>
</nav>

<h2 id="cosa">1. Templates und Seiten</h2>
<p>Ein Template ist der Inhalt, den du mit Olobuild baust: Abschnitte, Zeilen, Spalten und Tiles. Eine WordPress-Seite zeigt das Template, das mit ihr verbunden ist, und die Verbindung entsteht von selbst, wenn du die Seite in Olobuild anlegst.</p>
<p>Ein Template lässt sich auch überall einfügen, wo WordPress einen Shortcode annimmt. In Blöcke &amp; Seiten kopiert ein Klick auf den Shortcode ihn, in der Form <code>[olo_template id="12"]</code>.</p>
<div class="nota"><p>Landet die WordPress-Seite im Papierkorb, legt Olobuild beim nächsten Speichern des Templates eine neue an, verbunden mit demselben Template.</p></div>

<h2 id="tipi">2. Die Template-Typen</h2>
<table>
	<thead><tr><th>Typ</th><th>Wofür er da ist</th></tr></thead>
	<tbody>
		<tr><td><strong>Seite</strong></td><td>Der Inhalt einer Seite der Website.</td></tr>
		<tr><td><strong>Header</strong></td><td>Die Kopfzeile mit Logo und Menü, oben auf den Seiten.</td></tr>
		<tr><td><strong>Footer</strong></td><td>Die Fußzeile, unten auf den Seiten.</td></tr>
		<tr><td><strong>Single</strong></td><td>Das gemeinsame Layout aller Inhalte eines Typs, zum Beispiel jedes Beitrags oder jedes Produkts. Du legst es über Single-Template an und wählst den Inhaltstyp.</td></tr>
		<tr><td><strong>Mega Panel</strong></td><td>Der Inhalt eines Panels im Mega-Menü.</td></tr>
		<tr><td><strong>Widget</strong></td><td>Ein wiederverwendbarer Inhalt in Karten, Tabs und Slidern.</td></tr>
		<tr><td><strong>404</strong></td><td>Die Seite, die erscheint, wenn es eine Adresse nicht gibt.</td></tr>
	</tbody>
</table>

<h2 id="elenco">3. Blöcke &amp; Seiten</h2>
<p><span class="ui">Bauen</span> › <span class="ui">Blöcke &amp; Seiten</span> listet alle Templates der Website auf. Die Filter oben zeigen einen Typ nach dem anderen, und jede Karte nennt den Status: Entwurf, Veröffentlicht oder Aktiv. Über die Aktionen einer Karte kannst du das Template bearbeiten, duplizieren, exportieren und löschen.</p>
<div class="nota attenzione"><p><strong>Löschen</strong> entfernt das Template endgültig. Die verbundene WordPress-Seite bleibt, zeigt aber den mit Olobuild gemachten Inhalt nicht mehr.</p></div>

<h2 id="header-footer">4. Welchen Header eine Seite bekommt</h2>
<p>Jede Seite zeigt einen Header und einen Footer. Olobuild wählt sie in dieser Reihenfolge und hält bei der ersten Antwort an, die es findet.</p>
<figure class="olo-schema">
	<div class="flusso">
		<div><strong>1. Die Wahl der Seite</strong>Der Olobuild-Kasten im WordPress-Editor, mit den Menüs Header und Footer.</div>
		<div class="freccia" aria-hidden="true"><?php echo Olobuild_Guida::icona( 'freccia', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico. ?></div>
		<div><strong>2. Die Zuweisungsregeln</strong>Zum Beispiel ein anderer Header für die Blogbeiträge.</div>
		<div class="freccia" aria-hidden="true"><?php echo Olobuild_Guida::icona( 'freccia', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico. ?></div>
		<div><strong>3. Aktiver Header und Footer</strong>Die, die für die ganze Website gelten.</div>
	</div>
	<figcaption>Mit „Standard (global)“ im Kasten der Seite entscheiden die Regeln oder, wenn es keine gibt, der aktive Header und Footer.</figcaption>
</figure>
<p>Um einen Header oder Footer zu aktivieren, öffne ihn im Builder, veröffentliche ihn und drücke in der oberen Leiste auf <span class="ui">Aktivieren</span>. Dasselbe gilt für ein Single-Template und für die 404-Seite.</p>
<p>Im Canvas einer Seite zeigen Header und Footer, aus welchem Template sie stammen und ob auch andere Seiten sie benutzen. <span class="ui">Template öffnen</span> öffnet sie im Builder, und eine Schaltfläche bringt dich danach zur Seite zurück, von der du gekommen bist.</p>

<h2 id="regole">5. Zuweisungsregeln</h2>
<p>Die Regeln entscheiden, welcher Header, Footer, welches Single oder Archiv in einem Teil der Website benutzt wird. Du findest sie unter <span class="ui">System</span> › <span class="ui">Einrichtung</span> › <span class="ui">Inhalte &amp; Templates</span> › <span class="ui">Template-Zuweisung</span>.</p>
<ol class="passi">
	<li><strong>Leg die Regel an.</strong> Drücke auf <span class="ui">Neue Regel</span>, wähle den <span class="ui">Kontext</span> (Header, Footer, Single oder Archive) und das Template, das benutzt werden soll.</li>
	<li><strong>Füge die Bedingungen hinzu.</strong> Mit <span class="ui">Bedingung hinzufügen</span> wählst du, wo die Regel gilt, und bei jeder Bedingung, ob eingeschlossen oder ausgeschlossen wird. Den Wert wählst du aus einem Menü.</li>
	<li><strong>Kombiniere.</strong> <span class="ui">Bedingungsoperator</span> sagt, ob alle Bedingungen zutreffen müssen oder mindestens eine.</li>
	<li><strong>Leg die Priorität fest.</strong> Gelten zwei Regeln für dieselbe Seite, gewinnt die mit der niedrigeren Zahl bei <span class="ui">Priorität</span>. Der Standardwert ist 10.</li>
</ol>
<p>Die verfügbaren Bedingungen sind: Ganze Website, Nur Startseite, Alle Einzelseiten, Seite, Beitrag, Inhaltstyp, Archiv, Kategorie, Schlagwort, Angemeldeter Benutzer, Nicht angemeldeter Benutzer, Benutzerrolle, 404-Seite, Suchseite und 4 Bedingungen für WooCommerce.</p>
<div class="nota"><p>Ohne Regeln gelten der aktive Header und Footer. Eine Regel brauchst du nur dort, wo du etwas anderes willst.</p></div>

<h2 id="revisioni">6. Revisionen</h2>
<p>Bei jedem Speichern bewahrt Olobuild die vorige Version des Templates auf, bis zu 50 Versionen. Du findest sie im Builder unter <span class="ui">Revisionsverlauf</span>, mit Datum und Größe.</p>
<ol class="passi">
	<li>Öffne <span class="ui">Revisionsverlauf</span> in der oberen Leiste.</li>
	<li>Drücke bei der Version, die du brauchst, auf <span class="ui">Wiederherstellen</span>: Der Canvas lädt sie.</li>
	<li>Prüfe die Seite und drücke auf <span class="ui">Speichern</span>. Solange du nicht speicherst, bleibt die Website, wie sie war.</li>
</ol>

<h2 id="pronti">7. Fertige Blöcke, Themes und globale Elemente</h2>
<p>Die Schaltfläche <span class="ui">Blöcke &amp; Seiten</span> in der Leiste des Builders öffnet die Bibliothek: 89 Blöcke, 58 ganze Seiten und die Abschnitte, die du selbst mit <span class="ui">Abschnitt als Template speichern</span> über die rechte Maustaste gespeichert hast. Eine ganze Seite lässt sich auf zwei Arten benutzen: <span class="ui">Alles ersetzen</span> oder <span class="ui">Am Ende hinzufügen</span>.</p>
<p><span class="ui">Site-Themes</span> öffnet dieselben Themes wie bei der ersten Einrichtung. Ein Theme ändert die ganze Website, und bevor du bestätigst, listet das Fenster alles auf, was sich ändern wird.</p>
<p>Ein Element lässt sich über die rechte Maustaste mit <span class="ui">Als global speichern</span> <strong>global</strong> machen. Ein globales Element fügst du aus der Kategorie Global des Panels Elemente ein, und es ist auf allen Seiten, die es benutzen, dasselbe. <span class="ui">Global lösen</span> macht daraus eine unabhängige Kopie.</p>

<h2 id="import">8. Importieren und exportieren</h2>
<p><span class="ui">Bauen</span> › <span class="ui">Importieren / Exportieren</span> bringt die Arbeit mit <code>.json</code>-Dateien von einer Website zur anderen.</p>
<ul>
	<li><strong>Template exportieren</strong> speichert ein Template, auch mit seinen Bildern, wenn du <span class="ui">Medien einschließen</span> wählst.</li>
	<li><strong>Komplette Website exportieren</strong> speichert Templates, Seiten, Startseite, Menüs, Medien, Stile und globale Optionen.</li>
	<li><strong>Importieren</strong> lädt eine Datei, die aus einer anderen Olobuild-Website exportiert wurde.</li>
</ul>
<div class="nota attenzione"><p>Der Import einer kompletten Website <strong>ersetzt</strong> Farben, Schriften und globale Optionen der Website, die ihn erhält. Exportiere vorher die aktuelle Website, wenn du zurückgehen können willst.</p></div>
<p>In Blöcke &amp; Seiten legen <span class="ui">Auswählen</span> und dann <span class="ui">Theme exportieren</span> mehrere Templates in eine einzige Datei. Im Builder legt der Import einer JSON-Datei ein neues Template als Entwurf an und lässt das geöffnete unberührt.</p>
