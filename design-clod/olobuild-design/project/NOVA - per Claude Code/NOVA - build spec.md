# NOVA (creative studio) → OLObuild JSON — BUILD SPEC + NEW TILES

> Task for Claude Code: generate **importable OLObuild template JSON** for the
> **NOVA** creative-studio one-pager (immersive, parallax, expressive). You have the
> `claudiovinco/olobuild` repo — derive the JSON format from the code; this file
> gives design intent + content only. Blueprint: `NOVA - Creative studio.html`.
>
> IMPORTANT: this page intentionally pushes the expressive tiles. Most of it maps to
> EXISTING tiles. A short list of **NEW tiles / style options** is at the end —
> build the JSON with existing tiles where possible and flag the rest.

---

## READ FIRST (don't invent the format)
1. `includes/class-rest-api.php` → `templates/:id/export` + `templates/import` = the
   exact JSON **envelope**.
2. `includes/class-database.php` → `wp_olo_templates` schema.
3. `src/config/elementRegistry.js` + each `src/config/elements/<type>.js` →
   `default.defaults` = valid `settings.*` keys. Only emit keys that exist there.
   Pay attention to **`hero.js` `styleFields.preset`** (it already includes
   `gradient-aurora`, `neon-cyberpunk`, `glass-overlay`, `tilt-parallax`).
4. `includes/class-frontend-renderer.php` → node shape + UID scheme.
5. The shared `_shared.js` → `textEffectsFields/textEffectsDefaults` (Text FX:
   scramble/gradient/typewriter…) and `withHover` (hover transforms / border-radius).

**Safety step:** export one real template from the target site first; reuse its
top-level envelope.
**Node shape:** `{ "type", "id":"olo-<type>-xxxx", "settings":{…}, "children":[…] }`.
Containers (`section`,`row`,`column`,`grid`) hold `children`. Confirm every slug.

## GLOBAL
**Theme = dark, editorial, oversized.** Global colors (roles):
- text/ink **`#f1ece2`** (bone, used as "primary text"), background **`#0b0a0d`**
- accent **`#ff5436`** (vermilion), accent-2 **`#7c6cff`** (indigo), accent-3
  **`#ffd23f`** (amber — gradient/mesh only)
- lines `rgba(241,236,226,.13)`
Fonts: display **Syne** (800), body **Hanken Grotesk**, mono **Space Mono** (labels).
Type is huge (hero up to ~220px). Buttons pill-shaped. A faint **grain overlay** sits
over the whole page (see NEW TILES). Header is `mix-blend-mode:difference` over the
hero. Images = `image` tiles, empty `src`, `alt` = the caption text.

## GUARDRAILS
No hardcoded hex in tiles (use color roles/tokens). Only emit keys present in a
tile's `defaults`. Never rename saved keys. Text FX & hover transforms via the shared
helpers, not ad-hoc CSS. AA-ish contrast on body text.

---

## PAGE TREE (`type: page`, title "NOVA — Creative studio")

1. **navmenu** (confirm slug) — logo "✦ NOVA"; links Work / Studio / Services;
   CTA "Let's talk". Set its blend mode to *difference* (style option — see NEW TILES
   note 4).

2. **hero** (`hero.js`) — **preset `gradient-aurora`** (covers the mesh look) +
   **Parallax scroll-linked** enabled on the background layers, and **cursor-tracking
   / mouse parallax** on. Content:
   - eyebrow row: "✦ Creative studio · Est. 2014 · Milan / Remote"
   - title (uppercase, Syne): three lines "We make" / "brands" / **MOVE**, where the
     last word uses **Text FX = scramble** cycling `MOVE, SHINE, SPEAK, SELL, GLOW`
     (text_effect on the title word, accent-2 color).
   - subtitle: "NOVA is an independent studio crafting brand identities, motion and
     digital experiences that refuse to sit still."
   - a giant transparent **outline ghost word "NOVA"** behind, parallax — see NEW
     TILES note 4 (outline-text style on a Headline) — optional decorative layer.
   - scroll cue.

3. **marquee** (confirm slug) — oversized track, mixed solid + outline words:
   "Branding ✦ Motion ✦ Web ✦ 3D / CGI ✦ Art Direction ✦" (some words outline-style).

4. **blendtext** (`blendtext.js`) — full-bleed background image (`alt` "studio reel —
   abstract motion still, full-bleed"), giant mix-blend heading "DESIGN IN MOTION",
   sub: "We believe a brand isn't a logo frozen on a page — it's a system that
   breathes, reacts and moves. Every project we ship is built to live on screens, not
   just in a guidelines PDF."

5. **progallery** (confirm slug) — **scattered layout** + **Mouse-Tilt 3D** hover on
   each item. Head: "Selected work" + ghost button "All projects ↗". 4 projects
   (image alt / title / year / category / tag):
   - "Helios — brand identity system" · Helios · 2025 · Identity · Naming · Guidelines · tag Branding
   - "Pulse — title sequence still" · Pulse · 2025 · Title design · Motion system · tag Motion
   - "Atlas — site art direction" · Atlas · 2024 · Art direction · Web build · tag Web
   - "Forma — 3D product render" · Forma · 2024 · CGI · Product films · tag 3D / CGI

6. **imgcompare** (`imgcompare` — confirm slug) — eyebrow "Rebrand case · Helios",
   heading "From brief to brand". Before: alt "BEFORE — dated logo, greyscale";
   After: alt "AFTER — NOVA rebrand, full system". Labels Before / After, draggable.

7. **section** "How we work" — **sticky-pinned** two-column: left column pinned while
   right scrolls (see NEW TILES note 2). Left: eyebrow "The method", h2 "How we work",
   lede "Four phases, zero surprises. We move fast but we never skip the thinking."
   Right: 4 steps —
   - 01 Discover · "Listen & dig" · "Workshops, audits and a lot of questions. We map
     where the brand is and where it needs to go."
   - 02 Concept · "Find the idea" · "One sharp territory beats ten safe ones. We
     pressure-test a single bold direction."
   - 03 Craft · "Design & move" · "Identity, motion system and components — designed
     together so everything moves as one."
   - 04 Launch · "Ship & support" · "Guidelines, files and a launch toolkit. We stay
     on call for the first run."

8. **counter** ×4 (`counter.js`): 180+ projects shipped · 24 awards & mentions ·
   60 clients worldwide · 11 years in motion.

9. **desclist** / list (`desclist.js`) — editorial hover list "What we do", rows with
   number + title + tags, accent slide-in on hover (hover transform):
   01 Brand Identity (Naming · Logo · Systems) · 02 Motion Design (Titles · Loops ·
   Reels) · 03 Web & Interactive (Sites · Prototypes · Dev) · 04 Art Direction
   (Campaigns · Shoots) · 05 3D & CGI (Product · Worlds · Film).

10. **cta-banner** + **footer** — huge "LET'S TALK" (mailto hello@novastudio.cc),
    accent mail button, bottom bar: "© 2026 NOVA Studio — Built with OLObuild" +
    Instagram / Behance / LinkedIn + "Milan / Remote".

---

## ✅ COVERED BY EXISTING TILES (no new work)
- **Parallax (scroll-linked, multi-property)** and **Mouse-Tilt 3D / cursor tracking**
  — native, apply to hero layers, gallery cards, blend media.
- **Hero `gradient-aurora` preset** — covers the animated mesh look in the hero.
- **Text FX (scramble / gradient / typewriter)** — native, used on the hero word.
- **BlendText**, **ImgCompare**, **ProGallery (scattered)**, **Marquee**, **Counter**,
  **DescList** — all native.
- Hover transforms (service list slide, button dot, card scale) — native `withHover`.

## 🆕 NEW TILES / STYLE OPTIONS REQUIRED
Build these (or confirm an equivalent exists) — listed by priority:

1. **PinStack — sticky-pinned section/column**  *(priority: HIGH)*
   A layout where one column stays pinned (`position: sticky; top: …`) while sibling
   content scrolls past (the "How we work" section). Cleanest as a **`sticky` toggle +
   `sticky_offset` field on the Column tile** (`column.js`), rather than a whole new
   tile. If Column can't pin, add a small `pinstack` container tile.

2. **MeshBackdrop / Aurora background as a reusable Section background**
   *(priority: MEDIUM)*
   The hero is covered by Hero's `gradient-aurora` preset, but to reuse the animated
   multi-blob blurred gradient on **any** Section (and the footer), add a **Section
   background type = "aurora/mesh"** with 2–3 color stops (mapped to color roles),
   blur, and optional slow drift animation. Implement as a `styleFieldsBase`
   background option, not a separate tile.

3. **Grain / Noise overlay** *(priority: LOW)*
   A site-wide fine-grain texture over everything (`mix-blend: overlay`, low opacity).
   Best as a **global site setting / theme option** (or a tiny always-on-top overlay
   tile), with an opacity control.

4. **Minor style-field additions to existing tiles** *(priority: LOW)*
   - **Outline text** on Headline/BlendText: a style option `fill: none` +
     `text-stroke` (color + width) — for the giant ghost "NOVA" and outline marquee
     words.
   - **Blend mode** option on the Navmenu/any tile wrapper (`mix-blend-mode:
     difference`) so the nav inverts over light/dark hero.
   These are field additions, NOT new tiles.

---

## IMPORT
Generate the `.json`, validate the envelope against a real exported template, import
via *Gestione Template → Importa* (or `POST /olobuild/v1/templates/import`). Relink
images. For anything in "NEW TILES", either implement the field/option first or
substitute the nearest existing tile and leave a TODO note in your output.
