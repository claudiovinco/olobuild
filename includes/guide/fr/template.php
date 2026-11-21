<?php
/**
 * Guida «Template» (francese). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#cosa">Modèles et pages</a></li>
		<li><a href="#tipi">Les types de modèles</a></li>
		<li><a href="#elenco">Blocs &amp; Pages</a></li>
		<li><a href="#header-footer">Quel en-tête voit une page</a></li>
		<li><a href="#regole">Règles d’attribution</a></li>
		<li><a href="#revisioni">Révisions</a></li>
		<li><a href="#pronti">Blocs prêts, thèmes et éléments globaux</a></li>
		<li><a href="#import">Importer et exporter</a></li>
	</ol>
</nav>

<h2 id="cosa">1. Modèles et pages</h2>
<p>Un modèle, c’est le contenu construit avec Olobuild : sections, lignes, colonnes et tiles. Une page WordPress montre le modèle qui lui est relié, et le lien se crée tout seul quand tu crées la page depuis Olobuild.</p>
<p>Un modèle peut aussi s’insérer partout où WordPress accepte un shortcode. Dans Blocs &amp; Pages, un clic sur le shortcode le copie, sous la forme <code>[olo_template id="12"]</code>.</p>
<div class="nota"><p>Si la page WordPress finit à la corbeille, au prochain enregistrement du modèle Olobuild en crée une nouvelle, reliée au même modèle.</p></div>

<h2 id="tipi">2. Les types de modèles</h2>
<table>
	<thead><tr><th>Type</th><th>À quoi il sert</th></tr></thead>
	<tbody>
		<tr><td><strong>Page</strong></td><td>Le contenu d’une page du site.</td></tr>
		<tr><td><strong>Header</strong></td><td>L’en-tête avec le logo et le menu, en haut des pages.</td></tr>
		<tr><td><strong>Footer</strong></td><td>Le pied de page, en bas des pages.</td></tr>
		<tr><td><strong>Single</strong></td><td>La mise en page commune à tous les contenus d’un type, par exemple chaque article ou chaque produit. On le crée depuis Modèle Single, en choisissant le type de contenu.</td></tr>
		<tr><td><strong>Mega Panel</strong></td><td>Le contenu d’un panneau du méga-menu.</td></tr>
		<tr><td><strong>Widget</strong></td><td>Un contenu réutilisable dans les fiches, les onglets et les sliders.</td></tr>
		<tr><td><strong>404</strong></td><td>La page qui apparaît quand une adresse n’existe pas.</td></tr>
	</tbody>
</table>

<h2 id="elenco">3. Blocs &amp; Pages</h2>
<p><span class="ui">Construire</span> › <span class="ui">Blocs &amp; Pages</span> liste tous les modèles du site. Les filtres en haut montrent un type à la fois, et chaque fiche indique l’état : Brouillon, Publié ou Actif. Depuis les actions d’une fiche, tu peux modifier, dupliquer, exporter et supprimer le modèle.</p>
<div class="nota attenzione"><p><strong>Supprimer</strong> efface le modèle pour toujours. La page WordPress reliée reste, mais elle ne montre plus le contenu fait avec Olobuild.</p></div>

<h2 id="header-footer">4. Quel en-tête voit une page</h2>
<p>Chaque page montre un en-tête et un pied de page. Olobuild les choisit dans cet ordre, et s’arrête à la première réponse qu’il trouve.</p>
<figure class="olo-schema">
	<div class="flusso">
		<div><strong>1. Le choix de la page</strong>L’encadré Olobuild dans l’éditeur de WordPress, avec les menus Header et Footer.</div>
		<div class="freccia" aria-hidden="true"><?php echo Olobuild_Guida::icona( 'freccia', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico. ?></div>
		<div><strong>2. Les règles d’attribution</strong>Par exemple un en-tête différent pour les articles du blog.</div>
		<div class="freccia" aria-hidden="true"><?php echo Olobuild_Guida::icona( 'freccia', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico. ?></div>
		<div><strong>3. En-tête et pied de page actifs</strong>Ceux qui valent pour tout le site.</div>
	</div>
	<figcaption>Avec « Par défaut (global) » dans l’encadré de la page, ce sont les règles qui décident ou, à défaut, l’en-tête et le pied de page actifs.</figcaption>
</figure>
<p>Pour rendre actif un en-tête ou un pied de page, ouvre-le dans le builder, publie-le et appuie sur <span class="ui">Activer</span> dans la barre du haut. C’est pareil pour un modèle Single et pour la page 404.</p>
<p>Dans le canvas d’une page, l’en-tête et le pied de page disent de quel modèle ils viennent et si d’autres pages les utilisent aussi. <span class="ui">Ouvrir le modèle</span> les ouvre dans le builder, et un bouton te ramène ensuite à la page d’où tu es parti.</p>

<h2 id="regole">5. Règles d’attribution</h2>
<p>Les règles décident quel en-tête, pied de page, Single ou archive utiliser dans une partie du site. Elles se trouvent dans <span class="ui">Système</span> › <span class="ui">Configuration</span> › <span class="ui">Contenus &amp; Modèles</span> › <span class="ui">Attribution des modèles</span>.</p>
<ol class="passi">
	<li><strong>Crée la règle.</strong> Appuie sur <span class="ui">Nouvelle règle</span>, choisis le <span class="ui">Contexte</span> (Header, Footer, Single ou Archive) et le modèle à utiliser.</li>
	<li><strong>Ajoute les conditions.</strong> Avec <span class="ui">Ajouter une condition</span>, tu choisis où la règle s’applique, et pour chaque condition s’il faut inclure ou exclure. La valeur se choisit dans un menu.</li>
	<li><strong>Combine.</strong> <span class="ui">Opérateur des conditions</span> dit si toutes les conditions doivent être remplies ou au moins une.</li>
	<li><strong>Donne la priorité.</strong> Quand deux règles valent pour la même page, celle qui a le plus petit numéro de <span class="ui">Priorité</span> l’emporte. La valeur par défaut est 10.</li>
</ol>
<p>Les conditions disponibles sont : Tout le site, Page d’accueil seulement, Toutes les pages individuelles, Page, Article, Type de contenu, Archive, Catégorie, Étiquette, Utilisateur connecté, Utilisateur non connecté, Rôle de l’utilisateur, Page 404, Page de recherche, et 4 conditions pour WooCommerce.</p>
<div class="nota"><p>Sans règles, ce sont l’en-tête et le pied de page actifs qui valent. Une règle ne sert que là où tu veux quelque chose de différent.</p></div>

<h2 id="revisioni">6. Révisions</h2>
<p>À chaque enregistrement, Olobuild garde la version précédente du modèle, jusqu’à 50 versions. Tu les trouves dans le builder, dans <span class="ui">Historique des révisions</span>, avec la date et le poids.</p>
<ol class="passi">
	<li>Ouvre <span class="ui">Historique des révisions</span> depuis la barre du haut.</li>
	<li>Appuie sur <span class="ui">Restaurer</span> sur la version qu’il te faut : le canvas la charge.</li>
	<li>Vérifie la page et appuie sur <span class="ui">Enregistrer</span>. Tant que tu n’enregistres pas, le site reste comme il était.</li>
</ol>

<h2 id="pronti">7. Blocs prêts, thèmes et éléments globaux</h2>
<p>Le bouton <span class="ui">Blocs &amp; Pages</span> dans la barre du builder ouvre la bibliothèque : 89 blocs, 58 pages entières et les sections que tu as enregistrées toi-même avec <span class="ui">Enregistrer la section comme modèle</span> depuis le clic droit. Une page entière peut s’utiliser de deux façons : <span class="ui">Tout remplacer</span> ou <span class="ui">Ajouter à la fin</span>.</p>
<p><span class="ui">Thèmes du site</span> ouvre les mêmes thèmes que la première configuration. Un thème change tout le site, et avant de confirmer, la fenêtre liste tout ce qui va changer.</p>
<p>Un élément peut devenir <strong>global</strong> depuis le clic droit, avec <span class="ui">Enregistrer comme global</span>. Un élément global s’insère depuis la catégorie Globaux du panneau Éléments et il est le même dans toutes les pages qui l’utilisent. <span class="ui">Détacher le global</span> le transforme en copie indépendante.</p>

<h2 id="import">8. Importer et exporter</h2>
<p><span class="ui">Construire</span> › <span class="ui">Importer / Exporter</span> déplace le travail entre des sites différents avec des fichiers <code>.json</code>.</p>
<ul>
	<li><strong>Exporter le modèle</strong> enregistre un modèle, avec ses images aussi si tu choisis <span class="ui">Inclure les médias</span>.</li>
	<li><strong>Exporter le site complet</strong> enregistre modèles, pages, page d’accueil, menus, médias, styles et options globales.</li>
	<li><strong>Importer</strong> charge un fichier exporté d’un autre site Olobuild.</li>
</ul>
<div class="nota attenzione"><p>Importer un site complet <strong>remplace</strong> les couleurs, les polices et les options globales du site qui le reçoit. Exporte d’abord le site actuel, si tu veux pouvoir revenir en arrière.</p></div>
<p>Dans Blocs &amp; Pages, <span class="ui">Sélectionner</span> puis <span class="ui">Exporter le thème</span> mettent plusieurs modèles dans un seul fichier. Dans le builder, l’import d’un fichier JSON crée un nouveau modèle en brouillon et laisse intact celui qui est ouvert.</p>
