<?php
/**
 * Olo Booking — Email Notifications (v2 — date range)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Booking_Emails {

    public function init() {
        add_action( 'olo_booking_created', [ $this, 'on_booking_created' ], 10, 2 );
        add_action( 'olo_booking_status_changed', [ $this, 'on_status_changed' ], 10, 3 );
    }

    public function on_booking_created( $booking, $service_post ) {
        // Email to manager
        $manager_email = '';
        if ( ! empty( $booking['manager_id'] ) ) {
            $user = get_userdata( $booking['manager_id'] );
            if ( $user && $user->user_email ) {
                $manager_email = $user->user_email;
                $this->send(
                    $manager_email,
                    sprintf( 'Nuova prenotazione: %s — %s', $booking['guest_name'] ?? $booking['customer_name'] ?? '', $service_post->post_title ),
                    $this->build_manager_email( $booking, $service_post )
                );
            }
        }

        // Also notify site admin
        $admin_email = get_option( 'admin_email' );
        if ( $admin_email && $admin_email !== $manager_email ) {
            $this->send(
                $admin_email,
                sprintf( '[Nuova Prenotazione] %s — %s', $booking['guest_name'] ?? $booking['customer_name'] ?? '', $service_post->post_title ),
                $this->build_manager_email( $booking, $service_post )
            );
        }

        // Confirmation to guest
        $guest_email = $booking['guest_email'] ?? $booking['customer_email'] ?? '';
        if ( $guest_email ) {
            $this->send(
                $guest_email,
                sprintf( 'Conferma prenotazione — %s', $service_post->post_title ),
                $this->build_guest_email( $booking, $service_post )
            );
        }
    }

    public function on_status_changed( $booking, $old_status, $new_status ) {
        $guest_email = $booking['guest_email'] ?? $booking['customer_email'] ?? '';
        if ( ! $guest_email ) return;

        $service      = get_post( $booking['service_id'] );
        $service_name = $service ? $service->post_title : 'Struttura';

        $labels = [
            'confirmed' => 'Confermata',
            'cancelled' => 'Annullata',
            'pending'   => 'In attesa',
            'completed' => 'Completata',
        ];

        $guest_name = $booking['guest_name'] ?? $booking['customer_name'] ?? '';
        $checkin    = $this->format_date( $booking['checkin_date'] ?? $booking['booking_date'] ?? '' );
        $checkout   = $this->format_date( $booking['checkout_date'] ?? '' );
        $nights     = $booking['nights'] ?? 1;

        $body  = '<h2>La tua prenotazione è stata aggiornata</h2>';
        $body .= '<p>Gentile <strong>' . esc_html( $guest_name ) . '</strong>,</p>';
        $body .= '<table style="border-collapse:collapse;width:100%;max-width:500px">';
        $body .= $this->email_row( 'Struttura', $service_name );
        $body .= $this->email_row( 'Check-in', $checkin );
        if ( $checkout ) $body .= $this->email_row( 'Check-out', $checkout );
        $body .= $this->email_row( 'Notti', $nights );
        if ( ! empty( $booking['price_total'] ) ) {
            $body .= $this->email_row( 'Totale', '€ ' . number_format( (float) $booking['price_total'], 2, ',', '.' ) );
        }
        $body .= $this->email_row( 'Stato', $labels[ $new_status ] ?? $new_status );
        $body .= '</table>';

        if ( $new_status === 'confirmed' ) {
            $body .= '<p style="color:#10B981;font-weight:bold;margin-top:16px">La tua prenotazione è stata confermata! Ti aspettiamo.</p>';
        } elseif ( $new_status === 'cancelled' ) {
            $body .= '<p style="color:#EF4444;margin-top:16px">La tua prenotazione è stata annullata.</p>';
        }

        $this->send(
            $guest_email,
            sprintf( 'Prenotazione %s — %s', strtolower( $labels[ $new_status ] ?? $new_status ), $service_name ),
            $body
        );
    }

    private function build_manager_email( $booking, $service_post ) {
        $guest_name  = $booking['guest_name'] ?? $booking['customer_name'] ?? '';
        $guest_email = $booking['guest_email'] ?? $booking['customer_email'] ?? '';
        $guest_phone = $booking['guest_phone'] ?? $booking['customer_phone'] ?? '';
        $checkin     = $this->format_date( $booking['checkin_date'] ?? $booking['booking_date'] ?? '' );
        $checkout    = $this->format_date( $booking['checkout_date'] ?? '' );
        $nights      = $booking['nights'] ?? 1;
        $guests      = $booking['guest_count'] ?? 1;

        $html  = '<h2>Nuova prenotazione ricevuta</h2>';
        $html .= '<table style="border-collapse:collapse;width:100%;max-width:500px">';
        $html .= $this->email_row( 'Struttura', $service_post->post_title );
        $html .= $this->email_row( 'Ospite', $guest_name );
        $html .= $this->email_row( 'Email', $guest_email );
        $html .= $this->email_row( 'Telefono', $guest_phone );
        $html .= $this->email_row( 'Check-in', $checkin );
        if ( $checkout ) $html .= $this->email_row( 'Check-out', $checkout );
        $html .= $this->email_row( 'Notti', $nights );
        $html .= $this->email_row( 'Ospiti', $guests );
        if ( ! empty( $booking['price_total'] ) ) {
            $html .= $this->email_row( 'Totale', '€ ' . number_format( (float) $booking['price_total'], 2, ',', '.' ) );
        }
        if ( ! empty( $booking['season_name'] ) ) {
            $html .= $this->email_row( 'Stagione', $booking['season_name'] );
        }
        if ( ! empty( $booking['notes'] ) ) {
            $html .= $this->email_row( 'Note', $booking['notes'] );
        }
        $html .= '</table>';
        $html .= '<p style="margin-top:16px"><a href="' . esc_url( home_url( '/gestione/' ) ) . '">Vai al pannello di gestione</a></p>';

        return $html;
    }

    private function build_guest_email( $booking, $service_post ) {
        $guest_name = $booking['guest_name'] ?? $booking['customer_name'] ?? '';
        $checkin    = $this->format_date( $booking['checkin_date'] ?? $booking['booking_date'] ?? '' );
        $checkout   = $this->format_date( $booking['checkout_date'] ?? '' );
        $nights     = $booking['nights'] ?? 1;
        $site_name  = get_bloginfo( 'name' );

        // Get checkin time
        $checkin_time  = get_post_meta( $service_post->ID, '_olo_service_checkin_time', true ) ?: '15:00';
        $checkout_time = get_post_meta( $service_post->ID, '_olo_service_checkout_time', true ) ?: '10:00';
        $address       = get_post_meta( $service_post->ID, '_olo_service_address', true ) ?: '';

        $html  = '<h2>Prenotazione ricevuta</h2>';
        $html .= '<p>Gentile <strong>' . esc_html( $guest_name ) . '</strong>,</p>';
        $html .= '<p>la tua prenotazione è stata ricevuta con successo.</p>';
        $html .= '<table style="border-collapse:collapse;width:100%;max-width:500px">';
        $html .= $this->email_row( 'Struttura', $service_post->post_title );
        $html .= $this->email_row( 'Check-in', $checkin . ' (dalle ' . $checkin_time . ')' );
        if ( $checkout ) $html .= $this->email_row( 'Check-out', $checkout . ' (entro le ' . $checkout_time . ')' );
        $html .= $this->email_row( 'Notti', $nights );
        if ( ! empty( $booking['guest_count'] ) && $booking['guest_count'] > 1 ) {
            $html .= $this->email_row( 'Ospiti', $booking['guest_count'] );
        }
        if ( ! empty( $booking['price_total'] ) ) {
            $html .= $this->email_row( 'Totale', '€ ' . number_format( (float) $booking['price_total'], 2, ',', '.' ) );
        }
        if ( $address ) {
            $html .= $this->email_row( 'Indirizzo', $address );
        }
        $html .= '</table>';
        $html .= '<p style="margin-top:16px">Riceverai una conferma definitiva a breve.</p>';
        $html .= '<p>Grazie,<br><strong>' . esc_html( $site_name ) . '</strong></p>';

        return $html;
    }

    private function format_date( $date ) {
        if ( ! $date || $date === '0000-00-00' ) return '';
        return date_i18n( 'l j F Y', strtotime( $date ) );
    }

    private function email_row( $label, $value ) {
        if ( empty( $value ) && $value !== 0 && $value !== '0' ) return '';
        return '<tr><td style="padding:8px 12px;border-bottom:1px solid #eee;font-weight:600;color:#555;white-space:nowrap">' . esc_html( $label ) . '</td>'
             . '<td style="padding:8px 12px;border-bottom:1px solid #eee">' . esc_html( $value ) . '</td></tr>';
    }

    private function send( $to, $subject, $html_body ) {
        $site_name = get_bloginfo( 'name' );
        $headers   = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $site_name . ' <' . get_option( 'admin_email' ) . '>',
        ];

        $wrapped  = '<!DOCTYPE html><html><head><meta charset="utf-8"></head>';
        $wrapped .= '<body style="font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;color:#333;line-height:1.6;max-width:600px;margin:0 auto;padding:20px">';
        $wrapped .= $html_body;
        $wrapped .= '</body></html>';

        wp_mail( $to, $subject, $wrapped, $headers );
    }
}
