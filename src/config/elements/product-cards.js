import { t } from '@/i18n';
import { withHover } from './_shared';
import { ratioOptions } from './_imageFrame.js';
import { demo } from '../demoMedia.js';

const R = (n) => ({ tl: n, tr: n, br: n, bl: n, linked: true });

/**
 * Product Cards — griglia di card prodotto stile editoriale.
 * Pattern: metà alta gradient + lettera-monogramma grande + label outline,
 * metà bassa con brand colored + badge pill opzionale + titolo serif con
 * suffisso italic + descrizione + CTA testuale.
 * Allineato agli standard Olobuild: border-radius 4 angoli + hover,
 * background creativo unificato, editor rich per descrizione.
 */
export default {
  type: 'product-cards',
  name: t('Schede prodotto'),
  icon: 'dashicons-portfolio',
  category: 'layout',

  defaults: {
    typography_preset: '',
    columns:  5,
    gap:      24,

    items: [
      { letter: 'C', letter_color: '#b3261e', top_bg: { type: 'gradient', gradient_from: '#fdf2f2', gradient_to: '#fbe1e1', gradient_angle: 180 }, screenshot_label: 'SCREENSHOT · EDITOR LIVE', brand_label: 'OLOBUILD',  brand_color: '#b3261e', show_badge: true,  badge_text: 'GRATIS', badge_bg: 'var(--olo-color-dark, #16263d)', badge_color: 'var(--olo-color-surface, #ffffff)', title: 'Co',     title_accent: 'struisci',  title_accent_italic: true,  description: 'Page builder olonico. 187 tile, motion design, border-radius animato all\'hover. Alla pari dei top builder commerciali.', cta_text: 'SCOPRI OLOBUILD',  cta_url: '#' },
      { letter: 'T', letter_color: '#c2185b', top_bg: { type: 'gradient', gradient_from: '#fce4ec', gradient_to: '#f8bbd0', gradient_angle: 180 }, screenshot_label: 'SCREENSHOT · MULTILINGUA', brand_label: 'OLOLANG',   brand_color: '#c2185b', show_badge: false, badge_text: '',       badge_bg: 'var(--olo-color-dark, #16263d)', badge_color: 'var(--olo-color-surface, #ffffff)', title: 'Tra',    title_accent: 'duci',      title_accent_italic: true,  description: 'Multilingua nativo. Traduzioni IA + editing umano. SEO per ogni lingua. 28 idiomi inclusi.',                                cta_text: 'SCOPRI OLOLANG',   cta_url: '#' },
      { letter: 'P', letter_color: '#1976d2', top_bg: { type: 'gradient', gradient_from: '#e3f2fd', gradient_to: '#bbdefb', gradient_angle: 180 }, screenshot_label: 'SCREENSHOT · CALENDARIO', brand_label: 'OLOBOOKING', brand_color: '#1976d2', show_badge: false, badge_text: '',       badge_bg: 'var(--olo-color-dark, #16263d)', badge_color: 'var(--olo-color-surface, #ffffff)', title: 'Pre',    title_accent: 'nota',      title_accent_italic: true,  description: 'Motore di prenotazione multi-verticale. Strutture, appuntamenti, eventi, immobili, noleggi, ristoranti.',                   cta_text: 'SCOPRI OLOBOOKING', cta_url: '#' },
      { letter: 'M', letter_color: '#e65100', top_bg: { type: 'gradient', gradient_from: '#fff3e0', gradient_to: '#ffe0b2', gradient_angle: 180 }, screenshot_label: 'SCREENSHOT · TOUR 360°', brand_label: 'OLOTOUR',    brand_color: '#e65100', show_badge: false, badge_text: '',       badge_bg: 'var(--olo-color-dark, #16263d)', badge_color: 'var(--olo-color-surface, #ffffff)', title: 'Mo',     title_accent: 'stra',      title_accent_italic: true,  description: 'Percorsi guidati a 360°. Foto e video sferici con hot-spot interattivi, multi-stanza, supporto VR.',                       cta_text: 'SCOPRI OLOTOUR',   cta_url: '#' },
      { letter: 'I', letter_color: '#2e7d32', top_bg: { type: 'gradient', gradient_from: '#e8f5e9', gradient_to: '#c8e6c9', gradient_angle: 180 }, screenshot_label: 'SCREENSHOT · AREA CORSI', brand_label: 'OLOTUTOR',   brand_color: '#2e7d32', show_badge: false, badge_text: '',       badge_bg: 'var(--olo-color-dark, #16263d)', badge_color: 'var(--olo-color-surface, #ffffff)', title: 'In',     title_accent: 'segna',     title_accent_italic: true,  description: 'E-learning completo. Corsi, lezioni, esercizi, certificazioni, area allievi. WordPress-native.',                          cta_text: 'SCOPRI OLOTUTOR',  cta_url: '#' },
    ],

    // Card style
    card_bg:                       { type: 'solid', color: 'var(--olo-color-surface, #ffffff)' },
    card_color:                    'var(--olo-color-dark, #16263d)',
    card_radius:                   { ...R(24) },
    card_radius_hover:             { ...R(24) },
    card_radius_hover_duration:    400,
    card_shadow:                   'sm',
    card_padding:                  28,

    // Top section (lettera + screenshot label)
    top_aspect_ratio:        '3/4',
    top_padding:             24,
    letter_font_family:      'serif',
    letter_size:             140,
    letter_italic:           true,
    letter_align:            'center',
    logo_height:             52,
    show_screenshot_label:   true,
    screenshot_label_color:  'var(--olo-color-text-faint, #94a3b8)',

    // Bottom section
    brand_size:              13,
    brand_letter_spacing:    0.08,
    title_font_family:       'serif',
    title_size:              30,
    title_weight:            '500',
    description_size:        15,
    cta_size:                12,
    cta_arrow:               true,
    price_size:              20,
    price_color:             '',

    // Hover
    card_hover_effect: 'lift',
  },

  // Appena nata: quattro schede prodotto di un negozio di oggetti per la casa — foto in alto,
  // categoria nel colore primario, un badge «Novità», nome con la seconda parola in corsivo,
  // descrizione, prezzo e invito testuale nella riga in fondo; la card si solleva al passaggio.
  partenza: {
    columns: 4,
    items: [
      { letter: '', letter_color: 'var(--olo-color-primary, #e1474f)', logo_image: '', top_bg: { type: 'image', image_url: demo('vaso'), image_size: 'cover', image_position: 'center center' }, screenshot_label: '', brand_label: t('Ceramica'), brand_color: 'var(--olo-color-primary, #e1474f)', show_badge: true, badge_text: t('Novità'), badge_bg: 'var(--olo-color-primary, #e1474f)', badge_color: 'var(--olo-color-primary-contrast, #ffffff)', title: t('Vaso '), title_accent: t('Neve'), title_accent_italic: true, description: t('Ceramica bianca lavorata a mano, alto 28 cm.'), price: '48 €', cta_text: t('Scopri'), cta_url: '#' },
      { letter: '', letter_color: 'var(--olo-color-primary, #e1474f)', logo_image: '', top_bg: { type: 'image', image_url: demo('ciotola'), image_size: 'cover', image_position: 'center center' }, screenshot_label: '', brand_label: t('Porcellana'), brand_color: 'var(--olo-color-primary, #e1474f)', show_badge: false, badge_text: '', badge_bg: 'var(--olo-color-primary, #e1474f)', badge_color: 'var(--olo-color-primary-contrast, #ffffff)', title: t('Ciotola '), title_accent: t('Mare'), title_accent_italic: true, description: t('Porcellana dipinta a mano, diametro 16 cm.'), price: '26 €', cta_text: t('Scopri'), cta_url: '#' },
      { letter: '', letter_color: 'var(--olo-color-primary, #e1474f)', logo_image: '', top_bg: { type: 'image', image_url: demo('tavola'), image_size: 'cover', image_position: 'center center' }, screenshot_label: '', brand_label: t('Tavola'), brand_color: 'var(--olo-color-primary, #e1474f)', show_badge: false, badge_text: '', badge_bg: 'var(--olo-color-primary, #e1474f)', badge_color: 'var(--olo-color-primary-contrast, #ffffff)', title: t('Servizio '), title_accent: t('Terra'), title_accent_italic: true, description: t('Due piatti e un calice in gres smaltato.'), price: '64 €', cta_text: t('Scopri'), cta_url: '#' },
      { letter: '', letter_color: 'var(--olo-color-primary, #e1474f)', logo_image: '', top_bg: { type: 'image', image_url: demo('caffe'), image_size: 'cover', image_position: 'center center' }, screenshot_label: '', brand_label: t('Colazione'), brand_color: 'var(--olo-color-primary, #e1474f)', show_badge: false, badge_text: '', badge_bg: 'var(--olo-color-primary, #e1474f)', badge_color: 'var(--olo-color-primary-contrast, #ffffff)', title: t('Tazza '), title_accent: t('Aurora'), title_accent_italic: true, description: t('Tazza da cappuccino con piattino, 250 ml.'), price: '18 €', cta_text: t('Scopri'), cta_url: '#' },
    ],
    card_bg: { type: 'solid', color: 'var(--olo-color-surface, #ffffff)' },
    card_color: 'var(--olo-color-dark, #16263d)',
    top_aspect_ratio: '4/3',
    show_screenshot_label: false,
    title_size: 26,
  },

  // ═══ CONTENUTO ═══════════════════════════════════════════════
  // 16 nov 2026 (episodio 29): nomi in italiano (Card, Brand label, Badge testo, CTA testo, CTA URL, Label outline…),
  // spiegazioni nelle (i), la foto in alto nel Contenuto (è il prodotto, non uno stile: stava nello specchio dello
  // Stile), «Scheda» nuova coi suoi valori (la chiave era `defaults`, che il ripetitore non legge) e lo Stile nelle zone.
  fields: [
    { type: 'separator', label: t('Schede') },
    { key: 'items', label: t('Schede'), type: 'content-items', supportsDynamic: true,
      itemLabel: t('Scheda'), etichettaDa: 'title', miniaturaDa: 'logo_image',
      // Scheda aggiunta: colori dal tema (primario, tinta tenue), testi neutri in italiano.
      newItemDefaults: { letter: 'A', letter_color: 'var(--olo-color-primary, #e1474f)', logo_image: '', top_bg: { type: 'solid', color: 'var(--olo-color-muted, #f6f7f9)' }, screenshot_label: '', brand_label: t('Categoria'), brand_color: 'var(--olo-color-primary, #e1474f)', show_badge: false, badge_text: '', badge_bg: 'var(--olo-color-primary, #e1474f)', badge_color: 'var(--olo-color-primary-contrast, #ffffff)', title: t('Nuovo '), title_accent: t('prodotto'), title_accent_italic: true, description: t('Una riga che racconta il prodotto.'), price: '', cta_text: t('Scopri'), cta_url: '#' },
      itemFields: [
        { key: 'top_bg',              label: t('Sfondo / media in alto'), type: 'background', showParallax: false,
          description: t('La metà alta della scheda: la foto del prodotto, o una tinta o una sfumatura con la lettera o il logo sopra.') },
        { key: 'hover_image',         label: t('Seconda foto al passaggio'), type: 'image',
          description: t('Col mouse sopra la scheda, la foto lascia il posto a questa (un altro lato del prodotto, il dettaglio, l\'abbinamento). Sul telefono resta la prima.') },
        { key: 'letter',              label: t('Lettera'), type: 'text',
          description: t('Una lettera grande sopra lo sfondo in alto, come un monogramma. Vuota: niente.') },
        { key: 'logo_image',          label: t('Logo'), type: 'image',
          description: t('Al posto della lettera.') },
        { key: 'screenshot_label',    label: t('Scritta in alto'), type: 'text',
          description: t('Una piccola scritta a filo, sopra la foto o la lettera.') },
        { key: 'brand_label',         label: t('Marca o categoria'), type: 'text' },
        { key: 'show_badge',          label: t('Mostra il badge'),  type: 'toggle' },
        { key: 'badge_text',          label: t('Testo del badge'),  type: 'text', condition: { field: 'show_badge', op: 'eq', value: true } },
        { key: 'title',               label: t('Titolo'),      type: 'text' },
        { key: 'title_accent',        label: t('Seconda parte del titolo'), type: 'text',
          description: t('Attaccata al titolo e, se vuoi, in corsivo (Stile → Ogni scheda).') },
        { key: 'description',         label: t('Descrizione'),        type: 'editor', mode: 'inline' },
        { key: 'price',               label: t('Prezzo'),             type: 'text' },
        { key: 'cta_text',            label: t('Testo del pulsante'), type: 'text' },
        { key: 'cta_url',             label: t('Collegamento'),       type: 'link' },
      ],
    },

    { type: 'separator', label: t('Visibilità') },
    { key: 'show_screenshot_label', label: t('Mostra la scritta in alto'), type: 'toggle' },
    { key: 'cta_arrow',             label: t('Freccia accanto al pulsante'), type: 'toggle' },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  // Zone: Aspetto · Testo · Forma · Disposizione · Ogni scheda.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'card_bg',      label: t('Sfondo scheda'),     type: 'background', showParallax: false },
    { key: 'card_color',   label: t('Colore testo'),    type: 'color' },
    { key: 'card_shadow', label: t('Ombra scheda'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm',   label: t('Leggera') },
      { value: 'md',   label: t('Media') },
      { value: 'lg',   label: t('Forte') },
      { value: 'xl',   label: t('Molto forte') },
      { value: 'custom', label: t('Personalizzata') },
    ]},
    { key: 'card_shadow_custom', label: t('Ombra personalizzata'), type: 'box-shadow',
      legacyKeys: { h: 'card_shadow_h', v: 'card_shadow_v', blur: 'card_shadow_blur', spread: 'card_shadow_spread', color: 'card_shadow_color', inset: 'card_shadow_inset' },
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_border', label: t('Bordo scheda'), type: 'border' },
    { key: 'card_hover_effect', label: t('Al passaggio'), type: 'select', options: [
      { value: 'none',  label: t('Nessuno') },
      { value: 'lift',  label: t('Sollevamento') },
      { value: 'scale', label: t('Scala') },
      { value: 'tilt',  label: t('Inclinazione 3D') },
    ]},
    { key: 'screenshot_label_color', label: t('Colore scritta in alto'), type: 'color',
      condition: { field: 'show_screenshot_label', op: 'neq', value: false } },

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Lettera'), responsiveKeys: [], keys: { size: 'letter_size', italic: 'letter_italic', family: 'letter_font_family' }, sizeMin: 40, sizeMax: 280, sizeStep: 4 },
    { type: 'typography', label: t('Marca'), responsiveKeys: [], keys: { size: 'brand_size', letterSpacing: 'brand_letter_spacing' }, sizeMin: 10, sizeMax: 22,
      letterSpacingUnit: 'em' },   // il renderer la scrive in em (0–0,3): il controllo la mostrava in px
    { type: 'typography', label: t('Titolo'), responsiveKeys: [], keys: { size: 'title_size', weight: 'title_weight', family: 'title_font_family' }, sizeMin: 16, sizeMax: 80, sizeStep: 2 },
    { type: 'typography', label: t('Descrizione'), responsiveKeys: [], keys: { size: 'description_size' }, sizeMin: 11, sizeMax: 22 },
    // Vuoto = colore del testo della scheda.
    { type: 'typography', label: t('Prezzo'), responsiveKeys: [], keys: { size: 'price_size', color: 'price_color' }, sizeMin: 12, sizeMax: 40 },
    { type: 'typography', label: t('Pulsante'), responsiveKeys: [], keys: { size: 'cta_size' }, sizeMin: 9, sizeMax: 16 },

    { type: 'separator', label: t('Forma') },
    { key: 'card_padding', label: t('Padding scheda'), type: 'spacing', min: 0, max: 80 },
    withHover({ key: 'card_radius', label: t('Raggio scheda'), type: 'border-radius' }, { hoverKey: 'card_radius_hover', hoverDurationKey: 'card_radius_hover_duration' }),
    // Senza voce «Auto»: la metà alta è un riquadro con sfondo (colore o immagine),
    // non un <img>, e senza aspect-ratio non avrebbe nessuna altezza — la card si
    // schiaccerebbe sul testo. I cinque rapporti di prima sono tutti nel set canonico.
    { key: 'top_aspect_ratio', label: t('Proporzioni in alto'), type: 'select',
      options: ratioOptions({ auto: false }) },
    { key: 'top_padding', label: t('Padding in alto'), type: 'spacing', min: 0, max: 80 },
    { key: 'logo_height',   label: t('Altezza logo'),       type: 'range', min: 16, max: 160, step: 2, unit: 'px' },

    { type: 'separator', label: t('Disposizione') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 6, step: 1,
      description: t('Al massimo: se le schede resterebbero più strette di 220 px, in una colonna stretta della pagina, la griglia ne mette meno da sola.') },
    { key: 'columns_mobile', label: t('Colonne sul telefono'), type: 'range', min: 1, max: 2, step: 1,
      description: t('Sotto i 700 px. Due vanno bene per schede brevi (copertine, prodotti col solo nome e il prezzo).') },
    { key: 'gap',     label: t('Gap'), type: 'range', min: 0, max: 60, step: 2, unit: 'px' },
    { key: 'letter_align', label: t('Allineamento lettera'), type: 'select', options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centrato') },
      { value: 'right',  label: t('Destra') },
    ]},

    { type: 'separator', label: t('Ogni scheda') },
    { key: 'items', type: 'content-items', label: t('Schede'), itemLabel: t('Scheda'), etichettaDa: 'title', miniaturaDa: 'logo_image', itemFields: [
        { key: 'letter_color',        label: t('Colore lettera'),     type: 'color' },
        { key: 'brand_color',         label: t('Colore marca'),       type: 'color' },
        { key: 'badge_bg',            label: t('Sfondo badge'),       type: 'color' },
        { key: 'badge_color',         label: t('Colore testo badge'), type: 'color' },
        { key: 'title_accent_italic', label: t('Seconda parte in corsivo'), type: 'toggle' },
    ] },
  ],
};
