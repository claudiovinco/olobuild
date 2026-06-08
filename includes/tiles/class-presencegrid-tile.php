<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tile PresenceGrid — griglia membri con stato online live.
 *
 * Famiglia E (Live / dati) · bucket C (nuovo).
 * Riferimento visivo + runtime: handoff-tile-speciali/temi/60-tema-community-gamer.html (#wall).
 *
 * Contratto §2:
 * - Parametrico: ogni numero/colore/testo è un campo editor con default (vedi presencegrid.js).
 * - Scoped per istanza: classi, @keyframes e selettori tutti prefissati con $uid (N istanze ok).
 * - SSR: la griglia è renderizzata server-side, già visibile, con lo stato (pallino + TESTO) per ogni
 *   membro. Il runtime JS solo arricchisce (poll endpoint debounced / flip demo casuale).
 * - prefers-reduced-motion: niente flip cosmetico, niente animazione ticker; lo stato resta leggibile.
 * - a11y: lo stato è comunicato anche a parole ("Online"/"Offline"), non solo col colore del pallino;
 *   avatar con role=img + aria-label; pallino aria-hidden.
 * - Performance: poll/flip girano solo dentro il viewport (IntersectionObserver), fetch debounced.
 * - Origine dati esplicita: manual | query (utenti WP) | endpoint (poll olo/v1). Vuoto → stato demo.
 */
class Olo_Presencegrid_Tile extends Olo_Tile_Base {

    protected $type     = 'presencegrid';
    protected $name     = 'Griglia Presenze';
    protected $icon     = 'dashicons-groups';
    protected $category = 'dynamic';

    protected $defaults = [
        'source'        => 'manual',
        'endpoint_url'  => '',
        'poll_interval' => 4000,

        'members' => [
            [ 'name' => 'KiraByte',   'avatar' => '', 'role' => 'Diamante', 'online' => true,  'color' => '' ],
            [ 'name' => 'nott2late',  'avatar' => '', 'role' => 'Platino',  'online' => true,  'color' => '' ],
            [ 'name' => 'pixelmom',   'avatar' => '', 'role' => 'Oro',      'online' => true,  'color' => '' ],
            [ 'name' => 'grumpyTank', 'avatar' => '', 'role' => 'Argento',  'online' => false, 'color' => '' ],
            [ 'name' => 'luna404',    'avatar' => '', 'role' => 'Maestro',  'online' => true,  'color' => '' ],
            [ 'name' => 'frostbyte',  'avatar' => '', 'role' => 'Bronzo',   'online' => false, 'color' => '' ],
            [ 'name' => 'wasd_',      'avatar' => '', 'role' => 'Oro',      'online' => true,  'color' => '' ],
            [ 'name' => 'glhf',       'avatar' => '', 'role' => 'Platino',  'online' => true,  'color' => '' ],
        ],

        'columns'        => 6,
        'columns_tablet' => 4,
        'columns_mobile' => 2,
        'gap'            => 14,
        'show_ranks'     => true,

        'online_label'  => 'Online',
        'offline_label' => 'Offline',

        'show_ticker'  => false,
        'ticker_text'  => '@KiraByte ha sbloccato Diamante I · torneo FIFA Cup domenica 21:00 · @pixelmom ha vinto MVP · nuovo record clan: +18.000 XP',
        'ticker_speed' => 26,
        'ticker_bg'    => '',
        'ticker_color' => '',

        'card_bg'      => '',
        'card_color'   => '',
        'role_color'   => '',
        'avatar_size'  => 54,
        'avatar_shape' => 'circle',
        'online_color' => '',
        'offline_color'=> '',
        'dot_size'     => 13,

        'name_size'   => 14,
        'name_weight' => '600',
        'role_size'   => 10,

        'card_radius'                => [ 'tl' => 14, 'tr' => 14, 'br' => 14, 'bl' => 14, 'linked' => true ],
        'card_radius_hover'          => null,
        'card_radius_hover_duration' => 300,
        'card_hover_effect'          => 'lift',

        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [];
    }

    /**
     * Normalizza un valore "image" (stringa URL oppure array { url, id }) a URL pulito.
     */
    private function img_url( $val ) {
        $url = is_array( $val ) ? ( $val['url'] ?? '' ) : $val;
        return is_string( $url ) ? trim( $url ) : '';
    }

    /**
     * Palette fallback per gli avatar-iniziale (token-first con fallback esadecimale).
     */
    private function avatar_palette() {
        return [
            'var(--olo-color-primary, #8B5CF6)',
            'var(--olo-color-secondary, #22D3EE)',
            'var(--olo-color-accent, #B6FF3D)',
            'var(--olo-color-danger, #FF4D9D)',
            'var(--olo-color-info, #6366F1)',
        ];
    }

    /**
     * Costruisce l'elenco membri server-side a partire dalla sorgente.
     * - manual / endpoint  → usa l'elenco curato (per endpoint è lo stato demo iniziale, poi il JS aggiorna).
     * - query              → utenti WordPress reali (avatar Gravatar, ruolo = display role, online = euristica).
     * Vuoto → elenco demo (stato placeholder), così la griglia non è mai vuota.
     *
     * @return array di [ name, avatar, role, online, color ]
     */
    private function resolve_members( $s ) {
        $source = $s['source'] ?? 'manual';

        if ( $source === 'query' ) {
            $users = get_users( [
                'number'  => 24,
                'orderby' => 'registered',
                'order'   => 'DESC',
                'fields'  => [ 'ID', 'display_name', 'user_login' ],
            ] );
            $out = [];
            foreach ( $users as $u ) {
                $roles = function_exists( 'get_userdata' ) ? ( get_userdata( $u->ID )->roles ?? [] ) : [];
                $role  = ! empty( $roles ) ? ucfirst( (string) reset( $roles ) ) : '';
                // Euristica "online": attività recente nei meta (best-effort, niente sessioni reali nel demo).
                $last   = (int) get_user_meta( $u->ID, 'olo_last_active', true );
                $online = $last ? ( ( time() - $last ) < 15 * MINUTE_IN_SECONDS ) : false;
                $out[] = [
                    'name'   => $u->display_name ?: $u->user_login,
                    'avatar' => get_avatar_url( $u->ID, [ 'size' => 96 ] ),
                    'role'   => $role,
                    'online' => $online,
                    'color'  => '',
                ];
            }
            if ( ! empty( $out ) ) {
                return $out;
            }
            // fallthrough al demo se non ci sono utenti
        }

        $members = is_array( $s['members'] ?? null ) ? $s['members'] : [];
        $members = array_values( array_filter( $members, function ( $m ) {
            return is_array( $m ) && trim( (string) ( $m['name'] ?? '' ) ) !== '';
        } ) );

        if ( ! empty( $members ) ) {
            return $members;
        }

        // Stato demo/placeholder (mai griglia vuota)
        return $this->defaults['members'];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-presencegrid-' . wp_rand( 10000, 99999 );

        // ── Origine dati ──
        $source        = in_array( $s['source'], [ 'manual', 'query', 'endpoint' ], true ) ? $s['source'] : 'manual';
        $endpoint_url  = esc_url_raw( trim( (string) ( $s['endpoint_url'] ?? '' ) ) );
        $poll_interval = max( 2000, min( 60000, intval( $s['poll_interval'] ) ) );

        $members = $this->resolve_members( $s );

        // ── Layout ──
        $cols      = max( 1, min( 10, intval( $s['columns'] ) ) );
        $cols_tab  = max( 1, min( 8,  intval( $s['columns_tablet'] ) ) );
        $cols_mob  = max( 1, min( 6,  intval( $s['columns_mobile'] ) ) );
        $gap       = max( 0, min( 40, intval( $s['gap'] ) ) );
        $show_rank = ! empty( $s['show_ranks'] );

        // ── Etichette stato (testo a11y) ──
        $on_label  = $s['online_label']  !== '' ? sanitize_text_field( $s['online_label'] )  : 'Online';
        $off_label = $s['offline_label'] !== '' ? sanitize_text_field( $s['offline_label'] ) : 'Offline';

        // ── Ticker ──
        $show_ticker  = ! empty( $s['show_ticker'] );
        $ticker_text  = trim( (string) ( $s['ticker_text'] ?? '' ) );
        $ticker_speed = max( 8, min( 80, intval( $s['ticker_speed'] ) ) );

        // ── Colori / aspetto (token-first con fallback) ──
        $card_bg   = $this->safe_color_css( $s['card_bg'] )   ?: 'var(--olo-color-surface, #120C22)';
        $card_clr  = $this->safe_color_css( $s['card_color'] ) ?: 'var(--olo-color-text, #EDEAFB)';
        $role_clr  = $this->safe_color_css( $s['role_color'] ) ?: 'var(--olo-color-muted, #948CC4)';
        $on_clr    = $this->safe_color_css( $s['online_color'] )  ?: 'var(--olo-color-success, #22C55E)';
        $off_clr   = $this->safe_color_css( $s['offline_color'] ) ?: 'var(--olo-color-muted, #5E568C)';
        $ticker_bg = $this->safe_color_css( $s['ticker_bg'] )    ?: 'var(--olo-color-surface, #120C22)';
        $ticker_cl = $this->safe_color_css( $s['ticker_color'] ) ?: 'var(--olo-color-muted, #948CC4)';
        $line_clr  = 'var(--olo-color-border, rgba(237,234,251,.12))';

        $av_size   = max( 32, min( 96, intval( $s['avatar_size'] ) ) );
        $av_shape  = in_array( $s['avatar_shape'], [ 'circle', 'rounded', 'square' ], true ) ? $s['avatar_shape'] : 'circle';
        $av_radius = $av_shape === 'circle' ? '50%' : ( $av_shape === 'rounded' ? '14px' : '0' );
        $dot_size  = max( 6, min( 22, intval( $s['dot_size'] ) ) );

        $name_size   = max( 10, min( 24, intval( $s['name_size'] ) ) );
        $name_weight = in_array( (string) $s['name_weight'], [ '400', '500', '600', '700' ], true ) ? $s['name_weight'] : '600';
        $role_size   = max( 8, min( 16, intval( $s['role_size'] ) ) );

        $hover_effect = in_array( $s['card_hover_effect'], [ 'none', 'lift', 'scale', 'glow' ], true ) ? $s['card_hover_effect'] : 'lift';

        // Raggio card (4 angoli) + hover
        $card_radius = $this->build_border_radius_css( $s['card_radius'] ?? [] );
        $card_rad_h  = isset( $s['card_radius_hover'] ) && $s['card_radius_hover'] !== null
            ? $this->build_border_radius_css( $s['card_radius_hover'] )
            : '';
        $card_rdur   = max( 0, intval( $s['card_radius_hover_duration'] ?? 300 ) );

        $palette = $this->avatar_palette();
        $pcount  = count( $palette );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                --pg-on:  <?php echo $on_clr; ?>;
                --pg-off: <?php echo $off_clr; ?>;
            }
            <?php if ( $show_ticker && $ticker_text !== '' ) : ?>
            @keyframes <?php echo $uid; ?>-tick {
                from { transform: translateX(0); }
                to   { transform: translateX(-50%); }
            }
            .<?php echo $uid; ?> .olo-pg-ticker {
                overflow: hidden;
                border: 1px solid <?php echo $line_clr; ?>;
                border-radius: 14px;
                background: <?php echo $ticker_bg; ?>;
                margin-bottom: 28px;
            }
            .<?php echo $uid; ?> .olo-pg-ticker-track {
                display: inline-flex;
                white-space: nowrap;
                padding: 13px 0;
                color: <?php echo $ticker_cl; ?>;
                animation: <?php echo $uid; ?>-tick <?php echo $ticker_speed; ?>s linear infinite;
                will-change: transform;
            }
            .<?php echo $uid; ?> .olo-pg-ticker:hover .olo-pg-ticker-track { animation-play-state: paused; }
            .<?php echo $uid; ?> .olo-pg-ticker-seg { padding: 0 28px; }
            <?php endif; ?>

            .<?php echo $uid; ?> .olo-pg-grid {
                display: grid;
                grid-template-columns: repeat(<?php echo $cols; ?>, minmax(0, 1fr));
                gap: <?php echo $gap; ?>px;
            }

            .<?php echo $uid; ?> .olo-pg-card {
                position: relative;
                background: <?php echo $card_bg; ?>;
                border: 1px solid <?php echo $line_clr; ?>;
                <?php if ( $card_radius ) : ?>border-radius: <?php echo $card_radius; ?>;<?php endif; ?>
                padding: 18px 14px;
                text-align: center;
                transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease<?php if ( $card_rad_h ) : ?>, border-radius <?php echo $card_rdur; ?>ms ease<?php endif; ?>;
            }
            <?php if ( $hover_effect === 'lift' ) : ?>
            .<?php echo $uid; ?> .olo-pg-card:hover { transform: translateY(-4px); border-color: var(--olo-color-primary, #8B5CF6); }
            <?php elseif ( $hover_effect === 'scale' ) : ?>
            .<?php echo $uid; ?> .olo-pg-card:hover { transform: scale(1.04); border-color: var(--olo-color-primary, #8B5CF6); }
            <?php elseif ( $hover_effect === 'glow' ) : ?>
            .<?php echo $uid; ?> .olo-pg-card:hover { border-color: var(--olo-color-primary, #8B5CF6); box-shadow: 0 0 0 1px var(--olo-color-primary, #8B5CF6), 0 8px 28px rgba(0,0,0,.28); }
            <?php endif; ?>
            <?php if ( $card_rad_h ) : ?>
            .<?php echo $uid; ?> .olo-pg-card:hover { border-radius: <?php echo $card_rad_h; ?>; }
            <?php endif; ?>

            .<?php echo $uid; ?> .olo-pg-avwrap {
                position: relative;
                width: <?php echo $av_size; ?>px;
                height: <?php echo $av_size; ?>px;
                margin: 0 auto 12px;
            }
            .<?php echo $uid; ?> .olo-pg-av {
                width: 100%;
                height: 100%;
                border-radius: <?php echo $av_radius; ?>;
                object-fit: cover;
                display: grid;
                place-items: center;
                font-weight: 700;
                font-size: <?php echo max( 14, (int) round( $av_size * 0.38 ) ); ?>px;
                color: #0A0712;
                overflow: hidden;
                -webkit-user-drag: none;
            }
            .<?php echo $uid; ?> .olo-pg-dot {
                position: absolute;
                right: -2px;
                bottom: -2px;
                width: <?php echo $dot_size; ?>px;
                height: <?php echo $dot_size; ?>px;
                border-radius: 50%;
                border: 3px solid <?php echo $card_bg; ?>;
                background: var(--pg-on);
                box-sizing: content-box;
            }
            .<?php echo $uid; ?> .olo-pg-card[data-online="0"] .olo-pg-dot { background: var(--pg-off); }

            .<?php echo $uid; ?> .olo-pg-name {
                font-weight: <?php echo $name_weight; ?>;
                font-size: <?php echo $name_size; ?>px;
                color: <?php echo $card_clr; ?>;
                line-height: 1.25;
                word-break: break-word;
            }
            .<?php echo $uid; ?> .olo-pg-role {
                font-size: <?php echo $role_size; ?>px;
                letter-spacing: .06em;
                color: <?php echo $role_clr; ?>;
                margin-top: 4px;
            }
            /* Stato testuale: visibile come micro-label (a11y reale, non solo colore) */
            .<?php echo $uid; ?> .olo-pg-status {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                font-size: <?php echo max( 9, $role_size - 1 ); ?>px;
                letter-spacing: .04em;
                margin-top: 6px;
                color: var(--pg-off);
            }
            .<?php echo $uid; ?> .olo-pg-card[data-online="1"] .olo-pg-status { color: var(--pg-on); }
            .<?php echo $uid; ?> .olo-pg-status::before {
                content: "";
                width: 6px; height: 6px; border-radius: 50%;
                background: currentColor;
            }

            .<?php echo $uid; ?> .olo-pg-card:focus-visible {
                outline: 2px solid var(--olo-color-primary, #8B5CF6);
                outline-offset: 2px;
            }

            /* Responsive — colonne per tablet / mobile */
            @media (max-width: 960px) {
                .<?php echo $uid; ?> .olo-pg-grid { grid-template-columns: repeat(<?php echo $cols_tab; ?>, minmax(0, 1fr)); }
            }
            @media (max-width: 600px) {
                .<?php echo $uid; ?> .olo-pg-grid { grid-template-columns: repeat(<?php echo $cols_mob; ?>, minmax(0, 1fr)); }
            }

            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?> .olo-pg-ticker-track { animation: none; }
                .<?php echo $uid; ?> .olo-pg-card { transition: none; }
            }
        </style>

        <div class="olo-pg <?php echo esc_attr( $uid ); ?>"
             data-source="<?php echo esc_attr( $source ); ?>"
             <?php if ( $source === 'endpoint' && $endpoint_url ) : ?>data-endpoint="<?php echo esc_attr( $endpoint_url ); ?>"<?php endif; ?>
             data-poll="<?php echo esc_attr( $poll_interval ); ?>"
             data-on-label="<?php echo esc_attr( $on_label ); ?>"
             data-off-label="<?php echo esc_attr( $off_label ); ?>"
             data-show-ranks="<?php echo $show_rank ? '1' : '0'; ?>">

            <?php if ( $show_ticker && $ticker_text !== '' ) :
                $ticker_html = esc_html( $ticker_text );
            ?>
            <div class="olo-pg-ticker" aria-hidden="true">
                <div class="olo-pg-ticker-track">
                    <span class="olo-pg-ticker-seg"><?php echo $ticker_html; ?></span>
                    <span class="olo-pg-ticker-seg"><?php echo $ticker_html; ?></span>
                </div>
            </div>
            <?php endif; ?>

            <ul class="olo-pg-grid" role="list" aria-label="<?php esc_attr_e( 'Membri e stato di presenza', 'olobuild' ); ?>">
                <?php foreach ( $members as $i => $m ) :
                    $name    = trim( (string) ( $m['name'] ?? '' ) );
                    if ( $name === '' ) { continue; }
                    $role    = trim( (string) ( $m['role'] ?? '' ) );
                    $online  = ! empty( $m['online'] );
                    $avatar  = $this->img_url( $m['avatar'] ?? '' );
                    $ovr_clr = $this->safe_color_css( $m['color'] ?? '' );
                    $bg_a    = $palette[ $i % $pcount ];
                    $bg_b    = $palette[ ( $i + 2 ) % $pcount ];
                    $av_bg   = $ovr_clr
                        ? $ovr_clr
                        : 'linear-gradient(135deg, ' . $bg_a . ', ' . $bg_b . ')';
                    $initial = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( mb_substr( $name, 0, 1 ) ) : strtoupper( substr( $name, 0, 1 ) );
                    $state_label = $online ? $on_label : $off_label;
                    // aria-label completo: nome + ruolo + stato testuale → screen reader legge tutto.
                    // La base (nome + ruolo) è salvata a parte così il runtime ricostruisce l'aria
                    // senza regex su input utente quando lo stato cambia.
                    $aria_base = '@' . $name . ( $role && $show_rank ? ', ' . $role : '' );
                    $aria      = $aria_base . ', ' . $state_label;
                ?>
                <li class="olo-pg-card" data-online="<?php echo $online ? '1' : '0'; ?>"
                    tabindex="0"
                    data-base-label="<?php echo esc_attr( $aria_base ); ?>"
                    aria-label="<?php echo esc_attr( $aria ); ?>">
                    <div class="olo-pg-avwrap">
                        <?php if ( $avatar !== '' ) : ?>
                        <img class="olo-pg-av" src="<?php echo esc_url( $avatar ); ?>"
                             alt="<?php echo esc_attr( $name ); ?>" loading="lazy" decoding="async" />
                        <?php else : ?>
                        <span class="olo-pg-av" style="background: <?php echo esc_attr( $av_bg ); ?>" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
                        <?php endif; ?>
                        <span class="olo-pg-dot" aria-hidden="true"></span>
                    </div>
                    <div class="olo-pg-name">@<?php echo esc_html( $name ); ?></div>
                    <?php if ( $role !== '' && $show_rank ) : ?>
                    <div class="olo-pg-role"><?php echo esc_html( $role ); ?></div>
                    <?php endif; ?>
                    <span class="olo-pg-status">
                        <span class="olo-pg-status-on"><?php echo esc_html( $on_label ); ?></span>
                        <span class="olo-pg-status-off"><?php echo esc_html( $off_label ); ?></span>
                    </span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <script>
        /* PresenceGrid — runtime scoped per istanza (rif. 60-tema-community-gamer.html).
           - SSR già completo: il JS solo AGGIORNA lo stato.
           - endpoint → poll debounced di olo/v1 (o URL custom); demo/manual/query → flip casuale.
           - idempotente (guard dataset), multi-istanza, si ferma fuori dal viewport (IO),
             rispetta prefers-reduced-motion (niente flip cosmetico). */
        (function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if ( ! root ) { return; }
            if ( root.dataset.oloPgInit ) { return; }   // una sola init per istanza
            root.dataset.oloPgInit = '1';

            // Mostra solo l'etichetta di stato corretta in ogni card (l'altra resta nel DOM per gli aggiornamenti).
            function syncStatusText( card ){
                var on  = card.getAttribute('data-online') === '1';
                var onE = card.querySelector('.olo-pg-status-on');
                var ofE = card.querySelector('.olo-pg-status-off');
                if ( onE ) { onE.style.display = on ? '' : 'none'; }
                if ( ofE ) { ofE.style.display = on ? 'none' : ''; }
            }
            var cards = root.querySelectorAll('.olo-pg-card');
            for ( var c = 0; c < cards.length; c++ ) { syncStatusText( cards[c] ); }

            var onLabel  = root.getAttribute('data-on-label')  || 'Online';
            var offLabel = root.getAttribute('data-off-label') || 'Offline';

            function applyOnline( card, online ){
                var cur = card.getAttribute('data-online') === '1';
                if ( cur === online ) { return; }
                card.setAttribute('data-online', online ? '1' : '0');
                syncStatusText( card );
                // Ricostruisce l'aria-label dalla base (nome + ruolo) + stato corrente.
                // Niente regex su input utente: la base è server-rendered in data-base-label.
                var base = card.getAttribute('data-base-label') || '';
                card.setAttribute('aria-label', base + ', ' + (online ? onLabel : offLabel));
            }

            var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
            var reduced = !!( rm && rm.matches );

            var source   = root.getAttribute('data-source') || 'manual';
            var endpoint = root.getAttribute('data-endpoint') || '';
            var poll     = parseInt( root.getAttribute('data-poll'), 10 ) || 4000;

            var timer = null, running = false, inFlight = false;

            // ── endpoint: poll debounced ──
            function indexCards(){
                var map = {};
                var list = root.querySelectorAll('.olo-pg-card');
                for ( var i = 0; i < list.length; i++ ) {
                    var nm = (list[i].querySelector('.olo-pg-name') || {}).textContent || '';
                    map[ nm.replace(/^@/, '').trim().toLowerCase() ] = list[i];
                }
                return map;
            }
            function fetchOnce(){
                if ( inFlight || ! endpoint || ! window.fetch ) { return; }
                inFlight = true;
                fetch( endpoint, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' } )
                    .then(function(r){ return r.ok ? r.json() : null; })
                    .then(function(data){
                        if ( ! data ) { return; }
                        var arr = Array.isArray(data) ? data : (data.members || data.data || []);
                        if ( ! Array.isArray(arr) ) { return; }
                        var map = indexCards();
                        for ( var j = 0; j < arr.length; j++ ) {
                            var it = arr[j] || {};
                            var key = String(it.name || it.username || it.login || '').replace(/^@/, '').trim().toLowerCase();
                            if ( key && map[key] ) {
                                applyOnline( map[key], !!(it.online || it.is_online || it.status === 'online') );
                            }
                        }
                    })
                    .catch(function(){ /* endpoint giù → resta lo stato SSR/demo, nessun errore visibile */ })
                    .then(function(){ inFlight = false; });
            }

            // ── demo / manual / query: flip casuale di una card (solo se motion consentito) ──
            function flipRandom(){
                var list = root.querySelectorAll('.olo-pg-card');
                if ( ! list.length ) { return; }
                var t = list[ Math.floor( Math.random() * list.length ) ];
                applyOnline( t, t.getAttribute('data-online') !== '1' );
            }

            function tick(){
                if ( ! running ) { return; }
                if ( source === 'endpoint' && endpoint ) {
                    fetchOnce();
                } else if ( ! reduced ) {
                    flipRandom();   // il flip è puramente cosmetico → off con reduced-motion
                }
            }

            function start(){
                if ( running ) { return; }
                // se non c'è niente da fare (reduced + nessun endpoint) non avviare alcun timer
                if ( source !== 'endpoint' && reduced ) { return; }
                running = true;
                if ( source === 'endpoint' && endpoint ) { fetchOnce(); }
                timer = setInterval( tick, poll );
            }
            function stop(){
                running = false;
                if ( timer ) { clearInterval( timer ); timer = null; }
            }

            // Performance: aggiorna solo quando la griglia è (almeno in parte) nel viewport.
            if ( 'IntersectionObserver' in window ) {
                var io = new IntersectionObserver(function( entries ){
                    for ( var k = 0; k < entries.length; k++ ) {
                        if ( entries[k].isIntersecting ) { start(); } else { stop(); }
                    }
                }, { threshold: 0 });
                io.observe( root );
            } else {
                start();
            }
            window.addEventListener('beforeunload', stop);
        })();
        </script>

        <?php
        // Border system (riuso helper come nel Marquee)
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid} .olo-pg-card", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid} .olo-pg-card", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) { echo ".{$uid} .olo-pg-card{{$border_css}}"; }
            echo $border_hover_css . $border_effect_css . '</style>';
        }

        return ob_get_clean();
    }
}
