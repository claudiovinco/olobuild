<?php
/**
 * Guida «Prestazioni» (inglese). Solo HTML: titolo, descrizione e minuti
 * li stampa Olobuild_Guida::render_guida() dall'elenco delle guide.
 *
 * @package Olobuild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<nav class="olo-guida-toc" aria-label="In this guide">
	<strong>In this guide</strong>
	<ol>
		<li><a href="#da-solo">What Olobuild does on its own</a></li>
		<li><a href="#impostazioni">The Performance &amp; Cache settings</a></li>
		<li><a href="#cache">The full-page cache</a></li>
		<li><a href="#esterne">External caches and clearing</a></li>
		<li><a href="#pagina">A fast page, in practice</a></li>
		<li><a href="#misura">Measuring</a></li>
	</ol>
</nav>

<h2 id="da-solo">1. What Olobuild does on its own</h2>
<p>Some optimisations are always on and have no switches.</p>
<ul>
	<li><strong>Only what's needed.</strong> Olobuild's stylesheets and scripts load only on pages made with Olobuild, and a tile's scripts, for example the map's, only if the tile is on the page.</li>
	<li><strong>Fonts from your server.</strong> The Google Fonts the site uses are downloaded once and served by the site, with no calls to Google on every visit. If the download fails, the page uses the system fonts.</li>
	<li><strong>Content on demand.</strong> After the first section, the content is mounted when it is about to come into view. The tiles of the first section, in any case the first 3, forms, maps and menus are always ready. Search engines and AI crawlers get the whole page: many of them don't run JavaScript and wouldn't see the content on demand.</li>
	<li><strong>Lazy images.</strong> Images load when they are about to become visible.</li>
	<li><strong>Light videos.</strong> The Video tile, with <span class="ui">Lazy Load (Facade)</span> on as by default, shows the cover and loads the YouTube or Vimeo player only on click.</li>
	<li><strong>Weightless animations.</strong> Entrance animations start when the element comes into view. People who asked their operating system to reduce motion see the elements straight away, without animation.</li>
</ul>

<h2 id="impostazioni">2. The Performance &amp; Cache settings</h2>
<p>You find them in <span class="ui">System</span> › <span class="ui">Setup</span> › <span class="ui">Performance &amp; Services</span> › <span class="ui">Performance &amp; Cache</span>, and from the Performance card in the dashboard.</p>
<table>
	<thead><tr><th>Setting</th><th>What it does</th><th>Default</th></tr></thead>
	<tbody>
		<tr><td><strong>Defer JavaScript</strong></td><td>Olobuild's scripts download without holding up the page and start when it is ready.</td><td>On</td></tr>
		<tr><td><strong>CSS cache in static files</strong></td><td>Puts each template's style in a file the browser keeps, instead of rewriting it in the page.</td><td>On</td></tr>
		<tr><td><strong>Minify CSS</strong></td><td>Removes spaces and comments from Olobuild's stylesheets.</td><td>On</td></tr>
		<tr><td><strong>Preload custom fonts</strong></td><td>Starts downloading the text and heading fonts straight away, at the weight used: the text appears in its own font, without changing look a moment later.</td><td>On</td></tr>
		<tr><td><strong>fetchpriority hero image</strong></td><td>The first image of the first section loads straight away and with high priority. The others stay lazy.</td><td>On</td></tr>
		<tr><td><strong>Lazy load self-hosted videos</strong></td><td>Videos uploaded to the site, playing automatically and without sound, start when they come into view.</td><td>On</td></tr>
		<tr><td><strong>Full-page cache</strong></td><td>Stores pages ready-made and serves them to visitors without rebuilding them. Read chapter 3 before turning it on.</td><td>Off</td></tr>
		<tr><td><strong>UIkit subset</strong></td><td>Cuts the UIkit stylesheet down to the parts the pages actually use. It learns by visiting the pages and corrects itself.</td><td>Off</td></tr>
		<tr><td><strong>Per-tile CSS</strong></td><td>Loads the style of some tile families only where needed, such as maps, galleries and menus.</td><td>Off</td></tr>
		<tr><td><strong>Browser cache for media (.htaccess)</strong></td><td>Tells the browser to keep images, fonts, CSS and JS for a long time. It works on Apache and LiteSpeed servers.</td><td>Off</td></tr>
		<tr><td><strong>Automatic DNS prefetch &amp; preconnect</strong></td><td>Prepares the connection to YouTube and Vimeo in advance on the pages that contain them. The domains written in Custom domains are always included.</td><td>On</td></tr>
		<tr><td><strong>Head cleanup</strong></td><td>Removes WordPress scripts and styles that many sites don't use: jQuery Migrate, emoji, block styles and classic theme styles.</td><td>Off</td></tr>
		<tr><td><strong>Critical CSS</strong></td><td>Writes the style of the first sections into the page (2, in <span class="ui">Above-the-fold sections</span>) and loads the rest right after. It is redone after the days set in <span class="ui">Cache duration</span> and when you save the template. <span class="ui">Regenerate critical CSS</span> rebuilds it straight away.</td><td>Off</td></tr>
	</tbody>
</table>
<div class="nota attenzione"><p><strong>Remove Block CSS</strong> also removes WordPress's global styles. With a block theme, after turning it on check that the site still has its colours and fonts.</p></div>
<p>At the top of the card, <span class="ui">Cache status</span> counts the pages in the full-page cache, the style files and the pages with Critical CSS, and tells you when the cache was last emptied. <span class="ui">Empty all</span> deletes them all together, with the UIkit subset and Olobuild's temporary data.</p>

<h2 id="cache">3. The full-page cache</h2>
<p>The full-page cache stores each page ready-made and serves it to visitors without rebuilding it. It is the setting that matters most on a site with many visits.</p>
<ul>
	<li>It doesn't apply to people who are logged in, to the WooCommerce cart and checkout, to sitemaps or to addresses with parameters other than tracking ones.</li>
	<li>It keeps separate versions for computer and phone, and per language.</li>
	<li>Each page stays in the cache for 8 hours.</li>
	<li>It empties itself when you save in the builder or in WordPress, change menus, theme, active header or footer, global colours and fonts, assignment rules or Performance settings, and with every Olobuild update.</li>
</ul>
<div class="nota attenzione"><p>Don't turn it on if the site already uses a cache plugin such as LiteSpeed Cache or WP Rocket. In that case Olobuild doesn't install it, and next to the switch the card tells you why.</p></div>

<h2 id="esterne">4. External caches and clearing</h2>
<p>Olobuild doesn't control external caches. If the site uses LiteSpeed Cache, WP Rocket, the hosting's cache or Cloudflare, empty it yourself after changing colours, fonts, header or footer: otherwise visitors may still see the previous version.</p>
<p><span class="ui">System</span> › <span class="ui">Tools</span> › <span class="ui">Olobuild cache</span> › <span class="ui">Delete files and data</span> deletes the generated style files, the full-page cache and Olobuild's temporary data, which are recreated on the next visit. It helps when a page shows an old style. It does the same as <span class="ui">Empty all</span> in the Performance card.</p>

<h2 id="pagina">5. A fast page, in practice</h2>
<ol class="passi">
	<li><strong>Images at the right size.</strong> Upload images no wider than the space they take up, 2000 pixels for a full-width photo, in WebP or compressed JPEG.</li>
	<li><strong>The first image straight away.</strong> With <span class="ui">fetchpriority hero image</span> on, as by default, the first image of the first section starts by itself with high priority. If the main photo is further down, in its Image tile open <span class="ui">Advanced</span> › <span class="ui">SEO &amp; Accessibility</span> and choose Eager in <span class="ui">Image loading</span> and High in <span class="ui">Fetch Priority</span>.</li>
	<li><strong>Few fonts.</strong> 2 families are enough, with 3 or 4 weights in all. Every extra weight is one more file to download.</li>
	<li><strong>Videos with a cover.</strong> Use the Video tile with Lazy Load (Facade) on, instead of pasting the YouTube code into an HTML tile.</li>
	<li><strong>Effects where they count.</strong> Animated backgrounds, parallax and background videos weigh more than a photo. Use them where they tell something.</li>
</ol>

<h2 id="misura">6. Measuring</h2>
<p>Measure the page with Google's PageSpeed Insights or with Lighthouse, in Chrome's developer tools. Run the test in a private window: when you are logged in you see the page without the full-page cache, slower than the visitors' one.</p>
<p>Repeat the measurement 2 or 3 times and look above all at the loading of the largest element (LCP) and the layout stability (CLS). The score changes from one test to the next, and from one phone to another.</p>
