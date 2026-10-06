<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Html_Tile extends Olobuild_Tile_Base {

    protected $type     = 'html';
    protected $name     = 'HTML / Codice';
    protected $icon     = 'dashicons-editor-code';
    protected $category = 'text';
    protected $defaults = [
        'html_content' => '<div style="padding:20px;text-align:center;color:var(--olo-color-text-faint, #9ca3af);">Custom HTML block</div>',
        'sandbox'      => false,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'html_content', 'type' => 'textarea', 'label' => 'HTML Content' ],
            [ 'key' => 'sandbox',      'type' => 'toggle',   'label' => 'Sandbox (iframe)' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        ob_start();

        if ( $s['sandbox'] ) {
            // L'iframe isolato prima era alto 150 px fissi (l'altezza di serie di un iframe:
            // il contenuto veniva tagliato o lasciava un vuoto) e dentro non aveva né i
            // caratteri né i colori del tema. Ora il documento si misura e lo dice alla
            // pagina, che gli passa token, font e colore del punto in cui sta (ponte_sandbox()).
            // Id diverso anche fra richieste diverse (il canvas del builder rende le tile a parte),
            // ma ricavato dalle impostazioni, non dal caso: con uniqid() l'HTML cambiava a ogni
            // render e il banco lo segnava sempre come modificato. Due copie identiche possono
            // avere lo stesso id: il ponte riconosce l'iframe dalla sua finestra, non dall'id.
            $frame_id = 'olo-html-' . wp_unique_id() . '-' . substr( md5( wp_json_encode( $s ) ), 0, 6 );
            ?>
            <div class="olo-html uk-panel">
                <iframe
                    id="<?php echo esc_attr( $frame_id ); ?>"
                    sandbox="allow-scripts"
                    title="<?php echo esc_attr( olobuild_t( 'Contenuto HTML' ) ); ?>"
                    srcdoc="<?php echo htmlspecialchars( $this->documento_sandbox( (string) $s['html_content'], $frame_id ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute-escaped with double_encode=true on purpose: esc_attr() leaves existing entities as they are, so an «&lt;» written in the HTML reached the iframe as a real «<» ?>"
                    style="display:block;width:100%;min-height:100px;border:none;"
                    loading="lazy"
                ></iframe>
            </div>
            <script><?php echo self::ponte_sandbox(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed JS literal defined in this class, no user data ?></script>
            <?php
        } else {
            // Il tile "HTML / Codice" esiste apposta per inserire HTML grezzo
            // (script, style, form, iframe inclusi). Il contenuto viene scritto
            // SOLO da chi ha capability di editare i template Olobuild → trust.
            // Per casi che vogliono ri-sanitizzare opt-in c'è il filter
            // `olobuild_html_tile_output`.
            $html = apply_filters( 'olobuild_html_tile_output', $s['html_content'], $s );
            ?>
            <div class="olo-html uk-panel">
                <?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- raw HTML by design: html_content is stored unfiltered only for users with the unfiltered_html capability, everyone else is forced through wp_kses_post() on save (Olobuild_Rest_Api::sanitize_unfiltered_tile_fields); opt-in re-sanitization via the olobuild_html_tile_output filter. ?>
            </div>
            <?php
        }

        return ob_get_clean();
    }

    /**
     * Il documento dell'iframe in sandbox: l'HTML dell'utente con, in testa, una base a
     * specificità zero (:where, vince qualunque regola dell'utente) e il piccolo script che
     * misura l'altezza e riceve dalla pagina token, font e colore del tema.
     * Il margine di 8 px del body si toglie: fuori dalla sandbox l'HTML non lo aveva.
     * Un documento completo (con <html>/<head>) resta tale: la testa si aggiunge dentro.
     *
     * @param string $html     HTML salvato nella tile (lo stesso del ramo senza sandbox).
     * @param string $frame_id Id dell'iframe, con cui i messaggi si riconoscono.
     * @return string Documento da passare (con esc_attr) a srcdoc.
     */
    private function documento_sandbox( $html, $frame_id ) {
        $testa = '<meta charset="utf-8">'
            . '<style>:where(body){margin:0}:where(h1,h2,h3,h4,h5,h6){font-family:var(--olo-font-family-heading, inherit)}:where(a){color:var(--olo-color-link, inherit)}</style>'
            . '<script>' . str_replace( '__ID__', wp_json_encode( (string) $frame_id ), self::SCRIPT_INTERNO ) . '</script>';

        if ( preg_match( '/<head\b[^>]*>/i', $html, $m, PREG_OFFSET_CAPTURE ) ) {
            $dopo = $m[0][1] + strlen( $m[0][0] );
            return substr( $html, 0, $dopo ) . $testa . substr( $html, $dopo );
        }
        if ( preg_match( '/<html\b[^>]*>/i', $html, $m, PREG_OFFSET_CAPTURE ) ) {
            $dopo = $m[0][1] + strlen( $m[0][0] );
            return substr( $html, 0, $dopo ) . '<head>' . $testa . '</head>' . substr( $html, $dopo );
        }
        return '<!DOCTYPE html><html><head>' . $testa . '</head><body>' . $html . '</body></html>';
    }

    /**
     * Script DENTRO l'iframe. Dice alla pagina l'altezza del documento (quella del box di
     * <html>, che segue il contenuto anche quando l'iframe è più alto; se l'utente fissa
     * html/body al 100% vale lo scrollHeight del body) e applica tema e font che riceve.
     * Niente && (WordPress lo rovina negli script in linea): if annidati.
     */
    const SCRIPT_INTERNO = '(function(){var id=__ID__,ultima=0;'
        . 'function misura(){var h=Math.ceil(document.documentElement.getBoundingClientRect().height);'
        . 'if(document.body){h=Math.max(h,document.body.scrollHeight);}'
        . 'if(h!==ultima){ultima=h;parent.postMessage({oloSandbox:id,altezza:h},"*");}}'
        . 'window.addEventListener("message",function(e){var d=e.data;if(e.source!==parent){return;}if(!d){return;}if(d.oloSandbox!==id){return;}'
        . 'if(d.css){var st=document.createElement("style");st.textContent=d.css;document.head.insertBefore(st,document.head.firstChild);}'
        . 'if(d.font){try{var f=new FontFace(d.font.famiglia,d.font.dati,d.font.desc);document.fonts.add(f);f.load().then(misura,misura);}catch(err){}}'
        . 'ultima=0;misura();});'
        . 'parent.postMessage({oloSandbox:id,pronto:1},"*");'
        . 'window.addEventListener("load",misura);'
        . 'if(window.ResizeObserver){new ResizeObserver(misura).observe(document.documentElement);}'
        . 'setTimeout(misura,60);})();';

    /**
     * Script della PAGINA (uno solo anche con più tile: window.oloSandboxPonte). Alla richiesta
     * «pronto» di un iframe risponde con i token --olo-* e font, colore, corpo e interlinea
     * calcolati dove la tile sta (come li erediterebbe l'HTML fuori dalla sandbox: anche in
     * una sezione scura o in modalità scura); poi i file dei font del testo e dei titoli.
     * I font passano come dati (FontFace): la sandbox ha un'origine opaca e i file serviti
     * dal sito senza intestazioni CORS da lì verrebbero rifiutati. All'«altezza» ridimensiona
     * l'iframe (al massimo 10000 px: un contenuto alto 100vh+x non cresce all'infinito).
     * L'iframe si riconosce dalla finestra che scrive (e.source), poi deve combaciare l'id: due
     * copie identiche della tile rese a parte nel canvas hanno lo stesso id, e getElementById
     * avrebbe trovato sempre la prima. Niente && (vedi sopra).
     */
    private static function ponte_sandbox() {
        return '(function(){if(window.oloSandboxPonte){return;}window.oloSandboxPonte=1;var FILE={};'
            . 'var BASE=["--olo-color-primary","--olo-color-primary-contrast","--olo-color-secondary","--olo-color-secondary-contrast","--olo-color-accent","--olo-color-text","--olo-color-text-muted","--olo-color-background","--olo-color-surface","--olo-color-border","--olo-color-muted","--olo-color-link","--olo-color-light","--olo-color-dark","--olo-font-family","--olo-font-family-heading"];'
            . 'function tema(el){var cs=getComputedStyle(el),visti={},dich=[],i;'
            . 'function metti(p){if(visti[p]){return;}visti[p]=1;var v=cs.getPropertyValue(p).trim();if(v){dich.push(p+":"+v);}}'
            . 'for(i=0;i<cs.length;i++){if(cs[i].indexOf("--olo-")===0){metti(cs[i]);}}BASE.forEach(metti);'
            . 'return ":root{"+dich.join(";")+"}:where(body){font-family:"+cs.fontFamily+";color:"+cs.color+";font-size:"+cs.fontSize+";line-height:"+cs.lineHeight+"}";}'
            . 'function prima(stack){return String(stack||"").split(",")[0].replace(/["\']/g,"").trim().toLowerCase();}'
            // Solo il sottoinsieme latino (unicode-range che parte da U+0; il browser lo riscrive
            // «U+0-FF»), una volta per file+peso+stile anche se il foglio compare due volte.
            . 'function caratteri(el){var cs=getComputedStyle(el),nomi=[prima(cs.fontFamily),prima(cs.getPropertyValue("--olo-font-family-heading"))],out=[],visti={};'
            . 'function giro(regole,base){for(var i=0;i<regole.length;i++){var r=regole[i];'
            . 'if(r.styleSheet){try{giro(r.styleSheet.cssRules,r.styleSheet.href||base);}catch(e){}continue;}'
            . 'if(r.type!==5){continue;}var fam=r.style.getPropertyValue("font-family");if(nomi.indexOf(prima(fam))===-1){continue;}'
            . 'var ur=r.style.getPropertyValue("unicode-range");if(ur){if(!/^\s*U\+0+-/i.test(ur)){continue;}}'
            . 'var m=/url\(\s*["\']?([^"\')]+)["\']?\s*\)/.exec(r.style.getPropertyValue("src"));if(!m){continue;}'
            . 'var st=r.style.getPropertyValue("font-style")||"normal",pe=r.style.getPropertyValue("font-weight")||"normal",u;'
            . 'try{u=new URL(m[1],base).href;}catch(e){continue;}if(visti[u+pe+st]){continue;}visti[u+pe+st]=1;'
            . 'out.push({famiglia:fam.replace(/["\']/g,"").trim(),url:u,desc:{style:st,weight:pe,unicodeRange:ur||"U+0-10FFFF"}});}}'
            . 'for(var s=0;s<document.styleSheets.length;s++){var ss=document.styleSheets[s];try{giro(ss.cssRules,ss.href||location.href);}catch(e){}}'
            . 'return out;}'
            . 'function servi(f,id){if(!f.contentWindow){return;}var w=f.parentNode||f;'
            . 'f.contentWindow.postMessage({oloSandbox:id,css:tema(w)},"*");'
            . 'if(!window.fetch){return;}if(!window.FontFace){return;}'
            . 'caratteri(w).forEach(function(c){if(!FILE[c.url]){FILE[c.url]=fetch(c.url).then(function(r){return r.arrayBuffer();});}FILE[c.url].then(function(buf){if(f.contentWindow){f.contentWindow.postMessage({oloSandbox:id,font:{famiglia:c.famiglia,dati:buf,desc:c.desc}},"*");}}).catch(function(){});});}'
            . 'window.addEventListener("message",function(e){var d=e.data;if(!d){return;}if(typeof d!=="object"){return;}if(!d.oloSandbox){return;}'
            . 'var f=null,tutti=document.querySelectorAll("iframe[id^=\'olo-html-\']");for(var k=0;k<tutti.length;k++){if(tutti[k].contentWindow===e.source){f=tutti[k];}}'
            . 'if(!f){return;}if(f.id!==String(d.oloSandbox)){return;}'
            . 'if(d.pronto){servi(f,d.oloSandbox);}'
            . 'var h=parseInt(d.altezza,10);if(h>0){f.style.height=Math.min(h,10000)+"px";f.style.minHeight="0";}});'
            // Iframe già pronti prima che questo script girasse (tile idratate in un altro ordine).
            . 'document.querySelectorAll("iframe[id^=\'olo-html-\']").forEach(function(f){servi(f,f.id);});'
            . '})();';
    }
}
