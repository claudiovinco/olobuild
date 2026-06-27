# IOTfarm → OLObuild JSON — PAGE D: CONTACT

> Task for Claude Code: generate **importable OLObuild template JSON** for the
> IOTfarm **Contact** page. You have the `claudiovinco/olobuild` repo — derive the
> JSON format from the code; this file gives design intent + content only.
> Blueprint reference: `IOTfarm - Contact.html`.

---

## READ FIRST (don't invent the format)
1. `includes/class-rest-api.php` → `templates/:id/export` + `templates/import` = the
   exact JSON **envelope**.
2. `includes/class-database.php` → `wp_olo_templates` schema.
3. `src/config/elementRegistry.js` + each `src/config/elements/<type>.js` →
   `default.defaults` = the valid `settings.*` keys. Only emit keys that exist there.
4. `includes/class-frontend-renderer.php` → node shape + UID scheme.
5. **`includes/class-form-handler.php`** + `src/config/elements/form.js` → the form
   field schema, recipient/mailer config, and how form submissions are stored.

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

## CONTACT PAGE TREE (`type: page`, title "IOTfarm — Contact")

1. **Page title bar** (dark; breadcrumbs + headline):
   - crumbs: Home / Contact
   - h1: "Let's map a kit to your field"
   - sub: "Tell us about your operation. We'll come back within one working day with a
     starter kit and a rollout plan — no commitment."

2. **section** → row, two columns (~1fr / 0.95fr):

   **Left column — `form` tile** (`form.js`), optional multi-step (3 steps).
   Fields (label / type / required):
   - First name — text — required
   - Last name — text — required
   - Work email — email — required
   - Phone — tel
   - Operation type — select: Vineyard / orchard · Open field crops · Greenhouse ·
     Water utility / environment · Livestock · Other
   - Approx. size (hectares) — select: Under 50 ha · 50 – 200 ha · 200 – 1,000 ha ·
     1,000+ ha
   - "What do you want to monitor?" — textarea — placeholder "e.g. soil moisture across
     6 irrigation zones, plus frost alerts in the orchard…"
   - Consent checkbox: "I agree to be contacted about my request and accept the privacy
     policy."
   - Submit button: "Request a demo".
   Configure the recipient/mailer in the Form handler; store submissions per
   `class-form-submissions.php`.

   **Right column — info cards** (use `iconbox.js`/`iconlist.js`):
   - **Talk to a human**: Sales hello@iotfarm.io · Support support@iotfarm.io ·
     Phone +39 011 555 0142.
   - **Visit us**: HQ Via dei Sensori 12, 10138 Torino, Italy · Lab open Mon–Fri 9–18.
   - **Map**: image tile, alt "interactive map — Torino HQ" (or a real map embed tile
     if one exists in the registry).
   - Reassurance card (primary-soft bg): chip "Avg. reply: 4 hours" + "We answer every
     demo request within one working day."

3. **accordion** (`accordion.js`), light bg — "Quick answers" (first item open):
   - **What does a demo actually involve?** → "A 20-minute call where we look at your
     site on a map, talk through what you want to monitor, and show you the live
     platform with real device data. You leave with a kit proposal and a quote."
   - **Is there a minimum order?** → "No. You can start with a single node and one
     gateway, then scale at your pace. The platform price is per active device, billed
     monthly or annually."
   - **Do you ship outside Italy?** → "Yes — we ship to 38 countries and tune the
     LoRaWAN radios to your region's frequency plan before they leave the lab."

## IMPORT
Generate the `.json`, validate envelope vs a real exported template, import via
*Gestione Template → Importa* (or `POST /olobuild/v1/templates/import`). After import:
wire the form recipient/mailer and test a submission; relink the map image.
