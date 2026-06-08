<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Availability — griglia toggle (fasce × giorni) → conteggio → verdetto a soglie.
 * Estratto dai demo OLOthemes (AvailabilityHeat). Render == Vue (AvailabilityTile.vue).
 * Lookup conteggio→tier precomputato lato PHP (stringa di cifre) → JS senza '&&' né '<'/'>'.
 */
class Olo_Availability_Tile extends Olo_Tile_Base {

    protected $type     = 'availability';
    protected $name     = 'Availability';
    protected $icon     = 'dashicons-calendar';
    protected $category = 'interactive';
    protected $defaults = [
        'eyebrow'       => '',
        'heading'       => 'Quando puoi?',
        'intro'         => '',
        'days'          => 'Mon, Tue, Wed, Thu, Fri, Sat, Sun',
        'bands'         => 'Morning, Midday, Evening',
        'count_label'   => 'Slot scelti',
        'verdict_label' => 'Consigliato',
        'tiers'         => [
            [ 'min' => 0, 'label' => 'Reset', 'text' => 'Poco tempo: una base sostenibile.' ],
            [ 'min' => 5, 'label' => 'Build', 'text' => 'Buona costanza: progressione vera.' ],
            [ 'min' => 10, 'label' => 'Peak', 'text' => 'Massima disponibilità: spingi.' ],
        ],
        'zone_accent'   => '',
        'zone_on'       => '#ffffff',
        'cell_bg'       => '',
        'card_border'   => '',
        'align'         => 'left',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'oav-' . wp_rand( 10000, 99999 );

        $accent = $this->safe_color_css( $s['zone_accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $on     = $this->safe_color_css( $s['zone_on'] ?? '' ) ?: '#ffffff';
        $cellbg = $this->safe_color_css( $s['cell_bg'] ?? '' ) ?: 'var(--olo-color-surface, #ffffff)';
        $line   = $this->safe_color_css( $s['card_border'] ?? '' ) ?: 'var(--olo-color-border, #e5e7eb)';
        $center = ( ( $s['align'] ?? 'left' ) === 'center' );
        $serif  = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
        $sans   = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";

        $days  = array_values( array_filter( array_map( 'trim', explode( ',', (string) $s['days'] ) ), function ( $d ) { return $d !== ''; } ) );
        $bands = array_values( array_filter( array_map( 'trim', explode( ',', (string) $s['bands'] ) ), function ( $d ) { return $d !== ''; } ) );
        $nd = count( $days ); $nb = count( $bands );
        if ( $nd === 0 || $nb === 0 ) return '';
        $maxc = $nd * $nb;

        $tiers = is_array( $s['tiers'] ) ? array_values( $s['tiers'] ) : [];
        if ( empty( $tiers ) ) $tiers = [ [ 'min' => 0, 'label' => '', 'text' => '' ] ];
        usort( $tiers, function ( $a, $b ) { return intval( $a['min'] ?? 0 ) - intval( $b['min'] ?? 0 ); } );
        $nt = count( $tiers );

        // Precompute count -> tier index lookup (server-side; JS only indexes it).
        $lookup = '';
        for ( $c = 0; $c <= $maxc; $c++ ) {
            $idx = 0;
            for ( $ti = 0; $ti < $nt; $ti++ ) {
                if ( $c >= intval( $tiers[ $ti ]['min'] ?? 0 ) ) { $idx = $ti; }
            }
            $lookup .= (string) min( 9, $idx );
        }

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{ --av-accent:<?php echo $accent; ?>; --av-on:<?php echo $on; ?>; font-family:<?php echo $sans; ?>; <?php if ( $center ) echo 'text-align:center;'; ?> }
            .<?php echo $uid; ?> .oav-eyebrow{font-size:12px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--av-accent);display:block;margin-bottom:10px;}
            .<?php echo $uid; ?> .oav-h{font-family:<?php echo $serif; ?>;font-size:clamp(26px,3.6vw,42px);line-height:1.12;margin:0;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .oav-intro{font-size:15.5px;line-height:1.6;opacity:.8;margin:14px 0 24px;max-width:560px;<?php echo $center ? 'margin-left:auto;margin-right:auto;' : ''; ?>}
            .<?php echo $uid; ?> .oav-grid{display:grid;grid-template-columns:minmax(64px,auto) repeat(<?php echo $nd; ?>,1fr);gap:6px;text-align:left;}
            .<?php echo $uid; ?> .oav-hd{font-weight:700;font-size:11px;letter-spacing:.05em;text-transform:uppercase;color:var(--olo-color-text-muted,#6b7280);padding:6px 4px;align-self:center;}
            .<?php echo $uid; ?> .oav-bl{font-weight:700;font-size:12px;color:var(--olo-color-text-muted,#6b7280);align-self:center;}
            .<?php echo $uid; ?> .oav-cell{background:<?php echo $cellbg; ?>;border:1px solid <?php echo $line; ?>;border-radius:8px;min-height:34px;cursor:pointer;transition:all .12s;padding:0;}
            .<?php echo $uid; ?> .oav-cell:hover{border-color:var(--av-accent);}
            .<?php echo $uid; ?> .oav-cell.on{background:var(--av-accent);border-color:var(--av-accent);}
            .<?php echo $uid; ?> .oav-cell:focus-visible{outline:2px solid var(--av-accent);outline-offset:2px;}
            .<?php echo $uid; ?> .oav-foot{display:flex;align-items:center;gap:22px;flex-wrap:wrap;margin-top:24px;padding-top:20px;border-top:2px solid <?php echo $line; ?>;<?php echo $center ? 'justify-content:center;' : ''; ?>}
            .<?php echo $uid; ?> .oav-count b{font-family:<?php echo $serif; ?>;font-size:30px;color:var(--av-accent);}
            .<?php echo $uid; ?> .oav-count span{font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;opacity:.6;display:block;}
            .<?php echo $uid; ?> .oav-tier{display:none;}
            .<?php echo $uid; ?> .oav-tier.show{display:block;}
            .<?php echo $uid; ?> .oav-tier__l{font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;opacity:.6;}
            .<?php echo $uid; ?> .oav-tier__n{font-family:<?php echo $serif; ?>;font-size:22px;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .oav-tier__x{font-size:13.5px;line-height:1.5;opacity:.8;margin-top:2px;max-width:340px;}
        </style>
        <div class="olo-availability <?php echo esc_attr( $uid ); ?>" data-availability data-lookup="<?php echo esc_attr( $lookup ); ?>">
            <?php if ( $s['eyebrow'] !== '' ) : ?><span class="oav-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
            <?php if ( $s['heading'] !== '' ) : ?><h2 class="oav-h"><?php echo esc_html( $s['heading'] ); ?></h2><?php endif; ?>
            <?php if ( $s['intro'] !== '' ) : ?><p class="oav-intro"><?php echo esc_html( $s['intro'] ); ?></p><?php endif; ?>
            <div class="oav-grid">
                <div class="oav-hd"></div>
                <?php foreach ( $days as $d ) : ?><div class="oav-hd"><?php echo esc_html( $d ); ?></div><?php endforeach; ?>
                <?php foreach ( $bands as $b ) : ?>
                    <div class="oav-bl"><?php echo esc_html( $b ); ?></div>
                    <?php for ( $i = 0; $i < $nd; $i++ ) : ?>
                        <button type="button" class="oav-cell" data-av-cell aria-label="<?php echo esc_attr( $b . ' ' . $days[ $i ] ); ?>"></button>
                    <?php endfor; ?>
                <?php endforeach; ?>
            </div>
            <div class="oav-foot">
                <div class="oav-count"><span><?php echo esc_html( $s['count_label'] ?? '' ); ?></span><b data-av-count>0</b></div>
                <div>
                    <?php foreach ( $tiers as $ti => $tr ) : ?>
                        <div class="oav-tier<?php echo $ti === 0 ? ' show' : ''; ?>" data-av-tier="<?php echo intval( $ti ); ?>">
                            <div class="oav-tier__l"><?php echo esc_html( $s['verdict_label'] ?? '' ); ?></div>
                            <div class="oav-tier__n"><?php echo esc_html( $tr['label'] ?? '' ); ?></div>
                            <?php if ( ! empty( $tr['text'] ) ) : ?><div class="oav-tier__x"><?php echo esc_html( $tr['text'] ); ?></div><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <script>
        (function(){
            var root=document.querySelector('.<?php echo $uid; ?>[data-availability]'); if(!root){return;}
            var lookup=root.getAttribute('data-lookup')||'0';
            var cells=[].slice.call(root.querySelectorAll('[data-av-cell]'));
            var countEl=root.querySelector('[data-av-count]');
            var tiers=[].slice.call(root.querySelectorAll('[data-av-tier]'));
            function update(){
                var n=0;
                cells.forEach(function(c){ if(c.classList.contains('on')){ n+=1; } });
                if(countEl){ countEl.textContent=n; }
                var idx=parseInt(lookup.charAt(Math.min(n, lookup.length-1)))||0;
                tiers.forEach(function(t){ t.classList.toggle('show', parseInt(t.getAttribute('data-av-tier'))===idx); });
            }
            cells.forEach(function(c){ c.addEventListener('click', function(){ c.classList.toggle('on'); update(); }); });
            update();
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
