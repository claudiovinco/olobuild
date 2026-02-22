<?php
/**
 * Olo Amenities Catalog
 *
 * Gestisce il catalogo amenities come opzione WP.
 * Migrazione lazy dal catalogo hardcoded.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Amenities_Catalog {

    const OPTION_KEY = 'olo_amenities_catalog';

    /**
     * Catalogo built-in di default (usato per migrazione iniziale).
     */
    private static function get_default_catalog() {
        return [
            'categories' => [
                [
                    'key'   => 'general',
                    'label' => 'Generali',
                    'order' => 0,
                    'items' => [
                        [ 'key' => 'wifi',       'label' => 'Wi-Fi',              'icon' => 'wifi',       'order' => 0 ],
                        [ 'key' => 'heating',    'label' => 'Riscaldamento',      'icon' => 'heating',    'order' => 1 ],
                        [ 'key' => 'aircon',     'label' => 'Aria condizionata',  'icon' => 'aircon',     'order' => 2 ],
                        [ 'key' => 'fireplace',  'label' => 'Camino / Stufa',     'icon' => 'fireplace',  'order' => 3 ],
                        [ 'key' => 'tv',         'label' => 'TV',                 'icon' => 'tv',         'order' => 4 ],
                        [ 'key' => 'pets',       'label' => 'Animali ammessi',    'icon' => 'pets',       'order' => 5 ],
                        [ 'key' => 'smoking',    'label' => 'Fumatori',           'icon' => 'smoking',    'order' => 6 ],
                        [ 'key' => 'elevator',   'label' => 'Ascensore',          'icon' => 'elevator',   'order' => 7 ],
                        [ 'key' => 'accessible', 'label' => 'Accesso disabili',   'icon' => 'accessible', 'order' => 8 ],
                    ],
                ],
                [
                    'key'   => 'kitchen',
                    'label' => 'Cucina',
                    'order' => 1,
                    'items' => [
                        [ 'key' => 'kitchen',    'label' => 'Cucina attrezzata',  'icon' => 'kitchen',    'order' => 0 ],
                        [ 'key' => 'oven',       'label' => 'Forno',              'icon' => 'oven',       'order' => 1 ],
                        [ 'key' => 'microwave',  'label' => 'Microonde',          'icon' => 'microwave',  'order' => 2 ],
                        [ 'key' => 'dishwasher', 'label' => 'Lavastoviglie',      'icon' => 'dishwasher', 'order' => 3 ],
                        [ 'key' => 'fridge',     'label' => 'Frigorifero',        'icon' => 'fridge',     'order' => 4 ],
                        [ 'key' => 'coffee',     'label' => 'Macchina caffè',     'icon' => 'coffee',     'order' => 5 ],
                        [ 'key' => 'kettle',     'label' => 'Bollitore',          'icon' => 'kettle',     'order' => 6 ],
                    ],
                ],
                [
                    'key'   => 'laundry',
                    'label' => 'Bagno e Lavanderia',
                    'order' => 2,
                    'items' => [
                        [ 'key' => 'washer',    'label' => 'Lavatrice',       'icon' => 'washer',    'order' => 0 ],
                        [ 'key' => 'dryer',     'label' => 'Asciugatrice',    'icon' => 'dryer',     'order' => 1 ],
                        [ 'key' => 'iron',      'label' => 'Ferro da stiro',  'icon' => 'iron',      'order' => 2 ],
                        [ 'key' => 'hairdryer', 'label' => 'Asciugacapelli',  'icon' => 'hairdryer', 'order' => 3 ],
                        [ 'key' => 'bathtub',   'label' => 'Vasca da bagno',  'icon' => 'bathtub',   'order' => 4 ],
                    ],
                ],
                [
                    'key'   => 'outdoor',
                    'label' => 'Esterno',
                    'order' => 3,
                    'items' => [
                        [ 'key' => 'parking', 'label' => 'Parcheggio',          'icon' => 'parking', 'order' => 0 ],
                        [ 'key' => 'garage',  'label' => 'Garage',              'icon' => 'garage',  'order' => 1 ],
                        [ 'key' => 'garden',  'label' => 'Giardino',            'icon' => 'garden',  'order' => 2 ],
                        [ 'key' => 'terrace', 'label' => 'Terrazza / Balcone',  'icon' => 'terrace', 'order' => 3 ],
                        [ 'key' => 'bbq',     'label' => 'Barbecue',            'icon' => 'bbq',     'order' => 4 ],
                        [ 'key' => 'pool',    'label' => 'Piscina',             'icon' => 'pool',    'order' => 5 ],
                        [ 'key' => 'hottub',  'label' => 'Vasca idromassaggio', 'icon' => 'hottub',  'order' => 6 ],
                    ],
                ],
                [
                    'key'   => 'sport',
                    'label' => 'Sport e Attività',
                    'order' => 4,
                    'items' => [
                        [ 'key' => 'ski',        'label' => 'Vicino piste da sci',    'icon' => 'ski',        'order' => 0 ],
                        [ 'key' => 'bikes',      'label' => 'Biciclette disponibili', 'icon' => 'bikes',      'order' => 1 ],
                        [ 'key' => 'playground', 'label' => 'Area giochi bambini',    'icon' => 'playground', 'order' => 2 ],
                        [ 'key' => 'sauna',      'label' => 'Sauna',                  'icon' => 'sauna',      'order' => 3 ],
                        [ 'key' => 'hiking',     'label' => 'Sentieri escursionistici','icon' => 'hiking',     'order' => 4 ],
                    ],
                ],
                [
                    'key'   => 'extras',
                    'label' => 'Servizi extra',
                    'order' => 5,
                    'items' => [
                        [ 'key' => 'linens',    'label' => 'Biancheria inclusa',     'icon' => 'linens',    'order' => 0 ],
                        [ 'key' => 'towels',    'label' => 'Asciugamani inclusi',    'icon' => 'towels',    'order' => 1 ],
                        [ 'key' => 'cleaning',  'label' => 'Pulizia finale inclusa', 'icon' => 'cleaning',  'order' => 2 ],
                        [ 'key' => 'crib',      'label' => 'Culla disponibile',      'icon' => 'crib',      'order' => 3 ],
                        [ 'key' => 'highchair', 'label' => 'Seggiolone',             'icon' => 'highchair', 'order' => 4 ],
                        [ 'key' => 'safe',      'label' => 'Cassaforte',             'icon' => 'safe',      'order' => 5 ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Ritorna il catalogo completo. Lazy-migrate se non esiste.
     */
    public static function get_catalog() {
        $catalog = get_option( self::OPTION_KEY, false );
        if ( $catalog === false || ! is_array( $catalog ) || empty( $catalog['categories'] ) ) {
            $catalog = self::get_default_catalog();
            update_option( self::OPTION_KEY, $catalog, false );
        }
        return $catalog;
    }

    /**
     * Salva il catalogo (con validazione).
     */
    public static function save_catalog( $data ) {
        if ( ! is_array( $data ) || empty( $data['categories'] ) || ! is_array( $data['categories'] ) ) {
            return false;
        }

        $clean = [ 'categories' => [] ];

        foreach ( $data['categories'] as $ci => $cat ) {
            if ( empty( $cat['key'] ) || empty( $cat['label'] ) ) {
                continue;
            }

            $clean_cat = [
                'key'   => sanitize_key( $cat['key'] ),
                'label' => sanitize_text_field( $cat['label'] ),
                'order' => isset( $cat['order'] ) ? intval( $cat['order'] ) : $ci,
                'items' => [],
            ];

            if ( ! empty( $cat['items'] ) && is_array( $cat['items'] ) ) {
                foreach ( $cat['items'] as $ii => $item ) {
                    if ( empty( $item['key'] ) || empty( $item['label'] ) ) {
                        continue;
                    }
                    $clean_cat['items'][] = [
                        'key'   => sanitize_key( $item['key'] ),
                        'label' => sanitize_text_field( $item['label'] ),
                        'icon'  => sanitize_text_field( $item['icon'] ?? $item['key'] ),
                        'order' => isset( $item['order'] ) ? intval( $item['order'] ) : $ii,
                    ];
                }
            }

            $clean['categories'][] = $clean_cat;
        }

        update_option( self::OPTION_KEY, $clean, false );
        return true;
    }

    /**
     * Ritorna array flat key => label (per tile Olobuild).
     */
    public static function get_all_labels() {
        $catalog = self::get_catalog();
        $labels  = [];
        foreach ( $catalog['categories'] as $cat ) {
            if ( ! empty( $cat['items'] ) ) {
                foreach ( $cat['items'] as $item ) {
                    $labels[ $item['key'] ] = $item['label'];
                }
            }
        }
        return $labels;
    }
}
