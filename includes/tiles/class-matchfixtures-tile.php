<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Match Fixtures — rail partite sport: data/ora+luogo, badge lega+giornata, due crest
 * squadra (monogramma), punteggio o "vs", riga squadra/venue. Render == Vue. Nessun JS.
 * Valori 1:1 dal blueprint OLOthemes "MatchFixtures" (verdano.css .fix-rail/.fix).
 */
class Olo_MatchFixtures_Tile extends Olo_Tile_Base {

    protected $type     = 'matchfixtures';
    protected $name     = 'Match Fixtures';
    protected $icon     = 'dashicons-calendar-alt';
    protected $category = 'content';
    protected $defaults = [
        'items' => [
            [ 'day' => 'Sat, 14.03', 'time_place' => '15:00 · Verdano Park', 'league' => 'Super League', 'matchday' => 'Matchday 04', 'home_crest' => 'VF', 'home_crest_bg' => '#15543c', 'home_name' => 'Verdano FC', 'away_crest' => 'RA', 'away_crest_bg' => '#7a2230', 'away_name' => 'Real Alta', 'score' => '', 'venue' => "First Men's Team" ],
        ],
        'columns'          => 3,
        'gap'              => 16,
        'card_bg'          => '#0f3a2a',
        'card_border'      => 'rgba(255,255,255,0.1)',
        'accent'           => '',
        'day_color'        => '#ffffff',
        'meta_color'       => 'rgba(255,255,255,0.55)',
        'name_color'       => '#ffffff',
        'score_color'      => '#ffffff',
        'crest_text_color' => '#ffffff',
        'radius'           => 18,

        // ── Spaziatura / Forma (additivi, no-op coi default) ──
        // Padding interno card: oggi fisso 22px → default = ESATTAMENTE 22 su 4 lati.
        'content_padding'  => [ 'top' => 22, 'right' => 22, 'bottom' => 22, 'left' => 22 ],
        // Override 4-angoli del raggio card: vuoto/0 = usa il range 'radius' (no-op).
        'card_radius'      => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],

        // KIT standard OLObuild: sfondo completo opzionale + ombra + bordo (no-op coi default)
        'bg'                      => [ 'type' => 'none' ],
        'shadow'                  => 'none',
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'omf-' . wp_rand( 10000, 99999 );

        $cols   = max( 1, min( 4, intval( $s['columns'] ) ) );
        $gap    = intval( $s['gap'] ) . 'px';
        $accent = $this->safe_color_css( $s['accent'] ) ?: 'var(--olo-color-primary, #c8ff3c)';
        $cbg    = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'var(--olo-color-surface-alt, #0f3a2a)';
        $cbd    = $this->safe_color_css( $s['card_border'] ?? '' ) ?: 'rgba(255,255,255,0.1)';
        $dayc   = $this->safe_color_css( $s['day_color'] ?? '' ) ?: '#ffffff';
        $meta   = $this->safe_color_css( $s['meta_color'] ?? '' ) ?: 'rgba(255,255,255,0.55)';
        $namec  = $this->safe_color_css( $s['name_color'] ?? '' ) ?: '#ffffff';
        $scorec = $this->safe_color_css( $s['score_color'] ?? '' ) ?: '#ffffff';
        $crestc = $this->safe_color_css( $s['crest_text_color'] ?? '' ) ?: '#ffffff';
        $rad    = intval( $s['radius'] ) . 'px';

        // ── Spaziatura card: padding da 'content_padding' (default 22px su 4 lati = invariato) ──
        $cp   = is_array( $s['content_padding'] ?? null ) ? $s['content_padding'] : [];
        $cp_t = max( 0, intval( $cp['top']    ?? 22 ) );
        $cp_r = max( 0, intval( $cp['right']  ?? 22 ) );
        $cp_b = max( 0, intval( $cp['bottom'] ?? 22 ) );
        $cp_l = max( 0, intval( $cp['left']   ?? 22 ) );
        // 4 lati uguali → forma compatta (byte-identica al precedente "22px"); altrimenti 4 valori.
        $card_pad = ( $cp_t === $cp_r && $cp_r === $cp_b && $cp_b === $cp_l )
            ? "{$cp_t}px"
            : "{$cp_t}px {$cp_r}px {$cp_b}px {$cp_l}px";

        // ── Forma card: override 4-angoli SOLO se valorizzato, altrimenti il range 'radius' (no-op) ──
        $card_rad = $this->build_border_radius_css( $s['card_radius'] ?? [] );
        if ( $card_rad === '' ) { $card_rad = $rad; }
        $disp   = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
        $sans   = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";
        $badgebg = 'color-mix(in srgb, ' . $accent . ' 16%, transparent)';

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) return '';

        // ── KIT standard: sfondo completo (override SOLO se valorizzato → default invariato) ──
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // ── KIT standard: ombra (preset/custom; '' = nessuna) ──
        $shadow_css = $this->build_shadow_decl( $s );
        // ── KIT standard: bordo (come la coda di particlefx) ──
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        // Coda di dichiarazioni extra per la regola .$uid del contenitore (no-op coi default).
        $kit_extra = '';
        if ( $bg_decl )    { $kit_extra .= $bg_decl . ';'; }
        if ( $shadow_css ) { $kit_extra .= 'box-shadow:' . $shadow_css . ';'; }
        if ( $border_css ) { $kit_extra .= $border_css; }
        // Position:relative necessario per gli effetti bordo; aggiunto solo se servono.
        $kit_pos = ( $border_css || $border_hover_css || $border_effect_css ) ? 'position:relative;' : '';

        $shield = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2 4 6v6c0 5 8 8 8 8s8-3 8-8V6Z"/></svg>';
        $pin    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{display:grid;grid-template-columns:repeat(<?php echo $cols; ?>,1fr);gap:<?php echo $gap; ?>;font-family:<?php echo $sans; ?>;<?php echo $kit_pos; ?><?php echo $kit_extra; ?>}
            .<?php echo $uid; ?> .omf-fix{background:<?php echo $cbg; ?>;border:1px solid <?php echo $cbd; ?>;border-radius:<?php echo $card_rad; ?>;padding:<?php echo $card_pad; ?>;display:flex;flex-direction:column;gap:18px;transition:border-color .2s,transform .3s;}
            .<?php echo $uid; ?> .omf-fix:hover{transform:translateY(-4px);border-color:color-mix(in srgb, <?php echo $accent; ?> 40%, transparent);}
            .<?php echo $uid; ?> .omf-top{display:flex;align-items:center;justify-content:space-between;gap:10px;}
            .<?php echo $uid; ?> .omf-when b{font-family:<?php echo $disp; ?>;font-weight:800;font-size:15px;color:<?php echo $dayc; ?>;display:block;}
            .<?php echo $uid; ?> .omf-when span{font-size:12px;color:<?php echo $meta; ?>;}
            .<?php echo $uid; ?> .omf-league{display:flex;align-items:center;gap:8px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:<?php echo $accent; ?>;text-align:right;}
            .<?php echo $uid; ?> .omf-badge{width:26px;height:26px;border-radius:7px;background:<?php echo $badgebg; ?>;display:grid;place-items:center;flex:none;}
            .<?php echo $uid; ?> .omf-badge svg{width:14px;height:14px;color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .omf-teams{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:8px;padding:10px 0;border-top:1px solid <?php echo $cbd; ?>;border-bottom:1px solid <?php echo $cbd; ?>;}
            .<?php echo $uid; ?> .omf-side{display:flex;flex-direction:column;align-items:center;gap:9px;text-align:center;}
            .<?php echo $uid; ?> .omf-crest{display:inline-grid;place-items:center;width:46px;height:50px;font-family:<?php echo $disp; ?>;font-weight:900;font-size:15px;letter-spacing:.02em;color:<?php echo $crestc; ?>;border-radius:14px 14px 16px 16px/14px 14px 22px 22px;box-shadow:inset 0 0 0 2px rgba(255,255,255,.2);}
            .<?php echo $uid; ?> .omf-nm{font-family:<?php echo $disp; ?>;font-weight:800;font-size:13px;text-transform:uppercase;color:<?php echo $namec; ?>;line-height:1.05;}
            .<?php echo $uid; ?> .omf-score{font-family:<?php echo $disp; ?>;font-weight:900;font-size:26px;color:<?php echo $scorec; ?>;text-align:center;min-width:64px;}
            .<?php echo $uid; ?> .omf-score.omf-vs{color:<?php echo $accent; ?>;font-size:18px;}
            .<?php echo $uid; ?> .omf-venue{display:flex;align-items:center;gap:7px;font-size:12.5px;color:<?php echo $meta; ?>;}
            .<?php echo $uid; ?> .omf-venue svg{width:14px;height:14px;flex:none;opacity:.85;}
            @media(max-width:960px){.<?php echo $uid; ?>{grid-template-columns:repeat(2,1fr);}}
            @media(max-width:620px){.<?php echo $uid; ?>{grid-template-columns:1fr;}}
        </style>
        <div class="olo-matchfixtures <?php echo esc_attr( $uid ); ?>">
            <?php foreach ( $items as $it ) :
                $score = trim( (string) ( $it['score'] ?? '' ) );
                $hbg = $this->safe_color_css( $it['home_crest_bg'] ?? '' ) ?: '#15543c';
                $abg = $this->safe_color_css( $it['away_crest_bg'] ?? '' ) ?: '#7a2230';
            ?>
                <article class="omf-fix">
                    <div class="omf-top">
                        <div class="omf-when"><b><?php echo esc_html( $it['day'] ?? '' ); ?></b><span><?php echo esc_html( $it['time_place'] ?? '' ); ?></span></div>
                        <div class="omf-league"><span class="omf-badge"><?php echo $shield; ?></span><span><?php echo esc_html( $it['league'] ?? '' ); ?><?php if ( ! empty( $it['matchday'] ) ) : ?><br><?php echo esc_html( $it['matchday'] ); ?><?php endif; ?></span></div>
                    </div>
                    <div class="omf-teams">
                        <div class="omf-side"><span class="omf-crest" style="background:<?php echo $hbg; ?>"><?php echo esc_html( $it['home_crest'] ?? '' ); ?></span><span class="omf-nm"><?php echo esc_html( $it['home_name'] ?? '' ); ?></span></div>
                        <div class="omf-score<?php echo $score === '' ? ' omf-vs' : ''; ?>"><?php echo $score === '' ? 'vs' : esc_html( $score ); ?></div>
                        <div class="omf-side"><span class="omf-crest" style="background:<?php echo $abg; ?>"><?php echo esc_html( $it['away_crest'] ?? '' ); ?></span><span class="omf-nm"><?php echo esc_html( $it['away_name'] ?? '' ); ?></span></div>
                    </div>
                    <?php if ( ! empty( $it['venue'] ) ) : ?><div class="omf-venue"><?php echo $pin; ?><?php echo esc_html( $it['venue'] ); ?></div><?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
        <?php
        // ── KIT standard: hover bordo + effetti bordo (no-op coi default) ──
        if ( $border_hover_css || $border_effect_css ) {
            echo '<style>' . $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none.
     * Copiato 1:1 da Olo_Particlefx_Tile (KIT standard OLObuild).
     */
    private function build_shadow_decl( $s ) {
        $preset = $s['shadow'] ?? 'none';
        if ( $preset === 'none' || $preset === '' ) {
            return '';
        }
        if ( $preset === 'custom' ) {
            $h      = intval( $s['shadow_h'] ?? 0 );
            $v      = intval( $s['shadow_v'] ?? 4 );
            $blur   = max( 0, intval( $s['shadow_blur'] ?? 10 ) );
            $spread = intval( $s['shadow_spread'] ?? 0 );
            $color  = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $inset  = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            return "{$inset}{$h}px {$v}px {$blur}px {$spread}px {$color}";
        }
        $map = [
            'sm' => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md' => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg' => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl' => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
        ];
        return $map[ $preset ] ?? '';
    }
}
