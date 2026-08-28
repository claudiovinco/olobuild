<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Lookbook Mixer — "componi la tua routine": slot con prev/next che scorrono le opzioni
 * (nome · prezzo · colore) e una card che somma il totale live. Voci piatte raggruppate per "step".
 * Runtime inline scoped, senza operatori vietati da wptexturize.
 */
class Olobuild_LookbookMixer_Tile extends Olobuild_Tile_Base {

    protected $type     = 'lookbookmixer';
    protected $name     = 'Lookbook Mixer';
    protected $icon     = 'dashicons-randomize';
    protected $category = 'layout';
    protected $defaults = [
        'items' => [
            [ 'step' => 'Cleanse', 'name' => 'Rosewater Gel',     'price' => '24', 'color' => '#f4c9d4' ],
            [ 'step' => 'Cleanse', 'name' => 'Clay Melt Balm',    'price' => '29', 'color' => '#e3b778' ],
            [ 'step' => 'Treat',   'name' => 'Vitamin C Drops',   'price' => '38', 'color' => '#e3b778' ],
            [ 'step' => 'Treat',   'name' => 'Niacinamide 10%',   'price' => '32', 'color' => '#e7a0b4' ],
            [ 'step' => 'Hydrate', 'name' => 'Ceramide Cream',    'price' => '34', 'color' => '#f4c9d4' ],
            [ 'step' => 'Protect', 'name' => 'Sheer SPF 50',      'price' => '30', 'color' => '#f6e9ec' ],
        ],
        'currency' => '€', 'card_title' => 'Your routine', 'card_steps_label' => 'steps',
        'card_sub' => 'Built in four taps. Swap any step until it’s yours.',
        'cta_text' => 'Add routine to bag', 'cta_url' => '#',
        'panel_bg' => '#4d2f40', 'slot_bg' => '#432838', 'accent' => '#e7a0b4', 'accent_ink' => '#23131d',
        'name_color' => '#f6e9ec', 'price_color' => '#9c7e8c', 'line_color' => 'rgba(246,233,236,.13)',
        'name_font_family' => 'heading', 'mono_font_family' => '',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-lbmix-' . wp_rand( 10000, 99999 );

        $heading = "var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $body    = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $mono_fb = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
        $mono_fam = $this->resolve_font_family( $s['mono_font_family'] ?? '' );
        // Nome font puro (legacy campo text) → wrap con lo stack mono di fallback storico.
        if ( $mono_fam !== '' && preg_match( '/^[A-Za-z0-9 \-]+$/', $mono_fam ) ) {
            $mono_fam = "'" . $mono_fam . "'," . $mono_fb;
        }
        $mono    = $mono_fam !== '' ? $mono_fam : $mono_fb;
        $nfam    = $this->resolve_font_family( $s['name_font_family'] ?? '', [ 'heading' => $heading, 'body' => $body ] ) ?: $heading;

        $panel  = $this->safe_color_css( $s['panel_bg'] ) ?: '#4d2f40';
        $slotbg = $this->safe_color_css( $s['slot_bg'] ) ?: '#432838';
        $acc    = $this->safe_color_css( $s['accent'] ) ?: '#e7a0b4';
        $accink = $this->safe_color_css( $s['accent_ink'] ) ?: '#23131d';
        $namec  = $this->safe_color_css( $s['name_color'] ) ?: '#f6e9ec';
        $pricec = $this->safe_color_css( $s['price_color'] ) ?: '#9c7e8c';
        $line   = $this->safe_color_css( $s['line_color'] ) ?: 'rgba(246,233,236,.13)';
        $cur    = sanitize_text_field( $s['currency'] ?? '€' );

        // Raggruppa le voci per "step" preservando l'ordine
        $groups = [];
        $order  = [];
        foreach ( ( is_array( $s['items'] ) ? $s['items'] : [] ) as $it ) {
            $st = (string) ( $it['step'] ?? '' );
            if ( ! isset( $groups[ $st ] ) ) { $groups[ $st ] = []; $order[] = $st; }
            $groups[ $st ][] = $it;
        }
        $n_steps = count( $order );

        ob_start();
        ?>
        <div class="olo-lbmix <?php echo esc_attr( $uid ); ?>" data-currency="<?php echo esc_attr( $cur ); ?>"
             style="display:grid;grid-template-columns:1.12fr .88fr;gap:clamp(28px,4vw,56px);align-items:center;border:1px solid <?php echo esc_attr( $line ); ?>;border-radius:24px;background:<?php echo esc_attr( $panel ); ?>;padding:clamp(24px,4vw,42px);">
            <div class="olo-lbmix__slots" style="display:flex;flex-direction:column;gap:12px;">
                <?php foreach ( $order as $st ) :
                    $opts = $groups[ $st ];
                ?>
                    <div class="olo-lbmix__slot" data-slot data-idx="0" style="display:flex;align-items:center;gap:16px;background:<?php echo esc_attr( $slotbg ); ?>;border:1px solid <?php echo esc_attr( $line ); ?>;border-radius:14px;padding:13px 16px;">
                        <span class="olo-lbmix__sw" data-sw style="width:46px;height:46px;border-radius:50%;flex:none;box-shadow:inset 0 0 0 1.5px rgba(246,233,236,.3);transition:background .3s;"></span>
                        <div class="olo-lbmix__meta" style="flex:1;min-width:0;">
                            <span class="olo-lbmix__step" style="font-family:<?php echo esc_attr( $mono ); ?>;font-weight:700;font-size:10.5px;letter-spacing:.14em;text-transform:uppercase;color:<?php echo esc_attr( $acc ); ?>;"><?php echo esc_html( $st ); ?></span>
                            <div class="olo-lbmix__nm" style="display:flex;align-items:baseline;justify-content:space-between;gap:12px;margin-top:2px;">
                                <span class="olo-lbmix__name" data-name style="font-family:<?php echo esc_attr( $nfam ); ?>;font-size:21px;color:<?php echo esc_attr( $namec ); ?>;line-height:1.08;"></span>
                                <span class="olo-lbmix__price" data-price style="font-family:<?php echo esc_attr( $mono ); ?>;font-weight:700;font-size:14px;color:<?php echo esc_attr( $pricec ); ?>;white-space:nowrap;"></span>
                            </div>
                        </div>
                        <div class="olo-lbmix__nav" style="display:flex;gap:6px;flex:none;">
                            <button type="button" data-prev aria-label="<?php echo esc_attr( olobuild_t( 'Precedente' ) ); ?>" style="width:40px;height:40px;border-radius:50%;border:1px solid <?php echo esc_attr( $acc ); ?>;background:transparent;color:<?php echo esc_attr( $namec ); ?>;cursor:pointer;display:grid;place-items:center;font-size:20px;line-height:1;">‹</button>
                            <button type="button" data-next aria-label="<?php echo esc_attr( olobuild_t( 'Successivo' ) ); ?>" style="width:40px;height:40px;border-radius:50%;border:1px solid <?php echo esc_attr( $acc ); ?>;background:transparent;color:<?php echo esc_attr( $namec ); ?>;cursor:pointer;display:grid;place-items:center;font-size:20px;line-height:1;">›</button>
                        </div>
                        <?php foreach ( $opts as $o ) : ?>
                            <span data-opt data-name="<?php echo esc_attr( $o['name'] ?? '' ); ?>" data-price="<?php echo esc_attr( preg_replace( '/[^0-9.]/', '', (string) ( $o['price'] ?? '0' ) ) ); ?>" data-color="<?php echo esc_attr( $this->safe_color_css( $o['color'] ?? '' ) ?: '#999' ); ?>" style="display:none;"></span>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="olo-lbmix__card" style="text-align:center;border:1px solid <?php echo esc_attr( $acc ); ?>;border-radius:20px;padding:clamp(28px,4vw,40px);background:linear-gradient(150deg,<?php echo esc_attr( $acc ); ?>28,<?php echo esc_attr( $acc ); ?>0a);">
                <span style="font-family:<?php echo esc_attr( $mono ); ?>;font-weight:700;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:<?php echo esc_attr( $acc ); ?>;"><?php echo esc_html( $s['card_title'] ); ?> · <?php echo intval( $n_steps ); ?> <?php echo esc_html( $s['card_steps_label'] ); ?></span>
                <div class="olo-lbmix__total" data-total style="font-family:<?php echo esc_attr( $nfam ); ?>;font-size:clamp(46px,7vw,68px);color:<?php echo esc_attr( $namec ); ?>;line-height:1;margin:12px 0 10px;"><?php echo esc_html( $cur ); ?>0</div>
                <span style="display:block;font-size:14px;color:<?php echo esc_attr( $pricec ); ?>;margin-bottom:22px;line-height:1.55;"><?php echo esc_html( $s['card_sub'] ); ?></span>
                <a href="<?php echo esc_url( $s['cta_url'] ?: '#' ); ?>" style="display:inline-flex;align-items:center;justify-content:center;padding:14px 26px;border-radius:999px;background:<?php echo esc_attr( $acc ); ?>;color:<?php echo esc_attr( $accink ); ?>;font-family:<?php echo esc_attr( $body ); ?>;font-weight:600;font-size:14px;text-decoration:none;"><?php echo esc_html( $s['cta_text'] ); ?></a>
            </div>
        </div>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css colors); $uid is an internal generated class name. ?>
        <style>
            @media (max-width: 860px) { .<?php echo $uid; ?> { grid-template-columns: 1fr !important; gap: 28px !important; } }
            .<?php echo $uid; ?> .olo-lbmix__nav button:hover { background: <?php echo $acc; ?>; color: <?php echo $accink; ?>; }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <script>(function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if (!root) { return; }
            var cur = root.getAttribute('data-currency') || '';
            var totalEl = root.querySelector('[data-total]');
            var slots = root.querySelectorAll('[data-slot]');
            function total(){
                var sum = 0;
                slots.forEach(function (slot) {
                    var opts = slot.querySelectorAll('[data-opt]');
                    var n = opts.length;
                    if (n === 0) { return; }
                    var i = parseInt(slot.getAttribute('data-idx') || '0', 10);
                    i = ((i % n) + n) % n;
                    sum = sum + parseFloat(opts[i].getAttribute('data-price') || '0');
                });
                totalEl.textContent = cur + sum;
            }
            slots.forEach(function (slot) {
                var opts = slot.querySelectorAll('[data-opt]');
                var n = opts.length;
                var sw = slot.querySelector('[data-sw]');
                var nm = slot.querySelector('[data-name]');
                var pr = slot.querySelector('[data-price]');
                function show(){
                    if (n === 0) { return; }
                    var i = parseInt(slot.getAttribute('data-idx') || '0', 10);
                    i = ((i % n) + n) % n;
                    slot.setAttribute('data-idx', i);
                    var o = opts[i];
                    sw.style.background = o.getAttribute('data-color') || '#000';
                    nm.textContent = o.getAttribute('data-name') || '';
                    pr.textContent = cur + (o.getAttribute('data-price') || '0');
                    total();
                }
                var prev = slot.querySelector('[data-prev]');
                var next = slot.querySelector('[data-next]');
                if (prev) { prev.addEventListener('click', function () { var i = parseInt(slot.getAttribute('data-idx') || '0', 10); slot.setAttribute('data-idx', i - 1); show(); }); }
                if (next) { next.addEventListener('click', function () { var i = parseInt(slot.getAttribute('data-idx') || '0', 10); slot.setAttribute('data-idx', i + 1); show(); }); }
                show();
            });
        })();</script>
        <?php
        return ob_get_clean();
    }
}
