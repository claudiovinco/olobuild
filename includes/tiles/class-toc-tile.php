<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Toc_Tile extends Olo_Tile_Base {
    protected $type     = 'toc';
    protected $name     = 'Indice contenuti';
    protected $icon     = 'dashicons-list-view';
    protected $category = 'navigation';
    protected $defaults = [
        'preset' => 'custom',
        'title'            => 'Sommario',
        'max_depth'        => '3',
        'list_style'       => 'numbered',
        'text_color'       => '',
        'link_color'       => '',
        'title_color'      => '',
        'font_size'        => '15',
        'indent'           => '20',
        'sticky'           => false,
        'highlight_active' => true,
        'smooth_scroll'    => true,
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
        $uid = 'olo-toc-' . wp_rand(10000, 99999);

        $max_depth  = absint($s['max_depth']) ?: 3;
        $list_style = $s['list_style'];
        $link_clr   = $this->safe_color_css($s['link_color']) ?: 'var(--olo-color-primary, #e1474f)';
        $title_clr  = $this->safe_color_css($s['title_color']) ?: 'var(--olo-color-text, #374151)';
        $text_clr   = $this->safe_color_css($s['text_color']) ?: 'var(--olo-color-text, #374151)';
        $font_size  = absint($s['font_size']) ?: 15;
        $indent     = absint($s['indent']) ?: 20;
        $sticky     = !empty($s['sticky']);
        $smooth     = !empty($s['smooth_scroll']);
        $highlight  = !empty($s['highlight_active']);

        $tags = [];
        for ($i = 1; $i <= $max_depth; $i++) $tags[] = "h{$i}";
        $tag_selector = implode(',', $tags);

        ob_start();
        ?>
        <style>
        .<?php echo $uid; ?> { padding: 16px; border-radius: 8px; <?php if ($sticky) echo 'position: sticky; top: 20px; z-index: 10;'; ?> }
        .<?php echo $uid; ?> .olo-toc-title { font-weight: 700; font-size: 16px; color: <?php echo $title_clr; ?>; margin-bottom: 12px; border-bottom: 1px solid var(--olo-color-border, #E5E7EB); padding-bottom: 8px; }
        .<?php echo $uid; ?> .olo-toc-item { margin-bottom: 6px; }
        .<?php echo $uid; ?> .olo-toc-item a { color: <?php echo $link_clr; ?>; text-decoration: none; font-size: <?php echo $font_size; ?>px; transition: color 0.2s; }
        .<?php echo $uid; ?> .olo-toc-item a:hover { text-decoration: underline; }
        .<?php echo $uid; ?> .olo-toc-item a:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); border-radius: 3px; }
        <?php if ($highlight) : ?>
        .<?php echo $uid; ?> .olo-toc-item a.olo-toc-active { font-weight: 700; }
        <?php endif; ?>
        </style>
        <nav class="olo-toc <?php echo $uid; ?> olo-toc-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" id="<?php echo $uid; ?>">
            <?php if (!empty($s['title'])) : ?>
                <?php list( $tt_cls, $tt_data ) = $this->tfx_attrs( $s, 'title', $s['title'] ); ?>
                <div class="olo-toc-title<?php echo $tt_cls; ?>"<?php echo $tt_data; ?>><?php echo esc_html($s['title']); ?></div>
            <?php endif; ?>
            <div class="olo-toc-list" data-tags="<?php echo esc_attr($tag_selector); ?>" data-indent="<?php echo $indent; ?>" data-list-style="<?php echo esc_attr($list_style); ?>">
                <p style="font-size:13px;color:var(--olo-color-text-muted, #9CA3AF);font-style:italic;"><?php echo esc_html( olo_t( 'Caricamento indice...' ) ); ?></p>
            </div>
        </nav>
        <script>
        (function(){
            var uid = '<?php echo $uid; ?>';
            var smooth = <?php echo $smooth ? 'true' : 'false'; ?>;
            var doHighlight = <?php echo $highlight ? 'true' : 'false'; ?>;
            var linkClr = '<?php echo $link_clr; ?>';
            var isSticky = <?php echo $sticky ? 'true' : 'false'; ?>;

            // Fix sticky: push position:sticky up to the inner-column parent
            if(isSticky){
                var nav = document.getElementById(uid);
                if(nav){
                    var p = nav.closest('.olo-inner-column');
                    if(p){
                        p.style.position = 'sticky';
                        p.style.top = '20px';
                        p.style.alignSelf = 'flex-start';
                        nav.style.position = 'static';
                    }
                }
            }

            function buildToc(){
                var container = document.getElementById(uid);
                if(!container) return false;
                var listEl = container.querySelector('.olo-toc-list');
                if(!listEl) return false;
                var tags = listEl.getAttribute('data-tags').split(',');
                var indent = parseInt(listEl.getAttribute('data-indent')) || 20;
                var listStyle = listEl.getAttribute('data-list-style');

                // Collect headings from all .olo-template elements, excluding those inside the TOC itself
                var sel = tags.map(function(t){ return '.olo-template ' + t; }).join(', ');
                var allH = document.querySelectorAll(sel);
                var headings = [];
                for(var i = 0; i < allH.length; i++){
                    if(!container.contains(allH[i])){
                        headings.push(allH[i]);
                    }
                }

                if(!headings.length) return false;

                var html = '';
                var counter = [0,0,0,0,0,0];
                headings.forEach(function(h){
                    var level = parseInt(h.tagName.charAt(1)) - 1;
                    if(!h.id) h.id = 'olo-heading-' + Math.random().toString(36).substr(2,6);
                    counter[level]++;
                    for(var j = level + 1; j < 6; j++) counter[j] = 0;
                    var num = '';
                    if(listStyle === 'numbered') {
                        var parts = [];
                        for(var k = 0; k <= level; k++){
                            if(counter[k] > 0) parts.push(counter[k]);
                        }
                        num = '<span style="opacity:0.5;margin-right:6px;">' + parts.join('.') + '.</span>';
                    } else if(listStyle === 'bullets') {
                        num = '<span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:' + linkClr + ';margin-right:8px;vertical-align:middle;"></span>';
                    }
                    html += '<div class="olo-toc-item" style="padding-left:' + (level * indent) + 'px;">' + num + '<a href="#' + h.id + '">' + h.textContent + '</a></div>';
                });
                listEl.innerHTML = html;

                if(smooth){
                    listEl.addEventListener('click', function(e){
                        var a = e.target.closest('a');
                        if(a){
                            e.preventDefault();
                            var target = document.querySelector(a.getAttribute('href'));
                            if(target) target.scrollIntoView({behavior:'smooth',block:'start'});
                        }
                    });
                }
                if(doHighlight){
                    var tocLinks = listEl.querySelectorAll('a');
                    var obs = new IntersectionObserver(function(entries){
                        entries.forEach(function(entry){
                            if(entry.isIntersecting){
                                tocLinks.forEach(function(l){ l.classList.remove('olo-toc-active'); });
                                var activeLink = listEl.querySelector('a[href="#' + entry.target.id + '"]');
                                if(activeLink) activeLink.classList.add('olo-toc-active');
                            }
                        });
                    }, {rootMargin: '-20% 0px -80% 0px'});
                    headings.forEach(function(h){ obs.observe(h); });
                }
                return true;
            }

            // Try immediately
            if(buildToc()) return;

            // Wait for lazy-loaded content via MutationObserver
            var attempts = 0;
            var maxAttempts = 50;
            var mo = new MutationObserver(function(){
                attempts++;
                if(buildToc()){
                    mo.disconnect();
                    return;
                }
                if(attempts >= maxAttempts) mo.disconnect();
            });
            mo.observe(document.body, {childList: true, subtree: true});

            // Fallback: also try after window load
            window.addEventListener('load', function(){
                setTimeout(function(){
                    if(!document.querySelector('#' + uid + ' .olo-toc-item')){
                        if(!buildToc()){
                            var listEl = document.querySelector('#' + uid + ' .olo-toc-list');
                            if(listEl) listEl.innerHTML = '<p style="font-size:13px;color:var(--olo-color-text-muted, #9CA3AF);font-style:italic;">Nessun heading trovato nella pagina.</p>';
                        }
                    }
                    mo.disconnect();
                }, 500);
            });
        })();
        </script>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }
}
