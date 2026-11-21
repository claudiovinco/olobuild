<?php
/**
 * Guida «SEO e Open Graph» (inglese). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#dove">Where to set it</a></li>
		<li><a href="#titolo">Title and description</a></li>
		<li><a href="#analisi">Page analysis</a></li>
		<li><a href="#social">Social media preview</a></li>
		<li><a href="#robots">Indexing and canonical</a></li>
		<li><a href="#schema">Structured data</a></li>
		<li><a href="#redirect">Redirects and 404</a></li>
		<li><a href="#sitemap">Sitemap</a></li>
		<li><a href="#verifica">Search Console and Bing</a></li>
		<li><a href="#plugin">With Yoast, Rank Math and the like</a></li>
	</ol>
</nav>

<h2 id="dove">1. Where to set it</h2>
<p>Olobuild writes title, description, social preview and structured data into the head of every page of the site. You set them page by page, in two places that save the same data.</p>
<ul>
	<li><strong>In the builder:</strong> <span class="ui">Page settings</span> › <span class="ui">SEO</span>, with the Basic, Social, Robots, Schema and FAQ tabs. The group saves itself, without pressing Save, and appears when the template is connected to a page.</li>
	<li><strong>In the WordPress editor:</strong> the <span class="ui">Olobuild SEO</span> box, in pages, posts and the other public content, with the preview of the result on Google.</li>
</ul>
<p>In the lists of pages and posts, the SEO column shows with a dot how many of the main fields are filled in.</p>
<p>The values that apply to the whole site are in <span class="ui">System</span> › <span class="ui">Setup</span> › <span class="ui">SEO &amp; Privacy</span> › <span class="ui">Global SEO</span>: the title pattern and separator, the default description, the image for social media, the X profile and the type of whoever publishes the site. A page with its own fields filled in always wins.</p>

<h2 id="titolo">2. Title and description</h2>
<p><span class="ui">SEO Title</span> is the title that appears in the browser tab and in search results. The counter shows 60 characters, the length Google shows in full. If you leave it empty, the pattern of the Global SEO tab applies: by default the page title, the separator and the site name.</p>
<p><span class="ui">Meta Description</span> is the text under the title in the results, with a 160-character counter. If it is missing, Olobuild uses the first 30 words of the page summary, then the default description of the Global SEO tab and, if that is missing too, the site tagline.</p>
<p><span class="ui">Focus keyword</span> is the page's main keyword, that is the words someone looking for it would use. The page analysis checks where it appears.</p>

<h2 id="analisi">3. Page analysis</h2>
<p>Below the Focus keyword, in the Basic tab, <span class="ui">Page analysis</span> reads the page the way Google and AI assistants see it: the text of the tiles, the headings, the images and the links. Header and footer are left out. Changes count straight away, even before saving; the button with the arrow runs the analysis again.</p>
<ul>
	<li><strong>To fix</strong>: what takes the page out of the results or leaves it without a main heading, such as noindex turned on, the whole site hidden from search engines, no H1 heading.</li>
	<li><strong>To improve</strong>: title or description too long or too short, more than one H1, headings that skip a level (from H2 to H4), too little text, images without alternative text, no links to the other pages of the site, the keyword missing from the places that matter or already used by another page.</li>
	<li><strong>Passed</strong> and <strong>Page headings</strong> open with a click: the second is the index of the H1–H6 headings, with the ones to fix marked.</li>
</ul>
<p>The (i) next to each item explains how to fix it. The analysis doesn't count how many times you repeat the keyword: Google no longer looks at that, what counts is that the page really talks about it. The <span class="ui">Olobuild SEO</span> box in the WordPress editor has the same analysis, on the saved page.</p>

<h2 id="social">4. Social media preview</h2>
<p>When someone shares the page on Facebook, LinkedIn, WhatsApp or X, the preview comes from the Open Graph data. You find them in the Social tab.</p>
<ul>
	<li><span class="ui">OG Title</span> and <span class="ui">OG Description</span> change title and text for social media only. Empty, those of the Basic tab apply.</li>
	<li><span class="ui">OG Image</span> is the preview image. The right size is 1200 × 630 pixels.</li>
	<li>The <span class="ui">Twitter / X</span> fields are only needed if you want a different text: empty, X uses the Open Graph ones.</li>
</ul>
<p>Without an OG Image, Olobuild uses the page's featured image, then the default image of the Global SEO tab and finally the site logo. The format of the X preview is chosen in the same tab. Without an image, the preview is compact anyway.</p>
<div class="nota"><p>Social networks keep the preview for days. After changing it, refresh it with the platform's debugging tool, for example Facebook's Sharing Debugger.</p></div>

<h2 id="robots">5. Indexing and canonical</h2>
<p><span class="ui">Robots default</span> in the Global SEO tab applies to the whole site and is the same choice as WordPress's Settings › Reading. For a single page, in the Robots tab, <span class="ui">noindex</span> asks search engines not to show the page in the results. The page's links are still followed, unless you also turn on <span class="ui">nofollow</span>. Use noindex for service pages, such as a thank-you page after a form.</p>
<p><span class="ui">Canonical URL</span> gives the main address of a page that exists in several versions. Empty, the page's own address applies, and that is the right choice almost always.</p>

<h2 id="schema">6. Structured data</h2>
<p>Structured data describe the page to search engines in a format they read without interpreting the text. Olobuild writes them on its own:</p>
<ul>
	<li>on every page, whoever publishes the site, with logo and social profiles: the type (organisation, person, hotel…) is chosen in the Global SEO tab</li>
	<li>on the home page, also the site itself, with its internal search</li>
	<li>on the other pages, the navigation path (breadcrumb)</li>
	<li>on posts the Article type, on pages the Page type, on WooCommerce products the Product type</li>
</ul>
<p>All these data come out as a single graph, linked to each other: the page is part of the site, the article is the content of the page, site and article are published by whoever publishes the site. That is how search engines and AI recognise who you are.</p>
<p>In the FAQ tab, <span class="ui">Add FAQ</span> collects questions and answers that become FAQ data, read by search engines and AI assistants. Google shows rich FAQs in the results only for government and health sites. The Accordion tile has the same switch, <span class="ui">FAQ schema (SEO)</span>, and the Map tile can describe the place with <span class="ui">Schema.org JSON-LD (SEO)</span>.</p>
<p>In <span class="ui">Schema.org type</span>, two types have their own fields, which appear under the choice:</p>
<ul>
	<li><strong>Event</strong>, for a concert, a show or a course: start and end, in person or online, venue and address, status (scheduled, moved, cancelled), organiser, ticket price and where to book.</li>
	<li><strong>SoftwareApplication</strong>, for a program or an app: category, what it runs on, version and price, 0 if it is free.</li>
</ul>
<p>The page analysis flags the missing fields, such as an event without a start date.</p>
<div class="nota"><p>The other types, for example Recipe or HowTo, produce only name, description and image. For the data Google needs for rich results, write them in <span class="ui">Custom JSON-LD</span>, which Olobuild checks before publishing it.</p></div>

<h2 id="redirect">7. Redirects and 404</h2>
<p>When a page changes address, a redirect takes whoever arrives from the old link to the new page. You manage them in <span class="ui">System</span> › <span class="ui">Setup</span> › <span class="ui">SEO &amp; Privacy</span> › <span class="ui">Redirects &amp; 404</span>, and the dashboard flags the 404 addresses that still have no redirect.</p>
<ol class="passi">
	<li><strong>Add the redirect.</strong> <span class="ui">Add redirect</span>, then the old address in <span class="ui">From URL</span> and the new one in <span class="ui">To URL</span>.</li>
	<li><strong>Choose the type.</strong> 301 for a permanent move, the most common case. 302 and 307 for a temporary one. 410 to say the page has been removed and won't come back.</li>
	<li><strong>Check the 404s.</strong> The 404 log lists the requested addresses that don't exist, up to 500. <span class="ui">Create redirect</span> on a row prepares a 301 redirect: correct the destination before saving it.</li>
</ol>
<p>To move a group of addresses, start <span class="ui">From URL</span> with <code>~</code> and write a regular expression. <code>$1</code> in the destination picks up the captured part.</p>
<div class="nota attenzione"><p>A redirect applies even when the starting address is a page that exists. Before creating one on an address in use, check that the page is no longer needed.</p></div>
<p>The IndexNow view notifies Bing and the other participating search engines when you publish or update a page, from the builder too. <span class="ui">Generate</span> creates a key, and Olobuild publishes by itself the verification file the search engines check: the link appears under the key after saving.</p>

<h2 id="sitemap">8. Sitemap</h2>
<p>The sitemap lists the pages of the site for search engines. It is WordPress's own, at the address <code>/wp-sitemap.xml</code>: Olobuild removes the noindex pages from it, and the XML Sitemap switch in the Global SEO tab turns it off. Submit it in Google Search Console. If you use an SEO plugin, its sitemap applies.</p>

<h2 id="verifica">9. Search Console and Bing</h2>
<p>Google Search Console and Bing Webmaster Tools tell you which searches the site shows up for, how often it is clicked and whether there are pages the search engines can't read. Bing also counts for Copilot. To get in, you have to prove the site is yours, and there are two ways.</p>
<p><strong>With DNS, the best way.</strong> In <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Search Console</a> choose the “Domain” property and type the domain, for example <code>mysite.com</code>. Google gives you a TXT record to add to the domain's DNS, from the panel of whoever manages it for you. Once verified, it applies to the whole domain, subdomains and folders included, and in Olobuild there is nothing to fill in. When Olobuild finds that code in the DNS, it says so in <span class="ui">System</span> › <span class="ui">Setup</span> › <span class="ui">SEO &amp; Privacy</span> › <span class="ui">Global SEO</span> › <span class="ui">Search engine verification</span>, under the Google field: “Google code found in the DNS”.</p>
<p><strong>With the code in the head, if you can't touch the DNS.</strong></p>
<ol class="passi">
	<li><strong>Add the site.</strong> In Search Console choose “URL prefix”, type the site's address and, as the verification, “HTML tag”.</li>
	<li><strong>Copy the code.</strong> Google shows a meta tag: copy it whole, or just the code between the quotes of content.</li>
	<li><strong>Paste it.</strong> In <span class="ui">Search engine verification</span>, in the Google Search Console field, then Save. Olobuild keeps only the code and writes it into the head of every page.</li>
	<li><strong>Verify.</strong> Go back to Search Console and press Verify.</li>
</ol>
<p>In both cases, then add the sitemap, <code>/wp-sitemap.xml</code>, in the Sitemaps section of Search Console. If the site has other WordPress sites in folders, such as <code>mysite.com/shop</code>, each has its own sitemap: add them all.</p>
<p>For <a href="https://www.bing.com/webmasters" target="_blank" rel="noopener">Bing Webmaster Tools</a> the shortest way is to import the site already verified from Search Console: it brings the sitemaps along too. Otherwise there is the Bing field, as for Google.</p>

<h2 id="plugin">10. With Yoast, Rank Math and the like</h2>
<p>With Yoast SEO, Rank Math, All in One SEO or SEOPress active, Olobuild steps aside: it doesn't write title, description, Open Graph, canonical and structured data, so the page doesn't get them twice. In that case fill in the fields in the SEO plugin's panel. Olobuild's fields stay saved but are not used.</p>
<p>The versions of the page in other languages and their links for search engines are OLOlang's job.</p>
