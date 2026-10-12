<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olobuild_Animatedheading_Tile extends Olobuild_Tile_Base {
    protected $type     = 'animatedheading';
    protected $name     = 'Titolo animato';
    protected $icon     = 'dashicons-editor-textcolor';
    protected $category = 'text';
    protected $defaults = [
        'before_text'     => 'Noi siamo',
        'animated_words'  => "creativi\ninnovativi\nappassionati",
        'after_text'      => '',
        'animation'       => 'typing',
        'tag'             => 'h2',
        'alignment'       => 'center',
        'text_color'      => '',
        'animated_color'  => '',
        'font_size'       => '36',
        'font_size_tablet' => '',
        'font_size_mobile' => '',
        'font_weight'     => '700',
        'typing_speed'    => '100',
        'pause_time'      => '2000',
        'highlight_style' => 'underline',
        'highlight_color' => '',
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

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-ah-' . wp_rand(10000, 99999);

        $aw_raw = is_array( $s['animated_words'] ) ? implode( "\n", $s['animated_words'] ) : (string) $s['animated_words'];
        $words = array_filter(array_map('trim', explode("\n", $aw_raw)));
        if (empty($words)) $words = ['animato'];

        $tag   = in_array($s['tag'], ['h1','h2','h3','h4','h5','h6','p'], true) ? $s['tag'] : 'h2';
        $align = in_array($s['alignment'], ['left','center','right'], true) ? $s['alignment'] : 'center';
        $clr   = $this->safe_color_css($s['text_color']) ?: 'var(--olo-color-text, #374151)';
        $aclr  = $this->safe_color_css($s['animated_color']) ?: 'var(--olo-color-primary, #e1474f)';
        $fs    = absint($s['font_size']) ?: 36;
        $fw    = absint($s['font_weight']) ?: 700;
        $anim  = $s['animation'];
        $speed = absint($s['typing_speed']) ?: 100;
        $pause = absint($s['pause_time']) ?: 2000;
        // La parola animata tiene sempre lo spazio della più LUNGA (.olo-ah-spazio: tutte le parole
        // sovrapposte in una griglia, invisibili, scritte dai ::before così non entrano nel testo del
        // titolo), e quella visibile (.olo-ah-testo) si muove lì dentro, allineata come il titolo. Prima
        // lo spazio seguiva la parola mostrata: una corta saliva nella riga di sopra, una lunga tornava a
        // capo, e a ogni cambio tutta la pagina sotto saliva e scendeva (Teatro Darsena: 115 px).
        $dentro = [ 'left' => 'start', 'center' => 'center', 'right' => 'end' ][ $align ];
        $cursore = 'typing' === $anim;

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, absint() for sizes, in_array() whitelists for enums, fixed literals and the internally generated $uid. ?>
        <style>
        .<?php echo $uid; ?> { text-align: <?php echo $align; ?>; padding: 24px 16px; }
        .<?php echo $uid; ?> .olo-ah-heading { color: <?php echo $clr; ?>; font-size: <?php echo $fs; ?>px; font-weight: <?php echo $fw; ?>; margin: 0; line-height: 1.2; }
        <?php // Sul telefono la misura scelta si riduce da sola (mai oltre quella scelta): 76px
        // portavano «astronauti» fuori dallo schermo. Tablet e telefono possono averne una propria,
        // che vince (le regole per dispositivo vengono dopo). ?>
        @media (max-width:640px) { .<?php echo $uid; ?> .olo-ah-heading { font-size: clamp(<?php echo min( 28, $fs ); ?>px, 9vw, <?php echo $fs; ?>px); } }
        <?php echo $this->css_per_dispositivo( $s, 'font_size', '.' . $uid . ' .olo-ah-heading', function ( $v ) {
            return 'font-size:' . max( 12, min( 200, absint( $v ) ) ) . 'px';
        } ); ?>
        .<?php echo $uid; ?> .olo-ah-word { color: <?php echo $aclr; ?>; display: inline-grid; }
        .<?php echo $uid; ?> .olo-ah-word > span, .<?php echo $uid; ?> .olo-ah-spazio > span { grid-area: 1 / 1; }
        .<?php echo $uid; ?> .olo-ah-spazio { display: inline-grid; visibility: hidden; }
        .<?php echo $uid; ?> .olo-ah-spazio > span::before { content: attr(data-w)<?php echo $cursore ? ' "|"' : ''; ?>; }
        .<?php echo $uid; ?> .olo-ah-testo { justify-self: <?php echo $dentro; ?>; }
        <?php if ($anim === 'typing') : ?>
        .<?php echo $uid; ?> .olo-ah-cursor { animation: olo-blink 0.7s infinite; }
        @keyframes olo-blink { 0%,50%{opacity:1}51%,100%{opacity:0} }
        <?php elseif ($anim === 'rotating' || $anim === 'slide') : ?>
        <?php // la parola entra ed esce scorrendo: la finestra è larga quanto lo spazio riservato ?>
        .<?php echo $uid; ?> .olo-ah-testo { justify-self: stretch; overflow: hidden; text-align: <?php echo $align; ?>; }
        <?php elseif ($anim === 'clip') : ?>
        .<?php echo $uid; ?> .olo-ah-testo { overflow: hidden; white-space: nowrap; border-right: 2px solid currentColor; }
        <?php elseif ($anim === 'fade') : ?>
        .<?php echo $uid; ?> .olo-ah-testo { transition: opacity 0.5s ease; }
        <?php elseif ($anim === 'highlight') : ?>
        <?php
            // Il segno sta sulla parola visibile; lo spazio riservato tiene anche il suo margine.
            $hclr = $this->safe_color_css($s['highlight_color']) ?: 'var(--olo-color-primary, #e1474f)';
            $hstyle = $s['highlight_style'];
            if ($hstyle === 'underline') {
                echo ".{$uid} .olo-ah-testo { border-bottom: 3px solid {$hclr}; }";
            } elseif ($hstyle === 'background') {
                echo ".{$uid} .olo-ah-testo { background: " . Olobuild_Tile_Utils::con_alfa( $hclr, '30' ) . "; padding: 0 8px; border-radius: 4px; }";
                echo ".{$uid} .olo-ah-spazio > span { padding: 0 8px; }";
            } elseif ($hstyle === 'strikethrough') {
                echo ".{$uid} .olo-ah-testo { text-decoration: line-through {$hclr}; }";
            } elseif ($hstyle === 'circle') {
                // «Cerchio» era nel menu senza una riga di CSS: la parola restava nuda.
                // Un ovale tracciato attorno, leggermente inclinato come fatto a mano.
                echo ".{$uid} .olo-ah-testo { position: relative; padding: 0 .2em; margin: 0 .15em; }";
                echo ".{$uid} .olo-ah-spazio > span { padding: 0 .2em; margin: 0 .15em; }";
                // Ai lati in % della parola (un ovale inscritto nel suo riquadro taglierebbe le
                // lettere agli estremi delle parole lunghe), sopra e sotto in em: se la frase va
                // a capo l'ovale non invade la riga di sopra.
                echo ".{$uid} .olo-ah-testo::after { content: ''; position: absolute; left: -5%; right: -5%; top: -.22em; bottom: -.22em; border: 3px solid {$hclr}; border-radius: 50%; transform: rotate(-2deg); pointer-events: none; }";
            }
        ?>
        <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-animatedheading <?php echo esc_attr( $uid ); ?>">
            <<?php echo tag_escape( $tag ); ?> class="olo-ah-heading">
                <?php if (!empty($s['before_text'])) echo esc_html($s['before_text']) . ' '; ?>
                <?php // su una riga sola: uno spazio fra i tag spingerebbe la parola dentro lo spazio riservato ?>
                <span class="olo-ah-word" data-words="<?php echo esc_attr(wp_json_encode($words)); ?>"><span class="olo-ah-spazio" aria-hidden="true"><?php foreach ( $words as $w ) { echo '<span data-w="' . esc_attr( $w ) . '"></span>'; } ?></span><span class="olo-ah-testo"><?php if ( $cursore ) : ?><span class="olo-ah-lettere"><?php echo esc_html($words[0]); ?></span><span class="olo-ah-cursor">|</span><?php else : echo esc_html($words[0]); endif; ?></span></span>
                <?php if (!empty($s['after_text'])) echo ' ' . esc_html($s['after_text']); ?>
            </<?php echo tag_escape( $tag ); ?>>
        </div>
        <script>
        (function(){
            var el = document.querySelector('.<?php echo esc_js( $uid ); ?> .olo-ah-word');
            if(!el) return;
            var words = JSON.parse(el.getAttribute('data-words'));
            if(!words.length) return;
            // Si anima solo la parola visibile: lo spazio lo tiene .olo-ah-spazio, sempre quello
            // della parola più lunga, e il titolo non cambia più forma a ogni parola.
            var tx = el.querySelector('.olo-ah-testo') || el;
            var lettere = el.querySelector('.olo-ah-lettere') || tx;
            // WCAG 2.2.2 / 2.3.3: rispetta prefers-reduced-motion.
            // Se l'utente preferisce ridurre il movimento, resta la prima parola
            // (già scritta) senza avviare alcuna animazione/intervallo.
            // If annidati, niente «&&»: WordPress lo trasforma in &#038; e rompe lo script.
            if(window.matchMedia){
                if(window.matchMedia('(prefers-reduced-motion: reduce)').matches){
                    return;
                }
            }
            var idx = 0;
            var anim = '<?php echo esc_js($anim); ?>';
            var speed = <?php echo (int) $speed; ?>;
            var pauseTime = <?php echo (int) $pause; ?>;

            if(anim === 'typing'){
                var currentText = words[0];
                var isDeleting = false;
                var charIdx = currentText.length;
                function typeStep(){
                    if(isDeleting){
                        charIdx--;
                        lettere.textContent = currentText.substring(0, charIdx);
                        if(charIdx === 0){
                            isDeleting = false;
                            idx = (idx + 1) % words.length;
                            currentText = words[idx];
                            setTimeout(typeStep, speed);
                            return;
                        }
                        setTimeout(typeStep, speed / 2);
                    } else {
                        charIdx++;
                        lettere.textContent = currentText.substring(0, charIdx);
                        if(charIdx === currentText.length){
                            isDeleting = true;
                            setTimeout(typeStep, pauseTime);
                            return;
                        }
                        setTimeout(typeStep, speed);
                    }
                }
                setTimeout(function(){ isDeleting = true; typeStep(); }, pauseTime);
            } else if(anim === 'fade'){
                tx.style.transition = 'opacity 0.5s ease';
                setInterval(function(){
                    tx.style.opacity = '0';
                    setTimeout(function(){
                        idx = (idx + 1) % words.length;
                        tx.textContent = words[idx];
                        tx.style.opacity = '1';
                    }, 500);
                }, pauseTime);
            } else if(anim === 'rotating'){
                // la finestra (.olo-ah-testo, overflow nascosto) è alta e larga quanto lo spazio riservato
                var inner = document.createElement('span');
                inner.style.display = 'block';
                inner.style.transition = 'transform 0.5s cubic-bezier(.4,0,.2,1)';
                inner.textContent = words[0];
                tx.textContent = '';
                tx.appendChild(inner);
                setInterval(function(){
                    inner.style.transform = 'translateY(-100%)';
                    inner.style.opacity = '0';
                    setTimeout(function(){
                        idx = (idx + 1) % words.length;
                        inner.style.transition = 'none';
                        inner.style.transform = 'translateY(100%)';
                        inner.textContent = words[idx];
                        setTimeout(function(){
                            inner.style.transition = 'transform 0.5s cubic-bezier(.4,0,.2,1), opacity 0.5s ease';
                            inner.style.transform = 'translateY(0)';
                            inner.style.opacity = '1';
                        }, 30);
                    }, 500);
                }, pauseTime);
            } else if(anim === 'slide'){
                var inner2 = document.createElement('span');
                inner2.style.display = 'inline-block';
                inner2.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
                inner2.textContent = words[0];
                tx.textContent = '';
                tx.appendChild(inner2);
                setInterval(function(){
                    inner2.style.transform = 'translateX(-110%)';
                    inner2.style.opacity = '0';
                    setTimeout(function(){
                        idx = (idx + 1) % words.length;
                        inner2.style.transition = 'none';
                        inner2.style.transform = 'translateX(110%)';
                        inner2.textContent = words[idx];
                        setTimeout(function(){
                            inner2.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
                            inner2.style.transform = 'translateX(0)';
                            inner2.style.opacity = '1';
                        }, 30);
                    }, 500);
                }, pauseTime);
            } else if(anim === 'clip'){
                // overflow, a capo e cursore stanno nel CSS della tile; qui solo la larghezza
                tx.style.transition = 'width 0.8s cubic-bezier(.4,0,.2,1)';
                tx.style.width = 'auto';
                var fullW = tx.offsetWidth;
                tx.style.width = fullW + 'px';
                setInterval(function(){
                    tx.style.width = '0px';
                    setTimeout(function(){
                        idx = (idx + 1) % words.length;
                        tx.textContent = words[idx];
                        tx.style.transition = 'none';
                        tx.style.width = '0px';
                        var nw = tx.scrollWidth;
                        setTimeout(function(){
                            tx.style.transition = 'width 0.8s cubic-bezier(.4,0,.2,1)';
                            tx.style.width = nw + 'px';
                        }, 30);
                    }, 800);
                }, pauseTime);
            } else if(anim === 'highlight'){
                tx.style.transition = 'opacity 0.4s ease';
                setInterval(function(){
                    tx.style.opacity = '0';
                    setTimeout(function(){
                        idx = (idx + 1) % words.length;
                        tx.textContent = words[idx];
                        tx.style.opacity = '1';
                    }, 400);
                }, pauseTime);
            }
        })();
        </script>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
