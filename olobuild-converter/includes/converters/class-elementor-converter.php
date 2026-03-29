<?php
/**
 * Elementor Pro → OloBuild converter.
 *
 * Converts Elementor sections/columns/widgets to OloBuild's
 * Section > Row > Column > Element hierarchy.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Elementor_Converter extends Olo_Converter_Interface {

    public function get_source_name() {
        return 'Elementor Pro';
    }

    public function get_source_slug() {
        return 'elementor';
    }

    public function is_source_installed() {
        return Olo_Elementor_Db_Reader::is_installed();
    }

    public function get_available_pages() {
        return Olo_Elementor_Db_Reader::get_available_pages();
    }

    protected function read_from_db( $post_id ) {
        return Olo_Elementor_Db_Reader::read( $post_id );
    }

    protected function parse_file( $file_data ) {
        $parsed = Olo_Elementor_File_Parser::parse( $file_data );
        return $parsed['content'];
    }

    public function convert_from_file( $file_data ) {
        $parsed = Olo_Elementor_File_Parser::parse( $file_data );
        return $this->convert( $parsed['content'], $parsed['title'] );
    }

    // ─── Source data parsing ───

    protected function parse_source_data( $raw ) {
        // Elementor data is already an array tree; return as-is.
        return is_array( $raw ) ? $raw : [];
    }

    // ─── Node conversion (recursive) ───

    protected function convert_node( $node, Olo_Conversion_Report $report ) {
        $el_type = $node['elType'] ?? '';

        switch ( $el_type ) {
            case 'section':
                return $this->convert_section( $node, $report );
            case 'container':
                return $this->convert_section( $node, $report );
            case 'column':
                return $this->convert_column( $node, $report );
            case 'widget':
                return $this->convert_widget( $node, $report );
            default:
                $report->add_skipped( $el_type ?: 'unknown', "Tipo nodo Elementor non riconosciuto: '{$el_type}'" );
                return null;
        }
    }

    // ─── Structural converters ───

    private function convert_section( $node, Olo_Conversion_Report $report ) {
        $s = $node['settings'] ?? [];
        $children = $this->convert_tree( $node['elements'] ?? [], $report );

        $settings = [
            'style' => 'default',
            'width' => 'default',
        ];

        // Section padding.
        if ( ! empty( $s['padding'] ) ) {
            $pad = $s['padding'];
            if ( ! empty( $pad['top'] ) || ! empty( $pad['bottom'] ) ) {
                $top = (int) ( $pad['top'] ?? 0 );
                $bot = (int) ( $pad['bottom'] ?? 0 );
                $avg = ( $top + $bot ) / 2;
                if ( $avg <= 20 ) $settings['padding'] = 'small';
                elseif ( $avg <= 50 ) $settings['padding'] = 'default';
                elseif ( $avg <= 80 ) $settings['padding'] = 'large';
                else $settings['padding'] = 'xlarge';
            }
        }

        // Full-width / stretch.
        $stretch = $s['stretch_section'] ?? '';
        if ( $stretch === 'section-stretched' || ( $s['layout'] ?? '' ) === 'full_width' ) {
            $settings['width'] = 'expand';
        }

        $section = $this->build_node( 'section', $settings, [] );

        // Elementor section children are columns.
        // OloBuild: section -> row -> columns.
        if ( ! empty( $children ) ) {
            // Detect layout from column widths.
            $percentages = [];
            foreach ( $node['elements'] ?? [] as $col ) {
                $pct = (float) ( $col['settings']['_column_size'] ?? 100 );
                $percentages[] = $pct;
            }
            $layout = $this->structure->detect_row_layout( $percentages );

            $row = $this->build_node( 'row', [
                'layout'       => $layout,
                'gap'          => '16',
                'stack_mobile' => true,
            ], $children );

            $section['children'] = [ $row ];
        }

        $report->add_converted( 'section', 'section' );
        return $section;
    }

    private function convert_column( $node, Olo_Conversion_Report $report ) {
        $s = $node['settings'] ?? [];
        $children = $this->convert_tree( $node['elements'] ?? [], $report );

        $col_size = (float) ( $s['_column_size'] ?? 100 );
        $fraction = $this->structure->percentage_to_fraction( $col_size );

        $settings = [
            'width_default' => $fraction,
        ];

        $report->add_converted( 'column', 'column', "Larghezza: {$col_size}% → {$fraction}" );
        return $this->build_node( 'column', $settings, $children );
    }

    // ─── Widget converter dispatcher ───

    private function convert_widget( $node, Olo_Conversion_Report $report ) {
        $widget_type = $node['widgetType'] ?? '';
        $s           = $node['settings'] ?? [];
        $mapping     = Olo_Elementor_Widget_Map::get( $widget_type );

        if ( ! $mapping ) {
            return $this->build_fallback_html( $widget_type, $s, $report );
        }

        $method = $mapping['method'];
        if ( method_exists( $this, $method ) ) {
            return $this->$method( $s, $mapping['olo_type'], $widget_type, $report );
        }

        return $this->build_fallback_html( $widget_type, $s, $report );
    }

    // ─── Widget converters ───

    private function convert_heading( $s, $olo_type, $src_type, $report ) {
        $tag = $this->style_mapper->map_heading_tag( $s['header_size'] ?? 'h2' );

        $settings = [
            'heading'        => $s['title'] ?? 'Titolo',
            'subtitle'       => '',
            'tag'            => $tag,
            'alignment'      => $this->style_mapper->map_alignment( $s['align'] ?? 'center' ),
            'decoration'     => 'none',
            'heading_color'  => $this->style_mapper->map_color( $s['title_color'] ?? '' ) ?: '#F3F4F6',
            'subtitle_color' => '#9CA3AF',
            'heading_size'   => 'lg',
            'shadow'         => 'none',
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_text_editor( $s, $olo_type, $src_type, $report ) {
        $settings = [
            'heading' => '',
            'text'    => $s['editor'] ?? '',
        ];

        $text_color = $this->style_mapper->map_color( $s['text_color'] ?? '' );
        if ( $text_color ) {
            $settings['text_color'] = $text_color;
        }

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_button( $s, $olo_type, $src_type, $report ) {
        $settings = [
            'text'           => $s['text'] ?? 'Click',
            'url'            => $s['link']['url'] ?? '#',
            'target'         => ( $s['link']['is_external'] ?? '' ) === 'on' ? '_blank' : '_self',
            'alignment'      => $this->style_mapper->map_alignment( $s['align'] ?? 'center' ),
            'full_width'     => false,
            'bg_color'       => $this->style_mapper->map_color( $s['button_background_color'] ?? $s['background_color'] ?? '' ) ?: '#6366F1',
            'text_color'     => $this->style_mapper->map_color( $s['button_text_color'] ?? '' ) ?: '#FFFFFF',
            'border_radius'  => '6',
            'padding_x'      => '32',
            'padding_y'      => '14',
            'font_size'      => '16',
            'font_weight'    => '600',
            'letter_spacing' => '0',
            'text_transform' => 'none',
            'border_width'   => '0',
            'border_color'   => '#6366F1',
            'shadow'         => 'none',
            'hover_effect'   => 'lift',
        ];

        // Elementor typography settings.
        if ( ! empty( $s['typography_font_size']['size'] ) ) {
            $settings['font_size'] = $this->style_mapper->map_spacing( $s['typography_font_size'] );
        }
        if ( ! empty( $s['typography_font_weight'] ) ) {
            $settings['font_weight'] = $this->style_mapper->map_font_weight( $s['typography_font_weight'] );
        }
        if ( ! empty( $s['typography_text_transform'] ) ) {
            $settings['text_transform'] = $this->style_mapper->map_text_transform( $s['typography_text_transform'] );
        }

        // Border radius.
        if ( ! empty( $s['border_radius'] ) ) {
            $settings['border_radius'] = $this->style_mapper->map_border_radius( $s['border_radius'] );
        }
        // Border.
        if ( ! empty( $s['border_border'] ) ) {
            $settings['border_width'] = $this->style_mapper->map_spacing( $s['border_width']['top'] ?? '0' );
            $settings['border_color'] = $this->style_mapper->map_color( $s['border_color'] ?? '' ) ?: '#6366F1';
        }

        // Hover colors.
        if ( ! empty( $s['button_background_hover_color'] ?? $s['hover_color'] ?? '' ) ) {
            $settings['hover_bg_color'] = $this->style_mapper->map_color( $s['button_background_hover_color'] ?? $s['hover_color'] ?? '' );
        }
        if ( ! empty( $s['button_hover_color'] ?? '' ) ) {
            $settings['hover_text_color'] = $this->style_mapper->map_color( $s['button_hover_color'] );
        }

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_image( $s, $olo_type, $src_type, $report ) {
        $image_url = $s['image']['url'] ?? '';

        $settings = [
            'image_url' => $image_url,
            'alt_text'  => $s['image']['alt'] ?? '',
            'caption'   => $s['caption'] ?? '',
            'shadow'    => 'none',
        ];

        if ( ! empty( $s['link']['url'] ) ) {
            $settings['link_url']    = $s['link']['url'];
            $settings['link_target'] = ( $s['link']['is_external'] ?? '' ) === 'on' ? '_blank' : '_self';
        }

        if ( ! empty( $s['width']['size'] ) ) {
            $settings['width'] = $s['width']['size'] . ( $s['width']['unit'] ?? 'px' );
        }
        if ( ! empty( $s['height']['size'] ) ) {
            $settings['height'] = $s['height']['size'] . ( $s['height']['unit'] ?? 'px' );
        }

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_video( $s, $olo_type, $src_type, $report ) {
        $video_type = $s['video_type'] ?? 'youtube';
        $video_url  = '';

        switch ( $video_type ) {
            case 'youtube':
                $video_url = $s['youtube_url'] ?? '';
                break;
            case 'vimeo':
                $video_url = $s['vimeo_url'] ?? '';
                break;
            case 'hosted':
            case 'self_hosted':
                $video_url = $s['hosted_url']['url'] ?? $s['insert_url'] ?? '';
                break;
        }

        $settings = [
            'source_type' => ( $video_type === 'hosted' || $video_type === 'self_hosted' ) ? 'file' : 'embed',
            'video_url'   => $video_url,
            'autoplay'    => ( $s['autoplay'] ?? '' ) === 'yes',
            'muted'       => ( $s['mute'] ?? '' ) === 'yes',
            'loop'        => ( $s['loop'] ?? '' ) === 'yes',
            'controls'    => ( $s['controls'] ?? 'yes' ) !== 'no',
            'shadow'      => 'none',
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_icon_box( $s, $olo_type, $src_type, $report ) {
        $lost = [];

        // Elementor uses FontAwesome icons; OloBuild uses emoji.
        $icon = $s['selected_icon']['value'] ?? $s['icon']['value'] ?? '';
        $emoji = $this->fa_to_emoji( $icon );
        if ( $icon && ! $emoji ) {
            $lost[] = "Icona FontAwesome '{$icon}' approssimata";
            $emoji = '📦';
        }

        $settings = [
            'icon_emoji'  => $emoji ?: '📦',
            'title'       => $s['title_text'] ?? 'Titolo',
            'description' => $s['description_text'] ?? '',
            'alignment'   => $this->style_mapper->map_alignment( $s['position'] ?? 'center' ),
            'text_color'  => $this->style_mapper->map_color( $s['title_color'] ?? '' ) ?: '#F3F4F6',
            'shadow'      => 'none',
        ];

        if ( ! empty( $s['link']['url'] ) ) {
            $settings['link_url']  = $s['link']['url'];
            $settings['link_text'] = $s['link_text'] ?? 'Scopri di più';
        }

        if ( ! empty( $lost ) ) {
            $report->add_approximated( $src_type, $olo_type, implode( '; ', $lost ) );
        } else {
            $report->add_converted( $src_type, $olo_type );
        }
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_image_box( $s, $olo_type, $src_type, $report ) {
        $settings = [
            'heading'        => $s['title_text'] ?? '',
            'text'           => $s['description_text'] ?? '',
            'image'          => $s['image']['url'] ?? '',
            'image_position' => 'top',
        ];

        if ( ! empty( $s['link']['url'] ) ) {
            $settings['link_url']    = $s['link']['url'];
            $settings['link_target'] = ( $s['link']['is_external'] ?? '' ) === 'on' ? '_blank' : '_self';
        }

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_gallery( $s, $olo_type, $src_type, $report ) {
        $images = [];
        $gallery = $s['gallery'] ?? $s['wp_gallery'] ?? [];

        foreach ( $gallery as $img ) {
            $images[] = [
                'url'     => $img['url'] ?? '',
                'alt'     => $img['alt'] ?? '',
                'caption' => $img['caption'] ?? '',
            ];
        }

        $settings = [
            'images'    => $images,
            'columns'   => (string) ( $s['gallery_columns'] ?? $s['columns'] ?? 3 ),
            'gap'       => '8',
            'shadow'    => 'none',
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_map( $s, $olo_type, $src_type, $report ) {
        $address = $s['address'] ?? 'Roma, Italia';
        $zoom    = (string) ( $s['zoom']['size'] ?? $s['zoom'] ?? 13 );

        $settings = [
            'mode'    => 'single',
            'address' => $address,
            'zoom'    => $zoom,
            'height'  => (string) ( $s['height']['size'] ?? 400 ),
            'shadow'  => 'none',
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_spacer( $s, $olo_type, $src_type, $report ) {
        $height = '60';
        if ( ! empty( $s['space']['size'] ) ) {
            $height = $this->style_mapper->map_spacing( $s['space'] );
        }

        $settings = [
            'height'       => $height,
            'show_divider' => false,
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_divider( $s, $olo_type, $src_type, $report ) {
        $settings = [
            'height'           => '30',
            'show_divider'     => true,
            'divider_style'    => $this->map_divider_style( $s['style'] ?? 'solid' ),
            'divider_color'    => $this->style_mapper->map_color( $s['color'] ?? '' ) ?: '#374151',
            'divider_width'    => (string) ( $s['width']['size'] ?? 100 ),
            'divider_thickness' => (string) ( $s['weight']['size'] ?? 1 ),
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_accordion( $s, $olo_type, $src_type, $report ) {
        $panels = [];
        foreach ( $s['tabs'] ?? [] as $i => $tab ) {
            $panels[] = [
                'id'      => 'p-' . ( $i + 1 ),
                'title'   => $tab['tab_title'] ?? 'Pannello ' . ( $i + 1 ),
                'content' => $tab['tab_content'] ?? '',
                'image'   => '',
                'video'   => '',
                'icon'    => '',
            ];
        }

        $settings = [
            'panels'       => $panels,
            'toggle_mode'  => false,
            'default_open' => 'first',
            'faq_schema'   => ( $s['faq_schema'] ?? '' ) === 'yes',
            'shadow'       => 'none',
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_toggle( $s, $olo_type, $src_type, $report ) {
        $node = $this->convert_accordion( $s, $olo_type, 'toggle', $report );
        $node['settings']->toggle_mode = true;
        // Update report: last item was accordion → fix to toggle.
        return $node;
    }

    private function convert_tabs( $s, $olo_type, $src_type, $report ) {
        $items = [];
        foreach ( $s['tabs'] ?? [] as $i => $tab ) {
            $items[] = [
                'id'      => 'sw-' . ( $i + 1 ),
                'title'   => $tab['tab_title'] ?? 'Tab ' . ( $i + 1 ),
                'content' => $tab['tab_content'] ?? '',
            ];
        }

        $settings = [
            'items'     => $items,
            'nav_style' => 'tab',
            'shadow'    => 'none',
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_testimonial( $s, $olo_type, $src_type, $report ) {
        $settings = [
            'quote'       => $s['testimonial_content'] ?? '',
            'author_name' => $s['testimonial_name'] ?? '',
            'author_role' => $s['testimonial_job'] ?? '',
            'avatar'      => $s['testimonial_image']['url'] ?? '',
            'rating'      => '5',
            'text_color'  => $this->style_mapper->map_color( $s['content_content_color'] ?? '' ) ?: '#F3F4F6',
            'shadow'      => 'none',
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_pricing( $s, $olo_type, $src_type, $report ) {
        // Build features string from Elementor's repeater.
        $features_lines = [];
        foreach ( $s['features_list'] ?? [] as $feat ) {
            $features_lines[] = $feat['item_text'] ?? '';
        }

        $settings = [
            'plan_name'  => $s['heading'] ?? 'Piano',
            'price'      => $s['price'] ?? '0',
            'currency'   => $s['currency_symbol'] ?? '€',
            'period'     => $s['period'] ?? '',
            'features'   => implode( "\n", $features_lines ),
            'is_popular' => ( $s['show_ribbon'] ?? '' ) === 'yes',
            'badge_text' => $s['ribbon_title'] ?? 'Popolare',
            'cta_text'   => $s['button_text'] ?? 'Acquista',
            'cta_url'    => $s['button_url']['url'] ?? '#',
            'shadow'     => 'none',
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_counter( $s, $olo_type, $src_type, $report ) {
        $settings = [
            'number'     => (string) ( $s['ending_number'] ?? 100 ),
            'label'      => $s['title'] ?? '',
            'prefix'     => $s['prefix'] ?? '',
            'suffix'     => $s['suffix'] ?? '',
            'icon_emoji' => '🏆',
            'shadow'     => 'none',
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_progress( $s, $olo_type, $src_type, $report ) {
        $title   = $s['title'] ?? 'Progresso';
        $percent = $s['percent']['size'] ?? $s['percent'] ?? 50;

        $settings = [
            'bars'            => "{$title}|{$percent}",
            'bar_color'       => $this->style_mapper->map_color( $s['bar_color'] ?? '' ) ?: '#6366F1',
            'show_percentage' => true,
            'shadow'          => 'none',
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_alert( $s, $olo_type, $src_type, $report ) {
        $type_map = [
            'info'    => 'info',
            'success' => 'success',
            'warning' => 'warning',
            'danger'  => 'danger',
        ];

        $settings = [
            'alert_type' => $type_map[ $s['alert_type'] ?? 'info' ] ?? 'info',
            'title'      => $s['alert_title'] ?? '',
            'message'    => $s['alert_description'] ?? '',
            'show_icon'  => true,
            'shadow'     => 'none',
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_html( $s, $olo_type, $src_type, $report ) {
        $settings = [
            'html_content' => $s['html'] ?? '',
            'shadow'       => 'none',
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_cta( $s, $olo_type, $src_type, $report ) {
        $settings = [
            'title'         => $s['title'] ?? '',
            'subtitle'      => $s['description'] ?? '',
            'text_color'    => $this->style_mapper->map_color( $s['title_color'] ?? '' ) ?: '#FFFFFF',
            'min_height'    => $this->style_mapper->map_spacing( $s['min_height'] ?? '400' ) . 'px',
            'text_align'    => 'center',
            'bg_type'       => 'color',
            'bg_color'      => $this->style_mapper->map_color( $s['background_color'] ?? '' ) ?: '#6366F1',
            'overlay'       => false,
            'cta_text'      => $s['button'] ?? 'Scopri di più',
            'cta_url'       => $s['link']['url'] ?? '#',
            'shadow'        => 'none',
        ];

        // Background image.
        if ( ! empty( $s['bg_image']['url'] ) ) {
            $settings['bg_type']  = 'image';
            $settings['bg_image'] = $s['bg_image']['url'];
            $settings['overlay']  = true;
            $settings['overlay_color']   = '#000000';
            $settings['overlay_opacity'] = '50';
        }

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_icon_list( $s, $olo_type, $src_type, $report ) {
        // OloBuild "list" element — convert to pipe-delimited or content fallback.
        $items = [];
        foreach ( $s['icon_list'] ?? [] as $item ) {
            $items[] = $item['text'] ?? '';
        }

        $settings = [
            'text' => implode( "\n", $items ),
        ];

        $report->add_approximated( $src_type, $olo_type, 'Icone della lista non convertite' );
        return $this->build_node( 'content', $settings );
    }

    private function convert_social( $s, $olo_type, $src_type, $report ) {
        $links = [];
        foreach ( $s['icon_list'] ?? $s['social_icon_list'] ?? [] as $item ) {
            $network = $item['social_icon']['value'] ?? $item['social'] ?? '';
            // Extract network name from FA class: "fab fa-facebook" → "facebook"
            $network = preg_replace( '/^(fab?|fas)\s+fa-/', '', $network );
            $url     = $item['link']['url'] ?? '#';
            if ( $network ) {
                $links[] = "{$network}|{$url}";
            }
        }

        $settings = [
            'links'     => implode( "\n", $links ),
            'alignment' => 'center',
            'shadow'    => 'none',
        ];

        $report->add_converted( $src_type, $olo_type );
        return $this->build_node( $olo_type, $settings );
    }

    private function convert_form( $s, $olo_type, $src_type, $report ) {
        $lost   = [];
        $fields = [];

        foreach ( $s['form_fields'] ?? [] as $i => $field ) {
            $field_type = $this->map_form_field_type( $field['field_type'] ?? 'text' );

            $olo_field = [
                'id'          => 'f-' . ( $i + 1 ),
                'field_type'  => $field_type,
                'label'       => $field['field_label'] ?? '',
                'placeholder' => $field['placeholder'] ?? '',
                'name'        => $field['custom_id'] ?? 'field_' . ( $i + 1 ),
                'required'    => ( $field['required'] ?? '' ) === 'yes',
                'width'       => $this->map_form_field_width( $field['width'] ?? '100' ),
            ];

            // Options for select/radio/checkbox.
            if ( ! empty( $field['field_options'] ) ) {
                $olo_field['options'] = $field['field_options'];
            }

            $fields[] = $olo_field;
        }

        $settings = [
            'fields'          => $fields,
            'email_to'        => $s['email_to'] ?? '',
            'email_subject'   => $s['email_subject'] ?? 'Nuovo messaggio',
            'success_message' => $s['success_message'] ?? 'Messaggio inviato!',
            'submit_text'     => $s['button_text'] ?? 'Invia',
            'honeypot'        => true,
        ];

        if ( ! empty( $s['email_to_2'] ?? '' ) ) {
            $lost[] = 'Email CC secondaria non mappata';
        }

        if ( ! empty( $lost ) ) {
            $report->add_approximated( $src_type, $olo_type, implode( '; ', $lost ) );
        } else {
            $report->add_converted( $src_type, $olo_type );
        }
        return $this->build_node( $olo_type, $settings );
    }

    // ─── Private helpers ───

    /**
     * Map FontAwesome icon class to emoji (best effort).
     */
    private function fa_to_emoji( $fa_class ) {
        $map = [
            'fa-star'        => '⭐', 'fa-heart'       => '❤️',
            'fa-check'       => '✅', 'fa-times'       => '❌',
            'fa-home'        => '🏠', 'fa-user'        => '👤',
            'fa-envelope'    => '✉️', 'fa-phone'       => '📞',
            'fa-map-marker'  => '📍', 'fa-clock'       => '🕐',
            'fa-calendar'    => '📅', 'fa-camera'      => '📷',
            'fa-video'       => '🎥', 'fa-music'       => '🎵',
            'fa-search'      => '🔍', 'fa-cog'         => '⚙️',
            'fa-lock'        => '🔒', 'fa-globe'       => '🌍',
            'fa-rocket'      => '🚀', 'fa-bolt'        => '⚡',
            'fa-shield'      => '🛡️', 'fa-trophy'      => '🏆',
            'fa-chart-line'  => '📈', 'fa-chart-bar'   => '📊',
            'fa-code'        => '💻', 'fa-paint-brush' => '🎨',
            'fa-wrench'      => '🔧', 'fa-truck'       => '🚛',
            'fa-dollar-sign' => '💲', 'fa-euro-sign'   => '💶',
            'fa-thumbs-up'   => '👍', 'fa-thumbs-down' => '👎',
            'fa-smile'       => '😊', 'fa-fire'        => '🔥',
            'fa-lightbulb'   => '💡', 'fa-book'        => '📖',
            'fa-graduation-cap' => '🎓', 'fa-coffee'   => '☕',
            'fa-gem'         => '💎', 'fa-leaf'        => '🍃',
            'fa-sun'         => '☀️', 'fa-moon'        => '🌙',
            'fa-cloud'       => '☁️', 'fa-wifi'        => '📶',
            'fa-database'    => '🗄️', 'fa-server'      => '🖥️',
        ];

        // Try exact match first.
        $class = strtolower( trim( $fa_class ) );
        if ( isset( $map[ $class ] ) ) {
            return $map[ $class ];
        }

        // Try extracting icon name from full class.
        foreach ( $map as $fa => $emoji ) {
            if ( strpos( $class, $fa ) !== false ) {
                return $emoji;
            }
        }

        return null;
    }

    private function map_divider_style( $style ) {
        $map = [
            'solid'  => 'solid',
            'double' => 'solid',
            'dotted' => 'dotted',
            'dashed' => 'dashed',
        ];
        return $map[ $style ] ?? 'solid';
    }

    private function map_form_field_type( $type ) {
        $map = [
            'text'     => 'text',
            'email'    => 'email',
            'textarea' => 'textarea',
            'tel'      => 'text',
            'number'   => 'text',
            'url'      => 'text',
            'select'   => 'select',
            'radio'    => 'radio',
            'checkbox' => 'checkbox',
            'date'     => 'text',
            'time'     => 'text',
            'upload'   => 'text',
            'password' => 'text',
            'html'     => 'text',
            'hidden'   => 'hidden',
            'acceptance' => 'checkbox',
        ];
        return $map[ $type ] ?? 'text';
    }

    private function map_form_field_width( $width ) {
        $map = [
            '100' => '1-1',
            '80'  => '4-5',
            '75'  => '3-4',
            '66'  => '2-3',
            '60'  => '3-5',
            '50'  => '1-2',
            '40'  => '2-5',
            '33'  => '1-3',
            '25'  => '1-4',
            '20'  => '1-5',
        ];
        return $map[ $width ] ?? '1-1';
    }
}
