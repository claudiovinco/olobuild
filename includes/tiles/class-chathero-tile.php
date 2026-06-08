<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Chat (Glow + Conversation Cards) : hero SaaS centrato su fondo scuro con
 * GLOW radiale sfocato dietro un eyebrow PILL, un H1 multi-riga (parola finale a gradiente
 * accento), sub e fino a 2 CTA — seguito SOTTO da una finestra "workspace" stile chat con
 * barra finestrata e una pila di BOLLE messaggio (you / ai) ripetibili, ognuna con testo.
 * Meccanica firma = glow radiale + le card-conversazione impilate. Nessun JS (pure CSS).
 * Render == Vue (ChatHeroTile.vue). Default fedeli al blueprint OLOthemes "Synapse".
 */
class Olo_ChatHero_Tile extends Olo_Tile_Base {

    protected $type     = 'chathero';
    protected $name     = 'Hero — Chat (Glow + Conversation Cards)';
    protected $icon     = 'dashicons-format-chat';
    protected $category = 'marketing';
    protected $defaults = [
        // Eyebrow pill
        'pill_text'      => 'Synapse 3 · now with long-term memory',
        'pill_dot'       => true,
        // Titolo: testo base + parola/coda a gradiente accento
        'headline_text'  => 'The AI workspace that ',
        'accent_text'    => 'remembers.',
        'subhead'        => "Chat, agents and your company's knowledge in one place — grounded in your docs, your data and every conversation you've had before.",
        'cta1_text'      => 'Try free',
        'cta1_url'       => '#pricing',
        'cta2_text'      => 'See how it works',
        'cta2_url'       => '#features',
        // Finestra chat
        'chat_enabled'   => true,
        'chat_label'     => 'synapse · workspace',
        'messages'       => [
            [ 'side' => 'you', 'text' => "Summarise last week's customer calls and flag anything about pricing." ],
            [ 'side' => 'ai',  'text' => 'Across 9 calls: 3 flagged pricing — two want annual billing, one found the Team tier "a jump". Drafted a follow-up for each. Want me to send?' ],
            [ 'side' => 'you', 'text' => 'Yes, and add them to the CRM.' ],
            [ 'side' => 'ai',  'text' => '…' ],
        ],
        // Aspetto / colori
        'bg_color'       => '#140e22',
        'panel_color'    => '#1e1633',
        'panel2_color'   => '#271d42',
        'accent'         => '',
        'accent2'        => '',
        'accent_on'      => '#ffffff',
        'text_color'     => '#ffffff',
        'sub_color'      => '#776e92',
        'msg_text_color' => '#b3a9cc',
        'pill_color'     => '',
        // Glow radiale
        'glow_color'     => 'rgba(160,107,255,0.3)',
        'glow_w'         => 820,
        'glow_h'         => 560,
        'glow_blur'      => 110,
        'glow_x'         => 50,
        'glow_y'         => -220,
        // Layout
        'h_size_min'     => 40,
        'h_size_vw'      => 6.6,
        'h_size_max'     => 82,
        'max_width'      => 840,
        'chat_max_width' => 760,
        // Spaziatura / Raggio (additivi, default = resa attuale invariata)
        'content_padding' => [ 'top' => 0, 'right' => 28, 'bottom' => 0, 'left' => 28 ],
        'chat_radius'     => [ 'tl' => 16, 'tr' => 16, 'br' => 0, 'bl' => 0 ],
        // KIT standard OLObuild — sfondo completo + ombra + bordo (default no-op)
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
        $uid = 'ocht-' . wp_rand( 10000, 99999 );

        $bg     = $this->safe_color_css( $s['bg_color'] ) ?: '#140e22';
        $panel  = $this->safe_color_css( $s['panel_color'] ?? '' ) ?: '#1e1633';
        $panel2 = $this->safe_color_css( $s['panel2_color'] ?? '' ) ?: '#271d42';
        $accent = $this->safe_color_css( $s['accent'] ) ?: 'var(--olo-color-primary, #a06bff)';
        $acc2   = $this->safe_color_css( $s['accent2'] ?? '' ) ?: 'var(--olo-color-secondary, #ff7ad1)';
        $accOn  = $this->safe_color_css( $s['accent_on'] ?? '' ) ?: '#ffffff';
        $txt    = $this->safe_color_css( $s['text_color'] ?? '' ) ?: '#ffffff';
        $sub    = $this->safe_color_css( $s['sub_color'] ?? '' ) ?: '#776e92';
        $msgtxt = $this->safe_color_css( $s['msg_text_color'] ?? '' ) ?: '#b3a9cc';
        $pillc  = $this->safe_color_css( $s['pill_color'] ?? '' ) ?: $accent;
        $glow   = $this->safe_color_css( $s['glow_color'] ?? '' ) ?: 'rgba(160,107,255,0.3)';

        $gw     = max( 100, intval( $s['glow_w'] ) );
        $gh     = max( 100, intval( $s['glow_h'] ) );
        $gblur  = max( 0, intval( $s['glow_blur'] ) );
        $gx     = max( 0, min( 100, intval( $s['glow_x'] ) ) );
        $gy     = intval( $s['glow_y'] );

        $hmin   = max( 20, intval( $s['h_size_min'] ) );
        $hvw    = max( 1, floatval( $s['h_size_vw'] ) );
        $hmax   = max( $hmin, intval( $s['h_size_max'] ) );
        $mw     = max( 480, intval( $s['max_width'] ) );
        $cmw    = max( 360, intval( $s['chat_max_width'] ) );
        $dot    = ! empty( $s['pill_dot'] );
        $chat   = ! empty( $s['chat_enabled'] );

        // Spaziatura contenuto interno (.cht-in) — oggetto {top,right,bottom,left} in px.
        // Default {0,28,0,28}: si emette LA STRINGA ORIGINALE '0 28px' (byte-identica),
        // altrimenti la forma a 4 valori dal field.
        $cp    = is_array( $s['content_padding'] ?? null ) ? $s['content_padding'] : [];
        $cp_t  = intval( $cp['top']    ?? 0 );
        $cp_r  = intval( $cp['right']  ?? 28 );
        $cp_b  = intval( $cp['bottom'] ?? 0 );
        $cp_l  = intval( $cp['left']   ?? 28 );
        if ( $cp_t === 0 && $cp_r === 28 && $cp_b === 0 && $cp_l === 28 ) {
            $in_pad = '0 28px';
        } else {
            $in_pad = "{$cp_t}px {$cp_r}px {$cp_b}px {$cp_l}px";
        }

        // Raggio finestra chat — oggetto {tl,tr,br,bl}.
        // Default {16,16,0,0}: si emette LA STRINGA ORIGINALE '16px 16px 0 0' (byte-identica),
        // altrimenti la forma generata da build_border_radius_css.
        $cr      = is_array( $s['chat_radius'] ?? null ) ? $s['chat_radius'] : [];
        $cr_tl   = intval( $cr['tl'] ?? 16 );
        $cr_tr   = intval( $cr['tr'] ?? 16 );
        $cr_br   = intval( $cr['br'] ?? 0 );
        $cr_bl   = intval( $cr['bl'] ?? 0 );
        if ( $cr_tl === 16 && $cr_tr === 16 && $cr_br === 0 && $cr_bl === 0 ) {
            $chat_radius = '16px 16px 0 0';
        } else {
            $cr_val      = $this->build_border_radius_css( [ 'tl' => $cr_tl, 'tr' => $cr_tr, 'br' => $cr_br, 'bl' => $cr_bl ] );
            // build_border_radius_css() restituisce '' quando tutti i 4 angoli sono 0:
            // in quel caso l'utente ha scelto ESPLICITAMENTE spigoli vivi (non è il
            // default {16,16,0,0}, già intercettato sopra) → emettiamo '0px 0px 0px 0px'
            // per parità byte-per-byte con il render Vue (chatRadius).
            $chat_radius = ( $cr_val !== '' ) ? $cr_val : '0px 0px 0px 0px';
        }

        $disp   = "var(--olo-font-family-heading, 'Instrument Sans',-apple-system,sans-serif)";
        $sans   = "var(--olo-font-family, 'Instrument Sans',-apple-system,sans-serif)";
        $mono   = "var(--olo-font-family-mono, 'Space Mono',ui-monospace,Menlo,monospace)";

        $msgs = is_array( $s['messages'] ?? null ) ? $s['messages'] : [];

        // KIT standard OLObuild ─────────────────────────────────────────────
        // Sfondo completo: override del bg attuale SOLO se valorizzato (default none = invariato).
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // Ombra (preset sm/md/lg/xl o custom). '' se none.
        $shadow_css = $this->build_shadow_decl( $s );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden;background:<?php echo $bg; ?>;color:<?php echo $txt; ?>;font-family:<?php echo $sans; ?>;padding:clamp(56px,8vw,104px) 0 0;text-align:center;<?php if ( $bg_decl ) { echo $bg_decl . ';'; } ?><?php if ( $shadow_css ) { echo 'box-shadow:' . $shadow_css . ';'; } ?>}
            .<?php echo $uid; ?> .cht-glow{position:absolute;top:<?php echo $gy; ?>px;left:<?php echo $gx; ?>%;transform:translateX(-50%);width:<?php echo $gw; ?>px;height:<?php echo $gh; ?>px;border-radius:50%;filter:blur(<?php echo $gblur; ?>px);pointer-events:none;background:radial-gradient(circle, <?php echo $glow; ?>, transparent 70%);z-index:0;}
            .<?php echo $uid; ?> .cht-in{position:relative;z-index:2;max-width:<?php echo $mw; ?>px;margin:0 auto;padding:<?php echo $in_pad; ?>;}
            .<?php echo $uid; ?> .cht-pill{display:inline-flex;align-items:center;gap:9px;padding:6px 14px;border-radius:999px;background:rgba(160,107,255,.12);border:1px solid rgba(160,107,255,.4);font-family:<?php echo $mono; ?>;font-size:12px;color:<?php echo $pillc; ?>;margin-bottom:24px;}
            <?php if ( $dot ) : ?>.<?php echo $uid; ?> .cht-pill::before{content:"";width:7px;height:7px;border-radius:50%;background:<?php echo $accent; ?>;box-shadow:0 0 10px <?php echo $accent; ?>;}<?php endif; ?>
            .<?php echo $uid; ?> .cht-h{font-family:<?php echo $disp; ?>;font-weight:700;font-size:clamp(<?php echo $hmin; ?>px,<?php echo $hvw; ?>vw,<?php echo $hmax; ?>px);line-height:1;letter-spacing:-.01em;color:<?php echo $txt; ?>;margin:0;}
            .<?php echo $uid; ?> .cht-h .grad{background:linear-gradient(110deg, <?php echo $accent; ?>, <?php echo $acc2; ?>);-webkit-background-clip:text;background-clip:text;color:transparent;}
            .<?php echo $uid; ?> .cht-sub{font-size:18px;line-height:1.6;color:<?php echo $sub; ?>;max-width:560px;margin:24px auto 30px;}
            .<?php echo $uid; ?> .cht-cta{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
            .<?php echo $uid; ?> .cht-btn{display:inline-flex;align-items:center;gap:8px;padding:15px 28px;border-radius:9px;font-family:<?php echo $sans; ?>;font-weight:600;font-size:15px;text-decoration:none;cursor:pointer;border:0;transition:transform .15s,background .2s,box-shadow .2s,filter .2s;}
            .<?php echo $uid; ?> .cht-btn svg{width:16px;height:16px;}
            .<?php echo $uid; ?> .cht-btn:hover{transform:translateY(-2px);}
            .<?php echo $uid; ?> .cht-btn--solid{background:<?php echo $accent; ?>;color:<?php echo $accOn; ?>;box-shadow:0 10px 28px -10px <?php echo $glow; ?>;}
            .<?php echo $uid; ?> .cht-btn--solid:hover{filter:brightness(1.06);}
            .<?php echo $uid; ?> .cht-btn--ghost{background:rgba(255,255,255,.05);color:#fff;border:1px solid rgba(255,255,255,.16);}
            .<?php echo $uid; ?> .cht-btn--ghost:hover{border-color:rgba(160,107,255,.4);}
            .<?php echo $uid; ?> .cht-btn:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> .cht-chatwrap{position:relative;z-index:2;max-width:1180px;margin:0 auto;padding:0 28px;}
            .<?php echo $uid; ?> .cht-chat{position:relative;z-index:2;max-width:<?php echo $cmw; ?>px;margin:clamp(44px,7vw,76px) auto 0;border:1px solid rgba(255,255,255,.08);border-radius:<?php echo $chat_radius; ?>;background:<?php echo $panel; ?>;overflow:hidden;box-shadow:0 -10px 90px -24px <?php echo $glow; ?>;text-align:left;}
            .<?php echo $uid; ?> .cht-bar{display:flex;align-items:center;gap:7px;padding:13px 16px;border-bottom:1px solid rgba(255,255,255,.08);background:<?php echo $panel2; ?>;}
            .<?php echo $uid; ?> .cht-bar i{width:11px;height:11px;border-radius:50%;background:rgba(255,255,255,.16);}
            .<?php echo $uid; ?> .cht-bar .u{margin-left:12px;font-family:<?php echo $mono; ?>;font-size:11px;color:<?php echo $sub; ?>;}
            .<?php echo $uid; ?> .cht-body{padding:22px;display:flex;flex-direction:column;gap:16px;}
            .<?php echo $uid; ?> .cht-msg{max-width:80%;padding:13px 16px;border-radius:14px;font-size:14.5px;line-height:1.5;}
            .<?php echo $uid; ?> .cht-msg.you{align-self:flex-end;background:<?php echo $accent; ?>;color:#fff;border-bottom-right-radius:4px;}
            .<?php echo $uid; ?> .cht-msg.ai{align-self:flex-start;background:<?php echo $panel2; ?>;color:<?php echo $msgtxt; ?>;border:1px solid rgba(255,255,255,.08);border-bottom-left-radius:4px;}
            @media(max-width:680px){.<?php echo $uid; ?> .cht-msg{max-width:92%;}}
        </style>
        <section class="olo-chathero <?php echo esc_attr( $uid ); ?>">
            <span class="cht-glow"></span>
            <div class="cht-in">
                <?php if ( ! empty( $s['pill_text'] ) ) : ?><span class="cht-pill"><?php echo esc_html( $s['pill_text'] ); ?></span><?php endif; ?>
                <h1 class="cht-h"><?php echo esc_html( $s['headline_text'] ); ?><?php if ( ! empty( $s['accent_text'] ) ) : ?><span class="grad"><?php echo esc_html( $s['accent_text'] ); ?></span><?php endif; ?></h1>
                <?php if ( ! empty( $s['subhead'] ) ) : ?><p class="cht-sub"><?php echo esc_html( $s['subhead'] ); ?></p><?php endif; ?>
                <?php if ( ! empty( $s['cta1_text'] ) || ! empty( $s['cta2_text'] ) ) : ?>
                <div class="cht-cta">
                    <?php if ( ! empty( $s['cta1_text'] ) ) : ?><a class="cht-btn cht-btn--solid" href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta1_text'] ); ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><?php endif; ?>
                    <?php if ( ! empty( $s['cta2_text'] ) ) : ?><a class="cht-btn cht-btn--ghost" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php if ( $chat ) : ?>
            <div class="cht-chatwrap">
                <div class="cht-chat">
                    <div class="cht-bar"><i></i><i></i><i></i><?php if ( ! empty( $s['chat_label'] ) ) : ?><span class="u"><?php echo esc_html( $s['chat_label'] ); ?></span><?php endif; ?></div>
                    <div class="cht-body">
                        <?php foreach ( $msgs as $m ) {
                            $mtext = isset( $m['text'] ) ? (string) $m['text'] : '';
                            if ( $mtext === '' ) { continue; }
                            $side = ( isset( $m['side'] ) && $m['side'] === 'you' ) ? 'you' : 'ai';
                            echo '<div class="cht-msg ' . esc_attr( $side ) . '">' . esc_html( $mtext ) . '</div>';
                        } ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </section>
        <?php
        // ── Sistema bordi standard (KIT OLObuild) ─────────────────────────
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) {
                echo ".{$uid}{{$border_css}}";
            }
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none.
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
