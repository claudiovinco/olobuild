=== Olobuild ===
Contributors: claudiovinco
Tags: page-builder, drag-and-drop, builder, grid, blocks
Requires at least: 5.9
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.4.172
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional holonic page builder for WordPress with a drag & drop tile grid system.

== Description ==

Olobuild is a professional page builder that lets you design pages, headers and footers
visually on a grid, by dragging and dropping ready-made "tiles" (sections, sliders, cards,
charts, maps, galleries, forms and more). It ships with a global style system (colors,
typography, spacing) so every tile stays part of one consistent design family.

**Key features**

* Visual drag & drop builder on a responsive grid
* 50+ content tiles (hero, slider, gallery, cards, pricing, timeline, accordion, charts, maps, forms, mega menu…)
* Global design tokens: colors, self-hosted web fonts, spacing scale, border & radius system
* Reusable headers, footers and global widgets with display conditions
* Entrance/scroll animations, hover effects and parallax
* Built-in stock media search (optional, see *External services* below)
* Newsletter manager, contact form handler and SEO tools

All processing happens on your own site. Olobuild does not phone home and does not collect
any usage data or analytics about you.

== External services ==

Olobuild can connect to a number of **optional** third-party services. None of them is
contacted unless you explicitly enable the related feature or enter the corresponding API
key. The following list documents what is sent, when, and to whom.

**Stock media search (WordPress admin only, when you open the Media Search panel or a tile media picker, and only if you configured the provider's API key):**

* Unsplash — photo search. Sends your search terms. Terms: https://unsplash.com/api-terms — Privacy: https://unsplash.com/privacy
* Pexels — photo/video search. Sends your search terms. Terms & Privacy: https://www.pexels.com/api/ , https://www.pexels.com/privacy-policy/
* Pixabay — photo search. Sends your search terms. Terms & Privacy: https://pixabay.com/service/terms/ , https://pixabay.com/service/privacy/
* Openverse — openly-licensed media search. Sends your search terms. Terms & Privacy: https://openverse.org/terms , https://openverse.org/privacy
* Freesound — audio search (only if you enter a Freesound API key). Sends your search terms. Terms: https://freesound.org/help/tos_web/

**AI assistant (WordPress admin only, only if you enter the provider's API key):**

* OpenAI — AI image generation. Sends your prompt. Terms: https://openai.com/policies/terms-of-use — Privacy: https://openai.com/policies/privacy-policy
* Anthropic — AI layout suggestions and SEO alt-text. Sends your prompt and, for alt-text, the selected image. Terms: https://www.anthropic.com/legal/consumer-terms — Privacy: https://www.anthropic.com/legal/privacy
* LottieFiles — animation search. Sends your search terms. Terms: https://lottiefiles.com/terms — Privacy: https://lottiefiles.com/privacy

**Front-end services (only on pages where you use the related tile/feature):**

* Google Fonts — when you select a Google Font, the **server** downloads the font files once and self-hosts them under /wp-content/uploads/olo-fonts/. Public-site visitors are served the local copies and never contact Google. (Inside the admin editor, the live font preview may load the selected font from Google Fonts.) Terms & Privacy: https://policies.google.com/terms , https://policies.google.com/privacy
* Google reCAPTCHA — spam protection, only if you enable reCAPTCHA on a form. Loads Google's script and sends visitor interaction data. Terms & Privacy: https://policies.google.com/terms , https://policies.google.com/privacy
* YouTube / Vimeo — used by the Video tile to fetch thumbnails/oEmbed data for the video you embed. Privacy: https://policies.google.com/privacy , https://vimeo.com/privacy
* Facebook, Instagram, X (Twitter) — the matching social tiles load the official embed SDK from the network to render the embedded content. Privacy: https://www.facebook.com/privacy/policy , https://help.instagram.com/519522125107875 , https://x.com/privacy
* OpenStreetMap / CARTO / Esri (ArcGIS) / OpenTopoMap / OpenStreetMap France (HOT) — the Map tiles load raster map tiles from the basemap provider you select, and may geocode addresses via OpenStreetMap Nominatim. Privacy: https://wiki.osmfoundation.org/wiki/Privacy_Policy , https://carto.com/privacy/ , https://www.esri.com/en-us/legal/privacy , https://opentopomap.org/about
* Web analytics & tag managers — only if you enter the corresponding ID in Olobuild → Analytics, the matching tag is loaded on the front-end (consent-gated): Google Analytics 4 / Google Tag Manager, Facebook (Meta) Pixel, Microsoft Clarity, Hotjar. They load the provider's script and send visitor analytics data. Privacy: https://policies.google.com/privacy , https://www.facebook.com/privacy/policy , https://privacy.microsoft.com/privacystatement , https://www.hotjar.com/legal/policies/privacy/

**Marketing & SEO integrations (only if you configure them):**

* Mailchimp, HubSpot, ActiveCampaign, ConvertKit, Brevo — form submissions are forwarded to the list/CRM you configure. Data sent is the form data submitted by the visitor.
* Bing IndexNow — if enabled, notifies search engines of new/updated URLs. Sends the URL of the changed content. More info: https://www.indexnow.org/

API keys for stock media providers can also be defined as constants in wp-config.php:
`OLO_UNSPLASH_API_KEY`, `OLO_PEXELS_API_KEY`, `OLO_PIXABAY_API_KEY`.

== Third-party libraries ==

Olobuild bundles the following open-source libraries (all GPL-compatible), shipped locally
under assets/vendor/ — no library is loaded from a CDN:

* UIkit 3 (MIT)
* Leaflet + Leaflet.markercluster (BSD-2-Clause / MIT)
* Chart.js (MIT)
* lottie-web (MIT)
* html2canvas (MIT)
* PDF.js (Apache-2.0)
* Pannellum (MIT)
* StPageFlip (MIT)

== Installation ==

1. Upload the `olobuild` folder to `/wp-content/plugins/`, or install the ZIP from Plugins → Add New → Upload.
2. Activate the plugin through the *Plugins* menu in WordPress.
3. Open *Olobuild* in the admin menu to run the setup wizard and start building.

== Frequently Asked Questions ==

= Does Olobuild send my data anywhere? =
No. Olobuild does not track you and does not phone home. It only contacts the optional
third-party services listed under *External services*, and only when you enable the
related feature or enter an API key.

= Are Google Fonts GDPR-friendly? =
Yes. Google Fonts are self-hosted: the font files are downloaded once by your server and
then served from your own site, so visitors never connect to Google's servers.

= Can I use my own stock media API keys? =
Yes. Enter them in Olobuild settings, or define them as constants in wp-config.php
(`OLO_UNSPLASH_API_KEY`, `OLO_PEXELS_API_KEY`, `OLO_PIXABAY_API_KEY`).

== Changelog ==

= 1.4.172 =
* Security/compliance: output-escaping hardening campaign across all PHP renderers (246 files). User-supplied values are escaped or whitelisted at the point of output (esc_html/esc_attr/esc_url/int casts); internally-built CSS/JS blocks are documented with specific phpcs annotations after per-variable upstream-sanitization review.
* Security: closed raw-value CSS injection vectors in nav, subnav, textmask, blendtext, floatingpanel, headline, button and the Woo gallery slider (values now pass through the color/slug whitelists; valid values render byte-identical).
* Security: analytics tracking IDs (GA4/GTM/Meta Pixel/Clarity) are now restricted to a strict alphanumeric charset before being printed in the tag snippets.

= 1.4.103 =
* Set "Requires at least" to WordPress 5.9 (str_contains / str_starts_with / str_ends_with are provided by core since 5.9).

= 1.4.102 =
* Compliance: the admin builder now loads Leaflet, lottie-web and PDF.js from the local assets/vendor/ folder instead of unpkg/cdnjs — no library is loaded from a CDN, front-end or admin.
* Disclosure: documented all front-end web-analytics tags (GA4/GTM, Meta Pixel, Microsoft Clarity, Hotjar) and the additional map providers (Esri/ArcGIS, OpenTopoMap, OSM France, Nominatim) under External services.
* i18n: fixed 7 strings using the wrong text domain ('olobuilder' → 'olobuild').

= 1.4.101 =
* Compliance: all bundled JavaScript/CSS libraries (Chart.js, lottie-web, Leaflet) now load locally from assets/vendor/ instead of a CDN.
* Privacy: Google Fonts are now self-hosted (downloaded once, served from /uploads); no visitor request to Google. Removed Google Fonts preconnect/dns-prefetch hints.
* Security: removed all hard-coded stock media API keys; keys must be configured by the user (settings or wp-config constants).
* Security: saving custom head/body/footer code now requires the `unfiltered_html` capability.
* Added readme.txt, LICENSE (GPLv2) and License headers.

== Upgrade Notice ==

= 1.4.101 =
After upgrading, re-enter your Unsplash/Pexels/Pixabay API keys (or define them in wp-config.php) to keep stock media search working.
