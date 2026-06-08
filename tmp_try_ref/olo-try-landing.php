<?php
/**
 * Plugin Name: OLOtheme try.olotheme.com · Landing intro + tutorial builder
 * Description: Sostituisce la rendering della home con una landing page introduttiva accattivante (cosa è la sandbox, cosa puoi fare, CTA al builder). Inietta tutorial overlay al primo accesso al builder.
 * Version: 1.0.0
 * Author: OLOtheme
 *
 * Da installare in: ~/domains/olotheme.com/public_html/try/wp-content/mu-plugins/
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Try_Landing {

    const TUTORIAL_COOKIE = 'olo_sandbox_tutorial_seen';

    public function __construct() {
        // ── Try Home v2 (2026) ───────────────────────────────────────────────
        // La home è ora un TEMPLATE OLOBUILD NATIVO ("Try Home v2", id 493)
        // puntato da page_on_front (post 58 → _olo_template_id). Quindi NON
        // bypassiamo più il render del template né iniettiamo la vecchia landing
        // HTML/CSS (i metodi bypass_demo_master_on_home / render_landing /
        // inject_css restano nel file ma NON sono più agganciati).
        //
        // I CTA "Apri il builder" nel template usano il sentinel  #apri-builder :
        // lo sostituiamo qui con l'URL builder del clone corrente (per-visitatore),
        // DOPO che auto_render_template (prio 20) ha reso il template.
        add_filter( 'the_content', [ $this, 'inject_builder_url' ], 25 );

        // La home v2 ha la propria nav dark integrata: sopprimiamo l'header
        // globale Olobuild (107, chiaro) SOLO sulla front page. Le altre pagine
        // del sito try conservano l'header attivo.
        add_filter( 'option_olo_active_header', [ $this, 'suppress_header_on_front' ] );

        // La barra sandbox (olo-sandbox) è un doppione sulla landing v2: togliamo i
        // suoi asset SOLO sulla front page (prio 100 = dopo l'enqueue del plugin).
        add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_sandbox_bar_on_front' ], 100 );

        // Tutorial overlay nel builder (admin)
        add_action( 'admin_footer', [ $this, 'render_tutorial_overlay' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'tutorial_assets' ] );
    }

    /**
     * Toglie la barra sandbox in cima SOLO sulla front page (Try Home v2): la nav e
     * l'hero hanno già "Apri il builder", il timer di reset e i contatori, quindi la
     * toolbar guest sarebbe un doppione. Dequeue di CSS+JS → il div #olo-sandbox-banner
     * resta vuoto/invisibile. Sulle altre pagine e nel builder la barra resta attiva.
     */
    public function dequeue_sandbox_bar_on_front() {
        if ( is_admin() ) {
            return;
        }
        if ( function_exists( 'is_front_page' ) && is_front_page() && ! $this->is_preview_request() ) {
            wp_dequeue_style( 'olo-sandbox-banner' );
            wp_dequeue_script( 'olo-sandbox-banner' );
        }
    }

    /**
     * Sostituisce il sentinel #apri-builder con l'URL builder del clone sandbox
     * del visitatore corrente. Idempotente, no-op se il sentinel non c'è.
     */
    public function inject_builder_url( $content ) {
        if ( is_admin() ) {
            return $content;
        }
        if ( strpos( $content, '#apri-builder' ) === false ) {
            return $content;
        }
        $clone_id = class_exists( 'Olo_Sandbox_Clone' ) ? (int) Olo_Sandbox_Clone::current_clone_id() : 0;
        $url = $clone_id
            ? admin_url( 'admin.php?page=olobuilder-templates&template_id=' . $clone_id )
            : admin_url( 'admin.php?page=olobuilder-templates' );
        return str_replace( '#apri-builder', esc_url( $url ), $content );
    }

    /**
     * Filtro option_olo_active_header: 0 (= nessun header) sulla front page,
     * così la nav del template Try Home v2 non si duplica con l'header globale.
     */
    public function suppress_header_on_front( $value ) {
        if ( ! is_admin()
            && function_exists( 'is_front_page' ) && is_front_page()
            && ! $this->is_preview_request() ) {
            return 0;
        }
        return $value;
    }

    /**
     * SVG icon lucide-style 24x24 stroke currentColor.
     * Stile minimal-line coerente con Olobuild.
     */
    public static function icon( $name ) {
        $paths = [
            'sparkles'      => '<path d="M12 3l1.8 5.5L19 10l-5.2 1.5L12 17l-1.8-5.5L5 10l5.2-1.5z"/><path d="M5 3v4"/><path d="M3 5h4"/><path d="M19 17v4"/><path d="M17 19h4"/>',
            'mouse-pointer' => '<path d="M3 3l7.07 16.97 2.51-7.39 7.39-2.51z"/><path d="M13 13l6 6"/>',
            'panels'        => '<rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/>',
            'edit'          => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4z"/>',
            'sliders'       => '<line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>',
            'save'          => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
            'clock'         => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
            'user'          => '<circle cx="12" cy="8" r="4"/><path d="M5 20c0-3.866 3.134-7 7-7s7 3.134 7 7"/>',
            'grid'          => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
            'globe'         => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
        ];
        $path = $paths[ $name ] ?? '';
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
    }

    /* ============== HOME LANDING ============== */

    /**
     * Quando l'utente clicca "Reale" nel builder Olobuild, l'URL include
     * `?olo_preview=1` per indicare "voglio vedere il rendering effettivo del
     * template, salta la landing CTA". In quella modalità non bypassiamo il
     * template Olobuild né mostriamo la landing.
     */
    private function is_preview_request() {
        return ! empty( $_GET['olo_preview'] );
    }

    public function bypass_demo_master_on_home( $tiles, $template_id ) {
        // Quando la home (page_on_front) deve renderizzare, ritorna array vuoto
        // per disattivare il rendering Olobuild — il post_content prende il sopravvento.
        if ( ! is_front_page() ) return $tiles;
        if ( $this->is_preview_request() ) return $tiles;
        return null; // null = no tile render, content nativo
    }

    public function render_landing( $content ) {
        if ( ! is_front_page() ) return $content;
        if ( is_admin() ) return $content;
        if ( $this->is_preview_request() ) return $content;

        // Risolvi clone_id corrente
        $clone_id = 0;
        if ( class_exists( 'Olo_Sandbox_Clone' ) ) {
            $clone_id = (int) Olo_Sandbox_Clone::current_clone_id();
        }
        $builder_url = $clone_id
            ? admin_url( 'admin.php?page=olobuilder-templates&template_id=' . $clone_id )
            : admin_url( 'admin.php?page=olobuilder-templates' );

        $tile_count = class_exists( 'Olo_Sandbox_Config' )
            ? count( Olo_Sandbox_Config::allowed_tiles() )
            : 28;

        ob_start();
        ?>
<div class="olo-landing">

    <section class="olo-landing-hero">
        <div class="olo-landing-badge">🧪 Sandbox demo · 12h gratis</div>
        <h1>Prova OLObuild ora.<br>Niente registrazione, niente carta.</h1>
        <p class="olo-landing-lead">
            Hai un <strong>canvas vuoto</strong> e una copia personale del builder. Trascina i tile, costruisci, salva, vedi il risultato dal vivo.
            Dopo <strong>12 ore di inattività</strong> tutto si resetta — il tuo browser è la tua sandbox.
        </p>
        <p class="olo-landing-cta-row">
            <a class="olo-landing-cta olo-landing-cta--primary" href="<?php echo esc_url( $builder_url ); ?>">
                Apri il builder · inizia ora →
            </a>
            <a class="olo-landing-cta olo-landing-cta--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>#cosa-puoi-fare">
                Cosa puoi fare?
            </a>
        </p>
        <p class="olo-landing-reassure">
            ✓ Niente account · ✓ Niente email richiesta · ✓ Reset automatico dopo 12h · ✓ Tutto sul tuo browser
        </p>
    </section>

    <section class="olo-landing-section" id="cosa-puoi-fare">
        <h2>Cosa puoi fare in 12 ore</h2>
        <div class="olo-landing-steps">
            <article>
                <div class="olo-landing-step-icon"><?php echo self::icon( 'grid' ); ?></div>
                <h3>Costruisci da zero</h3>
                <p>Parti da <strong>canvas vuoto</strong>. Trascini <strong><?php echo (int) $tile_count; ?> tile demo</strong> dalla sidebar — headline, immagini, gallery, accordion, button, hero, form, tutto drag &amp; drop.</p>
            </article>
            <article>
                <div class="olo-landing-step-icon"><?php echo self::icon( 'edit' ); ?></div>
                <h3>Modifica al volo</h3>
                <p><strong>Doppio click</strong> su un testo, lo editi inline con toolbar floating. Niente switch tra "edit mode" e "preview".</p>
            </article>
            <article>
                <div class="olo-landing-step-icon"><?php echo self::icon( 'sliders' ); ?></div>
                <h3>Personalizza tutto</h3>
                <p>Inspector laterale: colori, padding, margini, animazioni, hover effects, responsive. Anteprima <strong>fedele al frontend</strong>.</p>
            </article>
            <article>
                <div class="olo-landing-step-icon"><?php echo self::icon( 'save' ); ?></div>
                <h3>Salva e visualizza</h3>
                <p>Click su <strong>"Salva"</strong> (o <kbd>Ctrl</kbd>+<kbd>S</kbd>) e vedi il risultato sul frontend. Modifica e ripeti. Senza limiti durante la sessione.</p>
            </article>
        </div>
    </section>

    <section class="olo-landing-section olo-landing-section--alt">
        <h2>Cosa puoi <em>provare</em> nell'assaggio demo</h2>
        <div class="olo-landing-features-grid">
            <div>✓ 28 tile selezionati (su 90 free totali)</div>
            <div>✓ Drag &amp; drop sulla griglia</div>
            <div>✓ Inline editing con doppio click</div>
            <div>✓ Inspector laterale completo</div>
            <div>✓ Anteprima responsive (mobile/tablet/desktop)</div>
            <div>✓ Animazioni d'ingresso e hover</div>
            <div>✓ Effetti testo (typewriter, glitch, ecc.)</div>
            <div>✓ Reset al template originale (un click)</div>
        </div>
    </section>

    <section class="olo-landing-section olo-landing-section--limits">
        <h2>Limiti della sandbox</h2>
        <div class="olo-landing-limits">
            <article class="olo-landing-limit">
                <div class="olo-landing-limit-icon"><?php echo self::icon( 'clock' ); ?></div>
                <strong>12 ore di inattività</strong>
                <p>Dopo 12h senza modifiche il tuo template viene cancellato. Torna prima e riparti da dove avevi lasciato.</p>
            </article>
            <article class="olo-landing-limit">
                <div class="olo-landing-limit-icon"><?php echo self::icon( 'user' ); ?></div>
                <strong>Sandbox personale</strong>
                <p>Ognuno ha il <em>suo</em> template. Quello che modifichi tu non lo vede nessun altro. Niente registrazione.</p>
            </article>
            <article class="olo-landing-limit">
                <div class="olo-landing-limit-icon"><?php echo self::icon( 'grid' ); ?></div>
                <strong>28 tile su 90+22</strong>
                <p>Selezione rappresentativa per la demo. Nella versione completa di OLObuild ne hai 90 free + 22 Pro.</p>
            </article>
            <article class="olo-landing-limit">
                <div class="olo-landing-limit-icon"><?php echo self::icon( 'globe' ); ?></div>
                <strong>Solo template demo</strong>
                <p>Non puoi creare pagine nuove o cambiare header/footer del sito. Modifichi il template che ti diamo.</p>
            </article>
        </div>
    </section>

    <section class="olo-landing-section olo-landing-section--final">
        <h2>Convinto? Installa OLObuild gratis</h2>
        <p>
            OLObuild è <strong>gratis per sempre</strong> con tutte le 90 tile free, 11 effetti testo, 36 animazioni d'ingresso, form builder multi-step.
            La sandbox ti dà un assaggio. <strong>Sul tuo WordPress hai tutto</strong>.
        </p>
        <p class="olo-landing-cta-row">
            <a class="olo-landing-cta olo-landing-cta--primary" href="https://olotheme.com/prodotti/olobuild/" target="_blank" rel="noopener">
                Vai a olotheme.com → installa OLObuild
            </a>
            <a class="olo-landing-cta olo-landing-cta--ghost" href="<?php echo esc_url( $builder_url ); ?>">
                Continua nella sandbox →
            </a>
        </p>
    </section>

</div>
        <?php
        return ob_get_clean();
    }

    public function inject_css() {
        if ( is_admin() ) return;
        if ( ! is_front_page() ) return;
        if ( $this->is_preview_request() ) return;
        ?>
<style id="olo-landing-css">
.olo-landing {
    max-width: 1100px;
    margin: 0 auto;
    padding: 40px 24px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #475569;
}
.olo-landing h1, .olo-landing h2, .olo-landing h3 { color: #0f172a; }

/* Hero */
.olo-landing-hero { text-align: center; padding: 48px 16px 56px; }
.olo-landing-badge {
    display: inline-block;
    background: #fef3c7;
    color: #92400e;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 18px;
    letter-spacing: 0.04em;
}
.olo-landing-hero h1 {
    font-size: clamp(2rem, 4.5vw, 3.2rem);
    line-height: 1.15;
    font-weight: 800;
    margin: 0 0 18px;
    letter-spacing: -0.02em;
}
.olo-landing-lead {
    font-size: clamp(1.05rem, 1.6vw, 1.2rem);
    max-width: 720px;
    margin: 0 auto 28px;
    line-height: 1.55;
    color: #475569;
}
.olo-landing-cta-row {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
    margin: 0 0 18px;
}
.olo-landing-cta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 14px 26px;
    border-radius: 999px;
    font-weight: 700;
    text-decoration: none;
    font-size: 14.5px;
    transition: transform .15s, box-shadow .15s;
}
.olo-landing-cta--primary {
    background: linear-gradient(135deg, #f97316, #ef4444);
    color: #fff !important;
    box-shadow: 0 8px 24px rgba(239,68,68,.32);
}
.olo-landing-cta--primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(239,68,68,.42);
}
.olo-landing-cta--ghost {
    background: transparent;
    color: #0f172a !important;
    border: 1.5px solid #e2e8f0;
}
.olo-landing-cta--ghost:hover { border-color: #0f172a; }
/* Override per sezioni con sfondo scuro */
.olo-landing-section--final .olo-landing-cta--ghost {
    color: #f1f5f9 !important;
    border-color: rgba(255, 255, 255, 0.35);
}
.olo-landing-section--final .olo-landing-cta--ghost:hover {
    border-color: #f1f5f9;
    background: rgba(255, 255, 255, 0.08);
}
.olo-landing-reassure {
    font-size: 13px;
    color: #64748b;
    margin: 8px 0 0;
}

/* Sections */
.olo-landing-section {
    padding: 48px 24px;
    margin: 0 -24px;
}
.olo-landing-section h2 {
    font-size: clamp(1.4rem, 2.6vw, 1.9rem);
    text-align: center;
    margin: 0 0 36px;
    font-weight: 800;
}
.olo-landing-section--alt { background: #fafbfc; border-radius: 16px; }
.olo-landing-section--limits { background: #fef9c3; border-radius: 16px; }
.olo-landing-section--final { background: linear-gradient(135deg, #1e293b, #0f172a); color: #cbd5e1; border-radius: 16px; text-align: center; }
.olo-landing-section--final h2 { color: #f1f5f9; }
.olo-landing-section--final p { max-width: 640px; margin: 0 auto 24px; }

/* Steps grid */
.olo-landing-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 18px;
}
.olo-landing-steps article {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 24px 20px;
    text-align: center;
}
.olo-landing-step-icon {
    width: 40px;
    height: 40px;
    margin: 0 auto 14px;
    color: #ef4444;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.olo-landing-step-icon svg { width: 100%; height: 100%; }
.olo-landing-steps h3 { margin: 0 0 8px; font-size: 1.05rem; }
.olo-landing-steps p { margin: 0; font-size: 13.5px; line-height: 1.55; color: #475569; }
.olo-landing-steps kbd {
    background: #f1f5f9;
    padding: 1px 6px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    font-size: 11px;
    font-family: ui-monospace, monospace;
}

/* Features grid */
.olo-landing-features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 10px;
    max-width: 800px;
    margin: 0 auto;
}
.olo-landing-features-grid div {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 14px;
    font-size: 13.5px;
    color: #475569;
}

/* Limits */
.olo-landing-limits {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 18px;
    max-width: 900px;
    margin: 0 auto;
}
.olo-landing-limit {
    background: #fff;
    border: 1px solid #fbbf24;
    border-radius: 10px;
    padding: 20px 18px;
}
.olo-landing-limit-icon {
    width: 28px;
    height: 28px;
    color: #92400e;
    margin-bottom: 10px;
}
.olo-landing-limit-icon svg { width: 100%; height: 100%; }
.olo-landing-limit strong {
    display: block;
    color: #92400e;
    margin-bottom: 6px;
    font-size: 14px;
}
.olo-landing-limit p { margin: 0; font-size: 13px; color: #78350f; line-height: 1.5; }
</style>
        <?php
    }

    /* ============== TUTORIAL OVERLAY BUILDER ============== */

    public function tutorial_assets( $hook ) {
        // Solo nella pagina builder
        if ( strpos( (string) $hook, 'olobuilder-templates' ) === false ) return;
        // Solo per guest sandbox
        if ( ! $this->is_sandbox_guest() ) return;
        // Solo se non già visto
        if ( isset( $_COOKIE[ self::TUTORIAL_COOKIE ] ) && $_COOKIE[ self::TUTORIAL_COOKIE ] === '1' ) return;
        // Flag che il tutorial deve essere mostrato (letto da render_tutorial_overlay)
        $GLOBALS['olo_tutorial_show'] = true;
    }

    public function render_tutorial_overlay() {
        if ( empty( $GLOBALS['olo_tutorial_show'] ) ) return;
        ?>
<div class="olo-tutorial-overlay" id="olo-tutorial" role="dialog" aria-labelledby="olo-tutorial-title">
    <div class="olo-tutorial-dialog">
        <button type="button" class="olo-tutorial-skip" aria-label="Salta il tutorial" onclick="oloTutorialDismiss()">Salta</button>

        <div class="olo-tutorial-step is-active" data-step="1">
            <div class="olo-tutorial-icon"><?php echo self::icon( 'sparkles' ); ?></div>
            <h2 id="olo-tutorial-title">Benvenuto in OLObuild · Sandbox</h2>
            <p>Hai un <strong>canvas vuoto</strong> e 12 ore per riempirlo. Quello che modifichi resta tuo, finché torni — poi si resetta automaticamente.</p>
            <p class="olo-tutorial-hint">6 step rapidi e poi al lavoro.</p>
        </div>

        <div class="olo-tutorial-step" data-step="2">
            <div class="olo-tutorial-icon"><?php echo self::icon( 'mouse-pointer' ); ?></div>
            <h2>Trascina un tile dalla sidebar</h2>
            <p>A <strong>sinistra</strong> hai 28 tile demo. Trascinali sul canvas centrale per costruire la pagina. Riprovi finché non ti piace.</p>
            <p class="olo-tutorial-hint">Tip: puoi cliccare invece di trascinare — il tile va in fondo automaticamente.</p>
        </div>

        <div class="olo-tutorial-step" data-step="3">
            <div class="olo-tutorial-icon"><?php echo self::icon( 'panels' ); ?></div>
            <h2>I pannelli laterali sono collassabili</h2>
            <p>Sia il pannello <strong>sinistro (tile)</strong> sia quello <strong>destro (Inspector)</strong> li puoi chiudere per avere più spazio sul canvas. Cerca le freccette di collasso ai loro bordi.</p>
            <p class="olo-tutorial-hint">L'Inspector destro <strong>ritorna automaticamente</strong> appena clicchi un tile nel canvas — non serve riaprirlo a mano.</p>
        </div>

        <div class="olo-tutorial-step" data-step="4">
            <div class="olo-tutorial-icon"><?php echo self::icon( 'edit' ); ?></div>
            <h2>Doppio click per editare i testi</h2>
            <p>Su qualunque testo: <strong>doppio click</strong> e lo modifichi inline. Appare una toolbar floating per grassetto, link, lista, ecc.</p>
            <p class="olo-tutorial-hint">Click fuori dal blocco per uscire dall'editing.</p>
        </div>

        <div class="olo-tutorial-step" data-step="5">
            <div class="olo-tutorial-icon"><?php echo self::icon( 'sliders' ); ?></div>
            <h2>L'Inspector è il tuo amico</h2>
            <p>Quando selezioni un tile, l'<strong>Inspector a destra</strong> mostra tutte le opzioni: colori, padding, animazioni, hover, responsive.</p>
            <p class="olo-tutorial-hint">Le modifiche sono live — vedi il risultato sul canvas mentre cambi i valori.</p>
        </div>

        <div class="olo-tutorial-step" data-step="6">
            <div class="olo-tutorial-icon"><?php echo self::icon( 'save' ); ?></div>
            <h2>Salva e visualizza</h2>
            <p>In alto a destra c'è <strong>"Salva"</strong> (o <kbd>Ctrl</kbd>+<kbd>S</kbd>). Salvi, apri il sito in altra tab, vedi il risultato.</p>
            <p class="olo-tutorial-hint">Pulsante "Resetta" se vuoi tornare al template originale in qualunque momento.</p>
        </div>

        <div class="olo-tutorial-progress">
            <span class="olo-tutorial-dot is-active"></span>
            <span class="olo-tutorial-dot"></span>
            <span class="olo-tutorial-dot"></span>
            <span class="olo-tutorial-dot"></span>
            <span class="olo-tutorial-dot"></span>
            <span class="olo-tutorial-dot"></span>
        </div>

        <div class="olo-tutorial-nav">
            <button type="button" class="olo-tutorial-btn olo-tutorial-btn--back" onclick="oloTutorialBack()" disabled>← Indietro</button>
            <button type="button" class="olo-tutorial-btn olo-tutorial-btn--next" onclick="oloTutorialNext()">Avanti →</button>
        </div>
    </div>
</div>

<style id="olo-tutorial-css">
.olo-tutorial-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: olo-tut-fadein .25s ease-out;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
.olo-tutorial-overlay.is-hidden { display: none !important; }
@keyframes olo-tut-fadein { from { opacity: 0 } to { opacity: 1 } }

.olo-tutorial-dialog {
    background: #fff;
    border-radius: 16px;
    max-width: 480px;
    width: 100%;
    padding: 36px 32px 24px;
    text-align: center;
    box-shadow: 0 30px 80px rgba(0,0,0,0.4);
    position: relative;
    animation: olo-tut-slidein .3s cubic-bezier(.16,1,.3,1);
}
@keyframes olo-tut-slidein {
    from { opacity: 0; transform: translateY(20px) scale(.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.olo-tutorial-skip {
    position: absolute;
    top: 14px;
    right: 16px;
    background: transparent;
    border: 0;
    color: #94a3b8;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    transition: color .15s, background .15s;
}
.olo-tutorial-skip:hover { color: #0f172a; background: #f1f5f9; }

.olo-tutorial-step { display: none; }
.olo-tutorial-step.is-active { display: block; animation: olo-tut-stepin .3s ease-out; }
@keyframes olo-tut-stepin {
    from { opacity: 0; transform: translateX(10px); }
    to { opacity: 1; transform: translateX(0); }
}

.olo-tutorial-icon {
    width: 56px;
    height: 56px;
    margin: 0 auto 18px;
    color: #ef4444;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #fef2f2;
    border-radius: 50%;
    padding: 12px;
}
.olo-tutorial-icon svg { width: 100%; height: 100%; }
.olo-tutorial-step h2 {
    font-size: 22px;
    color: #0f172a;
    font-weight: 700;
    margin: 0 0 12px;
    line-height: 1.25;
}
.olo-tutorial-step p {
    color: #475569;
    line-height: 1.55;
    font-size: 15px;
    margin: 0 0 12px;
}
.olo-tutorial-step strong { color: #0f172a; }
.olo-tutorial-step kbd {
    background: #f1f5f9;
    padding: 1px 6px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    font-size: 12px;
    font-family: ui-monospace, monospace;
    color: #475569;
}
.olo-tutorial-hint {
    font-size: 13px !important;
    color: #94a3b8 !important;
    font-style: italic;
    margin-top: 8px !important;
}

.olo-tutorial-progress {
    display: flex;
    gap: 6px;
    justify-content: center;
    margin: 28px 0 18px;
}
.olo-tutorial-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #e2e8f0;
    transition: background .2s;
}
.olo-tutorial-dot.is-active { background: #ef4444; }
.olo-tutorial-dot.is-done { background: #94a3b8; }

.olo-tutorial-nav {
    display: flex;
    gap: 10px;
    justify-content: space-between;
}
.olo-tutorial-btn {
    border: 0;
    padding: 11px 22px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 13.5px;
    cursor: pointer;
    transition: background .15s, transform .1s;
    font-family: inherit;
}
.olo-tutorial-btn--back {
    background: #f1f5f9;
    color: #475569;
}
.olo-tutorial-btn--back:not(:disabled):hover { background: #e2e8f0; }
.olo-tutorial-btn--back:disabled { opacity: .4; cursor: not-allowed; }
.olo-tutorial-btn--next {
    background: linear-gradient(135deg, #f97316, #ef4444);
    color: #fff;
}
.olo-tutorial-btn--next:hover { transform: translateY(-1px); filter: brightness(1.05); }
</style>

<script>
(function(){
    var TOTAL = 6;
    var current = 1;

    window.oloTutorialNext = function(){
        if (current < TOTAL) {
            current++;
            oloTutorialUpdate();
        } else {
            oloTutorialDismiss();
        }
    };
    window.oloTutorialBack = function(){
        if (current > 1) {
            current--;
            oloTutorialUpdate();
        }
    };
    window.oloTutorialDismiss = function(){
        var overlay = document.getElementById('olo-tutorial');
        if (overlay) overlay.classList.add('is-hidden');
        // Cookie persistence
        document.cookie = '<?php echo esc_js( self::TUTORIAL_COOKIE ); ?>=1;path=/;max-age=' + (60*60*24*365) + ';samesite=Lax';
    };

    function oloTutorialUpdate(){
        var steps = document.querySelectorAll('.olo-tutorial-step');
        var dots  = document.querySelectorAll('.olo-tutorial-dot');
        steps.forEach(function(s){
            s.classList.toggle('is-active', parseInt(s.dataset.step, 10) === current);
        });
        dots.forEach(function(d, i){
            d.classList.remove('is-active', 'is-done');
            if (i + 1 < current) d.classList.add('is-done');
            if (i + 1 === current) d.classList.add('is-active');
        });
        var back = document.querySelector('.olo-tutorial-btn--back');
        var next = document.querySelector('.olo-tutorial-btn--next');
        if (back) back.disabled = (current === 1);
        if (next) next.textContent = (current === TOTAL) ? "Inizia a costruire →" : "Avanti →";
    }

    // ESC per saltare
    document.addEventListener('keydown', function(e){
        var overlay = document.getElementById('olo-tutorial');
        if (!overlay || overlay.classList.contains('is-hidden')) return;
        if (e.key === 'Escape') oloTutorialDismiss();
        if (e.key === 'ArrowRight' || e.key === 'Enter') oloTutorialNext();
        if (e.key === 'ArrowLeft') oloTutorialBack();
    });
})();
</script>
        <?php
    }

    private function is_sandbox_guest() {
        $user = wp_get_current_user();
        return $user && $user->ID && class_exists( 'Olo_Sandbox_Config' )
            && $user->user_login === Olo_Sandbox_Config::GUEST_LOGIN;
    }
}

new Olo_Try_Landing();
