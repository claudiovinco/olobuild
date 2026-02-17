<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Pricing_Tile extends Olo_Tile_Base {

    protected $type     = 'pricing';
    protected $name     = 'Listino prezzi';
    protected $icon     = 'dashicons-money-alt';
    protected $category = 'content';
    protected $defaults = [
        'plan_name'    => 'Pro Plan',
        'price'        => '29',
        'period'       => '/month',
        'features'     => "Unlimited projects\n10 GB storage\nPriority support\nCustom domain",
        'cta_text'     => 'Get Started',
        'cta_url'      => '#',
        'is_popular'   => false,
        'bg_color'     => '#1F2937',
        'accent_color' => '#6366F1',
        'text_color'   => '#F3F4F6',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'plan_name',    'type' => 'text',     'label' => 'Plan Name' ],
            [ 'key' => 'price',        'type' => 'text',     'label' => 'Price' ],
            [ 'key' => 'period',       'type' => 'text',     'label' => 'Period' ],
            [ 'key' => 'features',     'type' => 'textarea', 'label' => 'Features (one per line)' ],
            [ 'key' => 'cta_text',     'type' => 'text',     'label' => 'Button Text' ],
            [ 'key' => 'cta_url',      'type' => 'text',     'label' => 'Button URL' ],
            [ 'key' => 'is_popular',   'type' => 'toggle',   'label' => 'Popular Badge' ],
            [ 'key' => 'bg_color',     'type' => 'color',    'label' => 'Background Color' ],
            [ 'key' => 'accent_color', 'type' => 'color',    'label' => 'Accent Color' ],
            [ 'key' => 'text_color',   'type' => 'color',    'label' => 'Text Color' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $features = array_filter( array_map( 'trim', explode( "\n", $s['features'] ) ) );

        $accent = $this->safe_color( $s['accent_color'] ) ?: '#6366F1';

        ob_start();
        ?>
        <div class="olo-pricing uk-card uk-card-body uk-card-default" style="<?php echo $this->build_style( [ 'background' => $s['bg_color'], 'color' => $s['text_color'] ] ); ?>; text-align: center; position: relative; border: 2px solid <?php echo $s['is_popular'] ? $accent : 'transparent'; ?>;">
            <?php if ( $s['is_popular'] ) : ?>
                <div class="uk-label" style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: <?php echo $accent; ?>; color: #fff; padding: 4px 16px; border-radius: 20px; font-size: 0.75em; font-weight: 600; text-transform: uppercase;">Popular</div>
            <?php endif; ?>
            <h3 style="font-size: 1.25em; font-weight: 600; margin: 0 0 16px;"><?php echo wp_kses_post( $s['plan_name'] ); ?></h3>
            <div style="margin-bottom: 24px;">
                <span style="font-size: 3em; font-weight: 700; line-height: 1;">$<?php echo esc_html( $s['price'] ); ?></span>
                <span style="font-size: 0.875em; opacity: 0.7;"><?php echo wp_kses_post( $s['period'] ); ?></span>
            </div>
            <?php if ( ! empty( $features ) ) : ?>
                <ul class="uk-list uk-list-divider" style="margin: 0 0 24px; text-align: left;">
                    <?php foreach ( $features as $feature ) : ?>
                        <li style="font-size: 0.9em;">&#10003; <?php echo esc_html( $feature ); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <a href="<?php echo esc_url( $s['cta_url'] ); ?>" class="uk-button" style="display: inline-block; width: 100%; padding: 12px 24px; background: <?php echo $accent; ?>; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 1em;"><?php echo wp_kses_post( $s['cta_text'] ); ?></a>
        </div>
        <?php
        return ob_get_clean();
    }
}
