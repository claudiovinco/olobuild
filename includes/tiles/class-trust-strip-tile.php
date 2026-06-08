<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Trust Strip — riga di "garanzie" con icona + testo, intervallate da separatori.
 * Standard Olobuild: select per ogni enum, color picker globe, sfondo wrapper
 * via sistema base. Riusa render_icon_html (UIkit + Lucide).
 */
class Olo_TrustStrip_Tile extends Olo_Tile_Base {

    protected $type     = 'trust-strip';
    protected $name     = 'Trust Strip';
    protected $icon     = 'dashicons-yes-alt';
    protected $category = 'layout';
    protected $defaults = [
        'items' => [
            [ 'icon' => 'check', 'icon_color' => '', 'text' => 'Licenza <b>GPL-v3</b>' ],
            [ 'icon' => 'check', 'icon_color' => '', 'text' => '<b>WCAG 2.2 AA</b>' ],
            [ 'icon' => 'check', 'icon_color' => '', 'text' => 'Hosting <b>a scelta tua</b>' ],
            [ 'icon' => 'check', 'icon_color' => '', 'text' => 'Export <b>HTML/JSON</b> totale' ],
            [ 'icon' => 'check', 'icon_color' => '', 'text' => 'Trento, <b>Italia 🇮🇹</b>' ],
        ],
        'separator_char'  => '·',
        'separator_color' => '',
        'text_color'      => '',
        'text_size'       => 14,
        'font_family'     => 'sans-serif',
        'align'           => 'center',
        'flow'            => 'wrap',
        'gap'             => 24,
        'variant'         => 'inline',
        'logo_height'     => 18,
        'pill_bg'         => 'rgba(255,255,255,0.05)',
        'pill_border'     => 'rgba(255,255,255,0.12)',
        'pill_text_color' => '',
        'badge_bg'        => '#D8FF4A',
        'badge_color'     => '#1B2A4E',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $serif = "var(--olo-font-family-heading, 'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif)";
        $sans  = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $mono  = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
        $fmap  = [ 'serif' => $serif, 'sans-serif' => $sans, 'mono' => $mono ];
        $fam   = $fmap[ $s['font_family'] ] ?? $sans;

        $text_color = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $sep_color  = $this->safe_color_css( $s['separator_color'] ) ?: 'var(--olo-color-text-faint, #9ca3af)';
        $text_size  = max( 10, min( 24, absint( $s['text_size'] ) ) );
        $gap        = max( 4, min( 80, absint( $s['gap'] ) ) );
        $align      = in_array( $s['align'], [ 'left', 'center', 'right', 'space-between' ], true ) ? $s['align'] : 'center';
        $flow       = $s['flow'] === 'nowrap' ? 'nowrap' : 'wrap';
        $sep_char   = $s['separator_char'] ?? '';
        $variant    = ( $s['variant'] ?? 'inline' ) === 'pill' ? 'pill' : 'inline';

        $align_map = [
            'left'          => 'flex-start',
            'center'        => 'center',
            'right'         => 'flex-end',
            'space-between' => 'space-between',
        ];

        $items = is_array( $s['items'] ) ? $s['items'] : [];

        $row_style = sprintf(
            'display:flex;flex-wrap:%s;align-items:center;justify-content:%s;gap:%dpx;font-family:%s;font-size:%dpx;color:%s;line-height:1.5;',
            esc_attr( $flow ),
            esc_attr( $align_map[ $align ] ),
            $gap,
            esc_attr( $fam ),
            $text_size,
            esc_attr( $text_color )
        );

        // ── Variante PILL: ogni voce in un box "glass" (logo + label + badge) ──
        if ( $variant === 'pill' ) {
            $logo_h    = max( 10, min( 64, absint( $s['logo_height'] ?? 18 ) ) );
            $pill_bg   = $s['pill_bg'] ?? 'rgba(255,255,255,0.05)';
            $pill_bd   = $s['pill_border'] ?? 'rgba(255,255,255,0.12)';
            $pill_txt  = $this->safe_color_css( $s['pill_text_color'] ?? '' ) ?: $text_color;
            $badge_bg  = $this->safe_color_css( $s['badge_bg'] ?? '' ) ?: '#D8FF4A';
            $badge_clr = $this->safe_color_css( $s['badge_color'] ?? '' ) ?: '#1B2A4E';
            ob_start();
            ?>
            <div class="olo-tstrip olo-tstrip--pill" style="<?php echo esc_attr( $row_style ); ?>">
                <?php foreach ( $items as $idx => $it ) :
                    $logo  = $it['logo'] ?? '';
                    $text  = $it['text'] ?? '';
                    $badge = $it['badge'] ?? '';
                    $icon  = $it['icon'] ?? '';
                    if ( $logo === '' && $text === '' && $icon === '' ) { continue; }
                ?>
                    <span class="olo-tstrip__pill" style="display:inline-flex;align-items:center;gap:11px;padding:10px 16px;border-radius:100px;background:<?php echo esc_attr( $pill_bg ); ?>;border:1px solid <?php echo esc_attr( $pill_bd ); ?>;-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);white-space:nowrap">
                        <?php if ( $logo !== '' ) : ?>
                            <img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $text ) ); ?>" style="height:<?php echo $logo_h; ?>px;width:auto;display:block;flex-shrink:0" />
                        <?php elseif ( $icon !== '' ) : ?>
                            <span style="display:inline-flex;align-items:center;color:<?php echo esc_attr( $this->safe_color_css( $it['icon_color'] ?? '' ) ?: $pill_txt ); ?>"><?php echo $this->render_icon_html( $icon, 0.9 ); ?></span>
                        <?php endif; ?>
                        <?php if ( $text !== '' ) : ?>
                            <span class="olo-tstrip__pill-txt" style="color:<?php echo esc_attr( $pill_txt ); ?>;<?php echo ( $logo !== '' || $icon !== '' ) ? 'border-left:1px solid ' . esc_attr( $pill_bd ) . ';padding-left:11px;' : ''; ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.text'; ?>" data-olo-richtext><?php echo wp_kses_post( $text ); ?></span>
                        <?php endif; ?>
                        <?php if ( $badge !== '' ) : ?>
                            <span style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:9.5px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:<?php echo esc_attr( $badge_clr ); ?>;background:<?php echo esc_attr( $badge_bg ); ?>;padding:2px 7px;border-radius:5px" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.badge'; ?>"><?php echo esc_html( $badge ); ?></span>
                        <?php endif; ?>
                    </span>
                <?php endforeach; ?>
            </div>
            <style>.olo-tstrip--pill .olo-tstrip__pill{max-width:100%;box-sizing:border-box}@media(max-width:768px){.olo-tstrip--pill{flex-direction:column!important;align-items:stretch!important;gap:8px!important}.olo-tstrip--pill .olo-tstrip__pill{width:100%!important;max-width:100%!important;justify-content:center!important;align-items:center!important;flex-wrap:wrap!important}.olo-tstrip--pill .olo-tstrip__pill-txt{border-left:none!important;padding-left:0!important;text-align:center}}</style>
            <?php
            return ob_get_clean();
        }

        ob_start();
        ?>
        <div class="olo-tstrip" style="<?php echo esc_attr( $row_style ); ?>">
            <?php $last = count( $items ) - 1; foreach ( $items as $idx => $it ) :
                $icon       = $it['icon'] ?? '';
                $icon_color = $this->safe_color_css( $it['icon_color'] ?? '' ) ?: 'var(--olo-color-success, #10b981)';
                $text       = $it['text'] ?? '';
                if ( $text === '' && $icon === '' ) continue;
            ?>
                <span class="olo-tstrip__item" style="display:inline-flex;align-items:center;gap:8px">
                    <?php if ( $icon ) : ?>
                        <span style="color:<?php echo esc_attr( $icon_color ); ?>;display:inline-flex;align-items:center">
                            <?php echo $this->render_icon_html( $icon, 0.9 ); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ( $text ) : ?>
                        <span data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.text'; ?>" data-olo-richtext><?php echo wp_kses_post( $text ); ?></span>
                    <?php endif; ?>
                </span>
                <?php if ( $sep_char && $idx < $last ) : ?>
                    <span class="olo-tstrip__sep" aria-hidden="true" style="color:<?php echo esc_attr( $sep_color ); ?>;opacity:.65"><?php echo esc_html( $sep_char ); ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
