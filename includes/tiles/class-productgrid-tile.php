<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Product Grid — griglia prodotti moda/shop minimale: media (immagine o placeholder
 * a strisce) + tag d'angolo + barra "Quick add" in hover, sotto categoria + titolo + prezzo.
 * Card SENZA sfondo/bordo/padding (contenitore trasparente). Link finale opzionale.
 * Estratta 1:1 dal blueprint OLOthemes Atelier Noir (.an-prods/.an-prod). Render == Vue.
 */
class Olo_ProductGrid_Tile extends Olo_Tile_Base {

    protected $type     = 'productgrid';
    protected $name     = 'Product Grid';
    protected $icon     = 'dashicons-products';
    protected $category = 'media';
    protected $defaults = [
        'items' => [
            [ 'image' => '', 'media_label' => 'product', 'tag' => '', 'category' => 'Category', 'title' => 'Product', 'price' => '', 'link' => '#', 'quick_add' => 'Quick add' ],
        ],
        'columns'         => 4,
        'gap'             => 22,
        'media_aspect'    => '3/4',
        'media_bg'        => '',
        'stripe_dark'     => false,
        'hover_zoom'      => true,
        'tag_bg'          => '',
        'tag_color'       => '',
        'quick_add_show'  => true,
        'quick_add_bg'    => '',
        'quick_add_color' => '',
        'category_color'  => '',
        'title_font'      => 'heading',
        'title_size'      => 21,
        'title_color'     => '',
        'price_color'     => '',
        'footer_text'     => '',
        'footer_url'      => '#',
        'footer_color'    => '',
        'show_filters'        => false,
        'filter_all_label'    => 'All',
        'filter_text_color'   => '',
        'filter_active_bg'    => '',
        'filter_active_color' => '',
        'filter_border_color' => '',

        // KIT standard OLObuild (contenitore) — default no-op.
        'bg'                      => [ 'type' => 'none' ],
        'shadow'                  => 'none',
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'opg-' . wp_rand( 10000, 99999 );

        $cols = max( 1, min( 5, intval( $s['columns'] ) ) );
        $gap  = max( 0, intval( $s['gap'] ) ) . 'px';
        $asp  = preg_replace( '/[^0-9.\/]/', '', $s['media_aspect'] ?: '3/4' ) ?: '3/4';

        $dark   = ! empty( $s['stripe_dark'] );
        $mbg    = $this->safe_color_css( $s['media_bg'] ?? '' ) ?: 'var(--olo-color-surface-alt, #f2f2f4)';
        $stripe = $dark ? 'rgba(0,0,0,.06)' : 'rgba(255,255,255,.05)';
        $lblcol = $dark ? 'rgba(0,0,0,.45)' : 'rgba(255,255,255,.4)';
        $tagbg  = $this->safe_color_css( $s['tag_bg'] ?? '' ) ?: 'var(--olo-color-text, #0c0c0c)';
        $tagcol = $this->safe_color_css( $s['tag_color'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)';
        $qabg   = $this->safe_color_css( $s['quick_add_bg'] ?? '' ) ?: 'rgba(255,255,255,0.95)';
        $qacol  = $this->safe_color_css( $s['quick_add_color'] ?? '' ) ?: '#111111';
        $catcol = $this->safe_color_css( $s['category_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #7c776e)';
        $tcol   = $this->safe_color_css( $s['title_color'] ?? '' ) ?: 'var(--olo-color-text, #111827)';
        $pcol   = $this->safe_color_css( $s['price_color'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)';
        $fcol   = $this->safe_color_css( $s['footer_color'] ?? '' ) ?: 'var(--olo-color-text, #111827)';
        $tsize  = max( 12, intval( $s['title_size'] ?? 21 ) );
        $qa_show = ! empty( $s['quick_add_show'] );
        $zoom    = ! empty( $s['hover_zoom'] );

        // Card (sfondo opzionale) — default trasparente → tile minimale invariata
        $card_bg  = $this->safe_color_css( $s['card_bg'] ?? '' );
        $card_bd  = $this->safe_color_css( $s['card_border'] ?? '' );
        $card_rad = max( 0, intval( $s['card_radius'] ?? 0 ) );
        $card_pad = max( 0, intval( $s['card_padding'] ?? 0 ) );
        $has_card = ( $card_bg !== '' || $card_bd !== '' || $card_rad > 0 || $card_pad > 0 );
        // Shade swatches
        $shsize = max( 8, intval( $s['shade_size'] ?? 16 ) );
        $shbd   = $this->safe_color_css( $s['shade_border'] ?? '' ) ?: 'rgba(255,255,255,0.3)';
        // Note (sottotitolo) + roast meter
        $notes_col  = $this->safe_color_css( $s['notes_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #7c776e)';
        $notes_mono = ! empty( $s['notes_mono'] );
        $roast_lbl  = (string) ( $s['roast_label'] ?? 'Roast' );
        $roast_on   = $this->safe_color_css( $s['roast_on_color'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)';
        $roast_off  = $this->safe_color_css( $s['roast_off_color'] ?? '' ) ?: 'rgba(255,255,255,0.15)';
        $mono       = "var(--olo-font-family-mono, ui-monospace, 'Spline Sans Mono', Menlo, monospace)";
        // Add button (footer)
        $add_on  = ! empty( $s['add_button'] );
        $add_lbl = (string) ( $s['add_label'] ?? 'Add' ); if ( $add_lbl === '' ) { $add_lbl = 'Add'; }
        $add_bg  = $this->safe_color_css( $s['add_bg'] ?? '' ) ?: 'var(--olo-color-text-emphasis, #f6e9ec)';
        $add_col = $this->safe_color_css( $s['add_color'] ?? '' ) ?: 'var(--olo-color-text, #111111)';
        $mw_mb   = $has_card ? '0' : '16px';

        $tf = $s['title_font'] ?? 'heading';
        if ( $tf === 'sans' ) {
            $title_font = "var(--olo-font-family, -apple-system, sans-serif)";
        } else {
            // 'heading' e 'serif' usano il font heading del tema (serif nei temi editoriali).
            $title_font = "var(--olo-font-family-heading, Georgia, serif)";
        }
        $sans = "var(--olo-font-family, -apple-system, sans-serif)";

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) { return ''; }

        // Filtri (additivo): chip espliciti `filter_list` (filtrano per `filter_tags`) oppure,
        // se vuoto, chip dalle categorie (filtrano per `category`). "Tutti" mostra sempre tutto.
        $show_filters = ! empty( $s['show_filters'] );
        $flist = [];
        $fl_raw = trim( (string) ( $s['filter_list'] ?? '' ) );
        if ( $fl_raw !== '' ) { $flist = array_values( array_filter( array_map( 'trim', explode( ',', $fl_raw ) ) ) ); }
        $cats = [];
        if ( $show_filters && empty( $flist ) ) {
            foreach ( $items as $it_c ) {
                $c = trim( (string) ( $it_c['category'] ?? '' ) );
                if ( $c !== '' && ! in_array( $c, $cats, true ) ) { $cats[] = $c; }
            }
        }
        $chips = ! empty( $flist ) ? $flist : $cats;
        $show_filters = $show_filters && ! empty( $chips );
        $f_all  = (string) ( $s['filter_all_label'] ?? 'All' ); if ( $f_all === '' ) { $f_all = 'All'; }
        $f_txt  = $this->safe_color_css( $s['filter_text_color'] ?? '' ) ?: 'var(--olo-color-text, #111827)';
        $f_bd   = $this->safe_color_css( $s['filter_border_color'] ?? '' ) ?: 'var(--olo-color-border, rgba(0,0,0,.14))';
        $f_abg  = $this->safe_color_css( $s['filter_active_bg'] ?? '' ) ?: $pcol;
        $f_acol = $this->safe_color_css( $s['filter_active_color'] ?? '' ) ?: '#ffffff';

        // ── KIT standard OLObuild (contenitore) ──
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        $shadow_css = $this->build_shadow_decl( $s );
        $border_css = $this->build_border_css( $s['border'] ?? [] );
        $kit_decl = '';
        if ( $bg_decl !== '' )    { $kit_decl .= $bg_decl . ';'; }
        if ( $shadow_css !== '' ) { $kit_decl .= 'box-shadow:' . $shadow_css . ';'; }
        if ( $border_css !== '' ) { $kit_decl .= $border_css; }
        $kit_pos = ( $kit_decl !== '' ) ? 'position:relative;' : '';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{font-family:<?php echo $sans; ?>;<?php echo $kit_pos . $kit_decl; ?>}
            .<?php echo $uid; ?> .opg-grid{display:grid;grid-template-columns:repeat(<?php echo $cols; ?>,1fr);gap:<?php echo $gap; ?>;}
            .<?php echo $uid; ?> .opg-filters{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-bottom:48px;}
            .<?php echo $uid; ?> .opg-filter{font-family:<?php echo $sans; ?>;font-weight:500;font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:<?php echo $f_txt; ?>;border:1px solid <?php echo $f_bd; ?>;padding:10px 20px;cursor:pointer;background:transparent;transition:all .2s;}
            .<?php echo $uid; ?> .opg-filter.on,.<?php echo $uid; ?> .opg-filter:hover{background:<?php echo $f_abg; ?>;color:<?php echo $f_acol; ?>;border-color:<?php echo $f_abg; ?>;}
            .<?php echo $uid; ?> .opg-filter:focus-visible{outline:2px solid <?php echo $f_abg; ?>;outline-offset:2px;}
            .<?php echo $uid; ?> .opg-card{display:flex;flex-direction:column;<?php if ( $has_card ) : ?>background:<?php echo $card_bg ?: 'transparent'; ?>;<?php if ( $card_bd ) : ?>border:1px solid <?php echo $card_bd; ?>;<?php endif; ?>border-radius:<?php echo $card_rad; ?>px;overflow:hidden;<?php endif; ?>}
            .<?php echo $uid; ?> .opg-mw{position:relative;overflow:hidden;margin-bottom:<?php echo $mw_mb; ?>;display:block;}
            .<?php echo $uid; ?> .opg-body{display:flex;flex-direction:column;flex:1;<?php if ( $has_card && $card_pad > 0 ) : ?>padding:<?php echo $card_pad; ?>px;<?php endif; ?>}
            .<?php echo $uid; ?> .opg-notes{font-size:13.5px;color:<?php echo $notes_col; ?>;margin:2px 0 14px;line-height:1.5;<?php if ( $notes_mono ) : ?>font-family:<?php echo $mono; ?>;<?php endif; ?>}
            .<?php echo $uid; ?> .opg-roast{display:flex;align-items:center;gap:8px;margin:0 0 18px;}
            .<?php echo $uid; ?> .opg-roast__lbl{font-family:<?php echo $mono; ?>;font-size:10.5px;letter-spacing:.05em;text-transform:uppercase;color:<?php echo $notes_col; ?>;}
            .<?php echo $uid; ?> .opg-roast__dots{display:flex;gap:4px;}
            .<?php echo $uid; ?> .opg-roast__dots i{width:8px;height:8px;border-radius:50%;background:<?php echo $roast_off; ?>;display:inline-block;}
            .<?php echo $uid; ?> .opg-roast__dots i.on{background:<?php echo $roast_on; ?>;}
            .<?php echo $uid; ?> .opg-shades{display:flex;gap:6px;margin:8px 0 14px;}
            .<?php echo $uid; ?> .opg-shades i{width:<?php echo $shsize; ?>px;height:<?php echo $shsize; ?>px;border-radius:50%;box-shadow:inset 0 0 0 1.5px <?php echo $shbd; ?>;display:inline-block;flex:none;}
            .<?php echo $uid; ?> .opg-cardfoot{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:auto;}
            .<?php echo $uid; ?> .opg-addbtn{font-family:<?php echo $sans; ?>;font-weight:700;font-size:12px;color:<?php echo $add_col; ?>;background:<?php echo $add_bg; ?>;border:0;border-radius:999px;padding:9px 15px;cursor:pointer;transition:opacity .2s;}
            .<?php echo $uid; ?> .opg-addbtn:hover{opacity:.85;}
            .<?php echo $uid; ?> .opg-media{display:block;aspect-ratio:<?php echo $asp; ?>;background:<?php echo $mbg; ?>;background-size:cover;background-position:center;background-image:repeating-linear-gradient(135deg, <?php echo $stripe; ?> 0 16px, transparent 16px 32px);transition:transform .7s cubic-bezier(.2,.7,.3,1);}
            <?php if ( $zoom ) : ?>.<?php echo $uid; ?> .opg-card:hover .opg-media{transform:scale(1.05);}<?php endif; ?>
            .<?php echo $uid; ?> .opg-lbl{position:absolute;left:14px;bottom:12px;font-size:10px;letter-spacing:.14em;text-transform:uppercase;font-weight:500;color:<?php echo $lblcol; ?>;}
            .<?php echo $uid; ?> .opg-tag{position:absolute;top:14px;left:14px;background:<?php echo $tagbg; ?>;color:<?php echo $tagcol; ?>;font-weight:500;font-size:9.5px;letter-spacing:.16em;text-transform:uppercase;padding:5px 11px;}
            .<?php echo $uid; ?> .opg-add{position:absolute;left:14px;right:14px;bottom:14px;background:<?php echo $qabg; ?>;color:<?php echo $qacol; ?>;font-weight:500;font-size:11px;letter-spacing:.18em;text-transform:uppercase;text-align:center;padding:12px;opacity:0;transform:translateY(8px);transition:all .35s;cursor:pointer;}
            .<?php echo $uid; ?> .opg-card:hover .opg-add{opacity:1;transform:none;}
            .<?php echo $uid; ?> .opg-cat{font-weight:500;font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:<?php echo $catcol; ?>;}
            .<?php echo $uid; ?> .opg-t{font-family:<?php echo $title_font; ?>;font-weight:400;font-size:<?php echo $tsize; ?>px;margin:7px 0 6px;color:<?php echo $tcol; ?>;line-height:1.15;}
            .<?php echo $uid; ?> .opg-t a{color:inherit;text-decoration:none;}
            .<?php echo $uid; ?> .opg-price{font-size:14px;letter-spacing:.06em;color:<?php echo $pcol; ?>;}
            .<?php echo $uid; ?> .opg-foot{text-align:center;margin-top:48px;}
            .<?php echo $uid; ?> .opg-foot a{display:inline-block;font-weight:500;font-size:12px;letter-spacing:.18em;text-transform:uppercase;color:<?php echo $fcol; ?>;border-bottom:1px solid <?php echo $pcol; ?>;padding-bottom:3px;text-decoration:none;transition:opacity .2s;}
            .<?php echo $uid; ?> .opg-foot a:hover{opacity:.7;}
            .<?php echo $uid; ?> .opg-mw:focus-visible{outline:2px solid <?php echo $pcol; ?>;outline-offset:3px;}
            @media(max-width:900px){.<?php echo $uid; ?> .opg-grid{grid-template-columns:1fr 1fr;}}
        </style>
        <div class="olo-productgrid <?php echo esc_attr( $uid ); ?>">
            <?php if ( $show_filters ) : ?>
            <div class="opg-filters">
                <button class="opg-filter on" type="button" data-opg-filter=""><?php echo esc_html( $f_all ); ?></button>
                <?php foreach ( $chips as $c ) : ?><button class="opg-filter" type="button" data-opg-filter="<?php echo esc_attr( $c ); ?>"><?php echo esc_html( $c ); ?></button><?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="opg-grid">
            <?php foreach ( $items as $pi => $it ) :
                $img  = isset( $it['image'] ) ? trim( $it['image'] ) : '';
                $href = ! empty( $it['link'] ) ? $it['link'] : '#';
                $qa   = trim( (string) ( $it['quick_add'] ?? '' ) );
                $mb   = $this->bg_media_parts( $it['media_bg'] ?? null, $uid . '-i' . $pi );
                if ( $mb['has'] ) {
                    $msty = $mb['css'] !== '' ? ' style="' . esc_attr( $mb['css'] ) . '"' : '';
                } else {
                    $msty = $img !== '' ? ' style="background-image:url(' . esc_url( $img ) . ')"' : '';
                }
            ?>
                <div class="opg-card" data-cat="<?php echo esc_attr( $it['category'] ?? '' ); ?>" data-tags="<?php echo esc_attr( $it['filter_tags'] ?? '' ); ?>">
                    <a class="opg-mw" href="<?php echo esc_url( $href ); ?>">
                        <span class="opg-media"<?php echo $msty; ?>></span>
                        <?php if ( $mb['has'] && $mb['markup'] !== '' ) { echo $mb['markup']; } ?>
                        <?php if ( ! $mb['has'] && $img === '' && ! empty( $it['media_label'] ) ) : ?><span class="opg-lbl"><?php echo esc_html( $it['media_label'] ); ?></span><?php endif; ?>
                        <?php if ( ! empty( $it['tag'] ) ) : ?><span class="opg-tag"><?php echo esc_html( $it['tag'] ); ?></span><?php endif; ?>
                        <?php if ( $qa_show && $qa !== '' ) : ?><span class="opg-add"><?php echo esc_html( $qa ); ?></span><?php endif; ?>
                    </a>
                    <div class="opg-body">
                        <?php if ( ! empty( $it['category'] ) ) : ?><div class="opg-cat"><?php echo esc_html( $it['category'] ); ?></div><?php endif; ?>
                        <?php if ( ! empty( $it['title'] ) ) : ?><h3 class="opg-t"><a href="<?php echo esc_url( $href ); ?>"><?php echo esc_html( $it['title'] ); ?></a></h3><?php endif; ?>
                        <?php if ( ! empty( $it['notes'] ) ) : ?><p class="opg-notes"><?php echo esc_html( $it['notes'] ); ?></p><?php endif; ?>
                        <?php
                        $shraw = trim( (string) ( $it['shades'] ?? '' ) );
                        if ( $shraw !== '' ) :
                            $shts = array_filter( array_map( 'trim', preg_split( '/[,\s]+/', $shraw ) ) );
                        ?>
                            <div class="opg-shades"><?php foreach ( $shts as $sc ) : $scc = $this->safe_color_css( $sc ); if ( $scc ) : ?><i style="background:<?php echo esc_attr( $scc ); ?>"></i><?php endif; endforeach; ?></div>
                        <?php endif; ?>
                        <?php $roast_n = intval( $it['roast'] ?? 0 ); if ( $roast_n > 0 ) : ?>
                            <div class="opg-roast"><span class="opg-roast__lbl"><?php echo esc_html( $roast_lbl ); ?></span><span class="opg-roast__dots"><?php for ( $ri = 1; $ri <= 5; $ri++ ) : ?><i class="<?php echo $ri <= $roast_n ? 'on' : ''; ?>"></i><?php endfor; ?></span></div>
                        <?php endif; ?>
                        <?php if ( $add_on ) : ?>
                            <div class="opg-cardfoot">
                                <?php if ( ! empty( $it['price'] ) ) : ?><div class="opg-price"><?php echo esc_html( $it['price'] ); ?></div><?php endif; ?>
                                <button type="button" class="opg-addbtn"><?php echo esc_html( $add_lbl ); ?></button>
                            </div>
                        <?php elseif ( ! empty( $it['price'] ) ) : ?>
                            <div class="opg-price"><?php echo esc_html( $it['price'] ); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
            <?php if ( ! empty( $s['footer_text'] ) ) : ?>
            <div class="opg-foot"><a href="<?php echo esc_url( $s['footer_url'] ?: '#' ); ?>"><?php echo esc_html( $s['footer_text'] ); ?></a></div>
            <?php endif; ?>
        </div>
        <?php if ( $show_filters ) : ?>
        <script>
        (function(){var r=document.querySelector('.<?php echo esc_js( $uid ); ?>');if(!r){return;}var fs=r.querySelectorAll('[data-opg-filter]');var cs=r.querySelectorAll('.opg-card');for(var i=0;i<fs.length;i++){(function(b){b.addEventListener('click',function(){var c=b.getAttribute('data-opg-filter');for(var j=0;j<fs.length;j++){fs[j].classList.remove('on');}b.classList.add('on');for(var k=0;k<cs.length;k++){var cc=cs[k].getAttribute('data-cat')||'';var tg=cs[k].getAttribute('data-tags')||'';var inT=false;if(tg){var ar=tg.split(',');for(var t=0;t<ar.length;t++){if(ar[t].trim()===c){inT=true;break;}}}if(c===''||cc===c||inT){cs[k].style.display='';}else{cs[k].style.display='none';}}});})(fs[i]);}})();
        </script>
        <?php endif; ?>
        <?php
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_hover_css || $border_effect_css ) {
            echo '<style>' . $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    /**
     * Dichiarazione box-shadow (valore, senza "box-shadow:") dal setting shadow
     * (preset sm/md/lg/xl o custom). '' se none.
     */
    private function build_shadow_decl( $s ) {
        $preset = $s['shadow'] ?? 'none';
        if ( $preset === 'none' || $preset === '' ) {
            return '';
        }
        if ( $preset === 'custom' ) {
            $h      = intval( $s['shadow_h'] ?? 0 );
            $v      = intval( $s['shadow_v'] ?? 4 );
            $blur   = max( 0, intval( $s['shadow_blur'] ?? 10 ) );
            $spread = intval( $s['shadow_spread'] ?? 0 );
            $color  = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $inset  = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            return "{$inset}{$h}px {$v}px {$blur}px {$spread}px {$color}";
        }
        $map = [
            'sm' => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md' => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg' => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl' => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
        ];
        return $map[ $preset ] ?? '';
    }
}
