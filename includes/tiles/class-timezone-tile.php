<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Timezone — slider ora (città base) → orari locali + stato lavoro/limite/notte.
 * Estratto dai demo OLOthemes (setupTimezone).
 * Stati orari precomputati lato PHP (stringa 24 char w/o/s) → JS senza '&&' né '<'/'>'.
 *
 * Fuso di ogni città (1.4.506): `tz` = fuso IANA (Europe/Rome…), da cui il browser ricava
 * lo scarto di OGGI, ora legale compresa. Senza `tz` vale `offset` (ore da UTC, anche
 * 5.5); le città salvate prima con una sigla nota (PDT, BST, CEST…) vengono ricondotte al
 * loro fuso, e la sigla segue l'ora legale. Prima gli scarti erano fissi: d'inverno ogni
 * ora era sbagliata di un'ora, e i fusi da mezz'ora venivano arrotondati.
 */
class Olobuild_Timezone_Tile extends Olobuild_Tile_Base {

    protected $type     = 'timezone';
    protected $name     = 'Timezone';
    protected $icon     = 'dashicons-clock';
    protected $category = 'interactive';
    protected $defaults = [
        'eyebrow'     => '',
        'heading'     => 'Trova un orario che funziona',
        'intro'       => '',
        'base_label'  => 'La tua ora',
        'input_value' => 14,
        'work_start'  => 9,
        'work_end'    => 18,
        'items'       => [
            [ 'city' => 'Milano', 'tz' => 'Europe/Rome', 'offset' => 1, 'label' => '' ],
            [ 'city' => 'Londra', 'tz' => 'Europe/London', 'offset' => 0, 'label' => '' ],
            [ 'city' => 'New York', 'tz' => 'America/New_York', 'offset' => -5, 'label' => '' ],
            [ 'city' => 'Tokyo', 'tz' => 'Asia/Tokyo', 'offset' => 9, 'label' => '' ],
        ],
        'zone_accent' => '',
        'work_color'  => '',
        'ok_color'    => 'var(--olo-color-accent, #e0a23a)',
        'sleep_color' => '',
        'card_bg'     => '',
        'card_border' => '',
        'align'       => 'left',
    ];

    public function get_controls() { return []; }

    /**
     * Luminanza relativa (WCAG) di un fondo, coi token della Palette risolti; null se il
     * fondo non è pieno (vuoto, trasparente, velato sotto il 50%) o non si risolve.
     * Stesso calcolo di finder e leaderboard.
     */
    private function luminanza_fondo( $c ) {
        $c = trim( (string) $c );
        if ( '' === $c || 'transparent' === strtolower( $c ) ) {
            return null;
        }
        $alfa = 1.0;
        if ( preg_match( '/^#[0-9a-f]{6}([0-9a-f]{2})$/i', $c, $m ) ) {
            $alfa = hexdec( $m[1] ) / 255;
        } elseif ( preg_match( '/^rgba?\(\s*[\d.]+%?\s*[,\s]\s*[\d.]+%?\s*[,\s]\s*[\d.]+%?\s*[,\/]\s*([\d.]+)(%?)\s*\)$/i', $c, $m ) ) {
            $alfa = (float) $m[1] / ( '%' === $m[2] ? 100 : 1 );
        } elseif ( preg_match( '/^color-mix\(.+\s([\d.]+)%\s*,\s*transparent\s*\)$/is', $c, $m ) ) {
            $alfa = (float) $m[1] / 100;
        }
        $hex = Olobuild_Tile_Utils::colore_hex( $c );
        if ( $alfa < 0.5 || '' === $hex ) {
            return null;
        }
        $l = 0.0;
        foreach ( [ 1 => 0.2126, 3 => 0.7152, 5 => 0.0722 ] as $i => $k ) {
            $v  = hexdec( substr( $hex, $i, 2 ) ) / 255;
            $l += $k * ( $v <= 0.03928 ? $v / 12.92 : pow( ( $v + 0.055 ) / 1.055, 2.4 ) );
        }
        return $l;
    }

    /**
     * Il colore di un testo su un fondo disegnato dalla tile: $colore se si legge (contrasto
     * almeno 3:1), altrimenti il chiaro o lo scuro della Palette secondo il fondo. Prima titolo,
     * città e orari avevano --olo-color-text fisso: con le righe città o il contenitore scuri
     * erano scuro su scuro, e una versione scura non si poteva fare.
     */
    private function testo_su( $fondo, $colore ) {
        $lf = $this->luminanza_fondo( $fondo );
        $lc = $this->luminanza_fondo( $colore );
        if ( null === $lf || null === $lc ) {
            return $colore;
        }
        if ( ( max( $lf, $lc ) + 0.05 ) / ( min( $lf, $lc ) + 0.05 ) >= 3 ) {
            return $colore;
        }
        return $lf < 0.18 ? 'var(--olo-color-light, #fdfcfa)' : 'var(--olo-color-dark, #14161c)';
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'otz-' . wp_rand( 10000, 99999 );

        $accent = $this->safe_color_css( $s['zone_accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $work   = $this->safe_color_css( $s['work_color'] ?? '' ) ?: $accent;
        // Riserva sul token accento, come la chiave di serie: un esadecimale qui non seguiva la Palette.
        $ok     = $this->safe_color_css( $s['ok_color'] ?? '' ) ?: 'var(--olo-color-accent, #e0a23a)';
        $sleep  = $this->safe_color_css( $s['sleep_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #9ca3af)';
        $cardbg = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'var(--olo-color-surface, #ffffff)';
        $line   = Olobuild_Tile_Utils::border_color( $s['card_border'] ?? null, 'var(--olo-color-border, #e5e7eb)' );
        $center = ( ( $s['align'] ?? 'left' ) === 'center' );
        $serif  = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
        $sans   = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
        // Testi che seguono il fondo su cui stanno: titolo ed etichette sul fondo pieno del
        // contenitore della tile (se c'è; sennò la pagina, com'era), città e orari sullo sfondo
        // delle righe città. Dove si leggevano già escono identici.
        $testo_def  = 'var(--olo-color-text,#111827)';
        $fondo_zona = '';
        if ( is_array( $style ) && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $eff = ( new Olobuild_CSS_Builder() )->get_effective_bg( $style );
            if ( 'solid' === ( $eff['type'] ?? '' ) && intval( $eff['color_opacity'] ?? 100 ) >= 50 ) {
                $fondo_zona = $this->safe_color_css( $eff['color'] ?? '' );
            }
        }
        $testo_zona = $this->testo_su( $fondo_zona, $testo_def );
        // Righe città trasparenti (o velate): i loro testi stanno sulla zona.
        $card_piena = null !== $this->luminanza_fondo( $cardbg );
        $testo_card = $card_piena ? $this->testo_su( $cardbg, $testo_def ) : $testo_zona;
        $col_testo  = is_array( $style ) && ! empty( $style['text_color'] );
        // Il colore del testo del contenitore resta sulle righe finché ci si legge (lo scarto
        // orario lo seguiva già); un colore che non si risolve resta ereditato.
        $testo_cont = $col_testo ? $this->safe_color_css( (string) $style['text_color'] ) : '';
        $cont_ok    = '' !== $testo_cont && $this->testo_su( $cardbg, $testo_cont ) === $testo_cont;
        // Sulla radice solo se il contenitore non ha un colore del testo suo (il renderer lo
        // mette sul wrapper e introduzione ed etichetta lo ereditano). Una riga piena ha il suo
        // appena la zona cambia colore, o quando quello del contenitore sarebbe chiaro su
        // chiaro: sennò la sigla ereditava il chiaro anche sulla riga chiara.
        $colore_radice = ( $testo_zona !== $testo_def && ! $col_testo ) ? $testo_zona : '';
        $colore_card   = ( $card_piena && ! $cont_ok && ( $testo_card !== $testo_def || $testo_zona !== $testo_def || $col_testo ) ) ? $testo_card : '';
        // Su righe scure il filo di serie (grigio chiaro) diventa una velatura del testo.
        $line_card = $line;
        if ( $testo_card !== $testo_def && 'var(--olo-color-border, #e5e7eb)' === $line ) {
            $line_card = 'color-mix(in srgb, ' . $testo_card . ' 22%, transparent)';
        }

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) return '';
        $start    = intval( $s['input_value'] ?? 14 );

        // Precompute 24-hour state string (server-side; JS only indexes it).
        $ws = intval( $s['work_start'] ?? 9 );
        $we = intval( $s['work_end'] ?? 18 );
        $states = '';
        for ( $h = 0; $h < 24; $h++ ) {
            if ( $h >= $ws && $h < $we ) {
                $states .= 'w';
            } elseif ( ( $h >= $ws - 3 && $h < $ws ) || ( $h >= $we && $h < $we + 3 ) ) {
                $states .= 'o';
            } else {
                $states .= 's';
            }
        }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour (text colours chosen by testo_su() among those or fixed var()/color-mix() literals), min()/max()/count() integer column count, fixed font-stack and alignment literals; $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>{ --tz-accent:<?php echo $accent; ?>; font-family:<?php echo $sans; ?>; <?php if ( $center ) echo 'text-align:center;'; ?><?php if ( '' !== $colore_radice ) echo 'color:' . $colore_radice . ';'; ?> }
            .<?php echo $uid; ?> .otz-eyebrow{font-size:12px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--tz-accent);display:block;margin-bottom:10px;}
            .<?php echo $uid; ?> .otz-h{font-family:<?php echo $serif; ?>;font-size:clamp(26px,3.6vw,42px);line-height:1.12;margin:0;color:<?php echo $testo_zona; ?>;}
            .<?php echo $uid; ?> .otz-intro{font-size:15.5px;line-height:1.6;opacity:.8;margin:14px 0 22px;max-width:560px;<?php echo $center ? 'margin-left:auto;margin-right:auto;' : ''; ?>}
            .<?php echo $uid; ?> .otz-base{display:flex;align-items:baseline;justify-content:space-between;gap:14px;flex-wrap:wrap;}
            .<?php echo $uid; ?> .otz-base__l{font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;opacity:.7;}
            .<?php echo $uid; ?> .otz-base__v{font-family:<?php echo $serif; ?>;font-size:26px;color:var(--tz-accent);}
            .<?php echo $uid; ?> .otz-range{-webkit-appearance:none;appearance:none;width:100%;height:6px;border-radius:99px;cursor:pointer;background:linear-gradient(to right,var(--tz-accent) var(--pct,60%),<?php echo $line; ?> var(--pct,60%));margin:10px 0 24px;}
            .<?php echo $uid; ?> .otz-range::-webkit-slider-thumb{-webkit-appearance:none;width:20px;height:20px;border-radius:50%;background:var(--olo-color-surface, #ffffff);border:2px solid var(--tz-accent);box-shadow:0 1px 4px rgba(16,24,40,.3);cursor:pointer;}
            .<?php echo $uid; ?> .otz-range::-moz-range-thumb{width:20px;height:20px;border-radius:50%;background:var(--olo-color-surface, #ffffff);border:2px solid var(--tz-accent);cursor:pointer;}
            .<?php echo $uid; ?> .otz-range:focus-visible{outline:2px solid var(--tz-accent);outline-offset:4px;}
            .<?php echo $uid; ?> .otz-grid{display:grid;grid-template-columns:repeat(<?php echo min( 4, max( 1, count( $items ) ) ); ?>,1fr);gap:12px;}
            @media(max-width:680px){.<?php echo $uid; ?> .otz-grid{grid-template-columns:1fr 1fr;}}
            .<?php echo $uid; ?> .otz-city{background:<?php echo $cardbg; ?>;<?php echo esc_attr( Olobuild_Tile_Utils::border_css( $s['card_border'] ?? null, [ 'width' => 1, 'color' => $line_card ] ) ); ?>border-radius:12px;padding:16px;text-align:left;<?php if ( '' !== $colore_card ) echo 'color:' . $colore_card . ';'; ?>}
            .<?php echo $uid; ?> .otz-city__c{font-weight:600;font-size:14px;color:<?php echo $testo_card; ?>;}
            .<?php echo $uid; ?> .otz-city__o{font-size:11px;opacity:.55;letter-spacing:.04em;}
            .<?php echo $uid; ?> .otz-city__t{font-family:<?php echo $serif; ?>;font-size:24px;margin-top:8px;color:<?php echo $testo_card; ?>;font-variant-numeric:tabular-nums;display:flex;align-items:center;gap:8px;}
            .<?php echo $uid; ?> .otz-dot{width:9px;height:9px;border-radius:50%;background:<?php echo $sleep; ?>;flex:none;}
            .<?php echo $uid; ?> .otz-city[data-state="w"] .otz-dot{background:<?php echo $work; ?>;}
            .<?php echo $uid; ?> .otz-city[data-state="o"] .otz-dot{background:<?php echo $ok; ?>;}
            .<?php echo $uid; ?> .otz-city[data-state="s"] .otz-dot{background:<?php echo $sleep; ?>;}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-timezone <?php echo esc_attr( $uid ); ?>" data-timezone data-states="<?php echo esc_attr( $states ); ?>">
            <?php if ( $s['eyebrow'] !== '' ) : ?><span class="otz-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
            <?php if ( $s['heading'] !== '' ) : ?><h2 class="otz-h"><?php echo esc_html( $s['heading'] ); ?></h2><?php endif; ?>
            <?php if ( $s['intro'] !== '' ) : ?><p class="otz-intro"><?php echo esc_html( $s['intro'] ); ?></p><?php endif; ?>
            <div class="otz-base">
                <span class="otz-base__l"><?php echo esc_html( $s['base_label'] ?? '' ); ?> · <?php echo esc_html( $items[0]['city'] ?? '' ); ?></span>
                <span class="otz-base__v" data-tz-disp>—</span>
            </div>
            <input class="otz-range" type="range" data-tz-input min="0" max="23" step="1" value="<?php echo esc_attr( $start ); ?>" aria-label="<?php echo esc_attr( $s['base_label'] ?: 'hour' ); ?>"/>
            <div class="otz-grid">
                <?php foreach ( $items as $it ) : ?>
                    <div class="otz-city" data-tz-city data-tz-off="<?php echo esc_attr( floatval( $it['offset'] ?? 0 ) ); ?>" data-tz="<?php echo esc_attr( preg_replace( '/[^A-Za-z0-9_\/+\-]/', '', (string) ( $it['tz'] ?? '' ) ) ); ?>" data-state="s">
                        <div class="otz-city__c"><?php echo esc_html( $it['city'] ?? '' ); ?></div>
                        <div class="otz-city__o" data-tz-label><?php echo esc_html( $it['label'] ?? '' ); ?></div>
                        <div class="otz-city__t"><span class="otz-dot" role="img"></span><span data-tz-clock>—</span></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <script>
        (function(){
            var root=document.querySelector('.<?php echo esc_js( $uid ); ?>[data-timezone]'); if(!root){return;}
            var states=root.getAttribute('data-states')||'';
            var input=root.querySelector('[data-tz-input]');
            var disp=root.querySelector('[data-tz-disp]');
            var cities=[].slice.call(root.querySelectorAll('[data-tz-city]'));
            var NOMI={w:'<?php echo esc_js( olobuild_t( 'Orario di lavoro' ) ); ?>',o:'<?php echo esc_js( olobuild_t( 'Ai limiti' ) ); ?>',s:'<?php echo esc_js( olobuild_t( 'Notte' ) ); ?>'};
            /* Sigle con l'ora legale salvate nelle città senza fuso: [fuso, inverno, estate]. Solo
               quelle che non si confondono con zone senza ora legale (niente EST, CST, GMT, AEST). */
            var SIGLE={PST:['America/Los_Angeles','PST','PDT'],PDT:['America/Los_Angeles','PST','PDT'],MDT:['America/Denver','MST','MDT'],CDT:['America/Chicago','CST','CDT'],EDT:['America/New_York','EST','EDT'],BST:['Europe/London','GMT','BST'],WEST:['Europe/Lisbon','WET','WEST'],CET:['Europe/Berlin','CET','CEST'],CEST:['Europe/Berlin','CET','CEST'],EEST:['Europe/Athens','EET','EEST'],AEDT:['Australia/Sydney','AEST','AEDT'],NZST:['Pacific/Auckland','NZST','NZDT'],NZDT:['Pacific/Auckland','NZST','NZDT']};
            var anno=new Date().getFullYear();
            /* Minuti da UTC del fuso nel giorno dato (null se il browser non conosce il fuso). */
            function scartoIn(tz,giorno){
                try{
                    var d=new Date(giorno.getTime()); d.setSeconds(0,0);
                    var parti=new Intl.DateTimeFormat('en-US',{timeZone:tz,hourCycle:'h23',year:'numeric',month:'numeric',day:'numeric',hour:'numeric',minute:'numeric'}).formatToParts(d);
                    var o={}; parti.forEach(function(p){ o[p.type]=p.value; });
                    return Math.round((Date.UTC(+o.year,+o.month-1,+o.day,(+o.hour)%24,+o.minute)-d.getTime())/60000);
                }catch(e){ return null; }
            }
            function estivo(tz,ora){
                var gen=scartoIn(tz,new Date(Date.UTC(anno,0,1))), lug=scartoIn(tz,new Date(Date.UTC(anno,6,1)));
                if(gen===lug){ return false; }
                return ora===Math.max(gen,lug);
            }
            function due(n){ var x=String(n); return x.length===1?('0'+x):x; }
            function utc(m){ var a=Math.abs(m); var s=(m===a)?'+':'−'; var mi=a%60; return 'UTC'+s+Math.floor(a/60)+(mi?(':'+due(mi)):''); }
            /* Lo scarto di ogni città, oggi; aggiorna anche la sigla se segue l'ora legale. */
            var scarti=cities.map(function(c){
                var tz=c.getAttribute('data-tz')||'';
                var lab=c.querySelector('[data-tz-label]');
                var sigla=lab?lab.textContent.replace(/\s+/g,''):'';
                var coppia=SIGLE[sigla.toUpperCase()]||null;
                var fisso=Math.round((parseFloat(c.getAttribute('data-tz-off'))||0)*60);
                if(!tz){
                    if(coppia){
                        var g=scartoIn(coppia[0],new Date(Date.UTC(anno,0,1))), l=scartoIn(coppia[0],new Date(Date.UTC(anno,6,1)));
                        if(fisso===g){ tz=coppia[0]; } else if(fisso===l){ tz=coppia[0]; }
                    }
                }
                var ora=tz?scartoIn(tz,new Date()):null;
                if(ora===null){ ora=fisso; tz=''; }
                if(lab){
                    if(coppia){ if(tz){ lab.textContent=estivo(tz,ora)?coppia[2]:coppia[1]; } }
                    else if(!sigla){ if(tz){ lab.textContent=utc(ora); } }
                }
                return ora;
            });
            var base=scarti.length?scarti[0]:0;
            function recalc(){
                var h=parseFloat(input.value)||0;
                if(disp){ disp.textContent=due(h)+':00'; }
                var minUtc=h*60-base;
                cities.forEach(function(c,i){
                    var locale=((minUtc+scarti[i])%1440+1440)%1440;
                    var ore=Math.floor(locale/60);
                    var t=c.querySelector('[data-tz-clock]'); if(t){ t.textContent=due(ore)+':'+due(locale%60); }
                    var st=states.charAt(ore)||'s';
                    c.setAttribute('data-state', st);
                    var dot=c.querySelector('.otz-dot'); if(dot){ dot.setAttribute('aria-label',NOMI[st]); dot.setAttribute('title',NOMI[st]); }
                });
                input.style.setProperty('--pct',(h/23*100)+'%');
            }
            if(input){ input.addEventListener('input',recalc); recalc(); }
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
