<?php
/**
 * Parses Elementor export .json files.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Elementor_File_Parser {

    /**
     * Parse uploaded Elementor export file data.
     *
     * Elementor export format:
     * {
     *   "title": "...",
     *   "type": "page",
     *   "version": "...",
     *   "content": [ ...sections... ]
     * }
     *
     * @param  array $file_data  Decoded JSON from uploaded file.
     * @return array [ 'content' => array, 'title' => string ]
     * @throws InvalidArgumentException On invalid file format.
     */
    public static function parse( $file_data ) {
        if ( ! is_array( $file_data ) ) {
            throw new \InvalidArgumentException( 'File non valido: JSON malformato.' );
        }

        // Elementor export files have a "content" array.
        if ( isset( $file_data['content'] ) && is_array( $file_data['content'] ) ) {
            return [
                'content' => $file_data['content'],
                'title'   => $file_data['title'] ?? 'Importato da Elementor',
            ];
        }

        // Some exports are just the content array directly.
        if ( isset( $file_data[0] ) && isset( $file_data[0]['elType'] ) ) {
            return [
                'content' => $file_data,
                'title'   => 'Importato da Elementor',
            ];
        }

        throw new \InvalidArgumentException(
            'File non riconosciuto come export Elementor. ' .
            'Chiavi trovate: ' . implode( ', ', array_keys( $file_data ) )
        );
    }
}
