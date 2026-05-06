<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_IconTabs_Tile extends Olo_Tile_Base {

    protected $type     = 'icontabs';
    protected $name     = 'Tab a Icone';
    protected $icon     = 'dashicons-menu-alt';
    protected $category = 'interactive';
    protected $defaults = [
        'items' => [
            [ 'icon' => 'location', 'label' => 'Viaggi', 'heading' => 'HeyConad Viaggi', 'title' => 'Catalogo vacanze di primavera ed estate', 'content' => 'Destinazioni da sogno? Prenota subito la tua vacanza ideale.', 'link_text' => 'Scopri tutte le offerte', 'link_url' => '#' ],
            [ 'icon' => 'tablet',   'label' => 'Mobile', 'heading' => 'HeyConad Mobile',  'title' => 'Tariffe mobile esclusive',            'content' => 'Naviga e chiama senza pensieri a prezzi vantaggiosi.', 'link_text' => 'Scopri le tariffe',       'link_url' => '#' ],
            [ 'icon' => 'lock',     'label' => 'Tutela', 'heading' => 'HeyConad Tutela',  'title' => 'Assicurazioni pensate per te',        'content' => 'Protezione per la casa, la persona e i tuoi animali.', 'link_text' => 'Scopri i prodotti',       'link_url' => '#' ],
        ],
        'pill_bg'        => '#F5F2EB',
        'active_bg'      => '#E8622A',
        'active_color'   => '#FFFFFF',
        'inactive_color' => '#1A1A1A',
        'card_bg'        => '#F9D7D7',
        'card_radius'    => '16',
        'heading_color'  => '#E8622A',
        'title_color'    => '#1A1A1A',
        'text_color'     => '#333333',
        'link_color'     => '#2563EB',
        'default_index'  => '0',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'items',          'type' => 'custom', 'label' => 'Schede' ],
            [ 'key' => 'pill_bg',        'type' => 'color',  'label' => 'Sfondo pill' ],
            [ 'key' => 'active_bg',      'type' => 'color',  'label' => 'Sfondo attivo' ],
            [ 'key' => 'active_color',   'type' => 'color',  'label' => 'Colore icona attiva' ],
            [ 'key' => 'inactive_color', 'type' => 'color',  'label' => 'Colore icone inattive' ],
            [ 'key' => 'card_bg',        'type' => 'color',  'label' => 'Sfondo card' ],
            [ 'key' => 'card_radius',    'type' => 'range',  'label' => 'Raggio card' ],
            [ 'key' => 'heading_color',  'type' => 'color',  'label' => 'Colore occhiello' ],
            [ 'key' => 'title_color',    'type' => 'color',  'label' => 'Colore titolo' ],
            [ 'key' => 'text_color',     'type' => 'color',  'label' => 'Colore testo' ],
            [ 'key' => 'link_color',     'type' => 'color',  'label' => 'Colore link' ],
            [ 'key' => 'default_index',  'type' => 'text',   'label' => 'Scheda attiva iniziale (0-based)' ],
        ];
    }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = is_array( $s['items'] ) ? $s['items'] : [];
        if ( empty( $items ) ) return '';

        $uid           = 'oit-' . wp_rand( 10000, 99999 );
        $default       = max( 0, min( intval( $s['default_index'] ), count( $items ) - 1 ) );
        $pill_bg       = $this->safe_color_css( $s['pill_bg'] );
        $active_bg     = $this->safe_color_css( $s['active_bg'] );
        $active_color  = $this->safe_color_css( $s['active_color'] );
        $inactive_col  = $this->safe_color_css( $s['inactive_color'] );
        $card_bg       = $this->safe_color_css( $s['card_bg'] );
        $radius        = max( 0, Olo_Tile_Utils::radius_int( $s['card_radius'] ) );
        $heading_color = $this->safe_color_css( $s['heading_color'] );
        $title_color   = $this->safe_color_css( $s['title_color'] );
        $text_color    = $this->safe_color_css( $s['text_color'] );
        $link_color    = $this->safe_color_css( $s['link_color'] );

        ob_start();
        ?>
        <style>
            .<?php echo esc_attr( $uid ); ?> { display:flex; flex-direction:column; align-items:center; gap:20px; }
            .<?php echo esc_attr( $uid ); ?> .oit-pill { display:inline-flex; align-items:center; gap:6px; padding:6px; background: <?php echo $pill_bg; ?>; border-radius:999px; }
            .<?php echo esc_attr( $uid ); ?> .oit-tab { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-width:56px; height:48px; padding:0 14px; border:none; background:transparent; color: <?php echo $inactive_col; ?>; border-radius:999px; cursor:pointer; font-size:14px; font-weight:600; transition:all .25s ease; }
            .<?php echo esc_attr( $uid ); ?> .oit-tab span.oit-label { display:none; }
            .<?php echo esc_attr( $uid ); ?> .oit-tab svg { display:block; width:22px; height:22px; }
            .<?php echo esc_attr( $uid ); ?> .oit-tab.is-active { background: <?php echo $active_bg; ?>; color: <?php echo $active_color; ?>; padding: 0 22px; }
            .<?php echo esc_attr( $uid ); ?> .oit-tab.is-active span.oit-label { display:inline; }
            .<?php echo esc_attr( $uid ); ?> .oit-tab:hover:not(.is-active) { background: rgba(0,0,0,.04); }
            .<?php echo esc_attr( $uid ); ?> .oit-card { width:100%; background: <?php echo $card_bg; ?>; border-radius: <?php echo $radius; ?>px; padding:32px; }
            .<?php echo esc_attr( $uid ); ?> .oit-card .oit-heading { font-size:16px; font-weight:700; color: <?php echo $heading_color; ?>; margin:0 0 8px; letter-spacing:-.01em; }
            .<?php echo esc_attr( $uid ); ?> .oit-card .oit-title { font-size:28px; font-weight:700; color: <?php echo $title_color; ?>; margin:0 0 12px; letter-spacing:-.01em; line-height:1.2; }
            .<?php echo esc_attr( $uid ); ?> .oit-card .oit-content { font-size:15px; color: <?php echo $text_color; ?>; line-height:1.6; margin:0; }
            .<?php echo esc_attr( $uid ); ?> .oit-card .oit-link { color: <?php echo $link_color; ?>; text-decoration:underline; font-weight:600; }
            .<?php echo esc_attr( $uid ); ?> .oit-panel { display:none; }
            .<?php echo esc_attr( $uid ); ?> .oit-panel.is-active { display:block; animation: <?php echo $uid; ?>-fade .25s ease; }
            @keyframes <?php echo $uid; ?>-fade { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:none; } }
            @media (max-width: 640px) {
                .<?php echo esc_attr( $uid ); ?> .oit-card { padding:22px; }
                .<?php echo esc_attr( $uid ); ?> .oit-card .oit-title { font-size:22px; }
            }
        </style>

        <div class="olo-icontabs <?php echo esc_attr( $uid ); ?>" data-olo-icontabs>
            <div class="oit-pill" role="tablist">
                <?php foreach ( $items as $i => $item ) :
                    $icon_raw = $item['icon'] ?? '';
                    $label    = $item['label'] ?? '';
                    $active   = ( $i === $default ) ? ' is-active' : '';
                ?>
                <button type="button" class="oit-tab<?php echo $active; ?>" data-idx="<?php echo $i; ?>" role="tab" aria-selected="<?php echo $i === $default ? 'true' : 'false'; ?>" title="<?php echo esc_attr( $label ); ?>">
                    <?php if ( $icon_raw ) : ?>
                        <?php if ( preg_match( '/^[a-z][a-z0-9-]*$/', $icon_raw ) ) : ?>
                            <span uk-icon="icon: <?php echo esc_attr( $icon_raw ); ?>; ratio: 1.2"></span>
                        <?php else : ?>
                            <?php echo wp_kses( $icon_raw, [ 'svg' => [ 'width' => [], 'height' => [], 'viewbox' => [], 'fill' => [], 'stroke' => [], 'xmlns' => [] ], 'path' => [ 'd' => [], 'fill' => [], 'stroke' => [], 'stroke-width' => [], 'stroke-linecap' => [], 'stroke-linejoin' => [] ], 'circle' => [ 'cx'=>[], 'cy'=>[], 'r'=>[], 'fill'=>[], 'stroke'=>[] ], 'rect' => [ 'x'=>[], 'y'=>[], 'width'=>[], 'height'=>[], 'rx'=>[], 'fill'=>[], 'stroke'=>[] ], 'line' => [ 'x1'=>[], 'y1'=>[], 'x2'=>[], 'y2'=>[], 'stroke'=>[] ], 'polyline' => [ 'points'=>[], 'stroke'=>[] ], 'polygon' => [ 'points'=>[], 'fill'=>[] ], 'g' => [] ] ); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <span class="oit-label"><?php echo esc_html( $label ); ?></span>
                </button>
                <?php endforeach; ?>
            </div>

            <?php foreach ( $items as $i => $item ) :
                $active = ( $i === $default ) ? ' is-active' : '';
            ?>
            <div class="oit-panel<?php echo $active; ?>" data-panel="<?php echo $i; ?>" role="tabpanel">
                <div class="oit-card">
                    <?php
                    list( $itl_cls, $itl_data ) = $this->tfx_attrs( $s, 'label', $item['label'] ?? '' );
                    list( $ith_cls, $ith_data ) = $this->tfx_attrs( $s, 'heading', $item['heading'] ?? '' );
                    list( $itt_cls, $itt_data ) = $this->tfx_attrs( $s, 'title', $item['title'] ?? '' );
                    list( $itc_cls, $itc_data ) = $this->tfx_attrs( $s, 'content', wp_strip_all_tags( $item['content'] ?? '' ) );
                    ?>
                    <?php if ( ! empty( $item['heading'] ) ) : ?>
                        <div class="oit-heading<?php echo $ith_cls; ?>"<?php echo $ith_data; ?>><?php echo esc_html( $item['heading'] ); ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $item['title'] ) ) : ?>
                        <h3 class="oit-title<?php echo $itt_cls; ?>"<?php echo $itt_data; ?>><?php echo esc_html( $item['title'] ); ?></h3>
                    <?php endif; ?>
                    <?php if ( ! empty( $item['content'] ) ) : ?>
                        <p class="oit-content<?php echo $itc_cls; ?>"<?php echo $itc_data; ?>>
                            <?php echo wp_kses_post( $item['content'] ); ?>
                            <?php if ( ! empty( $item['link_text'] ) ) : ?>
                                <a class="oit-link" href="<?php echo esc_url( $item['link_url'] ?? '#' ); ?>"><?php echo esc_html( $item['link_text'] ); ?></a>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <script>
        (function () {
            var root = document.currentScript.previousElementSibling;
            if (!root || root.getAttribute('data-olo-icontabs') === null) {
                root = document.querySelector('.<?php echo esc_attr( $uid ); ?>');
            }
            if (!root || root._oloInit) return;
            root._oloInit = 1;
            var tabs = root.querySelectorAll('.oit-tab');
            var panels = root.querySelectorAll('.oit-panel');
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    var idx = tab.getAttribute('data-idx');
                    tabs.forEach(function (t) {
                        var isActive = t.getAttribute('data-idx') === idx;
                        t.classList.toggle('is-active', isActive);
                        t.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    });
                    panels.forEach(function (p) {
                        p.classList.toggle('is-active', p.getAttribute('data-panel') === idx);
                    });
                });
            });
        })();
        </script>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }
}
