<?php
/**
 * Guida «Primi passi» (inglese). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#configurazione">The first setup</a></li>
		<li><a href="#bacheca">The dashboard</a></li>
		<li><a href="#crea">Creating a page</a></li>
		<li><a href="#builder">The builder in 4 areas</a></li>
		<li><a href="#elementi">Adding and arranging elements</a></li>
		<li><a href="#pubblica">Saving and publishing</a></li>
		<li><a href="#dispositivi">Computer, tablet and phone</a></li>
		<li><a href="#scorciatoie">Keyboard shortcuts</a></li>
	</ol>
</nav>

<h2 id="configurazione">1. The first setup</h2>
<p>The first time it is activated, Olobuild opens the setup wizard, in 3 steps. If you put it off, the welcome panel in the WordPress Dashboard opens it again with <span class="ui">Start the setup</span>.</p>
<ol class="passi">
	<li><strong>WordPress theme.</strong> <span class="ui">Install and activate</span> puts in Hello Olobuild, the light theme included in the plugin. With <span class="ui">Use current theme</span> you keep the theme you already have.</li>
	<li><strong>Choose a design.</strong> Choose one of the 53 ready-made themes, or <span class="ui">Empty</span>, and press <span class="ui">Import selected theme</span>. The theme creates pages and menus, activates header and footer, applies its colours and fonts and sets the home page. Before importing, Olobuild saves a snapshot of the site, which you can bring back with <span class="ui">Restore</span>.</li>
	<li><strong>All set.</strong> <span class="ui">Open Olobuild</span> takes you to the dashboard, <span class="ui">View the site</span> opens the site.</li>
</ol>
<div class="nota"><p>With <strong>Empty</strong> you get the bare minimum to start: a Home page set as the home page, the “Main Menu”, a “Basic header” and a “Basic footer”, already active.</p></div>

<h2 id="bacheca">2. The dashboard</h2>
<p>The dashboard opens from the <span class="ui">Olobuild</span> entry of the WordPress menu. The top bar is the same on every Olobuild page and splits the work into 4 areas.</p>
<table>
	<thead><tr><th>Area</th><th>What it holds</th></tr></thead>
	<tbody>
		<tr><td><strong>Build</strong></td><td>Blocks &amp; Pages (the site's templates), Popups, Import / Export</td></tr>
		<tr><td><strong>Media</strong></td><td>Search for photos and videos to put in the pages</td></tr>
		<tr><td><strong>Collect</strong></td><td>Form submissions, Newsletter, Tracking &amp; Analytics</td></tr>
		<tr><td><strong>System</strong></td><td>Setup, Tools and this Guide</td></tr>
	</tbody>
</table>
<p>In the dashboard you find <span class="ui">Continue where you left off</span>, the <span class="ui">Quick actions</span>, the <span class="ui">Management</span> cards and, on the right, the Resource centre with news and guides. The search field at the top, or <kbd>Ctrl</kbd> <kbd>K</kbd>, searches pages, templates and settings from any Olobuild page.</p>

<h2 id="crea">3. Creating a page</h2>
<p><span class="ui">Create page</span>, among the Quick actions, creates in one go an “Untitled” template and the connected WordPress page, both as drafts, and opens the builder. <span class="ui">New page</span> at the top of the dashboard does the same.</p>
<p>You can also start from WordPress:</p>
<ul>
	<li>in the Pages list, the <span class="ui">Create with Olobuild</span> action under the title of a page</li>
	<li>in the admin bar, <span class="ui">Edit with Olobuild</span> while you look at a page of the site</li>
	<li>in the WordPress editor, the <span class="ui">Olobuild</span> box, where you also choose the page's header and footer</li>
</ul>
<div class="nota"><p>Give the page its title before publishing it. You change the title with a click on the page name, in the builder bar. WordPress takes the page's address from the title at the moment of publishing.</p></div>

<h2 id="builder">4. The builder in 4 areas</h2>
<figure class="olo-schema">
	<div class="finestra" aria-hidden="true">
		<div class="z-barra"><span class="num">1</span>Top bar</div>
		<div class="z-palette"><span class="num">2</span>Elements<br>and Structure</div>
		<div class="z-canvas"><span class="num">3</span>Canvas</div>
		<div class="z-inspector"><span class="num">4</span>Content · Style<br>· Advanced</div>
	</div>
	<ol class="legenda">
		<li><span class="num">1</span><span><strong>Top bar.</strong> Title, Page settings, devices and zoom, Blocks &amp; Pages, undo and redo, Preview, Save and Publish.</span></li>
		<li><span class="num">2</span><span><strong>Left panel.</strong> <span class="ui">Elements</span> gathers the tiles by category, with Recent, Favourites and Global. <span class="ui">Structure</span> shows the page tree.</span></li>
		<li><span class="num">3</span><span><strong>Canvas.</strong> The page as the visitor will see it, drawn by the same engine as the site, with header and footer.</span></li>
		<li><span class="num">4</span><span><strong>Right panel.</strong> It appears when you select an element: <span class="ui">Content</span> for texts, images and links, <span class="ui">Style</span> for the look, <span class="ui">Advanced</span> for visibility, effects and positioning.</span></li>
	</ol>
	<figcaption>The 4 areas of the builder. The right panel also opens with Page settings.</figcaption>
</figure>
<p>Every page is made of sections. A section holds rows, a row holds columns, and the columns hold the elements, which in Olobuild are called tiles.</p>
<figure class="olo-schema">
	<div class="albero" aria-hidden="true">
		<div class="l1">Section <small>full-width band, with its own background</small></div>
		<div class="l2">Row <small>splits the section into columns</small></div>
		<div class="l3">Column <small>a different width for each device</small></div>
		<div class="l4">Tile <small>title, text, image, button, gallery…</small></div>
	</div>
	<figcaption>The hierarchy of a page, as the Structure panel shows it.</figcaption>
</figure>
<p>In a tile's Style the controls are split into two blocks. <strong>Element</strong> acts on what the tile draws, for example the button. <strong>Container</strong> acts on the box the tile occupies in the grid, with its spacing and its background.</p>

<h2 id="elementi">5. Adding and arranging elements</h2>
<ol class="passi">
	<li><strong>Add.</strong> Drag a tile from the Elements panel into the canvas, or click its card: the tile goes in after the selected element, or at the bottom of the page. On an empty page use <span class="ui">Add a tile</span> or <span class="ui">Choose layout</span>.</li>
	<li><strong>Search.</strong> <kbd>Ctrl</kbd> <kbd>K</kbd> opens the Finder, which finds a tile by name without scrolling through the categories.</li>
	<li><strong>Insert a section.</strong> The <span class="ui">+</span> button between two sections opens <span class="ui">Insert a tile or a row</span>, with a new tile, an empty row or a ready-made block from the library.</li>
	<li><strong>Move and duplicate.</strong> When you hover over an element, the commands to drag it, move it up or down, duplicate it and delete it appear. Right-click opens the full menu, with Copy, Paste, Copy style and Paste style.</li>
	<li><strong>Type.</strong> Double-click a text to edit it right in the canvas. <kbd>Esc</kbd> cancels the edit in progress.</li>
</ol>
<div class="nota"><p>Next to the title of a field or a section there may be an <strong>(i)</strong>. Open it to read what the control is for: the explanations live there, so the panel stays tidy.</p></div>

<h2 id="pubblica">6. Saving and publishing</h2>
<p><span class="ui">Save</span> lights up when there is something to save, and saves page, header and footer together if you changed them in the canvas. Olobuild doesn't save on its own: if you close the tab with unsaved changes, the browser asks you to confirm.</p>
<p><span class="ui">Publish</span> puts the template and the connected WordPress page online, with its title.</p>
<div class="nota attenzione"><p>After publishing, every <strong>Save</strong> goes online straight away: the site always shows the last saved version. To try a change without touching the site, duplicate the page in Blocks &amp; Pages and work on the copy.</p></div>
<p>Every time you save, Olobuild keeps the previous version. You find them in <span class="ui">Revision history</span>, in the top bar. The <a href="<?php echo esc_url( Olobuild_Guida::url( 'template' ) . '#revisioni' ); ?>">Templates</a> guide explains how to bring one back.</p>
<p><span class="ui">Preview</span> hides the panels and shows the page as the visitor sees it. <span class="ui">Live</span> opens the real page in a new tab. <kbd>Esc</kbd> takes you back to editing.</p>

<h2 id="dispositivi">7. Computer, tablet and phone</h2>
<p>The device buttons in the top bar resize the canvas: Widescreen, Desktop, Tablet and Mobile. Tablet landscape and Mobile landscape are turned on in <span class="ui">System</span> › <span class="ui">Setup</span> › <span class="ui">Editor devices</span>. Many controls have a value for each device. When you change one, you change the value of the device chosen in the bar.</p>
<p>The zoom, next to the devices, shrinks or enlarges the canvas without changing the page.</p>

<h2 id="scorciatoie">8. Keyboard shortcuts</h2>
<p>On a Mac use <kbd>Cmd</kbd> instead of <kbd>Ctrl</kbd>. In the builder the list is always at hand: it's the keyboard icon in the top bar.</p>
<table>
	<thead><tr><th>Keys</th><th>Action</th></tr></thead>
	<tbody>
		<tr><td><kbd>Ctrl</kbd> <kbd>S</kbd></td><td>Save</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Z</kbd> · <kbd>Ctrl</kbd> <kbd>Shift</kbd> <kbd>Z</kbd></td><td>Undo · Redo, up to 100 steps</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>C</kbd> · <kbd>Ctrl</kbd> <kbd>V</kbd> · <kbd>Ctrl</kbd> <kbd>D</kbd></td><td>Copy · Paste · Duplicate the selected element</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Alt</kbd> <kbd>C</kbd> · <kbd>Ctrl</kbd> <kbd>Alt</kbd> <kbd>V</kbd></td><td>Copy style · Paste style</td></tr>
		<tr><td><kbd>Delete</kbd> or <kbd>Backspace</kbd></td><td>Delete the selected element</td></tr>
		<tr><td><kbd>Ctrl</kbd> + click</td><td>Select several elements</td></tr>
		<tr><td><kbd>Alt</kbd> <kbd>↑</kbd> · <kbd>Alt</kbd> <kbd>↓</kbd></td><td>Move up · Move down</td></tr>
		<tr><td><kbd>Alt</kbd> <kbd>←</kbd> · <kbd>Alt</kbd> <kbd>→</kbd></td><td>Move to the column alongside</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>K</kbd></td><td>Search for a tile (Finder)</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Alt</kbd> <kbd>P</kbd></td><td>Preview. <kbd>Esc</kbd> to leave</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Shift</kbd> <kbd>A</kbd></td><td>AI assistant, if it is set up</td></tr>
	</tbody>
</table>
