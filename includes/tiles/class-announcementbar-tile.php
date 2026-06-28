<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Announcement Bar — striscia annuncio full-width (sopra la nav): testo centrato +
 * parte evidenziata (accento), link opzionale, chiudibile opzionale (memoria localStorage).
 * Estratta 1:1 dal blueprint OLOthemes Atelier Noir (.an-ann). Render == Vue. No '&&' nello script.
 */
class Olobuild_AnnouncementBar_Tile extends Olobuild_Tile_Base {

    protected $type     = 'announcementbar';
    protected $name     = 'Announcement Bar';
    protected $icon     = 'dashicons-megaphone';
    protected $category = 'marketing';
    protected $defaults = [
        'text'           => 'Complimentary shipping & returns worldwide · ',
        'accent_text'    => 'The Nocturne collection has arrived',
        'link_url'       => '',
        'dismissible'    => false,
        'bg_color'       => '',
        'text_color'     => '',
        'accent_color'   => '',
        'font_size'      => '11',
        'font_weight'    => '500',
        'letter_spacing' => '0.2em',
        'text_transform' => 'uppercase',
        'alignment'      => 'center',
        'tile_padding'   => [ 'top' => 10, 'right' => 20, 'bottom' => 10, 'left' => 20 ],
        'border_bottom'  => '0',
        'border_color'   => '',
        'bg'             => [ 'type' => 'none' ],
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'oab-' . wp_rand( 10000, 99999 );

        $text   = (string) ( $s['text'] ?? '' );
        $accent = (string) ( $s['accent_text'] ?? '' );
        if ( $text === '' && $accent === '' ) { return ''; }

        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $bg_decl = ( new Olobuild_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        $bgcol = $this->safe_color_css( $s['bg_color'] ?? '' ) ?: 'var(--olo-color-text, #0c0c0c)';
        $bg    = $bg_decl !== '' ? $bg_decl : ( 'background:' . $bgcol );
        $tcol  = $this->safe_color_css( $s['text_color'] ?? '' ) ?: 'var(--olo-color-secondary, #efe9de)';
        $acol  = $this->safe_color_css( $s['accent_color'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)';
        $fs    = max( 8, intval( $s['font_size'] ?? 11 ) );
        $fw    = in_array( (string) ( $s['font_weight'] ?? '500' ), [ '400', '500', '600', '700' ], true ) ? (string) $s['font_weight'] : '500';
        $ls    = preg_replace( '/[^0-9a-z.\-]/i', '', (string) ( $s['letter_spacing'] ?? '0.2em' ) ) ?: '0.2em';
        $tt    = in_array( $s['text_transform'] ?? 'uppercase', [ 'none', 'uppercase', 'lowercase' ], true ) ? $s['text_transform'] : 'uppercase';
        $align = in_array( $s['alignment'] ?? 'center', [ 'left', 'center', 'right' ], true ) ? $s['alignment'] : 'center';
        $cp    = is_array( $s['tile_padding'] ?? null ) ? $s['tile_padding'] : [];
        $pad   = intval( $cp['top'] ?? 10 ) . 'px ' . intval( $cp['right'] ?? 20 ) . 'px ' . intval( $cp['bottom'] ?? 10 ) . 'px ' . intval( $cp['left'] ?? 20 ) . 'px';
        $bb    = max( 0, intval( $s['border_bottom'] ?? 0 ) );
        $bc    = $this->safe_color_css( $s['border_color'] ?? '' ) ?: 'rgba(239,233,222,.12)';
        $sans  = "var(--olo-font-family, -apple-system, sans-serif)";
        $link  = trim( (string) ( $s['link_url'] ?? '' ) );
        $dismiss = ! empty( $s['dismissible'] );

        $inner = esc_html( $text );
        if ( $accent !== '' ) { $inner .= '<b>' . esc_html( $accent ) . '</b>'; }

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: background via Olobuild_CSS_Builder::get_bg_inline_css() or the safe_color_css() whitelist, colours via safe_color_css(), $fs/$bb via intval() clamps, $fw/$tt/$align via in_array() whitelists, $ls via preg_replace() character whitelist, padding integer-forced, font stack fixed literal; $uid is internally generated.
        ?>
        <style>
            .<?php echo $uid; ?>{<?php echo $bg; ?>;color:<?php echo $tcol; ?>;text-align:<?php echo $align; ?>;font-family:<?php echo $sans; ?>;font-size:<?php echo $fs; ?>px;font-weight:<?php echo $fw; ?>;letter-spacing:<?php echo $ls; ?>;text-transform:<?php echo $tt; ?>;padding:<?php echo $pad; ?>;<?php if ( $bb > 0 ) : ?>border-bottom:<?php echo $bb; ?>px solid <?php echo $bc; ?>;<?php endif; ?>position:relative;line-height:1.4;}
            .<?php echo $uid; ?> b{color:<?php echo $acol; ?>;font-weight:<?php echo $fw; ?>;}
            .<?php echo $uid; ?> a.oab-link{color:inherit;text-decoration:none;}
            .<?php echo $uid; ?> .oab-close{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:0;color:inherit;cursor:pointer;font-size:16px;line-height:1;opacity:.6;}
            .<?php echo $uid; ?> .oab-close:hover{opacity:1;}
            .<?php echo $uid; ?> .oab-close:focus-visible{outline:2px solid <?php echo $acol; ?>;outline-offset:2px;}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-annbar <?php echo esc_attr( $uid ); ?>">
            <?php if ( $link !== '' ) : ?><a class="oab-link" href="<?php echo esc_url( $link ); ?>"><?php echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above from esc_html()'d text/accent plus a literal <b> wrapper ?></a><?php else : ?><span><?php echo $inner; ?></span><?php endif; ?>
            <?php if ( $dismiss ) : ?><button class="oab-close" type="button" aria-label="<?php echo esc_attr( __( 'Close', 'olobuild' ) ); ?>">&times;</button><?php endif; ?>
        </div>
        <?php if ( $dismiss ) :
            $key = 'olo_annbar_' . substr( md5( $text . $accent ), 0, 8 );
        ?>
        <script>
        (function(){var r=document.querySelector('.<?php echo esc_js( $uid ); ?>');if(!r){return;}var k='<?php echo esc_js( $key ); ?>';
        try{if(localStorage.getItem(k)){r.style.display='none';return;}}catch(e){}
        var b=r.querySelector('.oab-close');if(b){b.addEventListener('click',function(){r.style.display='none';try{localStorage.setItem(k,'1');}catch(e){}});}})();
        </script>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }
}
