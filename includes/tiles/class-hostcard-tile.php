<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_HostCard_Tile extends Olo_Tile_Base {

    protected $type     = 'hostcard';
    protected $name     = 'Host Card';
    protected $icon     = 'dashicons-admin-users';
    protected $category = 'booking';
    protected $defaults = [
        'source'          => 'auto',
        'manual_user_id'  => '',
        'variant'         => 'card',
        'button_text'     => 'Il tuo gestore',
        'button_style'    => 'primary',
        'button_icon'     => 'user',
        'show_photo'      => true,
        'show_bio'        => true,
        'show_phone'      => true,
        'show_email'      => true,
        'show_languages'  => true,
        'show_services'   => false,
        'label_role'      => 'Gestore della struttura',
        'photo_size'      => '100',
        'photo_rounded'   => true,
        'accent_color'    => '',
        'card_bg'         => '#ffffff',
        'card_radius'     => '12',
        'modal_shadow'    => 'lg',
        'modal_overlay'   => '60',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-hc-' . wp_rand( 10000, 99999 );

        // Determine user ID
        $user_id = 0;
        if ( $s['source'] === 'manual' && ! empty( $s['manual_user_id'] ) ) {
            $user_id = absint( $s['manual_user_id'] );
        } else {
            global $post;
            if ( $post ) {
                $user_id = absint( get_post_meta( $post->ID, '_olo_service_manager', true ) );
            }
        }

        if ( ! $user_id ) {
            return '<div class="olo-hostcard olo-hostcard--empty" style="padding:24px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<span uk-icon="icon:user;ratio:1.5" style="margin-bottom:8px;display:block"></span>'
                 . '<p style="margin:0">' . esc_html( olo_t( 'Nessun gestore assegnato' ) ) . '</p>'
                 . '</div>';
        }

        $user = get_userdata( $user_id );
        if ( ! $user ) {
            return '<div class="olo-hostcard olo-hostcard--empty" style="padding:24px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<p style="margin:0">' . esc_html( olo_t( 'Gestore non trovato' ) ) . '</p>'
                 . '</div>';
        }

        // Gather data
        $name       = $user->display_name;
        $bio        = get_user_meta( $user_id, 'olo_bio', true );
        $email      = get_user_meta( $user_id, 'olo_public_email', true );
        $phone      = get_user_meta( $user_id, 'olo_public_phone', true );
        $languages  = get_user_meta( $user_id, 'olo_languages', true );
        $photo_id   = get_user_meta( $user_id, 'olo_photo_id', true );
        $photo_url  = '';

        if ( $photo_id ) {
            $photo_url = wp_get_attachment_image_url( absint( $photo_id ), 'medium' );
        }

        if ( is_string( $languages ) ) {
            $languages = maybe_unserialize( $languages );
        }
        if ( ! is_array( $languages ) ) {
            $languages = array_filter( array_map( 'trim', explode( ',', (string) $languages ) ) );
        }

        // Settings
        $variant     = $s['variant'] ?: 'card';
        $photo_size  = max( 60, min( 200, absint( $s['photo_size'] ) ) );
        $rounded     = ! empty( $s['photo_rounded'] );
        $radius      = $this->build_border_radius_css( $s["card_radius"] );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['card_radius_hover'] ?? null );
        $accent      = $this->safe_color_css( $s['accent_color'] ?? '' ) ?: 'var(--olo-color-primary, #6366F1)';
        $card_bg     = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'var(--olo-color-background, #FFFFFF)';
        $shadow      = Olo_Tile_Utils::shadow( $s['modal_shadow'] ?? 'lg' );
        $overlay_pct = max( 0, min( 100, intval( $s['modal_overlay'] ?? 60 ) ) );
        $overlay_a   = round( $overlay_pct / 100, 2 );
        $role_label  = $s['label_role'] ?: '';

        // Services managed by this user
        $services_html = '';
        if ( ! empty( $s['show_services'] ) && post_type_exists( 'olo_service' ) ) {
            $managed = get_posts( [
                'post_type'      => 'olo_service',
                'post_status'    => 'publish',
                'posts_per_page' => 20,
                'meta_key'       => '_olo_service_manager',
                'meta_value'     => $user_id,
                'orderby'        => 'title',
                'order'          => 'ASC',
            ] );
            if ( ! empty( $managed ) ) {
                $services_html .= '<div class="olo-hostcard-services">';
                $services_html .= '<h4 class="olo-hostcard-services-title">' . esc_html( olo_t( 'Strutture gestite' ) ) . '</h4>';
                $services_html .= '<ul>';
                foreach ( $managed as $svc ) {
                    $services_html .= '<li><a href="' . esc_url( get_permalink( $svc->ID ) ) . '">' . esc_html( $svc->post_title ) . '</a></li>';
                }
                $services_html .= '</ul></div>';
            }
        }

        // Build content sections HTML
        $content_html = $this->build_content_html( $s, $name, $bio, $email, $phone, $languages, $photo_url, $photo_size, $rounded, $role_label, $services_html );

        ob_start();
        ?>
        <style>
            #<?php echo esc_attr( $uid ); ?>-modal { background: rgba(0,0,0,<?php echo $overlay_a; ?>) !important; }
            #<?php echo esc_attr( $uid ); ?>-modal .uk-modal-dialog {
                border-radius: <?php echo $radius; ?>;
                overflow: hidden;
                <?php if ( $shadow !== 'none' ) : ?>box-shadow: <?php echo $shadow; ?>;<?php endif; ?>
                max-width: 480px;
            }
            <?php if ( $radius_hover_css !== '' ) : ?>#<?php echo esc_attr( $uid ); ?>-modal .uk-modal-dialog{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}#<?php echo esc_attr( $uid ); ?>-modal .uk-modal-dialog:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
        </style>
        <?php

        if ( $variant === 'button' ) {
            echo $this->render_button_variant( $s, $uid, $content_html, $accent, $card_bg, $radius );
        } elseif ( $variant === 'inline' ) {
            echo $this->render_inline_variant( $uid, $content_html, $card_bg, $radius, $shadow );
        } else {
            echo $this->render_card_variant( $uid, $name, $photo_url, $photo_size, $rounded, $role_label, $content_html, $accent, $card_bg, $radius, $shadow );
        }

        return ob_get_clean();
    }

    /**
     * Build the full content HTML (photo + name + bio + contacts + languages + services).
     */
    private function build_content_html( $s, $name, $bio, $email, $phone, $languages, $photo_url, $photo_size, $rounded, $role_label, $services_html ) {
        $html = '<div class="olo-hostcard-header">';

        // Photo
        if ( ! empty( $s['show_photo'] ) ) {
            $br = $rounded ? '50%' : '8px';
            if ( $photo_url ) {
                $html .= '<img class="olo-hostcard-photo" src="' . esc_url( $photo_url ) . '" alt="' . esc_attr( $name ) . '" '
                       . 'style="width:' . $photo_size . 'px;height:' . $photo_size . 'px;border-radius:' . $br . ';object-fit:cover;" loading="lazy" />';
            } else {
                $html .= '<div class="olo-hostcard-photo olo-hostcard-photo--placeholder" '
                       . 'style="width:' . $photo_size . 'px;height:' . $photo_size . 'px;border-radius:' . $br . ';background:var(--olo-color-border, #E5E7EB);display:flex;align-items:center;justify-content:center;">'
                       . '<span uk-icon="icon:user;ratio:2" style="color:var(--olo-color-text-muted, #9CA3AF)"></span></div>';
            }
        }

        $html .= '<div class="olo-hostcard-name">' . esc_html( $name ) . '</div>';
        if ( $role_label ) {
            $html .= '<div class="olo-hostcard-role">' . esc_html( olo_t( $role_label ) ) . '</div>';
        }
        $html .= '</div>';

        // Body
        $html .= '<div class="olo-hostcard-body">';

        // Bio
        if ( ! empty( $s['show_bio'] ) && $bio ) {
            $html .= '<div class="olo-hostcard-bio">' . wp_kses_post( wpautop( $bio ) ) . '</div>';
        }

        // Contacts
        $has_contacts = ( ! empty( $s['show_phone'] ) && $phone ) || ( ! empty( $s['show_email'] ) && $email );
        if ( $has_contacts ) {
            $html .= '<div class="olo-hostcard-contacts">';
            if ( ! empty( $s['show_phone'] ) && $phone ) {
                $html .= '<a href="tel:' . esc_attr( $phone ) . '" class="olo-hostcard-contact-item">'
                       . '<span uk-icon="icon:receiver;ratio:0.9"></span> '
                       . esc_html( $phone ) . '</a>';
            }
            if ( ! empty( $s['show_email'] ) && $email ) {
                $html .= '<a href="mailto:' . esc_attr( $email ) . '" class="olo-hostcard-contact-item">'
                       . '<span uk-icon="icon:mail;ratio:0.9"></span> '
                       . esc_html( $email ) . '</a>';
            }
            $html .= '</div>';
        }

        // Languages
        if ( ! empty( $s['show_languages'] ) && ! empty( $languages ) ) {
            $html .= '<div class="olo-hostcard-languages">';
            $html .= '<span class="olo-hostcard-languages-label">' . esc_html( olo_t( 'Lingue parlate' ) ) . ':</span> ';
            foreach ( $languages as $lang ) {
                $html .= '<span class="olo-hostcard-lang-pill">' . esc_html( $lang ) . '</span>';
            }
            $html .= '</div>';
        }

        // Services
        $html .= $services_html;

        $html .= '</div>';

        return $html;
    }

    /**
     * Variant: button — renders a UIkit button that opens a modal.
     */
    private function render_button_variant( $s, $uid, $content_html, $accent, $card_bg, $radius ) {
        $btn_classes = [ 'uk-button' ];
        $btn_classes[] = 'uk-button-' . sanitize_html_class( $s['button_style'] ?: 'primary' );
        $btn_class = implode( ' ', $btn_classes );

        $icon_html = '';
        if ( ! empty( $s['button_icon'] ) && preg_match( '/^[a-z][a-z0-9-]*$/', $s['button_icon'] ) ) {
            $icon_html = '<span uk-icon="icon: ' . esc_attr( $s['button_icon'] ) . '"></span> ';
        }

        $btn_text = esc_html( olo_t( $s['button_text'] ?: 'Il tuo gestore' ) );

        $html  = '<div class="olo-hostcard olo-hostcard--button">';
        $html .= '<button class="' . esc_attr( $btn_class ) . '" type="button" uk-toggle="target: #' . esc_attr( $uid ) . '-modal">';
        $html .= $icon_html . $btn_text;
        $html .= '</button>';
        $html .= $this->render_modal( $uid, $content_html, $card_bg );
        $html .= '</div>';

        return $html;
    }

    /**
     * Variant: card — mini-card trigger that opens a modal.
     */
    private function render_card_variant( $uid, $name, $photo_url, $photo_size, $rounded, $role_label, $content_html, $accent, $card_bg, $radius, $shadow ) {
        $mini_size = min( $photo_size, 56 );
        $br = $rounded ? '50%' : '6px';

        $html  = '<div class="olo-hostcard olo-hostcard--card" style="background:' . esc_attr( $card_bg ) . ';border-radius:' . $radius . 'px;'
               . ( $shadow !== 'none' ? 'box-shadow:' . $shadow . ';' : '' ) . '">';
        $html .= '<a class="olo-hostcard-trigger" href="#" uk-toggle="target: #' . esc_attr( $uid ) . '-modal" style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:14px;padding:16px;">';

        if ( $photo_url ) {
            $html .= '<img src="' . esc_url( $photo_url ) . '" alt="' . esc_attr( $name ) . '" '
                   . 'style="width:' . $mini_size . 'px;height:' . $mini_size . 'px;border-radius:' . $br . ';object-fit:cover;flex-shrink:0;" loading="lazy" />';
        } else {
            $html .= '<div style="width:' . $mini_size . 'px;height:' . $mini_size . 'px;border-radius:' . $br . ';background:var(--olo-color-border, #E5E7EB);display:flex;align-items:center;justify-content:center;flex-shrink:0;">'
                   . '<span uk-icon="icon:user;ratio:1.2" style="color:var(--olo-color-text-muted, #9CA3AF)"></span></div>';
        }

        $html .= '<div style="flex:1;min-width:0;">';
        $html .= '<div style="font-weight:600;font-size:15px;color:var(--olo-color-text, #374151);">' . esc_html( $name ) . '</div>';
        if ( $role_label ) {
            $html .= '<div style="font-size:13px;color:var(--olo-color-text-muted, #9CA3AF);margin-top:2px;">' . esc_html( olo_t( $role_label ) ) . '</div>';
        }
        $html .= '</div>';
        $html .= '<span uk-icon="icon:chevron-right;ratio:0.9" style="color:var(--olo-color-text-muted, #9CA3AF);flex-shrink:0;"></span>';
        $html .= '</a>';
        $html .= $this->render_modal( $uid, $content_html, $card_bg );
        $html .= '</div>';

        return $html;
    }

    /**
     * Variant: inline — all info visible directly, no modal.
     */
    private function render_inline_variant( $uid, $content_html, $card_bg, $radius, $shadow ) {
        return '<div class="olo-hostcard olo-hostcard--inline" style="background:' . esc_attr( $card_bg ) . ';border-radius:' . $radius . 'px;padding:24px;'
             . ( $shadow !== 'none' ? 'box-shadow:' . $shadow . ';' : '' ) . '">'
             . $content_html
             . '</div>';
    }

    /**
     * Render the UIkit modal wrapper.
     */
    private function render_modal( $uid, $content_html, $card_bg ) {
        $html  = '<div id="' . esc_attr( $uid ) . '-modal" uk-modal>';
        $html .= '<div class="uk-modal-dialog" style="background:' . esc_attr( $card_bg ) . ';">';
        $html .= '<button class="uk-modal-close-default" type="button" uk-close></button>';
        $html .= '<div class="uk-modal-body olo-hostcard-modal" style="padding:32px;">';
        $html .= $content_html;
        $html .= '</div></div></div>';

        return $html;
    }
}
