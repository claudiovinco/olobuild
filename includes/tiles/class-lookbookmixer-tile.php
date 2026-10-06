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
            [ 'step' => 'Cleanse', 'name' => 'Rosewater Gel',     'price' => '24', 'color' => 'var(--olo-color-light, #f4c9d4)' ],
            [ 'step' => 'Cleanse', 'name' => 'Clay Melt Balm',    'price' => '29', 'color' => 'var(--olo-color-light, #e3b778)' ],
            [ 'step' => 'Treat',   'name' => 'Vitamin C Drops',   'price' => '38', 'color' => 'var(--olo-color-primary, #e3b778)' ],
            [ 'step' => 'Treat',   'name' => 'Niacinamide 10%',   'price' => '32', 'color' => 'var(--olo-color-light, #e7a0b4)' ],
            [ 'step' => 'Hydrate', 'name' => 'Ceramide Cream',    'price' => '34', 'color' => 'var(--olo-color-primary, #f4c9d4)' ],
            [ 'step' => 'Protect', 'name' => 'Sheer SPF 50',      'price' => '30', 'color' => 'var(--olo-color-light, #f6e9ec)' ],
        ],
        'currency' => '€', 'card_title' => 'Your routine', 'card_steps_label' => 'steps',
        'card_sub' => 'Built in four taps. Swap any step until it’s yours.',
        'cta_text' => 'Add routine to bag', 'cta_url' => '#',
        'panel_bg' => 'var(--olo-color-dark, #4d2f40)', 'slot_bg' => 'var(--olo-color-dark, #432838)', 'accent' => 'var(--olo-color-primary, #e7a0b4)', 'accent_ink' => 'var(--olo-color-dark, #23131d)',
        'name_color' => 'var(--olo-color-light, #f6e9ec)', 'price_color' => 'var(--olo-color-text-soft, #9c7e8c)', 'line_color' => 'rgba(246,233,236,.13)',
        'name_font_family' => 'heading', 'mono_font_family' => '',
    ];

    public function get_controls() { return []; }

    /**
     * Prezzo salvato → numero. «4,50» e «4.50» valgono 4,5: prima la virgola veniva
     * tolta e «4,50» diventava 450. Con entrambi i separatori («1.234,50») l'ultimo è
     * quello dei decimali e l'altro raggruppa le migliaia.
     */
    private function prezzo( $raw ) {
        $p  = preg_replace( '/[^0-9.,]/', '', (string) $raw );
        // Un solo separatore, ripetuto a gruppi di tre cifre («1.500», «1,500», «1.234.567»):
        // sono le migliaia, non i decimali. Letto come decimale «1.500» diventava 1,50 e
        // «1.234.567» (non numerico) 0; nessun prezzo si scrive con tre decimali.
        if ( preg_match( '/^[1-9]\d{0,2}([.,])\d{3}(?:\1\d{3})*$/', $p ) ) {
            return (float) str_replace( [ '.', ',' ], '', $p );
        }
        // Altrimenti l'ULTIMO separatore indica i decimali e gli altri si tolgono: anche
        // «1.234.56» resta 1234,56 invece di diventare non numerico (= 0).
        $pc  = strrpos( $p, ',' );
        $pd  = strrpos( $p, '.' );
        $ult = max( $pc === false ? -1 : $pc, $pd === false ? -1 : $pd );
        if ( $ult >= 0 ) {
            $p = str_replace( [ '.', ',' ], '', substr( $p, 0, $ult ) ) . '.' . substr( $p, $ult + 1 );
        }
        return ( $p === '' || ! is_numeric( $p ) ) ? 0.0 : round( (float) $p, 2 );
    }

    /**
     * Importo da mostrare: «€ 48» / «€ 48,50» nel formato della lingua del sito, con lo
     * spazio (non separabile) fra simbolo e cifra. Gemello di fmt() nello script sotto.
     */
    private function importo( $valore, $cur, $dec ) {
        $n = number_format_i18n( $valore, $dec );
        return $cur !== '' ? $cur . "\u{00A0}" . $n : $n;
    }

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
            $it['_prezzo'] = $this->prezzo( $it['price'] ?? '0' );
            $groups[ $st ][] = $it;
        }
        $n_steps = count( $order );

        // Decimali: due se almeno un prezzo li ha, altrimenti nessuno (resa di sempre).
        // Il totale di partenza (prima opzione di ogni slot) si stampa già qui: senza
        // script la card mostrava «€0» e gli slot restavano senza nome né prezzo.
        $dec     = 0;
        $tot_c   = 0;
        // Pastiglia di un'opzione senza colore valido: un grigio del tema (prima #999 fisso).
        // Unica riserva per il markup di partenza e per data-color, che lo script ricopia.
        $col_fb  = 'var(--olo-color-text-muted, #999999)';
        foreach ( $order as $st ) {
            foreach ( $groups[ $st ] as $o ) {
                if ( abs( $o['_prezzo'] - floor( $o['_prezzo'] ) ) > 0.001 ) { $dec = 2; }
            }
            $tot_c += (int) round( $groups[ $st ][0]['_prezzo'] * 100 );
        }

        ob_start();
        ?>
        <div class="olo-lbmix <?php echo esc_attr( $uid ); ?>" data-currency="<?php echo esc_attr( $cur ); ?>" data-decimals="<?php echo (int) $dec; ?>"
             style="display:grid;grid-template-columns:1.12fr .88fr;gap:clamp(28px,4vw,56px);align-items:center;border:1px solid <?php echo esc_attr( $line ); ?>;border-radius:24px;background:<?php echo esc_attr( $panel ); ?>;padding:clamp(24px,4vw,42px);">
            <div class="olo-lbmix__slots" style="display:flex;flex-direction:column;gap:12px;">
                <?php foreach ( $order as $st ) :
                    $opts  = $groups[ $st ];
                    $primo = $opts[0];
                    $col0  = $this->safe_color_css( $primo['color'] ?? '' ) ?: $col_fb;
                ?>
                    <div class="olo-lbmix__slot" data-slot data-idx="0" style="display:flex;align-items:center;gap:16px;background:<?php echo esc_attr( $slotbg ); ?>;border:1px solid <?php echo esc_attr( $line ); ?>;border-radius:14px;padding:13px 16px;">
                        <span class="olo-lbmix__sw" data-sw style="width:46px;height:46px;border-radius:50%;flex:none;box-shadow:inset 0 0 0 1.5px rgba(246,233,236,.3);transition:background .3s;background:<?php echo esc_attr( $col0 ); ?>;"></span>
                        <div class="olo-lbmix__meta" style="flex:1;min-width:0;">
                            <span class="olo-lbmix__step" style="font-family:<?php echo esc_attr( $mono ); ?>;font-weight:700;font-size:10.5px;letter-spacing:.14em;text-transform:uppercase;color:<?php echo esc_attr( $acc ); ?>;"><?php echo esc_html( $st ); ?></span>
                            <div class="olo-lbmix__nm" style="display:flex;align-items:baseline;justify-content:space-between;gap:12px;margin-top:2px;">
                                <span class="olo-lbmix__name" data-name style="font-family:<?php echo esc_attr( $nfam ); ?>;font-size:21px;color:<?php echo esc_attr( $namec ); ?>;line-height:1.08;"><?php echo esc_html( $primo['name'] ?? '' ); ?></span>
                                <span class="olo-lbmix__price" data-price style="font-family:<?php echo esc_attr( $mono ); ?>;font-weight:700;font-size:14px;color:<?php echo esc_attr( $pricec ); ?>;white-space:nowrap;"><?php echo esc_html( $this->importo( $primo['_prezzo'], $cur, $dec ) ); ?></span>
                            </div>
                        </div>
                        <?php // Frecce SVG (chevron del set Lucide) al posto dei caratteri ‹ ›, che cambiavano peso e allineamento col font del tema. ?>
                        <div class="olo-lbmix__nav" style="display:flex;gap:6px;flex:none;">
                            <button type="button" data-prev aria-label="<?php echo esc_attr( olobuild_t( 'Precedente' ) ); ?>" style="width:40px;height:40px;border-radius:50%;border:1px solid <?php echo esc_attr( $acc ); ?>;background:transparent;color:<?php echo esc_attr( $namec ); ?>;cursor:pointer;display:grid;place-items:center;padding:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m15 18-6-6 6-6"/></svg></button>
                            <button type="button" data-next aria-label="<?php echo esc_attr( olobuild_t( 'Successivo' ) ); ?>" style="width:40px;height:40px;border-radius:50%;border:1px solid <?php echo esc_attr( $acc ); ?>;background:transparent;color:<?php echo esc_attr( $namec ); ?>;cursor:pointer;display:grid;place-items:center;padding:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m9 18 6-6-6-6"/></svg></button>
                        </div>
                        <?php foreach ( $opts as $o ) : ?>
                            <span data-opt data-name="<?php echo esc_attr( $o['name'] ?? '' ); ?>" data-price="<?php echo esc_attr( (string) $o['_prezzo'] ); ?>" data-color="<?php echo esc_attr( $this->safe_color_css( $o['color'] ?? '' ) ?: $col_fb ); ?>" style="display:none;"></span>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="olo-lbmix__card" style="text-align:center;border:1px solid <?php echo esc_attr( $acc ); ?>;border-radius:20px;padding:clamp(28px,4vw,40px);background:linear-gradient(150deg,<?php echo esc_attr( Olobuild_Tile_Utils::con_alfa( $acc, '28' ) ); ?>,<?php echo esc_attr( Olobuild_Tile_Utils::con_alfa( $acc, '0a' ) ); ?>);">
                <span style="font-family:<?php echo esc_attr( $mono ); ?>;font-weight:700;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:<?php echo esc_attr( $acc ); ?>;"><?php echo esc_html( $s['card_title'] ); ?> · <?php echo intval( $n_steps ); ?> <?php echo esc_html( $s['card_steps_label'] ); ?></span>
                <div class="olo-lbmix__total" data-total style="font-family:<?php echo esc_attr( $nfam ); ?>;font-size:clamp(46px,7vw,68px);color:<?php echo esc_attr( $namec ); ?>;line-height:1;margin:12px 0 10px;"><?php echo esc_html( $this->importo( $tot_c / 100, $cur, $dec ) ); ?></div>
                <span style="display:block;font-size:14px;color:<?php echo esc_attr( $pricec ); ?>;margin-bottom:22px;line-height:1.55;"><?php echo esc_html( $s['card_sub'] ); ?></span>
                <a href="<?php echo esc_url( $s['cta_url'] ?: '#' ); ?>" style="display:inline-flex;align-items:center;justify-content:center;padding:14px 26px;border-radius:999px;background:<?php echo esc_attr( $acc ); ?>;color:<?php echo esc_attr( $accink ); ?>;font-family:<?php echo esc_attr( $body ); ?>;font-weight:600;font-size:14px;text-decoration:none;"><?php echo esc_html( $s['cta_text'] ); ?></a>
            </div>
        </div>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css colors); $uid is an internal generated class name. ?>
        <style>
            @media (max-width: 860px) { .<?php echo $uid; ?> { grid-template-columns: 1fr !important; gap: 28px !important; } }
            .<?php echo $uid; ?> .olo-lbmix__nav button:hover { background: <?php echo $acc; ?>; color: <?php echo $accink; ?>; }
            .<?php echo $uid; ?> .olo-lbmix__nav button:focus-visible,
            .<?php echo $uid; ?> .olo-lbmix__card a:focus-visible { outline: 2px solid <?php echo $acc; ?>; outline-offset: 2px; }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <script>(function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if (!root) { return; }
            var cur = root.getAttribute('data-currency') || '';
            var dec = parseInt(root.getAttribute('data-decimals') || '0', 10);
            var lang = document.documentElement.lang || 'it-IT';
            var totalEl = root.querySelector('[data-total]');
            var slots = root.querySelectorAll('[data-slot]');
            // Importi in CENTESIMI interi: sommati in virgola mobile 24.9 + 5.2 davano 30.099999999999998.
            function cents(v){ var n = parseFloat(v || '0'); return isNaN(n) ? 0 : Math.round(n * 100); }
            // «€ 48» / «€ 48,50»: cifra nel formato della lingua della pagina, spazio non
            // separabile dopo il simbolo (prima «€48»). Gemello di importo() nel PHP.
            function fmt(c){
                var n = c / 100;
                var t;
                try { t = n.toLocaleString(lang, { minimumFractionDigits: dec, maximumFractionDigits: dec }); }
                catch (e) { t = n.toFixed(dec); }
                return cur ? cur + '\u00a0' + t : t;
            }
            function total(){
                var sum = 0;
                slots.forEach(function (slot) {
                    var opts = slot.querySelectorAll('[data-opt]');
                    var n = opts.length;
                    if (n === 0) { return; }
                    var i = parseInt(slot.getAttribute('data-idx') || '0', 10);
                    i = ((i % n) + n) % n;
                    sum = sum + cents(opts[i].getAttribute('data-price'));
                });
                totalEl.textContent = fmt(sum);
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
                    pr.textContent = fmt(cents(o.getAttribute('data-price')));
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
