# Farmacie Comunali Borgoverde → OLObuild JSON — PAGE A: HOME

> Task for Claude Code: generate **importable OLObuild template JSON** for the
> **Home** of the "Farmacie Comunali Borgoverde" demo site (a fictional network of
> municipal/public pharmacies). You have the `claudiovinco/olobuild` repo — derive
> the JSON format from the code; this file gives design intent + content only.
> Blueprint reference: `Farmacie Comunali - Home.html`.

---

## READ FIRST (don't invent the format)
1. `includes/class-rest-api.php` → `templates/:id/export` + `templates/import`
   handlers = the exact JSON **envelope** to match.
2. `includes/class-database.php` → `wp_olo_templates` schema.
3. `src/config/elementRegistry.js` + each `src/config/elements/<type>.js` →
   `default.defaults` is the **complete list of valid `settings.*` keys**. Only emit
   keys that exist there.
4. `includes/class-frontend-renderer.php` → node shape + per-instance UID scheme.

**Safety step:** export one real template from the target site first and reuse its
top-level structure as the canonical envelope.

**Node shape:** `{ "type", "id":"olo-<type>-xxxx", "settings":{…overrides…},
"children":[…] }`. Containers (`section`,`row`,`column`,`grid`) hold `children`;
leaf tiles don't. Confirm every `type` slug exists in `src/config/elements/`.

## GLOBAL (shared by all pages — set once)
**Theme = LIGHT, warm, accessible** (audience: elderly + families). No dark hero;
only the footer + CTA banner are dark green.
Global colors (GlobalColorsPanel roles):
- **primary `#1b9e6f`** (pharmacy green), primary-strong `#16855d` for text/links
- **accent `#e8674e`** (warm coral — CTAs secondary, "turno" badge, highlights)
- secondary/dark **`#102a22`** (deep green — footer/CTA shell)
- light **`#eef6f1`** (faint green) / **`#f4f2ea`** (warm sand) section backgrounds
- text **`#23302b`**; on-primary white
Fonts: display **Newsreader** (serif, weights 500–700), body **Mulish** (400–800).
Base font 18px, generous line-height, larger hit areas (≥48px) for accessibility.
Radii friendly/rounded; buttons are **pill-shaped** (full radius).
Header, Footer **and** the "farmacia di turno" Alert bar = **global templates**,
assigned site-wide. Images = `image` tiles with empty `src` + `alt` = caption.

## GUARDRAILS
No hardcoded hex in tiles (use color roles/tokens). Only emit keys present in a
tile's `defaults`. Never rename/restructure saved keys. Icons from the SVG set, no
emoji. AA contrast, ≥44px hit areas. Spacing/radius from the scale; one radius
language. Logo mark = green rounded square with a **white pharmacy cross**.

---

## TURNO ALERT BAR (global — type `alert`, top of every page)
Thin dark-green bar above the nav: coral badge "Di turno oggi" + "**Farmacia Comunale
Centro** — Via Roma 14 · aperta fino alle **22:00**" + right link "Tutti i turni →"
to the Le Farmacie page.

## HEADER (global template — `navmenu`/`megamenu`, confirm slug, LIGHT/sticky)
Logo: green mark + cross, text "Farmacie Comunali" (display) / "BORGOVERDE" (small
caps green). Links: Home / Servizi / Le Farmacie / Contatti. CTAs: "Le farmacie"
(ghost) + "Prenota un servizio" (primary pill).

## FOOTER (global template — `footer`, deep green bg)
Brand blurb: "Cinque farmacie pubbliche al servizio dei cittadini di Borgoverde. La
salute, vicino a casa." Chip "Azienda Speciale del Comune di Borgoverde". Columns —
**Servizi**: Prenotazioni CUP, Autoanalisi, Consegna a domicilio, Noleggio
elettromedicali · **Le farmacie**: Sede Centro, Sede Parco, Sede Stazione, Turni di
guardia · **Contatti**: Prenota un servizio, Numero verde 800 12 34 56,
info@farmaciecomunali.bv.it. Bottom: "© 2026 Farmacie Comunali Borgoverde — P.IVA
01234567890 · Realizzato con OLObuild" + "Privacy · Note legali · Trasparenza".

---

## HOME PAGE TREE (`type: page`, title "Farmacie Comunali Borgoverde — Home")

1. **hero** (`hero.js`) — LIGHT, soft green-cream background, two columns.
   - title (serif): "La tua salute pubblica, *vicino a casa*." (italic green on the
     second phrase)
   - subtitle: "Cinque farmacie comunali al servizio dei cittadini di Borgoverde:
     prenotazioni sanitarie, autoanalisi, consegna a domicilio e la consulenza del
     farmacista, ogni giorno."
   - cta "Prenota un servizio" → Contatti; cta2 "Trova la farmacia di turno" → Le
     Farmacie.
   - trust line: 5 stars + "Oltre **40 anni** di servizio pubblico · **5 sedi** sul
     territorio comunale".
   - Side image tile, alt "farmacista al banco che accoglie un cittadino" + a small
     floating card "Consegna a domicilio / Gratuita per over 70 e disabili".

2. **iconbox** row (4 quick-access cards) — each: icon, title, short text, link:
   - **Prenotazioni CUP** — "Visite ed esami del Servizio Sanitario, senza fare la
     fila agli sportelli." → Servizi
   - **Ritiro referti** — "Ritira i referti delle analisi direttamente in farmacia,
     in pochi minuti." → Servizi
   - **Autoanalisi** — "Glicemia, colesterolo e pressione con risultato immediato e
     consulenza." → Servizi
   - **Consegna a domicilio** — "Farmaci e prodotti a casa tua, gratis per over 70 e
     persone con disabilità." → Contatti

3. **section** (light) → two **rows** (image + content; 2nd reversed):
   - Eyebrow "Molto più di una farmacia", h2 **"Tutta la salute, in un unico luogo di
     fiducia"** — body "Le farmacie comunali sono presìdi sanitari di prossimità.
     Oltre ai farmaci, trovi servizi pensati per semplificare la vita di famiglie,
     anziani e persone fragili." Image alt "interno farmacia — area servizi alla
     persona". List: Prenotazioni CUP e ritiro referti / Autoanalisi (glicemia,
     colesterolo, emoglobina, pressione) / Vaccinazioni stagionali e tamponi su
     appuntamento / Noleggio elettromedicali (tiralatte, stampelle, aerosol). CTA
     "Scopri tutti i servizi".
   - Eyebrow "Un servizio pubblico", h2 **"Prezzi calmierati, personale che conosci"**
     (reversed) — body "Siamo un'azienda speciale del Comune di Borgoverde: ogni utile
     torna ai cittadini sotto forma di servizi, prezzi più equi e presenza sul
     territorio." Image alt "scaffali e banco con farmacista". List: Farmaci sempre
     disponibili o ordinabili in giornata / Prezzi calmierati su parafarmaco, cosmesi
     e prima infanzia / Tessera fedeltà comunale con sconti ai residenti / Farmacisti
     dipendenti pubblici, sempre gli stessi volti. CTA "Vedi le nostre farmacie".

4. **counter** band (green bg, white numbers, 4× `counter.js`):
   5 farmacie sul territorio · 40+ anni di servizio pubblico · 12 servizi sanitari
   attivi · 1.200 cittadini serviti ogni giorno.

5. **newsticker** (`newsticker` — confirm slug) — coral "Avvisi" label + scrolling
   items (with date badge):
   - "15 ott — Al via la campagna vaccinale antinfluenzale in tutte le sedi"
   - "nuovo — Servizio di holter pressorio attivo alla sede Centro"
   - "2 giu — Chiusura straordinaria della sede Parco per festività"
   - "info — Disponibili i test rapidi per celiachia e intolleranza al lattosio"

6. **testimonial** (confirm slug) — light green section, 3 cards (5 stars each):
   - "Prenoto le visite qui sotto casa e ritiro i referti senza andare in ospedale.
     Per mia madre anziana è stata una svolta." — Giulia M., Quartiere Centro
   - "La consegna a domicilio gratuita mi permette di avere i farmaci sempre in tempo.
     Personale gentile e competente." — Antonio R., Quartiere Parco
   - "Faccio l'autoanalisi della glicemia ogni mese: pochi minuti e parlo subito con
     il farmacista. Mi sento seguita." — Lucia B., Quartiere Stazione
   Section head: eyebrow "La voce dei cittadini", h2 "Un punto di riferimento per il
   quartiere".

7. **marquee** (confirm slug) — lead "In convenzione e collaborazione con"; items:
   Comune di Borgoverde · ASL Borgoverde · CUP Regionale · Federfarma · Servizio
   Sanitario Nazionale.

8. **cta-banner** (`cta-banner.js`, deep-green bg): h2 "Hai bisogno di un farmaco o di
   un servizio?", body "Prenota online in pochi clic oppure passa in una delle nostre
   cinque farmacie comunali. Siamo qui per te." CTAs "Prenota un servizio" (white) +
   "Chiamaci" (outline-on-dark).

## IMPORT
Generate the `.json`, validate the envelope against a real exported template, then
import via *Gestione Template → Importa* (or `POST /olobuild/v1/templates/import`).
Set Home as the site front page; assign the Alert bar + Header + Footer site-wide;
relink images.
