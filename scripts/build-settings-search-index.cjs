#!/usr/bin/env node
/**
 * Genera src/config/settingsSearchIndex.js estraendo dai 17 *Tab.vue della
 * Configurazione le voci ricercabili a livello di campo:
 *   - ogni <label>{{ t('…') }}</label> con l'eventuale .hint adiacente
 *   - ogni <h3>{{ t('…') }}</h3> di card (sezione) con la descrizione <p> adiacente
 * L'indice alimenta la ricerca profonda della SettingsApp (sidebar + vista
 * risultati + "Vai al campo"). Viene rigenerato a ogni build admin dal hook
 * buildStart in vite.config.admin.js: NON va modificato a mano.
 */
const fs = require( 'fs' );
const path = require( 'path' );

const ADMIN_DIR = path.resolve( __dirname, '..', 'src', 'components', 'Admin' );
const OUT_FILE  = path.resolve( __dirname, '..', 'src', 'config', 'settingsSearchIndex.js' );

// Label e gruppo di ogni scheda per la palette globale (olo-palette.js).
// ⚠️ Tenere allineato a IA_GROUPS in SettingsApp.vue (come TAB_FILES qui sotto).
const TAB_META = {
	colori:        { label: 'Palette & Stili',         group: 'Design' },
	tipografia:    { label: 'Tipografia',              group: 'Design' },
	spaziature:    { label: 'Spaziature & layout',     group: 'Design' },
	responsive:    { label: 'Breakpoint responsive',   group: 'Design' },
	tplconditions: { label: 'Assegnazione template',   group: 'Contenuti & Template' },
	wootemplates:  { label: 'WooCommerce template',    group: 'Contenuti & Template' },
	popups:        { label: 'Popup globali',           group: 'Contenuti & Template' },
	seo:           { label: 'SEO globale',             group: 'SEO & Privacy' },
	redirects:     { label: 'Redirect & 404',          group: 'SEO & Privacy' },
	cookie:        { label: 'Cookie Consent & GDPR',   group: 'SEO & Privacy' },
	analytics:     { label: 'Tracking & Analytics',    group: 'SEO & Privacy' },
	performance:   { label: 'Performance & Cache',     group: 'Prestazioni & Servizi' },
	maintenance:   { label: 'Manutenzione & Coming Soon', group: 'Prestazioni & Servizi' },
	ai:            { label: 'AI Assistant',            group: 'Prestazioni & Servizi' },
	stockmedia:    { label: 'Stock media',             group: 'Prestazioni & Servizi' },
	whitelabel:    { label: 'White Label',             group: 'Team & Brand' },
	permessi:      { label: 'Permessi & Ruoli',        group: 'Team & Brand' },
};

// Stessa mappa di IA_GROUPS in SettingsApp.vue (tab id → componente).
const TAB_FILES = {
	colori:        'ColorsTab.vue',
	tipografia:    'TypographyTab.vue',
	spaziature:    'SpaziatureTab.vue',
	responsive:    'BreakpointsTab.vue',
	tplconditions: 'TemplateConditionsTab.vue',
	wootemplates:  'WooTemplatesTab.vue',
	popups:        'PopupsTab.vue',
	seo:           'SeoTab.vue',
	redirects:     'RedirectsTab.vue',
	cookie:        'CookieTab.vue',
	analytics:     'AnalyticsTab.vue',
	performance:   'PerformanceTab.vue',
	maintenance:   'MaintenanceTab.vue',
	ai:            'AITab.vue',
	stockmedia:    'StockmediaTab.vue',
	whitelabel:    'WhitelabelTab.vue',
	permessi:      'PermessiTab.vue',
};

const T_STR = "t\\(\\s*'((?:[^'\\\\]|\\\\.)*)'\\s*\\)";
const RE_LABEL   = new RegExp( '<label>\\{\\{\\s*' + T_STR + '\\s*\\}\\}</label>', 'g' );
const RE_H3      = new RegExp( '<h3>\\{\\{\\s*' + T_STR + '\\s*\\}\\}', 'g' );
const RE_HINT    = new RegExp( 'class="hint">\\{\\{\\s*' + T_STR, 'g' );
const RE_CARD_P  = new RegExp( '<p>\\{\\{\\s*' + T_STR, 'g' );

function unesc( s ) {
	return s.replace( /\\(.)/g, '$1' );
}

function collect( re, src ) {
	const out = [];
	let m;
	re.lastIndex = 0;
	while ( ( m = re.exec( src ) ) !== null ) {
		out.push( { text: unesc( m[1] ), at: m.index } );
	}
	return out;
}

const index = {};
let fields = 0;
let sections = 0;

for ( const [ tabId, file ] of Object.entries( TAB_FILES ) ) {
	const full = path.join( ADMIN_DIR, file );
	if ( ! fs.existsSync( full ) ) {
		console.error( `[settings-search-index] file mancante: ${file}` );
		process.exitCode = 1;
		continue;
	}
	const src = fs.readFileSync( full, 'utf8' );
	// Solo il template: le stringhe dello <script> non sono campi visibili.
	const tplEnd = src.indexOf( '</template>' );
	const tpl = tplEnd > -1 ? src.slice( 0, tplEnd ) : src;

	const labels = collect( RE_LABEL, tpl );
	const h3s    = collect( RE_H3, tpl );
	const hints  = collect( RE_HINT, tpl );
	const ps     = collect( RE_CARD_P, tpl );

	const entries = [];

	// Sezioni (h3 delle card) con la loro descrizione <p> più vicina (entro 400 char).
	for ( const h of h3s ) {
		const p = ps.find( ( x ) => x.at > h.at && x.at - h.at < 400 );
		entries.push( { kind: 'section', label: h.text, hint: p ? p.text : '', at: h.at } );
		sections++;
	}

	// Campi: label + hint adiacente (entro 400 char) + sezione contenitrice (ultimo h3 prima).
	for ( const l of labels ) {
		const hint = hints.find( ( x ) => x.at > l.at && x.at - l.at < 400 );
		let section = '';
		for ( const h of h3s ) {
			if ( h.at < l.at ) section = h.text; else break;
		}
		entries.push( { kind: 'field', label: l.text, hint: hint ? hint.text : '', section, at: l.at } );
		fields++;
	}

	// Ordine di apparizione nel template, senza la posizione interna.
	entries.sort( ( a, b ) => a.at - b.at );
	index[ tabId ] = entries.map( ( { at, ...rest } ) => rest ); // eslint-disable-line no-unused-vars
}

const banner = '// ─────────────────────────────────────────────────────────────────────\n' +
	'// GENERATO da scripts/build-settings-search-index.cjs (hook buildStart di\n' +
	'// vite.config.admin.js). NON modificare a mano: rigenerare con\n' +
	'//   node scripts/build-settings-search-index.cjs\n' +
	'// Indice dei campi della Configurazione per la ricerca a livello di campo.\n' +
	'// ─────────────────────────────────────────────────────────────────────\n';

fs.mkdirSync( path.dirname( OUT_FILE ), { recursive: true } );
fs.writeFileSync( OUT_FILE, banner + 'export const SETTINGS_FIELD_INDEX = ' + JSON.stringify( index, null, '\t' ) + ';\n' );

// Stesso indice come JSON statico per la palette globale (fetch da olo-palette.js
// su qualsiasi pagina admin, senza portarsi dietro il bundle Vue).
const OUT_JSON = path.resolve( __dirname, '..', 'assets', 'data', 'settings-search-index.json' );
fs.mkdirSync( path.dirname( OUT_JSON ), { recursive: true } );
fs.writeFileSync( OUT_JSON, JSON.stringify( { tabs: TAB_META, fields: index } ) );

console.log( `[settings-search-index] ${fields} campi + ${sections} sezioni da ${Object.keys( index ).length} tab → src/config/settingsSearchIndex.js + assets/data/settings-search-index.json` );
