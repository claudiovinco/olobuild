<?php
/**
 * Abstract base class for all builder converters.
 *
 * Each concrete converter (Elementor, YooTheme, Divi) extends this class
 * and implements the abstract methods for its specific source format.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Olo_Converter_Interface {

    /** @var Olo_Style_Mapper */
    protected $style_mapper;

    /** @var Olo_Structure_Builder */
    protected $structure;

    public function __construct() {
        $this->style_mapper = new Olo_Style_Mapper();
        $this->structure    = new Olo_Structure_Builder();
    }

    // ─── Abstract methods (each converter implements) ───

    /** Human-readable name, e.g. "Elementor Pro". */
    abstract public function get_source_name();

    /** Machine slug, e.g. "elementor". */
    abstract public function get_source_slug();

    /** Whether the source builder plugin is active in this WordPress install. */
    abstract public function is_source_installed();

    /**
     * List pages/posts that have content from this builder.
     * @return array [ post_id => title, ... ]
     */
    abstract public function get_available_pages();

    /**
     * Parse raw source data into a normalized intermediate tree.
     * @param  mixed $raw  Raw data from DB or file.
     * @return array       Normalized tree of source nodes.
     */
    abstract protected function parse_source_data( $raw );

    /**
     * Convert a single source node to an OloBuild node.
     * @param  array                $node   Normalized source node.
     * @param  Olo_Conversion_Report $report Report to log status.
     * @return array|null           OloBuild node, or null if skipped.
     */
    abstract protected function convert_node( $node, Olo_Conversion_Report $report );

    // ─── Concrete methods ───

    /**
     * Main conversion entry point.
     *
     * @param  mixed  $source_data  Raw source data (from DB or file).
     * @param  string $title        Template title.
     * @return array  [ 'template' => OloBuild export array, 'report' => Olo_Conversion_Report ]
     */
    public function convert( $source_data, $title = 'Importato' ) {
        $report = new Olo_Conversion_Report( $title, $this->get_source_slug() );
        $tree   = $this->parse_source_data( $source_data );
        $nodes  = $this->convert_tree( $tree, $report );

        // Wrap orphan elements in Section > Row > Column if needed.
        $nodes = $this->structure->ensure_valid_hierarchy( $nodes );

        $template = [
            'olo_export' => 'template',
            'version'    => defined( 'OLO_VERSION' ) ? OLO_VERSION : '1.0.0',
            'title'      => sanitize_text_field( $title ),
            'type'       => 'page',
            'content'    => $nodes,
            'settings'   => new stdClass,
        ];

        return [
            'template' => $template,
            'report'   => $report,
        ];
    }

    /**
     * Convert from database (post ID).
     */
    public function convert_from_db( $post_id ) {
        $raw   = $this->read_from_db( $post_id );
        $title = get_the_title( $post_id ) ?: 'Importato';
        return $this->convert( $raw, $title );
    }

    /**
     * Convert from uploaded file data.
     */
    public function convert_from_file( $file_data ) {
        $raw   = $this->parse_file( $file_data );
        $title = $file_data['title'] ?? 'Importato';
        return $this->convert( $raw, $title );
    }

    /**
     * Read raw data from WordPress database.
     * @param  int   $post_id
     * @return mixed Raw source data.
     */
    abstract protected function read_from_db( $post_id );

    /**
     * Parse uploaded file content.
     * @param  array $file_data Decoded JSON from uploaded file.
     * @return mixed Raw source data in same format as read_from_db.
     */
    abstract protected function parse_file( $file_data );

    // ─── Tree walking ───

    /**
     * Recursively convert source tree nodes.
     */
    protected function convert_tree( $nodes, Olo_Conversion_Report $report ) {
        if ( ! is_array( $nodes ) ) {
            return [];
        }

        $result = [];
        foreach ( $nodes as $node ) {
            $converted = $this->convert_node( $node, $report );
            if ( null !== $converted ) {
                $result[] = $converted;
            }
        }
        return $result;
    }

    // ─── Helper factories ───

    /**
     * Create a valid OloBuild node.
     */
    protected function build_node( $type, $settings = [], $children = [] ) {
        return $this->structure->build_node( $type, $settings, $children );
    }

    /**
     * Create a fallback HTML tile preserving original data.
     */
    protected function build_fallback_html( $source_type, $original_settings, Olo_Conversion_Report $report ) {
        $json_data = wp_json_encode( $original_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
        $html      = sprintf(
            "<!-- OloBuild Converter: elemento '%s' non supportato -->\n" .
            "<!-- Dati originali:\n%s\n-->",
            esc_html( $source_type ),
            esc_html( $json_data )
        );

        $report->add_fallback( $source_type, "Convertito a HTML con dati originali preservati" );

        return $this->build_node( 'html', [ 'code' => $html ] );
    }
}
