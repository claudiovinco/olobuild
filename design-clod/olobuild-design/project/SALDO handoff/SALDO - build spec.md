# SALDO (studio fiscale) → OLObuild JSON — BUILD SPEC

> Task per Claude Code: generare **template JSON importabile in OLObuild** per la
> one-page **SALDO** — studio di consulenza fiscale/tributaria. Hai il repo
> `claudiovinco/olobuild`: ricava il formato JSON dal codice; questo file dà solo
> intento di design + contenuti. Blueprint: `SALDO - Studio fiscale.html`.
>
> A differenza di NOVA (dark, espressivo), SALDO è la versione **LIGHT, editoriale,
> rassicurante**: serif ad alto contrasto, carta calda, palette pino + ottone.
> Tutto mappa su **tile esistenti** — niente tile nuove richieste.

---

## READ FIRST (non inventare il formato)
1. `includes/class-rest-api.php` → `templates/:id/export` + `templates/import` =
   envelope JSON esatto.
2. `includes/class-database.php` → schema `wp_olo_templates`.
3. `src/config/elementRegistry.js` + `src/config/elements/<type>.js` →
   `default.defaults` = chiavi `settings.*` valide. Emetti SOLO chiavi presenti lì.
4. `includes/class-frontend-renderer.php` → shape nodo + schema UID.
5. `_shared.js` → `withHover` (hover transform / border-radius) e i campi colore.

**Safety step:** esporta prima un template reale dal sito target e riusa l'envelope.
**Node shape:** `{ "type", "id":"olo-<type>-xxxx", "settings":{…}, "children":[…] }`.
Container (`section`,`row`,`column`,`grid`) hanno `children`. Conferma ogni slug.

## GLOBAL — token & tema
**Tema = light, editoriale, caldo.** Colori globali (ruoli):
- testo/ink **`#15302a`** (pino, anche per sezioni scure), background **`#f3ece0`** (carta)
- accento **`#a9772c`** (ottone), su pino l'ottone schiarisce a `#d9a85a`
- band alt **`#ece2d1`**, card **`#fbf8f1`**, righe `rgba(21,48,42,.15)`
- semantici: ok `#3f7d54`, error `#b4543f`
Font: display **Newsreader** (serif, 500/600, anche corsivo per le parole-accento),
body **Manrope**, mono **JetBrains Mono** (eyebrow + cifre/€). Type grande ma sobrio
(hero ~92px). Bottoni a pillola. Grana carta leggera (multiply, ~4%) su tutta la pagina.
Le parole-accento in corsivo ottone (`.ac`) sono un tratto ricorrente — mapparle come
span con color role accento + italic.

## GUARDRAIL
Nessun hex hardcoded nelle tile (usa ruoli colore/token). Solo chiavi presenti nei
`defaults`. Mai rinominare chiavi salvate. Hover via `withHover`, non CSS ad-hoc.
Contrasto AA sul testo. Le immagini = tile `image` con `src` vuoto, `alt` = la caption.

---

## PAGE TREE (`type: page`, titolo "SALDO — Studio fiscale")

1. **navmenu** — logo "Saldo · studio fiscale"; link Servizi / Metodo / Pacchetti /
   Studio / FAQ; due CTA: telefono (ghost) + "Prenota una call" (solid). Sticky shrink
   con sfondo carta+blur quando scrolla.

2. **hero** (`hero.js`) — split editoriale (no mesh). Sinistra: eyebrow "● Studio
   fiscale e tributario · Milano · dal 2009"; H1 serif "I tuoi numeri, finalmente
   *in saldo.*" (ultima in corsivo ottone); sub; CTA "Prima consulenza gratuita" +
   "Vedi i pacchetti"; due chip trust. Destra: **card "Dichiarazione 2025"** (parallax
   leggero) con barre animate (Ricavi/Imposta/INPS) e footer "Risparmio stimato € 4.180"
   + due float chip ("Scadenza F24 · Tutto pagato", "Risposta media · entro 2 ore").
   La card mappa su un **content/iconbox** stilizzato; le barre = elementi progress o
   semplici div (no tile nuova).

3. **marquee** — strip serif scorrevole: Partita IVA ✦ Forfettari ✦ Contabilità ✦
   Dichiarazioni ✦ Pianificazione ✦ Bilanci ✦ Successioni (alcune parole in outline corsivo).

4. **counter** ×4 (`counter.js`): 640+ clienti/anno · € 2,4M imposte ottimizzate ·
   16 anni · 99% scadenze rispettate. Numeri serif, suffissi/prefissi ottone.

5. **desclist** / list (`desclist.js`) — lista editoriale hover "Cosa facciamo", righe
   numero + titolo + descrizione + freccia, slide-in ottone su hover:
   01 Apertura Partita IVA · 02 Regime forfettario · 03 Contabilità & bilanci ·
   04 Dichiarazione dei redditi · 05 Pianificazione fiscale · 06 Successioni & contenzioso.

6. **blendtext** (`blendtext.js`) — **band pino** "Il nostro approccio": H2 "Il fisco
   non deve fare *paura.*" + sub manifesto + 3 colonne (Trasparenza / Proattività /
   Digitale). Glow radiale ottone+verde di sfondo.

7. **eventlist / timeline** (`eventlist.js` o equivalente) — head sticky a sinistra
   "Come lavoriamo / Quattro passi…", timeline a destra: 01 Analisi · 02 Strategia ·
   03 Adempimenti · 04 Monitoraggio (dot numerati + linea verticale).

8. **pricing** (`pricing.js`) — band alt. Head + **toggle Mensile/Annuale** (annuale
   = −2 mesi) + 3 piani: **Start** (Forfettari, €59/49), **Studio** (Professionisti,
   €129/109, *featured* pino con badge "Più scelto"), **Impresa** (SRL, €279/239).
   Liste feature con check ottone. Il toggle commuta i due prezzi mese/anno.

9. **testimonial** (`testimonial.js`) — band pino: quote serif con accento ottone
   (Giulia Ferrara, architetto) + 2 card laterali (4.9/5 · 210 recensioni; 93% rinnova).

10. **accordion** (`accordion.js`) — FAQ: head sticky sinistra, 5 domande a destra
    (cambio commercialista, canone incluso, online vs studio, forfettario, tempi P.IVA).
    Prima aperta di default.

11. **authorbox / team** (`authorbox.js`) — band alt "Lo studio", 3 membri card
    (Marco De Santis, Elena Conti, Davide Greco) con ruolo, bio breve, tag.

12. **cta-banner** + **footer** — CTA pino "Mettiamo i tuoi numeri *in saldo.*"
    (mailto ciao@saldostudio.it + tel), poi footer 4 colonne (brand + Servizi + Studio
    + Contatti) e barra "© 2026 SALDO · Realizzato con OLObuild".

---

## ✅ COPERTO DA TILE ESISTENTI (nessuna tile nuova)
- Navmenu (sticky shrink), Hero, Marquee, Counter, DescList, BlendText, EventList,
  Pricing (con billing toggle), Testimonial, Accordion, AuthorBox, CtaBanner, Footer.
- Parallax leggero (card hero / float) → `data-parallax`/mouse nativi.
- Hover (riga servizio slide, card lift, freccia) → `withHover`.
- Le **parole-accento corsivo ottone** = span con color role accento + `font-style:italic`.

## NOTE
- Tieni 1–2 sole band di sfondo (carta + carta-2) e le band **pino** per manifesto,
  testimonial e CTA: è il ritmo visivo del template, non aggiungerne altre.
- Toggle pricing: salva due valori prezzo (mese/anno) per piano; mostra/nascondi via
  lo stato del toggle, non duplicando le tile.

## IMPORT
Genera il `.json`, valida l'envelope contro un template reale esportato, importa via
*Gestione Template → Importa* (o `POST /olobuild/v1/templates/import`). Ricollega le
immagini (ritratti team = `image`, `alt` come da blueprint).
