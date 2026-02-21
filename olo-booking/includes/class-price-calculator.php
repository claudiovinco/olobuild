<?php
/**
 * Olo Booking — Price Calculator
 *
 * Calcola prezzi, valida vincoli di soggiorno (minimo notti, settimane intere),
 * verifica chiusure e disponibilità.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Price_Calculator {

    /**
     * Calculate pricing for a stay.
     *
     * @param int    $service_id
     * @param string $checkin    Y-m-d
     * @param string $checkout   Y-m-d
     * @param int    $guests
     * @return array {success, nights, price_per_night, price_total, season_name, breakdown, errors}
     */
    public static function calculate( $service_id, $checkin, $checkout, $guests = 1 ) {
        $result = [
            'success'         => true,
            'nights'          => 0,
            'price_per_night' => 0,
            'price_total'     => 0,
            'season_name'     => '',
            'breakdown'       => [],
            'errors'          => [],
        ];

        // Basic validation
        $d_in  = new DateTime( $checkin );
        $d_out = new DateTime( $checkout );
        $diff  = $d_in->diff( $d_out );
        $nights = $diff->days;

        if ( $nights < 1 ) {
            $result['success'] = false;
            $result['errors'][] = 'La data di check-out deve essere successiva al check-in.';
            return $result;
        }

        $result['nights'] = $nights;

        // Get service data
        $base_price = floatval( get_post_meta( $service_id, '_olo_service_price', true ) );
        $capacity   = intval( get_post_meta( $service_id, '_olo_service_capacity', true ) );
        $seasons    = get_post_meta( $service_id, '_olo_service_seasons', true ) ?: [];
        $closures   = get_post_meta( $service_id, '_olo_service_closures', true ) ?: [];
        $checkin_day = get_post_meta( $service_id, '_olo_service_checkin_day', true ) ?: '';

        // Check capacity
        if ( $capacity > 0 && $guests > $capacity ) {
            $result['success'] = false;
            $result['errors'][] = "Capienza massima: {$capacity} ospiti.";
            return $result;
        }

        // Check closures
        $closure_err = self::check_closures( $checkin, $checkout, $closures );
        if ( $closure_err ) {
            $result['success'] = false;
            $result['errors'][] = $closure_err;
            return $result;
        }

        // Find applicable season
        $season = self::find_season( $checkin, $seasons );

        // Validate minimum nights
        if ( $season ) {
            $min = intval( $season['min_nights'] ?? 1 );
            if ( $nights < $min ) {
                $result['success'] = false;
                $result['errors'][] = "Soggiorno minimo {$min} notti per {$season['name']}.";
                return $result;
            }

            // Week only constraint
            if ( ! empty( $season['week_only'] ) && $season['week_only'] === '1' ) {
                if ( $nights !== 7 ) {
                    $result['success'] = false;
                    $result['errors'][] = "In {$season['name']} è richiesto soggiorno di 7 notti (settimana intera).";
                    return $result;
                }
                // Check checkin day
                if ( $checkin_day ) {
                    $day_map = [ 'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6, 'sun' => 7 ];
                    $required_day = $day_map[ $checkin_day ] ?? 0;
                    $actual_day   = (int) $d_in->format( 'N' );
                    if ( $required_day && $actual_day !== $required_day ) {
                        $day_names = [ 'mon' => 'Lunedì', 'tue' => 'Martedì', 'wed' => 'Mercoledì', 'thu' => 'Giovedì', 'fri' => 'Venerdì', 'sat' => 'Sabato', 'sun' => 'Domenica' ];
                        $result['success'] = false;
                        $result['errors'][] = "In {$season['name']} il check-in è consentito solo il " . ( $day_names[ $checkin_day ] ?? $checkin_day ) . ".";
                        return $result;
                    }
                }
            }
        }

        // Calculate price per night with seasonal breakdown
        $breakdown = self::calc_nightly_breakdown( $checkin, $nights, $base_price, $seasons );
        $total = 0;
        foreach ( $breakdown as $entry ) {
            $total += $entry['price'];
        }

        $avg_price = $nights > 0 ? round( $total / $nights, 2 ) : 0;

        $result['price_per_night'] = $avg_price;
        $result['price_total']     = round( $total, 2 );
        $result['season_name']     = $season ? $season['name'] : 'Tariffa base';
        $result['breakdown']       = $breakdown;

        return $result;
    }

    /**
     * Get month availability map for a service.
     *
     * @return array [ 'Y-m-d' => [ 'available' => bool, 'price' => float, 'min_nights' => int, 'season' => string, 'closure' => bool ] ]
     */
    public static function get_month_availability( $service_id, $year_month ) {
        $start = $year_month . '-01';
        $end   = date( 'Y-m-t', strtotime( $start ) );
        $days  = [];

        $base_price = floatval( get_post_meta( $service_id, '_olo_service_price', true ) );
        $seasons    = get_post_meta( $service_id, '_olo_service_seasons', true ) ?: [];
        $closures   = get_post_meta( $service_id, '_olo_service_closures', true ) ?: [];

        // Get booked ranges
        $booked = Olo_Booking_DB::get_booked_ranges( $service_id, $start, date( 'Y-m-d', strtotime( $end . ' +1 day' ) ) );

        $current = $start;
        while ( $current <= $end ) {
            $is_closed = self::is_date_in_closure( $current, $closures );
            $is_booked = self::is_date_booked( $current, $booked );
            $season    = self::find_season( $current, $seasons );
            $price     = $season && ! empty( $season['price_night'] ) ? floatval( $season['price_night'] ) : $base_price;
            $min_n     = $season ? intval( $season['min_nights'] ?? 1 ) : 1;

            $days[ $current ] = [
                'available'  => ! $is_closed && ! $is_booked,
                'price'      => $price,
                'min_nights' => $min_n,
                'week_only'  => $season && ! empty( $season['week_only'] ) && $season['week_only'] === '1',
                'season'     => $season ? $season['name'] : '',
                'closure'    => $is_closed,
                'booked'     => $is_booked,
            ];

            $current = date( 'Y-m-d', strtotime( $current . ' +1 day' ) );
        }

        return $days;
    }

    /* ─── Private helpers ─── */

    private static function find_season( $date, $seasons ) {
        foreach ( $seasons as $season ) {
            if ( empty( $season['date_from'] ) || empty( $season['date_to'] ) ) continue;
            if ( $date >= $season['date_from'] && $date <= $season['date_to'] ) {
                return $season;
            }
        }
        return null;
    }

    private static function check_closures( $checkin, $checkout, $closures ) {
        foreach ( $closures as $c ) {
            if ( empty( $c['date_from'] ) ) continue;
            $cf = $c['date_from'];
            $ct = ! empty( $c['date_to'] ) ? $c['date_to'] : $cf;

            // Check overlap: booking overlaps closure if checkin < closure_end+1 AND checkout > closure_start
            if ( $checkin <= $ct && $checkout > $cf ) {
                $reason = ! empty( $c['reason'] ) ? ' (' . $c['reason'] . ')' : '';
                return "La struttura non è disponibile dal {$cf} al {$ct}{$reason}.";
            }
        }
        return null;
    }

    private static function is_date_in_closure( $date, $closures ) {
        foreach ( $closures as $c ) {
            if ( empty( $c['date_from'] ) ) continue;
            $ct = ! empty( $c['date_to'] ) ? $c['date_to'] : $c['date_from'];
            if ( $date >= $c['date_from'] && $date <= $ct ) return true;
        }
        return false;
    }

    private static function is_date_booked( $date, $booked_ranges ) {
        foreach ( $booked_ranges as $b ) {
            // A date is "occupied" if it's >= checkin AND < checkout
            if ( $date >= $b['checkin_date'] && $date < $b['checkout_date'] ) return true;
        }
        return false;
    }

    private static function calc_nightly_breakdown( $checkin, $nights, $base_price, $seasons ) {
        $breakdown = [];
        $current_season = null;
        $current_price  = 0;
        $current_count  = 0;

        for ( $i = 0; $i < $nights; $i++ ) {
            $date   = date( 'Y-m-d', strtotime( $checkin . ' +' . $i . ' days' ) );
            $season = self::find_season( $date, $seasons );
            $price  = $season && ! empty( $season['price_night'] ) ? floatval( $season['price_night'] ) : $base_price;
            $name   = $season ? $season['name'] : 'Tariffa base';

            if ( $name === $current_season && $price === $current_price ) {
                $current_count++;
            } else {
                if ( $current_count > 0 ) {
                    $breakdown[] = [
                        'season' => $current_season,
                        'nights' => $current_count,
                        'price_per_night' => $current_price,
                        'price'  => round( $current_count * $current_price, 2 ),
                    ];
                }
                $current_season = $name;
                $current_price  = $price;
                $current_count  = 1;
            }
        }

        if ( $current_count > 0 ) {
            $breakdown[] = [
                'season' => $current_season,
                'nights' => $current_count,
                'price_per_night' => $current_price,
                'price'  => round( $current_count * $current_price, 2 ),
            ];
        }

        return $breakdown;
    }
}
