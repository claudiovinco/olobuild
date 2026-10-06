<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Builder — «Preventivo»: righe con stepper +/− e totale live.
 * Estratto dai demo OLOthemes (setupBuilder). Token-first: `zone_accent`.
 * Runtime inline scoped, senza '&&' né '<'/'>'.
 *
 * Dalla 1.4.508 la scelta parte davvero (`send_mode`): per email (mailto con l'elenco),
 * su WhatsApp (wa.me), verso una pagina (?scelta=…&totale=…) o nel carrello WooCommerce
 * (`product_id` per riga). Senza la chiave (tile salvate prima) resta il collegamento di
 * sempre a `cta_url`. Il pulsante è spento finché non si sceglie niente; i prezzi
 * seguono il formato della lingua della pagina (prima en-US e senza decimali).
 */
class Olobuild_Builder_Tile extends Olobuild_Tile_Base {

    protected $type     = 'builder';
    protected $name     = 'Preventivo';
    protected $icon     = 'dashicons-cart';
    protected $category = 'interactive';
    protected $defaults = [
        'eyebrow'     => 'Preventivo',
        'heading'     => 'Componi la tua richiesta',
        'intro'       => '',
        'currency'    => '€',
        'cap'         => 0,
        'items'       => [
            [ 'name' => 'Servizio base', 'price' => '120', 'note' => '', 'start' => 1 ],
            [ 'name' => 'Ora aggiuntiva', 'price' => '45', 'note' => '', 'start' => 0 ],
            [ 'name' => 'Trasferta', 'price' => '30', 'note' => '', 'start' => 0 ],
        ],
        'total_label' => 'Totale',
        'count_label' => 'voci',
        'count_label_one' => '',
        'cta_text'    => 'Richiedi il preventivo',
        'cta_url'     => '',
        'send_mode'   => 'mail',
        'send_to'     => '',
        'send_subject'=> 'Richiesta di preventivo',
        'send_intro'  => 'Vorrei un preventivo per:',
        'zone_accent' => '',
        'zone_on'     => 'var(--olo-color-surface, #ffffff)',
        'card_bg'     => '',
        'card_border' => '',
        'align'       => 'left',
        'layout'          => 'panel',
        'heading_accent'  => '',
        'heading_color'   => '',
        'tally_bg'        => '',
        'item_name_color' => '',
        'item_price_color'=> '',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'obd-' . wp_rand( 10000, 99999 );

        $accent = $this->safe_color_css( $s['zone_accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $on     = $this->safe_color_css( $s['zone_on'] ?? '' ) ?: '#ffffff';
        $cardbg = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'var(--olo-color-surface-alt, #f6f7f9)';
        $cardbd = Olobuild_Tile_Utils::border_color( $s['card_border'] ?? null, 'var(--olo-color-border, #e5e7eb)' );
        $center = ( ( $s['align'] ?? 'left' ) === 'center' );
        $serif  = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
        $sans   = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
        $cur    = (string) ( $s['currency'] ?? '' );
        $cap    = intval( $s['cap'] ?? 0 );
        $layout = ( ( $s['layout'] ?? 'panel' ) === 'split' ) ? 'split' : 'panel';
        $disp   = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
        $hcol   = $this->safe_color_css( $s['heading_color'] ?? '' ) ?: 'var(--olo-color-text,#111827)';
        $tally  = $this->safe_color_css( $s['tally_bg'] ?? '' ) ?: 'var(--olo-color-text,#111827)';
        $inm    = $this->safe_color_css( $s['item_name_color'] ?? '' ) ?: 'var(--olo-color-text,#111827)';
        $ipr    = $this->safe_color_css( $s['item_price_color'] ?? '' ) ?: $accent;
        $hacc   = (string) ( $s['heading_accent'] ?? '' );
        // Il conteggio ha due forme: prima con una voce scelta usciva «1 voci». Senza la forma
        // singolare (tile salvate prima, campo vuoto) la si ricava per le etichette comuni;
        // per le altre resta il plurale, come prima.
        $conta_molti = (string) ( $s['count_label'] ?? '' );
        $conta_uno   = trim( (string) ( $s['count_label_one'] ?? '' ) );
        if ( '' === $conta_uno ) {
            $singolari = [
                'voci' => 'voce', 'scelte' => 'scelta', 'articoli' => 'articolo', 'servizi' => 'servizio',
                'prodotti' => 'prodotto', 'elementi' => 'elemento', 'pezzi' => 'pezzo', 'ospiti' => 'ospite',
                'persone' => 'persona', 'notti' => 'notte', 'ore' => 'ora', 'giorni' => 'giorno', 'camere' => 'camera',
                'posti' => 'posto', 'biglietti' => 'biglietto', 'lezioni' => 'lezione', 'items' => 'item',
            ];
            $chiave_conta = strtolower( trim( $conta_molti ) );
            $conta_uno    = $conta_molti;
            if ( isset( $singolari[ $chiave_conta ] ) ) {
                $conta_uno = $singolari[ $chiave_conta ];
                if ( preg_match( '/^[A-Z]/', trim( $conta_molti ) ) ) {
                    $conta_uno = ucfirst( $conta_uno );
                }
            }
        }
        $conta_attr = ' data-bd-count-label data-uno="' . esc_attr( $conta_uno ) . '" data-molti="' . esc_attr( $conta_molti ) . '"';

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) return '';

        // ── Invio della scelta. Senza la chiave (tile salvate prima): il collegamento di sempre.
        $modo = isset( $settings['send_mode'] ) ? (string) $settings['send_mode'] : '';
        if ( ! in_array( $modo, [ '', 'mail', 'whatsapp', 'page', 'woocommerce' ], true ) ) {
            $modo = '';
        }
        if ( 'woocommerce' === $modo ) {
            if ( ! class_exists( 'WooCommerce' ) ) { $modo = ''; }
        }
        $dest  = trim( (string) ( $s['send_to'] ?? '' ) );
        $href  = '';
        $extra = '';
        if ( '' === $modo ) {
            $href = (string) ( $s['cta_url'] ?: '#' );
        } elseif ( 'mail' === $modo ) {
            $dest = sanitize_email( $dest );
            $href = '' !== $dest ? 'mailto:' . $dest : '';
        } elseif ( 'whatsapp' === $modo ) {
            $dest = preg_replace( '/\D+/', '', $dest );
            $href = '' !== $dest ? 'https://wa.me/' . $dest : '';
        } elseif ( 'page' === $modo ) {
            $href = $dest;
        } else {
            $href  = wc_get_cart_url();
            $extra = ' data-bd-ajax="' . esc_url( WC_AJAX::get_endpoint( 'add_to_cart' ) ) . '"';
        }
        $avviso = '';
        if ( '' === $href ) {
            // Manca la destinazione: al pubblico niente pulsante, all'autore un avviso.
            if ( ! empty( $s['_builder_mode'] ) ) {
                $avviso = olobuild_t( "Scrivi dove inviare la scelta (indirizzo, numero o pagina): finché manca, il pulsante non compare sul sito." );
            }
        }
        $cta_attr = '';
        if ( '' !== $href ) {
            $cta_attr = ' href="' . esc_url( $href, '' === $modo ? null : [ 'http', 'https', 'mailto' ] ) . '" data-bd-cta data-mode="' . esc_attr( $modo ) . '"'
                . ' data-subject="' . esc_attr( (string) ( $s['send_subject'] ?? '' ) ) . '"'
                . ' data-intro="' . esc_attr( (string) ( $s['send_intro'] ?? '' ) ) . '"'
                . ' data-total-label="' . esc_attr( (string) ( $s['total_label'] ?? 'Totale' ) ) . '"' . $extra;
        }

        ob_start();
        if ( $layout === 'split' ) : ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css() for every colour, fixed font-stack literals, internal wp_rand() uid). ?>
        <style>
            .<?php echo $uid; ?>{font-family:<?php echo $sans; ?>;}
            .<?php echo $uid; ?> .obds-eyebrow{font-size:12px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:<?php echo $accent; ?>;display:block;margin-bottom:10px;}
            .<?php echo $uid; ?> .obds-head{display:flex;justify-content:space-between;align-items:flex-end;gap:28px;flex-wrap:wrap;margin-bottom:26px;}
            .<?php echo $uid; ?> .obds-h{font-family:<?php echo $disp; ?>;font-weight:800;font-size:clamp(32px,4.6vw,56px);line-height:1.02;letter-spacing:-.01em;text-transform:uppercase;margin:0;color:<?php echo $hcol; ?>;}
            .<?php echo $uid; ?> .obds-h .acc{color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .obds-intro{color:var(--olo-color-text-muted,#6b7280);font-size:15.5px;line-height:1.6;margin:10px 0 0;max-width:440px;}
            .<?php echo $uid; ?> .obds-tally{display:flex;align-items:center;gap:22px;background:<?php echo $tally; ?>;color:<?php echo $accent; ?>;border-radius:10px;padding:14px 24px;white-space:nowrap;}
            .<?php echo $uid; ?> .obds-tally .cnt b{font-family:<?php echo $disp; ?>;font-weight:800;font-size:28px;line-height:1;color:var(--olo-color-surface,#fff);}
            .<?php echo $uid; ?> .obds-tally .cnt span{font-weight:600;font-size:13px;opacity:.75;}
            .<?php echo $uid; ?> .obds-tally .tot{font-family:<?php echo $disp; ?>;font-weight:800;font-size:28px;line-height:1;border-left:1px solid color-mix(in srgb, <?php echo $accent; ?> 30%, transparent);padding-left:22px;}
            .<?php echo $uid; ?> .obds-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
            .<?php echo $uid; ?> .obds-item{display:flex;align-items:center;justify-content:space-between;gap:16px;background:<?php echo $cardbg; ?>;<?php echo esc_attr( Olobuild_Tile_Utils::border_css( $s['card_border'] ?? null, [ 'width' => 1, 'color' => $cardbd ] ) ); ?>border-radius:10px;padding:16px 20px;transition:border-color .25s,box-shadow .25s;}
            .<?php echo $uid; ?> .obds-item.on{border-color:<?php echo $accent; ?>;box-shadow:0 12px 28px -18px rgba(0,0,0,.4);}
            .<?php echo $uid; ?> .obds-meta h3{font-family:<?php echo $disp; ?>;font-weight:700;font-size:18px;text-transform:uppercase;letter-spacing:.01em;color:<?php echo $inm; ?>;margin:0;}
            .<?php echo $uid; ?> .obds-meta .no{font-size:12px;color:var(--olo-color-text-muted,#8a948d);margin-top:2px;}
            .<?php echo $uid; ?> .obds-r{display:flex;align-items:center;gap:14px;flex:0 0 auto;}
            .<?php echo $uid; ?> .obds-r .pr{font-family:<?php echo $disp; ?>;font-weight:700;font-size:18px;color:<?php echo $ipr; ?>;}
            .<?php echo $uid; ?> .obds-step{display:inline-flex;align-items:center;gap:8px;border:1.5px solid <?php echo $cardbd; ?>;border-radius:8px;padding:4px 6px;}
            .<?php echo $uid; ?> .obds-step button{width:28px;height:28px;border:0;background:transparent;color:<?php echo $inm; ?>;font-size:18px;line-height:1;cursor:pointer;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;transition:background .15s;}
            .<?php echo $uid; ?> .obds-step button:hover{background:color-mix(in srgb, <?php echo $accent; ?> 18%, transparent);}
            .<?php echo $uid; ?> .obds-step button:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:2px;}
            .<?php echo $uid; ?> .obds-step [data-bd-c]{min-width:18px;text-align:center;font-weight:700;font-variant-numeric:tabular-nums;color:<?php echo $inm; ?>;}
            @media(max-width:680px){.<?php echo $uid; ?> .obds-grid{grid-template-columns:1fr;}}
            .<?php echo $uid; ?> .obds-foot{display:flex;justify-content:flex-end;margin-top:1.25rem;}
            .<?php echo $uid; ?> .obds-cta{display:inline-flex;align-items:center;gap:0.5rem;font-weight:700;font-size:14.5px;color:<?php echo $on; ?>;background:<?php echo $accent; ?>;padding:0.8125rem 1.625rem;border-radius:0.625rem;text-decoration:none;}
            .<?php echo $uid; ?> .obds-cta:focus-visible,.<?php echo $uid; ?> .obd-cta:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> [data-bd-cta].off{opacity:.45;pointer-events:none;}
            .<?php echo $uid; ?> .obd-avviso{margin-top:0.75rem;font-size:13px;opacity:.8;}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-builder <?php echo esc_attr( $uid ); ?>" data-builder data-cap="<?php echo esc_attr( $cap ); ?>" data-currency="<?php echo esc_attr( $cur ); ?>">
            <div class="obds-head">
                <div>
                    <?php if ( $s['eyebrow'] !== '' ) : ?><span class="obds-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
                    <?php if ( $s['heading'] !== '' ) : ?><h2 class="obds-h"><?php echo esc_html( $s['heading'] ); ?><?php if ( $hacc !== '' ) : ?> <span class="acc"><?php echo esc_html( $hacc ); ?></span><?php endif; ?></h2><?php endif; ?>
                    <?php if ( $s['intro'] !== '' ) : ?><p class="obds-intro"><?php echo esc_html( $s['intro'] ); ?></p><?php endif; ?>
                </div>
                <div class="obds-tally">
                    <div class="cnt"><b data-bd-count-out>0</b> <span<?php echo $conta_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes built above with esc_attr() ?>><?php echo esc_html( $conta_molti ); ?></span></div>
                    <div class="tot" data-bd-total><?php echo esc_html( $cur . '0' ); ?></div>
                </div>
            </div>
            <div class="obds-grid">
                <?php foreach ( $items as $i => $it ) :
                    $start = max( 0, intval( $it['start'] ?? 0 ) );
                    $price = is_numeric( $it['price'] ?? '' ) ? $it['price'] : '0';
                    // Display: stringhe esistenti raw (render identico); Number (field number editor) a 2 decimali — come BuilderTile.vue.
                    $price_disp = ( is_int( $price ) || is_float( $price ) ) ? number_format( (float) $price, 2, '.', '' ) : $price;
                ?>
                    <div class="obds-item<?php echo $start > 0 ? ' on' : ''; ?>" data-bd-item data-n="<?php echo intval( $start ); ?>" data-price="<?php echo esc_attr( $price ); ?>" data-name="<?php echo esc_attr( $it['name'] ?? '' ); ?>" data-product="<?php echo esc_attr( absint( $it['product_id'] ?? 0 ) ); ?>">
                        <div class="obds-meta"><h3><?php echo esc_html( $it['name'] ?? '' ); ?></h3><?php if ( ! empty( $it['note'] ) ) : ?><span class="no"><?php echo esc_html( $it['note'] ); ?></span><?php endif; ?></div>
                        <div class="obds-r">
                            <span class="pr"><?php echo esc_html( $cur . $price_disp ); ?></span>
                            <div class="obds-step"><button type="button" data-bd-dec aria-label="-">&minus;</button><span data-bd-c><?php echo intval( $start ); ?></span><button type="button" data-bd-inc aria-label="+">+</button></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if ( '' !== $cta_attr && '' !== $modo ) : // nello split le tile di prima non avevano il pulsante ?>
                <?php if ( ! empty( $s['cta_text'] ) ) : ?><div class="obds-foot"><a class="obds-cta"<?php echo $cta_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes built above with esc_url()/esc_attr() ?>><?php echo esc_html( $s['cta_text'] ); ?></a></div><?php endif; ?>
            <?php elseif ( '' !== $avviso ) : ?>
                <p class="obd-avviso"><?php echo esc_html( $avviso ); ?></p>
            <?php endif; ?>
        </div>
        <?php else : ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css() for every colour, fixed font-stack/alignment literals, internal wp_rand() uid). ?>
        <style>
            .<?php echo $uid; ?>{ --bd-accent:<?php echo $accent; ?>; --bd-on:<?php echo $on; ?>; font-family:<?php echo $sans; ?>; <?php if ( $center ) echo 'text-align:center;'; ?> }
            .<?php echo $uid; ?> .obd-eyebrow{font-size:12px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--bd-accent);display:block;margin-bottom:10px;}
            .<?php echo $uid; ?> .obd-h{font-family:<?php echo $serif; ?>;font-size:clamp(26px,3.6vw,42px);line-height:1.12;margin:0;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .obd-intro{font-size:15.5px;line-height:1.6;opacity:.8;margin:14px 0 0;max-width:560px;<?php echo $center ? 'margin-left:auto;margin-right:auto;' : ''; ?>}
            .<?php echo $uid; ?> .obd-panel{margin-top:26px;background:<?php echo $cardbg; ?>;<?php echo esc_attr( Olobuild_Tile_Utils::border_css( $s['card_border'] ?? null, [ 'width' => 1, 'color' => $cardbd ] ) ); ?>border-radius:16px;padding:clamp(20px,3vw,32px);text-align:left;<?php echo $center ? 'max-width:640px;margin-left:auto;margin-right:auto;' : ''; ?>}
            .<?php echo $uid; ?> .obd-row{display:flex;align-items:center;gap:16px;padding:16px 0;border-top:1px solid <?php echo $cardbd; ?>;}
            .<?php echo $uid; ?> .obd-row:first-child{border-top:0;}
            .<?php echo $uid; ?> .obd-row.on{}
            .<?php echo $uid; ?> .obd-row__main{flex:1;min-width:0;}
            .<?php echo $uid; ?> .obd-row__name{font-weight:600;font-size:15.5px;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .obd-row__note{font-size:13px;opacity:.6;margin-top:2px;}
            .<?php echo $uid; ?> .obd-row__price{font-weight:600;font-size:14.5px;color:var(--bd-accent);white-space:nowrap;}
            .<?php echo $uid; ?> .obd-step{display:inline-flex;align-items:center;gap:12px;}
            .<?php echo $uid; ?> .obd-step button{width:32px;height:32px;border-radius:50%;<?php echo esc_attr( Olobuild_Tile_Utils::border_css( $s['card_border'] ?? null, [ 'width' => 1, 'color' => $cardbd ] ) ); ?>background:transparent;color:var(--olo-color-text,#111827);font-size:18px;line-height:1;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .15s;}
            .<?php echo $uid; ?> .obd-step button:hover{border-color:var(--bd-accent);color:var(--bd-accent);}
            .<?php echo $uid; ?> .obd-step button:focus-visible{outline:2px solid var(--bd-accent);outline-offset:2px;}
            .<?php echo $uid; ?> .obd-step [data-bd-c]{min-width:20px;text-align:center;font-weight:700;font-variant-numeric:tabular-nums;}
            .<?php echo $uid; ?> .obd-foot{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-top:22px;padding-top:20px;border-top:2px solid <?php echo $cardbd; ?>;}
            .<?php echo $uid; ?> .obd-tot{font-family:<?php echo $serif; ?>;}
            .<?php echo $uid; ?> .obd-tot b{font-size:clamp(26px,3.4vw,38px);color:var(--olo-color-text,#111827);font-variant-numeric:tabular-nums;}
            <?php /* Solo la riga dell'etichetta va a capo: con «.obd-tot span» anche il numero e la parola dentro erano blocchi, e «Totale · 3 voci» stava su tre righe. */ ?>
            .<?php echo $uid; ?> .obd-tot > span{font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;opacity:.6;display:block;}
            .<?php echo $uid; ?> .obd-cta{display:inline-flex;align-items:center;gap:8px;font-weight:600;font-size:14.5px;color:var(--bd-on);background:var(--bd-accent);padding:13px 26px;border-radius:999px;text-decoration:none;transition:transform .18s;}
            .<?php echo $uid; ?> .obd-cta:hover{transform:translateY(-1px);}
            .<?php echo $uid; ?> .obd-cta:focus-visible{outline:2px solid var(--bd-accent);outline-offset:3px;}
            .<?php echo $uid; ?> [data-bd-cta].off{opacity:.45;pointer-events:none;}
            .<?php echo $uid; ?> .obd-avviso{margin-top:0.75rem;font-size:13px;opacity:.8;}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-builder <?php echo esc_attr( $uid ); ?>" data-builder data-cap="<?php echo esc_attr( $cap ); ?>" data-currency="<?php echo esc_attr( $cur ); ?>">
            <?php if ( $s['eyebrow'] !== '' ) : ?><span class="obd-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
            <?php if ( $s['heading'] !== '' ) : ?><h2 class="obd-h"><?php echo esc_html( $s['heading'] ); ?></h2><?php endif; ?>
            <?php if ( $s['intro'] !== '' ) : ?><p class="obd-intro"><?php echo esc_html( $s['intro'] ); ?></p><?php endif; ?>
            <div class="obd-panel">
                <?php foreach ( $items as $i => $it ) :
                    $start = max( 0, intval( $it['start'] ?? 0 ) );
                    $price = is_numeric( $it['price'] ?? '' ) ? $it['price'] : '0';
                    // Display: stringhe esistenti raw (render identico); Number (field number editor) a 2 decimali — come BuilderTile.vue.
                    $price_disp = ( is_int( $price ) || is_float( $price ) ) ? number_format( (float) $price, 2, '.', '' ) : $price;
                ?>
                    <div class="obd-row<?php echo $start > 0 ? ' on' : ''; ?>" data-bd-item data-n="<?php echo intval( $start ); ?>" data-price="<?php echo esc_attr( $price ); ?>" data-name="<?php echo esc_attr( $it['name'] ?? '' ); ?>" data-product="<?php echo esc_attr( absint( $it['product_id'] ?? 0 ) ); ?>">
                        <div class="obd-row__main">
                            <div class="obd-row__name"><?php echo esc_html( $it['name'] ?? '' ); ?></div>
                            <?php if ( ! empty( $it['note'] ) ) : ?><div class="obd-row__note"><?php echo esc_html( $it['note'] ); ?></div><?php endif; ?>
                        </div>
                        <div class="obd-row__price"><?php echo esc_html( $cur . $price_disp ); ?></div>
                        <div class="obd-step">
                            <button type="button" data-bd-dec aria-label="-">&minus;</button>
                            <span data-bd-c><?php echo intval( $start ); ?></span>
                            <button type="button" data-bd-inc aria-label="+">+</button>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="obd-foot">
                    <div class="obd-tot">
                        <span><?php echo esc_html( $s['total_label'] ?? 'Totale' ); ?> · <span data-bd-count-out>0</span> <span<?php echo $conta_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes built above with esc_attr() ?>><?php echo esc_html( $conta_molti ); ?></span></span>
                        <b data-bd-total><?php echo esc_html( $cur . '0' ); ?></b>
                    </div>
                    <?php if ( ! empty( $s['cta_text'] ) ) : ?>
                        <?php if ( '' !== $cta_attr ) : ?><a class="obd-cta"<?php echo $cta_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes built above with esc_url()/esc_attr() ?>><?php echo esc_html( $s['cta_text'] ); ?></a><?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php if ( '' !== $avviso ) : ?><p class="obd-avviso"><?php echo esc_html( $avviso ); ?></p><?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <script>
        (function(){
            var root=document.querySelector('.<?php echo $uid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal 'obd-' . wp_rand() identifier. ?>[data-builder]'); if(!root){return;}
            var cap=parseInt(root.getAttribute('data-cap'))||0;
            var cur=root.getAttribute('data-currency')||'';
            var items=[].slice.call(root.querySelectorAll('[data-bd-item]'));
            /* Formato della lingua della pagina; i decimali solo se un prezzo li ha. */
            var decimali=items.some(function(it){ return (it.getAttribute('data-price')||'').indexOf('.')!==-1; });
            var fmt=new Intl.NumberFormat(document.documentElement.lang||undefined,{useGrouping:'always',minimumFractionDigits:decimali?2:0,maximumFractionDigits:2});
            var totalEl=root.querySelector('[data-bd-total]');
            var countEl=root.querySelector('[data-bd-count-out]');
            var contaEl=root.querySelector('[data-bd-count-label]');
            var cta=root.querySelector('[data-bd-cta]');
            var base=cta?cta.getAttribute('href'):'';
            function getN(it){ return parseInt(it.getAttribute('data-n'))||0; }
            var E=String.fromCharCode(38); /* la e commerciale, fuori dal testo dello script */
            /* L'elenco della scelta, una riga per voce: «Nome × 2 — €90». */
            function riepilogo(total){
                var righe=[];
                items.forEach(function(it){
                    var c=getN(it); if(c===0){ return; }
                    var p=parseFloat(it.getAttribute('data-price'))||0;
                    righe.push((it.getAttribute('data-name')||'')+' \u00d7 '+c+' \u2014 '+cur+fmt.format(c*p));
                });
                righe.push((cta.getAttribute('data-total-label')||'')+': '+cur+fmt.format(total));
                return righe;
            }
            function aggiornaCta(total,n){
                if(!cta){ return; }
                var modo=cta.getAttribute('data-mode')||'';
                if(modo===''){ return; }
                cta.classList.toggle('off', n===0);
                cta.setAttribute('aria-disabled', n===0?'true':'false');
                var intro=cta.getAttribute('data-intro')||'';
                var testo=(intro?intro+'\n\n':'')+riepilogo(total).join('\n');
                if(modo==='mail'){
                    cta.setAttribute('href', base+'?subject='+encodeURIComponent(cta.getAttribute('data-subject')||'')+E+'body='+encodeURIComponent(testo));
                } else if(modo==='whatsapp'){
                    cta.setAttribute('href', base+'?text='+encodeURIComponent(testo));
                } else if(modo==='page'){
                    var sep=base.indexOf('?')===-1?'?':E;
                    cta.setAttribute('href', base+sep+'scelta='+encodeURIComponent(riepilogo(total).slice(0,-1).join('; '))+E+'totale='+encodeURIComponent(String(Math.round(total*100)/100)));
                }
            }
            function recalc(){
                var total=0,n=0;
                items.forEach(function(it){
                    var c=getN(it), p=parseFloat(it.getAttribute('data-price'))||0;
                    total+=c*p; n+=c;
                    var cn=it.querySelector('[data-bd-c]'); if(cn){cn.textContent=c;}
                    it.classList.toggle('on', c!==0);
                });
                if(totalEl){totalEl.textContent=cur+fmt.format(total);}
                if(countEl){countEl.textContent=n;}
                /* «1 voce», «0 voci», «3 voci». */
                if(contaEl){contaEl.textContent=contaEl.getAttribute(n===1?'data-uno':'data-molti')||'';}
                aggiornaCta(total,n);
            }
            /* WooCommerce: ogni riga con un prodotto va nel carrello, poi si apre il carrello. */
            if(cta){
                if(cta.getAttribute('data-mode')==='woocommerce'){
                    cta.addEventListener('click',function(e){
                        e.preventDefault();
                        var url=cta.getAttribute('data-bd-ajax')||'';
                        var coda=items.filter(function(it){ return getN(it)!==0; }).filter(function(it){ return (parseInt(it.getAttribute('data-product'))||0)!==0; });
                        var vai=function(){ window.location.href=cta.getAttribute('href'); };
                        var passo=function(i){
                            if(i===coda.length){ vai(); return; }
                            var dati=new FormData();
                            dati.append('product_id', coda[i].getAttribute('data-product'));
                            dati.append('quantity', String(getN(coda[i])));
                            fetch(url,{method:'POST',body:dati,credentials:'same-origin'}).then(function(){ passo(i+1); },function(){ passo(i+1); });
                        };
                        passo(0);
                    });
                }
            }
            function setN(it,v){
                v=Math.max(0,v);
                if(cap!==0){
                    var others=0; items.forEach(function(x){ if(x!==it){ others+=getN(x); } });
                    v=Math.max(0, Math.min(v, cap-others));
                }
                it.setAttribute('data-n', v); recalc();
            }
            items.forEach(function(it){
                var dec=it.querySelector('[data-bd-dec]'), inc=it.querySelector('[data-bd-inc]');
                if(dec){dec.addEventListener('click',function(){ setN(it, getN(it)-1); });}
                if(inc){inc.addEventListener('click',function(){ setN(it, getN(it)+1); });}
            });
            recalc();
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
