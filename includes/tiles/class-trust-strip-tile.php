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
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $serif = "'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif";
        $sans  = "'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif";
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
