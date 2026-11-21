<?php
/**
 * Guida «Template» (inglese). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#cosa">Templates and pages</a></li>
		<li><a href="#tipi">The template types</a></li>
		<li><a href="#elenco">Blocks &amp; Pages</a></li>
		<li><a href="#header-footer">Which header a page gets</a></li>
		<li><a href="#regole">Assignment rules</a></li>
		<li><a href="#revisioni">Revisions</a></li>
		<li><a href="#pronti">Ready-made blocks, themes and global elements</a></li>
		<li><a href="#import">Import and export</a></li>
	</ol>
</nav>

<h2 id="cosa">1. Templates and pages</h2>
<p>A template is the content built with Olobuild: sections, rows, columns and tiles. A WordPress page shows the template connected to it, and the connection is made by itself when you create the page from Olobuild.</p>
<p>A template can also be inserted anywhere WordPress accepts a shortcode. In Blocks &amp; Pages one click on the shortcode copies it, in the form <code>[olo_template id="12"]</code>.</p>
<div class="nota"><p>If the WordPress page ends up in the bin, the next time you save the template Olobuild creates a new one, connected to the same template.</p></div>

<h2 id="tipi">2. The template types</h2>
<table>
	<thead><tr><th>Type</th><th>What it is for</th></tr></thead>
	<tbody>
		<tr><td><strong>Page</strong></td><td>The content of a page of the site.</td></tr>
		<tr><td><strong>Header</strong></td><td>The header with logo and menu, at the top of the pages.</td></tr>
		<tr><td><strong>Footer</strong></td><td>The footer, at the bottom of the pages.</td></tr>
		<tr><td><strong>Single</strong></td><td>The layout shared by all the content of a type, for example every post or every product. You create it from Single Template, choosing the content type.</td></tr>
		<tr><td><strong>Mega Panel</strong></td><td>The content of a mega menu panel.</td></tr>
		<tr><td><strong>Widget</strong></td><td>Reusable content inside cards, tabs and sliders.</td></tr>
		<tr><td><strong>404</strong></td><td>The page that appears when an address doesn't exist.</td></tr>
	</tbody>
</table>

<h2 id="elenco">3. Blocks &amp; Pages</h2>
<p><span class="ui">Build</span> › <span class="ui">Blocks &amp; Pages</span> lists all the templates of the site. The filters at the top show one type at a time, and each card shows its status: Draft, Published or Active. From the actions of a card you can edit, duplicate, export and delete the template.</p>
<div class="nota attenzione"><p><strong>Delete</strong> removes the template for good. The connected WordPress page stays, but no longer shows the content made with Olobuild.</p></div>

<h2 id="header-footer">4. Which header a page gets</h2>
<p>Every page shows a header and a footer. Olobuild picks them in this order, and stops at the first answer it finds.</p>
<figure class="olo-schema">
	<div class="flusso">
		<div><strong>1. The page's own choice</strong>The Olobuild box in the WordPress editor, with the Header and Footer menus.</div>
		<div class="freccia" aria-hidden="true"><?php echo Olobuild_Guida::icona( 'freccia', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico. ?></div>
		<div><strong>2. The assignment rules</strong>For example a different header for blog posts.</div>
		<div class="freccia" aria-hidden="true"><?php echo Olobuild_Guida::icona( 'freccia', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico. ?></div>
		<div><strong>3. Active header and footer</strong>The ones that apply to the whole site.</div>
	</div>
	<figcaption>With “Default (global)” in the page box, the rules decide or, if there are none, the active header and footer.</figcaption>
</figure>
<p>To make a header or a footer active, open it in the builder, publish it and press <span class="ui">Activate</span> in the top bar. The same goes for a Single template and for the 404 page.</p>
<p>In a page's canvas, the header and footer tell you which template they come from and whether other pages use them too. <span class="ui">Open the template</span> opens them in the builder, and a button then takes you back to the page you started from.</p>

<h2 id="regole">5. Assignment rules</h2>
<p>Rules decide which header, footer, Single or archive to use in a part of the site. You find them in <span class="ui">System</span> › <span class="ui">Setup</span> › <span class="ui">Content &amp; Templates</span> › <span class="ui">Template assignment</span>.</p>
<ol class="passi">
	<li><strong>Create the rule.</strong> Press <span class="ui">New rule</span>, choose the <span class="ui">Context</span> (Header, Footer, Single or Archive) and the template to use.</li>
	<li><strong>Add the conditions.</strong> With <span class="ui">Add condition</span> you choose where the rule applies, and for each condition whether to include or exclude. The value is picked from a menu.</li>
	<li><strong>Combine.</strong> <span class="ui">Conditions operator</span> says whether all the conditions must apply or at least one.</li>
	<li><strong>Set the priority.</strong> When two rules apply to the same page, the one with the lowest <span class="ui">Priority</span> number wins. The default value is 10.</li>
</ol>
<p>The available conditions are: Whole site, Home page only, All single pages, Page, Post, Content type, Archive, Category, Tag, Logged-in user, Logged-out user, User role, 404 page, Search page, and 4 conditions for WooCommerce.</p>
<div class="nota"><p>Without rules, the active header and footer apply. A rule is only needed where you want something different.</p></div>

<h2 id="revisioni">6. Revisions</h2>
<p>Every time you save, Olobuild keeps the previous version of the template, up to 50 versions. You find them in the builder, in <span class="ui">Revision history</span>, with date and size.</p>
<ol class="passi">
	<li>Open <span class="ui">Revision history</span> from the top bar.</li>
	<li>Press <span class="ui">Restore</span> on the version you need: the canvas loads it.</li>
	<li>Check the page and press <span class="ui">Save</span>. Until you save, the site stays as it was.</li>
</ol>

<h2 id="pronti">7. Ready-made blocks, themes and global elements</h2>
<p>The <span class="ui">Blocks &amp; Pages</span> button in the builder bar opens the library: 89 blocks, 58 full pages and the sections you saved yourself with <span class="ui">Save section as template</span> from the right-click menu. A full page can be used in two ways: <span class="ui">Replace all</span> or <span class="ui">Add at the end</span>.</p>
<p><span class="ui">Site themes</span> opens the same themes as the first setup. A theme changes the whole site, and before you confirm, the window lists everything that will change.</p>
<p>An element can be made <strong>global</strong> from the right-click menu, with <span class="ui">Save as global</span>. A global element is inserted from the Global category of the Elements panel and is the same on every page that uses it. <span class="ui">Unlink global</span> turns it into an independent copy.</p>

<h2 id="import">8. Import and export</h2>
<p><span class="ui">Build</span> › <span class="ui">Import / Export</span> moves your work between sites with <code>.json</code> files.</p>
<ul>
	<li><strong>Export Template</strong> saves a template, with its images too if you choose <span class="ui">Include media</span>.</li>
	<li><strong>Export Full Site</strong> saves templates, pages, home page, menus, media, styles and global options.</li>
	<li><strong>Import</strong> loads a file exported from another Olobuild site.</li>
</ul>
<div class="nota attenzione"><p>Importing a full site <strong>replaces</strong> the colours, fonts and global options of the site that receives it. Export the current site first, if you want to be able to go back.</p></div>
<p>In Blocks &amp; Pages, <span class="ui">Select</span> and then <span class="ui">Export theme</span> put several templates in a single file. In the builder, importing a JSON file creates a new draft template and leaves the open one untouched.</p>
