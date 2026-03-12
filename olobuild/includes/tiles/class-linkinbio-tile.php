<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_LinkInBio_Tile extends Olo_Tile_Base {

    protected $type     = 'linkinbio';
    protected $name     = 'Link in Bio';
    protected $icon     = 'dashicons-smartphone';
    protected $category = 'marketing';
    protected $defaults = [
        'items'              => [],
        'profile_image'      => '',
        'profile_name'       => 'Il tuo nome',
        'profile_bio'        => 'Una breve descrizione qui',
        'max_width'          => '420',
        'link_color'         => '#1e87f0',
        'link_bg'            => '#ffffff',
        'link_hover_bg'      => '#f3f4f6',
        'link_border_radius' => '12',
        'link_padding'       => '14',
        'gap'                => '12',
        'text_align'         => 'center',
        'profile_name_color' => '',
        'bio_color'          => '#6b7280',
        'background_color'   => '#f9fafb',
        'background_gradient' => '',
        'show_social_icons'  => false,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'items',              'type' => 'content-items', 'label' => olo_t( 'Link' ) ],
            [ 'key' => 'profile_image',      'type' => 'image',   'label' => olo_t( 'Foto profilo' ) ],
            [ 'key' => 'profile_name',       'type' => 'text',    'label' => olo_t( 'Nome' ) ],
            [ 'key' => 'profile_bio',        'type' => 'text',    'label' => olo_t( 'Bio' ) ],
            [ 'key' => 'profile_name_color', 'type' => 'color',   'label' => olo_t( 'Colore nome' ) ],
            [ 'key' => 'bio_color',          'type' => 'color',   'label' => olo_t( 'Colore bio' ) ],
            [ 'key' => 'max_width',          'type' => 'range',   'label' => olo_t( 'Larghezza max' ), 'min' => 300, 'max' => 600 ],
            [ 'key' => 'text_align',         'type' => 'select',  'label' => olo_t( 'Allineamento' ), 'options' => [ 'left' => 'Sinistra', 'center' => 'Centro', 'right' => 'Destra' ] ],
            [ 'key' => 'gap',                'type' => 'range',   'label' => olo_t( 'Gap (px)' ), 'min' => 4, 'max' => 24 ],
            [ 'key' => 'link_color',         'type' => 'color',   'label' => olo_t( 'Colore testo link' ) ],
            [ 'key' => 'link_bg',            'type' => 'color',   'label' => olo_t( 'Sfondo link' ) ],
            [ 'key' => 'link_hover_bg',      'type' => 'color',   'label' => olo_t( 'Sfondo hover' ) ],
            [ 'key' => 'link_border_radius', 'type' => 'range',   'label' => olo_t( 'Arrotondamento' ), 'min' => 0, 'max' => 30 ],
            [ 'key' => 'link_padding',       'type' => 'range',   'label' => olo_t( 'Padding' ), 'min' => 8, 'max' => 24 ],
            [ 'key' => 'background_color',   'type' => 'color',   'label' => olo_t( 'Colore sfondo' ) ],
            [ 'key' => 'background_gradient','type' => 'text',    'label' => olo_t( 'Gradiente CSS' ) ],
            [ 'key' => 'show_social_icons',  'type' => 'toggle',  'label' => olo_t( 'Mostra icone social' ) ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $items = is_array( $s['items'] ) ? $s['items'] : [];
        if ( empty( $items ) ) {
            return '<div class="olo-linkinbio" style="text-align:center;padding:20px;color:var(--olo-color-text-muted, #9CA3AF);">' . olo_t( 'Aggiungi dei link nell\'inspector.' ) . '</div>';
        }

        $max_width    = max( 300, min( 600, absint( $s['max_width'] ) ) );
        $gap          = max( 4, min( 24, absint( $s['gap'] ) ) );
        $radius       = Olo_Tile_Utils::border_radius( $s['link_border_radius'] ?? 0 );
        $padding      = max( 8, min( 24, absint( $s['link_padding'] ) ) );
        $text_align   = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'center';

        $link_color      = $this->safe_color_css( $s['link_color'] )      ?: 'var(--olo-color-primary, #6366F1)';
        $link_bg         = $this->safe_color_css( $s['link_bg'] )         ?: 'var(--olo-color-background, #FFFFFF)';
        $link_hover_bg   = $this->safe_color_css( $s['link_hover_bg'] )   ?: 'var(--olo-color-muted, #F3F4F6)';
        $name_color      = $this->safe_color_css( $s['profile_name_color'] ) ?: 'var(--olo-color-text, #374151)';
        $bio_color       = $this->safe_color_css( $s['bio_color'] )       ?: 'var(--olo-color-text-muted, #9CA3AF)';
        $bg_color        = $this->safe_color_css( $s['background_color'] ) ?: 'var(--olo-color-muted, #F3F4F6)';

        $bg_gradient = '';
        if ( ! empty( $s['background_gradient'] ) ) {
            // Basic sanitization: only allow safe gradient chars
            $bg_gradient = preg_replace( '/[^a-zA-Z0-9(),.\s%#\-deg]/', '', $s['background_gradient'] );
        }

        $bg_style = $bg_gradient ? $bg_gradient : $bg_color;

        $uid = 'olo-lib-' . wp_unique_id();

        ob_start();
        ?>
        <style>
            #<?php echo $uid; ?> { background: <?php echo $bg_style; ?>; padding: 32px 16px; display: flex; justify-content: center; }
            #<?php echo $uid; ?> .olo-lib-inner { width: 100%; max-width: <?php echo $max_width; ?>px; text-align: <?php echo $text_align; ?>; }
            #<?php echo $uid; ?> .olo-lib-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; <?php echo $text_align === 'center' ? 'margin: 0 auto 12px;' : 'margin: 0 0 12px;'; ?> display: block; }
            #<?php echo $uid; ?> .olo-lib-avatar-placeholder { width: 80px; height: 80px; border-radius: 50%; background: var(--olo-color-border, #E5E7EB); <?php echo $text_align === 'center' ? 'margin: 0 auto 12px;' : 'margin: 0 0 12px;'; ?> display: block; }
            #<?php echo $uid; ?> .olo-lib-name { font-weight: 700; font-size: 1.2em; color: <?php echo $name_color; ?>; margin: 0 0 4px; }
            #<?php echo $uid; ?> .olo-lib-bio { color: <?php echo $bio_color; ?>; font-size: 0.9em; margin: 0 0 20px; }
            #<?php echo $uid; ?> .olo-lib-links { display: flex; flex-direction: column; gap: <?php echo $gap; ?>px; }
            #<?php echo $uid; ?> .olo-lib-btn { display: block; width: 100%; text-align: <?php echo $text_align; ?>; padding: <?php echo $padding; ?>px; font-size: 0.95em; font-weight: 500; text-decoration: none; transition: all 0.2s ease; box-sizing: border-box; }
            #<?php echo $uid; ?> .olo-lib-btn--filled { background: <?php echo $link_bg; ?>; color: <?php echo $link_color; ?>; border: 1px solid rgba(0,0,0,0.08); border-radius: <?php echo $radius; ?>; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
            #<?php echo $uid; ?> .olo-lib-btn--filled:hover { background: <?php echo $link_hover_bg; ?>; }
            #<?php echo $uid; ?> .olo-lib-btn--outline { background: transparent; color: <?php echo $link_color; ?>; border: 2px solid <?php echo $link_color; ?>; border-radius: <?php echo $radius; ?>; }
            #<?php echo $uid; ?> .olo-lib-btn--outline:hover { background: <?php echo $link_hover_bg; ?>; }
            #<?php echo $uid; ?> .olo-lib-btn--minimal { background: transparent; color: <?php echo $link_color; ?>; border: none; text-decoration: underline; border-radius: <?php echo $radius; ?>; }
            #<?php echo $uid; ?> .olo-lib-btn--minimal:hover { background: <?php echo $link_hover_bg; ?>; }
            #<?php echo $uid; ?> .olo-lib-btn:hover { transform: translateY(-1px); }
        </style>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-linkinbio">
            <div class="olo-lib-inner">
                <!-- Profile -->
                <div style="margin-bottom:20px;">
                    <?php if ( ! empty( $s['profile_image'] ) ) : ?>
                        <img class="olo-lib-avatar" src="<?php echo esc_url( $s['profile_image'] ); ?>" alt="<?php echo esc_attr( $s['profile_name'] ); ?>" loading="lazy" />
                    <?php else : ?>
                        <div class="olo-lib-avatar-placeholder"></div>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['profile_name'] ) ) : ?>
                        <div class="olo-lib-name"><?php echo esc_html( $s['profile_name'] ); ?></div>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['profile_bio'] ) ) : ?>
                        <div class="olo-lib-bio"><?php echo esc_html( $s['profile_bio'] ); ?></div>
                    <?php endif; ?>
                </div>

                <!-- Links -->
                <div class="olo-lib-links">
                    <?php foreach ( $items as $item ) :
                        $title = isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '';
                        $url   = isset( $item['url'] )   ? esc_url( $item['url'] ) : '#';
                        $style = isset( $item['style'] )  ? sanitize_key( $item['style'] ) : 'filled';

                        if ( ! in_array( $style, [ 'filled', 'outline', 'minimal' ], true ) ) {
                            $style = 'filled';
                        }

                        if ( empty( $title ) ) {
                            continue;
                        }
                    ?>
                        <a href="<?php echo $url; ?>" class="olo-lib-btn olo-lib-btn--<?php echo $style; ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html( $title ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
