<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Quiz a punteggio — una domanda alla volta, ogni risposta vale dei punti, alla fine
 * l'esito della fascia raggiunta con il suo testo e il suo link (es. «Il pacchetto giusto
 * per te» → la pagina del pacchetto). Nata nell'ondata I4 (1.4.508) al posto della tile
 * Availability, il cui verdetto non portava da nessuna parte.
 *
 * Risposte: una per riga, «Testo|punti» (senza punti = 0). Esiti: ognuno vale da `min`
 * punti in su; vince il più alto raggiunto. Tutto il calcolo è nel browser, nessun dato
 * esce dalla pagina. Runtime inline senza '&&' né '<'/'>' (WordPress li altera).
 */
class Olobuild_ScoreQuiz_Tile extends Olobuild_Tile_Base {

    protected $type     = 'scorequiz';
    protected $name     = 'Quiz a punteggio';
    protected $icon     = 'dashicons-forms';
    protected $category = 'interactive';
    protected $defaults = [
        'eyebrow'       => 'Trova la soluzione giusta',
        'heading'       => 'Quale pacchetto fa per te?',
        'intro'         => 'Tre domande, meno di un minuto.',
        'questions'     => [
            [ 'text' => 'Quanto tempo puoi dedicarci?', 'options' => "Poco|1\nAbbastanza|2\nTutto quello che serve|3" ],
            [ 'text' => 'Hai già esperienza?', 'options' => "No, parto da zero|1\nUn po'|2\nSì, molta|3" ],
            [ 'text' => 'Cosa ti interessa di più?', 'options' => "Un risultato veloce|1\nUn percorso guidato|2\nApprofondire tutto|3" ],
        ],
        'results'       => [
            [ 'min' => 0, 'title' => 'Base', 'text' => 'Il pacchetto Base è il punto di partenza giusto: essenziale e veloce.', 'link_text' => '', 'link_url' => '' ],
            [ 'min' => 5, 'title' => 'Plus', 'text' => 'Il pacchetto Plus ti accompagna passo per passo.', 'link_text' => '', 'link_url' => '' ],
            [ 'min' => 8, 'title' => 'Completo', 'text' => 'Il pacchetto Completo ti dà tutto, senza limiti.', 'link_text' => '', 'link_url' => '' ],
        ],
        'result_label'  => 'Il tuo risultato',
        'back_label'    => 'Indietro',
        'restart_label' => 'Ricomincia',
        'show_score'    => false,
        'zone_accent'   => '',
        'zone_on'       => 'var(--olo-color-primary-contrast, #ffffff)',
        'card_bg'       => '',
        'card_border'   => '',
        'align'         => 'left',
    ];

    public function get_controls() { return []; }

    /** Le risposte di una domanda: [ [ testo, punti ], … ]. */
    private function risposte( $testo ) {
        $out = [];
        foreach ( preg_split( '/\r\n|\r|\n/', (string) $testo ) as $riga ) {
            $riga = trim( $riga );
            if ( '' === $riga ) { continue; }
            $punti = 0;
            if ( false !== strpos( $riga, '|' ) ) {
                $parti = explode( '|', $riga );
                $punti = floatval( array_pop( $parti ) );
                $riga  = trim( implode( '|', $parti ) );
            }
            $out[] = [ $riga, $punti ];
        }
        return $out;
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'osq-' . $this->chiave_stabile( $settings );

        $domande = [];
        foreach ( (array) $s['questions'] as $q ) {
            if ( ! is_array( $q ) ) { continue; }
            $r = $this->risposte( $q['options'] ?? '' );
            if ( '' === trim( (string) ( $q['text'] ?? '' ) ) ) { continue; }
            if ( empty( $r ) ) { continue; }
            $domande[] = [ (string) $q['text'], $r ];
        }
        $esiti = array_values( array_filter( (array) $s['results'], 'is_array' ) );
        usort( $esiti, function ( $a, $b ) { return floatval( $a['min'] ?? 0 ) <=> floatval( $b['min'] ?? 0 ); } );
        if ( empty( $domande ) ) {
            return ! empty( $s['_builder_mode'] ) ? '<p>' . esc_html( olobuild_t( 'Aggiungi almeno una domanda con le sue risposte.' ) ) . '</p>' : '';
        }

        $accent = $this->safe_color_css( $s['zone_accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $on     = $this->safe_color_css( $s['zone_on'] ?? '' ) ?: 'var(--olo-color-primary-contrast, #ffffff)';
        $cardbg = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'var(--olo-color-surface, #ffffff)';
        $line   = Olobuild_Tile_Utils::border_color( $s['card_border'] ?? null, 'var(--olo-color-border, #e5e7eb)' );
        $center = ( ( $s['align'] ?? 'left' ) === 'center' );
        $n      = count( $domande );

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist (with fixed var() fallbacks), Olobuild_Tile_Utils::border_css()/border_color(), fixed literals; $uid is an internal md5-based id. ?>
        <style>
            .<?php echo $uid; ?>{--sq-accent:<?php echo $accent; ?>;--sq-on:<?php echo $on; ?>;--sq-line:<?php echo $line; ?>;<?php if ( $center ) echo 'text-align:center;'; ?>}
            .<?php echo $uid; ?> .osq-eyebrow{display:block;font-size:12px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--sq-accent);margin-bottom:0.625rem;}
            .<?php echo $uid; ?> .osq-h{font-family:var(--olo-font-family-heading,inherit);font-size:clamp(26px,3.6vw,40px);line-height:1.12;margin:0;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .osq-intro{font-size:15.5px;line-height:1.6;opacity:.8;margin:0.75rem 0 0;}
            .<?php echo $uid; ?> .osq-card{margin-top:1.5rem;background:<?php echo $cardbg; ?>;<?php echo esc_attr( Olobuild_Tile_Utils::border_css( $s['card_border'] ?? null, [ 'width' => 1, 'color' => $line ] ) ); ?>border-radius:1rem;padding:clamp(1.25rem,3vw,2rem);text-align:left;<?php echo $center ? 'max-width:640px;margin-left:auto;margin-right:auto;' : ''; ?>}
            .<?php echo $uid; ?> .osq-bar{height:4px;border-radius:6.1875rem;background:var(--sq-line);overflow:hidden;margin-bottom:1.125rem;}
            .<?php echo $uid; ?> .osq-bar i{display:block;height:100%;width:0;background:var(--sq-accent);transition:width .3s ease;}
            .<?php echo $uid; ?> .osq-step{font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;opacity:.6;}
            .<?php echo $uid; ?> .osq-q{font-size:20px;font-weight:600;line-height:1.35;margin:0.5rem 0 1rem;color:var(--olo-color-text,#111827);outline:0;}
            .<?php echo $uid; ?> .osq-opts{display:grid;gap:0.625rem;}
            .<?php echo $uid; ?> .osq-opt{font:inherit;font-size:15.5px;text-align:left;padding:0.875rem 1.125rem;border-radius:0.75rem;border:1.5px solid var(--sq-line);background:transparent;color:var(--olo-color-text,#111827);cursor:pointer;transition:border-color .15s,background .15s;}
            .<?php echo $uid; ?> .osq-opt:hover{border-color:var(--sq-accent);background:color-mix(in srgb,var(--sq-accent) 8%,transparent);}
            .<?php echo $uid; ?> .osq-opt:focus-visible,.<?php echo $uid; ?> .osq-link:focus-visible,.<?php echo $uid; ?> .osq-ghost:focus-visible{outline:2px solid var(--sq-accent);outline-offset:2px;}
            .<?php echo $uid; ?> .osq-nav{display:flex;justify-content:space-between;gap:0.75rem;margin-top:1.125rem;}
            .<?php echo $uid; ?> .osq-ghost{font:inherit;font-size:13px;background:transparent;border:0;color:inherit;opacity:.7;cursor:pointer;padding:0.375rem 0;text-decoration:underline;text-underline-offset:3px;}
            .<?php echo $uid; ?> .osq-res-t{font-size:clamp(24px,3vw,32px);font-weight:700;color:var(--sq-accent);margin:0.375rem 0 0.5rem;outline:0;}
            .<?php echo $uid; ?> .osq-res-p{font-size:15.5px;line-height:1.6;margin:0 0 1.125rem;}
            .<?php echo $uid; ?> .osq-score{font-size:13px;opacity:.65;margin:0 0 0.875rem;}
            .<?php echo $uid; ?> .osq-link{display:inline-flex;align-items:center;gap:0.5rem;font-weight:600;font-size:15px;color:var(--sq-on);background:var(--sq-accent);padding:0.75rem 1.5rem;border-radius:62.4375rem;text-decoration:none;}
            .<?php echo $uid; ?> [hidden]{display:none !important;}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-scorequiz <?php echo esc_attr( $uid ); ?>" data-scorequiz>
            <?php if ( '' !== $s['eyebrow'] ) : ?><span class="osq-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
            <?php if ( '' !== $s['heading'] ) : ?><h2 class="osq-h"><?php echo esc_html( $s['heading'] ); ?></h2><?php endif; ?>
            <?php if ( '' !== $s['intro'] ) : ?><p class="osq-intro"><?php echo esc_html( $s['intro'] ); ?></p><?php endif; ?>
            <div class="osq-card">
                <div class="osq-bar" aria-hidden="true"><i data-sq-bar></i></div>
                <?php foreach ( $domande as $i => $d ) : ?>
                    <div class="osq-pane" data-sq-q="<?php echo (int) $i; ?>"<?php echo 0 === $i ? '' : ' hidden'; ?>>
                        <div class="osq-step"><?php echo esc_html( sprintf( olobuild_t( 'Domanda %1$d di %2$d' ), $i + 1, $n ) ); ?></div>
                        <p class="osq-q" tabindex="-1"><?php echo esc_html( $d[0] ); ?></p>
                        <div class="osq-opts" role="group" aria-label="<?php echo esc_attr( $d[0] ); ?>">
                            <?php foreach ( $d[1] as $r ) : ?>
                                <button type="button" class="osq-opt" data-olo-interactive data-sq-pts="<?php echo esc_attr( $r[1] ); ?>"><?php echo esc_html( $r[0] ); ?></button>
                            <?php endforeach; ?>
                        </div>
                        <?php if ( $i > 0 ) : ?>
                            <div class="osq-nav"><button type="button" class="osq-ghost" data-olo-interactive data-sq-back><?php echo esc_html( $s['back_label'] ); ?></button></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="osq-pane" data-sq-result hidden aria-live="polite">
                    <div class="osq-step"><?php echo esc_html( $s['result_label'] ); ?></div>
                    <?php foreach ( $esiti as $k => $e ) :
                        $lt = trim( (string) ( $e['link_text'] ?? '' ) );
                        $lu = trim( (string) ( $e['link_url'] ?? '' ) );
                    ?>
                        <div data-sq-tier="<?php echo esc_attr( floatval( $e['min'] ?? 0 ) ); ?>" hidden>
                            <p class="osq-res-t" tabindex="-1"><?php echo esc_html( $e['title'] ?? '' ); ?></p>
                            <?php if ( ! empty( $e['text'] ) ) : ?><p class="osq-res-p"><?php echo esc_html( $e['text'] ); ?></p><?php endif; ?>
                            <?php if ( '' !== $lt ) : ?>
                                <?php if ( '' !== $lu ) : ?><a class="osq-link" href="<?php echo esc_url( $lu ); ?>"><?php echo esc_html( $lt ); ?></a><?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if ( ! empty( $s['show_score'] ) ) : ?><p class="osq-score"><?php echo esc_html( olobuild_t( 'Punteggio' ) ); ?>: <span data-sq-score>0</span></p><?php endif; ?>
                    <div class="osq-nav"><button type="button" class="osq-ghost" data-olo-interactive data-sq-restart><?php echo esc_html( $s['restart_label'] ); ?></button></div>
                </div>
            </div>
        </div>
        <script>
        (function(){
            var root=document.querySelector('.<?php echo esc_js( $uid ); ?>[data-scorequiz]'); if(!root){return;}
            if(root.getAttribute('data-sq-pronto')){return;}
            root.setAttribute('data-sq-pronto','1');
            var panes=[].slice.call(root.querySelectorAll('[data-sq-q]'));
            var esito=root.querySelector('[data-sq-result]');
            var tiers=[].slice.call(root.querySelectorAll('[data-sq-tier]'));
            var bar=root.querySelector('[data-sq-bar]');
            var punti=[], ora=0;
            function mostra(i){
                ora=i;
                panes.forEach(function(p,k){ p.hidden=(k!==i); });
                esito.hidden=(i!==panes.length);
                if(bar){ bar.style.width=(i/panes.length*100)+'%'; }
            }
            function fuoco(el){ if(el){ try{ el.focus({preventScroll:true}); }catch(e){ el.focus(); } } }
            function concludi(){
                var tot=0; punti.forEach(function(v){ tot+=v; });
                var scelto=null;
                /* l'esito più alto raggiunto: soglia uguale o inferiore al totale */
                tiers.forEach(function(t){ var m=parseFloat(t.getAttribute('data-sq-tier'))||0; if(Math.max(tot,m)===tot){ scelto=t; } });
                if(!scelto){ scelto=tiers[0]||null; }
                tiers.forEach(function(t){ t.hidden=(t!==scelto); });
                var sc=root.querySelector('[data-sq-score]'); if(sc){ sc.textContent=String(Math.round(tot*100)/100); }
                mostra(panes.length);
                if(scelto){ fuoco(scelto.querySelector('.osq-res-t')); }
            }
            panes.forEach(function(p,k){
                [].slice.call(p.querySelectorAll('[data-sq-pts]')).forEach(function(b){
                    b.addEventListener('click',function(){
                        punti[k]=parseFloat(b.getAttribute('data-sq-pts'))||0;
                        if(k+1===panes.length){ concludi(); return; }
                        mostra(k+1); fuoco(panes[k+1].querySelector('.osq-q'));
                    });
                });
                var back=p.querySelector('[data-sq-back]');
                if(back){ back.addEventListener('click',function(){ mostra(k-1); fuoco(panes[k-1].querySelector('.osq-q')); }); }
            });
            var again=root.querySelector('[data-sq-restart]');
            if(again){ again.addEventListener('click',function(){ punti=[]; mostra(0); fuoco(panes[0].querySelector('.osq-q')); }); }
            mostra(0);
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
