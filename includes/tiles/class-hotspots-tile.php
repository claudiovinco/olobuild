<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hotspots — pannello con marker posizionati (x%,y%): click → scheda info.
 * Estratto dai demo OLOthemes (data-hotspot). Image-free (pannello astratto).
 * Render == Vue (HotspotsTile.vue). Runtime inline scoped, senza '&&' né '<'/'>'.
 */
class Olobuild_Hotspots_Tile extends Olobuild_Tile_Base {

    protected $type     = 'hotspots';
    protected $name     = 'Hotspots';
    protected $icon     = 'dashicons-location';
    protected $category = 'interactive';
    protected $defaults = [
        'eyebrow'      => '',
        'heading'      => 'Esplora',
        'intro'        => '',
        'panel_label'  => 'SCENE',
        'aspect_ratio' => '16/10',
        'items'        => [
            [ 'x' => 28, 'y' => 36, 'title' => 'Punto 1', 'text' => 'Descrizione.', 'meta' => '' ],
            [ 'x' => 62, 'y' => 58, 'title' => 'Punto 2', 'text' => 'Descrizione.', 'meta' => '' ],
            [ 'x' => 44, 'y' => 74, 'title' => 'Punto 3', 'text' => 'Descrizione.', 'meta' => '' ],
        ],
        'zone_accent'  => '',
        'zone_on'      => '#ffffff',
        'panel_bg'     => '',
        'card_bg'      => '',
        'card_border'  => '',
        'align'        => 'left',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'osp-' . wp_rand( 10000, 99999 );

        $accent  = $this->safe_color_css( $s['zone_accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $on      = $this->safe_color_css( $s['zone_on'] ?? '' ) ?: '#ffffff';
        $panelbg = $this->safe_color_css( $s['panel_bg'] ?? '' ) ?: 'var(--olo-color-surface-alt, #f1f1f1)';
        $cardbg  = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'var(--olo-color-surface, #ffffff)';
        $line    = $this->safe_color_css( $s['card_border'] ?? '' ) ?: 'var(--olo-color-border, #e5e7eb)';
        $center  = ( ( $s['align'] ?? 'left' ) === 'center' );
        $serif   = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
        $sans    = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
        $ar      = in_array( $s['aspect_ratio'], [ '16/10', '16/9', '4/3', '3/2', '1/1' ], true ) ? $s['aspect_ratio'] : '16/10';

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) return '';

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every colour via the safe_color_css() whitelist, $ar via in_array() whitelist, font stacks and the $center branch are fixed literals; $uid is internally generated.
        ?>
        <style>
            .<?php echo $uid; ?>{ font-family:<?php echo $sans; ?>; <?php if ( $center ) echo 'text-align:center;'; ?> }
            .<?php echo $uid; ?> .osp-eyebrow{font-size:12px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:<?php echo $accent; ?>;display:block;margin-bottom:10px;}
            .<?php echo $uid; ?> .osp-h{font-family:<?php echo $serif; ?>;font-size:clamp(26px,3.6vw,42px);line-height:1.12;margin:0;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .osp-intro{font-size:15.5px;line-height:1.6;opacity:.8;margin:14px 0 0;max-width:560px;<?php echo $center ? 'margin-left:auto;margin-right:auto;' : ''; ?>}
            .<?php echo $uid; ?> .osp-panel{position:relative;margin-top:24px;aspect-ratio:<?php echo esc_attr( $ar ); ?>;border-radius:16px;overflow:hidden;border:1px solid <?php echo $line; ?>;background:<?php echo $panelbg; ?>;background-image:repeating-linear-gradient(135deg, rgba(127,127,127,.05) 0 16px, transparent 16px 32px);}
            .<?php echo $uid; ?> .osp-panel__label{position:absolute;left:16px;bottom:14px;font-size:10.5px;font-weight:600;letter-spacing:.05em;text-transform:uppercase;opacity:.45;}
            .<?php echo $uid; ?> .osp-mk{position:absolute;transform:translate(-50%,-50%);width:26px;height:26px;border-radius:50%;border:0;cursor:pointer;background:<?php echo $accent; ?>;box-shadow:0 0 0 0 <?php echo $accent; ?>;padding:0;}
            .<?php echo $uid; ?> .osp-mk::after{content:'';position:absolute;inset:7px;border-radius:50%;background:<?php echo $on; ?>;}
            .<?php echo $uid; ?> .osp-mk{animation:ospping 2s infinite;}
            @keyframes ospping{0%{box-shadow:0 0 0 0 color-mix(in srgb, <?php echo $accent; ?> 55%, transparent)}70%{box-shadow:0 0 0 12px transparent}100%{box-shadow:0 0 0 0 transparent}}
            .<?php echo $uid; ?> .osp-mk:focus-visible{outline:2px solid <?php echo $on; ?>;outline-offset:2px;}
            .<?php echo $uid; ?> .osp-tip{position:absolute;bottom:130%;left:50%;transform:translateX(-50%);width:220px;background:<?php echo $cardbg; ?>;border:1px solid <?php echo $line; ?>;border-radius:12px;padding:14px 16px;text-align:left;box-shadow:0 12px 30px -10px rgba(16,24,40,.3);display:none;z-index:5;}
            .<?php echo $uid; ?> .osp-mk.on .osp-tip{display:block;}
            .<?php echo $uid; ?> .osp-tip__meta{font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .osp-tip__t{font-family:<?php echo $serif; ?>;font-size:16px;margin:4px 0 0;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .osp-tip__x{font-size:13px;line-height:1.5;opacity:.75;margin:6px 0 0;}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-hotspots <?php echo esc_attr( $uid ); ?>" data-hotspots>
            <?php if ( $s['eyebrow'] !== '' ) : ?><span class="osp-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
            <?php if ( $s['heading'] !== '' ) : ?><h2 class="osp-h"><?php echo esc_html( $s['heading'] ); ?></h2><?php endif; ?>
            <?php if ( $s['intro'] !== '' ) : ?><p class="osp-intro"><?php echo esc_html( $s['intro'] ); ?></p><?php endif; ?>
            <div class="osp-panel">
                <span class="osp-panel__label"><?php echo esc_html( $s['panel_label'] ?? '' ); ?></span>
                <?php foreach ( $items as $it ) :
                    $x = max( 0, min( 100, floatval( $it['x'] ?? 50 ) ) );
                    $y = max( 0, min( 100, floatval( $it['y'] ?? 50 ) ) );
                ?>
                    <button type="button" class="osp-mk" data-hs style="left:<?php echo esc_attr( $x ); ?>%;top:<?php echo esc_attr( $y ); ?>%" aria-label="<?php echo esc_attr( $it['title'] ?? '' ); ?>">
                        <span class="osp-tip">
                            <?php if ( ! empty( $it['meta'] ) ) : ?><span class="osp-tip__meta"><?php echo esc_html( $it['meta'] ); ?></span><?php endif; ?>
                            <?php if ( ! empty( $it['title'] ) ) : ?><span class="osp-tip__t"><?php echo esc_html( $it['title'] ); ?></span><?php endif; ?>
                            <?php if ( ! empty( $it['text'] ) ) : ?><span class="osp-tip__x" style="display:block"><?php echo esc_html( $it['text'] ); ?></span><?php endif; ?>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <script>
        (function(){
            var root=document.querySelector('.<?php echo esc_js( $uid ); ?>[data-hotspots]'); if(!root){return;}
            var marks=[].slice.call(root.querySelectorAll('[data-hs]'));
            function close(){ marks.forEach(function(m){ m.classList.remove('on'); }); }
            marks.forEach(function(m){
                m.addEventListener('click', function(e){
                    e.stopPropagation();
                    var was=m.classList.contains('on');
                    close();
                    if(was){ return; }
                    m.classList.add('on');
                });
            });
            document.addEventListener('click', function(){ close(); });
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
