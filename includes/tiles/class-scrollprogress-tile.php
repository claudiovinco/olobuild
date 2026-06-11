<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Scrollprogress_Tile extends Olo_Tile_Base {

    protected $type     = 'scrollprogress';
    protected $name     = 'Barra Scroll';
    protected $icon     = 'dashicons-ellipsis';
    protected $category = 'interactive';
    protected $defaults = [
        'position'         => 'top',
        'bar_color'        => 'var(--olo-color-primary, #e1474f)',
        'bar_bg'           => 'var(--olo-color-border, #e5e7eb)',
        'bar_height'       => '4',
        'show_percentage'  => false,
        'percentage_color' => '#ffffff',
        'z_index'          => '9999',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'position',         'type' => 'select', 'label' => 'Posizione',            'options' => [ 'top' => 'In alto', 'bottom' => 'In basso' ] ],
            [ 'key' => 'bar_color',        'type' => 'color',  'label' => 'Colore barra' ],
            [ 'key' => 'bar_bg',           'type' => 'color',  'label' => 'Colore sfondo' ],
            [ 'key' => 'bar_height',       'type' => 'range',  'label' => 'Altezza barra (px)',   'min' => 2, 'max' => 12, 'step' => 1 ],
            [ 'key' => 'show_percentage',  'type' => 'toggle', 'label' => 'Mostra percentuale' ],
            [ 'key' => 'percentage_color', 'type' => 'color',  'label' => 'Colore percentuale' ],
            [ 'key' => 'z_index',          'type' => 'range',  'label' => 'Z-index',              'min' => 100, 'max' => 10000, 'step' => 100 ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid       = 'olo-scrollprogress-' . wp_unique_id();
        $pos       = ( $s['position'] === 'bottom' ) ? 'bottom' : 'top';
        $height    = max( 2, min( 12, absint( $s['bar_height'] ) ) );
        $bar_color = $this->safe_color_css( $s['bar_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $bar_bg    = $this->safe_color_css( $s['bar_bg'] ) ?: 'var(--olo-color-border, #e5e7eb)';
        $zidx      = absint( $s['z_index'] ) ?: 9999;
        $show_pct  = ! empty( $s['show_percentage'] );
        $pct_color = $this->safe_color_css( $s['percentage_color'] ) ?: '#ffffff';

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-scrollprogress"
             style="position:fixed;<?php echo esc_attr( $pos ); ?>:0;left:0;width:100%;height:<?php echo (int) $height; ?>px;background:<?php echo esc_attr( $bar_bg ); ?>;z-index:<?php echo (int) $zidx; ?>;pointer-events:none;">
            <div id="<?php echo esc_attr( $uid ); ?>-bar"
                 style="width:0%;height:100%;background:<?php echo esc_attr( $bar_color ); ?>;transition:width 0.1s linear;"></div>
            <?php if ( $show_pct ) : ?>
            <span id="<?php echo esc_attr( $uid ); ?>-pct"
                  style="position:absolute;right:6px;top:50%;transform:translateY(-50%);font-size:10px;font-weight:600;color:<?php echo esc_attr( $pct_color ); ?>;pointer-events:none;line-height:1;">0%</span>
            <?php endif; ?>
        </div>
        <script>
        (function(){
            var bar = document.getElementById('<?php echo esc_js( $uid ); ?>-bar');
            if(!bar) return;
            <?php if ( $show_pct ) : ?>
            var pctEl = document.getElementById('<?php echo esc_js( $uid ); ?>-pct');
            <?php endif; ?>
            function upd(){
                var docH = document.documentElement.scrollHeight - window.innerHeight;
                if(docH <= 0) return;
                var p = Math.round((window.scrollY / docH) * 100);
                if(p < 0){ p = 0; }
                if(p > 100){ p = 100; }
                bar.style.width = p + '%';
                <?php if ( $show_pct ) : ?>
                if(pctEl){ pctEl.textContent = p + '%'; }
                <?php endif; ?>
            }
            window.addEventListener('scroll', upd, {passive:true});
            upd();
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
