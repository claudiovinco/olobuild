<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ShatteredImage_Tile extends Olo_Tile_Base {

    protected $type     = 'shatteredimage';
    protected $name     = 'Shattered Image';
    protected $icon     = 'dashicons-shield';
    protected $category = 'media';

    protected $defaults = [
        'image_url'           => '',
        'preset'              => 'shards',
        'gap'                 => 4,
        'height'              => '400px',
        'image_position'      => 'center center',
        'gap_color'           => 'transparent',
        'zoom_variation'      => false,
        'zoom_min'            => 100,
        'zoom_max'            => 180,
        'zoom_random'         => false,
        'scroll_parallax'           => false,
        'scroll_parallax_intensity' => 30,
        'scroll_reveal'             => false,
        'scroll_reveal_stagger'     => 150,
        'scroll_reveal_duration'    => 600,
        'kenburns'            => true,
        'kenburns_duration'   => 20,
        'kenburns_style'      => 'mixed',
        'kenburns_intensity'  => 1.25,
        'overlay'             => false,
        'overlay_color'       => '#000000',
        'overlay_opacity'     => 30,
        'border_radius_outer' => 0,
        'shadow'              => 'none',
        'border_width'        => '0',
        'border_color'        => '#e5e7eb',
    ];

    public function get_controls() {
        return [];
    }

    /**
     * Preset definitions: arrays of polygons (each polygon = array of [x%, y%] vertices).
     */
    private function get_presets() {
        // Tutti i preset tassellano perfettamente il rettangolo [0,0]-[100,100].
        return [
            'shards' => [
                [[0,0],[22,0],[18,45],[0,40]],
                [[0,40],[18,45],[20,100],[0,100]],
                [[22,0],[78,0],[82,45],[80,100],[20,100],[18,45]],
                [[78,0],[100,0],[100,40],[82,45]],
                [[82,45],[100,40],[100,100],[80,100]],
            ],
            'radial_center' => [
                [[50,48],[20,0],[75,0]],
                [[50,48],[75,0],[100,0],[100,30]],
                [[50,48],[100,30],[100,72]],
                [[50,48],[100,72],[100,100],[70,100]],
                [[50,48],[70,100],[25,100]],
                [[50,48],[25,100],[0,100],[0,68]],
                [[50,48],[0,68],[0,25]],
                [[50,48],[0,25],[0,0],[20,0]],
            ],
            'shards_left' => [
                [[8,48],[0,12],[0,0],[40,0]],
                [[8,48],[40,0],[100,0],[100,35]],
                [[8,48],[100,35],[100,65]],
                [[8,48],[100,65],[100,100],[45,100]],
                [[8,48],[45,100],[0,100],[0,82]],
                [[8,48],[0,82],[0,12]],
            ],
            'shards_right' => [
                [[92,48],[100,12],[100,0],[60,0]],
                [[92,48],[60,0],[0,0],[0,35]],
                [[92,48],[0,35],[0,65]],
                [[92,48],[0,65],[0,100],[55,100]],
                [[92,48],[55,100],[100,100],[100,82]],
                [[92,48],[100,82],[100,12]],
            ],
            'shards_top' => [
                [[50,8],[88,0],[100,0],[100,40]],
                [[50,8],[100,40],[100,100],[75,100]],
                [[50,8],[75,100],[25,100]],
                [[50,8],[25,100],[0,100],[0,40]],
                [[50,8],[0,40],[0,0],[12,0]],
                [[50,8],[12,0],[88,0]],
            ],
            'shards_bottom' => [
                [[50,92],[88,100],[100,100],[100,60]],
                [[50,92],[100,60],[100,0],[75,0]],
                [[50,92],[75,0],[25,0]],
                [[50,92],[25,0],[0,0],[0,60]],
                [[50,92],[0,60],[0,100],[12,100]],
                [[50,92],[12,100],[88,100]],
            ],
            // --- Colonne ---
            'columns' => [
                [[0,0],[33.33,0],[33.33,100],[0,100]],
                [[33.33,0],[66.67,0],[66.67,100],[33.33,100]],
                [[66.67,0],[100,0],[100,100],[66.67,100]],
            ],
            'columns_4' => [
                [[0,0],[25,0],[25,100],[0,100]],
                [[25,0],[50,0],[50,100],[25,100]],
                [[50,0],[75,0],[75,100],[50,100]],
                [[75,0],[100,0],[100,100],[75,100]],
            ],
            'columns_5' => [
                [[0,0],[20,0],[20,100],[0,100]],
                [[20,0],[40,0],[40,100],[20,100]],
                [[40,0],[60,0],[60,100],[40,100]],
                [[60,0],[80,0],[80,100],[60,100]],
                [[80,0],[100,0],[100,100],[80,100]],
            ],
            'columns_6' => [
                [[0,0],[16.67,0],[16.67,100],[0,100]],
                [[16.67,0],[33.33,0],[33.33,100],[16.67,100]],
                [[33.33,0],[50,0],[50,100],[33.33,100]],
                [[50,0],[66.67,0],[66.67,100],[50,100]],
                [[66.67,0],[83.33,0],[83.33,100],[66.67,100]],
                [[83.33,0],[100,0],[100,100],[83.33,100]],
            ],
            // --- Mosaico ---
            'mosaic' => [
                [[0,0],[33.33,0],[33.33,55],[0,55]],
                [[33.33,0],[66.67,0],[66.67,55],[33.33,55]],
                [[66.67,0],[100,0],[100,55],[66.67,55]],
                [[0,55],[50,55],[50,100],[0,100]],
                [[50,55],[100,55],[100,100],[50,100]],
            ],
            'mosaic_4' => [
                [[0,0],[50,0],[50,50],[0,50]],
                [[50,0],[100,0],[100,50],[50,50]],
                [[0,50],[50,50],[50,100],[0,100]],
                [[50,50],[100,50],[100,100],[50,100]],
            ],
            'mosaic_6' => [
                [[0,0],[33.33,0],[33.33,50],[0,50]],
                [[33.33,0],[66.67,0],[66.67,50],[33.33,50]],
                [[66.67,0],[100,0],[100,50],[66.67,50]],
                [[0,50],[33.33,50],[33.33,100],[0,100]],
                [[33.33,50],[66.67,50],[66.67,100],[33.33,100]],
                [[66.67,50],[100,50],[100,100],[66.67,100]],
            ],
            // --- Diagonali ---
            'diagonal' => [
                [[0,0],[40,0],[15,100],[0,100]],
                [[40,0],[75,0],[50,100],[15,100]],
                [[75,0],[100,0],[100,100],[50,100]],
            ],
            'diagonal_4' => [
                [[0,0],[35,0],[15,100],[0,100]],
                [[35,0],[60,0],[40,100],[15,100]],
                [[60,0],[85,0],[65,100],[40,100]],
                [[85,0],[100,0],[100,100],[65,100]],
            ],
            'diagonal_5' => [
                [[0,0],[30,0],[10,100],[0,100]],
                [[30,0],[50,0],[30,100],[10,100]],
                [[50,0],[70,0],[50,100],[30,100]],
                [[70,0],[90,0],[70,100],[50,100]],
                [[90,0],[100,0],[100,100],[70,100]],
            ],
            'diagonal_reverse' => [
                [[0,0],[25,0],[50,100],[0,100]],
                [[25,0],[60,0],[85,100],[50,100]],
                [[60,0],[100,0],[100,100],[85,100]],
            ],
            // --- Esagoni ---
            'honeycomb' => [
                [[0,0],[50,0],[60,25],[50,50],[0,40]],
                [[50,0],[100,0],[100,40],[50,50],[60,25]],
                [[0,40],[50,50],[40,75],[50,100],[0,100]],
                [[50,50],[100,40],[100,100],[50,100],[40,75]],
            ],
            'honeycomb_6' => [
                [[0,0],[35,0],[30,55],[0,45]],
                [[35,0],[70,0],[70,45],[30,55]],
                [[70,0],[100,0],[100,60],[70,45]],
                [[0,45],[30,55],[35,100],[0,100]],
                [[30,55],[70,45],[65,100],[35,100]],
                [[70,45],[100,60],[100,100],[65,100]],
            ],
            'honeycomb_8' => [
                [[0,0],[25,0],[25,55],[0,45]],
                [[25,0],[50,0],[50,42],[25,55]],
                [[50,0],[75,0],[75,58],[50,42]],
                [[75,0],[100,0],[100,48],[75,58]],
                [[0,45],[25,55],[25,100],[0,100]],
                [[25,55],[50,42],[50,100],[25,100]],
                [[50,42],[75,58],[75,100],[50,100]],
                [[75,58],[100,48],[100,100],[75,100]],
            ],
        ];
    }

    /**
     * Circle preset definitions: array of { cx, cy, r } where r = visual radius as % of height.
     */
    private function get_circle_defs() {
        return [
            'circles_3' => [
                [ 'cx' => 20, 'cy' => 50, 'r' => 28 ],
                [ 'cx' => 50, 'cy' => 50, 'r' => 28 ],
                [ 'cx' => 80, 'cy' => 50, 'r' => 28 ],
            ],
            'circles_4' => [
                [ 'cx' => 28, 'cy' => 33, 'r' => 24 ],
                [ 'cx' => 72, 'cy' => 33, 'r' => 24 ],
                [ 'cx' => 28, 'cy' => 67, 'r' => 24 ],
                [ 'cx' => 72, 'cy' => 67, 'r' => 24 ],
            ],
            'circles_5' => [
                [ 'cx' => 20, 'cy' => 33, 'r' => 22 ],
                [ 'cx' => 50, 'cy' => 33, 'r' => 22 ],
                [ 'cx' => 80, 'cy' => 33, 'r' => 22 ],
                [ 'cx' => 35, 'cy' => 70, 'r' => 22 ],
                [ 'cx' => 65, 'cy' => 70, 'r' => 22 ],
            ],
            'circles_6' => [
                [ 'cx' => 20, 'cy' => 32, 'r' => 21 ],
                [ 'cx' => 50, 'cy' => 32, 'r' => 21 ],
                [ 'cx' => 80, 'cy' => 32, 'r' => 21 ],
                [ 'cx' => 20, 'cy' => 68, 'r' => 21 ],
                [ 'cx' => 50, 'cy' => 68, 'r' => 21 ],
                [ 'cx' => 80, 'cy' => 68, 'r' => 21 ],
            ],
            'circles_7' => [
                [ 'cx' => 50, 'cy' => 50, 'r' => 24 ],
                [ 'cx' => 18, 'cy' => 28, 'r' => 18 ],
                [ 'cx' => 82, 'cy' => 28, 'r' => 18 ],
                [ 'cx' => 10, 'cy' => 62, 'r' => 16 ],
                [ 'cx' => 90, 'cy' => 62, 'r' => 16 ],
                [ 'cx' => 30, 'cy' => 82, 'r' => 17 ],
                [ 'cx' => 70, 'cy' => 82, 'r' => 17 ],
            ],
            'circles_scattered' => [
                [ 'cx' => 14, 'cy' => 22, 'r' => 18 ],
                [ 'cx' => 46, 'cy' => 14, 'r' => 14 ],
                [ 'cx' => 78, 'cy' => 20, 'r' => 20 ],
                [ 'cx' => 28, 'cy' => 52, 'r' => 22 ],
                [ 'cx' => 62, 'cy' => 48, 'r' => 16 ],
                [ 'cx' => 88, 'cy' => 55, 'r' => 18 ],
                [ 'cx' => 18, 'cy' => 82, 'r' => 16 ],
                [ 'cx' => 52, 'cy' => 80, 'r' => 20 ],
                [ 'cx' => 84, 'cy' => 84, 'r' => 14 ],
            ],
        ];
    }

    /**
     * Generate polygon vertices approximating a circle.
     * Compensates for aspect ratio so circles appear visually round.
     */
    private function make_circle_poly( $cx, $cy, $r_pct_h, $cw = 600, $ch = 400, $segments = 36 ) {
        $rx  = $r_pct_h * ( $ch / $cw );
        $ry  = $r_pct_h;
        $pts = [];
        for ( $i = 0; $i < $segments; $i++ ) {
            $angle = ( 2 * M_PI * $i ) / $segments;
            $pts[] = [ $cx + $rx * cos( $angle ), $cy + $ry * sin( $angle ) ];
        }
        return $pts;
    }

    /**
     * Deterministic pseudo-random for a given seed (fragment index).
     */
    private function seeded_random( $seed ) {
        $x = sin( $seed * 127.1 + 311.7 ) * 43758.5453123;
        return $x - floor( $x );
    }

    /**
     * Shrink polygon vertices toward centroid to create gap.
     */
    private function apply_gap( $polygon, $gap_px, $container_w = 600, $container_h = 400 ) {
        if ( $gap_px <= 0 ) {
            return $polygon;
        }
        $n  = count( $polygon );
        $cx = array_sum( array_column( $polygon, 0 ) ) / $n;
        $cy = array_sum( array_column( $polygon, 1 ) ) / $n;

        $gap_x_pct = ( $gap_px / $container_w ) * 100;
        $gap_y_pct = ( $gap_px / $container_h ) * 100;
        $shrink    = min( $gap_x_pct, $gap_y_pct ) * 0.5;

        $result = [];
        foreach ( $polygon as $pt ) {
            $dx   = $pt[0] - $cx;
            $dy   = $pt[1] - $cy;
            $dist = sqrt( $dx * $dx + $dy * $dy );
            if ( $dist === 0.0 ) {
                $result[] = $pt;
                continue;
            }
            $factor   = max( 0, 1 - $shrink / $dist );
            $result[] = [ $cx + $dx * $factor, $cy + $dy * $factor ];
        }
        return $result;
    }

    /**
     * Convert polygon array to CSS clip-path polygon() value.
     */
    private function poly_to_clip( $polygon ) {
        $points = [];
        foreach ( $polygon as $pt ) {
            $points[] = number_format( $pt[0], 2 ) . '% ' . number_format( $pt[1], 2 ) . '%';
        }
        return 'polygon(' . implode( ', ', $points ) . ')';
    }

    /**
     * Ken Burns animation keyframe variants.
     */
    private function get_kb_variants( $intensity, $style = 'mixed' ) {
        $hi  = number_format( $intensity, 2 );
        $mid = number_format( max( 1.10, $intensity * 0.92 ), 2 );
        $lo  = number_format( max( 1.10, $intensity * 0.85 ), 2 );
        $top = number_format( min( 1.40, $intensity * 1.06 ), 2 );

        $styles = [
            'mixed' => [
                [ "scale({$mid}) translate(-4%,0)",    "scale({$hi}) translate(4%,0)" ],
                [ "scale({$hi}) translate(0,-4%)",     "scale({$mid}) translate(0,4%)" ],
                [ "scale({$mid}) translate(3%,3%)",    "scale({$hi}) translate(-3%,-3%)" ],
                [ "scale({$hi}) translate(-3%,3%)",    "scale({$mid}) translate(3%,-3%)" ],
                [ "scale({$lo}) translate(5%,-1%)",    "scale({$top}) translate(-3%,2%)" ],
                [ "scale({$top}) translate(-2%,-3%)",  "scale({$lo}) translate(2%,5%)" ],
            ],
            'horizontal' => [
                [ "scale({$mid}) translate(-5%,0)",    "scale({$hi}) translate(5%,0)" ],
                [ "scale({$hi}) translate(4%,0)",      "scale({$mid}) translate(-4%,0)" ],
                [ "scale({$mid}) translate(-6%,0)",    "scale({$hi}) translate(3%,0)" ],
                [ "scale({$hi}) translate(3%,0)",      "scale({$mid}) translate(-5%,0)" ],
                [ "scale({$mid}) translate(-3%,0)",    "scale({$top}) translate(6%,0)" ],
                [ "scale({$top}) translate(5%,0)",     "scale({$mid}) translate(-4%,0)" ],
            ],
            'vertical' => [
                [ "scale({$mid}) translate(0,-5%)",    "scale({$hi}) translate(0,5%)" ],
                [ "scale({$hi}) translate(0,4%)",      "scale({$mid}) translate(0,-4%)" ],
                [ "scale({$mid}) translate(0,-6%)",    "scale({$hi}) translate(0,3%)" ],
                [ "scale({$hi}) translate(0,3%)",      "scale({$mid}) translate(0,-5%)" ],
                [ "scale({$mid}) translate(0,-3%)",    "scale({$top}) translate(0,6%)" ],
                [ "scale({$top}) translate(0,5%)",     "scale({$mid}) translate(0,-4%)" ],
            ],
            'diagonal' => [
                [ "scale({$mid}) translate(-4%,-4%)",  "scale({$hi}) translate(4%,4%)" ],
                [ "scale({$hi}) translate(3%,3%)",     "scale({$mid}) translate(-3%,-3%)" ],
                [ "scale({$mid}) translate(-5%,-3%)",  "scale({$hi}) translate(3%,5%)" ],
                [ "scale({$hi}) translate(4%,2%)",     "scale({$mid}) translate(-4%,-4%)" ],
                [ "scale({$mid}) translate(-3%,-5%)",  "scale({$top}) translate(5%,3%)" ],
                [ "scale({$top}) translate(3%,4%)",    "scale({$mid}) translate(-5%,-3%)" ],
            ],
            'radial' => [
                [ "scale({$lo}) translate(0,0)",       "scale({$top}) translate(0,0)" ],
                [ "scale({$top}) translate(-2%,-2%)",  "scale({$lo}) translate(2%,2%)" ],
                [ "scale({$top}) translate(2%,-2%)",   "scale({$lo}) translate(-2%,2%)" ],
                [ "scale({$top}) translate(-2%,2%)",   "scale({$lo}) translate(2%,-2%)" ],
                [ "scale({$top}) translate(2%,2%)",    "scale({$lo}) translate(-2%,-2%)" ],
                [ "scale({$lo}) translate(0,0)",       "scale({$hi}) translate(0,0)" ],
            ],
            'rotation' => [
                [ "scale({$mid}) rotate(-2deg) translate(1%,1%)",    "scale({$hi}) rotate(2deg) translate(-1%,-1%)" ],
                [ "scale({$hi}) rotate(1.5deg) translate(-1%,0)",    "scale({$mid}) rotate(-2.5deg) translate(1%,0)" ],
                [ "scale({$mid}) rotate(-1deg) translate(0,1.5%)",   "scale({$hi}) rotate(3deg) translate(0,-1.5%)" ],
                [ "scale({$hi}) rotate(2.5deg) translate(1%,-1%)",   "scale({$mid}) rotate(-1.5deg) translate(-1%,1%)" ],
                [ "scale({$mid}) rotate(-3deg) translate(-1%,-1%)",  "scale({$hi}) rotate(1deg) translate(1%,1%)" ],
                [ "scale({$hi}) rotate(1deg) translate(0.5%,1.5%)",  "scale({$mid}) rotate(-2deg) translate(-0.5%,-1.5%)" ],
            ],
            'zoom' => [
                [ "scale({$lo}) translate(0,0)",       "scale({$top}) translate(0,0)" ],
                [ "scale({$hi}) translate(0,0)",       "scale({$lo}) translate(0,0)" ],
                [ "scale({$lo}) translate(0,0)",       "scale({$hi}) translate(0,0)" ],
                [ "scale({$top}) translate(0,0)",      "scale({$lo}) translate(0,0)" ],
                [ "scale({$lo}) translate(0,0)",       "scale({$top}) translate(0,0)" ],
                [ "scale({$hi}) translate(0,0)",       "scale({$mid}) translate(0,0)" ],
            ],
            'chaotic' => [
                [ "scale({$lo}) translate(-6%,2%)",    "scale({$top}) translate(4%,-4%)" ],
                [ "scale({$top}) translate(3%,-6%)",   "scale({$lo}) translate(-5%,3%)" ],
                [ "scale({$mid}) translate(5%,5%)",    "scale({$top}) translate(-6%,-2%)" ],
                [ "scale({$top}) translate(-4%,-5%)",  "scale({$mid}) translate(6%,4%)" ],
                [ "scale({$mid}) translate(-2%,6%)",   "scale({$top}) translate(5%,-5%)" ],
                [ "scale({$top}) translate(6%,-3%)",   "scale({$lo}) translate(-4%,6%)" ],
            ],
        ];

        return $styles[ $style ] ?? $styles['mixed'];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $image_url = esc_url( $s['image_url'] );
        if ( empty( $image_url ) ) {
            return '<div class="olo-shattered"><p style="text-align:center;color:#9ca3af;padding:40px 0">Seleziona un\'immagine</p></div>';
        }

        $preset_key   = $s['preset'] ?: 'shards';
        $gap          = max( 0, min( 16, intval( $s['gap'] ) ) );
        $zoom_on      = ! empty( $s['zoom_variation'] );
        $zoom_min     = max( 100, min( 250, intval( $s['zoom_min'] ) ) );
        $zoom_max     = max( 120, min( 350, intval( $s['zoom_max'] ) ) );
        $zoom_random  = ! empty( $s['zoom_random'] );

        // Resolve polygons: circle presets or polygon presets
        $circle_defs = $this->get_circle_defs();
        $container_h_px = intval( $s['height'] ) ?: 400;
        if ( isset( $circle_defs[ $preset_key ] ) ) {
            $polygons = [];
            foreach ( $circle_defs[ $preset_key ] as $c ) {
                $polygons[] = $this->make_circle_poly( $c['cx'], $c['cy'], $c['r'], 600, $container_h_px, 36 );
            }
        } else {
            $presets  = $this->get_presets();
            $polygons = $presets[ $preset_key ] ?? $presets['shards'];
        }
        $height     = esc_attr( $s['height'] ?: '400px' );
        $position   = esc_attr( $s['image_position'] ?: 'center center' );
        $gap_color  = $this->safe_color( $s['gap_color'] ) ?: 'transparent';
        $radius     = max( 0, min( 48, intval( $s['border_radius_outer'] ) ) );
        $bw         = max( 0, min( 10, intval( $s['border_width'] ) ) );
        $bc         = $this->safe_color( $s['border_color'] ) ?: '#e5e7eb';
        $kb_on      = ! empty( $s['kenburns'] );
        $kb_dur     = max( 10, min( 40, intval( $s['kenburns_duration'] ) ) );
        $kb_int     = max( 1.10, min( 1.40, floatval( $s['kenburns_intensity'] ) ) );
        $ov_on      = ! empty( $s['overlay'] );
        $ov_color   = $this->safe_color( $s['overlay_color'] ) ?: '#000000';
        $ov_opacity = max( 5, min( 90, intval( $s['overlay_opacity'] ) ) ) / 100;

        $shadow_map = [
            'none' => 'none',
            'sm'   => '0 1px 3px rgba(0,0,0,.12)',
            'md'   => '0 4px 12px rgba(0,0,0,.12)',
            'lg'   => '0 10px 30px rgba(0,0,0,.18)',
            'xl'   => '0 20px 50px rgba(0,0,0,.25)',
        ];
        $shadow = $shadow_map[ $s['shadow'] ] ?? 'none';

        $frag_count = count( $polygons );
        $kb_style   = $s['kenburns_style'] ?: 'mixed';
        $kb_vars    = $this->get_kb_variants( $kb_int, $kb_style );
        $uid        = 'olo-si-' . wp_unique_id();

        // Scroll effects
        $px_on       = ! empty( $s['scroll_parallax'] );
        $px_intensity = max( 10, min( 80, intval( $s['scroll_parallax_intensity'] ) ) );
        $rv_on       = ! empty( $s['scroll_reveal'] );
        $rv_stagger  = max( 50, min( 400, intval( $s['scroll_reveal_stagger'] ) ) );
        $rv_duration = max( 200, min( 1200, intval( $s['scroll_reveal_duration'] ) ) );

        ob_start();

        // Generate keyframes CSS if Ken Burns is enabled
        if ( $kb_on ) {
            echo '<style>';
            for ( $v = 0; $v < count( $kb_vars ); $v++ ) {
                echo "@keyframes {$uid}-kb{$v}{from{transform:{$kb_vars[$v][0]}}to{transform:{$kb_vars[$v][1]}}}";
            }
            echo "@media(prefers-reduced-motion:reduce){{$this->keyframes_reset($uid, count($kb_vars))}}";
            echo '</style>';
        }

        // Reveal CSS (per-instance with uid for stagger delays)
        if ( $rv_on ) {
            echo '<style>';
            $dur_s = number_format( $rv_duration / 1000, 2 );
            echo ".olo-shattered[data-si-uid=\"{$uid}\"]>div{opacity:0;transform:translateY(24px) scale(.92);transition:opacity {$dur_s}s ease,transform {$dur_s}s ease}";
            echo ".olo-shattered[data-si-uid=\"{$uid}\"].olo-si-visible>div{opacity:1;transform:none}";
            for ( $fi = 0; $fi < $frag_count; $fi++ ) {
                $delay_s = number_format( ( $rv_stagger * $fi ) / 1000, 2 );
                echo ".olo-shattered[data-si-uid=\"{$uid}\"]>div:nth-child(" . ( $fi + 1 ) . "){transition-delay:{$delay_s}s}";
            }
            echo "@media(prefers-reduced-motion:reduce){.olo-shattered[data-si-uid=\"{$uid}\"]>div{transition:none!important;opacity:1!important;transform:none!important}}";
            echo '</style>';
        }

        // Shared inline script (output once per page)
        self::maybe_output_scroll_script( $px_on, $rv_on );

        // Container data attributes
        $data_attrs = ' data-si-uid="' . esc_attr( $uid ) . '"';
        if ( $px_on ) {
            $data_attrs .= ' data-si-parallax="' . $px_intensity . '"';
        }
        if ( $rv_on ) {
            $data_attrs .= ' data-si-reveal="1"';
        }

        // Container
        $container_style = $this->build_style( [
            'position'      => 'relative',
            'width'         => '100%',
            'height'        => $height,
            'overflow'      => 'hidden',
            'border-radius' => $radius . 'px',
            'background'    => $gap_color,
            'box-shadow'    => $shadow,
            'border'        => $bw > 0 ? "{$bw}px solid {$bc}" : '',
        ] );

        echo '<div class="olo-shattered" style="' . $container_style . '"' . $data_attrs . '>';

        // Render each fragment: outer = static mask, inner = animated image
        $container_h = intval( $s['height'] ) ?: 400;
        foreach ( $polygons as $i => $poly ) {
            $shrunk   = $this->apply_gap( $poly, $gap, 600, $container_h );
            $clip     = $this->poly_to_clip( $shrunk );
            $var_idx  = $i % count( $kb_vars );

            // Outer div: maschera statica (clip-path fermo)
            $mask_parts = [
                'position'  => 'absolute',
                'inset'     => '0',
                'clip-path' => $clip,
                'overflow'  => 'hidden',
            ];
            $mask_style = $this->build_style( $mask_parts );

            // Parallax data attributes per fragment
            $frag_data = '';
            if ( $px_on ) {
                $px_x = round( sin( $i * 2.41 + 0.7 ) * 0.85 + cos( $i * 1.13 ) * 0.15, 3 );
                $px_y = round( cos( $i * 3.17 + 1.2 ) * 0.65 + sin( $i * 0.89 ) * 0.35, 3 );
                $frag_data = ' data-px-x="' . $px_x . '" data-px-y="' . $px_y . '"';
            }

            // Compute per-fragment zoom
            $bg_size = 'cover';
            if ( $zoom_on && $zoom_min < $zoom_max ) {
                if ( $zoom_random ) {
                    $zoom_pct = $zoom_min + ( $zoom_max - $zoom_min ) * $this->seeded_random( $i );
                } else {
                    $zoom_pct = $frag_count <= 1 ? $zoom_min : $zoom_min + ( $zoom_max - $zoom_min ) * ( $i / ( $frag_count - 1 ) );
                }
                $bg_size = round( $zoom_pct ) . '%';
            }

            // Inner div: immagine animata (Ken Burns si muove dentro la maschera)
            $img_style_parts = [
                'position'            => 'absolute',
                'inset'               => '0',
                'background-image'    => "url({$image_url})",
                'background-size'     => $bg_size,
                'background-position' => $position,
            ];

            if ( $kb_on ) {
                $delay = round( min( 2, ( $kb_dur / $frag_count ) * 0.3 ) * $i, 1 );
                $img_style_parts['animation']   = "{$uid}-kb{$var_idx} {$kb_dur}s {$delay}s ease-in-out infinite alternate both";
                $img_style_parts['will-change']  = 'transform';
            }

            $img_style = $this->build_style( $img_style_parts );

            echo '<div style="' . $mask_style . '"' . $frag_data . '>';
            echo '<div style="' . $img_style . '"></div>';

            // Overlay per frammento
            if ( $ov_on ) {
                $ov_style = $this->build_style( [
                    'position'       => 'absolute',
                    'inset'          => '0',
                    'background'     => $ov_color,
                    'opacity'        => $ov_opacity,
                    'pointer-events' => 'none',
                ] );
                echo '<div style="' . $ov_style . '"></div>';
            }

            echo '</div>';
        }

        echo '</div>';

        return ob_get_clean();
    }

    /**
     * Generate reduced-motion reset for keyframes.
     */
    private function keyframes_reset( $uid, $count ) {
        $css = '';
        for ( $v = 0; $v < $count; $v++ ) {
            $css .= "@keyframes {$uid}-kb{$v}{from,to{transform:none}}";
        }
        return $css;
    }

    /**
     * Output shared scroll effects script (once per page).
     */
    private static $scroll_script_output = false;
    private static function maybe_output_scroll_script( $needs_parallax, $needs_reveal ) {
        if ( self::$scroll_script_output || ( ! $needs_parallax && ! $needs_reveal ) ) {
            return;
        }
        self::$scroll_script_output = true;

        echo '<script>';
        echo '(function(){';
        echo 'if(window._oloSIscroll)return;window._oloSIscroll=1;';
        echo 'var rm=window.matchMedia("(prefers-reduced-motion:reduce)").matches;';

        // ── Parallax ──
        echo 'function initPx(){';
        echo 'if(rm)return;';
        echo 'var els=document.querySelectorAll(".olo-shattered[data-si-parallax]");';
        echo 'if(!els.length)return;';
        echo 'var ticking=false;';
        echo 'function update(){';
        echo 'var vh=window.innerHeight;';
        echo 'els.forEach(function(el){';
        echo 'var r=el.getBoundingClientRect();';
        echo 'var p=(vh-r.top)/(vh+r.height);';
        echo 'p=Math.max(0,Math.min(1,p));';
        echo 'var c=(p-0.5)*2;';  // centered: -1 to 1
        echo 'var inten=parseFloat(el.dataset.siParallax)||30;';
        echo 'var frags=el.children;';
        echo 'for(var i=0;i<frags.length;i++){';
        echo 'var fx=parseFloat(frags[i].dataset.pxX)||0;';
        echo 'var fy=parseFloat(frags[i].dataset.pxY)||0;';
        echo 'frags[i].style.transform="translate("+(fx*c*inten)+"px,"+(fy*c*inten)+"px)";';
        echo '}});ticking=false;}';
        echo 'window.addEventListener("scroll",function(){if(!ticking){ticking=true;requestAnimationFrame(update)}},{passive:true});';
        echo 'update();';
        echo '}';

        // ── Reveal ──
        echo 'function initRv(){';
        echo 'if(rm){document.querySelectorAll(".olo-shattered[data-si-reveal]").forEach(function(el){el.classList.add("olo-si-visible")});return}';
        echo 'var els=document.querySelectorAll(".olo-shattered[data-si-reveal]");';
        echo 'if(!els.length)return;';
        echo 'var obs=new IntersectionObserver(function(entries){';
        echo 'entries.forEach(function(e){';
        echo 'if(e.isIntersecting){e.target.classList.add("olo-si-visible");obs.unobserve(e.target)}';
        echo '})},{threshold:0.12});';
        echo 'els.forEach(function(el){obs.observe(el)});';
        echo '}';

        // ── Init ──
        echo 'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",function(){initPx();initRv()})}else{initPx();initRv()}';
        echo '})();';
        echo '</script>';
    }
}
