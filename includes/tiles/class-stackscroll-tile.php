<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tile StackScroll — sezione con card `position:sticky` che si impilano.
 *
 * Effetto "Section · StackScroll" (rif. handoff-tile-speciali/temi/64-tema-pastificio.html,
 * blocco sticky stacking). Bucket C / famiglia B.
 *
 * Ogni card si "incolla" a `top_offset + i*top_step` px durante lo scroll: la successiva sale
 * sopra la precedente creando una pila. È SOLO CSS sticky — il runtime è un IIFE minimo che
 * degrada a flusso verticale normale quando lo sticky non è supportato o l'utente preferisce
 * meno movimento (prefers-reduced-motion).
 *
 * Contratto §2:
 *   - Parametrico: ogni numero/colore = setting con default; nessun hardcode.
 *   - UID scoped: ogni regola CSS è prefissata con .olo-stack-<id>; N istanze non si calpestano.
 *   - SSR: tutte le card sono stampate server-side e visibili senza JS.
 *   - prefers-reduced-motion / browser senza sticky → card una sotto l'altra (flusso normale).
 *   - Additivo: chiavi salvate invariate; riusa build_border_*_css come il marquee.
 */
class Olobuild_Stackscroll_Tile extends Olobuild_Tile_Base {

    protected $type     = 'stackscroll';
    protected $name     = 'Card impilate (StackScroll)';
    protected $icon     = 'dashicons-index-card';
    protected $category = 'layout';
    protected $defaults = [
        // Comportamento pila
        'top_offset'     => 90,
        'top_step'       => 20,
        'card_gap'       => 24,
        'scale_on_stack' => true,
        'scale_amount'   => 4,

        // Aspetto card
        'card_min_height'    => 420,
        'round'              => 20,
        'card_padding'       => 48,
        'media_position'     => 'right',
        'object_position'    => 'center center',
        'card_bg_default'    => '',
        'text_color_default' => '',
        'num_color_default'  => '',
        'show_number'        => true,

        // Ombra (preset shadowField)
        'shadow'        => 'custom',
        'shadow_h'      => '0',
        'shadow_v'      => '-10',
        'shadow_blur'   => '40',
        'shadow_spread' => '-20',
        'shadow_color'  => 'rgba(0,0,0,0.30)',
        'shadow_inset'  => false,

        // Bordo (sistema condiviso)
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,

        'cards' => [],
    ];

    public function get_controls() {
        return [];
    }

    /**
     * box-shadow dai campi shadow_* (preset shadowField). Stesso schema del grid tile.
     */
    protected function stack_shadow_css( $s ) {
        $val = $s['shadow'] ?? 'none';
        $map = [
            'sm' => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md' => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg' => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl' => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
        ];
        if ( isset( $map[ $val ] ) ) {
            return $map[ $val ];
        }
        if ( $val === 'custom' ) {
            $sh = intval( $s['shadow_h'] ?? 0 );
            $sv = intval( $s['shadow_v'] ?? 4 );
            $sb = intval( $s['shadow_blur'] ?? 10 );
            $ss = intval( $s['shadow_spread'] ?? 0 );
            $sc = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $si = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            return $si . $sh . 'px ' . $sv . 'px ' . $sb . 'px ' . $ss . 'px ' . $sc;
        }
        return '';
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-stack-' . wp_rand( 10000, 99999 );

        // ── Parametri pila (clampati) ──
        $top_offset = max( 0, min( 240, intval( $s['top_offset'] ) ) );
        $top_step   = max( 0, min( 80,  intval( $s['top_step'] ) ) );
        $card_gap   = max( 0, min( 80,  intval( $s['card_gap'] ) ) );
        $do_scale   = ! empty( $s['scale_on_stack'] );
        $scale_amt  = max( 1, min( 12, intval( $s['scale_amount'] ) ) ) / 100; // frazione per card

        // ── Aspetto ──
        $min_h     = max( 120, min( 900, intval( $s['card_min_height'] ) ) );
        $round_css = $this->build_border_radius_css( $s['round'] ) ?: '0'; // dual-format: Number legacy E oggetto {tl,tr,br,bl}
        $pad       = max( 8,   min( 120, intval( $s['card_padding'] ) ) );
        $media_pos = in_array( $s['media_position'], [ 'left', 'right', 'none' ], true ) ? $s['media_position'] : 'right';
        $has_media = $media_pos !== 'none';

        // Punto focale globale immagine card (object-position). Default 'center center' = resa attuale.
        $obj_pos = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) { $obj_pos = 'center center'; }

        $bg_def   = $this->safe_color_css( $s['card_bg_default'] )    ?: 'var(--olo-color-surface, #ffffff)';
        $txt_def  = $this->safe_color_css( $s['text_color_default'] ) ?: 'var(--olo-color-text, #1f2937)';
        $num_def  = $this->safe_color_css( $s['num_color_default'] )  ?: 'var(--olo-color-primary, #e1474f)';
        $show_num = ! empty( $s['show_number'] );
        // Titolo display (additivo, no-op di default): heading-font + clamp grande.
        // Default false → resa IDENTICA ai temi esistenti (font ereditato, clamp 28..48).
        $title_display = ! empty( $s['title_display'] );
        $title_font_css = $title_display ? 'font-family: var(--olo-font-family-heading, var(--olo-font-family, inherit));' : '';
        $title_size_css = $title_display ? 'clamp(40px, 4.4vw, 60px)' : 'clamp(28px, 3.4vw, 48px)';

        $shadow_css = $this->stack_shadow_css( $s );

        // ── Card ──
        $cards = is_array( $s['cards'] ) ? array_values( $s['cards'] ) : [];
        $total = count( $cards );

        // Grid del singolo .scard (testo + immagine)
        if ( $has_media ) {
            $grid_cols = $media_pos === 'left' ? '0.9fr 1.1fr' : '1.1fr 0.9fr';
        } else {
            $grid_cols = '1fr';
        }

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colors via the safe_color_css() whitelist (with var() fallbacks), integers via intval()/absint() with min()/max() clamps, radius via build_border_radius_css() (integer-forced), shadow via stack_shadow_css() (fixed preset map or intval+safe_color_css), grid columns and media order from fixed in_array()-whitelisted literals; $uid is internally generated.
        ?>
        <style>
            .<?php echo $uid; ?> { position: relative; }

            .<?php echo $uid; ?> .olo-stack__list {
                position: relative;
            }

            .<?php echo $uid; ?> .scard {
                position: -webkit-sticky;
                position: sticky;
                display: grid;
                grid-template-columns: <?php echo $grid_cols; ?>;
                min-height: <?php echo $min_h; ?>px;
                margin-bottom: <?php echo $card_gap; ?>px;
                border-radius: <?php echo $round_css; ?>;
                overflow: hidden;
                background: <?php echo $bg_def; ?>;
                color: <?php echo $txt_def; ?>;
                <?php if ( $shadow_css ) : ?>box-shadow: <?php echo $shadow_css; ?>;<?php endif; ?>
                transform-origin: center top;
                will-change: transform;
            }
            <?php if ( ! $has_media ) : ?>
            .<?php echo $uid; ?> .scard { grid-template-columns: 1fr; }
            <?php endif; ?>

            .<?php echo $uid; ?> .scard__txt {
                padding: <?php echo $pad; ?>px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                <?php if ( $media_pos === 'left' ) : ?>order: 2;<?php endif; ?>
            }
            .<?php echo $uid; ?> .scard__num {
                font-size: 13px;
                letter-spacing: 0.14em;
                text-transform: uppercase;
                font-weight: 600;
                opacity: 0.92;
                margin-bottom: 14px;
                color: <?php echo $num_def; ?>;
            }
            .<?php echo $uid; ?> .scard__eyebrow {
                font-family: var(--olo-font-family-mono, ui-monospace, SFMono-Regular, Menlo, Consolas, monospace);
                font-size: 14px;
                letter-spacing: 0.02em;
                text-transform: uppercase;
                font-weight: 600;
                opacity: 0.85;
                margin-bottom: 10px;
            }
            .<?php echo $uid; ?> .scard__title {
                <?php echo $title_font_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed CSS literal toggled by $title_display ?>
                color: inherit;
                font-size: <?php echo $title_size_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed clamp() literal toggled by $title_display ?>;
                line-height: 1.02;
                font-weight: 700;
                margin: 0 0 14px;
                letter-spacing: -0.01em;
            }
            .<?php echo $uid; ?> .scard__title .acc {
                opacity: 0.62;
                font-weight: 500;
                margin-left: 0.18em;
            }
            .<?php echo $uid; ?> .scard__text {
                font-size: 16px;
                line-height: 1.7;
                margin: 0;
                max-width: 46ch;
                opacity: 0.96;
            }
            .<?php echo $uid; ?> .scard__text p { margin: 0 0 0.7em; }
            .<?php echo $uid; ?> .scard__text p:last-child { margin-bottom: 0; }

            .<?php echo $uid; ?> .scard__media {
                position: relative;
                min-height: 220px;
                background: rgba(0,0,0,0.06);
                <?php if ( $media_pos === 'left' ) : ?>order: 1;<?php endif; ?>
            }
            .<?php echo $uid; ?> .scard__media img {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: <?php echo esc_attr( $obj_pos ); ?>;
                display: block;
            }
            .<?php echo $uid; ?> .scard__media .ph {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                opacity: 0.45;
            }

            /* Responsive: a colonna singola la card non ha più senso "alta" */
            @media (max-width: 880px) {
                .<?php echo $uid; ?> .scard {
                    grid-template-columns: 1fr;
                    min-height: 0;
                }
                .<?php echo $uid; ?> .scard__txt { order: 1; padding: clamp(24px, 6vw, <?php echo $pad; ?>px); }
                .<?php echo $uid; ?> .scard__media { order: 2; min-height: 240px; }
            }

            /* Fallback statico: reduced-motion → niente pila, flusso verticale normale.
               Sovrascrive lo sticky e annulla l'eventuale scala impilamento. */
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?> .scard {
                    position: static;
                    top: auto !important;
                    transform: none !important;
                }
            }
            <?php
            // Override PER-DEVICE di altezza minima e padding card (campi responsive: true).
            // Ogni breakpoint senza valore eredita il desktop via cascade (come info-cards).
            $ss_bps = [ 'tablet_landscape' => 1200, 'tablet' => 960, 'mobile_landscape' => 640, 'mobile' => 480 ];
            foreach ( $ss_bps as $ss_bp => $ss_w ) :
                $ss_mh  = $s[ 'card_min_height_' . $ss_bp ] ?? '';
                $ss_pad = $s[ 'card_padding_' . $ss_bp ]    ?? '';
                if ( $ss_mh === '' && $ss_pad === '' ) { continue; }
                $ss_card_decls = '';
                $ss_txt_decls  = '';
                if ( $ss_mh !== '' )  { $ss_card_decls .= 'min-height:' . max( 0, min( 900, absint( $ss_mh ) ) ) . 'px;'; }
                if ( $ss_pad !== '' ) { $ss_txt_decls  .= 'padding:' . max( 8, min( 120, absint( $ss_pad ) ) ) . 'px;'; }
                ?>
            @media (max-width: <?php echo intval( $ss_w ); ?>px) {
                <?php if ( $ss_card_decls ) : ?>.<?php echo $uid; ?> .scard { <?php echo $ss_card_decls; ?> }<?php endif; ?>
                <?php if ( $ss_txt_decls ) : ?>.<?php echo $uid; ?> .scard__txt { <?php echo $ss_txt_decls; ?> }<?php endif; ?>
            }
            <?php endforeach; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="olo-stack <?php echo esc_attr( $uid ); ?>">
            <div class="olo-stack__list" role="list">
                <?php if ( $total === 0 ) : ?>
                    <div class="scard" style="top:<?php echo (int) $top_offset; ?>px;" role="listitem">
                        <div class="scard__txt">
                            <?php if ( $show_num ) : ?><div class="scard__num">01</div><?php endif; ?>
                            <h3 class="scard__title"><?php echo esc_html( olobuild_t( 'Aggiungi una card' ) ); ?></h3>
                            <div class="scard__text"><p><?php echo esc_html( olobuild_t( 'Usa il pannello a destra per aggiungere le card della pila.' ) ); ?></p></div>
                        </div>
                        <?php if ( $has_media ) : ?>
                        <div class="scard__media"><span class="ph"><?php echo esc_html( olobuild_t( 'Immagine' ) ); ?></span></div>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                    <?php foreach ( $cards as $i => $card ) :
                        $eyebrow    = isset( $card['eyebrow'] ) ? (string) $card['eyebrow'] : '';
                        $title      = isset( $card['title'] ) ? (string) $card['title'] : '';
                        $accent     = isset( $card['accent'] ) ? (string) $card['accent'] : '';
                        $text_raw   = isset( $card['text'] ) ? (string) $card['text'] : '';
                        $media      = isset( $card['media'] ) ? (string) $card['media'] : '';
                        $media_lbl  = isset( $card['media_label'] ) ? (string) $card['media_label'] : '';
                        $card_bg    = $this->safe_color_css( $card['color'] ?? '' );
                        $card_txt   = $this->safe_color_css( $card['text_color'] ?? '' );

                        // Testo: HTML pulito (Tiptap) o testo semplice
                        $text_html = ( $text_raw !== '' && preg_match( '/<[a-z!\/][^>]*>/i', $text_raw ) )
                            ? $this->safe_richtext_content( $text_raw )
                            : ( $text_raw !== '' ? '<p>' . nl2br( esc_html( $text_raw ) ) . '</p>' : '' );

                        // top per-istanza/per-card: offset base + scalino * indice
                        $card_top = $top_offset + ( $top_step * $i );

                        // Scala impilamento (solo CSS, niente JS): le card più "in basso" nella pila
                        // (indice minore) si riducono; l'ultima (in cima) resta a scala 1.
                        $inline_tf = '';
                        if ( $do_scale && $total > 1 ) {
                            $depth = $total - 1 - $i;                 // 0 = card in cima
                            $scale = 1 - ( $depth * $scale_amt );
                            if ( $scale < 0.85 ) { $scale = 0.85; }   // clamp di sicurezza
                            if ( $scale < 1 ) {
                                $inline_tf = 'transform:scale(' . rtrim( rtrim( number_format( $scale, 4, '.', '' ), '0' ), '.' ) . ');';
                            }
                        }

                        $card_style = 'top:' . $card_top . 'px;' . $inline_tf;
                        if ( $card_bg )  { $card_style .= 'background:' . $card_bg . ';'; }
                        if ( $card_txt ) { $card_style .= 'color:' . $card_txt . ';'; }

                        $num = str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT );
                    ?>
                        <div class="scard scard--<?php echo intval( $i ); ?>" style="<?php echo esc_attr( $card_style ); ?>" role="listitem">
                            <div class="scard__txt">
                                <?php if ( $show_num ) : ?>
                                    <div class="scard__num"><?php echo esc_html( $num ); ?></div>
                                <?php endif; ?>
                                <?php if ( $eyebrow !== '' ) : ?>
                                    <div class="scard__eyebrow" data-olo-editable="cards.<?php echo intval( $i ); ?>.eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
                                <?php endif; ?>
                                <?php if ( $title !== '' || $accent !== '' ) : ?>
                                    <h3 class="scard__title">
                                        <?php if ( $title !== '' ) : ?><span data-olo-editable="cards.<?php echo intval( $i ); ?>.title"><?php echo esc_html( $title ); ?></span><?php endif; ?>
                                        <?php if ( $accent !== '' ) : ?><span class="acc" data-olo-editable="cards.<?php echo intval( $i ); ?>.accent"><?php echo esc_html( $accent ); ?></span><?php endif; ?>
                                    </h3>
                                <?php endif; ?>
                                <?php if ( $text_html !== '' ) : ?>
                                    <div class="scard__text" data-olo-editable="cards.<?php echo intval( $i ); ?>.text" data-olo-richtext><?php echo $text_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered via wp_kses_post() inside safe_richtext_content() (or nl2br+esc_html) at assignment above ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if ( $has_media ) : ?>
                                <div class="scard__media">
                                    <?php if ( $media !== '' ) : ?>
                                        <img src="<?php echo esc_url( $media ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
                                    <?php elseif ( $media_lbl !== '' ) : ?>
                                        <span class="ph" data-olo-editable="cards.<?php echo intval( $i ); ?>.media_label"><?php echo esc_html( $media_lbl ); ?></span>
                                    <?php else : ?>
                                        <span class="ph"><?php echo esc_html( olobuild_t( 'Immagine' ) ); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <script>
        /* StackScroll — runtime minimo, scoped per istanza, idempotente.
           Le card sono già impilate via CSS `position:sticky` (stato base SSR).
           Questo IIFE degrada SOLO quando serve: niente sticky supportato → flusso
           verticale; prefers-reduced-motion → niente scala impilamento (la regola CSS
           già azzera top/transform, qui togliamo anche la scala inline). Nessun rAF. */
        (function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if ( ! root ) { return; }
            if ( root.dataset.oloStack ) { return; }   // una sola init per istanza
            root.dataset.oloStack = '1';

            var cards = root.querySelectorAll('.scard');
            if ( ! cards.length ) { return; }

            var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
            var reduce = !!( rm && rm.matches );

            // Sticky supportato?
            var stickySupported = false;
            try {
                stickySupported = CSS && CSS.supports && (
                    CSS.supports('position', 'sticky') ||
                    CSS.supports('position', '-webkit-sticky')
                );
            } catch ( e ) { stickySupported = false; }

            if ( reduce || ! stickySupported ) {
                // Flusso verticale normale: niente pila, niente scala.
                for ( var i = 0; i < cards.length; i++ ) {
                    var c = cards[i];
                    c.style.position = 'static';
                    c.style.top = 'auto';
                    c.style.transform = 'none';
                    c.style.willChange = 'auto';
                }
            }
        })();
        </script>

        <?php
        // ── Sistema bordi (come il marquee) — applicato al singolo .scard ──
        $card_sel          = ".{$uid} .scard";
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( $card_sel, $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( $card_sel, $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) { echo "{$card_sel}{{$border_css}}"; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $card_sel comes from the internally generated uid
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base border helpers from sanitized border settings
        }

        return ob_get_clean();
    }
}
