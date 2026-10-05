<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Scrollprogress_Tile extends Olobuild_Tile_Base {

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
        // Vuoto = testo sul primario (token): la percentuale sta in una pillola del colore della barra.
        'percentage_color' => '',
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
        $pct_color = $this->safe_color_css( $s['percentage_color'] ) ?: 'var(--olo-color-primary-contrast, #ffffff)';

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS costruito solo dall'id interno $uid (wp_unique_id) e da colori passati da safe_color_css() con riserve a token.
        ?>
        <style>
        <?php
        // In alto: sotto la barra di amministrazione di WordPress quando c'è (utenti collegati),
        // che altrimenti la copriva (z-index 99999). La sua altezza la dà WordPress stesso
        // (--wp-admin--admin-bar--height: 32px, 46px sotto i 782px); lo script poi segue il bordo
        // inferiore reale della barra, che sui telefoni scorre via con la pagina.
        // `top` sta qui e non nello style in linea, che vincerebbe sulla regola body.admin-bar.
        if ( $pos === 'top' ) : ?>
            #<?php echo $uid; ?> { top: 0; }
            body.admin-bar #<?php echo $uid; ?> { top: var(--wp-admin--admin-bar--height, 32px); }
        <?php endif; ?>
        <?php
        // La percentuale non sta in una barra di 2-12px: pillola leggibile a lato, verso l'interno
        // della pagina, del colore della barra (testo predefinito: il contrasto del primario).
        // Misure in em: seguono la dimensione del testo della pillola.
        if ( $show_pct ) : ?>
            #<?php echo $uid; ?>-pct {
                position: absolute;
                right: 12px;
                <?php echo $pos === 'top' ? 'top' : 'bottom'; ?>: calc(100% + 6px);
                padding: .2em .7em;
                border-radius: 2em;
                background: <?php echo $bar_color; ?>;
                color: <?php echo $pct_color; ?>;
                font-size: 12px;
                font-weight: 600;
                line-height: 1.4;
                font-variant-numeric: tabular-nums;
                white-space: nowrap;
                pointer-events: none;
            }
        <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-scrollprogress" aria-hidden="true"
             style="position:fixed;<?php echo $pos === 'bottom' ? 'bottom:0;' : ''; ?>left:0;width:100%;height:<?php echo (int) $height; ?>px;background:<?php echo esc_attr( $bar_bg ); ?>;z-index:<?php echo (int) $zidx; ?>;pointer-events:none;">
            <div id="<?php echo esc_attr( $uid ); ?>-bar"
                 style="width:0%;height:100%;background:<?php echo esc_attr( $bar_color ); ?>;transition:width 0.1s linear;"></div>
            <?php if ( $show_pct ) : ?>
            <span id="<?php echo esc_attr( $uid ); ?>-pct">0%</span>
            <?php endif; ?>
        </div>
        <script>
        (function(){
            var box = document.getElementById('<?php echo esc_js( $uid ); ?>');
            var bar = document.getElementById('<?php echo esc_js( $uid ); ?>-bar');
            if(!bar) return;
            <?php if ( empty( $settings['_builder_mode'] ) ) : ?>
            /* Il contenitore del template (transform + container-type) intrappola i position:fixed:
               la barra scorreva via con la pagina. Sul sito va in document.body, come la Bottom Bar. */
            if(box && box.parentNode !== document.body){ document.body.appendChild(box); }
            <?php endif; ?>
            <?php if ( $show_pct ) : ?>
            var pctEl = document.getElementById('<?php echo esc_js( $uid ); ?>-pct');
            <?php endif; ?>
            <?php if ( $pos === 'top' ) : ?>
            // Barra di amministrazione: fissa (32/46px) o, sui telefoni, che scorre via con la pagina.
            var wpBar = document.getElementById('wpadminbar');
            function sottoBarraWp(){
                if(!wpBar || !box) return;
                var fondo = wpBar.getBoundingClientRect().bottom;
                box.style.top = (fondo > 0 ? Math.round(fondo) : 0) + 'px';
            }
            <?php else : ?>
            function sottoBarraWp(){}
            <?php endif; ?>
            function upd(){
                sottoBarraWp();
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
            window.addEventListener('resize', upd, {passive:true});
            upd();
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
