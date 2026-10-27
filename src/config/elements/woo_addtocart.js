
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WooCommerce Aggiungi al Carrello — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → testo pulsante, toggle visibilita (icona/quantita), stile quantita, icona
 *   styleFields[] → preset, sfondo, tipografia, stile/dimensione, border-radius, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_addtocart',
  name: t('Aggiungi al Carrello'),
  icon: 'dashicons-cart',
  category: 'woocommerce',
  placeholder: t('Pulsante aggiungi al carrello WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    button_text: 'Aggiungi al carrello',
    show_quantity: true,
    show_icon: true,
    icon: 'cart',
    style: 'filled',
    size: 'medium',
    full_width: false,
    bg_color: '',
    text_color: '',
    hover_bg: '',
    hover_text: '',
    border_radius: '6',
    quantity_style: 'input',
    testo_senza_prezzo: 'Richiedi informazioni',
    link_senza_prezzo: '',
    parametro_senza_prezzo: 'prodotto',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Come nasce dalla palette (in una scheda prodotto): quantità coi pulsanti − e + accanto a un
  // pulsante grande nel colore del sito con l'icona della borsa, che al passaggio si scurisce.
  partenza: {
    quantity_style: 'stepper',
    icon: 'bag',
    size: 'large',
    border_radius: '10',
    hover_bg: 'color-mix(in srgb, var(--olo-color-primary) 80%, var(--olo-color-dark))',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // Il prodotto a cui si riferisce: senza, la tile funzionava solo nella scheda di un prodotto.
    { type: 'separator', label: t('Prodotto') },
    { key: 'product_id', label: t('ID prodotto'), type: 'number', min: 0, step: 1,
      description: t("Il prodotto a cui si riferisce la tile, per usarla fuori dalla sua scheda (una pagina di lancio). L'ID si legge in Prodotti, passando sul nome. Vuoto o 0 = il prodotto della pagina.") },

    { type: 'separator', label: t('Pulsante') },
    { key: 'button_text', label: t('Testo pulsante'), type: 'text' },
    { key: 'show_quantity', label: t('Mostra quantita'), type: 'toggle' },
    { key: 'show_icon', label: t('Mostra icona'), type: 'toggle' },
    { key: 'icon', label: t('Icona'), type: 'select', options: [
      { value: 'cart', label: t('Carrello') },
      { value: 'bag', label: t('Borsa') },
      { value: 'plus', label: t('Piu') },
    ], condition: { field: 'show_icon', value: true } },

    // Un prodotto senza prezzo non si compra: al posto di «Aggiungi» un pulsante di richiesta.
    { type: 'separator', label: t('Prodotto senza prezzo') },
    { key: 'testo_senza_prezzo', label: t('Testo del pulsante di richiesta'), type: 'text' },
    { key: 'link_senza_prezzo', label: t('Collegamento della richiesta'), type: 'link',
      description: t('La pagina o la sezione col modulo («#richiesta»). Il pulsante compare al posto di «Aggiungi al carrello» quando il prodotto non ha prezzo; senza collegamento non compare niente.') },
    { key: 'parametro_senza_prezzo', label: t('Porta il nome nel campo'), type: 'text', placeholder: 'prodotto',
      description: t("Il nome del parametro dell'indirizzo che il campo del modulo legge («Valore dall'indirizzo»): chi arriva trova il nome del prodotto già scritto. Vuoto: il link resta com'è.") },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'editorial-serif', label: t('Editorial Serif') },
      { value: 'compact-inline',  label: t('Compact Inline') },
      { value: 'glass-frosted',   label: t('Glass Frosted') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ] },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { key: 'quantity_style', label: t('Stile quantita'), type: 'select', options: [
      { value: 'input', label: t('Campo numerico') },
      { value: 'stepper', label: t('Stepper +/-') },
    ], condition: { field: 'show_quantity', value: true } },
    { type: 'separator', label: t('Pulsante') },
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'filled', label: t('Pieno') },
      { value: 'outline', label: t('Contorno') },
      { value: 'text', label: t('Solo testo') },
    ]},
    { key: 'size', label: t('Dimensione'), type: 'select', options: [
      { value: 'small', label: t('Piccolo') },
      { value: 'medium', label: t('Medio') },
      { value: 'large', label: t('Grande') },
    ]},
    { key: 'full_width', label: t('Larghezza piena'), type: 'toggle' },
    withHover({ key: 'border_radius', label: t('Raggio'), type: 'border-radius' }),
    // Il testo del pulsante: misura, peso, maiuscolo e spaziatura come il Pulsante. Vuoti = come prima
    // (la misura della «Dimensione», peso 600).
    { type: 'typography', label: t('Testo'),
      linkedPresetKey: 'typography_preset',
      linkedPresetGoverns: ['family', 'weight'],
      responsiveKeys: [],
      keys: {
        family:        'font_family',
        size:          'font_size',
        weight:        'font_weight',
        transform:     'text_transform',
        letterSpacing: 'letter_spacing',
      },
      sizeMin: 10, sizeMax: 28,
      letterSpacingUnit: 'px',
    },

    { type: 'separator', label: t('Colori') },
    withHover({ key: 'bg_color', label: t('Sfondo pulsante'), type: 'color' }, { hoverKey: 'hover_bg', defaultDuration: 200 }),
    withHover({ key: 'text_color', label: t('Colore testo'), type: 'color' }, { hoverKey: 'hover_text', defaultDuration: 200 }),
    ...borderFields(),
  ],
};
