<?php
/**
 * Guida «Primi passi» (francese). Solo HTML: titolo, descrizione e minuti
 * li stampa Olobuild_Guida::render_guida() dall'elenco delle guide.
 *
 * @package Olobuild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<nav class="olo-guida-toc" aria-label="Dans ce guide">
	<strong>Dans ce guide</strong>
	<ol>
		<li><a href="#configurazione">La première configuration</a></li>
		<li><a href="#bacheca">Le tableau de bord</a></li>
		<li><a href="#crea">Créer une page</a></li>
		<li><a href="#builder">Le builder en 4 zones</a></li>
		<li><a href="#elementi">Ajouter et arranger les éléments</a></li>
		<li><a href="#pubblica">Enregistrer et publier</a></li>
		<li><a href="#dispositivi">Ordinateur, tablette et téléphone</a></li>
		<li><a href="#scorciatoie">Raccourcis clavier</a></li>
	</ol>
</nav>

<h2 id="configurazione">1. La première configuration</h2>
<p>À la première activation, Olobuild ouvre la configuration guidée, en 3 étapes. Si tu l’as remise à plus tard, le panneau de bienvenue du Tableau de bord de WordPress la rouvre avec <span class="ui">Lancer la configuration</span>.</p>
<ol class="passi">
	<li><strong>Thème WordPress.</strong> <span class="ui">Installer et activer</span> met Hello Olobuild, le thème léger inclus dans l’extension. Avec <span class="ui">Utiliser le thème actuel</span>, tu gardes le thème que tu as déjà.</li>
	<li><strong>Choisis un design.</strong> Choisis un des 53 thèmes prêts, ou <span class="ui">Vide</span>, et appuie sur <span class="ui">Importer le thème choisi</span>. Le thème crée les pages et les menus, active l’en-tête et le pied de page, applique ses couleurs et ses polices et définit la page d’accueil. Avant l’import, Olobuild enregistre un instantané du site, que tu peux remettre avec <span class="ui">Restaurer</span>.</li>
	<li><strong>Tout est prêt.</strong> <span class="ui">Ouvrir Olobuild</span> mène au tableau de bord, <span class="ui">Voir le site</span> ouvre le site.</li>
</ol>
<div class="nota"><p>Avec <strong>Vide</strong>, tu obtiens le minimum pour démarrer : une page Accueil définie comme page d’accueil, le menu « Menu principal », un « En-tête de base » et un « Pied de page de base » déjà actifs.</p></div>

<h2 id="bacheca">2. Le tableau de bord</h2>
<p>Le tableau de bord s’ouvre depuis l’entrée <span class="ui">Olobuild</span> du menu de WordPress. La barre du haut est la même sur toutes les pages d’Olobuild et divise le travail en 4 zones.</p>
<table>
	<thead><tr><th>Zone</th><th>Ce qu’elle contient</th></tr></thead>
	<tbody>
		<tr><td><strong>Construire</strong></td><td>Blocs &amp; Pages (les modèles du site), Popups, Importer / Exporter</td></tr>
		<tr><td><strong>Médias</strong></td><td>Recherche de photos et de vidéos à mettre dans les pages</td></tr>
		<tr><td><strong>Collecte</strong></td><td>Envois des formulaires, Newsletter, Tracking &amp; Analytics</td></tr>
		<tr><td><strong>Système</strong></td><td>Configuration, Outils et ce Guide</td></tr>
	</tbody>
</table>
<p>Dans le tableau de bord, tu trouves <span class="ui">Reprendre où vous en étiez</span>, les <span class="ui">Actions rapides</span>, les fiches de <span class="ui">Gestion</span> et, à droite, le Centre de ressources avec les nouveautés et les guides. Le champ de recherche en haut, ou <kbd>Ctrl</kbd> <kbd>K</kbd>, cherche pages, modèles et réglages depuis n’importe quelle page d’Olobuild.</p>

<h2 id="crea">3. Créer une page</h2>
<p><span class="ui">Créer une page</span>, parmi les Actions rapides, crée d’un seul coup un modèle « Sans titre » et la page WordPress reliée, tous deux en brouillon, et ouvre le builder. <span class="ui">Nouvelle page</span>, en haut du tableau de bord, fait la même chose.</p>
<p>Tu peux aussi partir de WordPress :</p>
<ul>
	<li>dans la liste des Pages, l’action <span class="ui">Créer avec Olobuild</span> sous le titre d’une page</li>
	<li>dans la barre d’administration, <span class="ui">Modifier avec Olobuild</span> pendant que tu regardes une page du site</li>
	<li>dans l’éditeur de WordPress, l’encadré <span class="ui">Olobuild</span>, où tu choisis aussi l’en-tête et le pied de page de la page</li>
</ul>
<div class="nota"><p>Donne son titre à la page avant de la publier. Le titre se change d’un clic sur le nom de la page, dans la barre du builder. WordPress tire l’adresse de la page du titre au moment de la publication.</p></div>

<h2 id="builder">4. Le builder en 4 zones</h2>
<figure class="olo-schema">
	<div class="finestra" aria-hidden="true">
		<div class="z-barra"><span class="num">1</span>Barre du haut</div>
		<div class="z-palette"><span class="num">2</span>Éléments<br>et Structure</div>
		<div class="z-canvas"><span class="num">3</span>Canvas</div>
		<div class="z-inspector"><span class="num">4</span>Contenu · Style<br>· Avancé</div>
	</div>
	<ol class="legenda">
		<li><span class="num">1</span><span><strong>Barre du haut.</strong> Titre, Réglages de la page, appareils et zoom, Blocs &amp; Pages, annuler et rétablir, Aperçu, Enregistrer et Publier.</span></li>
		<li><span class="num">2</span><span><strong>Panneau de gauche.</strong> <span class="ui">Éléments</span> rassemble les tiles par catégorie, avec Récents, Favoris et Globaux. <span class="ui">Structure</span> montre l’arbre de la page.</span></li>
		<li><span class="num">3</span><span><strong>Canvas.</strong> La page telle que la verra le visiteur, dessinée par le même moteur que le site, avec l’en-tête et le pied de page.</span></li>
		<li><span class="num">4</span><span><strong>Panneau de droite.</strong> Il apparaît quand tu sélectionnes un élément : <span class="ui">Contenu</span> pour les textes, les images et les liens, <span class="ui">Style</span> pour l’apparence, <span class="ui">Avancé</span> pour la visibilité, les effets et le positionnement.</span></li>
	</ol>
	<figcaption>Les 4 zones du builder. Le panneau de droite s’ouvre aussi avec Réglages de la page.</figcaption>
</figure>
<p>Chaque page est faite de sections. Une section contient des lignes, une ligne contient des colonnes, et dans les colonnes il y a les éléments, qui dans Olobuild s’appellent des tiles.</p>
<figure class="olo-schema">
	<div class="albero" aria-hidden="true">
		<div class="l1">Section <small>bande sur toute la largeur, avec son fond</small></div>
		<div class="l2">Ligne <small>divise la section en colonnes</small></div>
		<div class="l3">Colonne <small>une largeur différente pour chaque appareil</small></div>
		<div class="l4">Tile <small>titre, texte, image, bouton, galerie…</small></div>
	</div>
	<figcaption>La hiérarchie d’une page, telle que la montre le panneau Structure.</figcaption>
</figure>
<p>Dans le Style d’une tile, les réglages sont divisés en deux blocs. <strong>Élément</strong> agit sur ce que la tile dessine, par exemple le bouton. <strong>Conteneur</strong> agit sur le cadre que la tile occupe dans la grille, avec ses espaces et son fond.</p>

<h2 id="elementi">5. Ajouter et arranger les éléments</h2>
<ol class="passi">
	<li><strong>Ajoute.</strong> Fais glisser une tile du panneau Éléments dans le canvas, ou clique sur sa fiche : la tile s’insère après l’élément sélectionné, ou en bas de la page. Dans une page vide, utilise <span class="ui">Ajouter une tile</span> ou <span class="ui">Choisir la mise en page</span>.</li>
	<li><strong>Cherche.</strong> <kbd>Ctrl</kbd> <kbd>K</kbd> ouvre le Finder, qui trouve une tile par son nom sans faire défiler les catégories.</li>
	<li><strong>Insère une section.</strong> Le bouton <span class="ui">+</span> entre deux sections ouvre <span class="ui">Insérer une tile ou une ligne</span>, avec une nouvelle tile, une ligne vide ou un bloc prêt de la bibliothèque.</li>
	<li><strong>Déplace et duplique.</strong> En passant sur un élément apparaissent les commandes pour le faire glisser, le monter ou le descendre, le dupliquer et le supprimer. Le clic droit ouvre le menu complet, avec Copier, Coller, Copier le style et Coller le style.</li>
	<li><strong>Écris.</strong> Double-clique sur un texte pour le modifier directement dans le canvas. <kbd>Échap</kbd> annule la modification en cours.</li>
</ol>
<div class="nota"><p>À côté du titre d’un champ ou d’une section peut apparaître un <strong>(i)</strong>. Ouvre-le pour lire à quoi sert le réglage : les explications sont là, comme ça le panneau reste en ordre.</p></div>

<h2 id="pubblica">6. Enregistrer et publier</h2>
<p><span class="ui">Enregistrer</span> s’active quand il y a quelque chose à enregistrer, et enregistre ensemble la page, l’en-tête et le pied de page si tu les as modifiés dans le canvas. Olobuild n’enregistre pas tout seul : si tu fermes l’onglet avec des modifications non enregistrées, le navigateur demande une confirmation.</p>
<p><span class="ui">Publier</span> met en ligne le modèle et la page WordPress reliée, avec son titre.</p>
<div class="nota attenzione"><p>Après la publication, chaque <strong>Enregistrer</strong> va tout de suite en ligne : le site montre toujours la dernière version enregistrée. Pour essayer une modification sans toucher au site, duplique la page dans Blocs &amp; Pages et travaille sur la copie.</p></div>
<p>À chaque enregistrement, Olobuild garde la version précédente. Tu les trouves dans <span class="ui">Historique des révisions</span>, dans la barre du haut. Le guide <a href="<?php echo esc_url( Olobuild_Guida::url( 'template' ) . '#revisioni' ); ?>">Modèles</a> explique comment en remettre une.</p>
<p><span class="ui">Aperçu</span> cache les panneaux et montre la page comme la voit le visiteur. <span class="ui">Réel</span> ouvre la vraie page dans un nouvel onglet. <kbd>Échap</kbd> ramène à la modification.</p>

<h2 id="dispositivi">7. Ordinateur, tablette et téléphone</h2>
<p>Les boutons des appareils dans la barre du haut redimensionnent le canvas : Écran large, Bureau, Tablette et Mobile. Tablette horizontale et Mobile horizontal s’allument dans <span class="ui">Système</span> › <span class="ui">Configuration</span> › <span class="ui">Appareils de l’éditeur</span>. Beaucoup de réglages ont une valeur pour chaque appareil. Quand tu en modifies un, tu changes la valeur de l’appareil choisi dans la barre.</p>
<p>Le zoom, à côté des appareils, rapetisse ou agrandit le canvas sans changer la page.</p>

<h2 id="scorciatoie">8. Raccourcis clavier</h2>
<p>Sur Mac, utilise <kbd>Cmd</kbd> à la place de <kbd>Ctrl</kbd>. Dans le builder, la liste est toujours à portée de main : c’est l’icône en forme de clavier dans la barre du haut.</p>
<table>
	<thead><tr><th>Touches</th><th>Action</th></tr></thead>
	<tbody>
		<tr><td><kbd>Ctrl</kbd> <kbd>S</kbd></td><td>Enregistrer</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Z</kbd> · <kbd>Ctrl</kbd> <kbd>Maj</kbd> <kbd>Z</kbd></td><td>Annuler · Rétablir, jusqu’à 100 étapes</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>C</kbd> · <kbd>Ctrl</kbd> <kbd>V</kbd> · <kbd>Ctrl</kbd> <kbd>D</kbd></td><td>Copier · Coller · Dupliquer l’élément sélectionné</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Alt</kbd> <kbd>C</kbd> · <kbd>Ctrl</kbd> <kbd>Alt</kbd> <kbd>V</kbd></td><td>Copier le style · Coller le style</td></tr>
		<tr><td><kbd>Suppr</kbd> ou <kbd>Retour arrière</kbd></td><td>Supprimer l’élément sélectionné</td></tr>
		<tr><td><kbd>Ctrl</kbd> + clic</td><td>Sélectionner plusieurs éléments</td></tr>
		<tr><td><kbd>Alt</kbd> <kbd>↑</kbd> · <kbd>Alt</kbd> <kbd>↓</kbd></td><td>Monter · Descendre</td></tr>
		<tr><td><kbd>Alt</kbd> <kbd>←</kbd> · <kbd>Alt</kbd> <kbd>→</kbd></td><td>Déplacer dans la colonne d’à côté</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>K</kbd></td><td>Chercher une tile (Finder)</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Alt</kbd> <kbd>P</kbd></td><td>Aperçu. <kbd>Échap</kbd> pour sortir</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Maj</kbd> <kbd>A</kbd></td><td>Assistant IA, s’il est configuré</td></tr>
	</tbody>
</table>
