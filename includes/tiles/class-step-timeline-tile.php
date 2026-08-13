<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Step Timeline — timeline orizzontale con N step.
 * Standard Olobuild: border-radius 4 angoli + hover, i18n, divisione
 * contenuto/stile, inline editing su tutti i testi, render_icon_html
 * per il footer (UIkit + Lucide).
 */
class Olobuild_StepTimeline_Tile extends Olobuild_Tile_Base {

    protected $type     = 'step-timeline';
    protected $name     = 'Step Timeline';
    protected $icon     = 'dashicons-clock';
    protected $category = 'layout';
    protected $defaults = [
        'items' => [
            [ 'counter' => '01', 'tag_text' => 'PRONTO IN 30"', 'tag_dot_color' => '#b3261e', 'media_label' => 'TERMINAL · INSTALL', 'media_type' => 'terminal', 'media_content' => "# installazione\n$ wp plugin install olobuild --activate\n✓ Plugin installato\n✓ 187 tile registrati\n# pronto\n$", 'media_image' => '', 'media_bg' => '#0f172a', 'media_color' => '#10b981', 'pre_title' => 'INSTALLA', 'title' => 'Scarichi', 'title_accent' => 'OLObuild', 'title_accent_italic' => true, 'title_after' => 'direttamente da WordPress.org.', 'title_after_italic' => false, 'description' => 'Zero configurazione obbligatoria. Funziona con qualunque tema WP. Anche dal nostro sito al lancio — un click e sei dentro.', 'footer_value' => '30"', 'footer_label' => 'TEMPO MEDIO', 'separator_text' => '→ POI' ],
            [ 'counter' => '02', 'tag_text' => 'DRAG & DROP', 'tag_dot_color' => '#b3261e', 'media_label' => 'OLOBUILD · EDITOR LIVE', 'media_type' => 'placeholder', 'media_content' => '', 'media_image' => '', 'media_bg' => '#f5efe7', 'media_color' => '#b3261e', 'pre_title' => 'COSTRUISCI', 'title' => 'Trascini i tile, scegli i colori,', 'title_accent' => 'doppio click', 'title_accent_italic' => true, 'title_after' => 'per editare.', 'title_after_italic' => false, 'description' => 'Anteprima fedele in tempo reale. Mobile, tablet, desktop con un click. Niente shortcode, niente "preview separato".', 'footer_value' => '≈ 1h', 'footer_label' => 'PRIMA PAGINA', 'separator_text' => '→ VAI LIVE' ],
            [ 'counter' => '03', 'tag_text' => 'ONLINE', 'tag_dot_color' => '#10b981', 'media_label' => 'TUOSITO.COM · LIVE', 'media_type' => 'placeholder', 'media_content' => '', 'media_image' => '', 'media_bg' => '#f5efe7', 'media_color' => '#10b981', 'pre_title' => 'PUBBLICHI & SCALI', 'title' => 'Quando ti serve,', 'title_accent' => 'aggiungi', 'title_accent_italic' => true, 'title_after' => 'OLOlang o OLObooking.', 'title_after_italic' => false, 'description' => 'Stesso stack, stessa interfaccia, niente migration. Pronto al traffico — i pezzi crescono insieme al sito.', 'footer_value' => '0"', 'footer_label' => 'PRONTO AL TRAFFICO', 'separator_text' => '' ],
        ],

        'show_timeline'          => true,
        'timeline_line_color'    => '',
        'timeline_dot_color'     => '',
        'timeline_dot_size'      => 14,
        'timeline_height'        => 3,
        'timeline_margin_bottom' => 50,

        'counter_font_family' => 'serif',
        'counter_size'        => 96,
        'counter_color'       => '',
        'counter_italic'      => true,
        'counter_weight'      => '500',

        'tag_size'  => 12,
        'tag_color' => '',

        'media_aspect_ratio'         => '5/4',
        'media_object_position'      => 'center center',
        'media_radius'               => [ 'tl' => 14, 'tr' => 14, 'br' => 14, 'bl' => 14, 'linked' => true ],
        'media_radius_hover'         => [ 'tl' => 14, 'tr' => 14, 'br' => 14, 'bl' => 14, 'linked' => true ],
        'media_radius_hover_duration' => 400,
        'media_shadow'               => 'sm',
        'show_media_label'           => true,

        'pre_title_size'  => 12,
        'pre_title_color' => '',

        'title_font_family'  => 'serif',
        'title_size'         => 30,
        'title_weight'       => '500',
        'title_color'        => '',
        'title_accent_color' => '',

        'description_size'  => 14,
        'description_color' => '',

        'footer_icon'        => 'clock',
        'footer_value_size'  => 18,
        'footer_label_size'  => 11,
        'footer_value_color' => '',
        'footer_label_color' => '',

        'separator_color' => '',
        'show_separator'  => true,

        'columns'     => 3,
        'gap'         => 32,
        'items_align' => 'start',
    ];

    public function get_controls() { return []; }

    private function _radius_hover_diff( $base, $hover ) {
        if ( ! is_array( $base ) || ! is_array( $hover ) ) return '';
        foreach ( [ 'tl', 'tr', 'br', 'bl' ] as $c ) {
            if ( intval( $base[ $c ] ?? 0 ) !== intval( $hover[ $c ] ?? 0 ) ) {
                return $this->build_border_radius_css( $hover );
            }
        }
        return '';
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-stl-' . wp_rand( 10000, 99999 );

        $serif = "var(--olo-font-family-heading, 'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif)";
        $sans  = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $mono  = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
        // Valori legacy ('serif'/'sans-serif'/'mono') → stack storici della tile;
        // valori nuovi (type 'font-family') → CSS pronto via resolver condiviso.
        $legacy = [ 'serif' => $serif, 'sans-serif' => $sans, 'mono' => $mono ];

        $counter_family = $this->resolve_font_family( $s['counter_font_family'], $legacy ) ?: $serif;
        $title_family   = $this->resolve_font_family( $s['title_font_family'], $legacy )   ?: $serif;

        $cols       = max( 1, min( 5, absint( $s['columns'] ) ) );
        $gap        = max( 0, min( 80, absint( $s['gap'] ) ) );
        $items_alig = $s['items_align'] === 'center' ? 'center' : 'flex-start';

        $tl_line   = $this->safe_color_css( $s['timeline_line_color'] ) ?: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 18%, #fff)';
        $tl_dot    = $this->safe_color_css( $s['timeline_dot_color'] )  ?: 'var(--olo-color-primary, #e1474f)';
        $tl_dotsz  = max( 6, min( 24, absint( $s['timeline_dot_size'] ) ) );
        $tl_h      = max( 1, min( 8, absint( $s['timeline_height'] ) ) );
        $tl_mb     = max( 0, min( 120, absint( $s['timeline_margin_bottom'] ) ) );

        $counter_size   = max( 40, min( 200, absint( $s['counter_size'] ) ) );
        $counter_clr    = $this->safe_color_css( $s['counter_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $counter_italic = ! empty( $s['counter_italic'] ) ? 'italic' : 'normal';
        $counter_w      = preg_match( '/^\d+$/', (string) $s['counter_weight'] ) ? $s['counter_weight'] : '500';

        $tag_size  = max( 10, min( 16, absint( $s['tag_size'] ) ) );
        $tag_color = $this->safe_color_css( $s['tag_color'] ) ?: 'var(--olo-color-text, #374151)';

        $aspect_allow = [ '16/9', '5/4', '4/3', '1/1', '3/2' ];
        $media_aspect = in_array( $s['media_aspect_ratio'] ?? '5/4', $aspect_allow, true ) ? ( $s['media_aspect_ratio'] ?? '5/4' ) : '5/4';

        $obj_pos = trim( (string) ( $s['media_object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) { $obj_pos = 'center center'; }

        $media_radius   = $this->build_border_radius_css( $s['media_radius'] ?? [] );
        $media_radius_h = $this->_radius_hover_diff( $s['media_radius'] ?? [], $s['media_radius_hover'] ?? [] );
        $media_rdur     = max( 50, intval( $s['media_radius_hover_duration'] ?? 400 ) );

        $shadow_map = [
            'none' => '',
            'sm'   => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md'   => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg'   => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
        ];
        $media_shadow = $shadow_map[ $s['media_shadow'] ?? 'sm' ] ?? '';

        $pre_title_size  = max( 9, min( 16, absint( $s['pre_title_size'] ) ) );
        $pre_title_clr   = $this->safe_color_css( $s['pre_title_color'] ) ?: 'var(--olo-color-text-faint, #9ca3af)';

        $title_size      = max( 18, min( 60, absint( $s['title_size'] ) ) );
        $title_w         = preg_match( '/^\d+$/', (string) $s['title_weight'] ) ? $s['title_weight'] : '500';
        $title_clr       = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #0f172a)';
        $title_accent_c  = $this->safe_color_css( $s['title_accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';

        $desc_size = max( 11, min( 20, absint( $s['description_size'] ) ) );
        $desc_clr  = $this->safe_color_css( $s['description_color'] ) ?: 'var(--olo-color-text-soft, #6b7280)';

        $fv_size = max( 12, min( 30, absint( $s['footer_value_size'] ) ) );
        $fl_size = max( 9, min( 14, absint( $s['footer_label_size'] ) ) );
        $fv_clr  = $this->safe_color_css( $s['footer_value_color'] ) ?: 'var(--olo-color-text, #0f172a)';
        $fl_clr  = $this->safe_color_css( $s['footer_label_color'] ) ?: 'var(--olo-color-text-faint, #9ca3af)';
        $f_icon  = $s['footer_icon'] ?? 'clock';

        $sep_clr = $this->safe_color_css( $s['separator_color'] ) ?: 'var(--olo-color-primary, #e1474f)';

        $items = is_array( $s['items'] ) ? $s['items'] : [];
        $n_items = count( $items );

        ob_start();
        ?>
        <div class="olo-stl <?php echo esc_attr( $uid ); ?>">

            <?php if ( ! empty( $s['show_timeline'] ) ) :
                // Timeline: linea con (n+1) pallini (1 iniziale, 1 dopo ogni step)
                $tl_dots = $n_items + 1;
            ?>
                <div class="olo-stl__timeline" style="position:relative;height:<?php echo (int) $tl_dotsz; ?>px;margin-bottom:<?php echo (int) $tl_mb; ?>px">
                    <div style="position:absolute;left:0;right:0;top:50%;transform:translateY(-50%);height:<?php echo (int) $tl_h; ?>px;background:<?php echo esc_attr( $tl_line ); ?>;border-radius:<?php echo (int) $tl_h; ?>px"></div>
                    <?php for ( $d = 0; $d < $tl_dots; $d++ ) :
                        $pct = $tl_dots > 1 ? ( $d / ( $tl_dots - 1 ) ) * 100 : 50;
                        // Ultimo pallino: usa il colore tag dell'ultimo item se "completato" (verde)
                        $dot_c = $tl_dot;
                        if ( $d === $tl_dots - 1 && isset( $items[ $n_items - 1 ]['tag_dot_color'] ) ) {
                            $dot_c = $this->safe_color_css( $items[ $n_items - 1 ]['tag_dot_color'] ) ?: $tl_dot;
                        }
                    ?>
                        <span style="position:absolute;left:<?php echo (float) $pct; ?>%;top:50%;transform:translate(-50%,-50%);width:<?php echo (int) $tl_dotsz; ?>px;height:<?php echo (int) $tl_dotsz; ?>px;border-radius:50%;background:<?php echo esc_attr( $dot_c ); ?>;box-shadow:0 0 0 4px #fff,0 0 0 5px color-mix(in srgb, <?php echo esc_attr( $dot_c ); ?> 20%, transparent)"></span>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

            <div class="olo-stl__grid" style="display:grid;grid-template-columns:repeat(<?php echo (int) $cols; ?>,1fr);gap:<?php echo (int) $gap; ?>px;align-items:<?php echo esc_attr( $items_alig ); ?>;position:relative">
                <?php foreach ( $items as $idx => $it ) :
                    $counter      = $it['counter'] ?? '';
                    $tag_text     = $it['tag_text'] ?? '';
                    $tag_dot_clr  = $this->safe_color_css( $it['tag_dot_color'] ?? '' ) ?: $tl_dot;
                    $m_label      = $it['media_label'] ?? '';
                    $m_type       = $it['media_type'] ?? 'placeholder';
                    $m_content    = $it['media_content'] ?? '';
                    $m_image      = $it['media_image'] ?? '';
                    $m_bg         = $this->safe_color_css( $it['media_bg'] ?? '' ) ?: '#f5efe7';
                    $m_color      = $this->safe_color_css( $it['media_color'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)';
                    $pre_title    = $it['pre_title'] ?? '';
                    $title        = $it['title'] ?? '';
                    $t_accent     = $it['title_accent'] ?? '';
                    $t_acc_it     = ! empty( $it['title_accent_italic'] );
                    $t_after      = $it['title_after'] ?? '';
                    $t_aft_it     = ! empty( $it['title_after_italic'] );
                    $desc_raw     = $it['description'] ?? '';
                    $desc         = preg_match( '/<[a-z!\/][^>]*>/i', $desc_raw ) ? $this->safe_richtext_content( $desc_raw ) : nl2br( esc_html( $desc_raw ) );
                    $f_val        = $it['footer_value'] ?? '';
                    $f_lbl        = $it['footer_label'] ?? '';
                    $sep_text     = $it['separator_text'] ?? '';
                    $is_last      = ( $idx === $n_items - 1 );
                ?>
                    <div class="olo-stl__item" style="display:flex;flex-direction:column;gap:18px;position:relative">

                        <!-- Counter + tag (riga numero step) -->
                        <div style="display:flex;align-items:flex-end;gap:18px;flex-wrap:wrap">
                            <?php if ( $counter !== '' ) : ?>
                                <span class="olo-stl__counter" style="font-family:<?php echo esc_attr( $counter_family ); ?>;font-size:<?php echo (int) $counter_size; ?>px;line-height:.9;color:<?php echo esc_attr( $counter_clr ); ?>;font-style:<?php echo esc_attr( $counter_italic ); ?>;font-weight:<?php echo esc_attr( $counter_w ); ?>;letter-spacing:-0.02em" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.counter'; ?>"><?php echo esc_html( $counter ); ?></span>
                            <?php endif; ?>
                            <?php if ( $tag_text !== '' ) : ?>
                                <div style="display:inline-flex;align-items:center;gap:8px;padding-bottom:14px">
                                    <span style="width:8px;height:8px;border-radius:50%;background:<?php echo esc_attr( $tag_dot_clr ); ?>"></span>
                                    <span style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo (int) $tag_size; ?>px;letter-spacing:0.08em;text-transform:uppercase;color:<?php echo esc_attr( $tag_color ); ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.tag_text'; ?>"><?php echo esc_html( $tag_text ); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Mockup card -->
                        <div class="olo-stl__mockup" style="background:<?php echo esc_attr( $m_bg ); ?>;<?php if ( $media_radius ) echo 'border-radius:' . esc_attr( $media_radius ) . ';'; ?>aspect-ratio:<?php echo esc_attr( $media_aspect ); ?>;overflow:hidden;<?php if ( $media_shadow ) echo 'box-shadow:' . esc_attr( $media_shadow ) . ';'; ?>display:flex;flex-direction:column;transition:transform .3s ease<?php if ( $media_radius_h ) echo ',border-radius ' . (int) $media_rdur . 'ms ease'; ?>">
                            <?php if ( ! empty( $s['show_media_label'] ) && $m_label !== '' ) : ?>
                                <div style="padding:10px 14px;background:rgba(0,0,0,0.06);font-family:<?php echo esc_attr( $mono ); ?>;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:<?php echo $m_bg === '#0f172a' ? 'var(--olo-color-text-faint, #9ca3af)' : 'var(--olo-color-text-soft, #6b7280)'; ?>;display:flex;align-items:center;gap:6px">
                                    <span style="display:inline-flex;gap:4px">
                                        <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;opacity:.7"></span>
                                        <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;opacity:.7"></span>
                                        <span style="width:8px;height:8px;border-radius:50%;background:#10b981;opacity:.7"></span>
                                    </span>
                                    <span style="margin-left:8px" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.media_label'; ?>"><?php echo esc_html( $m_label ); ?></span>
                                </div>
                            <?php endif; ?>
                            <div style="flex:1;padding:14px;display:flex;align-items:center;justify-content:center;overflow:hidden">
                                <?php if ( $m_type === 'image' && $m_image ) : ?>
                                    <img src="<?php echo esc_url( $m_image ); ?>" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;object-position:<?php echo esc_attr( $obj_pos ); ?>" />
                                <?php elseif ( $m_type === 'terminal' && $m_content ) : ?>
                                    <pre style="margin:0;width:100%;font-family:<?php echo esc_attr( $mono ); ?>;font-size:11px;line-height:1.6;color:<?php echo esc_attr( $m_color ); ?>;white-space:pre-wrap"><?php echo esc_html( $m_content ); ?></pre>
                                <?php else : ?>
                                    <!-- Placeholder visivo: 3 barre stilizzate -->
                                    <div style="width:100%;display:flex;flex-direction:column;gap:8px;opacity:.7">
                                        <span style="height:8px;background:color-mix(in srgb, <?php echo esc_attr( $m_color ); ?> 20%, transparent);border-radius:4px"></span>
                                        <span style="height:8px;width:80%;background:color-mix(in srgb, <?php echo esc_attr( $m_color ); ?> 13%, transparent);border-radius:4px"></span>
                                        <span style="height:8px;width:60%;background:color-mix(in srgb, <?php echo esc_attr( $m_color ); ?> 13%, transparent);border-radius:4px"></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Pre-title -->
                        <?php if ( $pre_title !== '' ) : ?>
                            <div style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo (int) $pre_title_size; ?>px;letter-spacing:0.12em;text-transform:uppercase;color:<?php echo esc_attr( $pre_title_clr ); ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.pre_title'; ?>"><?php echo esc_html( $pre_title ); ?></div>
                        <?php endif; ?>

                        <!-- Title (base + accent + after) -->
                        <?php if ( $title !== '' || $t_accent !== '' || $t_after !== '' ) : ?>
                            <h3 style="font-family:<?php echo esc_attr( $title_family ); ?>;font-size:<?php echo (int) $title_size; ?>px;font-weight:<?php echo esc_attr( $title_w ); ?>;line-height:1.15;letter-spacing:-0.01em;color:<?php echo esc_attr( $title_clr ); ?>;margin:0">
                                <?php if ( $title !== '' ) : ?><span data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.title'; ?>"><?php echo esc_html( $title ); ?></span><?php endif; ?><?php if ( $t_accent !== '' ) : ?> <span style="color:<?php echo esc_attr( $title_accent_c ); ?>;<?php if ( $t_acc_it ) echo 'font-style:italic;'; ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.title_accent'; ?>"><?php echo esc_html( $t_accent ); ?></span><?php endif; ?><?php if ( $t_after !== '' ) : ?> <span style="<?php if ( $t_aft_it ) echo 'font-style:italic;'; ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.title_after'; ?>"><?php echo esc_html( $t_after ); ?></span><?php endif; ?>
                            </h3>
                        <?php endif; ?>

                        <!-- Description -->
                        <?php if ( $desc !== '' ) : ?>
                            <div style="font-family:<?php echo esc_attr( $sans ); ?>;font-size:<?php echo (int) $desc_size; ?>px;line-height:1.55;color:<?php echo esc_attr( $desc_clr ); ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.description'; ?>" data-olo-richtext><?php echo $desc; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized via safe_richtext_content() (wp_kses_post) or esc_html()+nl2br() above ?></div>
                        <?php endif; ?>

                        <!-- Footer metric -->
                        <?php if ( $f_val !== '' || $f_lbl !== '' ) : ?>
                            <div style="display:inline-flex;align-items:center;gap:10px;margin-top:12px;padding-top:14px;border-top:1px solid rgba(15,23,42,0.08)">
                                <?php if ( $f_icon ) : ?>
                                    <span style="color:<?php echo esc_attr( $fv_clr ); ?>;display:inline-flex;align-items:center"><?php echo $this->render_icon_html( $f_icon, 0.9 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon HTML built by Olobuild_Tile_Base::render_icon_html() (escapes/sanitizes internally) ?></span>
                                <?php endif; ?>
                                <?php if ( $f_val !== '' ) : ?>
                                    <span style="font-family:<?php echo esc_attr( $title_family ); ?>;font-size:<?php echo (int) $fv_size; ?>px;font-weight:600;color:<?php echo esc_attr( $fv_clr ); ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.footer_value'; ?>"><?php echo esc_html( $f_val ); ?></span>
                                <?php endif; ?>
                                <?php if ( $f_lbl !== '' ) : ?>
                                    <span style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo (int) $fl_size; ?>px;letter-spacing:0.08em;text-transform:uppercase;color:<?php echo esc_attr( $fl_clr ); ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.footer_label'; ?>"><?php echo esc_html( $f_lbl ); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Separator (mostrato tra step, dopo questo item ma non sull'ultimo) -->
                        <?php if ( ! empty( $s['show_separator'] ) && ! $is_last && $sep_text !== '' ) : ?>
                            <span class="olo-stl__sep" style="position:absolute;right:-<?php echo intval( $gap / 2 ); ?>px;top:50px;transform:translateX(50%) rotate(-6deg);font-family:<?php echo esc_attr( $mono ); ?>;font-size:11px;letter-spacing:0.08em;text-transform:uppercase;color:<?php echo esc_attr( $sep_clr ); ?>;white-space:nowrap;pointer-events:none" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.separator_text'; ?>"><?php echo esc_html( $sep_text ); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from sanitized values: $media_radius_h via build_border_radius_css() (integer-forced); $uid is internally generated. ?>
        <style>
            <?php if ( $media_radius_h ) : ?>
            .<?php echo $uid; ?> .olo-stl__item:hover .olo-stl__mockup { border-radius: <?php echo $media_radius_h; ?> !important; }
            <?php endif; ?>
            @media (max-width: 900px) {
                .<?php echo $uid; ?> .olo-stl__grid { grid-template-columns: 1fr !important; }
                .<?php echo $uid; ?> .olo-stl__sep { display: none !important; }
                .<?php echo $uid; ?> .olo-stl__counter { font-size: 64px !important; }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php

        return ob_get_clean();
    }
}
