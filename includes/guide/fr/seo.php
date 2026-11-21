<?php
/**
 * Guida «SEO e Open Graph» (francese). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#dove">Où ça se règle</a></li>
		<li><a href="#titolo">Titre et description</a></li>
		<li><a href="#analisi">Analyse de la page</a></li>
		<li><a href="#social">Aperçu sur les réseaux sociaux</a></li>
		<li><a href="#robots">Indexation et canonical</a></li>
		<li><a href="#schema">Données structurées</a></li>
		<li><a href="#redirect">Redirections et 404</a></li>
		<li><a href="#sitemap">Sitemap</a></li>
		<li><a href="#verifica">Search Console et Bing</a></li>
		<li><a href="#plugin">Avec Yoast, Rank Math et compagnie</a></li>
	</ol>
</nav>

<h2 id="dove">1. Où ça se règle</h2>
<p>Olobuild écrit dans l’en-tête de chaque page du site le titre, la description, l’aperçu social et les données structurées. Tu les règles page par page, à deux endroits qui enregistrent les mêmes données.</p>
<ul>
	<li><strong>Dans le builder :</strong> <span class="ui">Réglages de la page</span> › <span class="ui">SEO</span>, avec les onglets Base, Social, Robots, Schema et FAQ. Le groupe s’enregistre tout seul, sans appuyer sur Enregistrer, et apparaît quand le modèle est relié à une page.</li>
	<li><strong>Dans l’éditeur de WordPress :</strong> l’encadré <span class="ui">Olobuild SEO</span>, présent dans les pages, les articles et les autres contenus publics, avec l’aperçu du résultat sur Google.</li>
</ul>
<p>Dans les listes des pages et des articles, la colonne SEO montre avec une pastille combien des champs principaux sont remplis.</p>
<p>Les valeurs qui valent pour tout le site se trouvent dans <span class="ui">Système</span> › <span class="ui">Configuration</span> › <span class="ui">SEO &amp; Confidentialité</span> › <span class="ui">SEO global</span> : le schéma et le séparateur du titre, la description par défaut, l’image pour les réseaux sociaux, le profil X et le type de celui qui publie le site. Une page avec ses champs remplis l’emporte toujours.</p>

<h2 id="titolo">2. Titre et description</h2>
<p><span class="ui">SEO Title</span> est le titre qui apparaît dans l’onglet du navigateur et dans les résultats de recherche. Le compteur indique 60 caractères, la longueur que Google montre en entier. Si tu le laisses vide, c’est le schéma de l’onglet SEO global qui vaut : par défaut le titre de la page, le séparateur et le nom du site.</p>
<p><span class="ui">Meta Description</span> est le texte sous le titre dans les résultats, avec un compteur de 160 caractères. S’il manque, Olobuild utilise les 30 premiers mots du résumé de la page, puis la description par défaut de l’onglet SEO global et, si elle manque aussi, le slogan du site.</p>
<p><span class="ui">Focus keyword</span> est le mot-clé principal de la page, c’est-à-dire les mots qu’utiliserait celui qui la cherche. L’analyse de la page vérifie où il apparaît.</p>

<h2 id="analisi">3. Analyse de la page</h2>
<p>Sous le Focus keyword, dans l’onglet Base, <span class="ui">Analyse de la page</span> lit la page comme la voient Google et les assistants IA : le texte des tiles, les titres, les images et les liens. L’en-tête et le pied de page restent dehors. Les modifications comptent tout de suite, même avant d’enregistrer ; le bouton avec la flèche refait l’analyse.</p>
<ul>
	<li><strong>À corriger</strong> : ce qui retire la page des résultats ou la laisse sans titre principal, comme le noindex allumé, tout le site caché aux moteurs, aucun titre H1.</li>
	<li><strong>À améliorer</strong> : titre ou description trop longs ou trop courts, plus d’un H1, des titres qui sautent un niveau (de H2 à H4), trop peu de texte, des images sans texte alternatif, aucun lien vers les autres pages du site, le mot-clé absent des endroits qui comptent ou déjà utilisé par une autre page.</li>
	<li><strong>Réussis</strong> et <strong>Titres de la page</strong> s’ouvrent d’un clic : le second est le sommaire des titres H1–H6, avec ceux à arranger signalés.</li>
</ul>
<p>Le (i) à côté de chaque entrée explique comment y remédier. L’analyse ne compte pas combien de fois tu répètes le mot-clé : Google ne regarde plus ça, ce qui compte, c’est que la page parle vraiment de ça. Dans l’encadré <span class="ui">Olobuild SEO</span> de l’éditeur de WordPress, il y a la même analyse, sur la page enregistrée.</p>

<h2 id="social">4. Aperçu sur les réseaux sociaux</h2>
<p>Quand quelqu’un partage la page sur Facebook, LinkedIn, WhatsApp ou X, l’aperçu naît des données Open Graph. Tu les trouves dans l’onglet Social.</p>
<ul>
	<li><span class="ui">OG Title</span> et <span class="ui">OG Description</span> changent le titre et le texte seulement pour les réseaux sociaux. Vides, ce sont ceux de l’onglet Base qui valent.</li>
	<li><span class="ui">OG Image</span> est l’image de l’aperçu. La bonne taille est 1200 × 630 pixels.</li>
	<li>Les champs pour <span class="ui">Twitter / X</span> ne servent que si tu veux un texte différent : vides, X utilise ceux d’Open Graph.</li>
</ul>
<p>Sans OG Image, Olobuild utilise l’image mise en avant de la page, puis l’image par défaut de l’onglet SEO global et enfin le logo du site. Le format de l’aperçu de X se choisit dans le même onglet. Sans image, l’aperçu est de toute façon compact.</p>
<div class="nota"><p>Les réseaux sociaux gardent l’aperçu pendant des jours. Après l’avoir changé, mets-le à jour avec l’outil de débogage de la plateforme, par exemple le Sharing Debugger de Facebook.</p></div>

<h2 id="robots">5. Indexation et canonical</h2>
<p><span class="ui">Robots default</span> de l’onglet SEO global vaut pour tout le site et c’est le même choix que Réglages › Lecture de WordPress. Pour une seule page, dans l’onglet Robots, <span class="ui">noindex</span> demande aux moteurs de recherche de ne pas montrer la page dans les résultats. Les liens de la page restent suivis, à moins d’activer aussi <span class="ui">nofollow</span>. Utilise noindex pour les pages de service, comme une page de remerciement après un formulaire.</p>
<p><span class="ui">Canonical URL</span> indique l’adresse principale d’une page qui existe en plusieurs versions. Vide, c’est l’adresse de la page elle-même qui vaut, et c’est le bon choix presque toujours.</p>

<h2 id="schema">6. Données structurées</h2>
<p>Les données structurées décrivent la page aux moteurs de recherche dans un format qu’ils lisent sans interpréter le texte. Olobuild les écrit tout seul :</p>
<ul>
	<li>sur toutes les pages, celui qui publie le site, avec le logo et les profils sociaux : le type (organisation, personne, hôtel…) se choisit dans l’onglet SEO global</li>
	<li>sur la page d’accueil, aussi le site lui-même, avec sa recherche interne</li>
	<li>sur les autres pages, le chemin de navigation (fil d’Ariane)</li>
	<li>sur les articles le type Article, sur les pages le type Page, sur les produits WooCommerce le type Produit</li>
</ul>
<p>Toutes ces données sortent comme un seul graphe, reliées entre elles : la page fait partie du site, l’article est le contenu de la page, le site et l’article sont publiés par celui qui publie le site. C’est comme ça que les moteurs et les IA reconnaissent qui tu es.</p>
<p>Dans l’onglet FAQ, <span class="ui">Ajouter une FAQ</span> rassemble des questions et des réponses qui deviennent des données de type FAQ, lues par les moteurs et les assistants IA. Google ne montre les FAQ enrichies dans les résultats que pour les sites gouvernementaux et de santé. La tile Accordéon a le même interrupteur, <span class="ui">Schema FAQ (SEO)</span>, et la tile Carte peut décrire le lieu avec <span class="ui">Schema.org JSON-LD (SEO)</span>.</p>
<p>Dans <span class="ui">Schema.org type</span>, deux types ont leurs champs, qui apparaissent sous le choix :</p>
<ul>
	<li><strong>Event</strong>, pour un concert, un spectacle ou un cours : début et fin, en présentiel ou en ligne, lieu et adresse, état (prévu, déplacé, annulé), organisateur, prix du billet et où réserver.</li>
	<li><strong>SoftwareApplication</strong>, pour un logiciel ou une app : catégorie, sur quoi il fonctionne, version et prix, 0 s’il est gratuit.</li>
</ul>
<p>L’analyse de la page signale les champs qui manquent, comme un événement sans date de début.</p>
<div class="nota"><p>Les autres types, par exemple Recipe ou HowTo, ne produisent que le nom, la description et l’image. Pour les données qui servent aux résultats enrichis de Google, écris-les dans <span class="ui">JSON-LD personnalisé</span>, qu’Olobuild vérifie avant de le publier.</p></div>

<h2 id="redirect">7. Redirections et 404</h2>
<p>Quand une page change d’adresse, une redirection amène celui qui arrive par l’ancien lien vers la nouvelle page. La gestion se trouve dans <span class="ui">Système</span> › <span class="ui">Configuration</span> › <span class="ui">SEO &amp; Confidentialité</span> › <span class="ui">Redirections &amp; 404</span>, et le tableau de bord signale les adresses en 404 encore sans redirection.</p>
<ol class="passi">
	<li><strong>Ajoute la redirection.</strong> <span class="ui">Ajouter une redirection</span>, puis l’ancienne adresse dans <span class="ui">Depuis l’URL</span> et la nouvelle dans <span class="ui">Vers l’URL</span>.</li>
	<li><strong>Choisis le type.</strong> 301 pour un déplacement définitif, le cas le plus courant. 302 et 307 pour un déplacement temporaire. 410 pour dire que la page a été retirée et ne reviendra pas.</li>
	<li><strong>Vérifie les 404.</strong> Le journal des 404 liste les adresses demandées qui n’existent pas, jusqu’à 500. <span class="ui">Créer une redirection</span> sur une ligne prépare une redirection 301 : corrige la destination avant de l’enregistrer.</li>
</ol>
<p>Pour déplacer un groupe d’adresses, commence <span class="ui">Depuis l’URL</span> par <code>~</code> et écris une expression régulière. <code>$1</code> dans la destination reprend la partie capturée.</p>
<div class="nota attenzione"><p>Une redirection vaut aussi quand l’adresse de départ est une page qui existe. Avant d’en créer une sur une adresse utilisée, vérifie que cette page ne sert plus.</p></div>
<p>La vue IndexNow prévient Bing et les autres moteurs qui y adhèrent quand tu publies ou mets à jour une page, depuis le builder aussi. <span class="ui">Générer</span> crée une clé, et Olobuild publie tout seul le fichier de vérification que les moteurs contrôlent : le lien apparaît sous la clé après l’enregistrement.</p>

<h2 id="sitemap">8. Sitemap</h2>
<p>Le sitemap liste les pages du site pour les moteurs de recherche. C’est celui de WordPress, à l’adresse <code>/wp-sitemap.xml</code> : Olobuild en retire les pages en noindex, et l’interrupteur Sitemap XML de l’onglet SEO global l’éteint. Indique-le dans Google Search Console. Si tu utilises une extension SEO, c’est le sien qui vaut.</p>

<h2 id="verifica">9. Search Console et Bing</h2>
<p>Google Search Console et Bing Webmaster Tools disent pour quelles recherches le site sort, combien de fois on clique dessus et s’il y a des pages que les moteurs n’arrivent pas à lire. Bing vaut aussi pour Copilot. Pour y entrer, tu dois prouver que le site est à toi, et il y a deux chemins.</p>
<p><strong>Par le DNS, le meilleur chemin.</strong> Dans la <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Search Console</a>, choisis la propriété « Domaine » et écris le domaine, par exemple <code>monsite.fr</code>. Google te donne un enregistrement TXT à ajouter dans le DNS du domaine, depuis le panneau de celui qui le gère pour toi. Une fois vérifié, il vaut pour tout le domaine, sous-domaines et dossiers compris, et dans Olobuild il n’y a rien à remplir. Quand Olobuild trouve ce code dans le DNS, il l’écrit dans <span class="ui">Système</span> › <span class="ui">Configuration</span> › <span class="ui">SEO &amp; Confidentialité</span> › <span class="ui">SEO global</span> › <span class="ui">Vérification des moteurs</span>, sous le champ de Google : « Code de Google trouvé dans le DNS ».</p>
<p><strong>Par le code dans l’en-tête, si tu ne peux pas toucher au DNS.</strong></p>
<ol class="passi">
	<li><strong>Ajoute le site.</strong> Dans la Search Console, choisis « Préfixe de l’URL », écris l’adresse du site et, comme vérification, « Balise HTML ».</li>
	<li><strong>Copie le code.</strong> Google montre une balise meta : copie-la en entier, ou seulement le code entre les guillemets de content.</li>
	<li><strong>Colle-le.</strong> Dans <span class="ui">Vérification des moteurs</span>, dans le champ Google Search Console, puis Enregistrer. Olobuild ne garde que le code et l’écrit dans l’en-tête de chaque page.</li>
	<li><strong>Vérifie.</strong> Retourne dans la Search Console et appuie sur Valider.</li>
</ol>
<p>Dans les deux cas, ajoute ensuite le sitemap, <code>/wp-sitemap.xml</code>, dans la section Sitemaps de la Search Console. Si le site a d’autres sites WordPress dans des dossiers, comme <code>monsite.fr/boutique</code>, chacun a son sitemap : ajoute-les tous.</p>
<p>Pour <a href="https://www.bing.com/webmasters" target="_blank" rel="noopener">Bing Webmaster Tools</a>, le chemin le plus court est d’importer le site déjà vérifié depuis la Search Console : il emporte aussi les sitemaps. Sinon, il y a le champ Bing, comme pour Google.</p>

<h2 id="plugin">10. Avec Yoast, Rank Math et compagnie</h2>
<p>Avec Yoast SEO, Rank Math, All in One SEO ou SEOPress actifs, Olobuild se met de côté : il n’écrit pas le titre, la description, Open Graph, le canonical et les données structurées, pour que la page ne les reçoive pas deux fois. Dans ce cas, remplis les champs dans le panneau de l’extension SEO. Ceux d’Olobuild restent enregistrés mais ne sont pas utilisés.</p>
<p>Les versions de la page dans d’autres langues et leurs liens pour les moteurs de recherche, c’est le travail d’OLOlang.</p>
