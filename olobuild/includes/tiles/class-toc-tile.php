<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Toc_Tile extends Olo_Tile_Base {
    protected $type     = 'toc';
    protected $name     = 'Indice contenuti';
    protected $icon     = 'dashicons-list-view';
    protected $category = 'text';
    protected $defaults = [
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
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-toc-' . wp_rand(10000, 99999);

        $max_depth  = absint($s['max_depth']) ?: 3;
        $list_style = $s['list_style'];
        $link_clr   = $this->safe_color_css($s['link_color']) ?: 'var(--olo-color-primary, #6366F1)';
        $title_clr  = $this->safe_color_css($s['title_color']) ?: 'var(--olo-color-text, #374151)';
        $text_clr   = $this->safe_color_css($s['text_color']) ?: 'var(--olo-color-text, #374151)';
        $font_size  = absint($s['font_size']) ?: 15;
        $indent     = absint($s['indent']) ?: 20;
        $sticky     = !empty($s['sticky']);
        $smooth     = !empty($s['smooth_scroll']);
        $highlight  = !empty($s['highlight_active']);

        $tag_selector = 'h1,h2,h3,h4,h5,h6';
        $tags = [];
        for ($i = 1; $i <= $max_depth; $i++) $tags[] = "h{$i}";
        $tag_selector = implode(',', $tags);

        ob_start();
        ?>
        <style>
        .<?php echo $uid; ?> { padding: 16px; border-radius: 8px; <?php if ($sticky) echo 'position: sticky; top: 20px;'; ?> }
        .<?php echo $uid; ?> .olo-toc-title { font-weight: 700; font-size: 16px; color: <?php echo $title_clr; ?>; margin-bottom: 12px; border-bottom: 1px solid var(--olo-color-border, #E5E7EB); padding-bottom: 8px; }
        .<?php echo $uid; ?> .olo-toc-item { margin-bottom: 6px; }
        .<?php echo $uid; ?> .olo-toc-item a { color: <?php echo $link_clr; ?>; text-decoration: none; font-size: <?php echo $font_size; ?>px; transition: color 0.2s; }
        .<?php echo $uid; ?> .olo-toc-item a:hover { text-decoration: underline; }
        <?php if ($highlight) : ?>
        .<?php echo $uid; ?> .olo-toc-item a.olo-toc-active { font-weight: 700; }
        <?php endif; ?>
        </style>
        <nav class="olo-toc <?php echo $uid; ?>" id="<?php echo $uid; ?>">
            <?php if (!empty($s['title'])) : ?>
                <div class="olo-toc-title"><?php echo esc_html($s['title']); ?></div>
            <?php endif; ?>
            <div class="olo-toc-list" data-tags="<?php echo esc_attr($tag_selector); ?>" data-indent="<?php echo $indent; ?>" data-list-style="<?php echo esc_attr($list_style); ?>">
                <!-- Populated by JavaScript -->
            </div>
        </nav>
        <script>
        (function(){
            var container = document.getElementById('<?php echo $uid; ?>');
            if(!container) return;
            var listEl = container.querySelector('.olo-toc-list');
            var tags = listEl.getAttribute('data-tags').split(',');
            var indent = parseInt(listEl.getAttribute('data-indent')) || 20;
            var listStyle = listEl.getAttribute('data-list-style');
            var headings = document.querySelectorAll('.olo-template ' + tags.join(', .olo-template '));
            if(!headings.length) {
                listEl.innerHTML = '<p style="font-size:13px;color:var(--olo-color-text-muted, #9CA3AF);font-style:italic;">Nessun heading trovato nella pagina.</p>';
                return;
            }
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
                    for(var k = 0; k <= level; k++) {
                        if(counter[k] > 0) parts.push(counter[k]);
                    }
                    num = '<span style="opacity:0.5;margin-right:6px;">' + parts.join('.') + '.</span>';
                } else if(listStyle === 'bullets') {
                    num = '<span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:<?php echo $link_clr; ?>;margin-right:8px;vertical-align:middle;"></span>';
                }
                html += '<div class="olo-toc-item" style="padding-left:' + (level * indent) + 'px;">' + num + '<a href="#' + h.id + '">' + h.textContent + '</a></div>';
            });
            listEl.innerHTML = html;
            <?php if ($smooth) : ?>
            listEl.addEventListener('click', function(e){
                if(e.target.tagName === 'A'){
                    e.preventDefault();
                    var target = document.querySelector(e.target.getAttribute('href'));
                    if(target) target.scrollIntoView({behavior:'smooth',block:'start'});
                }
            });
            <?php endif; ?>
            <?php if ($highlight) : ?>
            var tocLinks = listEl.querySelectorAll('a');
            var observer = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if(entry.isIntersecting){
                        tocLinks.forEach(function(l){ l.classList.remove('olo-toc-active'); });
                        var activeLink = listEl.querySelector('a[href="#' + entry.target.id + '"]');
                        if(activeLink) activeLink.classList.add('olo-toc-active');
                    }
                });
            }, {rootMargin: '-20% 0px -80% 0px'});
            headings.forEach(function(h){ observer.observe(h); });
            <?php endif; ?>
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
