<?php
/**
 * Guida «Prestazioni» (francese). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#da-solo">Ce qu’Olobuild fait tout seul</a></li>
		<li><a href="#impostazioni">Les réglages Performance &amp; Cache</a></li>
		<li><a href="#cache">Le cache de pages complètes</a></li>
		<li><a href="#esterne">Caches externes et nettoyage</a></li>
		<li><a href="#pagina">Une page rapide, en pratique</a></li>
		<li><a href="#misura">Mesurer</a></li>
	</ol>
</nav>

<h2 id="da-solo">1. Ce qu’Olobuild fait tout seul</h2>
<p>Certaines optimisations sont toujours actives et n’ont pas d’interrupteur.</p>
<ul>
	<li><strong>Seulement le nécessaire.</strong> Les feuilles de style et les scripts d’Olobuild ne se chargent que dans les pages faites avec Olobuild, et les scripts d’une tile, par exemple ceux de la carte, seulement si la tile est dans la page.</li>
	<li><strong>Les polices depuis ton serveur.</strong> Les Google Fonts utilisées par le site sont téléchargées une fois et servies par le site, sans appel à Google à chaque visite. Si le téléchargement échoue, la page utilise les polices du système.</li>
	<li><strong>Contenu à la demande.</strong> Après la première section, le contenu est monté quand il va entrer dans l’écran. Les tiles de la première section, en tout cas les 3 premières, les formulaires, les cartes et les menus sont toujours prêts. Les moteurs de recherche et ceux des IA reçoivent la page entière : beaucoup n’exécutent pas le JavaScript et ne verraient pas le contenu à la demande.</li>
	<li><strong>Images paresseuses.</strong> Les images se chargent quand elles vont devenir visibles.</li>
	<li><strong>Vidéos légères.</strong> La tile Vidéo, avec <span class="ui">Lazy Load (Facade)</span> actif comme par défaut, montre la couverture et ne charge le lecteur YouTube ou Vimeo qu’au clic.</li>
	<li><strong>Animations sans poids.</strong> Les animations d’entrée démarrent quand l’élément entre dans l’écran. Ceux qui ont demandé à leur système de réduire les animations voient les éléments tout de suite, sans animation.</li>
</ul>

<h2 id="impostazioni">2. Les réglages Performance &amp; Cache</h2>
<p>Ils se trouvent dans <span class="ui">Système</span> › <span class="ui">Configuration</span> › <span class="ui">Performances &amp; Services</span> › <span class="ui">Performance &amp; Cache</span>, et depuis la fiche Performance du tableau de bord.</p>
<table>
	<thead><tr><th>Réglage</th><th>Ce qu’il fait</th><th>Par défaut</th></tr></thead>
	<tbody>
		<tr><td><strong>Defer JavaScript</strong></td><td>Les scripts d’Olobuild se téléchargent sans bloquer la page et démarrent quand elle est prête.</td><td>Actif</td></tr>
		<tr><td><strong>Cache CSS en fichiers statiques</strong></td><td>Met le style de chaque modèle dans un fichier que le navigateur garde, au lieu de le réécrire dans la page.</td><td>Actif</td></tr>
		<tr><td><strong>Minifier le CSS</strong></td><td>Enlève les espaces et les commentaires des feuilles de style d’Olobuild.</td><td>Actif</td></tr>
		<tr><td><strong>Preload des polices</strong></td><td>Lance tout de suite le téléchargement des polices du texte et des titres, au poids utilisé : le texte apparaît avec sa police, sans changer d’aspect un instant après.</td><td>Actif</td></tr>
		<tr><td><strong>fetchpriority hero image</strong></td><td>La première image de la première section se charge tout de suite, avec une priorité haute. Les autres restent paresseuses.</td><td>Actif</td></tr>
		<tr><td><strong>Lazy load des vidéos hébergées</strong></td><td>Les vidéos téléversées sur le site, en lecture automatique et sans son, démarrent quand elles entrent dans l’écran.</td><td>Actif</td></tr>
		<tr><td><strong>Cache de pages complètes</strong></td><td>Garde les pages déjà prêtes et les sert aux visiteurs sans les reconstruire. Lis le chapitre 3 avant de l’activer.</td><td>Éteint</td></tr>
		<tr><td><strong>UIkit subset</strong></td><td>Réduit la feuille de style UIkit aux parties que les pages utilisent vraiment. Il apprend en visitant les pages et se corrige tout seul.</td><td>Éteint</td></tr>
		<tr><td><strong>CSS par tile</strong></td><td>Charge le style de certaines familles de tiles seulement là où il sert, comme les cartes, les galeries et les menus.</td><td>Éteint</td></tr>
		<tr><td><strong>Cache navigateur pour les médias (.htaccess)</strong></td><td>Dit au navigateur de garder longtemps les images, les polices, le CSS et le JS. Ça marche sur les serveurs Apache et LiteSpeed.</td><td>Éteint</td></tr>
		<tr><td><strong>DNS prefetch &amp; preconnect automatiques</strong></td><td>Prépare à l’avance la connexion à YouTube et Vimeo dans les pages qui les contiennent. Les domaines écrits dans Domaines personnalisés sortent toujours.</td><td>Actif</td></tr>
		<tr><td><strong>Nettoyage du head</strong></td><td>Enlève des scripts et des styles de WordPress que beaucoup de sites n’utilisent pas : jQuery Migrate, emoji, styles des blocs et du thème classique.</td><td>Éteint</td></tr>
		<tr><td><strong>Critical CSS</strong></td><td>Écrit dans la page le style des premières sections (2, dans <span class="ui">Sections above-the-fold</span>) et charge le reste juste après. Il se refait après les jours de <span class="ui">Durée du cache</span> et quand tu enregistres le modèle. <span class="ui">Régénérer le critical CSS</span> le reconstruit tout de suite.</td><td>Éteint</td></tr>
	</tbody>
</table>
<div class="nota attenzione"><p><strong>Supprimer le CSS des blocs</strong> enlève aussi les styles globaux de WordPress. Avec un thème à blocs, après l’avoir activé, vérifie que le site a encore ses couleurs et ses polices.</p></div>
<p>En haut de la fiche, <span class="ui">État du cache</span> compte les pages dans le cache de pages complètes, les fichiers de style et les pages avec Critical CSS, et dit quand le cache a été vidé la dernière fois. <span class="ui">Tout vider</span> les efface tous ensemble, avec le subset UIkit et les données temporaires d’Olobuild.</p>

<h2 id="cache">3. Le cache de pages complètes</h2>
<p>Le cache de pages complètes garde chaque page déjà prête et la sert aux visiteurs sans la reconstruire. C’est le réglage qui compte le plus sur un site très visité.</p>
<ul>
	<li>Il ne vaut pas pour ceux qui sont connectés, pour le panier et la caisse de WooCommerce, pour les sitemaps et pour les adresses avec des paramètres autres que ceux de suivi.</li>
	<li>Il garde des versions séparées pour l’ordinateur et le téléphone, et par langue.</li>
	<li>Chaque page reste en cache 8 heures.</li>
	<li>Il se vide tout seul quand tu enregistres dans le builder ou dans WordPress, quand tu changes les menus, le thème, l’en-tête ou le pied de page actifs, les couleurs et les polices globales, les règles d’attribution ou les réglages Performance, et à chaque mise à jour d’Olobuild.</li>
</ul>
<div class="nota attenzione"><p>Ne l’active pas si le site utilise déjà une extension de cache comme LiteSpeed Cache ou WP Rocket. Dans ce cas, Olobuild ne l’installe pas, et à côté de l’interrupteur la fiche dit pourquoi.</p></div>

<h2 id="esterne">4. Caches externes et nettoyage</h2>
<p>Olobuild ne commande pas les caches externes. Si le site utilise LiteSpeed Cache, WP Rocket, le cache de l’hébergeur ou Cloudflare, vide-le toi-même après avoir changé les couleurs, les polices, l’en-tête ou le pied de page : sinon, les visiteurs peuvent voir encore la version précédente.</p>
<p><span class="ui">Système</span> › <span class="ui">Outils</span> › <span class="ui">Cache d’Olobuild</span> › <span class="ui">Effacer fichiers et données</span> efface les fichiers de style générés, le cache de pages complètes et les données temporaires d’Olobuild, qui se recréent à la visite suivante. Ça sert quand une page montre un style ancien. C’est la même chose que <span class="ui">Tout vider</span> dans la fiche Performance.</p>

<h2 id="pagina">5. Une page rapide, en pratique</h2>
<ol class="passi">
	<li><strong>Des images à la bonne taille.</strong> Téléverse des images au plus aussi larges que l’espace qu’elles occupent, 2000 pixels pour une photo sur toute la largeur, en WebP ou en JPEG compressé.</li>
	<li><strong>La première image tout de suite.</strong> Avec <span class="ui">fetchpriority hero image</span> actif, comme par défaut, la première image de la première section part toute seule avec une priorité haute. Si la photo principale est plus bas, dans sa tile Image ouvre <span class="ui">Avancé</span> › <span class="ui">SEO &amp; Accessibilité</span> et choisis Eager dans <span class="ui">Chargement de l’image</span> et High dans <span class="ui">Fetch Priority</span>.</li>
	<li><strong>Peu de polices.</strong> 2 familles suffisent, avec 3 ou 4 graisses en tout. Chaque graisse en plus, c’est un fichier de plus à télécharger.</li>
	<li><strong>Des vidéos avec couverture.</strong> Utilise la tile Vidéo avec Lazy Load (Facade) actif, au lieu de coller le code de YouTube dans une tile HTML.</li>
	<li><strong>Des effets là où ils servent.</strong> Les fonds animés, la parallaxe et les vidéos de fond pèsent plus qu’une photo. Utilise-les là où ils racontent quelque chose.</li>
</ol>

<h2 id="misura">6. Mesurer</h2>
<p>Mesure la page avec PageSpeed Insights de Google ou avec Lighthouse, dans les outils de développement de Chrome. Fais l’essai dans une fenêtre privée : connecté, tu vois la page sans le cache de pages complètes, plus lente que celle des visiteurs.</p>
<p>Répète la mesure 2 ou 3 fois et regarde surtout le chargement du plus grand élément (LCP) et la stabilité de la mise en page (CLS). La note change d’un essai à l’autre, et d’un téléphone à l’autre.</p>
