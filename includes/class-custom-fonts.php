<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olobuild_Custom_Fonts {

    private static $option_key = 'olo_custom_fonts';

    /**
     * Get all registered custom fonts.
     * Returns array of: [ 'id' => string, 'name' => string, 'variants' => [ [ 'weight' => '400', 'style' => 'normal', 'file' => 'url' ] ] ]
     */
    public static function get_fonts() {
        return get_option( self::$option_key, [] );
    }

    /**
     * Save fonts array to option.
     */
    public static function save_fonts( $fonts ) {
        update_option( self::$option_key, $fonts );
    }

    /**
     * Get upload directory for fonts.
     */
    public static function get_upload_dir() {
        $upload = wp_upload_dir();
        return $upload['basedir'] . '/olobuild-fonts';
    }

    public static function get_upload_url() {
        $upload = wp_upload_dir();
        return $upload['baseurl'] . '/olobuild-fonts';
    }

    /**
     * Ensure upload directory exists.
     */
    public static function ensure_dir() {
        $dir = self::get_upload_dir();
        if ( ! is_dir( $dir ) ) {
            wp_mkdir_p( $dir );
        }
        // Add index.php for security
        $index = $dir . '/index.php';
        if ( ! file_exists( $index ) ) {
            file_put_contents( $index, '<?php // Silence is golden.' );
        }
        return $dir;
    }

    /**
     * Upload a font file. Returns file URL on success.
     */
    public static function upload_font_file( $file ) {
        $dir = self::ensure_dir();

        // Validate extension
        $ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        if ( ! in_array( $ext, [ 'woff2', 'woff', 'ttf', 'otf' ], true ) ) {
            return new WP_Error( 'invalid_type', 'Tipo di file non supportato. Usa WOFF2, WOFF, TTF o OTF.' );
        }

        // Validate size (max 5MB)
        if ( $file['size'] > 5 * 1024 * 1024 ) {
            return new WP_Error( 'too_large', 'Il file è troppo grande (max 5MB).' );
        }

        // Generate unique filename
        $filename = sanitize_file_name( pathinfo( $file['name'], PATHINFO_FILENAME ) ) . '-' . wp_generate_password( 6, false ) . '.' . $ext;
        $dest = $dir . '/' . $filename;

        // is_uploaded_file() garantisce un upload HTTP legittimo; copy() sostituisce
        // move_uploaded_file() (vietato dal Plugin Check wp.org). Il file temporaneo
        // viene comunque rimosso da PHP a fine richiesta.
        if ( ! is_uploaded_file( $file['tmp_name'] ) || ! copy( $file['tmp_name'], $dest ) ) {
            return new WP_Error( 'upload_failed', 'Errore durante il caricamento del file.' );
        }

        return self::get_upload_url() . '/' . $filename;
    }

    /**
     * Delete a font and its files.
     */
    public static function delete_font( $font_id ) {
        $fonts = self::get_fonts();
        $dir = self::get_upload_dir();

        foreach ( $fonts as $key => $font ) {
            if ( $font['id'] === $font_id ) {
                // Delete font files
                if ( ! empty( $font['variants'] ) ) {
                    foreach ( $font['variants'] as $variant ) {
                        if ( ! empty( $variant['file'] ) ) {
                            $filename = basename( $variant['file'] );
                            $filepath = $dir . '/' . $filename;
                            if ( file_exists( $filepath ) ) {
                                wp_delete_file( $filepath );
                            }
                        }
                    }
                }
                unset( $fonts[ $key ] );
                break;
            }
        }

        self::save_fonts( array_values( $fonts ) );
        return true;
    }

    /**
     * Generate @font-face CSS for all custom fonts.
     */
    public static function generate_css() {
        $fonts = self::get_fonts();
        if ( empty( $fonts ) ) return '';

        $css = '';
        foreach ( $fonts as $font ) {
            if ( empty( $font['variants'] ) ) continue;
            foreach ( $font['variants'] as $v ) {
                if ( empty( $v['file'] ) ) continue;
                $weight = $v['weight'] ?? '400';
                $style  = $v['style'] ?? 'normal';
                $format = 'woff2';
                $ext = strtolower( pathinfo( $v['file'], PATHINFO_EXTENSION ) );
                if ( $ext === 'ttf' ) $format = 'truetype';
                elseif ( $ext === 'otf' ) $format = 'opentype';
                elseif ( $ext === 'woff' ) $format = 'woff';

                $css .= "@font-face {\n";
                $css .= "  font-family: '" . esc_attr( $font['name'] ) . "';\n";
                $css .= "  src: url('" . esc_url( $v['file'] ) . "') format('{$format}');\n";
                $css .= "  font-weight: {$weight};\n";
                $css .= "  font-style: {$style};\n";
                $css .= "  font-display: swap;\n";
                $css .= "}\n";
            }
        }
        return $css;
    }
}
