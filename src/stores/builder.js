import { defineStore } from 'pinia';
import { useTilesStore as useTilesStoreRef } from './tiles';
import { useToast } from '../composables/useToast.js';
import { t } from '@/i18n';

function getOloData() {
  return window.oloData || {};
}

// ── Versione salvata di ogni zona ──
// Stringhe JSON di ciò che il server ha davvero: fissate a pagina caricata
// (captureSavedBaseline, da initHistory) e riscritte con ciò che si è INVIATO
// dopo ogni salvataggio riuscito (non con la risposta: il server può ripulire il
// contenuto e la zona resterebbe «da salvare» per sempre).
// Servono a due cose: al salvataggio si scrive ogni zona diversa da qui (il
// confronto copre tutto il payload, anche le zone che un'assegnazione ha segnato
// male; il flag decide solo finché la versione salvata non è nota), e il confronto
// corregge i flag «da salvare» (un annulla che torna al salvato mostra «Salvato»).
// b = tile del corpo · m = titolo, tipo, impostazioni e stato del corpo · h/f = header/footer.
const salvato = { b: null, m: null, h: null, f: null };

// Versione salvata non affidabile: la zona è stata scritta ma il master di un suo
// widget globale no. Non coincide con nessuna fotografia (un JSON di tile inizia
// sempre con «[»): la zona resta «da salvare», anche dopo un annulla, finché un
// salvataggio non scrive zona e master.
const VERSIONE_INCOMPLETA = '#incompleta';

function chiaveZona(zone) {
  if (zone === 'header') return 'h';
  if (zone === 'footer') return 'f';
  return 'b';
}

function azzeraSalvato() {
  salvato.b = null;
  salvato.m = null;
  salvato.h = null;
  salvato.f = null;
}

function metaJson(tpl) {
  return tpl ? JSON.stringify({
    title: tpl.title || '',
    type: tpl.type || '',
    settings: tpl.settings || {},
    status: tpl.status || 'draft',
  }) : null;
}

function fotoZone(ts) {
  return {
    b: JSON.stringify(ts.canvasTiles),
    h: JSON.stringify(ts.headerTiles),
    f: JSON.stringify(ts.footerTiles),
  };
}

// PHP può restituire settings = [] invece di {}.
function normalizzaTemplate(tpl) {
  if (tpl && (!tpl.settings || Array.isArray(tpl.settings))) tpl.settings = {};
  return tpl;
}

function titoloTra(tpl) {
  const s = tpl && typeof tpl.title === 'string' ? tpl.title.trim() : '';
  return s ? ' «' + s + '»' : '';
}

/**
 * Nonce REST scaduto (ore di lavoro): se ne chiede uno nuovo a WordPress
 * (admin-ajax.php?action=rest-nonce, endpoint del core). Il nonce si scrive
 * DENTRO window.oloData, senza sostituire l'oggetto: tiles.js e diversi
 * componenti ne tengono un riferimento. Una risposta che non è un nonce
 * ('0', '-1', 400) vuol dire sessione chiusa.
 */
async function rinnovaNonce() {
  if (typeof window === 'undefined') return false;
  try {
    const base = window.ajaxurl || 'admin-ajax.php';
    const url = base + (base.indexOf('?') === -1 ? '?' : '&') + 'action=rest-nonce';
    const res = await fetch(url, { credentials: 'same-origin', cache: 'no-store' });
    if (!res.ok) return false;
    const nonce = String(await res.text()).trim();
    if (!/^[a-f0-9]{10}$/.test(nonce)) return false;
    if (window.oloData) window.oloData.nonce = nonce;
    if (window.oloThumbConfig) window.oloThumbConfig.nonce = nonce;
    return true;
  } catch (e) {
    return false;
  }
}

async function leggiErrore(res) {
  try {
    const j = await res.json();
    return { code: (j && j.code) || '', message: (j && j.message) || '' };
  } catch (e) {
    return { code: '', message: '' };
  }
}

/**
 * Scrittura REST (PUT/POST) con UN solo nuovo tentativo se il nonce è scaduto.
 * Riprovare è sicuro: il 403 rest_cookie_invalid_nonce arriva prima dell'handler,
 * il server non ha scritto niente (nemmeno la revisione).
 * La usano le zone e i master dei widget globali (syncGlobalWidgetsOnSave).
 * Esito: { ok: true, status, data } oppure { ok: false, status, code, message }
 * con sessione (nonce non rinnovabile), rete (nessuna risposta) o invalida
 * (risposta 2xx che non è JSON).
 */
async function inviaRest(url, method, corpo) {
  const invia = async () => {
    try {
      return await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': getOloData().nonce,
        },
        body: corpo,
      });
    } catch (e) {
      return null; // nessuna risposta: rete assente o connessione caduta
    }
  };
  let res = await invia();
  if (res && res.status === 403) {
    const err = await leggiErrore(res);
    if (err.code !== 'rest_cookie_invalid_nonce') {
      return { ok: false, status: 403, code: err.code, message: err.message };
    }
    if (!(await rinnovaNonce())) {
      return { ok: false, status: 403, code: err.code, sessione: true };
    }
    res = await invia();
  }
  if (!res) return { ok: false, status: 0, rete: true };
  if (!res.ok) {
    const err = await leggiErrore(res);
    return {
      ok: false,
      status: res.status,
      code: err.code,
      message: err.message,
      sessione: err.code === 'rest_cookie_invalid_nonce',
    };
  }
  // 2xx che non è JSON (es. un avviso PHP stampato prima, una pagina di login):
  // non si sa che cosa il server abbia scritto, la zona resta da salvare.
  try {
    return { ok: true, status: res.status, data: await res.json() };
  } catch (e) {
    return { ok: false, status: res.status, invalida: true };
  }
}

async function inviaTemplate(url, method, corpo) {
  const r = await inviaRest(url, method, corpo);
  if (!r.ok) return r;
  if (!r.data || typeof r.data !== 'object') return { ok: false, status: r.status, invalida: true };
  r.data = normalizzaTemplate(r.data);
  return r;
}

/** Motivo di una scrittura non riuscita, per il messaggio del salvataggio. */
function motivoDi(r) {
  if (r.sessione) return t('sessione scaduta: accedi di nuovo e premi Riprova');
  if (r.rete) return t('errore di rete');
  if (r.invalida) return t('risposta non valida del server');
  if (r.imprevisto) return t('errore imprevisto');
  return 'HTTP ' + r.status + (r.message ? ' ' + r.message : '');
}

export const useBuilderStore = defineStore('builder', {
  state: () => ({
    currentTemplate: null,
    selectedTileId: null,
    // Multi-selezione (MVP, additiva): set di id selezionati via ctrl/cmd-click.
    // selectedTileId resta la selezione "primaria" (guida l'inspector).
    selectedTileIds: [],
    isDirty: false,
    isSaving: false,
    // Ultimo salvataggio con zone NON scritte: { zones: ['header'], message }
    // (zones = zone rimaste da salvare, anche quella del widget globale non scritto).
    // null dopo un salvataggio riuscito o a pagina appena caricata.
    // La toolbar legge il getter erroreSalvataggio, non questo.
    saveError: null,
    viewMode: 'desktop', // desktop | widescreen | tablet_landscape | tablet | mobile_landscape | mobile
    previewMode: false,
    wireframeMode: false,
    livePreviewMode: true,  // iframe live preview (Divi-style)
    _iframeContextMenu: null,
    previewHeaderContent: null,
    previewFooterContent: null,
    previewCssUrls: [],
    previewInlineCss: '',
    cleanMode: false,
    pageSettingsOpen: false,
    inlineEditingTileId: null,
    inlineEditingField: null,
    // Inspector V2: when true, every hoverable field opens its hover variant
    // by default. Visual indicator: amber bar at the top of the inspector panel.
    editingHover: false,
    // ── Unified Editing ──
    activeZone: 'body',          // 'header' | 'body' | 'footer'
    headerTemplate: null,        // Full template object for the active header
    footerTemplate: null,        // Full template object for the active footer
    headerDirty: false,
    footerDirty: false,
    // Da dove vengono header e footer caricati (oloData.resolvedZones), per zona:
    // { source: 'page' | 'rule' | 'global' | null, pages, rules }. Lo legge utils/zoneOrigin.js.
    zoneOrigin: { header: null, footer: null },
    unifiedMode: false,          // True when editing H+B+F together
    insertAfterTileId: null,     // When set, next element added from sidebar goes after this tile
    canvasZoom: 100,              // Canvas zoom percentage (25-200)
    iframeLayout: { sections: [], columns: [], containers: [], elements: [] },  // Cached layout snapshot from iframe (usato da CanvasDragOverlay per hit-test)
  }),

  getters: {
    selectedTile(state) {
      if (!state.selectedTileId) return null;
      // Validate tile still exists in canvas
      const tilesStore = useTilesStoreRef();
      const node = tilesStore.getTileById(state.selectedTileId);
      if (!node) {
        state.selectedTileId = null;
        return null;
      }
      return state.selectedTileId;
    },
    isEditing(state) {
      return state.currentTemplate !== null;
    },
    /**
     * True if any zone (body, header, footer) has unsaved changes
     */
    isAnyDirty(state) {
      return state.isDirty || state.headerDirty || state.footerDirty;
    },
    /**
     * L'ultimo salvataggio fallito, finché almeno una delle zone che non ha scritto
     * resta da salvare (stessa mappa di markZoneDirty): se un annulla la riporta al
     * salvato, l'errore non vale più per le modifiche arrivate dopo in altre zone.
     */
    erroreSalvataggio(state) {
      const e = state.saveError;
      if (!e) return null;
      const aperta = e.zones.some((z) => {
        if (state.unifiedMode && z === 'header') return state.headerDirty;
        if (state.unifiedMode && z === 'footer') return state.footerDirty;
        return state.isDirty;
      });
      return aperta ? e : null;
    },
    pageSettings(state) {
      const defaults = {
        content_max_width: 1200,
        breakpoints: {
          widescreen: 1400,
          tablet_landscape: 1200,
          tablet: 960,
          mobile_landscape: 640,
          mobile: 480,
        },
        page_bg: {
          type: 'none',
          color: '#ffffff',
          gradient_angle: 180,
          gradient_from: '#ffffff',
          gradient_to: '#000000',
          image_url: '',
          image_size: 'cover',
          image_position: 'center center',
          parallax: false,
          parallax_speed: 0.3,
          overlay_color: '#000000',
          overlay_opacity: 0,
        },
      };
      const settings = state.currentTemplate?.settings || {};
      return {
        ...defaults,
        ...settings,
        page_bg: { ...defaults.page_bg, ...(settings.page_bg || {}) },
      };
    },
  },

  actions: {
    async loadTemplate(id) {
      const olo = getOloData();
      const MAX_RETRIES = 2;
      for (let attempt = 0; attempt <= MAX_RETRIES; attempt++) {
        try {
          const res = await fetch(`${olo.restUrl}templates/${id}`, {
            headers: { 'X-WP-Nonce': olo.nonce },
          });
          if (!res.ok) {
            const errText = await res.text().catch(() => '');
            throw new Error(`HTTP ${res.status}: ${errText.slice(0, 200)}`);
          }
          const tpl = await res.json();
          // Ensure settings is always a plain object (PHP may return [] instead of {})
          if (!tpl.settings || Array.isArray(tpl.settings)) tpl.settings = {};
          this.currentTemplate = tpl;
          this.isDirty = false;
          // La versione salvata del template precedente non vale per questo:
          // si rifissa a caricamento finito (initHistory → captureSavedBaseline).
          azzeraSalvato();
          this.saveError = null;
          return true;
        } catch (err) {
          console.error(`loadTemplate attempt ${attempt + 1}/${MAX_RETRIES + 1} error:`, err);
          if (attempt < MAX_RETRIES) {
            await new Promise(r => setTimeout(r, 500 * (attempt + 1)));
          }
        }
      }
      return false;
    },

    /**
     * Salva. Restituisce { saved: [zone scritte], failed: [zone non scritte],
     * incerti: [zone di failed con esito incerto] } (in failed anche 'globali' se
     * il master di un widget globale non è stato scritto; incerta = risposta 2xx
     * che non è JSON, il server può aver scritto), oppure undefined se non c'è un
     * template aperto o un salvataggio è già in corso.
     */
    async saveTemplate() {
      if (!this.currentTemplate || this.isSaving) return;

      // In unified mode, save all zones at once
      if (this.unifiedMode) {
        return this.saveAllZones();
      }

      return this._salvaZone(['body']);
    },

    async togglePublish() {
      if (!this.currentTemplate || this.isSaving) return;

      const tpl = this.currentTemplate;
      const prevStatus = tpl.status;
      const prevDirty = this.isDirty;
      const nuovo = tpl.status === 'published' ? 'draft' : 'published';
      tpl.status = nuovo;
      this.isDirty = true;
      // Se il corpo non viene scritto, il messaggio dice che lo stato non è cambiato
      // e «Riprova» ripete QUESTA azione: un salvataggio semplice scriverebbe la
      // pagina nello stato di prima, o non avrebbe niente da scrivere. Un toast
      // rimasto aperto non ripete l'azione se lo stato è già quello voluto: si
      // guarda il template aperto ADESSO (un salvataggio riuscito lo sostituisce
      // con la risposta del server), riconosciuto dall'id.
      const esito = await this._salvaZone(this.unifiedMode ? ['body', 'header', 'footer'] : ['body'], {
        prefisso: nuovo === 'published' ? t('Non pubblicato') : t('Non riportato in bozza'),
        riprova: () => {
          const cur = this.currentTemplate;
          const ora = cur && cur.status === 'published' ? 'published' : 'draft';
          if (cur && cur.id === tpl.id && ora !== nuovo) this.togglePublish();
          else this.saveTemplate();
        },
      });
      // Corpo con esito incerto (risposta 2xx che non è JSON): il server ha
      // probabilmente già scritto il nuovo stato, e con lui lo stato della pagina
      // WordPress. Resta il nuovo stato, con il corpo da salvare: «Riprova» o il
      // prossimo salvataggio lo riscrivono. Rimettere quello di prima lo farebbe
      // sembrare salvato, e il salvataggio dopo cambierebbe lo stato in silenzio.
      if (!esito || (esito.failed.includes('body') && !esito.incerti.includes('body'))) {
        // Il template non è stato scritto: il pulsante torna allo stato di prima,
        // altrimenti direbbe «Pubblicato» per una pagina che sul sito non lo è.
        if (this.currentTemplate === tpl) tpl.status = prevStatus;
        this.isDirty = prevDirty;
        this.reconcileDirty(fotoZone(useTilesStoreRef()));
      }
      return esito;
    },

    async togglePreview() {
      this.previewMode = !this.previewMode;
      if (this.previewMode) {
        // Carica HTML renderizzato di header e footer attivi
        const olo = getOloData();
        const type = this.currentTemplate?.type;
        const cssSet = new Set();
        let inlineCss = '';
        // Non caricare header/footer se stai editando un header o footer
        if (type !== 'header' && type !== 'footer') {
          // Quelli che il builder ha caricato (della pagina, oloData.resolvedZones),
          // non i globali: senza header o footer (-1, tipo sbagliato) niente render.
          const headerId = parseInt(this.headerTemplate?.id, 10) || 0;
          const footerId = parseInt(this.footerTemplate?.id, 10) || 0;
          if (headerId > 0) {
            try {
              const res = await fetch(`${olo.restUrl}templates/${headerId}/render`, { headers: { 'X-WP-Nonce': olo.nonce } });
              if (res.ok) {
                const data = await res.json();
                this.previewHeaderContent = data.html || '';
                (data.css || []).forEach(u => cssSet.add(u));
                if (data.inline_css) inlineCss = data.inline_css;
              }
            } catch (e) { /* ignora */ }
          }
          if (footerId > 0) {
            try {
              const res = await fetch(`${olo.restUrl}templates/${footerId}/render`, { headers: { 'X-WP-Nonce': olo.nonce } });
              if (res.ok) {
                const data = await res.json();
                this.previewFooterContent = data.html || '';
                (data.css || []).forEach(u => cssSet.add(u));
                if (data.inline_css && !inlineCss) inlineCss = data.inline_css;
              }
            } catch (e) { /* ignora */ }
          }
        }
        this.previewCssUrls = [...cssSet];
        this.previewInlineCss = inlineCss;
      } else {
        this.previewHeaderContent = null;
        this.previewFooterContent = null;
        this.previewCssUrls = [];
        this.previewInlineCss = '';
      }
    },

    selectTile(tileId) {
      this.selectedTileId = tileId;
      this.selectedTileIds = tileId ? [tileId] : [];
      this.pageSettingsOpen = false;
    },

    // Ctrl/Cmd-click: aggiunge/toglie una tile dal set, mantenendo l'ultima come primaria.
    toggleTileSelection(tileId) {
      if (!tileId) return;
      const i = this.selectedTileIds.indexOf(tileId);
      if (i === -1) {
        this.selectedTileIds.push(tileId);
        this.selectedTileId = tileId;
      } else {
        this.selectedTileIds.splice(i, 1);
        this.selectedTileId = this.selectedTileIds.length
          ? this.selectedTileIds[this.selectedTileIds.length - 1]
          : null;
      }
      this.pageSettingsOpen = false;
    },

    deselectTile() {
      this.selectedTileId = null;
      this.selectedTileIds = [];
    },

    startInlineEdit(tileId, field) {
      this.inlineEditingTileId = tileId;
      this.inlineEditingField = field;
    },

    stopInlineEdit() {
      this.inlineEditingTileId = null;
      this.inlineEditingField = null;
    },

    setZoom(val) {
      this.canvasZoom = Math.max(25, Math.min(200, val));
    },
    zoomIn() {
      const steps = [25, 50, 75, 100, 125, 150, 175, 200];
      const next = steps.find(s => s > this.canvasZoom);
      this.canvasZoom = next || 200;
    },
    zoomOut() {
      const steps = [25, 50, 75, 100, 125, 150, 175, 200];
      const prev = [...steps].reverse().find(s => s < this.canvasZoom);
      this.canvasZoom = prev || 25;
    },

    togglePageSettings() {
      this.pageSettingsOpen = !this.pageSettingsOpen;
      if (this.pageSettingsOpen) {
        this.selectedTileId = null;
        this.selectedTileIds = [];
      }
    },

    updatePageSetting(path, value) {
      if (!this.currentTemplate) return;
      if (!this.currentTemplate.settings) {
        this.currentTemplate.settings = {};
      }
      // Support nested paths like 'page_bg.color'
      const keys = path.split('.');
      let target = this.currentTemplate.settings;
      for (let i = 0; i < keys.length - 1; i++) {
        if (!target[keys[i]] || typeof target[keys[i]] !== 'object') {
          target[keys[i]] = {};
        }
        target = target[keys[i]];
      }
      target[keys[keys.length - 1]] = value;
      this.isDirty = true;
    },

    setViewMode(mode) {
      this.viewMode = mode;
    },

    // ── Unified Editing ──

    setActiveZone(zone) {
      if (['header', 'body', 'footer'].includes(zone)) {
        this.activeZone = zone;
      }
    },

    /**
     * Load header and footer templates for unified editing.
     * Called after the main template is loaded.
     */
    async loadUnifiedContext() {
      const olo = getOloData();
      const type = this.currentTemplate?.type;

      // Only load H+F for page/single templates, not for header/footer themselves
      if (type === 'header' || type === 'footer') {
        this.unifiedMode = false;
        return;
      }

      const tilesStore = useTilesStoreRef();
      // Header e footer EFFETTIVI della pagina (oloData.resolvedZones, risolti dal
      // PHP come sul sito: meta della pagina → regole → globale, per la stessa
      // pagina che l'iframe mostra). Prima si caricava sempre il globale, e
      // salvando si scriveva l'header globale mentre la pagina ne usava un altro.
      // Se la zona risolta c'è, non si ripiega mai sul globale: -1 = la pagina non
      // ha header. Senza resolvedZones (dati vecchi) resta il globale.
      // Coerce stringhe '0' a falsy e ignora id non positivi.
      // Senza questa coercion, `'0'` (stringa) supera `if (id)` (truthy) e fa fetch a /templates/0
      // che restituisce 404 — innocuo ma sporca la console.
      const risolte = olo.resolvedZones && typeof olo.resolvedZones === 'object' ? olo.resolvedZones : null;
      const idZona = (zona, globale) => {
        const r = risolte && risolte[zona];
        if (r && typeof r === 'object' && r.id !== undefined && r.id !== null && r.id !== '') {
          return parseInt(r.id, 10) || 0;
        }
        return parseInt(globale, 10) || 0;
      };
      const headerId = idZona('header', olo.activeHeaderId);
      const footerId = idZona('footer', olo.activeFooterId);
      // Provenienza per il chip di zona e l'avviso (utils/zoneOrigin.js): senza
      // resolvedZones si è caricato il globale. pages = pagine con la stessa
      // assegnazione, rules = lo usa anche una regola (solo per 'page').
      const provenienza = (zona) => {
        const r = risolte && risolte[zona];
        if (!r || typeof r !== 'object') return { source: risolte ? null : 'global', pages: 0, rules: false };
        return { source: typeof r.source === 'string' ? r.source : null, pages: parseInt(r.pages, 10) || 0, rules: r.rules === true };
      };
      this.zoneOrigin = { header: provenienza('header'), footer: provenienza('footer') };

      // Load header template
      if (headerId > 0) {
        try {
          const res = await fetch(`${olo.restUrl}templates/${headerId}`, {
            headers: { 'X-WP-Nonce': olo.nonce },
          });
          if (res.ok) {
            const tpl = await res.json();
            // Solo un template header: il salvataggio lo riscrive con type 'header'
            // (_fotoZona), e un'assegnazione sbagliata lo trasformerebbe.
            if (tpl && tpl.type === 'header') {
              if (!tpl.settings || Array.isArray(tpl.settings)) tpl.settings = {};
              this.headerTemplate = tpl;
              tilesStore.setHeaderTiles(tpl.content || []);
            } else {
              console.warn('[Olobuild] L\'header della pagina non è un template header, non lo carico:', headerId);
            }
          }
        } catch (e) {
          console.warn('[Olobuild] Failed to load header template:', e);
        }
      }

      // Load footer template
      if (footerId > 0) {
        try {
          const res = await fetch(`${olo.restUrl}templates/${footerId}`, {
            headers: { 'X-WP-Nonce': olo.nonce },
          });
          if (res.ok) {
            const tpl = await res.json();
            if (tpl && tpl.type === 'footer') {
              if (!tpl.settings || Array.isArray(tpl.settings)) tpl.settings = {};
              this.footerTemplate = tpl;
              tilesStore.setFooterTiles(tpl.content || []);
            } else {
              console.warn('[Olobuild] Il footer della pagina non è un template footer, non lo carico:', footerId);
            }
          }
        } catch (e) {
          console.warn('[Olobuild] Failed to load footer template:', e);
        }
      }

      this.unifiedMode = true;
      this.headerDirty = false;
      this.footerDirty = false;
      this.activeZone = 'body';
    },

    /**
     * Save all dirty zones (body + header + footer) in unified mode
     */
    async saveAllZones() {
      if (this.isSaving) return;
      return this._salvaZone(['body', 'header', 'footer']);
    },

    /**
     * Salva le zone indicate, ognuna per conto suo: un errore su una zona non
     * ferma le altre e non si trasforma in «Tutto salvato». Le zone non scritte
     * restano «da salvare», il messaggio le nomina e offre «Riprova».
     * azione (facoltativa, da togglePublish): { prefisso, riprova } per quando
     * il corpo non viene scritto.
     */
    async _salvaZone(zone, azione) {
      this.isSaving = true;
      const tilesStore = useTilesStoreRef();
      const toast = useToast();
      const saved = [];
      const failed = [];
      const incerti = [];
      const motivi = [];
      const aggiungiMotivo = (m) => { if (m && !motivi.includes(m)) motivi.push(m); };
      // Tile con global_id il cui master non è stato scritto: [{ tileId, esito }].
      let globaliKo = [];

      try {
        // Fotografia di ciò che parte per ogni zona, PRIMA di ogni richiesta (anche
        // di quelle dei master, che fotografano i loro dati nello stesso istante):
        // una modifica fatta mentre il salvataggio è in corso resta «da salvare»,
        // e zona e master partono uguali.
        const foto = {};
        for (const z of zone) {
          try {
            foto[z] = this._fotoZona(z);
          } catch (err) {
            console.error('[Olobuild] salvataggio zona ' + z + ':', err);
            foto[z] = { errore: true };
          }
        }

        // Sincronizza widget globali → master nel DB, con lo stesso invio delle zone:
        // col nonce scaduto si rinnova anche qui e l'esito non si perde.
        try {
          globaliKo = (await tilesStore.syncGlobalWidgetsOnSave(inviaRest)) || [];
        } catch (err) {
          console.error('[Olobuild] syncGlobalWidgetsOnSave error:', err);
          globaliKo = [{ tileId: null, esito: { ok: false, imprevisto: true } }];
        }

        for (const z of zone) {
          let esito;
          try {
            esito = await this._scriviZona(z, foto[z]);
          } catch (err) {
            console.error('[Olobuild] salvataggio zona ' + z + ':', err);
            esito = { zone: z, stato: 'ko', motivo: t('errore imprevisto') };
          }
          if (esito.stato === 'ok') {
            saved.push(z);
          } else if (esito.stato === 'ko') {
            failed.push(z);
            if (esito.incerto) incerti.push(z);
            aggiungiMotivo(esito.motivo);
            this.markZoneDirty(z);
          }
        }
      } finally {
        this.isSaving = false;
      }

      // Master di un widget globale non scritto: il sito rende l'istanza dal master e
      // alla riapertura il builder la riallinea al master, quindi la modifica andrebbe
      // persa. Lo si dice («Widget globali») e la zona che contiene il widget resta da
      // salvare: il prossimo salvataggio riscrive zona e master.
      const zoneAperte = failed.slice();
      if (globaliKo.length) {
        failed.push('globali');
        globaliKo.forEach((g) => {
          aggiungiMotivo(motivoDi(g.esito || {}));
          const z = (this.unifiedMode && g.tileId && tilesStore.getZoneForTile(g.tileId)) || 'body';
          salvato[chiaveZona(z)] = VERSIONE_INCOMPLETA;
          this.markZoneDirty(z);
          if (!zoneAperte.includes(z)) zoneAperte.push(z);
        });
      }

      // I flag seguono ciò che c'è davvero: restano accese le zone non scritte
      // e quelle modificate mentre il salvataggio era in corso.
      this.reconcileDirty(fotoZone(tilesStore));

      if (failed.length) {
        // «Riprova» ripete l'azione che ha fallito: Pubblica/Ritira se il corpo non è
        // stato scritto durante un cambio di stato, altrimenti il salvataggio.
        // Corpo con esito incerto: non si sa se lo stato è cambiato, lo si dice.
        const suAzione = !!(azione && typeof azione.riprova === 'function' && failed.includes('body'));
        let prefisso = t('Non salvato');
        if (suAzione) prefisso = incerti.includes('body') ? t('Esito incerto') : azione.prefisso;
        const message = prefisso + ': '
          + failed.map((z) => this.zoneLabel(z)).join(' · ')
          + (motivi.length ? ' — ' + motivi.join('; ') : '');
        this.saveError = { zones: zoneAperte, message };
        toast.action(message, t('Riprova'), suAzione ? azione.riprova : () => { this.saveTemplate(); }, 10000, 'error');
      } else {
        this.saveError = null;
        if (saved.length) {
          toast.success(t('Salvato') + ': ' + saved.map((z) => this.zoneLabel(z)).join(' · '));
        } else {
          toast.info(t('Nessuna modifica da salvare'));
        }
      }

      // Trigger auto-thumbnail capture per il template body (handler standalone in olo-thumb-capture.js)
      if (saved.length && this.currentTemplate?.id && typeof window !== 'undefined') {
        window.dispatchEvent(new CustomEvent('olobuild:saved', {
          detail: { templateId: this.currentTemplate.id, type: this.currentTemplate.type },
        }));
      }

      return { saved, failed, incerti };
    },

    /**
     * Fotografia sincrona di UNA zona ('body' | 'header' | 'footer'): decide se va
     * scritta e ne prepara il payload. Si scrive se è diversa dalla versione
     * salvata; se la versione salvata non è nota, se è segnata. Il corpo mai
     * scritto (senza id) si scrive sempre.
     * Restituisce null (niente da scrivere), { manca: true } (header/footer da
     * scrivere senza template) o { tpl, json, meta, corpo }.
     * Il payload è quello di sempre (title, type, content, settings, status).
     */
    _fotoZona(zone) {
      const tilesStore = useTilesStoreRef();
      const isBody = zone === 'body';
      const chiave = chiaveZona(zone);
      const tpl = isBody ? this.currentTemplate : (zone === 'header' ? this.headerTemplate : this.footerTemplate);
      const tiles = isBody ? tilesStore.canvasTiles : (zone === 'header' ? tilesStore.headerTiles : tilesStore.footerTiles);
      const flag = isBody ? this.isDirty : (zone === 'header' ? this.headerDirty : this.footerDirty);

      if (isBody && !tpl) return null;

      const json = JSON.stringify(tiles);
      const meta = isBody ? metaJson(tpl) : null;
      // Con la versione salvata nota decide il confronto, che copre tutto il payload
      // (header e footer: il builder ne cambia solo le tile): un flag rimasto acceso
      // su una zona uguale al salvato non crea una revisione identica.
      const nota = salvato[chiave] !== null;
      const diversa = nota
        && (json !== salvato[chiave] || (isBody && meta !== salvato.m));
      const need = (isBody && !tpl.id) || (nota ? diversa : flag);
      if (!need) return null;

      // Header/footer da scrivere ma senza template: lo si dice, non si salta in silenzio.
      // (Il corpo senza id si crea con un POST, come sempre.)
      if (!isBody && (!tpl || !tpl.id)) return { manca: true };

      const corpo = JSON.stringify(isBody ? {
        title: tpl.title || 'Untitled',
        type: tpl.type || 'page',
        content: tiles,
        settings: tpl.settings || {},
        status: tpl.status || 'draft',
      } : {
        title: tpl.title || (zone === 'header' ? 'Header' : 'Footer'),
        type: zone,
        content: tiles,
        settings: tpl.settings || {},
        status: tpl.status || 'published',
      });
      return { tpl, json, meta, corpo };
    },

    /**
     * Scrive UNA zona con la sua fotografia (_fotoZona, presa all'inizio del
     * salvataggio): quello che cambia dopo resta «da salvare».
     * Esito: { zone, stato: 'ok' | 'skip' | 'ko', motivo, incerto }
     * (incerto = risposta 2xx che non è JSON: il server può aver scritto).
     */
    async _scriviZona(zone, foto) {
      if (!foto) return { zone, stato: 'skip' };
      if (foto.errore) return { zone, stato: 'ko', motivo: t('errore imprevisto') };
      if (foto.manca) return { zone, stato: 'ko', motivo: t('template non trovato') };

      const olo = getOloData();
      const isBody = zone === 'body';
      const { tpl, json, corpo } = foto;
      const metaInviato = foto.meta;
      const url = tpl.id ? `${olo.restUrl}templates/${tpl.id}` : `${olo.restUrl}templates`;
      const r = await inviaTemplate(url, tpl.id ? 'PUT' : 'POST', corpo);

      if (!r.ok) return { zone, stato: 'ko', status: r.status, motivo: motivoDi(r), incerto: !!r.invalida };

      const saved = r.data;
      if (isBody) {
        // Aperto un altro template nel frattempo: niente da aggiornare qui.
        if (this.currentTemplate !== tpl) return { zone, stato: 'ok' };
        if (metaJson(tpl) === metaInviato) {
          this.currentTemplate = saved;
          salvato.m = metaJson(saved);
        } else {
          // Titolo, impostazioni o stato cambiati durante il salvataggio: restano
          // quelli locali. Dal server arrivano id, date, linked_post_* e, chiave per
          // chiave, le impostazioni che ha scritto lui (settings.post_id della pagina
          // collegata creata da maybe_auto_create_linked_page) se il client non le ha
          // toccate: senza, il salvataggio dopo creerebbe un'altra pagina bozza.
          const base = JSON.parse(metaInviato);
          const inviate = base.settings && typeof base.settings === 'object' ? base.settings : (base.settings = {});
          const locali = tpl.settings && typeof tpl.settings === 'object' ? tpl.settings : {};
          const server = saved.settings || {};
          Object.keys(server).forEach((k) => {
            const s = JSON.stringify(server[k]);
            const i = JSON.stringify(inviate[k]);
            if (s !== i && JSON.stringify(locali[k]) === i) {
              locali[k] = server[k];
              inviate[k] = server[k];
            }
          });
          const unito = Object.assign({}, saved);
          ['title', 'type', 'status'].forEach((k) => { if (tpl[k] !== undefined) unito[k] = tpl[k]; });
          unito.settings = locali;
          this.currentTemplate = unito;
          salvato.m = JSON.stringify(base);
        }
        salvato.b = json;
      } else if (zone === 'header') {
        if (this.headerTemplate === tpl) {
          this.headerTemplate = saved;
          salvato.h = json;
        }
      } else if (this.footerTemplate === tpl) {
        this.footerTemplate = saved;
        salvato.f = json;
      }
      return { zone, stato: 'ok' };
    },

    /**
     * Nome di una zona nei messaggi del salvataggio. Unico punto: le etichette
     * delle zone del canvas potranno arricchirlo.
     */
    zoneLabel(zone) {
      if (zone === 'globali') return t('Widget globali');
      if (zone === 'header') return t('Header') + titoloTra(this.headerTemplate);
      if (zone === 'footer') return t('Footer') + titoloTra(this.footerTemplate);
      return this.unifiedMode ? t('Pagina') : t('Template');
    },

    /**
     * Fissa la versione salvata = stato attuale. Va richiamata a pagina CARICATA
     * (initHistory): da qui in poi ogni zona diversa è «da salvare».
     */
    captureSavedBaseline(foto) {
      const f = foto || fotoZone(useTilesStoreRef());
      salvato.b = f.b;
      salvato.h = f.h;
      salvato.f = f.f;
      salvato.m = metaJson(this.currentTemplate);
      this.saveError = null;
      this.reconcileDirty(f);
    },

    /**
     * Ricava i flag «da salvare» dal confronto con la versione salvata
     * (fotografia { b, h, f } della cronologia). Il corpo confronta anche
     * titolo, tipo, impostazioni e stato. Un template mai scritto (senza id)
     * resta «da salvare» finché il salvataggio non riesce.
     */
    reconcileDirty(foto) {
      if (!foto) return;
      if (this.currentTemplate && salvato.b !== null && salvato.m !== null) {
        const diversa = foto.b !== salvato.b || metaJson(this.currentTemplate) !== salvato.m;
        this.isDirty = this.currentTemplate.id ? diversa : (this.isDirty || diversa);
      }
      if (this.unifiedMode) {
        if (salvato.h !== null) this.headerDirty = foto.h !== salvato.h;
        if (salvato.f !== null) this.footerDirty = foto.f !== salvato.f;
      }
    },

    /**
     * Segna «da salvare» una zona ('header' | 'footer' | 'body').
     */
    markZoneDirty(zone) {
      if (this.unifiedMode && zone === 'header') this.headerDirty = true;
      else if (this.unifiedMode && zone === 'footer') this.footerDirty = true;
      else this.isDirty = true;
    },

    /**
     * Mark the appropriate zone as dirty based on tile ID
     */
    markDirtyForTile(tileId) {
      if (!this.unifiedMode) {
        this.isDirty = true;
        return;
      }
      const tilesStore = useTilesStoreRef();
      this.markZoneDirty(tilesStore.getZoneForTile(tileId));
    },
  },
});
