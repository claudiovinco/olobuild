<?php
/**
 * Guida «Primi passi» (tedesco). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#configurazione">Die erste Einrichtung</a></li>
		<li><a href="#bacheca">Das Dashboard</a></li>
		<li><a href="#crea">Eine Seite anlegen</a></li>
		<li><a href="#builder">Der Builder in 4 Bereichen</a></li>
		<li><a href="#elementi">Elemente hinzufügen und anordnen</a></li>
		<li><a href="#pubblica">Speichern und veröffentlichen</a></li>
		<li><a href="#dispositivi">Computer, Tablet und Handy</a></li>
		<li><a href="#scorciatoie">Tastenkürzel</a></li>
	</ol>
</nav>

<h2 id="configurazione">1. Die erste Einrichtung</h2>
<p>Bei der ersten Aktivierung öffnet Olobuild die geführte Einrichtung, in 3 Schritten. Hast du sie verschoben, öffnet das Willkommens-Panel im WordPress-Dashboard sie wieder mit <span class="ui">Einrichtung starten</span>.</p>
<ol class="passi">
	<li><strong>WordPress-Theme.</strong> <span class="ui">Installieren und aktivieren</span> setzt Hello Olobuild ein, das leichte Theme, das im Plugin enthalten ist. Mit <span class="ui">Aktuelles Theme verwenden</span> behältst du das Theme, das du schon hast.</li>
	<li><strong>Wähle ein Design.</strong> Wähle eines der 53 fertigen Themes oder <span class="ui">Leer</span> und drücke auf <span class="ui">Gewähltes Theme importieren</span>. Das Theme legt Seiten und Menüs an, aktiviert Header und Footer, übernimmt seine Farben und Schriften und legt die Startseite fest. Vor dem Import speichert Olobuild eine Momentaufnahme der Website, die du mit <span class="ui">Wiederherstellen</span> zurückholen kannst.</li>
	<li><strong>Alles bereit.</strong> <span class="ui">Olobuild öffnen</span> führt zum Dashboard, <span class="ui">Website ansehen</span> öffnet die Website.</li>
</ol>
<div class="nota"><p>Mit <strong>Leer</strong> bekommst du das Nötigste zum Start: eine Seite Home als Startseite, das Menü „Hauptmenü“, einen „Basis-Header“ und einen „Basis-Footer“, schon aktiv.</p></div>

<h2 id="bacheca">2. Das Dashboard</h2>
<p>Das Dashboard öffnest du über den Eintrag <span class="ui">Olobuild</span> im WordPress-Menü. Die obere Leiste ist auf allen Seiten von Olobuild gleich und teilt die Arbeit in 4 Bereiche.</p>
<table>
	<thead><tr><th>Bereich</th><th>Was er enthält</th></tr></thead>
	<tbody>
		<tr><td><strong>Bauen</strong></td><td>Blöcke &amp; Seiten (die Templates der Website), Popups, Importieren / Exportieren</td></tr>
		<tr><td><strong>Medien</strong></td><td>Suche nach Fotos und Videos für die Seiten</td></tr>
		<tr><td><strong>Sammeln</strong></td><td>Eingesandte Formulare, Newsletter, Tracking &amp; Analytics</td></tr>
		<tr><td><strong>System</strong></td><td>Einrichtung, Werkzeuge und diese Anleitung</td></tr>
	</tbody>
</table>
<p>Im Dashboard findest du <span class="ui">Mache dort weiter, wo du aufgehört hast</span>, die <span class="ui">Schnellaktionen</span>, die Karten der <span class="ui">Verwaltung</span> und rechts das Ressourcen-Center mit Neuigkeiten und Anleitungen. Das Suchfeld oben, oder <kbd>Strg</kbd> <kbd>K</kbd>, sucht Seiten, Templates und Einstellungen von jeder Seite von Olobuild aus.</p>

<h2 id="crea">3. Eine Seite anlegen</h2>
<p><span class="ui">Seite anlegen</span> unter den Schnellaktionen legt auf einen Schlag ein Template „Ohne Titel“ und die verbundene WordPress-Seite an, beide als Entwurf, und öffnet den Builder. Dasselbe macht <span class="ui">Neue Seite</span> oben im Dashboard.</p>
<p>Du kannst auch in WordPress anfangen:</p>
<ul>
	<li>in der Liste der Seiten, mit der Aktion <span class="ui">Mit Olobuild erstellen</span> unter dem Titel einer Seite</li>
	<li>in der Admin-Leiste, mit <span class="ui">Mit Olobuild bearbeiten</span>, während du eine Seite der Website ansiehst</li>
	<li>im WordPress-Editor, im Kasten <span class="ui">Olobuild</span>, wo du auch Header und Footer der Seite wählst</li>
</ul>
<div class="nota"><p>Gib der Seite ihren Titel, bevor du sie veröffentlichst. Den Titel änderst du mit einem Klick auf den Namen der Seite in der Leiste des Builders. WordPress leitet die Adresse der Seite im Moment der Veröffentlichung aus dem Titel ab.</p></div>

<h2 id="builder">4. Der Builder in 4 Bereichen</h2>
<figure class="olo-schema">
	<div class="finestra" aria-hidden="true">
		<div class="z-barra"><span class="num">1</span>Obere Leiste</div>
		<div class="z-palette"><span class="num">2</span>Elemente<br>und Struktur</div>
		<div class="z-canvas"><span class="num">3</span>Canvas</div>
		<div class="z-inspector"><span class="num">4</span>Inhalt · Stil<br>· Erweitert</div>
	</div>
	<ol class="legenda">
		<li><span class="num">1</span><span><strong>Obere Leiste.</strong> Titel, Seiteneinstellungen, Geräte und Zoom, Blöcke &amp; Seiten, Rückgängig und Wiederholen, Vorschau, Speichern und Veröffentlichen.</span></li>
		<li><span class="num">2</span><span><strong>Linkes Panel.</strong> <span class="ui">Elemente</span> sammelt die Tiles nach Kategorie, mit Zuletzt verwendet, Favoriten und Global. <span class="ui">Struktur</span> zeigt den Baum der Seite.</span></li>
		<li><span class="num">3</span><span><strong>Canvas.</strong> Die Seite, wie der Besucher sie sehen wird, gezeichnet von derselben Engine wie die Website, mit Header und Footer.</span></li>
		<li><span class="num">4</span><span><strong>Rechtes Panel.</strong> Es erscheint, wenn du ein Element auswählst: <span class="ui">Inhalt</span> für Texte, Bilder und Links, <span class="ui">Stil</span> für das Aussehen, <span class="ui">Erweitert</span> für Sichtbarkeit, Effekte und Positionierung.</span></li>
	</ol>
	<figcaption>Die 4 Bereiche des Builders. Das rechte Panel öffnet sich auch mit den Seiteneinstellungen.</figcaption>
</figure>
<p>Jede Seite besteht aus Abschnitten. Ein Abschnitt enthält Zeilen, eine Zeile enthält Spalten, und in den Spalten stehen die Elemente, die in Olobuild Tiles heißen.</p>
<figure class="olo-schema">
	<div class="albero" aria-hidden="true">
		<div class="l1">Abschnitt <small>Streifen über die ganze Breite, mit eigenem Hintergrund</small></div>
		<div class="l2">Zeile <small>teilt den Abschnitt in Spalten</small></div>
		<div class="l3">Spalte <small>eine andere Breite für jedes Gerät</small></div>
		<div class="l4">Tile <small>Titel, Text, Bild, Schaltfläche, Galerie…</small></div>
	</div>
	<figcaption>Die Hierarchie einer Seite, wie das Panel Struktur sie zeigt.</figcaption>
</figure>
<p>Im Stil einer Tile sind die Einstellungen in zwei Blöcke geteilt. <strong>Element</strong> wirkt auf das, was die Tile zeichnet, zum Beispiel die Schaltfläche. <strong>Container</strong> wirkt auf den Rahmen, den die Tile im Raster einnimmt, mit seinen Abständen und seinem Hintergrund.</p>

<h2 id="elementi">5. Elemente hinzufügen und anordnen</h2>
<ol class="passi">
	<li><strong>Hinzufügen.</strong> Zieh eine Tile aus dem Panel Elemente in den Canvas oder klicke auf ihre Karte: Die Tile wird nach dem ausgewählten Element eingefügt oder ganz unten auf der Seite. Auf einer leeren Seite benutze <span class="ui">Eine Tile hinzufügen</span> oder <span class="ui">Layout wählen</span>.</li>
	<li><strong>Suchen.</strong> <kbd>Strg</kbd> <kbd>K</kbd> öffnet den Finder, der eine Tile nach Namen findet, ohne dass du die Kategorien durchgehen musst.</li>
	<li><strong>Einen Abschnitt einfügen.</strong> Die Schaltfläche <span class="ui">+</span> zwischen zwei Abschnitten öffnet <span class="ui">Eine Tile oder eine Zeile einfügen</span>, mit einer neuen Tile, einer leeren Zeile oder einem fertigen Block aus der Bibliothek.</li>
	<li><strong>Verschieben und duplizieren.</strong> Fährst du über ein Element, erscheinen die Befehle, um es zu ziehen, nach oben oder unten zu verschieben, zu duplizieren und zu löschen. Die rechte Maustaste öffnet das ganze Menü, mit Kopieren, Einfügen, Stil kopieren und Stil einfügen.</li>
	<li><strong>Schreiben.</strong> Doppelklicke auf einen Text, um ihn direkt im Canvas zu bearbeiten. <kbd>Esc</kbd> bricht die laufende Bearbeitung ab.</li>
</ol>
<div class="nota"><p>Neben der Überschrift eines Feldes oder Abschnitts kann ein <strong>(i)</strong> erscheinen. Öffne es, um zu lesen, wofür die Einstellung da ist: Die Erklärungen stehen dort, damit das Panel aufgeräumt bleibt.</p></div>

<h2 id="pubblica">6. Speichern und veröffentlichen</h2>
<p><span class="ui">Speichern</span> wird aktiv, wenn es etwas zu speichern gibt, und speichert Seite, Header und Footer zusammen, wenn du sie im Canvas geändert hast. Olobuild speichert nicht von selbst: Schließt du den Tab mit ungespeicherten Änderungen, fragt der Browser nach einer Bestätigung.</p>
<p><span class="ui">Veröffentlichen</span> stellt das Template und die verbundene WordPress-Seite online, mit ihrem Titel.</p>
<div class="nota attenzione"><p>Nach der Veröffentlichung geht jedes <strong>Speichern</strong> sofort online: Die Website zeigt immer die zuletzt gespeicherte Version. Um eine Änderung auszuprobieren, ohne die Website anzufassen, dupliziere die Seite in Blöcke &amp; Seiten und arbeite an der Kopie.</p></div>
<p>Bei jedem Speichern bewahrt Olobuild die vorige Version auf. Du findest sie unter <span class="ui">Revisionsverlauf</span> in der oberen Leiste. Die Anleitung <a href="<?php echo esc_url( Olobuild_Guida::url( 'template' ) . '#revisioni' ); ?>">Templates</a> erklärt, wie du eine zurückholst.</p>
<p><span class="ui">Vorschau</span> blendet die Panels aus und zeigt die Seite so, wie der Besucher sie sieht. <span class="ui">Live</span> öffnet die echte Seite in einem neuen Tab. <kbd>Esc</kbd> bringt dich zurück zum Bearbeiten.</p>

<h2 id="dispositivi">7. Computer, Tablet und Handy</h2>
<p>Die Geräte-Schaltflächen in der oberen Leiste ändern die Größe des Canvas: Widescreen, Desktop, Tablet und Mobile. Tablet quer und Mobile quer schaltest du unter <span class="ui">System</span> › <span class="ui">Einrichtung</span> › <span class="ui">Geräte des Editors</span> ein. Viele Einstellungen haben einen Wert für jedes Gerät. Wenn du eine änderst, änderst du den Wert des Geräts, das in der Leiste gewählt ist.</p>
<p>Der Zoom neben den Geräten verkleinert oder vergrößert den Canvas, ohne die Seite zu ändern.</p>

<h2 id="scorciatoie">8. Tastenkürzel</h2>
<p>Auf dem Mac nimmst du <kbd>Cmd</kbd> statt <kbd>Strg</kbd>. Im Builder ist die Liste immer zur Hand: Es ist das Tastatursymbol in der oberen Leiste.</p>
<table>
	<thead><tr><th>Tasten</th><th>Aktion</th></tr></thead>
	<tbody>
		<tr><td><kbd>Strg</kbd> <kbd>S</kbd></td><td>Speichern</td></tr>
		<tr><td><kbd>Strg</kbd> <kbd>Z</kbd> · <kbd>Strg</kbd> <kbd>Umschalt</kbd> <kbd>Z</kbd></td><td>Rückgängig · Wiederholen, bis zu 100 Schritte</td></tr>
		<tr><td><kbd>Strg</kbd> <kbd>C</kbd> · <kbd>Strg</kbd> <kbd>V</kbd> · <kbd>Strg</kbd> <kbd>D</kbd></td><td>Kopieren · Einfügen · Ausgewähltes Element duplizieren</td></tr>
		<tr><td><kbd>Strg</kbd> <kbd>Alt</kbd> <kbd>C</kbd> · <kbd>Strg</kbd> <kbd>Alt</kbd> <kbd>V</kbd></td><td>Stil kopieren · Stil einfügen</td></tr>
		<tr><td><kbd>Entf</kbd> oder <kbd>Rücktaste</kbd></td><td>Ausgewähltes Element löschen</td></tr>
		<tr><td><kbd>Strg</kbd> + Klick</td><td>Mehrere Elemente auswählen</td></tr>
		<tr><td><kbd>Alt</kbd> <kbd>↑</kbd> · <kbd>Alt</kbd> <kbd>↓</kbd></td><td>Nach oben · Nach unten</td></tr>
		<tr><td><kbd>Alt</kbd> <kbd>←</kbd> · <kbd>Alt</kbd> <kbd>→</kbd></td><td>In die Spalte daneben verschieben</td></tr>
		<tr><td><kbd>Strg</kbd> <kbd>K</kbd></td><td>Eine Tile suchen (Finder)</td></tr>
		<tr><td><kbd>Strg</kbd> <kbd>Alt</kbd> <kbd>P</kbd></td><td>Vorschau. <kbd>Esc</kbd> zum Beenden</td></tr>
		<tr><td><kbd>Strg</kbd> <kbd>Umschalt</kbd> <kbd>A</kbd></td><td>KI-Assistent, wenn er eingerichtet ist</td></tr>
	</tbody>
</table>
