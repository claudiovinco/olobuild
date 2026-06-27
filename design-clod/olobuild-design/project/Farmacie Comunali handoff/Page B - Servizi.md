# Farmacie Comunali Borgoverde → OLObuild JSON — PAGE B: SERVIZI

> Task for Claude Code: generate **importable OLObuild template JSON** for the
> **Servizi** page. You have the `claudiovinco/olobuild` repo — derive the JSON
> format from the code; this file gives design intent + content only.
> Blueprint reference: `Farmacie Comunali - Servizi.html`.

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
Containers (`section`,`row`,`column`,`grid`) hold `children`. Confirm slugs.

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

## SERVIZI PAGE TREE (`type: page`, title "Farmacie Comunali Borgoverde — Servizi")

1. **Page title bar** (light green gradient; breadcrumbs + headline):
   - crumbs: Home › Servizi
   - h1: "Tutti i servizi, sotto casa"
   - sub: "Le farmacie comunali sono presìdi sanitari di prossimità. Dalla prevenzione
     alle prenotazioni del Servizio Sanitario, ecco cosa puoi fare in farmacia."

2. **grid** 3×2 of **iconbox** (`iconbox.js`), each with icon + title + short text +
   a 2-item checklist:
   - **Salute & prevenzione** — "Screening e controlli rapidi con la consulenza del
     farmacista." → Pressione, glicemia, colesterolo / Holter pressorio ed ECG.
   - **Prenotazioni & referti** — "Lo sportello CUP in farmacia: prenoti, paghi il
     ticket e ritiri." → Prenotazione visite ed esami / Ritiro referti e pagamento
     ticket.
   - **Cosmesi & dermo** — "Linee dermocosmetiche e consigli su pelle, capelli e
     solari." → Dermocosmesi e prodotti specifici / Prezzi calmierati sui residenti.
   - **Mamma & bambino** — "Tutto per la prima infanzia, con uno spazio dedicato e
     riservato." → Latte, pannolini e svezzamento / Noleggio bilancia e tiralatte.
   - **Veterinaria** — "Farmaci e prodotti per i tuoi animali, con ricetta
     veterinaria." → Farmaci veterinari e antiparassitari / Alimenti dietetici su
     misura.
   - **Noleggio elettromedicali** — "Ausili e dispositivi a noleggio, per il tempo che
     ti servono." → Stampelle, carrozzine, aerosol / Tiralatte e bilance per neonati.

3. **section** (light green) → two **rows** (image + content; 2nd reversed):
   - Eyebrow "Autoanalisi & screening", h2 **"Controlli rapidi, risultati in pochi
     minuti"** — body "Senza prenotazione e senza prelievi di laboratorio: un piccolo
     campione, pochi minuti di attesa e il farmacista ti spiega subito i risultati."
     Image alt "misurazione pressione al banco autoanalisi". List: Glicemia,
     colesterolo totale e trigliceridi / Pressione arteriosa e holter pressorio / Test
     rapidi: celiachia, intolleranze, emoglobina. CTA "Prenota un'autoanalisi".
   - Eyebrow "Sportello CUP", h2 **"Il Servizio Sanitario, senza file in ospedale"**
     (reversed) — body "Prenoti visite ed esami, paghi il ticket e ritiri i referti
     direttamente al banco. Un aiuto concreto soprattutto per anziani e persone
     fragili." Image alt "sportello CUP — prenotazione visite". List: Prenotazione e
     disdetta di visite ed esami / Pagamento del ticket sanitario / Ritiro referti,
     anche per conto di familiari. CTA "Prenota allo sportello".

4. **accordion** (`accordion.js`) — "Domande frequenti" (first item open):
   - **Serve la prenotazione per le autoanalisi?** → "No, le autoanalisi si fanno
     senza appuntamento durante gli orari di apertura. Per holter pressorio ed ECG è
     invece consigliata la prenotazione, perché lo strumento va consegnato e
     ritirato."
   - **Posso prenotare visite del Servizio Sanitario in farmacia?** → "Sì. Allo
     sportello CUP prenoti visite ed esami, paghi il ticket e ritiri i referti. Porta
     la tessera sanitaria e la ricetta del medico, anche elettronica."
   - **La consegna a domicilio è davvero gratuita?** → "È gratuita per over 70, persone
     con disabilità e in condizioni di fragilità residenti nel Comune di Borgoverde.
     Per gli altri è disponibile con un piccolo contributo."
   - **Come funziona il noleggio degli elettromedicali?** → "Lasci un piccolo deposito
     cauzionale e paghi una tariffa giornaliera o settimanale calmierata. Tiralatte,
     aerosol, stampelle, carrozzine e bilance per neonati sono i più richiesti."
   - **I prezzi sono diversi da una farmacia privata?** → "Sui farmaci il prezzo è
     quello stabilito per legge. Su parafarmaco, cosmesi e prima infanzia applichiamo
     prezzi calmierati e sconti ai residenti con la tessera fedeltà comunale."

5. **cta-banner** (deep-green): h2 "Vuoi prenotare un servizio?", body "Scegli la
   farmacia più vicina e prenota online, oppure chiama il nostro numero verde
   gratuito." CTAs "Prenota ora" + "Trova la farmacia".

## IMPORT
Generate the `.json`, validate envelope vs a real exported template, import via
*Gestione Template → Importa* (or `POST /olobuild/v1/templates/import`). Relink images.
