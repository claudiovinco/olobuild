<?php
/**
 * Guida «SEO e Open Graph» (tedesco). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#dove">Wo man es einstellt</a></li>
		<li><a href="#titolo">Titel und Beschreibung</a></li>
		<li><a href="#analisi">Seitenanalyse</a></li>
		<li><a href="#social">Vorschau in sozialen Netzwerken</a></li>
		<li><a href="#robots">Indexierung und Canonical</a></li>
		<li><a href="#schema">Strukturierte Daten</a></li>
		<li><a href="#redirect">Weiterleitungen und 404</a></li>
		<li><a href="#sitemap">Sitemap</a></li>
		<li><a href="#verifica">Search Console und Bing</a></li>
		<li><a href="#plugin">Mit Yoast, Rank Math und Co.</a></li>
	</ol>
</nav>

<h2 id="dove">1. Wo man es einstellt</h2>
<p>Olobuild schreibt in den Kopfbereich jeder Seite der Website Titel, Beschreibung, Social-Vorschau und strukturierte Daten. Du stellst sie Seite für Seite ein, an zwei Stellen, die dieselben Daten speichern.</p>
<ul>
	<li><strong>Im Builder:</strong> <span class="ui">Seiteneinstellungen</span> › <span class="ui">SEO</span>, mit den Reitern Basis, Social, Robots, Schema und FAQ. Die Gruppe speichert sich von selbst, ohne dass du auf Speichern drückst, und erscheint, wenn das Template mit einer Seite verbunden ist.</li>
	<li><strong>Im WordPress-Editor:</strong> der Kasten <span class="ui">Olobuild SEO</span>, in Seiten, Beiträgen und den anderen öffentlichen Inhalten, mit der Vorschau des Ergebnisses auf Google.</li>
</ul>
<p>In den Listen der Seiten und Beiträge zeigt die Spalte SEO mit einem Punkt, wie viele der wichtigsten Felder ausgefüllt sind.</p>
<p>Die Werte, die für die ganze Website gelten, stehen unter <span class="ui">System</span> › <span class="ui">Einrichtung</span> › <span class="ui">SEO &amp; Datenschutz</span> › <span class="ui">Globale SEO</span>: Muster und Trennzeichen des Titels, die Standardbeschreibung, das Bild für soziale Netzwerke, das X-Profil und die Art dessen, der die Website veröffentlicht. Eine Seite mit eigenen ausgefüllten Feldern gewinnt immer.</p>

<h2 id="titolo">2. Titel und Beschreibung</h2>
<p><span class="ui">SEO Title</span> ist der Titel, der im Browser-Tab und in den Suchergebnissen erscheint. Der Zähler zeigt 60 Zeichen, die Länge, die Google vollständig zeigt. Lässt du ihn leer, gilt das Muster aus dem Reiter Globale SEO: standardmäßig der Seitentitel, das Trennzeichen und der Name der Website.</p>
<p><span class="ui">Meta Description</span> ist der Text unter dem Titel in den Ergebnissen, mit einem Zähler für 160 Zeichen. Fehlt sie, benutzt Olobuild die ersten 30 Wörter der Zusammenfassung der Seite, dann die Standardbeschreibung aus dem Reiter Globale SEO und, wenn auch die fehlt, den Untertitel der Website.</p>
<p><span class="ui">Focus keyword</span> ist das wichtigste Schlüsselwort der Seite, also die Wörter, die jemand benutzen würde, der sie sucht. Die Seitenanalyse prüft, wo es vorkommt.</p>

<h2 id="analisi">3. Seitenanalyse</h2>
<p>Unter dem Focus keyword, im Reiter Basis, liest <span class="ui">Seitenanalyse</span> die Seite so, wie Google und die KI-Assistenten sie sehen: den Text der Tiles, die Überschriften, die Bilder und die Links. Kopf- und Fußzeile bleiben außen vor. Änderungen zählen sofort, schon vor dem Speichern; die Schaltfläche mit dem Pfeil macht die Analyse neu.</p>
<ul>
	<li><strong>Zu korrigieren</strong>: was die Seite aus den Ergebnissen nimmt oder sie ohne Hauptüberschrift lässt, etwa ein eingeschaltetes noindex, die ganze Website vor den Suchmaschinen verborgen, keine H1-Überschrift.</li>
	<li><strong>Zu verbessern</strong>: Titel oder Beschreibung zu lang oder zu kurz, mehr als eine H1, Überschriften, die eine Ebene überspringen (von H2 zu H4), zu wenig Text, Bilder ohne Alternativtext, keine Links zu den anderen Seiten der Website, das Schlüsselwort fehlt an den Stellen, die zählen, oder wird schon von einer anderen Seite benutzt.</li>
	<li><strong>Bestanden</strong> und <strong>Überschriften der Seite</strong> öffnen sich mit einem Klick: Das Zweite ist das Verzeichnis der Überschriften H1–H6, mit denen, die zu richten sind, markiert.</li>
</ul>
<p>Das (i) neben jedem Eintrag erklärt, wie man es behebt. Die Analyse zählt nicht, wie oft du das Schlüsselwort wiederholst: Darauf schaut Google nicht mehr, es zählt, dass die Seite wirklich davon handelt. Im Kasten <span class="ui">Olobuild SEO</span> des WordPress-Editors gibt es dieselbe Analyse, auf der gespeicherten Seite.</p>

<h2 id="social">4. Vorschau in sozialen Netzwerken</h2>
<p>Teilt jemand die Seite auf Facebook, LinkedIn, WhatsApp oder X, entsteht die Vorschau aus den Open-Graph-Daten. Du findest sie im Reiter Social.</p>
<ul>
	<li><span class="ui">OG Title</span> und <span class="ui">OG Description</span> ändern Titel und Text nur für soziale Netzwerke. Leer gelten die aus dem Reiter Basis.</li>
	<li><span class="ui">OG Image</span> ist das Bild der Vorschau. Die richtige Größe ist 1200 × 630 Pixel.</li>
	<li>Die Felder für <span class="ui">Twitter / X</span> brauchst du nur, wenn du einen anderen Text willst: Leer benutzt X die von Open Graph.</li>
</ul>
<p>Ohne OG Image benutzt Olobuild das Beitragsbild der Seite, dann das Standardbild aus dem Reiter Globale SEO und zuletzt das Logo der Website. Das Format der X-Vorschau wählst du im selben Reiter. Ohne Bild ist die Vorschau ohnehin kompakt.</p>
<div class="nota"><p>Soziale Netzwerke behalten die Vorschau tagelang. Nachdem du sie geändert hast, aktualisiere sie mit dem Debugging-Werkzeug der Plattform, zum Beispiel dem Sharing Debugger von Facebook.</p></div>

<h2 id="robots">5. Indexierung und Canonical</h2>
<p><span class="ui">Robots default</span> im Reiter Globale SEO gilt für die ganze Website und ist dieselbe Wahl wie Einstellungen › Lesen in WordPress. Für eine einzelne Seite bittet <span class="ui">noindex</span> im Reiter Robots die Suchmaschinen, die Seite nicht in den Ergebnissen zu zeigen. Den Links der Seite wird weiter gefolgt, es sei denn, du schaltest auch <span class="ui">nofollow</span> ein. Benutze noindex für Serviceseiten, etwa eine Dankesseite nach einem Formular.</p>
<p><span class="ui">Canonical URL</span> gibt die Hauptadresse einer Seite an, die es in mehreren Versionen gibt. Leer gilt die Adresse der Seite selbst, und das ist fast immer die richtige Wahl.</p>

<h2 id="schema">6. Strukturierte Daten</h2>
<p>Strukturierte Daten beschreiben die Seite für Suchmaschinen in einem Format, das sie lesen, ohne den Text deuten zu müssen. Olobuild schreibt sie von selbst:</p>
<ul>
	<li>auf allen Seiten, wer die Website veröffentlicht, mit Logo und Social-Media-Profilen: Die Art (Organisation, Person, Hotel…) wählst du im Reiter Globale SEO</li>
	<li>auf der Startseite auch die Website selbst, mit ihrer internen Suche</li>
	<li>auf den anderen Seiten den Navigationspfad (Breadcrumb)</li>
	<li>bei Beiträgen den Typ Artikel, bei Seiten den Typ Seite, bei WooCommerce-Produkten den Typ Produkt</li>
</ul>
<p>Alle diese Daten kommen als ein einziger Graph heraus, miteinander verbunden: Die Seite gehört zur Website, der Artikel ist der Inhalt der Seite, Website und Artikel werden von dem veröffentlicht, der die Website veröffentlicht. So erkennen Suchmaschinen und KI, wer du bist.</p>
<p>Im Reiter FAQ sammelt <span class="ui">FAQ hinzufügen</span> Fragen und Antworten, die zu Daten vom Typ FAQ werden und von Suchmaschinen und KI-Assistenten gelesen werden. Google zeigt angereicherte FAQs in den Ergebnissen nur für Websites von Behörden und aus dem Gesundheitswesen. Die Tile Accordion hat denselben Schalter, <span class="ui">FAQ-Schema (SEO)</span>, und die Tile Karte kann den Ort mit <span class="ui">Schema.org JSON-LD (SEO)</span> beschreiben.</p>
<p>Unter <span class="ui">Schema.org type</span> haben zwei Typen eigene Felder, die unter der Auswahl erscheinen:</p>
<ul>
	<li><strong>Event</strong>, für ein Konzert, eine Aufführung oder einen Kurs: Beginn und Ende, vor Ort oder online, Ort und Adresse, Status (geplant, verschoben, abgesagt), Veranstalter, Ticketpreis und wo man bucht.</li>
	<li><strong>SoftwareApplication</strong>, für ein Programm oder eine App: Kategorie, worauf es läuft, Version und Preis, 0, wenn es kostenlos ist.</li>
</ul>
<p>Die Seitenanalyse meldet die fehlenden Felder, etwa ein Event ohne Anfangsdatum.</p>
<div class="nota"><p>Die anderen Typen, zum Beispiel Recipe oder HowTo, erzeugen nur Name, Beschreibung und Bild. Die Daten, die Google für angereicherte Ergebnisse braucht, schreibst du in <span class="ui">Eigenes JSON-LD</span>, das Olobuild vor der Veröffentlichung prüft.</p></div>

<h2 id="redirect">7. Weiterleitungen und 404</h2>
<p>Wenn eine Seite ihre Adresse ändert, bringt eine Weiterleitung jeden, der über den alten Link kommt, zur neuen Seite. Die Verwaltung steht unter <span class="ui">System</span> › <span class="ui">Einrichtung</span> › <span class="ui">SEO &amp; Datenschutz</span> › <span class="ui">Weiterleitungen &amp; 404</span>, und das Dashboard meldet die 404-Adressen, die noch keine Weiterleitung haben.</p>
<ol class="passi">
	<li><strong>Füge die Weiterleitung hinzu.</strong> <span class="ui">Weiterleitung hinzufügen</span>, dann die alte Adresse in <span class="ui">Von URL</span> und die neue in <span class="ui">Nach URL</span>.</li>
	<li><strong>Wähle den Typ.</strong> 301 für einen endgültigen Umzug, der häufigste Fall. 302 und 307 für einen vorübergehenden. 410, um zu sagen, dass die Seite entfernt wurde und nicht wiederkommt.</li>
	<li><strong>Prüfe die 404.</strong> Das 404-Protokoll listet die angefragten Adressen, die es nicht gibt, bis zu 500. <span class="ui">Weiterleitung erstellen</span> in einer Zeile bereitet eine 301-Weiterleitung vor: Korrigiere das Ziel, bevor du sie speicherst.</li>
</ol>
<p>Um eine Gruppe von Adressen umzuleiten, beginne <span class="ui">Von URL</span> mit <code>~</code> und schreib einen regulären Ausdruck. <code>$1</code> im Ziel übernimmt den erfassten Teil.</p>
<div class="nota attenzione"><p>Eine Weiterleitung gilt auch, wenn die Ausgangsadresse eine Seite ist, die es gibt. Bevor du eine auf einer benutzten Adresse anlegst, prüfe, dass diese Seite nicht mehr gebraucht wird.</p></div>
<p>Die Ansicht IndexNow benachrichtigt Bing und die anderen teilnehmenden Suchmaschinen, wenn du eine Seite veröffentlichst oder aktualisierst, auch aus dem Builder. <span class="ui">Erzeugen</span> legt einen Schlüssel an, und Olobuild veröffentlicht von selbst die Bestätigungsdatei, die die Suchmaschinen prüfen: Der Link erscheint nach dem Speichern unter dem Schlüssel.</p>

<h2 id="sitemap">8. Sitemap</h2>
<p>Die Sitemap listet die Seiten der Website für die Suchmaschinen auf. Es ist die von WordPress, unter der Adresse <code>/wp-sitemap.xml</code>: Olobuild nimmt die Seiten mit noindex heraus, und der Schalter XML-Sitemap im Reiter Globale SEO schaltet sie ab. Reich sie in der Google Search Console ein. Benutzt du ein SEO-Plugin, gilt dessen Sitemap.</p>

<h2 id="verifica">9. Search Console und Bing</h2>
<p>Google Search Console und Bing Webmaster Tools sagen dir, bei welchen Suchen die Website erscheint, wie oft sie angeklickt wird und ob es Seiten gibt, die die Suchmaschinen nicht lesen können. Bing zählt auch für Copilot. Um hineinzukommen, musst du beweisen, dass die Website dir gehört, und dafür gibt es zwei Wege.</p>
<p><strong>Über den DNS, der beste Weg.</strong> Wähle in der <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Search Console</a> die Property „Domain“ und schreib die Domain, zum Beispiel <code>meineseite.de</code>. Google gibt dir einen TXT-Eintrag, den du im DNS der Domain hinzufügst, im Panel dessen, der sie für dich verwaltet. Einmal bestätigt, gilt sie für die ganze Domain, Subdomains und Ordner eingeschlossen, und in Olobuild gibt es nichts auszufüllen. Findet Olobuild diesen Code im DNS, schreibt es das unter <span class="ui">System</span> › <span class="ui">Einrichtung</span> › <span class="ui">SEO &amp; Datenschutz</span> › <span class="ui">Globale SEO</span> › <span class="ui">Bestätigung bei Suchmaschinen</span>, unter dem Feld von Google: „Code von Google im DNS gefunden“.</p>
<p><strong>Über den Code im Kopfbereich, wenn du den DNS nicht ändern kannst.</strong></p>
<ol class="passi">
	<li><strong>Füge die Website hinzu.</strong> Wähle in der Search Console „URL-Präfix“, schreib die Adresse der Website und als Bestätigung „HTML-Tag“.</li>
	<li><strong>Kopiere den Code.</strong> Google zeigt ein Meta-Tag: Kopiere es ganz oder nur den Code zwischen den Anführungszeichen von content.</li>
	<li><strong>Füge ihn ein.</strong> Unter <span class="ui">Bestätigung bei Suchmaschinen</span>, im Feld Google Search Console, dann Speichern. Olobuild behält nur den Code und schreibt ihn in den Kopfbereich jeder Seite.</li>
	<li><strong>Bestätige.</strong> Geh zurück in die Search Console und drücke auf Bestätigen.</li>
</ol>
<p>In beiden Fällen fügst du danach die Sitemap, <code>/wp-sitemap.xml</code>, im Bereich Sitemaps der Search Console hinzu. Hat die Website weitere WordPress-Websites in Ordnern, etwa <code>meineseite.de/shop</code>, hat jede ihre eigene Sitemap: Füge sie alle hinzu.</p>
<p>Für die <a href="https://www.bing.com/webmasters" target="_blank" rel="noopener">Bing Webmaster Tools</a> ist der kürzeste Weg, die schon bestätigte Website aus der Search Console zu importieren: Sie bringt auch die Sitemaps mit. Sonst gibt es das Feld Bing, wie bei Google.</p>

<h2 id="plugin">10. Mit Yoast, Rank Math und Co.</h2>
<p>Sind Yoast SEO, Rank Math, All in One SEO oder SEOPress aktiv, hält sich Olobuild heraus: Es schreibt weder Titel noch Beschreibung, Open Graph, Canonical und strukturierte Daten, damit die Seite sie nicht doppelt bekommt. In diesem Fall füllst du die Felder im Panel des SEO-Plugins aus. Die von Olobuild bleiben gespeichert, werden aber nicht benutzt.</p>
<p>Die Versionen der Seite in anderen Sprachen und ihre Verknüpfungen für die Suchmaschinen sind Sache von OLOlang.</p>
