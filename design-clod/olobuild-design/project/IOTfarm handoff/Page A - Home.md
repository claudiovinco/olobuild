# IOTfarm → OLObuild JSON — PAGE A: HOME

> Task for Claude Code: generate **importable OLObuild template JSON** for the
> IOTfarm **Home** page. You have the `claudiovinco/olobuild` repo — derive the JSON
> format from the code; this file gives design intent + content only.
> Blueprint reference: `IOTfarm - Home.html` in the design project.

---

## READ FIRST (don't invent the format)
1. `includes/class-rest-api.php` → `templates/:id/export` + `templates/import`
   handlers = the exact JSON **envelope** to match.
2. `includes/class-database.php` → `wp_olo_templates` schema (how the element tree
   is stored).
3. `src/config/elementRegistry.js` + each `src/config/elements/<type>.js` →
   `default.defaults` is the **complete list of valid `settings.*` keys** for a tile.
   Only emit keys that exist there.
4. `includes/class-frontend-renderer.php` → node shape + per-instance UID scheme.

**Safety step:** export one real template from the target site first and reuse its
top-level structure as the canonical envelope.

**Node shape** (confirm against renderer):
```jsonc
{ "type": "hero", "id": "olo-hero-ab12cd",
  "settings": { /* only overrides from defaults */ },
  "children": [ /* nested nodes for containers */ ] }
```
Containers (`section`,`row`,`column`,`grid`) hold `children`; leaf tiles don't.
Confirm every `type` slug exists in `src/config/elements/`.

## GLOBAL (shared by all pages — set once)
Global colors (GlobalColorsPanel roles): **primary `#2563eb`**, **accent `#22d3ee`**,
secondary `#0a1020`, dark `#0f172a`, light `#f5f8fc`, text `#182338`; on-primary white.
Fonts: display **Space Grotesk**, body **IBM Plex Sans**, mono **IBM Plex Mono**.
Header & Footer = **global templates assigned site-wide**, not per page (see Page-A
header/footer content below; reuse on every page).
Media = `image` tiles with empty `src` + `alt` = the caption (user relinks via Media
Search later). Spacing/radius from SPACE/RADIUS scale; one radius language.

## GUARDRAILS
No hardcoded hex in tiles (use color roles/tokens). Only emit keys present in a
tile's `defaults`. Never rename/restructure saved keys (`margin_*`, `padding_*`,
`border_radius`, `hover.*`…). Icons from the SVG set, no emoji. AA contrast, ≥44px hits.

---

## HEADER (global template — type `navmenu`/`megamenu`, confirm slug)
Logo "IOTfarm" (IOT bold + "farm" muted) + generated mark. Links: Home / Solutions /
About / Contact. CTAs: "Sign in" (ghost) + "Request a demo" (primary). Transparent
over dark hero, solid white on scroll.

## FOOTER (global template — type `footer`)
Brand blurb: "Connected hardware and a cloud platform that bring the whole field into
one live view." Status chip "All systems operational". Columns —
**Product**: Sensors, Gateways, Platform, Pricing · **Company**: About, Careers,
Contact, Docs · **Get in touch**: Request a demo, Support, hello@iotfarm.io.
Bottom bar: "© 2026 IOTfarm S.r.l. — Built with OLObuild" + LinkedIn/X/GitHub icons.

---

## HOME PAGE TREE (`type: page`, title "IOTfarm — Home")

1. **hero** (`hero.js`) — preset `split-image` or `tilt-parallax`, dark bg (secondary).
   - `title`: "One live view of every sensor in your operation."
   - For the word-swap, use an **animatedheading** tile (`animatedheading.js`,
     words: sensor, gateway, machine, field, acre) or keep the title static.
   - `subtitle`: "IOTfarm builds the rugged devices and the cloud that bring soil,
     water, climate and machinery data together — installed in minutes, monitored
     from anywhere."
   - `cta_text` "Request a demo" → Contact; `cta2_text` "Explore solutions" → Solutions.
   - Side image tile, alt: "live fleet dashboard — device map & telemetry".

2. **counter** row (or hero stat strip), 4 items:
   250,000+ devices online · 99.98% platform uptime · 38 countries · 6-yr battery.

3. **marquee** (confirm slug) — lead "Trusted by growers, co-ops and field operators";
   items: GreenField Co-op, AgriNord, TerraSense, HydroValley, BioCampo, NorthAcre.
   Pause on hover, edge gradient mask.

4. **grid** → 3× **iconbox** (`iconbox.js`).
   Section head: eyebrow "A full stack, soil to dashboard", h2 "Everything you need to
   put the field online", lede "Three layers that work as one — no integrators, no
   glue code, no surprise license fees."
   - **Rugged sensors** — "Soil moisture, weather, water level and tank probes built
     to IP67 and a six-year battery. Install with one bolt and a QR scan." link → Solutions.
   - **Long-range connectivity** — "LoRaWAN gateways reach 15 km line-of-sight and
     fall back to 4G. Devices keep logging offline and sync the moment they reconnect."
   - **Cloud platform & API** — "Live maps, thresholds, automations and a clean REST
     API. Export to your ERP or trigger a valve — same dashboard, same login."

5. **section** (light bg) → two **rows** (image + content; 2nd reversed):
   - Eyebrow "The platform", h2 **"See the whole field in real time"** — body "Every
     device lands on one map the second it powers up. Set thresholds in plain language
     — 'alert me when zone 4 drops below 28%' — and IOTfarm watches it around the
     clock." Image alt "web dashboard — field map with live readings". Chips: live map ·
     custom thresholds · push + SMS alerts. List: 1-second telemetry on demand /
     automations that open valves, start pumps or notify a crew / role-based access for
     owners, agronomists and field crews. CTA "Tour the platform".
   - Eyebrow "The hardware", h2 **"Built to survive the whole season"** (reversed) —
     body "Our probes are sealed, solar-assisted and field-serviceable. No gateways in
     range? They cache readings locally and back-fill the timeline as soon as a signal
     returns." Image alt "close-up — weatherproof soil probe in soil". Chips: IP67 ·
     −20°C…60°C · 6-yr battery · LoRaWAN 15km. List: one-bolt mount, live in <12 min /
     OTA firmware across the fleet at once / offline-first logging with auto back-fill.
     CTA "Compare devices".

6. **counter** band (light bg, 4× `counter.js`, gradient numbers primary→accent):
   250,000+ devices reporting live · 4.2B data points per day · 99.98% platform uptime
   (12 mo) · 12 min average install time.

7. **testimonial** (confirm slug), dark bg:
   - quote: "We cut irrigation water by **31%** in one season and never lost a night of
     sleep wondering if a pump failed." (highlight "31%" in accent)
   - author: Marta Realini — Operations lead, GreenField Co-op · 1,400 ha.
   - 3 side stat cards: −31% water used vs previous season · 3 wks to roll out across
     1,400 ha · 0 missed pump failures since install.

8. **pricing** (confirm slug), monthly/annual toggle (annual −20%):
   - **Field** (Starter) €6/device·mo (annual €5) — "For a single site getting its
     first devices online." Up to 50 devices · live map & 90-day history · email & push
     alerts · community support. CTA ghost "Start with Field".
   - **Pro** (featured, dark card) €11/device·mo (annual €9) — "For multi-site
     operations that run on automations." Unlimited devices & sites · automations &
     valve control · full REST API & webhooks · unlimited history & exports · priority
     support. CTA accent "Choose Pro".
   - **Estate** (Enterprise) Custom — "For co-ops & agribusiness with custom needs."
     Everything in Pro · on-site survey & install · SLA & dedicated engineer · ERP &
     data-warehouse sync. CTA ghost "Talk to sales".

9. **cta-banner** (`cta-banner.js`, primary→ink gradient): h2 "Put your first field
   online this week", body "Book a 20-minute demo and we'll map a starter kit to your
   site — sensors, gateway and dashboard included." CTAs "Request a demo" + "See
   solutions".

## IMPORT
Generate the `.json`, validate the envelope against a real exported template, then
import via *Gestione Template → Importa* (or `POST /olobuild/v1/templates/import`).
Set Home as the site front page; assign Header/Footer site-wide; relink images.
