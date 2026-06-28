<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Film Reel — reel orizzontale cinematografico di progetti ("Lavori").
 * Estratta pixel-perfect dal blueprint "Clod — Evoluzione v2" (section.reel):
 * scroller drag + rotella + snap, fotogrammi a tre altezze, overlay REC con
 * viewfinder + scanline + timecode (25fps) all'hover, skew dei fotogrammi
 * proporzionale alla velocità di scroll, barra di progresso. Render == Vue
 * (FilmReelTile.vue). Runtime inline scoped per istanza (no '&&'), guard
 * prefers-reduced-motion. Marker effetti mouse: data-olo-wave (titolo),
 * data-olo-tilt-child (scroller), data-olo-cta (primo fotogramma).
 */
class Olo_FilmReel_Tile extends Olo_Tile_Base {

    protected $type     = 'filmreel';
    protected $name     = 'Film Reel';
    protected $icon     = 'dashicons-format-video';
    protected $category = 'media';
    protected $defaults = [
        'title'         => 'Lavori',
        'show_title'    => true,
        'hint_text'     => 'Trascina · rotella · scorri in orizzontale',
        'show_hint'     => true,
        'intro_eyebrow' => 'Selezione · photograph & video',
        'intro_text'    => 'Nove progetti tra industria, retail, ritratto ed eventi. Trascina i tuoi fotogrammi nelle cornici.',
        'show_intro'    => true,
        'items'         => [
            [ 'image' => '', 'media_label' => 'Comifo — still', 'name' => 'Comifo', 'tag' => 'Industriale · Video', 'size' => 'tall', 'link' => '' ],
            [ 'image' => '', 'media_label' => 'Valorizza', 'name' => 'Valorizza', 'tag' => 'Retail · Video', 'size' => 'short', 'link' => '' ],
            [ 'image' => '', 'media_label' => 'Confesercenti', 'name' => 'Confesercenti', 'tag' => 'Istituzionale', 'size' => 'normal', 'link' => '' ],
            [ 'image' => '', 'media_label' => 'Foto tecniche', 'name' => 'Foto tecniche', 'tag' => 'Industria · Foto', 'size' => 'short', 'link' => '' ],
            [ 'image' => '', 'media_label' => 'Wedding', 'name' => 'Wedding', 'tag' => 'Event · Video', 'size' => 'tall', 'link' => '' ],
            [ 'image' => '', 'media_label' => 'Darja Wilson', 'name' => 'Darja Wilson', 'tag' => 'Ritratto', 'size' => 'normal', 'link' => '' ],
            [ 'image' => '', 'media_label' => 'Antibrina', 'name' => 'Antibrina', 'tag' => 'Industriale', 'size' => 'short', 'link' => '' ],
            [ 'image' => '', 'media_label' => 'Industry', 'name' => 'Industry', 'tag' => 'Industria · Foto', 'size' => 'normal', 'link' => '' ],
            [ 'image' => '', 'media_label' => 'Event', 'name' => 'Event', 'tag' => 'Evento · Video', 'size' => 'tall', 'link' => '' ],
        ],
        'rec_overlay'    => true,
        'velocity_skew'  => true,
        'skew_max'       => '7',
        'progress_bar'   => true,
        // 'free' = scroller libero (drag + rotella, blueprint v2) · 'pin' = sezione
        // bloccata a schermo: lo scroll verticale della pagina guida il reel da cima
        // a fondo, non bypassabile. Fallback automatico a 'free' con reduced-motion.
        'scroll_mode'    => 'free',
        'progress_color' => '',
        'accent'         => '',
        'bg_color'       => '',
        'border_color'   => '',

        // Punto focale globale (object-position) di immagini/video nei fotogrammi.
        // Stringa CSS: 'center center' (= resa attuale) oppure es. '34% 23%'.
        'media_object_position' => 'center center',

        // Spaziatura (gated): padding verticale di base clamp(42px,6vw,78px) 0.
        // Override attivo SOLO se pad_custom=true → no-op coi default.
        'pad_custom'     => false,
        'padding'        => [ 'top' => 78, 'right' => 0, 'bottom' => 78, 'left' => 0 ],

        // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
        // Default no-op: bg none / shadow none / border 0 → render invariato.
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
        $uid = 'ofr-' . wp_rand( 10000, 99999 );

        // Cast difensivi (null-safe per esc_html su PHP 8.1+).
        foreach ( [ 'title', 'hint_text', 'intro_eyebrow', 'intro_text' ] as $k ) {
            $s[ $k ] = (string) ( $s[ $k ] ?? '' );
        }

        // ── Colori token-first (parità byte con FilmReelTile.vue) ──
        $accent = $this->safe_color_css( $s['accent'] ?? '' ) ?: 'var(--olo-color-primary, #C6F24E)';
        $bg     = $this->safe_color_css( $s['bg_color'] ?? '' ) ?: 'var(--olo-color-surface-alt, #101218)';
        $line   = $this->safe_color_css( $s['border_color'] ?? '' ) ?: 'var(--olo-color-border, rgba(236,234,227,.10))';
        $line2  = $this->safe_color_css( $s['border_color'] ?? '' ) ?: 'rgba(236,234,227,.20)';
        $prog   = $this->safe_color_css( $s['progress_color'] ?? '' ) ?: $accent;
        $text   = 'var(--olo-color-text, #ECEAE3)';
        $muted  = 'var(--olo-color-text-muted, #a0a298)';
        $itembg = 'var(--olo-color-muted, #161922)';

        $disp = "var(--olo-font-family-heading, 'Big Shoulders Display',sans-serif)";
        $sans = "var(--olo-font-family, 'Hanken Grotesk',sans-serif)";
        $mono = "var(--olo-font-family-mono, 'Space Mono',ui-monospace,monospace)";
        $pad  = 'clamp(20px,5vw,72px)';

        // Punto focale globale (object-position) — '' → 'center center' (= resa attuale).
        $obj_pos = trim( (string) ( $s['media_object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) { $obj_pos = 'center center'; }

        $rec  = ! empty( $s['rec_overlay'] );
        $skew = ! empty( $s['velocity_skew'] );
        $show_prog = ! empty( $s['progress_bar'] );
        $skmax = max( 0, min( 20, floatval( $s['skew_max'] !== '' ? $s['skew_max'] : 7 ) ) );
        $pin  = ( ( $s['scroll_mode'] ?? 'free' ) === 'pin' );

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) return '';

        // ── Spaziatura (gated): default = padding verticale responsivo invariato ──
        $root_pad = 'clamp(42px,6vw,78px) 0';
        if ( ! empty( $s['pad_custom'] ) && is_array( $s['padding'] ?? null ) ) {
            $pv = $s['padding'];
            $pt = intval( $pv['top'] ?? 0 );
            $pr = intval( $pv['right'] ?? 0 );
            $pb = intval( $pv['bottom'] ?? 0 );
            $pl = intval( $pv['left'] ?? 0 );
            $root_pad = "{$pt}px {$pr}px {$pb}px {$pl}px";
        }

        // ── KIT standard: sfondo completo (override del bg di base SOLO se valorizzato) ──
        $bg_block = 'background:' . $bg . ';';
        $bg_obj   = $s['bg'] ?? null;
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
            if ( $bg_decl !== '' ) {
                $bg_block = rtrim( trim( $bg_decl ), ';' ) . ';';
            }
        }

        // ── KIT standard: ombra + bordo (sul contenitore principale .$uid) ──
        $shadow_css        = $this->build_shadow_decl( $s );
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        $kit_decl          = $border_css;
        if ( $shadow_css !== '' ) {
            $kit_decl .= 'box-shadow:' . $shadow_css . ';';
        }

        $region_label = $s['title'] !== '' ? $s['title'] : __( 'Lavori', 'olobuild' );

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every colour via the safe_color_css() whitelist (o fallback var() letterali), padding integer-forced o clamp() letterale, fixed font-stack/clamp literals, background/ombra/bordo via gli helper condivisi Olo_CSS_Builder/Olo_Tile_Base (sanitizzati internamente); $uid è generato internamente. ?>
        <style>
            .<?php echo $uid; ?>{position:relative;color:<?php echo $text; ?>;font-family:<?php echo $sans; ?>;border-top:1px solid <?php echo $line2; ?>;border-bottom:1px solid <?php echo $line2; ?>;padding:<?php echo $root_pad; ?>;overflow:hidden;<?php echo $bg_block; ?><?php echo $kit_decl; ?>}
            .<?php echo $uid; ?> .ofr-bar{display:flex;align-items:baseline;justify-content:space-between;gap:20px;padding:0 <?php echo $pad; ?>;flex-wrap:wrap;}
            .<?php echo $uid; ?> .ofr-title{font-family:<?php echo $disp; ?>;font-weight:800;font-size:clamp(40px,6vw,82px);line-height:.92;letter-spacing:-.01em;text-transform:uppercase;color:<?php echo $text; ?>;margin:0;}
            .<?php echo $uid; ?> .ofr-hint{font-family:<?php echo $mono; ?>;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:<?php echo $muted; ?>;display:inline-flex;align-items:center;gap:9px;}
            .<?php echo $uid; ?> .ofr-hint svg{width:24px;height:13px;color:<?php echo $accent; ?>;flex:none;}
            .<?php echo $uid; ?> .ofr-scroller{overflow-x:auto;overflow-y:hidden;scroll-snap-type:x proximity;-webkit-overflow-scrolling:touch;cursor:grab;scrollbar-width:none;margin-top:8px;}
            <?php if ( $pin ) : ?>
            .<?php echo $uid; ?>-pin{position:relative;}
            .<?php echo $uid; ?>-pin .<?php echo $uid; ?>{position:sticky;top:0;min-height:100vh;display:flex;flex-direction:column;justify-content:center;box-sizing:border-box;}
            .<?php echo $uid; ?>-pin .ofr-scroller{scroll-snap-type:none;cursor:default;}
            <?php endif; ?>
            .<?php echo $uid; ?> .ofr-scroller::-webkit-scrollbar{display:none;}
            .<?php echo $uid; ?> .ofr-scroller.drag{cursor:grabbing;scroll-snap-type:none;}
            .<?php echo $uid; ?> .ofr-scroller.drag *{pointer-events:none;}
            .<?php echo $uid; ?> .ofr-scroller:focus-visible{outline:none;box-shadow:inset 0 0 0 3px color-mix(in srgb, <?php echo $accent; ?> 30%, transparent);}
            .<?php echo $uid; ?> .ofr-track{display:flex;gap:clamp(14px,1.8vw,26px);padding:26px <?php echo $pad; ?>;width:max-content;}
            .<?php echo $uid; ?> .ofr-pcap{flex:0 0 clamp(220px,22vw,300px);display:flex;flex-direction:column;justify-content:center;padding-right:20px;align-self:center;}
            .<?php echo $uid; ?> .ofr-eyebrow{display:block;margin-bottom:16px;font-family:<?php echo $mono; ?>;font-size:12.5px;letter-spacing:.18em;text-transform:uppercase;color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .ofr-pcap p{color:<?php echo $muted; ?>;font-size:15px;line-height:1.6;margin:14px 0 0;max-width:30ch;}
            .<?php echo $uid; ?> .ofr-item{position:relative;flex:0 0 clamp(260px,30vw,420px);height:clamp(320px,56vh,560px);overflow:hidden;border:1px solid <?php echo $line; ?>;background:<?php echo $itembg; ?>;scroll-snap-align:center;display:block;color:<?php echo $text; ?>;text-decoration:none;}
            .<?php echo $uid; ?> .ofr-item.tall{height:clamp(360px,62vh,610px);align-self:flex-start;}
            .<?php echo $uid; ?> .ofr-item.short{height:clamp(260px,46vh,460px);align-self:center;}
            .<?php echo $uid; ?> .ofr-item:focus-visible{outline:none;box-shadow:0 0 0 3px color-mix(in srgb, <?php echo $accent; ?> 30%, transparent);}
            .<?php echo $uid; ?> .ofr-img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:<?php echo esc_attr( $obj_pos ); ?>;background-position:<?php echo esc_attr( $obj_pos ); ?>;display:block;}
            .<?php echo $uid; ?> .ofr-ph{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(236,234,227,.05);}
            .<?php echo $uid; ?> .ofr-ph span{font-family:<?php echo $mono; ?>;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:<?php echo $muted; ?>;text-align:center;padding:0 18px;}
            .<?php echo $uid; ?> .ofr-meta{position:absolute;left:0;right:0;bottom:0;display:flex;align-items:flex-end;justify-content:space-between;gap:10px;padding:14px 15px;background:linear-gradient(transparent,rgba(8,9,12,.82));pointer-events:none;z-index:5;}
            .<?php echo $uid; ?> .ofr-name{font-family:<?php echo $disp; ?>;font-weight:700;font-size:22px;text-transform:uppercase;line-height:1;color:<?php echo $text; ?>;}
            .<?php echo $uid; ?> .ofr-tag{font-family:<?php echo $mono; ?>;font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:<?php echo $accent; ?>;white-space:nowrap;}
            <?php if ( $show_prog ) : ?>
            .<?php echo $uid; ?> .ofr-prog{margin:6px <?php echo $pad; ?> 0;height:2px;background:<?php echo $line; ?>;position:relative;}
            .<?php echo $uid; ?> .ofr-prog i{position:absolute;left:0;top:0;height:100%;width:0;background:<?php echo $prog; ?>;transition:width .08s linear;}
            <?php endif; ?>
            <?php if ( $rec ) : ?>
            .<?php echo $uid; ?> .ofr-rec{position:absolute;inset:0;z-index:4;pointer-events:none;opacity:0;transition:opacity .28s ease;}
            .<?php echo $uid; ?> .ofr-item:hover .ofr-rec{opacity:1;}
            .<?php echo $uid; ?> .ofr-rec::after{content:"";position:absolute;inset:0;background:repeating-linear-gradient(to bottom,rgba(255,255,255,.05) 0 1px,transparent 1px 3px);mix-blend-mode:overlay;}
            .<?php echo $uid; ?> .ofr-vf{position:absolute;inset:12px;}
            .<?php echo $uid; ?> .ofr-vf span{position:absolute;width:15px;height:15px;border:2px solid rgba(255,255,255,.85);}
            .<?php echo $uid; ?> .ofr-vf .tl{left:0;top:0;border-right:0;border-bottom:0;}
            .<?php echo $uid; ?> .ofr-vf .tr{right:0;top:0;border-left:0;border-bottom:0;}
            .<?php echo $uid; ?> .ofr-vf .bl{left:0;bottom:0;border-right:0;border-top:0;}
            .<?php echo $uid; ?> .ofr-vf .br{right:0;bottom:0;border-left:0;border-top:0;}
            .<?php echo $uid; ?> .ofr-recbadge{position:absolute;left:20px;top:20px;display:flex;align-items:center;gap:7px;font-family:<?php echo $mono; ?>;font-size:11px;font-weight:700;letter-spacing:.14em;color:#fff;text-shadow:0 1px 7px rgba(0,0,0,.8);}
            .<?php echo $uid; ?> .ofr-recbadge i{width:9px;height:9px;border-radius:50%;background:<?php echo $accent; ?>;box-shadow:0 0 10px 1px color-mix(in srgb, <?php echo $accent; ?> 85%, transparent);animation:<?php echo $uid; ?>-recblink 1.1s steps(2,end) infinite;}
            @keyframes <?php echo $uid; ?>-recblink{50%{opacity:.18;}}
            .<?php echo $uid; ?> .ofr-tc{position:absolute;right:20px;top:20px;font-family:<?php echo $mono; ?>;font-size:11px;font-weight:700;letter-spacing:.05em;color:#fff;text-shadow:0 1px 7px rgba(0,0,0,.8);font-variant-numeric:tabular-nums;}
            @media(prefers-reduced-motion:reduce){.<?php echo $uid; ?> .ofr-recbadge i{animation:none;}}
            <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php if ( $pin ) : ?><div class="<?php echo esc_attr( $uid ); ?>-pin" data-ofr-pin><?php endif; ?>
        <section class="olo-filmreel <?php echo esc_attr( $uid ); ?>">
            <?php if ( ! empty( $s['show_title'] ) || ! empty( $s['show_hint'] ) ) : ?>
            <div class="ofr-bar">
                <?php if ( ! empty( $s['show_title'] ) ) : ?><h2 class="ofr-title" data-olo-wave data-olo-editable="title"><?php echo esc_html( $s['title'] ); ?></h2><?php endif; ?>
                <?php if ( ! empty( $s['show_hint'] ) && $s['hint_text'] !== '' ) : ?><span class="ofr-hint"><span data-olo-editable="hint_text"><?php echo esc_html( $s['hint_text'] ); ?></span> <svg viewBox="0 0 40 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 7h34M30 2l6 5-6 5"/></svg></span><?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="ofr-scroller" data-ofr-scroller data-olo-tilt-child role="region" aria-label="<?php echo esc_attr( $region_label ); ?>" tabindex="0">
                <div class="ofr-track">
                    <?php if ( ! empty( $s['show_intro'] ) ) : ?>
                    <div class="ofr-pcap">
                        <?php if ( $s['intro_eyebrow'] !== '' ) : ?><span class="ofr-eyebrow" data-olo-editable="intro_eyebrow"><?php echo esc_html( $s['intro_eyebrow'] ); ?></span><?php endif; ?>
                        <?php if ( $s['intro_text'] !== '' ) : ?><p data-olo-editable="intro_text"><?php echo esc_html( $s['intro_text'] ); ?></p><?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php foreach ( $items as $idx => $it ) :
                        $img   = isset( $it['image'] ) ? trim( (string) $it['image'] ) : '';
                        $label = isset( $it['media_label'] ) ? (string) $it['media_label'] : '';
                        $name  = isset( $it['name'] ) ? (string) $it['name'] : '';
                        $tag   = isset( $it['tag'] ) ? (string) $it['tag'] : '';
                        $size  = isset( $it['size'] ) ? (string) $it['size'] : 'normal';
                        $cls   = in_array( $size, [ 'tall', 'short' ], true ) ? ' ' . $size : '';
                        $link  = isset( $it['link'] ) ? trim( (string) $it['link'] ) : '';
                        $tag_el = $link !== '' ? 'a' : 'article';
                        $cta    = $idx === 0 ? ' data-olo-cta' : '';
                        // Media del fotogramma: media_bg (video/immagine/colore/gradiente) ha
                        // precedenza; fallback al campo image legacy, poi al placeholder.
                        $mbg      = $it['media_bg'] ?? null;
                        $mbg_kind = 'none';
                        if ( is_array( $mbg ) && ! empty( $mbg['type'] ) && $mbg['type'] !== 'none' ) {
                            if ( $mbg['type'] === 'video' ) {
                                $mbg_kind = trim( (string) ( $mbg['video_url'] ?? '' ) ) !== '' ? 'video' : 'none';
                            } else {
                                $mbg_kind = 'bg';
                            }
                        }
                    ?>
                    <<?php echo tag_escape( $tag_el ); ?> class="ofr-item<?php echo esc_attr( $cls ); ?>"<?php if ( $link !== '' ) : ?> href="<?php echo esc_url( $link ); ?>"<?php endif; ?><?php echo $cta; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $cta is a fixed internal literal (' data-olo-cta' or '') ?>>
                        <?php if ( $mbg_kind === 'video' ) : ?>
                            <video class="ofr-img" src="<?php echo esc_url( $mbg['video_url'] ); ?>"<?php echo ! empty( $mbg['video_poster'] ) ? ' poster="' . esc_url( $mbg['video_poster'] ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url() inline above ?> autoplay muted loop playsinline aria-hidden="true"></video>
                        <?php elseif ( $mbg_kind === 'bg' && class_exists( 'Olo_CSS_Builder' ) ) : ?>
                            <span class="ofr-img" style="<?php echo esc_attr( ( new Olo_CSS_Builder() )->get_bg_inline_css( $mbg ) ); ?>"></span>
                        <?php elseif ( $img !== '' ) : ?>
                            <img class="ofr-img" src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy" />
                        <?php else : ?>
                            <span class="ofr-ph"><?php if ( $label !== '' ) : ?><span data-olo-editable="<?php echo esc_attr( 'items.' . intval( $idx ) . '.media_label' ); ?>"><?php echo esc_html( $label ); ?></span><?php endif; ?></span>
                        <?php endif; ?>
                        <?php if ( $rec ) : ?>
                        <span class="ofr-rec" aria-hidden="true">
                            <span class="ofr-vf"><span class="tl"></span><span class="tr"></span><span class="bl"></span><span class="br"></span></span>
                            <span class="ofr-recbadge"><i></i>REC</span>
                            <span class="ofr-tc" data-ofr-tc>00:00:00</span>
                        </span>
                        <?php endif; ?>
                        <?php if ( $name !== '' || $tag !== '' ) : ?>
                        <span class="ofr-meta">
                            <?php if ( $name !== '' ) : ?><span class="ofr-name" data-olo-editable="<?php echo esc_attr( 'items.' . intval( $idx ) . '.name' ); ?>"><?php echo esc_html( $name ); ?></span><?php endif; ?>
                            <?php if ( $tag !== '' ) : ?><span class="ofr-tag" data-olo-editable="<?php echo esc_attr( 'items.' . intval( $idx ) . '.tag' ); ?>"><?php echo esc_html( $tag ); ?></span><?php endif; ?>
                        </span>
                        <?php endif; ?>
                    </<?php echo tag_escape( $tag_el ); ?>>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if ( $show_prog ) : ?><div class="ofr-prog"><i data-ofr-prog></i></div><?php endif; ?>
        </section>
        <?php if ( $pin ) : ?></div><?php endif; ?>
        <script>
        (function(){
            var prev=document.currentScript.previousElementSibling;
            if(!prev){return;}
            var pinWrap=(prev.hasAttribute&&prev.hasAttribute('data-ofr-pin'))?prev:null;
            var root=pinWrap?pinWrap.firstElementChild:prev;
            if(!root){return;}
            var sc=root.querySelector('[data-ofr-scroller]');
            if(!sc){return;}
            var reduce=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var prog=root.querySelector('[data-ofr-prog]');
            function updateProg(){
                if(!prog){return;}
                var max=sc.scrollWidth-sc.clientWidth;
                var w=0;
                if(max>0){ w=sc.scrollLeft/max*100; }
                prog.style.width=w.toFixed(1)+'%';
            }
            sc.addEventListener('scroll',updateProg,{passive:true});
            updateProg();
            if(pinWrap){
                /* PIN: la sezione resta a schermo (sticky) e lo scroll verticale della
                   pagina guida il reel da cima a fondo — deciso e non bypassabile.
                   Il wrapper è alto 100vh + corsa orizzontale: per superare la sezione
                   bisogna attraversare tutto il reel. Reduced-motion: lo scroller resta
                   scrollabile nativamente (touch/scrollbar), nessun hijack aggiuntivo. */
                var ptk=false,useFix=false;
                function pinLayout(){
                    var max=sc.scrollWidth-sc.clientWidth;
                    /* Altezza wrapper = altezza REALE della sezione + corsa orizzontale.
                       Con 100vh fisso una sezione più alta del viewport accorciava la corsa
                       dello sticky → il pin si sganciava prima della fine del reel. */
                    pinWrap.style.height=(root.offsetHeight+max)+'px';
                }
                function pinScroll(){
                    ptk=false;
                    var r=pinWrap.getBoundingClientRect();
                    var max=sc.scrollWidth-sc.clientWidth;
                    var p=Math.max(0,Math.min(max,-r.top));
                    sc.scrollLeft=p;
                    /* Fallback: se position:sticky non aggancia (overflow/transform su un
                       antenato), la section scorrerebbe via lasciando il wrapper vuoto.
                       Rilevato lo scarto, compensiamo via translateY → stesso effetto pin. */
                    if(p>0&&p<max){
                        if(!useFix){
                            var rt=root.getBoundingClientRect().top;
                            if(Math.abs(rt)>2){ useFix=true; }
                        }
                        if(useFix){ root.style.transform='translateY('+p+'px)'; }
                    }else if(useFix){
                        root.style.transform=(max>0&&p>=max)?'translateY('+max+'px)':'';
                    }
                }
                if(!reduce){
                    window.addEventListener('scroll',function(){ if(!ptk){ ptk=true; requestAnimationFrame(pinScroll); } },{passive:true});
                    window.addEventListener('resize',function(){ pinLayout(); pinScroll(); });
                    window.addEventListener('load',function(){ pinLayout(); pinScroll(); });
                    if(window.ResizeObserver){
                        new ResizeObserver(function(){ pinLayout(); }).observe(root);
                    }
                    pinLayout(); pinScroll();
                }
            }else{
                var snapT=0;
                sc.addEventListener('wheel',function(e){
                    if(Math.abs(e.deltaY)<=Math.abs(e.deltaX)){return;}
                    var max=sc.scrollWidth-sc.clientWidth;
                    if(e.deltaY<0){ if(sc.scrollLeft<=0){return;} }
                    if(e.deltaY>0){ if(sc.scrollLeft>=max-1){return;} }
                    e.preventDefault();
                    /* Snap spento durante la rotella (come nel drag): senza questo lo
                       scroll-snap proximity "tira indietro" ad ogni tacca → tentennamento. */
                    sc.style.scrollSnapType='none';
                    clearTimeout(snapT);
                    snapT=setTimeout(function(){ sc.style.scrollSnapType=''; },160);
                    sc.scrollLeft+=e.deltaY;
                },{passive:false});
                var down=false,sx=0,sl=0,moved=0;
                sc.addEventListener('pointerdown',function(e){ down=true;moved=0;sx=e.clientX;sl=sc.scrollLeft;sc.classList.add('drag'); });
                window.addEventListener('pointermove',function(e){
                    if(!down){return;}
                    var dx=e.clientX-sx;
                    moved=Math.max(moved,Math.abs(dx));
                    sc.scrollLeft=sl-dx;
                });
                window.addEventListener('pointerup',function(){
                    if(!down){return;}
                    down=false;
                    sc.classList.remove('drag');
                });
                sc.addEventListener('click',function(e){ if(moved>6){ e.preventDefault(); } },true);
            }
            <?php if ( $skew ) : ?>
            if(!reduce){
                var skmax=<?php echo esc_js( (string) $skmax ); ?>;
                var last=sc.scrollLeft,sk=0,raf=0;
                function clampv(v,a,b){ return Math.max(a,Math.min(b,v)); }
                function loop(){
                    var its=sc.querySelectorAll('.ofr-item');
                    var v=sc.scrollLeft-last; last=sc.scrollLeft;
                    sk+=(clampv(v*0.18,-skmax,skmax)-sk)*0.14;
                    if(Math.abs(sk)<0.04){ if(v===0){ for(var i=0;i<its.length;i++){ its[i].style.transform=''; } raf=0; return; } }
                    for(var j=0;j<its.length;j++){ its[j].style.transform='skewX('+(-sk).toFixed(2)+'deg)'; }
                    raf=requestAnimationFrame(loop);
                }
                sc.addEventListener('scroll',function(){ if(!raf){ raf=requestAnimationFrame(loop); } },{passive:true});
            }
            <?php endif; ?>
            <?php if ( $rec ) : ?>
            (function(){
                var its=[].slice.call(root.querySelectorAll('.ofr-item'));
                function pad(n){ return (n<10?'0':'')+n; }
                its.forEach(function(it){
                    var tc=it.querySelector('[data-ofr-tc]');
                    if(!tc){return;}
                    var raf=0,start=0;
                    function tick(now){
                        var el=(now-start)/1000;
                        tc.textContent=pad(Math.floor(el/60))+':'+pad(Math.floor(el%60))+':'+pad(Math.floor((el*25)%25));
                        raf=requestAnimationFrame(tick);
                    }
                    it.addEventListener('pointerenter',function(){
                        if(reduce){return;}
                        start=performance.now();
                        cancelAnimationFrame(raf);
                        raf=requestAnimationFrame(tick);
                    });
                    it.addEventListener('pointerleave',function(){ cancelAnimationFrame(raf); });
                });
            })();
            <?php endif; ?>
        })();
        </script>
        <?php
        // ── Sistema bordi standard: hover + effetto (come categoryrail) ──
        if ( $border_hover_css || $border_effect_css ) {
            echo '<style>' . $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none.
     * Copiato dal pattern standard OLObuild (cfr. Olo_CategoryRail_Tile).
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
