# Farmacie Comunali Borgoverde → OLObuild JSON — PAGE D: CONTATTI

> Task for Claude Code: generate **importable OLObuild template JSON** for the
> **Contatti** page. You have the `claudiovinco/olobuild` repo — derive the JSON
> format from the code; this file gives design intent + content only.
> Blueprint reference: `Farmacie Comunali - Contatti.html`.

---

## READ FIRST (don't invent the format)
1. `includes/class-rest-api.php` → `templates/:id/export` + `templates/import` = the
   exact JSON **envelope**.
2. `includes/class-database.php` → `wp_olo_templates` schema.
3. `src/config/elementRegistry.js` + each `src/config/elements/<type>.js` →
   `default.defaults` = valid `settings.*` keys. Only emit keys that exist there.
4. `includes/class-frontend-renderer.php` → node shape + UID scheme.
5. **`includes/class-form-handler.php`** + `src/config/elements/form.js` → the form
   field schema, recipient/mailer config, and how submissions are stored
   (`includes/class-form-submissions.php`).

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

## CONTATTI PAGE TREE (`type: page`, title "Farmacie Comunali Borgoverde — Contatti")

1. **Page title bar** (breadcrumbs + headline):
   - crumbs: Home › Contatti
   - h1: "Prenota un servizio o scrivici"
   - sub: "Compila il modulo per prenotare un'autoanalisi, richiedere la consegna a
     domicilio o avere informazioni. Ti rispondiamo in giornata."

2. **section** → row, two columns (~1.05fr / 0.95fr):

   **Left — `form` tile** (`form.js`). Fields (label / type / required):
   - Nome — text — required
   - Cognome — text — required
   - Telefono — tel — required
   - Email — email
   - Servizio richiesto — select: Prenotazione CUP (visita/esame) · Autoanalisi
     (glicemia, colesterolo…) · Holter pressorio / ECG · Consegna a domicilio ·
     Noleggio elettromedicale · Altra informazione
   - Farmacia preferita — select: Sede Centro — Via Roma 14 · Sede Parco — Viale dei
     Tigli 8 · Sede Stazione — Piazza Stazione 3 · Sede Collina — Via Belvedere 22 ·
     Sede Mercato — Via del Mercato 5
   - Messaggio — textarea — placeholder "Descrivi la tua richiesta: ad esempio il tipo
     di esame da prenotare o i farmaci da consegnare…"
   - Consent checkbox: "Acconsento al trattamento dei miei dati per essere
     ricontattato, secondo l'informativa sulla privacy."
   - Submit: "Invia la richiesta".
   Configure recipient/mailer in the Form handler; store submissions per
   `class-form-submissions.php`.

   **Right — info cards** (`iconbox`/`iconlist`):
   - (highlight, green-soft bg) **Numero verde gratuito** — Telefono **800 12 34 56**
     · attivo 8:30–20:00 · Urgenze: farmacia di turno sempre attiva.
   - **Scrivici** — Email info@farmaciecomunali.bv.it · PEC
     farmaciecomunali@pec.bv.it · Domicilio domicilio@farmaciecomunali.bv.it.
   - **Sede amministrativa** — Via Roma 14, 20010 Borgoverde (MI) · Uffici Lun–Ven
     9:00–16:00.
   - **Map**: image tile, alt "mappa — sede amministrativa, Via Roma 14".

3. **accordion** (`accordion.js`), light-green bg — "Risposte rapide" (first open):
   - **In quanto tempo rispondete alle richieste?** → "Le richieste inviate dal modulo
     o via email ricevono risposta in giornata, negli orari di apertura degli uffici.
     Per le urgenze chiama il numero verde o rivolgiti alla farmacia di turno."
   - **Posso prenotare la consegna a domicilio da qui?** → "Sì: seleziona 'Consegna a
     domicilio' nel modulo e indica i farmaci o i prodotti. Per over 70 e persone con
     disabilità residenti il servizio è gratuito."
   - **Cosa devo portare per la prenotazione CUP?** → "La tessera sanitaria e la
     ricetta del medico (anche nel formato del promemoria elettronico). Puoi prenotare
     anche per un familiare portando i suoi documenti."

## IMPORT
Generate the `.json`, validate envelope vs a real exported template, import via
*Gestione Template → Importa* (or `POST /olobuild/v1/templates/import`). After import:
wire the form recipient/mailer and test a submission; relink the map image.
