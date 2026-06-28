<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Evo Notes — layer di annotazioni "Perché questa evoluzione" (blueprint Clod).
 * Tile-layer di pagina (una sola istanza, in fondo alla pagina): bottone fisso in
 * basso a destra che attiva/disattiva il layer; quando attivo compaiono marker
 * numerati circolari ancorati alle sezioni (per html_id o per ordine .olo-section)
 * e il click su un marker apre una card con titolo, testo e confronto "Prima → Ora".
 * Hint a pillola fisso in basso al centro (nascosto < 680px). Runtime JS per-istanza
 * scoped al proprio $uid (pattern smearhero: niente `&&`/`||` inline). Anteprima
 * Vue inline nel canvas (EvoNotesTile.vue) — i marker reali si ancorano alle
 * sezioni solo sul frontend. Estratta dal blueprint "Clod — Evoluzione v2".
 */
class Olobuild_EvoNotes_Tile extends Olobuild_Tile_Base {

    protected $type     = 'evonotes';
    protected $name     = 'Evo Notes (layer annotazioni)';
    protected $icon     = 'dashicons-info';
    protected $category = 'interactive';
    protected $defaults = [
        'toggle_label'        => 'Perché questa evoluzione',
        'toggle_label_active' => 'Nascondi motivazioni',
        'show_hint'           => true,
        'hint_text'           => 'Tocca i numeri per leggere ogni scelta — Prima → Ora',
        'kicker_label'        => 'Evoluzione',
        'accent'              => '',
        'card_bg'             => '',
        'text_color'          => '',
        'items'               => [
            [ 'number' => '01', 'title' => 'Identità tipografica', 'text' => 'Un display industriale proprio dà voce allo studio fin dalla prima schermata, invece di affidarsi al look di un tema preconfezionato.', 'before' => 'Tema YOOtheme', 'after' => 'Carattere proprio', 'anchor' => '', 'side' => 'right', 'offset' => '38%' ],
            [ 'number' => '02', 'title' => 'Una voce, non effetti', 'text' => 'Un messaggio editoriale netto sostituisce slider e animazioni: chi arriva capisce subito cosa fai e perché conta.', 'before' => 'Slider Revolution', 'after' => 'Messaggio chiaro', 'anchor' => '', 'side' => 'left', 'offset' => '70%' ],
            [ 'number' => '03', 'title' => 'Gerarchia leggibile', 'text' => 'I servizi diventano una lista numerata, scansionabile in un colpo d\'occhio — non più cinque parole schiacciate su una riga.', 'before' => 'Riga unica', 'after' => 'Lista 01–05', 'anchor' => 'servizi', 'side' => 'right', 'offset' => '30%' ],
            [ 'number' => '04', 'title' => 'Il lavoro al centro', 'text' => 'I progetti scorrono in un reel orizzontale cinematografico — trascina, usa la rotella o scorri — al posto di video sparsi senza ordine.', 'before' => 'Media sparsi', 'after' => 'Reel orizzontale', 'anchor' => 'lavori', 'side' => 'left', 'offset' => '18px' ],
            [ 'number' => '05', 'title' => 'Il sito è il prodotto', 'text' => 'Questo stesso sito è costruito come un OLOtheme: visitarlo significa vedere dal vivo cosa sa fare lo studio. Showreel e prova insieme.', 'before' => 'Portfolio statico', 'after' => 'Showreel vivo', 'anchor' => 'rs', 'side' => 'right', 'offset' => '20%' ],
            [ 'number' => '06', 'title' => 'Sala di regia', 'text' => 'Mirino col nome della sezione, timecode di scroll, grana pellicola, fotogrammi che si inclinano col drag: il mestiere — video e media — diventa il linguaggio stesso del sito.', 'before' => 'Pagina statica', 'after' => 'Monitor live', 'anchor' => 'contatto', 'side' => 'left', 'offset' => '26%' ],
        ],
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'oevn-' . wp_rand( 10000, 99999 );

        // ── Colori token-first (stesse stringhe dell'anteprima Vue) ──
        $accent  = $this->safe_color_css( $s['accent'] ?? '' ) ?: 'var(--olo-color-primary, #C6F24E)';
        $card_bg = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'var(--olo-color-surface-alt, #161922)';
        $txt     = $this->safe_color_css( $s['text_color'] ?? '' ) ?: 'var(--olo-color-text, #ECEAE3)';
        $on_acc  = 'var(--olo-color-on-primary, #0b0c0f)';
        $ink     = 'var(--olo-color-background, #0b0c0f)';
        $soft    = 'var(--olo-color-text-soft, #a0a298)';
        $faint   = 'var(--olo-color-text-faint, #6a6c64)';
        // line-2 del blueprint = testo al 20% (rgba(236,234,227,.20)) — segue la palette.
        $line2   = 'color-mix(in srgb, ' . $txt . ' 20%, transparent)';
        $ring    = 'color-mix(in srgb, ' . $accent . ' 30%, transparent)';
        $glow    = 'color-mix(in srgb, ' . $accent . ' 50%, transparent)';

        // ── Font via ruoli del tema ──
        $disp = "var(--olo-font-family-heading, 'Big Shoulders Display', sans-serif)";
        $sans = "var(--olo-font-family, 'Hanken Grotesk', sans-serif)";
        $mono = "var(--olo-font-family-mono, 'Space Mono', ui-monospace, monospace)";

        // Padding orizzontale di pagina del blueprint (--pad).
        $pad = 'clamp(20px,5vw,72px)';

        $label        = (string) ( $s['toggle_label'] ?? '' );
        $label_active = (string) ( $s['toggle_label_active'] ?? '' );
        if ( $label === '' )        { $label = 'Perché questa evoluzione'; }
        if ( $label_active === '' ) { $label_active = $label; }
        $kicker = (string) ( $s['kicker_label'] ?? '' );

        // ── Items sanificati ──
        $item_defaults = [ 'number' => '', 'title' => '', 'text' => '', 'before' => '', 'after' => '', 'anchor' => '', 'side' => 'right', 'offset' => '30%' ];
        $items = [];
        $raw_items = is_array( $s['items'] ?? null ) ? $s['items'] : [];
        foreach ( $raw_items as $raw ) {
            if ( ! is_array( $raw ) ) { continue; }
            $it = wp_parse_args( $raw, $item_defaults );
            $it['side']   = ( $it['side'] === 'left' ) ? 'left' : 'right';
            $it['anchor'] = preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $it['anchor'] );
            $off = trim( (string) $it['offset'] );
            if ( ! preg_match( '/^-?\d+(\.\d+)?(px|%|vh|vw|em|rem)$/', $off ) ) { $off = '30%'; }
            $it['offset'] = $off;
            $items[] = $it;
        }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every colour via the safe_color_css() whitelist or fixed var()/color-mix() literals, font stacks and paddings are fixed literals, $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>-toggle{position:fixed;right:22px;bottom:22px;z-index:90;display:inline-flex;align-items:center;gap:10px;background:<?php echo $accent; ?>;color:<?php echo $on_acc; ?>;font-family:<?php echo $sans; ?>;font-weight:700;font-size:13.5px;line-height:1.2;border:0;border-radius:999px;padding:13px 19px;cursor:pointer;box-shadow:0 14px 38px -10px <?php echo $glow; ?>;transition:transform .15s,background .15s;}
            .<?php echo $uid; ?>-toggle:hover{transform:translateY(-2px);}
            .<?php echo $uid; ?>-toggle:focus-visible{outline:none;box-shadow:0 14px 38px -10px <?php echo $glow; ?>,0 0 0 3px <?php echo $ring; ?>;}
            .<?php echo $uid; ?>-toggle .evn-ic{width:16px;height:16px;display:grid;place-items:center;}
            .<?php echo $uid; ?>-toggle .evn-ic b{width:9px;height:9px;border:2px solid <?php echo $on_acc; ?>;border-radius:50%;box-sizing:border-box;}
            body.olo-evo .<?php echo $uid; ?>-toggle{background:<?php echo $card_bg; ?>;color:<?php echo $txt; ?>;box-shadow:0 0 0 1px <?php echo $line2; ?>;}
            body.olo-evo .<?php echo $uid; ?>-toggle:focus-visible{box-shadow:0 0 0 1px <?php echo $line2; ?>,0 0 0 3px <?php echo $ring; ?>;}
            body.olo-evo .<?php echo $uid; ?>-toggle .evn-ic b{border-color:<?php echo $accent; ?>;background:<?php echo $accent; ?>;}
            .<?php echo $uid; ?>-pool{display:none;}
            .<?php echo $uid; ?>-m{position:absolute;z-index:40;width:34px;height:34px;border-radius:50%;background:<?php echo $accent; ?>;color:<?php echo $on_acc; ?>;font-family:<?php echo $mono; ?>;font-weight:700;font-size:13px;display:none;align-items:center;justify-content:center;cursor:pointer;border:2px solid <?php echo $ink; ?>;padding:0;box-shadow:0 6px 18px -6px rgba(0,0,0,.7);transition:transform .15s;}
            .<?php echo $uid; ?>-m::after{content:"";position:absolute;inset:-6px;border-radius:50%;}
            .<?php echo $uid; ?>-m:hover{transform:scale(1.12);}
            .<?php echo $uid; ?>-m:focus-visible{outline:none;box-shadow:0 6px 18px -6px rgba(0,0,0,.7),0 0 0 3px <?php echo $ring; ?>;}
            body.olo-evo .<?php echo $uid; ?>-m{display:flex;}
            .<?php echo $uid; ?>-m.open{background:<?php echo $txt; ?>;}
            .<?php echo $uid; ?>-m.evn-left{left:calc(<?php echo $pad; ?> + 2px);}
            .<?php echo $uid; ?>-m.evn-right{right:calc(<?php echo $pad; ?> - 17px);}
            .<?php echo $uid; ?>-c{position:absolute;z-index:41;width:300px;max-width:78vw;background:<?php echo $card_bg; ?>;border:1px solid <?php echo $line2; ?>;border-radius:13px;padding:17px 18px;box-shadow:0 30px 70px -20px rgba(0,0,0,.85);display:none;text-align:left;}
            .<?php echo $uid; ?>-c.show{display:block;}
            .<?php echo $uid; ?>-c.evn-left{left:<?php echo $pad; ?>;}
            .<?php echo $uid; ?>-c.evn-right{right:<?php echo $pad; ?>;}
            .<?php echo $uid; ?>-c .evn-k{font-family:<?php echo $mono; ?>;font-size:10.5px;letter-spacing:.1em;text-transform:uppercase;color:<?php echo $accent; ?>;margin:0 0 9px;}
            .<?php echo $uid; ?>-c h5{font-family:<?php echo $disp; ?>;font-weight:700;font-size:21px;text-transform:uppercase;line-height:1;letter-spacing:0;color:<?php echo $txt; ?>;margin:0 0 9px;}
            .<?php echo $uid; ?>-c p{font-family:<?php echo $sans; ?>;font-size:13.5px;line-height:1.55;color:<?php echo $soft; ?>;margin:0;}
            .<?php echo $uid; ?>-c .evn-ba{display:flex;gap:8px;margin-top:13px;font-family:<?php echo $mono; ?>;font-size:10.5px;text-transform:uppercase;letter-spacing:.04em;}
            .<?php echo $uid; ?>-c .evn-ba .evn-before{color:<?php echo $faint; ?>;text-decoration:line-through;}
            .<?php echo $uid; ?>-c .evn-ba .evn-arr{color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?>-c .evn-ba .evn-after{color:<?php echo $txt; ?>;}
            .<?php echo $uid; ?>-hint{position:fixed;left:50%;bottom:22px;transform:translateX(-50%);z-index:89;background:<?php echo $card_bg; ?>;border:1px solid <?php echo $line2; ?>;border-radius:999px;padding:10px 18px;font-family:<?php echo $sans; ?>;font-size:13px;line-height:1.4;color:<?php echo $soft; ?>;display:none;align-items:center;gap:9px;}
            .<?php echo $uid; ?>-hint .evn-sig{color:<?php echo $accent; ?>;font-weight:700;}
            body.olo-evo .<?php echo $uid; ?>-hint{display:flex;}
            @media(max-width:680px){.<?php echo $uid; ?>-hint{display:none!important;}}
            @media(prefers-reduced-motion:no-preference){
                body.olo-evo .<?php echo $uid; ?>-m{animation:pop-<?php echo $uid; ?> .3s ease backwards;}
                .<?php echo $uid; ?>-c.show{animation:pop-<?php echo $uid; ?> .22s ease;}
            }
            @keyframes pop-<?php echo $uid; ?>{from{opacity:0;transform:scale(.4);}}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-evonotes <?php echo esc_attr( $uid ); ?>">
            <button type="button" class="<?php echo esc_attr( $uid ); ?>-toggle evn-toggle" data-evn-toggle aria-pressed="false" data-label="<?php echo esc_attr( $label ); ?>" data-label-active="<?php echo esc_attr( $label_active ); ?>"><span class="evn-ic" aria-hidden="true"><b></b></span><span class="evn-lab"><?php echo esc_html( $label ); ?></span></button>
            <?php if ( ! empty( $s['show_hint'] ) && ! empty( $s['hint_text'] ) ) : ?>
            <div class="<?php echo esc_attr( $uid ); ?>-hint evn-hint" role="note"><span class="evn-sig" aria-hidden="true">●</span><span><?php echo esc_html( $s['hint_text'] ); ?></span></div>
            <?php endif; ?>
            <?php if ( ! empty( $items ) ) : ?>
            <div class="<?php echo esc_attr( $uid ); ?>-pool" data-evn-pool hidden>
                <?php foreach ( $items as $i => $it ) : ?>
                <button type="button" class="<?php echo esc_attr( $uid ); ?>-m evn-mark evn-<?php echo esc_attr( $it['side'] ); ?>" data-evn-mark data-idx="<?php echo esc_attr( $i ); ?>" data-anchor="<?php echo esc_attr( $it['anchor'] ); ?>" style="top:<?php echo esc_attr( $it['offset'] ); ?>" aria-expanded="false" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: numero nota, 2: titolo nota */ __( 'Apri nota %1$s — %2$s', 'olobuild' ), $it['number'], $it['title'] ) ); ?>"><?php echo esc_html( $it['number'] ); ?></button>
                <div class="<?php echo esc_attr( $uid ); ?>-c evn-card evn-<?php echo esc_attr( $it['side'] ); ?>" data-evn-card data-idx="<?php echo esc_attr( $i ); ?>" role="note">
                    <?php if ( $kicker !== '' ) : ?><div class="evn-k"><?php echo esc_html( $kicker ); ?><?php if ( $it['number'] !== '' ) : ?> · <?php echo esc_html( $it['number'] ); ?><?php endif; ?></div><?php endif; ?>
                    <?php if ( $it['title'] !== '' ) : ?><h5><?php echo esc_html( $it['title'] ); ?></h5><?php endif; ?>
                    <?php if ( $it['text'] !== '' ) : ?><p><?php echo esc_html( $it['text'] ); ?></p><?php endif; ?>
                    <?php if ( $it['before'] !== '' || $it['after'] !== '' ) : ?>
                    <div class="evn-ba"><span class="evn-before"><?php echo esc_html( $it['before'] ); ?></span><span class="evn-arr" aria-hidden="true">→</span><span class="evn-after"><?php echo esc_html( $it['after'] ); ?></span></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <script>
        (function(){
            var root=document.currentScript.previousElementSibling;
            if(!root){return;}
            var toggle=root.querySelector('[data-evn-toggle]');
            if(!toggle){return;}
            var lab=toggle.querySelector('.evn-lab');
            var pool=root.querySelector('[data-evn-pool]');
            var pairs=[];
            if(pool){
                var marks=pool.querySelectorAll('[data-evn-mark]');
                var secs=document.querySelectorAll('.olo-section');
                Array.prototype.forEach.call(marks,function(mark){
                    var idx=mark.getAttribute('data-idx');
                    var card=pool.querySelector('[data-evn-card][data-idx="'+idx+'"]');
                    var sec=null;
                    var anchor=mark.getAttribute('data-anchor');
                    if(anchor){sec=document.getElementById(anchor);}
                    if(!sec){sec=secs[parseInt(idx,10)];}
                    if(!sec){return;}
                    if(getComputedStyle(sec).position==='static'){sec.style.position='relative';}
                    sec.appendChild(mark);
                    if(card){sec.appendChild(card);}
                    if(card){pairs.push({mark:mark,card:card,sec:sec});}
                });
            }
            var open=null;
            function closeCard(){
                if(open){
                    open.card.classList.remove('show');
                    open.mark.classList.remove('open');
                    open.mark.setAttribute('aria-expanded','false');
                    open=null;
                }
            }
            function place(p){
                var top=p.mark.offsetTop;
                var h=p.card.offsetHeight;
                var secH=p.sec.offsetHeight;
                var below=top+44;
                if(below+h+20>secH){
                    if(top-h-10>0){p.card.style.top=(top-h-10)+'px';}
                    else{p.card.style.top=Math.max(10,secH-h-20)+'px';}
                }else{
                    p.card.style.top=below+'px';
                }
            }
            pairs.forEach(function(p){
                p.mark.addEventListener('click',function(e){
                    e.stopPropagation();
                    if(open){
                        if(open.mark===p.mark){closeCard();return;}
                    }
                    closeCard();
                    p.card.classList.add('show');
                    place(p);
                    p.mark.classList.add('open');
                    p.mark.setAttribute('aria-expanded','true');
                    open={mark:p.mark,card:p.card};
                });
            });
            toggle.addEventListener('click',function(){
                var on=document.body.classList.toggle('olo-evo');
                toggle.setAttribute('aria-pressed',on?'true':'false');
                if(lab){
                    var txt=toggle.getAttribute(on?'data-label-active':'data-label');
                    if(txt){lab.textContent=txt;}
                }
                if(!on){closeCard();}
            });
            document.addEventListener('click',function(e){
                if(!open){return;}
                if(e.target.closest('.evn-card')){return;}
                if(e.target.closest('.evn-mark')){return;}
                closeCard();
            });
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
