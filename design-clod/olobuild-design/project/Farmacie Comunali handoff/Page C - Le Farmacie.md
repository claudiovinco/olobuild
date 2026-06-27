# Farmacie Comunali Borgoverde → OLObuild JSON — PAGE C: LE FARMACIE

> Task for Claude Code: generate **importable OLObuild template JSON** for the
> **Le Farmacie** (locations + hours + on-call shifts) page. You have the
> `claudiovinco/olobuild` repo — derive the JSON format from the code; this file
> gives design intent + content only.
> Blueprint reference: `Farmacie Comunali - Le Farmacie.html`.

---

## READ FIRST (don't invent the format)
1. `includes/class-rest-api.php` → `templates/:id/export` + `templates/import` = the
   exact JSON **envelope**.
2. `includes/class-database.php` → `wp_olo_templates` schema.
3. `src/config/elementRegistry.js` + each `src/config/elements/<type>.js` →
   `default.defaults` = valid `settings.*` keys. Only emit keys that exist there.
4. `includes/class-frontend-renderer.php` → node shape + UID scheme.

**Safety step:** export one real template first; reuse its top-level structure.
**Node shape:** `{ "type", "id":"olo-<type>-xxxx", "settings":{…}, "children":[…] }`.
Containers hold `children`. Confirm slugs.

## GLOBAL (same on every page)
**Theme = LIGHT/warm/accessible.** Colors: primary `#1b9e6f` (strong `#16855d`),
accent `#e8674e`, dark `#102a22`, light `#eef6f1`/`#f4f2ea`, text `#23302b`,
on-primary white. Fonts: Newsreader (display serif) + Mulish (body). Base 18px, pill
buttons. Alert bar + Header + Footer = global templates (content in Page A file).
Images = `image` tiles with empty `src` + `alt` = caption.

## GUARDRAILS
No hardcoded hex (use color roles). Only keys present in a tile's `defaults`. Never
rename saved keys. Icons from SVG set, no emoji. AA contrast, ≥44px hits.

---

## LE FARMACIE PAGE TREE (`type: page`, title "Farmacie Comunali Borgoverde — Le Farmacie")

1. **Page title bar** (breadcrumbs + headline):
   - crumbs: Home › Le Farmacie
   - h1: "Cinque farmacie, un solo territorio"
   - sub: "Siamo un'azienda speciale del Comune di Borgoverde: cinque presìdi
     distribuiti nei quartieri, con orari ampi e un turno di guardia sempre attivo."

2. **PostGrid / repeater of 5 location cards** (`postgrid` — confirm slug; each card
   = photo + name + address + a small rows list + a status badge). Card layout:
   thumbnail left, body right. Badge: "Di turno" (coral) for the on-duty one,
   "Aperta ora" (green-soft) for the others.
   - **Sede Centro** — Via Roma 14 · Quartiere Centro — badge **Di turno** — Lun–Sab
     8:30–22:00 · Domenica 9:00–13:00 · Servizi: CUP · Autoanalisi · Holter · ECG.
     img alt "facciata Farmacia Centro".
   - **Sede Parco** — Viale dei Tigli 8 · Quartiere Parco — badge Aperta ora — Lun–Ven
     8:30–19:30 · Sabato 8:30–13:00 · Servizi: CUP · Autoanalisi · Mamma & bambino.
     img alt "facciata Farmacia Parco".
   - **Sede Stazione** — Piazza Stazione 3 · Quartiere Stazione — badge Aperta ora —
     Lun–Sab 7:30–20:00 · Domenica Chiusa · Servizi: CUP · Autoanalisi · Noleggio
     ausili. img alt "facciata Farmacia Stazione".
   - **Sede Collina** — Via Belvedere 22 · Quartiere Collina — badge Aperta ora —
     Lun–Ven 8:30–13:00 / 15:30–19:30 · Sabato 8:30–12:30 · Servizi: CUP · Veterinaria
     · Dermocosmesi. img alt "facciata Farmacia Collina".
   - **Sede Mercato** — Via del Mercato 5 · Quartiere Mercato — badge Aperta ora —
     Lun–Sab 8:00–20:00 · Domenica 9:00–13:00 · Servizi: CUP · Autoanalisi · Consegna a
     domicilio. img alt "facciata Farmacia Mercato".

3. **EventList — turni di guardia** (`eventlist` — confirm slug; else a row+column
   table). Light-green section. Eyebrow "Turni di guardia", h2 "La farmacia aperta,
   giorno per giorno", lede "Fuori dagli orari ordinari è sempre attiva una farmacia
   di turno per le urgenze. Ecco i prossimi giorni." Rows (date / pharmacy / tag;
   highlight "today"):
   - **Oggi · lun 30 mag** — Sede Centro, Via Roma 14 — *Aperta fino alle 22:00*
   - **31 mar** — Sede Mercato, Via del Mercato 5 — *Notturno 20:00–8:30*
   - **01 mer** — Sede Stazione, Piazza Stazione 3 — *Notturno 20:00–8:30*
   - **02 gio** — Sede Centro, Via Roma 14 (Festivo) — *Festivo 9:00–22:00*
   - **03 ven** — Sede Parco, Viale dei Tigli 8 — *Notturno 20:00–8:30*

4. **Map** (`map`/embed tile if available, else `image`): eyebrow "Dove siamo", h2
   "Le nostre sedi sulla mappa"; image alt "mappa interattiva — 5 farmacie comunali di
   Borgoverde".

5. **cta-banner** (deep-green): h2 "Non sai quale farmacia è di turno?", body "Chiama
   il numero verde gratuito: ti indichiamo la sede aperta più vicina a te, a qualsiasi
   ora." CTA "Numero verde 800 12 34 56".

## IMPORT
Generate the `.json`, validate envelope vs a real exported template, import via
*Gestione Template → Importa* (or `POST /olobuild/v1/templates/import`). Relink images
and, ideally, wire the location cards / on-call shifts to a custom post type or
dynamic source so they update without editing the template.
