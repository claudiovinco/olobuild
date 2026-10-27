<?php
/**
 * «Aggiunto al carrello»: l'avviso che conferma l'aggiunta di un prodotto, per tutte le tile di WooCommerce.
 *
 * Prima l'aggiunta col pulsante (AJAX di WooCommerce) cambiava solo il numero sul mini carrello: chi non lo
 * guardava non sapeva se il clic era andato. Ora compare un riquadro con foto, nome, quantità, subtotale e i
 * pulsanti «Vai al carrello» e «Continua gli acquisti»; in alto a destra, sul telefono in basso; si chiude da
 * solo dopo qualche secondo (fermo sotto il mouse o col fuoco dentro), con Esc o con la X.
 *
 * - Aggiunta con AJAX: i dati arrivano nei frammenti della risposta (woocommerce_add_to_cart_fragments) e lo
 *   script li legge dall'evento added_to_cart.
 * - Aggiunta senza AJAX (link «?add-to-cart=», modulo): le righe aggiunte restano in sessione e l'avviso esce
 *   sulla pagina che segue, se nessuno ha già stampato in pagina il messaggio di WooCommerce (tile Avvisi,
 *   pagine di WooCommerce): niente doppioni.
 *
 * Lo script si stampa sulle pagine con una tile del negozio (woo_*, anche nelle sezioni pigre: il renderer le disegna
 * subito dentro un <template>) e su quelle con un'aggiunta da confermare.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Avviso_Carrello {

    /** Le righe del carrello aggiunte senza AJAX, in attesa della pagina che le mostra. */
    const SESSIONE = 'olo_avviso_carrello';

    /** @var string[] Le righe aggiunte in questa richiesta. */
    private static $righe = [];

    /** @var bool La pagina ha una tile del negozio (serve()): solo lì lo script dell'avviso. */
    private static $serve = false;

    public static function init() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }
        add_action( 'woocommerce_add_to_cart', [ __CLASS__, 'ricorda' ], 20 );
        add_filter( 'woocommerce_add_to_cart_fragments', [ __CLASS__, 'frammento' ] );
        add_action( 'wp_footer', [ __CLASS__, 'stampa' ], 40 );
    }

    /** Il renderer disegna una tile woo_*: la pagina può aggiungere al carrello. */
    public static function serve() {
        self::$serve = true;
    }

    /** Una riga è stata aggiunta: con AJAX va nei frammenti, senza nella sessione per la pagina dopo. */
    public static function ricorda( $chiave ) {
        $chiave = (string) $chiave;
        if ( '' === $chiave ) {
            return;
        }
        self::$righe[] = $chiave;
        if ( wp_doing_ajax() || ! function_exists( 'WC' ) || ! WC()->session ) {
            return;
        }
        $attesa   = (array) WC()->session->get( self::SESSIONE, [] );
        $attesa[] = $chiave;
        WC()->session->set( self::SESSIONE, array_values( array_unique( $attesa ) ) );
    }

    /** I dati dell'avviso nella risposta dell'aggiunta con AJAX (chiave che non corrisponde a niente in pagina). */
    public static function frammento( $frammenti ) {
        if ( self::$righe ) {
            $dati = self::dati( self::$righe );
            if ( $dati ) {
                $frammenti['olo-avviso-carrello'] = wp_json_encode( $dati );
            }
        }
        return $frammenti;
    }

    /** Le righe aggiunte e il carrello com'è ora; null se nessuna riga c'è più. */
    private static function dati( $chiavi ) {
        $cart = WC()->cart;
        if ( ! $cart ) {
            return null;
        }
        $righe = [];
        foreach ( array_unique( (array) $chiavi ) as $k ) {
            $item = $cart->get_cart_item( (string) $k );
            if ( empty( $item['data'] ) || ! is_a( $item['data'], 'WC_Product' ) ) {
                continue;
            }
            $p   = $item['data'];
            $img = $p->get_image_id() ? wp_get_attachment_image_url( $p->get_image_id(), 'woocommerce_thumbnail' ) : '';
            $righe[] = [
                'nome'   => wp_strip_all_tags( $p->get_name() ),
                'img'    => $img ? $img : wc_placeholder_img_src( 'woocommerce_thumbnail' ),
                'qta'    => (int) $item['quantity'],
                'prezzo' => self::testo( $cart->get_product_subtotal( $p, $item['quantity'] ) ),
            ];
        }
        if ( ! $righe ) {
            return null;
        }
        return [
            'righe'     => $righe,
            'conta'     => (int) $cart->get_cart_contents_count(),
            'subtotale' => self::testo( $cart->get_cart_subtotal() ),
        ];
    }

    /** Un prezzo di WooCommerce (HTML con entità) come testo semplice. */
    private static function testo( $html ) {
        return trim( html_entity_decode( wp_strip_all_tags( (string) $html ), ENT_QUOTES, 'UTF-8' ) );
    }

    /**
     * L'avviso delle aggiunte senza AJAX, se il messaggio di WooCommerce è ancora in attesa (nessuno l'ha
     * stampato in pagina): l'avviso prende il suo posto e il messaggio non ricompare sulla pagina dopo.
     */
    private static function in_attesa() {
        if ( ! WC()->session ) {
            return null;
        }
        $chiavi = (array) WC()->session->get( self::SESSIONE, [] );
        if ( ! $chiavi ) {
            return null;
        }
        WC()->session->set( self::SESSIONE, null );
        if ( ! function_exists( 'wc_notice_count' ) || wc_notice_count( 'success' ) < 1 ) {
            return null;
        }
        $avvisi = (array) WC()->session->get( 'wc_notices', [] );
        unset( $avvisi['success'] );
        WC()->session->set( 'wc_notices', $avvisi );
        return self::dati( $chiavi );
    }

    public static function stampa() {
        if ( is_admin() || ! function_exists( 'WC' ) ) {
            return;
        }
        $subito = self::in_attesa();
        // Solo dove serve: sulle pagine con una tile del negozio, o con un'aggiunta da confermare. Stampato su
        // ogni pagina disegnata da OLObuild pesava 9 KB anche dove non si compra niente.
        if ( ! $subito && ! self::$serve ) {
            return;
        }
        // Gli indirizzi si prendono qui, dalla pagina: la richiesta AJAX non passa dai filtri della demo dei temi.
        $testi = [
            'titolo'    => olobuild_t( 'Aggiunto al carrello' ),
            'chiudi'    => olobuild_t( 'Chiudi' ),
            'quantita'  => olobuild_t( 'Quantità' ),
            'subtotale' => olobuild_t( 'Subtotale' ),
            'articolo'  => olobuild_t( 'articolo' ),
            'articoli'  => olobuild_t( 'articoli' ),
            'altri'     => olobuild_t( 'altri' ),
            'vai'       => is_cart() ? olobuild_t( 'Vai alla cassa' ) : olobuild_t( 'Vai al carrello' ),
            'link'      => is_cart() ? wc_get_checkout_url() : wc_get_cart_url(),
            'continua'  => olobuild_t( 'Continua gli acquisti' ),
        ];
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS fisso; nello script solo valori passati da wp_json_encode().
        ?>
        <style id="olo-avviso-carrello-css">
        .olo-avc{position:fixed;z-index:100000;top:16px;right:16px;width:min(360px,calc(100vw - 32px));box-sizing:border-box;padding:16px;background:var(--olo-color-background,#fff);color:var(--olo-color-text,#111827);border:1px solid var(--olo-color-border,#e5e7eb);border-radius:12px;box-shadow:0 18px 40px rgba(15,23,42,.18);font-family:var(--olo-font-family,inherit);font-size:14px;line-height:1.4;text-align:left;opacity:0;transform:translateY(-8px);transition:opacity .25s ease,transform .25s ease}
        .olo-avc.is-aperto{opacity:1;transform:none}
        .olo-avc *{box-sizing:border-box}
        .olo-avc-testa{display:flex;align-items:center;gap:8px;margin:0 0 10px}
        .olo-avc-ok{display:inline-flex;align-items:center;justify-content:center;flex:none;width:24px;height:24px;border-radius:50%;background:var(--olo-color-success,#10b981);color:#fff}
        .olo-avc-titolo{flex:1;font-size:15px;font-weight:700}
        .olo-avc-x{display:inline-flex;align-items:center;justify-content:center;flex:none;width:32px;height:32px;margin:-6px -6px -6px 0;padding:0;border:0;border-radius:8px;background:transparent;color:var(--olo-color-text-muted,#6b7280);cursor:pointer}
        .olo-avc-x:hover{background:var(--olo-color-muted,#f3f4f6);color:var(--olo-color-text,#111827)}
        .olo-avc-riga{display:flex;align-items:center;gap:12px;padding:10px 0;border-top:1px solid var(--olo-color-border,#e5e7eb)}
        .olo-avc-riga img{flex:none;width:56px;height:56px;object-fit:contain;border-radius:8px;background:var(--olo-color-muted,#f3f4f6)}
        .olo-avc-nome{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;font-weight:600}
        .olo-avc-qta{display:block;margin-top:2px;color:var(--olo-color-text-muted,#6b7280);font-size:13px}
        .olo-avc-altri{padding:0 0 8px;color:var(--olo-color-text-muted,#6b7280);font-size:13px}
        .olo-avc-tot{display:flex;justify-content:space-between;gap:12px;padding:10px 0 14px;border-top:1px solid var(--olo-color-border,#e5e7eb)}
        .olo-avc-tot b{font-weight:700;white-space:nowrap}
        .olo-avc-azioni{display:flex;gap:8px}
        .olo-avc-azioni a,.olo-avc-azioni button{flex:1;display:inline-flex;align-items:center;justify-content:center;min-height:42px;margin:0;padding:8px 12px;border-radius:8px;font:inherit;font-size:14px;font-weight:600;line-height:1.2;text-align:center;text-decoration:none;cursor:pointer}
        .olo-avc-vai{border:1px solid transparent;background:var(--olo-color-primary,#e1474f);color:var(--olo-color-primary-contrast,#fff)}
        .olo-avc-vai:hover{filter:brightness(.95)}
        .olo-avc-continua{border:1px solid var(--olo-color-border,#e5e7eb);background:transparent;color:var(--olo-color-text,#111827)}
        .olo-avc-continua:hover{background:var(--olo-color-muted,#f3f4f6)}
        .olo-avc a:focus-visible,.olo-avc button:focus-visible{outline:2px solid var(--olo-color-primary,#e1474f);outline-offset:2px}
        @media (max-width:640px){.olo-avc{top:auto;right:16px;bottom:16px;left:16px;width:auto;transform:translateY(8px)}}
        @media (prefers-reduced-motion:reduce){.olo-avc{transition:none}}
        /* Il link «Visualizza carrello» che WooCommerce aggiunge accanto al pulsante dopo l'aggiunta: l'avviso
           lo dice già, e nelle card e sotto i pulsanti delle tile spostava tutto. */
        .olo-template a.added_to_cart.wc-forward{display:none}
        </style>
        <script id="olo-avviso-carrello-js">
        (function(){
            var T = <?php echo wp_json_encode( $testi ); ?>;
            var box = null;
            var timer = null;
            var fermo = false;
            var NS = 'http://www.w3.org/2000/svg';
            function el(tag, cls, testo){
                var e = document.createElement(tag);
                if(cls){ e.className = cls; }
                if(testo !== undefined){ e.textContent = testo; }
                return e;
            }
            function icona(d){
                var s = document.createElementNS(NS, 'svg');
                s.setAttribute('width', '16'); s.setAttribute('height', '16'); s.setAttribute('viewBox', '0 0 24 24');
                s.setAttribute('fill', 'none'); s.setAttribute('stroke', 'currentColor'); s.setAttribute('stroke-width', '2.5');
                s.setAttribute('stroke-linecap', 'round'); s.setAttribute('stroke-linejoin', 'round'); s.setAttribute('aria-hidden', 'true');
                var p = document.createElementNS(NS, 'path'); p.setAttribute('d', d); s.appendChild(p);
                return s;
            }
            function esc(e){ if(e.key === 'Escape'){ chiudi(); } }
            function chiudi(){
                if(!box){return}
                var b = box;
                box = null;
                clearTimeout(timer);
                document.removeEventListener('keydown', esc);
                b.classList.remove('is-aperto');
                setTimeout(function(){ if(b.parentNode){ b.parentNode.removeChild(b); } }, 300);
            }
            function attendi(){
                clearTimeout(timer);
                if(fermo){return}
                timer = setTimeout(chiudi, 6000);
            }
            function mostra(d){
                if(!d){return}
                if(!d.righe){return}
                if(box){ if(box.parentNode){ box.parentNode.removeChild(box); } box = null; }
                fermo = false;
                var b = el('div', 'olo-tokens olo-avc');
                b.setAttribute('role', 'status');
                b.setAttribute('aria-live', 'polite');
                var testa = el('div', 'olo-avc-testa');
                var ok = el('span', 'olo-avc-ok'); ok.appendChild(icona('M20 6L9 17l-5-5'));
                testa.appendChild(ok);
                testa.appendChild(el('span', 'olo-avc-titolo', T.titolo));
                var x = el('button', 'olo-avc-x'); x.type = 'button'; x.setAttribute('aria-label', T.chiudi);
                x.appendChild(icona('M18 6L6 18M6 6l12 12'));
                x.addEventListener('click', chiudi);
                testa.appendChild(x);
                b.appendChild(testa);
                d.righe.slice(0, 3).forEach(function(r){
                    var riga = el('div', 'olo-avc-riga');
                    var img = el('img'); img.src = r.img; img.alt = ''; img.width = 56; img.height = 56;
                    riga.appendChild(img);
                    var info = el('div');
                    info.appendChild(el('span', 'olo-avc-nome', r.nome));
                    info.appendChild(el('span', 'olo-avc-qta', T.quantita + ': ' + r.qta + ' · ' + r.prezzo));
                    riga.appendChild(info);
                    b.appendChild(riga);
                });
                if(d.righe.length > 3){ b.appendChild(el('div', 'olo-avc-altri', '+' + (d.righe.length - 3) + ' ' + T.altri)); }
                var tot = el('div', 'olo-avc-tot');
                tot.appendChild(el('span', '', T.subtotale + ' (' + d.conta + ' ' + (d.conta === 1 ? T.articolo : T.articoli) + ')'));
                tot.appendChild(el('b', '', d.subtotale));
                b.appendChild(tot);
                var az = el('div', 'olo-avc-azioni');
                var vai = el('a', 'olo-avc-vai', T.vai); vai.href = T.link;
                var cont = el('button', 'olo-avc-continua', T.continua); cont.type = 'button';
                cont.addEventListener('click', chiudi);
                az.appendChild(vai); az.appendChild(cont);
                b.appendChild(az);
                b.addEventListener('mouseenter', function(){ fermo = true; clearTimeout(timer); });
                b.addEventListener('mouseleave', function(){ fermo = false; attendi(); });
                b.addEventListener('focusin', function(){ fermo = true; clearTimeout(timer); });
                b.addEventListener('focusout', function(e){ if(!b.contains(e.relatedTarget)){ fermo = false; attendi(); } });
                document.body.appendChild(b);
                box = b;
                document.addEventListener('keydown', esc);
                requestAnimationFrame(function(){ requestAnimationFrame(function(){ b.classList.add('is-aperto'); }); });
                attendi();
            }
            if(window.jQuery){
                window.jQuery(document.body).on('added_to_cart', function(e, frammenti){
                    if(!frammenti){return}
                    var dati = frammenti['olo-avviso-carrello'];
                    if(!dati){return}
                    try { mostra(JSON.parse(dati)); } catch(err){}
                });
            }
            window.oloAvvisoCarrello = mostra;
            <?php if ( $subito ) : ?>
            if(document.readyState === 'loading'){ document.addEventListener('DOMContentLoaded', function(){ mostra(<?php echo wp_json_encode( $subito ); ?>); }); }
            else { mostra(<?php echo wp_json_encode( $subito ); ?>); }
            <?php endif; ?>
        })();
        </script>
        <?php
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
