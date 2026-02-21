<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Testimonial_Tile extends Olo_Tile_Base {

    protected $type     = 'testimonial';
    protected $name     = 'Testimonianza';
    protected $icon     = 'dashicons-format-quote';
    protected $category = 'content';
    protected $defaults = [
        'quote'           => 'This is an amazing product! Highly recommended.',
        'author_name'     => 'John Doe',
        'author_role'     => 'CEO, Company',
        'avatar'          => '',
        'rating'          => '5',
        'bg_color'        => '#1F2937',
        'text_color'      => '#F3F4F6',
        'show_line'       => true,
        'line_color'      => '#6366F1',
        'author_position' => 'bottom-left',
        'avatar_size'     => '48',
        'avatar_shape'    => 'circle',
        'avatar_radius'      => '6',
        'avatar_shadow'      => 'none',
        'avatar_border_width' => '0',
        'avatar_border_color' => '#FFFFFF',
        'avatar_filter'      => 'none',
        'border_radius'      => '12',
        'border_width'    => '0',
        'border_color'    => '#374151',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-test-' . wp_rand( 10000, 99999 );

        $rating      = absint( $s['rating'] );
        $bg          = $this->safe_color( $s['bg_color'] ) ?: '#1F2937';
        $fg          = $this->safe_color( $s['text_color'] ) ?: '#F3F4F6';
        $line_col    = $this->safe_color( $s['line_color'] ) ?: '#6366F1';
        $show_line   = filter_var( $s['show_line'], FILTER_VALIDATE_BOOLEAN );
        $valid_pos   = [ 'bottom-left', 'bottom-center', 'bottom-right', 'left', 'right' ];
        $position    = in_array( $s['author_position'], $valid_pos ) ? $s['author_position'] : 'bottom-left';
        $is_bottom   = strpos( $position, 'bottom' ) === 0;
        $av_size     = intval( $s['avatar_size'] ) ?: 48;
        $is_square   = $s['avatar_shape'] === 'square';
        $av_radius   = $is_square ? ( intval( $s['avatar_radius'] ) . 'px' ) : '50%';
        $tile_radius = intval( $s['border_radius'] );

        $quote       = $this->sanitize_richtext( $s['quote'] );
        $author_name = $this->sanitize_richtext( $s['author_name'] );
        $author_role = $this->sanitize_richtext( $s['author_role'] );

        $star_svg = '<svg width="18" height="18" viewBox="0 0 20 20" fill="#FBBF24" style="vertical-align:-2px;display:inline-block"><polygon points="10,1.5 12.5,7 18.5,7.6 14,11.5 15.3,17.5 10,14.5 4.7,17.5 6,11.5 1.5,7.6 7.5,7"/></svg>';

        // Bottom alignment
        $bottom_jc = 'flex-start';
        if ( $position === 'bottom-center' ) $bottom_jc = 'center';
        if ( $position === 'bottom-right' )  $bottom_jc = 'flex-end';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                background: <?php echo $bg; ?>;
                color: <?php echo $fg; ?>;
                border-radius: <?php echo $tile_radius; ?>px;
                padding: 24px;
                <?php
                $bw = intval( $s['border_width'] );
                if ( $bw > 0 ) :
                    $bc = $this->safe_color( $s['border_color'] ) ?: '#374151';
                ?>
                border: <?php echo $bw; ?>px solid <?php echo $bc; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-test-stars {
                margin-bottom: 12px;
                display: flex;
                gap: 2px;
            }
            .<?php echo $uid; ?> blockquote {
                font-size: 1.1em;
                font-style: italic;
                margin: 0;
                line-height: 1.6;
                color: <?php echo $fg; ?>;
                <?php if ( $show_line ) : ?>
                border-left: 3px solid <?php echo $line_col; ?>;
                padding-left: 16px;
                <?php else : ?>
                border-left: none;
                padding-left: 0;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-test-author img {
                width: <?php echo $av_size; ?>px;
                height: <?php echo $av_size; ?>px;
                border-radius: <?php echo $av_radius; ?>;
                object-fit: cover;
                <?php
                $shadow_map = [ 'sm' => '0 1px 3px rgba(0,0,0,.3)', 'md' => '0 4px 8px rgba(0,0,0,.3)', 'lg' => '0 8px 20px rgba(0,0,0,.4)' ];
                if ( isset( $shadow_map[ $s['avatar_shadow'] ] ) ) :
                ?>
                box-shadow: <?php echo $shadow_map[ $s['avatar_shadow'] ]; ?>;
                <?php endif; ?>
                <?php
                $abw = intval( $s['avatar_border_width'] );
                if ( $abw > 0 ) :
                    $abc = $this->safe_color( $s['avatar_border_color'] ) ?: '#FFFFFF';
                ?>
                border: <?php echo $abw; ?>px solid <?php echo $abc; ?>;
                <?php endif; ?>
                <?php
                $filter_map = [ 'grayscale' => 'grayscale(100%)', 'sepia' => 'sepia(80%)', 'brightness' => 'brightness(1.15)', 'contrast' => 'contrast(1.3)' ];
                if ( isset( $filter_map[ $s['avatar_filter'] ] ) ) :
                ?>
                filter: <?php echo $filter_map[ $s['avatar_filter'] ]; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-test-author-name {
                font-weight: 600;
                color: <?php echo $fg; ?>;
            }
            .<?php echo $uid; ?> .olo-test-author-role {
                font-size: 0.875em;
                opacity: 0.7;
                color: <?php echo $fg; ?>;
            }
            <?php if ( ! $is_bottom ) : ?>
            .<?php echo $uid; ?> .olo-test-layout {
                display: flex;
                gap: 20px;
                align-items: flex-start;
                <?php if ( $position === 'right' ) : ?>flex-direction: row-reverse;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-test-author {
                flex-shrink: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 8px;
            }
            .<?php echo $uid; ?> .olo-test-quote {
                flex: 1;
                min-width: 0;
            }
            <?php else : ?>
            .<?php echo $uid; ?> .olo-test-author-wrap {
                display: flex;
                justify-content: <?php echo $bottom_jc; ?>;
                margin-top: 20px;
            }
            .<?php echo $uid; ?> .olo-test-author {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            <?php endif; ?>
        </style>
        <div class="olo-testimonial <?php echo esc_attr( $uid ); ?>">
            <?php if ( ! $is_bottom ) : ?>
                <div class="olo-test-layout">
                    <div class="olo-test-author">
                        <?php echo $this->render_author( $s, $author_name, $author_role ); ?>
                    </div>
                    <div class="olo-test-quote">
                        <?php echo $this->render_quote( $rating, $star_svg, $quote ); ?>
                    </div>
                </div>
            <?php else : ?>
                <?php echo $this->render_quote( $rating, $star_svg, $quote ); ?>
                <div class="olo-test-author-wrap">
                    <div class="olo-test-author">
                        <?php echo $this->render_author( $s, $author_name, $author_role ); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_quote( $rating, $star_svg, $quote ) {
        $out = '';
        if ( $rating > 0 ) {
            $out .= '<div class="olo-test-stars">' . str_repeat( $star_svg, $rating ) . '</div>';
        }
        $out .= '<blockquote>' . $quote . '</blockquote>';
        return $out;
    }

    private function render_author( $s, $author_name, $author_role ) {
        $out = '';
        if ( ! empty( $s['avatar'] ) ) {
            $out .= '<img src="' . esc_url( $s['avatar'] ) . '" alt="" />';
        }
        $out .= '<div>';
        $out .= '<div class="olo-test-author-name">' . $author_name . '</div>';
        if ( ! empty( $s['author_role'] ) ) {
            $out .= '<div class="olo-test-author-role">' . $author_role . '</div>';
        }
        $out .= '</div>';
        return $out;
    }
}
