<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceCheckin_Tile extends Olo_Tile_Base {

    protected $type     = 'servicecheckin';
    protected $name     = 'Check-in / Check-out';
    protected $icon     = 'dashicons-clock';
    protected $category = 'booking';
    protected $defaults = [
        'meta_prefix'   => '_olo_service_',
        'bg_color'      => '#F9FAFB',
        'text_color'    => '#374151',
        'label_color'   => '',
        'border_color'  => '#E5E7EB',
        'border_radius' => 12,
        'padding'       => 16,
        'font_size'     => 14,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;
        if ( ! $post || ! is_singular() ) {
            return '<div style="padding:24px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<p style="margin:0">Inserisci in un template single.</p></div>';
        }

        $pid = $post->ID;
        $pfx = rtrim( $s['meta_prefix'], '_' ) . '_';

        $checkin_time  = get_post_meta( $pid, $pfx . 'checkin_time', true );
        $checkout_time = get_post_meta( $pid, $pfx . 'checkout_time', true );
        $min_stay      = get_post_meta( $pid, $pfx . 'min_stay', true );

        if ( ! $checkin_time && ! $checkout_time && ! $min_stay ) {
            return '';
        }

        // Stringhe traducibili via output buffer
        $t = $this->get_translatable_strings();

        $uid = 'olo-scheck-' . wp_rand( 10000, 99999 );

        $style = $this->build_style([
            'background'    => $s['bg_color'],
            'color'         => $s['text_color'],
            'border'        => "1px solid {$s['border_color']}",
            'border-radius' => Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 ),
            'padding'       => $s['padding'] . 'px',
            'font-size'     => $s['font_size'] . 'px',
        ]);

        ob_start();
        ?>
        <div class="<?php echo esc_attr( $uid ); ?>" style="<?php echo $style; ?>">
            <?php if ( $checkin_time ) : ?>
                <strong style="color:<?php echo $this->safe_color_css( $s['label_color'] ); ?>"><?php echo esc_html( $t['checkin_label'] ); ?></strong> <?php echo esc_html( $t['checkin_prefix'] . $checkin_time ); ?>
            <?php endif; ?>
            <?php if ( $checkout_time ) : ?>
                &nbsp;| &nbsp;<strong style="color:<?php echo $this->safe_color_css( $s['label_color'] ); ?>"><?php echo esc_html( $t['checkout_label'] ); ?></strong> <?php echo esc_html( $t['checkout_prefix'] . $checkout_time ); ?>
            <?php endif; ?>
            <?php if ( $min_stay ) : ?>
                <br><strong style="color:<?php echo $this->safe_color_css( $s['label_color'] ); ?>"><?php echo esc_html( $t['minstay_label'] ); ?></strong> <?php echo esc_html( $min_stay ); ?> <?php echo esc_html( (int) $min_stay === 1 ? $t['night_single'] : $t['night_plural'] ); ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Stringhe di testo del tile — centralizzate per facilitare la traduzione.
     * L'output buffer di Olo Lang le intercetta e traduce automaticamente.
     */
    private function get_translatable_strings() {
        return [
            'checkin_label'   => 'Check-in:',
            'checkin_prefix'  => 'dalle ',
            'checkout_label'  => 'Check-out:',
            'checkout_prefix' => 'entro ',
            'minstay_label'   => 'Soggiorno minimo:',
            'night_single'    => 'notte',
            'night_plural'    => 'notti',
        ];
    }
}
