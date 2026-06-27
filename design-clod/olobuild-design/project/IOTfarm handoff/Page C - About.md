# IOTfarm → OLObuild JSON — PAGE C: ABOUT

> Task for Claude Code: generate **importable OLObuild template JSON** for the
> IOTfarm **About** page. You have the `claudiovinco/olobuild` repo — derive the JSON
> format from the code; this file gives design intent + content only.
> Blueprint reference: `IOTfarm - About.html`.

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

## ABOUT PAGE TREE (`type: page`, title "IOTfarm — About")

1. **Page title bar** (dark; breadcrumbs + headline):
   - crumbs: Home / About
   - h1: "We put farmers, not dashboards, first"
   - sub: "IOTfarm started in a vineyard with one frozen pump and a question: why is
     the field the last place to get good data?"

2. **blendtext** (`blendtext.js`) on **primary** bg (`#2563eb`):
   - big text: "Hardware that earns its keep." (use mix-blend per the tile)
   - sub: "Every device we ship has to pay for itself in a single season — in water
     saved, frost avoided or a failure caught at 3 a.m. If it doesn't, it doesn't ship."

3. **section** → row (image + content):
   - eyebrow "Our story", h2 **"From one vineyard to 38 countries"**
   - para 1: "In 2019 our founders lost a night's harvest to a pump that failed in
     silence. The off-the-shelf 'smart farm' gear was either too fragile, too closed,
     or needed an engineer to install."
   - para 2: "So they built their own: rugged, open, and simple enough that the person
     who works the field can install it. Today IOTfarm runs on a quarter of a million
     devices across vineyards, orchards, greenhouses and water utilities."
   - img alt "founders in a field with a prototype node".

4. **counter** band (4× `counter.js`):
   Founded 2019 · 64 people on the team · 38 countries deployed · 100% solar-assisted
   nodes.

5. **EventList** timeline (`eventlist` — confirm slug; else build with a row+column
   list pattern):
   - **2019 — The frozen pump**: "Founded after a lost harvest. First prototype node
     hand-built and buried in a Piedmont vineyard."
   - **2021 — Platform v1 & LoRaWAN**: "Launched the cloud platform and our own
     long-range gateways. First 1,000 devices online."
   - **2023 — Automations & open API**: "Valve control, webhooks and a public REST API.
     Crossed 50,000 active devices."
   - **2026 — A quarter-million nodes**: "250,000+ devices across 38 countries, 99.98%
     platform uptime, and a six-year battery in every node."

6. **authorbox** team grid (light bg, 4× `authorbox.js`):
   - Section head: eyebrow "The people", h2 "Engineers who get muddy boots".
   - **Luca Ferrara** — Co-founder · CEO — "Agronomist turned builder. Still installs
     nodes on weekends." (img alt "portrait")
   - **Sara Bianchi** — Co-founder · CTO — "Leads hardware. Obsessed with battery life
     and IP ratings." (img alt "portrait")
   - **Tomás Riva** — Head of Platform — "Keeps the cloud fast and the API honest and
     open." (img alt "portrait")
   - **Nadia El-Amin** — Head of Field Success — "Runs rollouts. Has installed nodes in
     19 countries." (img alt "portrait")

7. **marquee** values — lead "What we won't compromise on"; items: Open data ·
   Rugged by default · Installable by anyone · No lock-in · Pays for itself.

8. **cta-banner** (`cta-banner.js`, primary→ink gradient): h2 "Want to build the
   connected field with us?", body "We're hiring across hardware, platform and field
   success — and we always want to hear from growers." CTAs "Get in touch" + "See what
   we build".

## IMPORT
Generate the `.json`, validate envelope vs a real exported template, import via
*Gestione Template → Importa* (or `POST /olobuild/v1/templates/import`). Relink images.
