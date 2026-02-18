<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Team_Tile extends Olo_Tile_Base {

    protected $type     = 'team';
    protected $name     = 'Membro del team';
    protected $icon     = 'dashicons-businessperson';
    protected $category = 'content';
    protected $defaults = [
        'photo'       => '',
        'hover_image' => '',
        'hover_video' => '',
        'name'        => 'Jane Smith',
        'role'        => 'Lead Designer',
        'bio'         => 'Passionate about creating beautiful user experiences.',
        'link_url'    => '',
        'bg_color'    => '#1F2937',
        'text_color'  => '#F3F4F6',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'photo',      'type' => 'image',    'label' => 'Photo' ],
            [ 'key' => 'name',       'type' => 'text',     'label' => 'Name' ],
            [ 'key' => 'role',       'type' => 'text',     'label' => 'Role' ],
            [ 'key' => 'bio',        'type' => 'textarea', 'label' => 'Bio' ],
            [ 'key' => 'link_url',   'type' => 'text',     'label' => 'Profile URL' ],
            [ 'key' => 'bg_color',   'type' => 'color',    'label' => 'Background Color' ],
            [ 'key' => 'text_color', 'type' => 'color',    'label' => 'Text Color' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        ob_start();
        ?>
        <div class="olo-team uk-card uk-card-default" style="<?php echo $this->build_style( [ 'background' => $s['bg_color'], 'color' => $s['text_color'] ] ); ?>; overflow: hidden; text-align: center;">
            <?php
            $photo_is_video = ! empty( $s['photo'] ) && preg_match( '/\.(mp4|webm|ogg)(\?.*)?$/i', $s['photo'] );
            ?>
            <?php if ( ! empty( $s['photo'] ) && $photo_is_video ) : ?>
                <div class="uk-card-media-top">
                    <video src="<?php echo esc_url( $s['photo'] ); ?>" autoplay muted loop playsinline style="width: 100%; height: 250px; object-fit: cover; display: block;"></video>
                </div>
            <?php elseif ( ! empty( $s['photo'] ) ) : ?>
                <div class="uk-card-media-top">
                    <?php
                    $team_img = '<img src="' . esc_url( $s['photo'] ) . '" alt="' . esc_attr( $s['name'] ) . '" style="width: 100%; height: 250px; object-fit: cover;" />';
                    echo $this->render_hover_wrap( $team_img, $s['hover_image'] ?? '', $s['hover_video'] ?? '' );
                    ?>
                </div>
            <?php else : ?>
                <div class="uk-card-media-top">
                    <div style="width: 100%; height: 250px; background: #374151; display: flex; align-items: center; justify-content: center; font-size: 4em;">&#x1F464;</div>
                </div>
            <?php endif; ?>
            <div class="uk-card-body">
                <h3 style="font-size: 1.25em; font-weight: 600; margin: 0 0 4px;"><?php echo wp_kses_post( $s['name'] ); ?></h3>
                <div style="font-size: 0.875em; opacity: 0.7; margin-bottom: 12px;"><?php echo wp_kses_post( $s['role'] ); ?></div>
                <?php if ( ! empty( $s['bio'] ) ) : ?>
                    <div style="font-size: 0.9em; opacity: 0.8; margin: 0; line-height: 1.5;"><?php echo wp_kses_post( $s['bio'] ); ?></div>
                <?php endif; ?>
                <?php if ( ! empty( $s['link_url'] ) ) : ?>
                    <a href="<?php echo esc_url( $s['link_url'] ); ?>" style="display: inline-block; margin-top: 12px; color: #6366F1; text-decoration: none; font-weight: 500;">View Profile &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
