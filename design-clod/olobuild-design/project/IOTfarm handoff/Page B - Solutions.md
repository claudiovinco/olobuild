# IOTfarm → OLObuild JSON — PAGE B: SOLUTIONS

> Task for Claude Code: generate **importable OLObuild template JSON** for the
> IOTfarm **Solutions** page. You have the `claudiovinco/olobuild` repo — derive the
> JSON format from the code; this file gives design intent + content only.
> Blueprint reference: `IOTfarm - Solutions.html`.

---

## READ FIRST (don't invent the format)
1. `includes/class-rest-api.php` → `templates/:id/export` + `templates/import` = the
   exact JSON **envelope**.
2. `includes/class-database.php` → `wp_olo_templates` schema.
3. `src/config/elementRegistry.js` + each `src/config/elements/<type>.js` →
   `default.defaults` = the valid `settings.*` keys. Only emit keys that exist there.
4. `includes/class-frontend-renderer.php` → node shape + UID scheme.

**Safety step:** export one real template from the target site first; reuse its
top-level structure as the envelope.

**Node shape:** `{ "type", "id":"olo-<type>-xxxx", "settings":{…overrides…},
"children":[…] }`. Containers (`section`,`row`,`column`,`grid`) hold `children`.
Confirm every `type` slug exists in `src/config/elements/`.

## GLOBAL (same on every page)
Colors: primary `#2563eb`, accent `#22d3ee`, secondary `#0a1020`, dark `#0f172a`,
light `#f5f8fc`, text `#182338`, on-primary white. Fonts: Space Grotesk (display),
IBM Plex Sans (body), IBM Plex Mono (mono/labels). Header & Footer = global templates
assigned site-wide (content in Page A file). Images = `image` tiles with empty `src`
+ `alt` = caption.

## GUARDRAILS
No hardcoded hex (use color roles). Only keys present in a tile's `defaults`. Never
rename saved keys. Icons from SVG set, no emoji. AA contrast, ≥44px hits. Spacing/
radius from the scale; one radius language.

---

## SOLUTIONS PAGE TREE (`type: page`, title "IOTfarm — Solutions")

1. **Page title bar** (dark; `breadcrumbs.js` + headline):
   - crumbs: Home / Solutions
   - h1: "One stack, four kinds of operation"
   - sub: "The same rugged hardware and cloud platform adapt to how you actually work
     — pick a starting point or mix them on one map."

2. **grid** 2×2 of feature cards (use `iconbox.js`, or `flipcard.js` if you want a
   back face). Each card: an image, a mono tag, h3, body, and chips.
   - tag "Smart irrigation" — h3 **"Water exactly where it's needed"** — "Soil-moisture
     probes drive your valves automatically, zone by zone, so you stop over- and
     under-watering." chips: soil probes · valve control · −30% water. img alt "drip
     irrigation lines in a field".
   - tag "Climate & weather" — h3 **"Forecast frost before it bites"** — "On-site
     weather stations track temperature, humidity, wind and rainfall, with frost and
     heat alerts pushed in real time." chips: weather station · frost alerts · rain log.
     img alt "weather station mast at dawn".
   - tag "Water & environment" — h3 **"Watch water levels & quality"** — "Tank, well
     and waterway sensors report level, flow and quality — with thresholds that warn
     you before a shortage or spill." chips: level & flow · quality probe · spill alert.
     img alt "river / canal water-level sensor".
   - tag "Machinery & assets" — h3 **"Track machines and tanks"** — "GPS and run-hour
     trackers tell you where equipment is, how hard it's working and when fuel or
     service is due." chips: GPS · run-hours · geofence. img alt "tractor / machinery
     in the field".

3. **hotspot** (`hotspot.js`), light bg:
   - Section head: eyebrow "Anatomy of a node", h2 "Every IOTfarm device, inside out",
     lede "Hover the points to explore what makes a node survive years in the field."
   - Image alt "product render — IOTfarm sensor node, exploded view".
   - 4 points (title / mono detail), positioned over the image:
     - "Solar + 6-yr battery" / "3 W panel · LiSOCl₂ backup"  (≈ left 24%, top 30%)
     - "LoRaWAN radio" / "15 km LOS · 4G fallback"            (≈ left 62%, top 22%)
     - "IP67 sealed shell" / "−20°C…60°C rated"               (≈ left 46%, top 62%)
     - "Swappable probe bay" / "soil · water · climate"       (≈ left 78%, top 70%)

4. **section** → row (image + content):
   - eyebrow "Open by design", h2 **"Your data, wherever you need it"** — "IOTfarm is
     open from day one. Pull readings into your ERP, push events to Slack, or build
     your own dashboards on our REST API and webhooks — no extra license."
   - img alt "REST API / integrations diagram". Chips: REST API · webhooks · CSV export
     · ERP sync.
   - list: documented REST API with API keys per project / webhooks for thresholds,
     automations and device health / scheduled CSV & Parquet exports to your warehouse.
   - CTA "Read the API docs".

5. **accordion** (`accordion.js`), light bg — "Technical questions, answered"
   (first item open):
   - **Do I need a SIM card or internet at the field?** → "No. Devices talk LoRaWAN to
     a gateway up to 15 km away, and the gateway uplinks over Ethernet, Wi-Fi or 4G. If
     you have one connection point anywhere on site, you're covered."
   - **What happens when a device loses signal?** → "Every node keeps logging to local
     memory and back-fills the full timeline automatically the moment it reconnects —
     so you never lose a reading, even after days offline."
   - **How long does installation actually take?** → "Most nodes are live in under 12
     minutes: one bolt to mount, scan the QR code to onboard, choose the probe. No
     tools beyond a wrench, no laptop required."
   - **Can I control valves and pumps from the platform?** → "Yes, on the Pro plan.
     Pair a controller node with your valve or pump and build automations — for example,
     open zone 4 when soil moisture drops below 28% between 6 and 9 a.m."
   - **Is my data locked into IOTfarm?** → "Never. Your full history is exportable as
     CSV or Parquet at any time, and the REST API gives you programmatic access to
     everything you see in the dashboard."

6. **cta-banner** (`cta-banner.js`, primary→ink gradient): h2 "Not sure which setup
   fits?", body "Tell us about your site and we'll design a starter kit and a rollout
   plan — no commitment." CTAs "Request a demo" + "Meet the team".

## IMPORT
Generate the `.json`, validate envelope vs a real exported template, import via
*Gestione Template → Importa* (or `POST /olobuild/v1/templates/import`). Relink images.
