# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1218 (18 nov 2026): le idee dai siti d'ispirazione — grana, gradini di luce, seta, vichy e
mezzitoni negli sfondi, il piè di pagina che si scopre, il passaggio fra le pagine, la Vetrina con l'anteprima in
movimento e «Affina per stile», il titolo che riempie la larghezza, il timbro che gira, il vetro liquido, la Sfera di
luce, l'immagine di condivisione disegnata, il giro guidato e la sorpresa nascosta.
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1218.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1218'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    # la sorpresa nascosta
    'Hai trovato la sorpresa!': ('Du hast die Überraschung gefunden!', 'You found the surprise!', '¡Has encontrado la sorpresa!', 'Tu as trouvé la surprise !'),
    'Sorpresa nascosta': ('Versteckte Überraschung', 'Hidden surprise', 'Sorpresa oculta', 'Surprise cachée'),
    'Un regalo per i curiosi: dopo cinque clic su questo elemento, o col codice Konami sulla tastiera (su, su, giù, giù, sinistra, destra, sinistra, destra, B, A), succede qualcosa.':
        ('Ein Geschenk für Neugierige: nach fünf Klicks auf dieses Element oder mit dem Konami-Code auf der Tastatur (hoch, hoch, runter, runter, links, rechts, links, rechts, B, A) passiert etwas.',
         'A gift for the curious: after five clicks on this element, or with the Konami code on the keyboard (up, up, down, down, left, right, left, right, B, A), something happens.',
         'Un regalo para los curiosos: tras cinco clics en este elemento, o con el código Konami en el teclado (arriba, arriba, abajo, abajo, izquierda, derecha, izquierda, derecha, B, A), pasa algo.',
         'Un cadeau pour les curieux : après cinq clics sur cet élément, ou avec le code Konami au clavier (haut, haut, bas, bas, gauche, droite, gauche, droite, B, A), il se passe quelque chose.'),
    "Sul telefono, dove la tastiera non c'è, bastano i cinque tocchi. Se l'elemento è un link a un'altra pagina, il primo clic la apre: meglio un logo che porta alla pagina stessa, o un elemento senza link.":
        ('Auf dem Handy, wo es keine Tastatur gibt, reichen die fünf Tipper. Ist das Element ein Link zu einer anderen Seite, öffnet der erste Klick sie: besser ein Logo, das auf dieselbe Seite führt, oder ein Element ohne Link.',
         'On a phone, where there is no keyboard, five taps are enough. If the element links to another page, the first click opens it: better a logo that leads to the same page, or an element without a link.',
         'En el móvil, donde no hay teclado, bastan los cinco toques. Si el elemento es un enlace a otra página, el primer clic la abre: mejor un logo que lleve a la misma página, o un elemento sin enlace.',
         'Sur le téléphone, où il n’y a pas de clavier, cinq touchers suffisent. Si l’élément est un lien vers une autre page, le premier clic l’ouvre : mieux vaut un logo qui mène à la même page, ou un élément sans lien.'),
    'Per chi ha chiesto meno movimento, coriandoli e capovolgimento diventano il messaggio.':
        ('Wer weniger Bewegung eingestellt hat, sieht statt Konfetti und Umdrehen die Nachricht.', 'For people who asked for less motion, confetti and flipping become the message.',
         'Para quien pidió menos movimiento, el confeti y el giro se convierten en el mensaje.', 'Pour qui a demandé moins de mouvement, les confettis et le retournement deviennent le message.'),
    'Come si trova': ('Wie man sie findet', 'How to find it', 'Cómo se encuentra', 'Comment la trouver'),
    'Messaggio (facoltativo)': ('Nachricht (optional)', 'Message (optional)', 'Mensaje (opcional)', 'Message (facultatif)'),
    'La pagina si capovolge': ('Die Seite steht kopf', 'The page flips over', 'La página da la vuelta', 'La page se retourne'),
    'Un messaggio': ('Eine Nachricht', 'A message', 'Un mensaje', 'Un message'),
    'Cinque clic': ('Fünf Klicks', 'Five clicks', 'Cinco clics', 'Cinq clics'),
    'Codice Konami': ('Konami-Code', 'Konami code', 'Código Konami', 'Code Konami'),

    # l'immagine di condivisione disegnata
    'Immagine disegnata': ('Gestaltetes Bild', 'Designed image', 'Imagen diseñada', 'Image dessinée'),
    "Per ogni contenuto senza un'immagine sua: il titolo, il nome del sito e la foto in evidenza, nel colore e nel carattere del sito.":
        ('Für jeden Inhalt ohne eigenes Bild: der Titel, der Name der Website und das Beitragsbild, in der Farbe und Schrift der Website.',
         'For every piece of content without its own image: the title, the site name and the featured image, in the site’s colour and font.',
         'Para cada contenido sin imagen propia: el título, el nombre del sitio y la imagen destacada, con el color y la fuente del sitio.',
         'Pour chaque contenu sans image propre : le titre, le nom du site et l’image mise en avant, dans la couleur et la police du site.'),
    "Anteprima dell'immagine disegnata": ('Vorschau des gestalteten Bildes', 'Preview of the designed image', 'Vista previa de la imagen diseñada', 'Aperçu de l’image dessinée'),
    "Disegno l'anteprima…": ('Vorschau wird gezeichnet…', 'Drawing the preview…', 'Dibujando la vista previa…', 'Je dessine l’aperçu…'),
    'Prova su: %s': ('Getestet mit: %s', 'Tried on: %s', 'Prueba con: %s', 'Essai sur : %s'),
    'Foto con titolo': ('Foto mit Titel', 'Photo with title', 'Foto con título', 'Photo avec titre'),
    'Metà e metà': ('Halb und halb', 'Half and half', 'Mitad y mitad', 'Moitié-moitié'),
    'Solo colore': ('Nur Farbe', 'Colour only', 'Solo color', 'Couleur seule'),
    'Il server non sa disegnare le immagini (manca GD con FreeType).':
        ('Der Server kann keine Bilder zeichnen (GD mit FreeType fehlt).', 'The server cannot draw images (GD with FreeType is missing).',
         'El servidor no puede dibujar imágenes (falta GD con FreeType).', 'Le serveur ne sait pas dessiner les images (GD avec FreeType manque).'),
    'Nessun carattere disponibile per disegnare il titolo.':
        ('Keine Schrift verfügbar, um den Titel zu zeichnen.', 'No font available to draw the title.', 'No hay ninguna fuente disponible para dibujar el título.', 'Aucune police disponible pour dessiner le titre.'),

    # sfondi: gradini di luce, grana, pattern, seta
    'Colori, dal basso': ('Farben, von unten', 'Colours, from the bottom', 'Colores, desde abajo', 'Couleurs, depuis le bas'),
    'Discesa': ('Abfall', 'Falloff', 'Caída', 'Descente'),
    "Un velo di grana fine sopra lo sfondo, come la carta o la pellicola: dà materia alle tinte piatte, ai gradienti e alle foto. Si vede sul chiaro e sullo scuro; a zero non c'è.":
        ('Ein Schleier aus feiner Körnung über dem Hintergrund, wie Papier oder Film: er gibt flachen Farben, Verläufen und Fotos Materie. Er wirkt auf Hell und Dunkel; bei null ist er weg.',
         'A veil of fine grain over the background, like paper or film: it gives texture to flat colours, gradients and photos. It shows on light and dark; at zero it is gone.',
         'Un velo de grano fino sobre el fondo, como el papel o la película: da materia a los colores planos, a los degradados y a las fotos. Se ve en claro y en oscuro; a cero no está.',
         'Un voile de grain fin sur le fond, comme le papier ou la pellicule : il donne de la matière aux teintes plates, aux dégradés et aux photos. Il se voit sur le clair et sur le foncé ; à zéro il n’y en a pas.'),
    'Intensità della grana': ('Stärke der Körnung', 'Grain intensity', 'Intensidad del grano', 'Intensité du grain'),
    'Gradini di luce': ('Lichtstufen', 'Light steps', 'Escalones de luz', 'Marches de lumière'),
    'Geometrici': ('Geometrisch', 'Geometric', 'Geométricos', 'Géométriques'),
    'Seta': ('Seide', 'Silk', 'Seda', 'Soie'),
    'Vichy': ('Vichy-Karo', 'Gingham', 'Vichy', 'Vichy'),
    'Mezzitoni': ('Halbtöne', 'Halftone', 'Semitonos', 'Demi-teintes'),

    # il piè di pagina che si scopre, il passaggio fra le pagine
    'Si scopre da sotto': ('Wird von unten aufgedeckt', 'Revealed from below', 'Se descubre desde abajo', 'Se découvre par-dessous'),
    'Il piè di pagina resta fermo in fondo e la pagina, scorrendo via, lo scopre come un sipario che sale. Funziona quando il piè di pagina sta nella finestra; se è più alto, resta com’è.':
        ('Die Fußzeile bleibt unten stehen und die Seite deckt sie beim Wegscrollen auf wie ein Vorhang, der sich hebt. Das funktioniert, wenn die Fußzeile ins Fenster passt; ist sie höher, bleibt sie, wie sie ist.',
         'The footer stays still at the bottom and the page, scrolling away, reveals it like a rising curtain. It works when the footer fits in the window; if it is taller, it stays as it is.',
         'El pie de página se queda quieto abajo y la página, al deslizarse, lo descubre como un telón que sube. Funciona cuando el pie cabe en la ventana; si es más alto, se queda como está.',
         'Le pied de page reste immobile en bas et la page, en défilant, le découvre comme un rideau qui se lève. Cela marche quand le pied de page tient dans la fenêtre ; s’il est plus haut, il reste tel quel.'),
    'Passaggio fra le pagine': ('Übergang zwischen den Seiten', 'Page transition', 'Transición entre páginas', 'Passage entre les pages'),
    "Come si passa da una pagina all'altra del sito: la pagina nuova arriva in dissolvenza, sale come una tendina, entra con uno zoom o si apre in un cerchio dal punto in cui hai cliccato.":
        ('Wie man von einer Seite der Website zur nächsten kommt: die neue Seite wird eingeblendet, steigt wie ein Rollo auf, kommt mit einem Zoom oder öffnet sich in einem Kreis von der Stelle, an der du geklickt hast.',
         'How you move from one page of the site to another: the new page fades in, rises like a blind, zooms in or opens in a circle from the point you clicked.',
         'Cómo se pasa de una página del sitio a otra: la página nueva aparece fundida, sube como una persiana, entra con un zoom o se abre en un círculo desde el punto donde hiciste clic.',
         'Comment on passe d’une page du site à l’autre : la nouvelle page arrive en fondu, monte comme un store, entre avec un zoom ou s’ouvre en cercle depuis l’endroit où tu as cliqué.'),
    'Vale per tutto il sito e si salva da sola. Funziona su Chrome, Edge e Safari recenti; negli altri browser le pagine cambiano come sempre, e niente movimento per chi ha chiesto di ridurlo.':
        ('Gilt für die ganze Website und speichert sich selbst. Funktioniert in aktuellen Chrome-, Edge- und Safari-Versionen; in anderen Browsern wechseln die Seiten wie immer, und keine Bewegung für alle, die weniger Bewegung eingestellt haben.',
         'It applies to the whole site and saves itself. It works on recent Chrome, Edge and Safari; in other browsers pages change as usual, and there is no motion for people who asked to reduce it.',
         'Vale para todo el sitio y se guarda sola. Funciona en Chrome, Edge y Safari recientes; en los demás navegadores las páginas cambian como siempre, y sin movimiento para quien pidió reducirlo.',
         'Vaut pour tout le site et s’enregistre toute seule. Fonctionne sur Chrome, Edge et Safari récents ; dans les autres navigateurs les pages changent comme d’habitude, et pas de mouvement pour qui a demandé de le réduire.'),
    'Durata del passaggio': ('Dauer des Übergangs', 'Transition duration', 'Duración de la transición', 'Durée du passage'),
    'Come sempre': ('Wie immer', 'As usual', 'Como siempre', 'Comme d’habitude'),
    'Tendina che sale': ('Aufsteigendes Rollo', 'Rising blind', 'Persiana que sube', 'Store qui monte'),
    'Cerchio dal clic': ('Kreis vom Klick aus', 'Circle from the click', 'Círculo desde el clic', 'Cercle depuis le clic'),
    'Passaggio fra le pagine salvato per tutto il sito.': ('Übergang zwischen den Seiten für die ganze Website gespeichert.', 'Page transition saved for the whole site.', 'Transición entre páginas guardada para todo el sitio.', 'Passage entre les pages enregistré pour tout le site.'),
    'Il passaggio fra le pagine non si è salvato.': ('Der Übergang zwischen den Seiten wurde nicht gespeichert.', 'The page transition was not saved.', 'La transición entre páginas no se ha guardado.', 'Le passage entre les pages ne s’est pas enregistré.'),
    'Dati non validi.': ('Ungültige Daten.', 'Invalid data.', 'Datos no válidos.', 'Données non valides.'),

    # la Sfera di luce (Sfondo Goo / Aurora / Sfera)
    'Sfondo Goo / Aurora / Sfera': ('Hintergrund Goo / Aurora / Kugel', 'Goo / Aurora / Orb background', 'Fondo Goo / Aurora / Esfera', 'Fond Goo / Aurore / Sphère'),
    'Sfera di luce (respira e segue il mouse)': ('Lichtkugel (atmet und folgt der Maus)', 'Light orb (breathes and follows the mouse)', 'Esfera de luz (respira y sigue el ratón)', 'Sphère de lumière (respire et suit la souris)'),
    'Goo: i blob si fondono come gocce (filtro SVG) e uno insegue il cursore. Aurora: aloni morbidi sfocati che derivano, senza fusione metaball. Sfera di luce: una sola sfera luminosa che respira, coi colori della palette che girano dentro; la luce segue il puntatore e al clic dà un battito.':
        ('Goo: die Blobs verschmelzen wie Tropfen (SVG-Filter) und einer folgt dem Cursor. Aurora: weiche, unscharfe Lichthöfe, die treiben, ohne Metaball-Verschmelzung. Lichtkugel: eine einzige leuchtende Kugel, die atmet, mit den Farben der Palette, die darin kreisen; das Licht folgt dem Zeiger, beim Klick schlägt sie einmal.',
         'Goo: the blobs merge like drops (SVG filter) and one chases the cursor. Aurora: soft blurred halos drifting, without metaball merging. Light orb: a single glowing sphere that breathes, with the palette colours swirling inside; the light follows the pointer and a click makes it beat.',
         'Goo: los blobs se funden como gotas (filtro SVG) y uno persigue el cursor. Aurora: halos suaves y difuminados que derivan, sin fusión metaball. Esfera de luz: una sola esfera luminosa que respira, con los colores de la paleta girando dentro; la luz sigue al puntero y al hacer clic da un latido.',
         'Goo : les blobs fusionnent comme des gouttes (filtre SVG) et l’un poursuit le curseur. Aurore : des halos doux et flous qui dérivent, sans fusion metaball. Sphère de lumière : une seule sphère lumineuse qui respire, avec les couleurs de la palette qui tournent dedans ; la lumière suit le pointeur et au clic elle bat.'),
    'Sfera': ('Kugel', 'Orb', 'Esfera', 'Sphère'),
    'Quanto in fretta la sfera respira e i colori girano dentro: 0 = ferma, 1 = un respiro ogni sei secondi circa.':
        ('Wie schnell die Kugel atmet und die Farben darin kreisen: 0 = still, 1 = etwa ein Atemzug alle sechs Sekunden.', 'How fast the orb breathes and the colours swirl inside: 0 = still, 1 = about one breath every six seconds.',
         'Qué tan rápido respira la esfera y giran los colores dentro: 0 = quieta, 1 = una respiración cada seis segundos aproximadamente.', 'À quelle vitesse la sphère respire et les couleurs tournent dedans : 0 = immobile, 1 = une respiration toutes les six secondes environ.'),
    'Reagisce al mouse': ('Reagiert auf die Maus', 'Reacts to the mouse', 'Reacciona al ratón', 'Réagit à la souris'),
    "La sfera si sporge verso il puntatore, la luce lo segue e l'alone cresce quando ti avvicini; al clic, o al tocco sul telefono, dà un battito.":
        ('Die Kugel neigt sich zum Zeiger, das Licht folgt ihm und der Lichthof wächst, wenn du näher kommst; beim Klick oder beim Tippen auf dem Handy schlägt sie einmal.',
         'The orb leans towards the pointer, the light follows it and the halo grows as you get closer; on click, or tap on a phone, it beats.',
         'La esfera se inclina hacia el puntero, la luz lo sigue y el halo crece cuando te acercas; al hacer clic, o al tocar en el móvil, da un latido.',
         'La sphère se penche vers le pointeur, la lumière le suit et le halo grandit quand tu t’approches ; au clic, ou au toucher sur le téléphone, elle bat.'),
    'Dimensione sfera': ('Größe der Kugel', 'Orb size', 'Tamaño de la esfera', 'Taille de la sphère'),
    'Sul telefono la sfera non supera i nove decimi dello schermo.': ('Auf dem Handy ist die Kugel höchstens neun Zehntel des Bildschirms groß.', 'On a phone the orb never exceeds nine tenths of the screen.', 'En el móvil la esfera no supera las nueve décimas partes de la pantalla.', 'Sur le téléphone la sphère ne dépasse pas les neuf dixièmes de l’écran.'),

    # il titolo che riempie, l'Overlay Grid, il timbro che gira, la Vetrina
    'Riempie la larghezza': ('Füllt die Breite', 'Fills the width', 'Llena el ancho', 'Remplit la largeur'),
    'Mosaico (bento)': ('Mosaik (Bento)', 'Mosaic (bento)', 'Mosaico (bento)', 'Mosaïque (bento)'),
    'Come gira': ('Wie er sich dreht', 'How it spins', 'Cómo gira', 'Comment il tourne'),
    'Sempre, da solo': ('Immer, von selbst', 'Always, on its own', 'Siempre, solo', 'Toujours, tout seul'),
    'Mentre scorri la pagina': ('Während du die Seite scrollst', 'While you scroll the page', 'Mientras desplazas la página', 'Pendant que tu fais défiler la page'),
    'Sempre: un giro ogni tanti secondi, anche a pagina ferma. Mentre scorri: il timbro gira solo quando la pagina si muove, avanti scendendo e indietro salendo.':
        ('Immer: eine Drehung alle paar Sekunden, auch bei stehender Seite. Während du scrollst: der Stempel dreht sich nur, wenn die Seite sich bewegt, vorwärts beim Hinunter- und rückwärts beim Hinaufscrollen.',
         'Always: one turn every few seconds, even when the page is still. While you scroll: the stamp turns only when the page moves, forwards going down and backwards going up.',
         'Siempre: una vuelta cada pocos segundos, también con la página quieta. Mientras desplazas: el sello gira solo cuando la página se mueve, hacia delante al bajar y hacia atrás al subir.',
         'Toujours : un tour toutes les quelques secondes, même page immobile. Pendant le défilement : le tampon tourne seulement quand la page bouge, en avant en descendant et en arrière en remontant.'),
    'Icona al centro': ('Symbol in der Mitte', 'Icon in the centre', 'Icono en el centro', 'Icône au centre'),
    'Dentro il cerchio, ferma mentre il testo gira: una freccia, una stella, il simbolo del marchio. Vuota = niente.':
        ('Im Kreis, still, während der Text sich dreht: ein Pfeil, ein Stern, das Zeichen der Marke. Leer = nichts.', 'Inside the circle, still while the text turns: an arrow, a star, the brand symbol. Empty = nothing.',
         'Dentro del círculo, quieta mientras el texto gira: una flecha, una estrella, el símbolo de la marca. Vacía = nada.', 'Dans le cercle, immobile pendant que le texte tourne : une flèche, une étoile, le symbole de la marque. Vide = rien.'),
    'Affina per stile': ('Nach Stil verfeinern', 'Refine by style', 'Afinar por estilo', 'Affiner par style'),

    # il giro guidato
    'Giro guidato': ('Geführte Tour', 'Guided tour', 'Recorrido guiado', 'Visite guidée'),
    'di': ('von', 'of', 'de', 'sur'),
    'Salta il giro': ('Tour überspringen', 'Skip the tour', 'Saltar el recorrido', 'Passer la visite'),
    'Ho capito': ('Verstanden', 'Got it', 'Entendido', 'J’ai compris'),
    'Inserisci una tile': ('Eine Tile einfügen', 'Insert a tile', 'Inserta un tile', 'Insère une tile'),
    'Qui ci sono tutte le tile: trascinane una nella pagina, o fai clic sul + che compare passando sulla pagina. Se sai già cosa ti serve, premi Ctrl+K e scrivi il nome.':
        ('Hier sind alle Tiles: zieh eine in die Seite oder klick auf das +, das erscheint, wenn du über die Seite fährst. Wenn du schon weißt, was du brauchst, drück Strg+K und schreib den Namen.',
         'All the tiles are here: drag one into the page, or click the + that appears as you move over the page. If you already know what you need, press Ctrl+K and type the name.',
         'Aquí están todos los tiles: arrastra uno a la página, o haz clic en el + que aparece al pasar por la página. Si ya sabes lo que necesitas, pulsa Ctrl+K y escribe el nombre.',
         'Toutes les tiles sont ici : fais-en glisser une dans la page, ou clique sur le + qui apparaît en passant sur la page. Si tu sais déjà ce qu’il te faut, appuie sur Ctrl+K et écris le nom.'),
    'Cambia lo stile': ('Ändere den Stil', 'Change the style', 'Cambia el estilo', 'Change le style'),
    'Fai clic su una tile nella pagina: qui trovi cosa dice, nel Contenuto, e come appare, nello Stile: colori, caratteri, spazi, bordi e ombre.':
        ('Klick auf eine Tile in der Seite: hier findest du, was sie sagt, unter Inhalt, und wie sie aussieht, unter Stil: Farben, Schriften, Abstände, Rahmen und Schatten.',
         'Click a tile in the page: here you find what it says, under Content, and how it looks, under Style: colours, fonts, spacing, borders and shadows.',
         'Haz clic en un tile de la página: aquí encuentras lo que dice, en Contenido, y cómo se ve, en Estilo: colores, fuentes, espacios, bordes y sombras.',
         'Clique sur une tile dans la page : ici tu trouves ce qu’elle dit, dans Contenu, et à quoi elle ressemble, dans Style : couleurs, polices, espaces, bordures et ombres.'),
    'Collega un dato': ('Verknüpfe einen Wert', 'Connect a piece of data', 'Conecta un dato', 'Relie une donnée'),
    'Il fulmine accanto a un campo lo collega a un dato: il titolo della pagina, la foto in evidenza, il prezzo di un prodotto, la data di uno spettacolo. Cambi il dato e la pagina si aggiorna da sola.':
        ('Der Blitz neben einem Feld verknüpft es mit einem Wert: dem Titel der Seite, dem Beitragsbild, dem Preis eines Produkts, dem Datum einer Vorstellung. Änderst du den Wert, aktualisiert sich die Seite von selbst.',
         'The lightning bolt next to a field connects it to a piece of data: the page title, the featured image, a product price, the date of a show. Change the data and the page updates by itself.',
         'El rayo junto a un campo lo conecta a un dato: el título de la página, la imagen destacada, el precio de un producto, la fecha de un espectáculo. Cambias el dato y la página se actualiza sola.',
         'L’éclair à côté d’un champ le relie à une donnée : le titre de la page, l’image mise en avant, le prix d’un produit, la date d’un spectacle. Tu changes la donnée et la page se met à jour toute seule.'),
    'Guarda sul telefono': ('Schau auf dem Handy', 'Look on the phone', 'Mira en el móvil', 'Regarde sur le téléphone'),
    "Da qui passi dal computer al tablet e al telefono e vedi la pagina com'è lì. I campi con l'icona del dispositivo accanto si regolano per ognuno: sul telefono rimpicciolisci un titolo senza toccare il computer.":
        ('Von hier wechselst du vom Computer zum Tablet und zum Handy und siehst die Seite so, wie sie dort ist. Felder mit dem Gerätesymbol daneben stellst du für jedes Gerät einzeln ein: auf dem Handy verkleinerst du einen Titel, ohne den Computer anzurühren.',
         'From here you switch from computer to tablet and phone and see the page as it is there. Fields with the device icon beside them are set for each one: on the phone you shrink a title without touching the computer.',
         'Desde aquí pasas del ordenador a la tableta y al móvil y ves la página como es allí. Los campos con el icono del dispositivo al lado se ajustan para cada uno: en el móvil reduces un título sin tocar el ordenador.',
         'D’ici tu passes de l’ordinateur à la tablette et au téléphone et tu vois la page telle qu’elle est là-bas. Les champs avec l’icône de l’appareil à côté se règlent pour chacun : sur le téléphone tu réduis un titre sans toucher à l’ordinateur.'),
    'Salva e pubblica': ('Speichern und veröffentlichen', 'Save and publish', 'Guarda y publica', 'Enregistre et publie'),
    'Salva tiene il lavoro in bozza, Pubblica lo mette sul sito. Ctrl+S salva senza staccare le mani dalla tastiera, e dalle Versioni torni a una copia di prima.':
        ('Speichern behält die Arbeit als Entwurf, Veröffentlichen stellt sie auf die Website. Strg+S speichert, ohne die Hände von der Tastatur zu nehmen, und über die Versionen kehrst du zu einer früheren Kopie zurück.',
         'Save keeps your work as a draft, Publish puts it on the site. Ctrl+S saves without taking your hands off the keyboard, and from Versions you go back to an earlier copy.',
         'Guardar mantiene el trabajo como borrador, Publicar lo pone en el sitio. Ctrl+S guarda sin quitar las manos del teclado, y desde Versiones vuelves a una copia anterior.',
         'Enregistrer garde le travail en brouillon, Publier le met sur le site. Ctrl+S enregistre sans lâcher le clavier, et depuis les Versions tu reviens à une copie précédente.'),

    # il vetro liquido
    'Spento': ('Aus', 'Off', 'Apagado', 'Éteint'),
    'Leggero': ('Leicht', 'Light', 'Ligero', 'Léger'),
}
FILE = {'de_DE': 0, 'en_GB': 1, 'en_US': 1, 'es_ES': 2, 'fr_FR': 3}


def esc(s):
    return s.replace('\\', '\\\\').replace('"', '\\"')


def main():
    for loc, i in FILE.items():
        po = os.path.join(LANG, 'olobuild-%s.po' % loc)
        testo = open(po, encoding='utf-8').read()
        nuove = []
        for it, tr in V.items():
            if '\nmsgid "%s"\n' % esc(it) in testo:
                continue
            nuove.append('\n#: %s\nmsgid "%s"\nmsgstr "%s"\n' % (VERSIONE, esc(it), esc(tr[i])))
        if nuove:
            with open(po, 'a', encoding='utf-8', newline='\n') as f:
                f.write(''.join(nuove))
        subprocess.run([sys.executable, MSGFMT, '-o', po[:-3] + '.mo', po], check=True)
        print(loc, 'voci nuove:', len(nuove))


if __name__ == '__main__':
    main()
