<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Video_Tile extends Olobuild_Tile_Base {

    protected $type     = 'video';
    protected $name     = 'Video';
    protected $icon     = 'dashicons-video-alt3';
    protected $category = 'essential';
    protected $defaults = [
        'source_type'     => 'embed',
        'video_url'       => '',
        'file_url'        => '',
        'display_mode'    => '16:9',
        'cover_height'    => '500',
        'object_position' => 'center center',
        'facade'          => true,
        'autoplay'        => false,
        'muted'           => false,
        'loop'            => false,
        'controls'        => true,
        'start_time'      => '',
        'end_time'        => '',
        'poster_image'    => '',
        'privacy_mode'    => false,
        'show_play_icon'  => true,
        'play_icon_size'  => '80',
        'play_icon_color' => '#ffffff',
        'overlay_text'        => '',
        'overlay_color'       => '#000000',
        'overlay_opacity'     => '0',
        'overlay_text_size'   => '32',
        'overlay_text_color'  => '#ffffff',
        'overlay_text_weight' => '700',
        'overlay_text_align'  => 'center',
        'caption'             => '',
        'border_radius'       => 0,
        'shadow'              => 'none',
        // Legacy compat
        'aspect_ratio'            => '16:9',
        'cover_mode'              => false,
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        // Legacy compat: migrate old fields
        if ( empty( $s['display_mode'] ) || $s['display_mode'] === '16:9' ) {
            if ( ! empty( $s['cover_mode'] ) ) {
                $s['display_mode'] = 'cover';
            } elseif ( ! empty( $s['aspect_ratio'] ) && $s['aspect_ratio'] !== '16:9' ) {
                $s['display_mode'] = $s['aspect_ratio'];
            }
        }

        // Border-radius: priorità al `style.border_radius` (tab Stile globale, gestita
        // da frontend-renderer sul tile wrapper esterno) per garantire coerenza visiva
        // tra wrapper e tile interno. Fallback al `border_radius` flat (tab Contenuto)
        // per backward-compat con template esistenti.
        $br_value = $s['style']['border_radius'] ?? null;
        if ( $br_value === null || $br_value === '' || $br_value === 0 || ( is_array( $br_value ) && ! array_filter( array_map( 'intval', $br_value ) ) ) ) {
            $br_value = $s['border_radius'] ?? 0;
        }
        $_br_css = $this->build_border_radius_css( $br_value );
        $_br_css_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['style']['hover']['border_radius'] ?? $s['border_radius_hover'] ?? null );
        // `transform:translateZ(0)` crea un nuovo stacking context: in Chromium senza
        // questo, <iframe> e a volte <img> con position:absolute dentro un parent con
        // border-radius+overflow:hidden NON vengono clippati — il video resta squadrato
        // anche se il wrapper è arrotondato. Workaround standard cross-browser.
        $this->_vbr = $_br_css ? "border-radius:" . $_br_css . ";overflow:hidden;transform:translateZ(0);" : "";
        if ( $_br_css_hover_css !== '' ) {
            $this->_vbr .= 'transition:border-radius 400ms cubic-bezier(.4,0,.2,1);';
            // ensure overflow hidden + new stacking context so we can clip the video during the transition
            if ( ! $_br_css ) $this->_vbr .= 'overflow:hidden;transform:translateZ(0);';
        }
        // Stash hover css as instance prop so render_* funcs can emit a :hover rule
        $this->_vbr_hover_css = $_br_css_hover_css;
        // Per-instance uid so hover rules don't leak to other video tiles on the same page
        $this->_v_uid = 'olo-v-' . wp_unique_id();
        // Shadow on the video wrapper
        $shadow_val = Olobuild_Tile_Utils::shadow_value( $s, 'shadow' );
        if ( $shadow_val && $shadow_val !== 'none' ) {
            $this->_vbr .= 'box-shadow:' . $shadow_val . ';';
        }

        // Border system
        $v_uid = $this->_v_uid;
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$v_uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$v_uid}", $s['border'] ?? [], $s );

        // L'utente si aspetta che il valore del raggio coincida con la curvatura del VIDEO
        // (= curvatura interna del bordo). In CSS standard `border-radius: X` produce
        // curvatura esterna X e interna max(0, X - border_width). Per ottenere curvatura
        // interna = X dobbiamo applicare al wrapper esterno X + border_width.
        $border_data = $this->parse_border( $s['border'] ?? [] );
        $bw_uniform  = 0;
        if ( $border_data
            && $border_data['top'] === $border_data['right']
            && $border_data['right'] === $border_data['bottom']
            && $border_data['bottom'] === $border_data['left'] ) {
            $bw_uniform = max( 0, intval( $border_data['top'] ) );
        }

        // Outer radius = inner radius + border-width (così la curvatura interna coincide).
        $outer_br_value = $br_value;
        if ( $bw_uniform > 0 ) {
            if ( is_array( $br_value ) ) {
                $outer_br_value = [
                    'tl' => intval( $br_value['tl'] ?? 0 ) + $bw_uniform,
                    'tr' => intval( $br_value['tr'] ?? 0 ) + $bw_uniform,
                    'br' => intval( $br_value['br'] ?? 0 ) + $bw_uniform,
                    'bl' => intval( $br_value['bl'] ?? 0 ) + $bw_uniform,
                ];
            } elseif ( is_numeric( $br_value ) && intval( $br_value ) > 0 ) {
                $outer_br_value = intval( $br_value ) + $bw_uniform;
            }
        }
        $outer_radius_css = $this->build_border_radius_css( $outer_br_value );
        $border_block = '';
        if ( $border_css || $border_hover_css || $border_effect_css || $outer_radius_css ) {
            $border_block = '<style>';
            $outer_rules = $border_css;
            if ( $outer_radius_css ) {
                $outer_rules .= 'border-radius:' . $outer_radius_css . ';';
            }
            if ( $outer_rules ) $border_block .= ".{$v_uid}{{$outer_rules}}";
            $border_block .= $border_hover_css . $border_effect_css . '</style>';
        }

        $is_file = $s["source_type"] === 'file' || $this->is_direct_video( $s['video_url'] );
        $is_cover = $s['display_mode'] === 'cover';

        // Build hover CSS prefix scoped to this instance only
        $hover_prefix = '';
        if ( $_br_css_hover_css !== '' ) {
            // Cover applies radius to the outer wrapper (which also has .olo-video-cover);
            // embed/native apply to the inner <div> (first child of the wrapper).
            $u = $this->_v_uid;
            $hover_prefix = '<style>'
                          . '.' . $u . '.olo-video-cover:hover{border-radius:' . $_br_css_hover_css . ' !important}'
                          . '.' . $u . ':not(.olo-video-cover):hover>div{border-radius:' . $_br_css_hover_css . ' !important}'
                          . '</style>';
        }

        if ( $is_cover ) {
            $body = $this->render_cover( $s, $is_file );
        } elseif ( $is_file ) {
            $body = $this->render_native( $s );
        } else {
            $body = $this->render_embed( $s );
        }

        // Text-effects scoped CSS + runtime script (per-instance via $this->_v_uid)
        $tfx_css = $this->tfx_css( $s, '.' . $this->_v_uid );
        $tfx_block = $tfx_css ? '<style>' . $tfx_css . '</style>' : '';
        ob_start(); $this->tfx_print_script(); $tfx_block .= ob_get_clean();

        return $hover_prefix . $body . $tfx_block . $border_block;
    }

    // =========================================================================
    // Standard embed (YouTube / Vimeo iframe)
    // =========================================================================

    private function render_embed( $s ) {
        $embed_url    = $this->get_embed_url( $s );
        $padding      = $this->get_aspect_padding( $s['display_mode'] );
        $facade_on    = ! empty( $s['facade'] );
        $obj_pos      = $this->get_object_position( $s );

        // Video Facade: use poster_image if set, otherwise auto-detect YouTube/Vimeo thumbnail
        $poster_url = '';
        if ( $facade_on ) {
            if ( ! empty( $s['poster_image'] ) ) {
                $poster_url = $s['poster_image'];
            } else {
                $poster_url = $this->get_auto_thumbnail( $s['video_url'] ?? '' );
            }
        }
        $has_poster = $facade_on && ! empty( $poster_url ) && $embed_url;

        ob_start();
        ?>
        <div class="olo-video uk-responsive-width <?php echo esc_attr( $this->_v_uid ); ?>">
            <div style="position: relative; padding-bottom: <?php echo esc_attr( $padding ); ?>; overflow: hidden; <?php echo $this->_vbr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built internally from build_border_radius_css() integer radii, Olobuild_Tile_Utils::shadow_value() and fixed literals ?>">
                <?php if ( $has_poster ) : ?>
                    <?php
                    $icon_size  = absint( $s['play_icon_size'] ) ?: 80;
                    $icon_color = $this->safe_color_css( $s['play_icon_color'] ) ?: '#fff';
                    $show_icon  = $s['show_play_icon'] !== false;
                    $uid        = 'olo-vp-' . wp_unique_id();
                    ?>
                    <div id="<?php echo esc_attr( $uid ); ?>" role="button" tabindex="0" aria-label="<?php echo esc_attr__( 'Riproduci video', 'olobuild' ); ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; cursor: pointer;" onkeydown="if(event.key==='Enter'||event.key===' '||event.key==='Spacebar'){event.preventDefault();this.click();}" onclick="(function(el){var p=el.parentNode;el.remove();var f=document.createElement('iframe');f.src='<?php echo esc_url( $embed_url . ( str_contains( $embed_url, '?' ) ? '&' : '?' ) . 'autoplay=1' ); ?>';f.style='position:absolute;top:0;left:0;width:100%;height:100%';f.frameBorder='0';f.allow='accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture';f.allowFullscreen=true;p.appendChild(f)})(this)">
                        <img src="<?php echo esc_url( $poster_url ); ?>" alt="" style="width:100%;height:100%;object-fit:cover;object-position:<?php echo esc_attr( $obj_pos ); ?>;display:block;" loading="lazy" />
                        <?php if ( $show_icon ) : ?>
                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                            <svg width="<?php echo (int) $icon_size; ?>" height="<?php echo (int) $icon_size; ?>" viewBox="0 0 80 80">
                                <circle cx="40" cy="40" r="38" fill="rgba(0,0,0,0.5)" stroke-width="2" stroke="<?php echo esc_attr( $icon_color ); ?>"/>
                                <polygon points="32,24 32,56 58,40" fill="<?php echo esc_attr( $icon_color ); ?>"/>
                            </svg>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php elseif ( $embed_url ) : ?>
                    <iframe
                        src="<?php echo esc_url( $embed_url ); ?>"
                        title="<?php echo esc_attr__( 'Embedded video', 'olobuild' ); ?>"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
                <?php else : ?>
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #1F2937; display: flex; align-items: center; justify-content: center; color: var(--olo-color-text-muted, #9CA3AF);">
                        <?php echo esc_html__( 'Inserisci un URL video', 'olobuild' ); ?>
                    </div>
                <?php endif; ?>
                <?php echo $this->render_overlay_layers( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- overlay HTML built internally with esc_attr()/esc_html()/safe_color_css()/absint() ?>
            </div>
            <?php $this->render_caption( $s ); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    // =========================================================================
    // Native <video> (self-hosted file, aspect-ratio mode)
    // =========================================================================

    private function render_native( $s ) {
        $src     = $this->get_file_src( $s );
        $padding = $this->get_aspect_padding( $s['display_mode'] );
        $obj_pos = $this->get_object_position( $s );
        // Builder mode: niente autoplay (evita re-download a ogni patch del tile,
        // che con video grandi freezza visibilmente l'editing) e preload=metadata
        // (Chrome scarica solo i pochi KB iniziali, non l'intero file).
        $is_builder = ! empty( $s['_builder_mode'] );
        $autoplay = ! $is_builder && ! empty( $s['autoplay'] );
        $muted    = ! empty( $s['muted'] ) || $autoplay;
        $preload  = $is_builder ? 'metadata' : 'auto';

        ob_start();
        ?>
        <div class="olo-video uk-responsive-width <?php echo esc_attr( $this->_v_uid ); ?>">
            <div style="position: relative; padding-bottom: <?php echo esc_attr( $padding ); ?>; overflow: hidden; <?php echo $this->_vbr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built internally from build_border_radius_css() integer radii, Olobuild_Tile_Utils::shadow_value() and fixed literals ?> background: #1F2937;">
                <?php if ( $src ) : ?>
                    <video
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: <?php echo esc_attr( $obj_pos ); ?>;"
                        preload="<?php echo esc_attr( $preload ); ?>"
                        <?php echo $autoplay ? 'autoplay' : ''; ?>
                        <?php echo $muted ? 'muted' : ''; ?>
                        <?php echo ! empty( $s['loop'] ) ? 'loop' : ''; ?>
                        <?php echo ( $is_builder || ! empty( $s['controls'] ) ) ? 'controls' : ''; ?>
                        <?php echo ! empty( $s['poster_image'] ) ? 'poster="' . esc_url( $s['poster_image'] ) . '"' : ''; ?>
                        playsinline
                    >
                        <source src="<?php echo esc_url( $src ); ?>" type="<?php echo esc_attr( $this->get_video_mime( $src ) ); ?>">
                    </video>
                <?php else : ?>
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--olo-color-text-muted, #9CA3AF);">
                        <?php echo esc_html__( 'Seleziona un file video', 'olobuild' ); ?>
                    </div>
                <?php endif; ?>
                <?php echo $this->render_overlay_layers( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- overlay HTML built internally with esc_attr()/esc_html()/safe_color_css()/absint() ?>
            </div>
            <?php $this->render_caption( $s ); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    // =========================================================================
    // Cover mode (fixed height, object-fit: cover, optional overlay)
    // =========================================================================

    private function render_cover( $s, $is_file ) {
        $height  = absint( $s['cover_height'] ) ?: 500;
        $src     = $is_file ? $this->get_file_src( $s ) : '';
        $embed   = ! $is_file ? $this->get_embed_url( $s ) : '';
        $obj_pos = $this->get_object_position( $s );

        $ov_opacity  = absint( $s['overlay_opacity'] );
        $ov_color    = $this->safe_color_css( $s['overlay_color'] );
        $has_overlay = $ov_opacity > 0 && $ov_color;

        // Video Facade: auto-detect thumbnail for cover mode too (only when facade enabled)
        $facade_on   = ! empty( $s['facade'] );
        $poster_url  = '';
        if ( $facade_on ) {
            $poster_url = ! empty( $s['poster_image'] ) ? $s['poster_image'] : $this->get_auto_thumbnail( $s['video_url'] ?? '' );
        }
        $has_poster  = $facade_on && ! empty( $poster_url );
        $icon_size   = absint( $s['play_icon_size'] ) ?: 80;
        $icon_color  = $this->safe_color_css( $s['play_icon_color'] ) ?: '#fff';
        $show_icon   = $s['show_play_icon'] !== false;

        $is_builder = ! empty( $s['_builder_mode'] );
        $autoplay   = ! $is_builder && ! empty( $s['autoplay'] );
        $muted      = ! empty( $s['muted'] ) || $autoplay;
        $preload    = $is_builder ? 'metadata' : 'auto';

        ob_start();
        ?>
        <div class="olo-video olo-video-cover uk-position-relative uk-overflow-hidden <?php echo esc_attr( $this->_v_uid ); ?>" style="height: <?php echo (int) $height; ?>px; <?php echo $this->_vbr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built internally from build_border_radius_css() integer radii, Olobuild_Tile_Utils::shadow_value() and fixed literals ?>">
            <?php if ( $src ) : ?>
                <video
                    class="uk-position-cover"
                    style="object-fit: cover; object-position: <?php echo esc_attr( $obj_pos ); ?>; width: 100%; height: 100%;"
                    preload="<?php echo esc_attr( $preload ); ?>"
                    <?php echo $autoplay ? 'autoplay' : ''; ?>
                    <?php echo $muted ? 'muted' : ''; ?>
                    <?php echo ! empty( $s['loop'] ) ? 'loop' : ''; ?>
                    <?php echo ( $is_builder || ! empty( $s['controls'] ) ) ? 'controls' : ''; ?>
                    <?php echo $has_poster ? 'poster="' . esc_url( $poster_url ) . '"' : ''; ?>
                    playsinline
                >
                    <source src="<?php echo esc_url( $src ); ?>" type="<?php echo esc_attr( $this->get_video_mime( $src ) ); ?>">
                </video>
            <?php elseif ( $embed ) : ?>
                <?php if ( $has_poster ) : ?>
                    <?php $uid = 'olo-vp-' . wp_unique_id(); ?>
                    <div id="<?php echo esc_attr( $uid ); ?>" role="button" tabindex="0" aria-label="<?php echo esc_attr__( 'Riproduci video', 'olobuild' ); ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;cursor:pointer;z-index:3;" onkeydown="if(event.key==='Enter'||event.key===' '||event.key==='Spacebar'){event.preventDefault();this.click();}" onclick="(function(el){var p=el.parentNode;el.remove();var f=document.createElement('iframe');f.src='<?php echo esc_url( $embed . ( str_contains( $embed, '?' ) ? '&' : '?' ) . 'autoplay=1' ); ?>';f.style='position:absolute;top:50%;left:50%;width:200%;height:200%;transform:translate(-50%,-50%);pointer-events:none';f.frameBorder='0';f.allow='accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture';f.allowFullscreen=true;p.appendChild(f)})(this)">
                        <img src="<?php echo esc_url( $poster_url ); ?>" alt="" style="width:100%;height:100%;object-fit:cover;object-position:<?php echo esc_attr( $obj_pos ); ?>;display:block;" loading="lazy" />
                        <?php if ( $show_icon ) : ?>
                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                            <svg width="<?php echo (int) $icon_size; ?>" height="<?php echo (int) $icon_size; ?>" viewBox="0 0 80 80">
                                <circle cx="40" cy="40" r="38" fill="rgba(0,0,0,0.5)" stroke-width="2" stroke="<?php echo esc_attr( $icon_color ); ?>"/>
                                <polygon points="32,24 32,56 58,40" fill="<?php echo esc_attr( $icon_color ); ?>"/>
                            </svg>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                    <iframe
                        src="<?php echo esc_url( $embed ); ?>"
                        title="<?php echo esc_attr__( 'Background video', 'olobuild' ); ?>"
                        style="position: absolute; top: 50%; left: 50%; width: 200%; height: 200%; transform: translate(-50%, -50%); pointer-events: none;"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
                <?php endif; ?>
            <?php else : ?>
                <div style="width: 100%; height: 100%; background: #1F2937; display: flex; align-items: center; justify-content: center; color: var(--olo-color-text-muted, #9CA3AF);">
                    <?php echo esc_html__( 'Seleziona una sorgente video', 'olobuild' ); ?>
                </div>
            <?php endif; ?>

            <?php echo $this->render_overlay_layers( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- overlay HTML built internally with esc_attr()/esc_html()/safe_color_css()/absint() ?>
        </div>
        <?php $this->render_caption( $s ); ?>
        <?php
        return ob_get_clean();
    }

    // =========================================================================
    // Overlay layers (color + text) — funziona in TUTTI display_mode (embed/native/cover).
    // Estratto in v1.0.58 (fix UX: overlay_text era esposto in inspector ma renderizzato solo in cover).
    // =========================================================================
    private function render_overlay_layers( $s ) {
        $ov_opacity  = absint( $s['overlay_opacity'] ?? 0 );
        $ov_color    = $this->safe_color_css( $s['overlay_color'] ?? '' );
        $has_overlay = $ov_opacity > 0 && $ov_color;
        $has_text    = ! empty( $s['overlay_text'] );
        if ( ! $has_overlay && ! $has_text ) return '';

        ob_start();
        if ( $has_overlay ) {
            ?><div style="position:absolute;inset:0;background-color:<?php echo esc_attr( $ov_color ); ?>;opacity:<?php echo (float) ( $ov_opacity / 100 ); ?>;pointer-events:none;z-index:1;"></div><?php
        }
        if ( $has_text ) {
            $ov_size    = max( 8, absint( $s['overlay_text_size'] ?? 32 ) );
            $ov_t_color = $this->safe_color_css( $s['overlay_text_color'] ?? '#ffffff' ) ?: '#ffffff';
            $ov_weight  = in_array( (string) ( $s['overlay_text_weight'] ?? '700' ), [ '300','400','500','600','700','800','900' ], true ) ? $s['overlay_text_weight'] : '700';
            $ov_align   = in_array( $s['overlay_text_align'] ?? 'center', [ 'left', 'center', 'right' ], true ) ? $s['overlay_text_align'] : 'center';
            $ov_text_style = sprintf(
                'text-align:%s;color:%s;padding:24px;max-width:800px;pointer-events:auto;font-size:%dpx;font-weight:%s;line-height:1.25;',
                esc_attr( $ov_align ), $ov_t_color, $ov_size, esc_attr( $ov_weight )
            );
            list( $ov_tfx_cls, $ov_tfx_data ) = $this->tfx_attrs( $s, 'overlay_text', wp_strip_all_tags( $s['overlay_text'] ) );
            ?><div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;z-index:2;pointer-events:none;"><div class="olo-video-overlay-text<?php echo $ov_tfx_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); style string built above via sprintf from esc_attr()'d enums, safe_color_css() colour and %d size; text esc_html()'d (nl2br only adds <br /> tags) ?>" style="<?php echo $ov_text_style; ?>"<?php echo $ov_tfx_data; ?>><?php echo nl2br( esc_html( wp_strip_all_tags( $s['overlay_text'] ) ) ); ?></div></div><?php
        }
        return ob_get_clean();
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Auto-detect thumbnail URL for YouTube/Vimeo videos (Video Facade).
     * YouTube: predictable URL pattern. Vimeo: oEmbed API (cached).
     */
    private function get_auto_thumbnail( $url ) {
        if ( empty( $url ) ) return '';

        // YouTube — try maxresdefault first, fallback to hqdefault
        if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $m ) ) {
            $video_id  = $m[1];
            $cache_key = 'olo_yt_thumb_' . $video_id;
            $cached    = get_transient( $cache_key );
            if ( $cached ) return $cached;

            // Check if maxresdefault exists (returns 404 for some videos)
            $maxres = 'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg';
            $check  = wp_remote_head( $maxres, [ 'timeout' => 3 ] );
            if ( ! is_wp_error( $check ) ) {
                $code = wp_remote_retrieve_response_code( $check );
                if ( $code === 200 ) {
                    set_transient( $cache_key, $maxres, WEEK_IN_SECONDS );
                    return $maxres;
                }
            }

            // Fallback to hqdefault (always available)
            $hq = 'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg';
            set_transient( $cache_key, $hq, WEEK_IN_SECONDS );
            return $hq;
        }

        // Vimeo — use oEmbed with transient cache (1 week)
        if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
            $cache_key = 'olo_vimeo_thumb_' . $m[1];
            $cached    = get_transient( $cache_key );
            if ( $cached ) return $cached;

            $resp = wp_remote_get( 'https://vimeo.com/api/oembed.json?url=' . urlencode( $url ), [ 'timeout' => 5 ] );
            if ( ! is_wp_error( $resp ) ) {
                $data = json_decode( wp_remote_retrieve_body( $resp ), true );
                if ( ! empty( $data['thumbnail_url'] ) ) {
                    set_transient( $cache_key, $data['thumbnail_url'], WEEK_IN_SECONDS );
                    return $data['thumbnail_url'];
                }
            }
        }

        return '';
    }

    private function get_aspect_padding( $mode ) {
        $map = [ '16:9' => '56.25%', '4:3' => '75%', '1:1' => '100%' ];
        return $map[ $mode ] ?? '56.25%';
    }

    /**
     * Punto focale (object-position) salvato come stringa CSS dal field 'object-position'.
     * Default 'center center' = comportamento storico (nessuna regressione sui template esistenti).
     */
    private function get_object_position( $s ) {
        $obj_pos = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) {
            $obj_pos = 'center center';
        }
        return $obj_pos;
    }

    private function render_caption( $s ) {
        if ( ! empty( $s['caption'] ) ) {
            list( $tfx_caption_cls, $tfx_caption_data ) = $this->tfx_attrs( $s, 'caption', wp_strip_all_tags( $s['caption'] ) );
            // Wrap in a div with the per-instance class so scoped tfx CSS (`.olo-v-XXX …`) catches it
            // even when the caption sits OUTSIDE the cover wrapper.
            echo '<div class="' . esc_attr( $this->_v_uid ) . '">';
            echo '<p class="uk-text-center uk-text-small' . $tfx_caption_cls . '" style="padding: 8px 0; color: var(--olo-color-text-muted, #9CA3AF);"' . $tfx_caption_data . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); the rest is a fixed literal
            echo esc_html( wp_strip_all_tags( $s['caption'] ) );
            echo '</p>';
            echo '</div>';
        }
    }

    private function get_file_src( $s ) {
        if ( ! empty( $s['file_url'] ) ) {
            return $s['file_url'];
        }
        if ( $this->is_direct_video( $s['video_url'] ) ) {
            return $s['video_url'];
        }
        return '';
    }

    private function is_direct_video( $url ) {
        if ( empty( $url ) ) return false;
        return (bool) preg_match( '/\.(mp4|webm|ogg)(\?.*)?$/i', $url );
    }

    private function get_video_mime( $url ) {
        $ext = strtolower( pathinfo( wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
        $map = [ 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg' ];
        return $map[ $ext ] ?? 'video/mp4';
    }

    private function get_embed_url( $s ) {
        $url = $s['video_url'] ?? '';
        if ( empty( $url ) ) return '';

        $autoplay = ! empty( $s['autoplay'] );
        $muted    = ! empty( $s['muted'] ) || ! empty( $s['autoplay'] );
        $loop     = ! empty( $s['loop'] );
        $controls = isset( $s['controls'] ) ? (bool) $s['controls'] : true;
        $cover    = $s['display_mode'] === 'cover';

        // Start/end time
        $start = ! empty( $s['start_time'] ) ? absint( $s['start_time'] ) : 0;
        $end   = ! empty( $s['end_time'] )   ? absint( $s['end_time'] )   : 0;

        // YouTube
        if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches ) ) {
            $params = [];
            if ( $autoplay || $cover ) $params[] = 'autoplay=1';
            if ( $muted || $cover )    $params[] = 'mute=1';
            if ( $loop )               $params[] = 'loop=1&playlist=' . $matches[1];
            if ( ! $controls || $cover ) $params[] = 'controls=0';
            if ( $cover ) {
                $params[] = 'showinfo=0';
                $params[] = 'modestbranding=1';
                $params[] = 'rel=0';
            }
            if ( $start ) $params[] = 'start=' . $start;
            if ( $end )   $params[] = 'end=' . $end;
            $query  = ! empty( $params ) ? '?' . implode( '&', $params ) : '';
            $domain = ! empty( $s['privacy_mode'] ) ? 'www.youtube-nocookie.com' : 'www.youtube.com';
            return 'https://' . $domain . '/embed/' . $matches[1] . $query;
        }

        // Vimeo
        if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $matches ) ) {
            $params = [];
            if ( $autoplay || $cover ) $params[] = 'autoplay=1';
            if ( $muted || $cover )    $params[] = 'muted=1';
            if ( $loop )               $params[] = 'loop=1';
            if ( ! $controls || $cover ) $params[] = 'controls=0';
            if ( $cover )              $params[] = 'background=1';
            $query = ! empty( $params ) ? '?' . implode( '&', $params ) : '';
            return 'https://player.vimeo.com/video/' . $matches[1] . $query;
        }

        return '';
    }
}
