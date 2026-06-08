# REFINEMENT — Pixel-perfect pass (standard elevato)

Obiettivo: portare un tema già ricomposto a **fedeltà pixel-perfect** col blueprint.
NIENTE approssimazioni: dove un tile generico (info-cards) non riproduce il layout del
blueprint, usa il **tile dedicato giusto** (vedi sotto). "Quasi" non è "fatto".

Riferimento aureo del nuovo standard: **`tmp_gen_sterling.cjs`** (usa process-steps borderless,
team tiles ad avatar circolare, cta-banner a 2 pulsanti).

## Tile NUOVI / aggiornati (usali!)
- **`process-steps`** — passi numerati. Default **borderless** (numero grande + titolo + testo).
  Settings: `columns, gap, align('left'|'center'), auto_number, number_style('plain'|'circle'|'outline'),
  number_color, number_bg(per circle/outline), number_size, number_font('serif'|'sans-serif'|'mono'),
  number_weight, title_color, title_size, title_font, title_weight, desc_color, desc_size, item_gap,
  card_bg, card_border, card_radius, card_padding`. 
  → Se nel CSS `.step`/`.cr-step` ha **background+border** (card), imposta `card_bg/card_border/card_radius/card_padding`.
  → Se è **senza card** (es. Sterling `.st-step{padding:0 12px}`), lascia `card_bg:'' card_border:'' card_padding:0`.
  USA QUESTO al posto di info-cards per ogni sezione "ProcessSteps/Approach/How it works/Steps".
- **`cta-banner` 2° pulsante** — campi nuovi `cta2_text, cta2_url, cta2_bg, cta2_color, cta2_border`.
  Se il blueprint ha **due** bottoni nella CTA finale, aggiungi `cta2_*` (di solito outline:
  `cta2_bg:'transparent', cta2_color:<cream/white>, cta2_border:<line-2>`).
- **`team`** — UN membro per tile (avatar circolare + nome + ruolo). Per una griglia persone:
  `row([ col('1-4',[tile('team',{...})]) ×N ])`. Settings chiave:
  `photo:'' (placeholder cerchio), name, role, bio:'', photo_shape:'circle', photo_size:'120',
  photo_border_width:'0', photo_shadow:'none', info_align:'center', info_bg_color:'transparent',
  info_text_color:<colore nome>, role_color:<accent>, name_size:'21', role_size:'13',
  bg_color:'transparent', tile_padding:{top:0,right:0,bottom:0,left:0}, border_radius:'0'`.
  USA QUESTO per ogni sezione "team/people/staff/stylists/advisors/coaches/instructors", MAI info-cards-monogramma.

## Altri tile dedicati (preferiscili a info-cards dove calzano)
- Prodotti con prezzo/badge → **`product-cards`** (leggi `src/config/elements/product-cards.js`).
- Gallerie foto → **`gallery`** (leggi config). Menu/listini → **`pricelist`** (già usato).
- Form contatto/RSVP/booking → **`form`** (leggi `src/config/elements/form.js`).
- Loghi clienti → **`trust-strip`**. Stat → **`counter`**. Ticker → **`marquee`/`newsticker`**.
  Countdown → **`countdown`**. Slider-calcolo → **`projector`**.

## Regole PIXEL (leggi il CSS per-sezione, non solo :root)
1. **Bordi/card**: replica ESATTAMENTE. Se `.x` non ha `background`/`border` → niente card.
   Se ce l'ha → riproducila (bg, border 1px colore, radius, padding dal CSS).
2. **Sfondi sezione**: alterna `--bg` / `--bg-2` / panel come nel blueprint (classe `.panel` ecc.).
3. **Font**: titoli serif → `headline_font_family/number_font/title_font:'serif'`; sans → `'sans-serif'`.
4. **Copy**: identica carattere per carattere (eyebrow, h1/h2, p, voci, prezzi, citazioni, footer).
   Simboli %, £, €, ·, –, — e apostrofi curvi: usa **BACKTICK**.
5. **Conteggi**: n. di card/stat/loghi/righe/persone = come nel blueprint.
6. **Nome brand** = logo/<title> del blueprint (es. "Verdano FC", "Field & Co", "Relay OS").
7. **Palette** = hex esatti dal `:root` del CSS (gallery solo sanity-check).
8. IMAGE-FREE resta per foto/hero-bg/lookbook (pannelli astratti); MA team→avatar cerchio del tile `team`,
   prodotti→product-cards, ecc. (placeholder nativi dei tile, non foto esterne).

## Procedura
1. Leggi blueprint HTML (ogni sezione + copy) e CSS (`:root` + le regole `.x` di OGNI sezione).
2. Leggi il generatore attuale `tmp_gen_<slug>.cjs`.
3. Riscrivi/aggiusta le sezioni per usare i tile giusti e i valori pixel esatti (vedi sopra).
4. `node tmp_gen_<slug>.cjs` → "text-block 0 ✓"; ogni tipo-tile deve esistere in `src/config/elements/`.
5. Report: verdetto fedeltà + correzioni puntuali (tile cambiati, copy, palette, bordi).

VINCOLI: 0 text-block; no version bump; no deploy; no HTML/preview; BACKTICK per apostrofi/accenti;
mai '&&' nei contenuti; icone solo-Lucide; non toccare `tmp_theme_kit.cjs`.
