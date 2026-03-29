<?php
/**
 * Builds valid OloBuild node trees (Section > Row > Column > Element).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Structure_Builder {

    /**
     * Create a valid OloBuild node with UUID.
     */
    public function build_node( $type, $settings = [], $children = [] ) {
        return [
            'id'       => Olo_Uuid_Generator::generate(),
            'type'     => $type,
            'settings' => (object) $settings,
            'children' => $children,
        ];
    }

    /**
     * Build a Section > Row > Column wrapper around child elements.
     */
    public function wrap_in_section( $children, $section_settings = [], $row_layout = '100' ) {
        $column = $this->build_node( 'column', [ 'width_default' => '1-1' ], $children );
        $row    = $this->build_node( 'row', [ 'layout' => $row_layout ], [ $column ] );
        return $this->build_node( 'section', $section_settings, [ $row ] );
    }

    /**
     * Build a Row > Column wrapper around child elements.
     */
    public function wrap_in_row( $children, $row_layout = '100' ) {
        $column = $this->build_node( 'column', [ 'width_default' => '1-1' ], $children );
        return $this->build_node( 'row', [ 'layout' => $row_layout ], [ $column ] );
    }

    /**
     * Ensure all top-level nodes are sections.
     * Orphan rows/columns/elements get wrapped in the missing structure.
     */
    public function ensure_valid_hierarchy( $nodes ) {
        if ( empty( $nodes ) ) {
            return [];
        }

        $result  = [];
        $orphans = [];

        foreach ( $nodes as $node ) {
            $type = $node['type'] ?? '';

            if ( $type === 'section' ) {
                // Flush any accumulated orphans into a section first.
                if ( ! empty( $orphans ) ) {
                    $result[] = $this->wrap_in_section( $orphans );
                    $orphans  = [];
                }
                // Ensure section children are rows.
                $node['children'] = $this->ensure_rows( $node['children'] ?? [] );
                $result[] = $node;
            } elseif ( $type === 'row' ) {
                if ( ! empty( $orphans ) ) {
                    $result[] = $this->wrap_in_section( $orphans );
                    $orphans  = [];
                }
                $node['children'] = $this->ensure_columns( $node['children'] ?? [] );
                $result[] = $this->build_node( 'section', [], [ $node ] );
            } elseif ( $type === 'column' ) {
                $orphans[] = $node;
            } else {
                // Element: accumulate as orphan.
                $orphans[] = $node;
            }
        }

        // Flush remaining orphans.
        if ( ! empty( $orphans ) ) {
            $result[] = $this->wrap_in_section( $orphans );
        }

        return $result;
    }

    /**
     * Ensure all children of a section are rows.
     */
    private function ensure_rows( $children ) {
        $result  = [];
        $orphans = [];

        foreach ( $children as $child ) {
            if ( ( $child['type'] ?? '' ) === 'row' ) {
                if ( ! empty( $orphans ) ) {
                    $result[] = $this->wrap_in_row( $orphans );
                    $orphans  = [];
                }
                $child['children'] = $this->ensure_columns( $child['children'] ?? [] );
                $result[] = $child;
            } else {
                $orphans[] = $child;
            }
        }

        if ( ! empty( $orphans ) ) {
            $result[] = $this->wrap_in_row( $orphans );
        }

        return $result;
    }

    /**
     * Ensure all children of a row are columns.
     */
    private function ensure_columns( $children ) {
        $result  = [];
        $orphans = [];

        foreach ( $children as $child ) {
            if ( ( $child['type'] ?? '' ) === 'column' ) {
                if ( ! empty( $orphans ) ) {
                    $col = $this->build_node( 'column', [ 'width_default' => '1-1' ], $orphans );
                    $result[] = $col;
                    $orphans  = [];
                }
                $result[] = $child;
            } else {
                $orphans[] = $child;
            }
        }

        if ( ! empty( $orphans ) ) {
            $result[] = $this->build_node( 'column', [ 'width_default' => '1-1' ], $orphans );
        }

        return $result;
    }

    // ─── Layout utilities ───

    /**
     * Map column percentages to OloBuild row layout preset.
     *
     * @param  float[] $percentages Array of column widths (0-100).
     * @return string  OloBuild layout string (e.g. "50-50", "33-33-33").
     */
    public function detect_row_layout( $percentages ) {
        $count = count( $percentages );

        $presets = [
            1 => [ '100' => [ 100 ] ],
            2 => [
                '50-50' => [ 50, 50 ],
                '66-33' => [ 66.66, 33.33 ],
                '33-66' => [ 33.33, 66.66 ],
                '75-25' => [ 75, 25 ],
                '25-75' => [ 25, 75 ],
            ],
            3 => [
                '33-33-33' => [ 33.33, 33.33, 33.33 ],
                '25-50-25' => [ 25, 50, 25 ],
                '50-25-25' => [ 50, 25, 25 ],
                '25-25-50' => [ 25, 25, 50 ],
            ],
            4 => [
                '25-25-25-25' => [ 25, 25, 25, 25 ],
            ],
        ];

        if ( ! isset( $presets[ $count ] ) ) {
            return 'custom';
        }

        $best_match    = 'custom';
        $best_distance = PHP_FLOAT_MAX;

        foreach ( $presets[ $count ] as $layout => $expected ) {
            $distance = 0;
            for ( $i = 0; $i < $count; $i++ ) {
                $distance += abs( ( $percentages[ $i ] ?? 0 ) - $expected[ $i ] );
            }
            if ( $distance < $best_distance ) {
                $best_distance = $distance;
                $best_match    = $layout;
            }
        }

        // Accept if total deviation is < 10%.
        return $best_distance < 10 ? $best_match : 'custom';
    }

    /**
     * Map a percentage (0-100) to OloBuild column width fraction.
     */
    public function percentage_to_fraction( $percent ) {
        $map = [
            100   => '1-1',
            83.33 => '5-6',
            80    => '4-5',
            75    => '3-4',
            66.66 => '2-3',
            60    => '3-5',
            50    => '1-2',
            40    => '2-5',
            33.33 => '1-3',
            25    => '1-4',
            20    => '1-5',
            16.66 => '1-6',
        ];

        $best       = '';
        $best_delta = PHP_FLOAT_MAX;

        foreach ( $map as $target => $fraction ) {
            $delta = abs( $percent - $target );
            if ( $delta < $best_delta ) {
                $best_delta = $delta;
                $best       = $fraction;
            }
        }

        return $best;
    }
}
