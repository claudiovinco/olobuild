<?php
/**
 * Tracks conversion results: converted, approximated, skipped, fallback items.
 * Designed to be detailed enough to guide iterative improvement of the converter.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Conversion_Report {

    private $title;
    private $source_builder;
    private $items    = [];
    private $warnings = [];
    private $errors   = [];

    public function __construct( $title, $source_builder ) {
        $this->title          = $title;
        $this->source_builder = $source_builder;
    }

    /**
     * Exact or very close mapping — no information loss.
     */
    public function add_converted( $source_type, $olo_type, $message = '' ) {
        $this->items[] = [
            'source_type' => $source_type,
            'olo_type'    => $olo_type,
            'status'      => 'converted',
            'message'     => $message ?: "Convertito {$source_type} → {$olo_type}",
            'details'     => '',
        ];
    }

    /**
     * Mapped to similar element but some settings were lost.
     */
    public function add_approximated( $source_type, $olo_type, $details ) {
        $this->items[] = [
            'source_type' => $source_type,
            'olo_type'    => $olo_type,
            'status'      => 'approximated',
            'message'     => "Approssimato {$source_type} → {$olo_type}",
            'details'     => $details,
        ];
    }

    /**
     * Element skipped entirely.
     */
    public function add_skipped( $source_type, $reason ) {
        $this->items[] = [
            'source_type' => $source_type,
            'olo_type'    => null,
            'status'      => 'skipped',
            'message'     => "Saltato {$source_type}",
            'details'     => $reason,
        ];
    }

    /**
     * No equivalent — converted to HTML tile with original data preserved.
     */
    public function add_fallback( $source_type, $details ) {
        $this->items[] = [
            'source_type' => $source_type,
            'olo_type'    => 'html',
            'status'      => 'fallback_html',
            'message'     => "Fallback HTML per {$source_type}",
            'details'     => $details,
        ];
    }

    public function add_warning( $message ) {
        $this->warnings[] = $message;
    }

    public function add_error( $message ) {
        $this->errors[] = $message;
    }

    /**
     * @return array Summary counts.
     */
    public function get_summary() {
        $counts = [
            'converted'    => 0,
            'approximated' => 0,
            'skipped'      => 0,
            'fallback_html' => 0,
            'total'        => count( $this->items ),
            'warnings'     => count( $this->warnings ),
            'errors'       => count( $this->errors ),
        ];
        foreach ( $this->items as $item ) {
            if ( isset( $counts[ $item['status'] ] ) ) {
                $counts[ $item['status'] ]++;
            }
        }
        return $counts;
    }

    /**
     * @return array Full report for JSON response.
     */
    public function to_array() {
        return [
            'title'          => $this->title,
            'source_builder' => $this->source_builder,
            'summary'        => $this->get_summary(),
            'items'          => $this->items,
            'warnings'       => $this->warnings,
            'errors'         => $this->errors,
        ];
    }
}
