# Blueprint — Cohere "North" (https://cohere.com/north)

Cattura reale 2026-06-16 (viewport 1440×900, scrollHeight 11476px, 16 `<section>`).
Obiettivo: ricreazione **pixel-perfect** come template Olobuild (tile vere, zero HTML inline).
Imagery di Cohere = copyright → si riproduce il **design** (layout/typografia/colori/animazioni/forme),
usando **immagini stock** equivalenti dalla libreria (aerial grass, office, city, gradient).

## Design tokens (estratti reali)

### Colori
| Ruolo | HEX | Uso |
|---|---|---|
| Hero green (dark) | `#062C22` | sfondo hero + sezione CTA finale |
| Mint | `#F1FDEA` | sfondo sezione "Accelerate impact" |
| Navy (dark) | `#061324` | sfondo sezione "Private. Secure. Compliant." |
| Footer ink | `#17171C` | sfondo footer/newsletter |
| Card green | `#0A2E22` (~) | card stack-scroll Discover/Create/Automate |
| Coral (brand) | `#FF7759` | accent |
| Coral light | `#FFAD9B` | accent chiaro |
| Green mid | `#39594D` | accent secondario |
| Volcanic (text) | `#212121` | testo su chiaro |
| Marble | `#FAFAFA` | superfici chiare |
| News card | `#EFECE6` (~) | card news (marble caldo) |
| Cyan graphic | `#B9FBE7`/`#9DF5D6` (~) | grafica blocchi nella bento security |

### Tipografia (3 ruoli)
- **Display** (h1/h2/h3 grandi): `CohereText` → **Space Grotesk** (Google). Weight 400.
  - hero h1: 72px / lh 72px / ls -1.44px (-0.02em)
  - h2 sezioni + display card (Discover/Create/Automate/Empower): 60px / lh 60px / ls -1.2px
  - lh ≈ 1.0 (tight), ls negativo ~-0.02em
- **Eyebrow** (label sopra titolo): `CohereMono` → **Space Mono** (Google). 14px, **UPPERCASE**, ls 0.28px (0.02em), weight 400. Es: `NORTH`, `ADVANCED SEARCH AND RETRIEVAL`, `GENERATIVE AI`, `WORKFLOW AUTOMATION`.
- **Body/UI/bottoni**: `Unica77 Cohere Web` → **Inter** (Google). body 18px / lh 25.2px (1.4); bottone 16px / lh 24px.

### Layout
- Container content ≈ **1280–1335px**, padding laterale **40px** per lato (`section{padding-inline:40px}`).
- Paddings verticali sezione: hero pt160/pb144; logos 80/80; "More mindspace" 80/40; capabilities 80/...; use-cases 96/80; security 144/144; CTA simile.
- Griglia a 12 colonne; molte sezioni a 2 colonne (titolo sx / corpo dx) o 3 card.

### Forme caratteristiche
- **Card news**: rounded ma con **angolo basso-destro tagliato a "piega"** (folded corner / notch) su sfondo marble caldo.
- Pill button bianco con testo scuro, raggio pieno (999px).
- Crest hero = **mirino**: cerchio con croce (+) sottile, stroke chiaro.
- Card bento security: raggio ~16-20px, bordo sottile chiaro su navy.

## Sezioni (top → bottom)

### 0. HERO (bg `#062C22`, testo bianco)  pt160 pb144
- Eyebrow mono `NORTH`.
- Crest **mirino** (cerchio + croce) a sinistra del titolo.
- H1 display 72px: **"AI for business that turns complexity into clarity"**.
- Sotto: **mockup prodotto = video** (player con scrubber "0:00", controlli play/mute/fullscreen) dentro un frame scuro arrotondato; il mockup entra "dentro" la pagina mentre si scorre.
- **Sfondo erba aerea (parallasse)**: una grande immagine aerea di campo/erba che appare sotto al mockup e resta **fissa** mentre il contenuto scorre.

### 1. SUB-HERO "Empower" (sopra lo sfondo erba fisso)
- H2 display 60px centrato, colore chiaro/muted (bianco semitrasparente): **"Empower your workforce with AI agents that operate in lockstep with your people, data, and tools."**
- **Effetto reveal**: il testo si schiarisce/fade-in parola per parola mentre scorre (scroll-linked opacity).
- Pill bianco **"Request a demo"** centrato.
- Background = la stessa aerial grass image, **fixed/parallax** (sfondo fisso classico).

### 2. TRUST LOGOS (bg bianco)  80/80
- Titolo piccolo centrato (Inter ~16-18px, grigio): **"Organizations that trust us"**.
- **Marquee infinito** di loghi mono-colore (nero): Oracle, Dell Technologies, RBC, LG CNS, Fujitsu, Bell, Asana, RWS, SAP, Salesforce, Notion, TD Bank, Ensemble, Second Front, McKinsey & Company, Accenture, BambooHR, STC.

### 3-5. "More mindspace, less mayhem" (bg bianco)
- 2 colonne: a sx **H2 display 60px** "More mindspace, less mayhem"; a dx body 18px: "North sets the standard for business performance by helping teams automate work and accelerate decisions that drive results — all in one scalable, secure workspace."
- Sotto: **3 card a colonne** con **immagine in alto** (full-width della card, raggio) + titolo (display ~28px) + body + link **"Learn more →"**:
  1. **Scalability** — foto donna alla scrivania — "Supercharge your team's ability to get more done with customizable AI agents and automated workflows." → /north/agent-studio
  2. **Productivity** — grafico voronoi/mesh su mint — "Get instant, context-aware answers and generate reports securely grounded in your internal sources of truth." → /north/workplace-productivity
  3. **Security** — (grafica) — "Protect your business with private deployment options, as well as industry-leading security and data protection." → /security
  (NB: nello scroll le immagini sono affiancate in alto e le 3 colonne testo sotto.)

### 6-7. "Accelerate impact and outcomes" (bg mint `#F1FDEA`)  pt80
- Header 2-col: H2 display 60px "Accelerate impact and outcomes" + body "Enable seamless human-agent collaboration, automate routine tasks, and transform fragmented data into actionable insights."
- **STACK-SCROLL**: 3 grandi card verde scuro `#0A2E22` (raggio ~24px) che si **impilano in pin** mentre si scorre (sticky stacking). Ogni card: a sx eyebrow mono + **display 60px** + body bianco; a dx **mockup UI** (chat/agent/documento):
  1. eyebrow `ADVANCED SEARCH AND RETRIEVAL` — **Discover** — "From basic Q&A to complex decision making, North surfaces verifiable insights grounded in your data." — mockup: "Finance Agent" + connettori (Drive/Salesforce) READY + input "Summarize our…".
  2. eyebrow `GENERATIVE AI` — **Create** — "Co-create documents, generate summaries, and produce tables and charts instantly." — mockup: doc "Financial Summary for Q4" + agent card "Quarterly Financial Summaries".
  3. eyebrow `WORKFLOW AUTOMATION` — **Automate** — "Deploy AI agents across teams to eliminate tedious tasks and accelerate complex workflows." — mockup: nodi flow agent.

### 8-9. "Put AI to work" (bg bianco)  pt96
- H2 display 60px centrato "Put AI to work" + body centrato "No matter the team, and no matter the task, North frees your teams to focus on the work that propels your business forward."
- **Tab switcher** pill (segmented): **Legal | Sales | Finance | Operations** (tab attivo = pill scuro).
- Sotto, 2 colonne: a sx body 18px del tab (Legal: "Accelerate contract review and redlining, ensure compliance, and uncover insights from large volumes of data to improve accuracy and reduce risk.") + a dx **mockup UI** documento (Licensing Contract con highlight + comment box).

### 10. "Private. Secure. Compliant." (bg navy `#061324`, testo bianco)  144/144
- H2 display 60px su 2 righe: "Private. Secure. Compliant." / "This is what enterprise-ready AI looks like".
- **BENTO GRID** (4 card, span misti, raggio ~18px, bordo 1px chiaro):
  1. (grande, sx) **immagine city skyline** con overlay testo in basso: titolo "Secure by design" + body "Safeguard sensitive data with a zero-trust security framework, precise access controls, and audit-ready visibility." + "Learn more →".
  2. (alto-dx) card testo con **icona asterisco/sparkle** SVG: "Fully customizable" + "Design and deploy agent-powered workflows tailored to your team's tools, processes, and objectives."
  3. (basso-sx) card con **grafica blocchi cyan** (barre pixel) + "Natively interoperable" + "Connect North to existing tools, data, and monitoring systems with flexible APIs and built-in connectors."
  4. (grande, dx) **immagine gradient viola/giallo** con overlay: "Privately deployable" + "Run North in your own VPC, on-prem environment, or through Cohere's secure Model Vault inference platform." + "Learn more →".

### 11. "Why enterprises and innovators choose Cohere" (bg bianco)
- H2 display ~44px sx + frecce ←/→ a dx.
- **Carousel** di quote-card (bordo sottile, raggio): logo cliente (RBC) + quote lunga + attribuzione "Dr. Foteini Agrafioti, SVP, Data & AI & Chief Science Officer". Card adiacente con grafica scura animata (linee punteggiate).

### 12-13. "News and insights" (bg bianco)
- H2 display ~44px "News and insights".
- 3 **card news** (forma con **angolo basso-dx tagliato**, sfondo marble): immagine + "Cohere Team - <data>" + titolo + "Read more". Es: "Introducing North: The next era of enterprise AI", "Defining AI automation: A new kind of workplace", "Bringing secure AI to critical systems".
- **Paginazione** "1 2" + frecce.

### 14. CTA "Accelerate your AI roadmap" (bg `#062C22`, testo bianco)
- 2 colonne: a sx H2 display "Accelerate your AI roadmap" + body "Connect with an expert to explore how Cohere's products can fit your stack, data, and goals." + bullet list:
  - Align AI to your workflows and use cases
  - Choose deployment options that fit your infrastructure
  - Move from pilot to production — safely and securely
- A dx **form contatto** (First/Last/Email/Title/Phone…) + "Submit" + Privacy Policy.

### 15. FOOTER / Newsletter (bg `#17171C`, testo chiaro)
- Blocco newsletter in alto: "AI moves fast" + "We'll keep you up to date with the latest." + campo email business + nota privacy.
- Footer 4 colonne: Products / Solutions / Resources / Company (vedi WebFetch dump per voci).
- Social: LinkedIn, Discord, X, Support email. Copyright "Cohere © 2026". Privacy/Terms/Manage Cookies. Lang: English.

## Animazioni / effetti da ricreare
1. **Parallax / fixed background** (erba aerea) dietro hero+sub-hero.
2. **Scroll-reveal fade** del titolo "Empower…" (opacità legata allo scroll).
3. **Marquee infinito** loghi.
4. **Stack-scroll pinned** delle 3 card Accelerate (sticky stacking).
5. **Tab switcher** use-cases (Legal/Sales/Finance/Operations).
6. **Bento grid** security.
7. **Carousel** quote (frecce).
8. **Paginazione** news.
9. Hover: link "Learn more →" freccia, pill button, card lift.
