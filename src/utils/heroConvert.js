/**
 * heroConvert — Fase 3 dell'unificazione hero: conversione ESPLICITA delle 12 tile
 * hero legacy (ritirate dalla palette in Fase 2) nei due canonici `hero` / `hero-split`.
 *
 * Filosofia (stile bgMigrate): elastica e per singola tile, MAI automatica — parte
 * solo dal bottone "Converti in Hero" nell'inspector. I settings sorgente non vengono
 * mutati: si produce un NUOVO oggetto settings per il tipo canonico, partendo dai
 * default del target e sovrascrivendo le chiavi mappate. L'annullamento passa dalla
 * history del builder come ogni altra modifica.
 *
 * Approssimazioni dichiarate (il resto è 1:1):
 * - le "parole accento" diventano <em> nel titolo (colorate dal campo `accent`);
 *   le modalità outline/gradiente per-riga di glowhero collassano su <em>;
 * - featuredstory: byline accodata al sottotitolo, media sempre a destra;
 * - searchhero/glowgallery: posizione glow ricondotta ai parametri standard;
 * - ratio colonne non standard → il più vicino tra i preset di hero-split.
 *
 * Nessun import dal registry (import.meta.glob è Vite-only): i default di sorgente
 * e target arrivano iniettati via `getDefaults(type)` — nel builder è
 * getElementDefaults, nei test un semplice stub.
 */

const HERO_TARGETS = {
  imagehero: 'hero',
  mediacta: 'hero',
  maskedvideohero: 'hero',
  photocover: 'hero',
  glowhero: 'hero',
  glowgallery: 'hero',
  chathero: 'hero',
  searchhero: 'hero',
  producthero: 'hero',
  featuredstory: 'hero-split',
  introsplit: 'hero-split',
  audiohero: 'hero-split',
};

/** Tipo canonico di destinazione per una tile legacy, o null se non convertibile. */
export function heroConvertTarget(type) {
  return HERO_TARGETS[type] || null;
}

/* ── helper ────────────────────────────────────────────────────────────── */

const escHtml = (s) => String(s ?? '')
  .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

/** Compone il titolo del hero: parti normali + parti accento come <em>. */
function joinTitle(parts, sep = ' ') {
  return parts
    .filter((p) => p && String(p.text ?? '').trim() !== '')
    .map((p) => (p.em ? `<em>${escHtml(p.text)}</em>` : escHtml(p.text)))
    .join(sep);
}

/** min_height numerica delle legacy (≤100 = vh, altrimenti px) → campo unit del hero. */
function vhpx(v, fallback) {
  const n = parseInt(v, 10);
  if (isNaN(n) || n <= 0) return fallback;
  return n <= 100 ? `${n}vh` : `${n}px`;
}

/** media_bg valorizzato, oppure fallback elastico dall'immagine legacy / colore. */
function sceneMedia(mediaBg, legacyImage, color, position) {
  if (mediaBg && mediaBg.type && mediaBg.type !== 'none') return mediaBg;
  const img = String(legacyImage || '').trim();
  if (img !== '') {
    return {
      type: 'image', image_url: img, color: color || '',
      image_size: 'cover', image_position: position || 'center center',
    };
  }
  if (color) return { type: 'solid', color };
  return { type: 'none' };
}

/** text_position delle legacy full-bleed → coppia allineamenti del hero. */
function posToAlign(pos) {
  switch (pos) {
    case 'center':       return { vertical_align: 'center', horizontal_align: 'center' };
    case 'bottom-left':  return { vertical_align: 'bottom', horizontal_align: 'left' };
    case 'center-right': return { vertical_align: 'center', horizontal_align: 'right' };
    default:             return { vertical_align: 'center', horizontal_align: 'left' };
  }
}

/** Ratio libera ('1.15fr .85fr') → la più vicina tra quelle supportate da hero-split. */
function nearestSplitRatio(ratio) {
  const allowed = ['1fr 1fr', '1.2fr 1fr', '1fr 1.2fr', '1fr 0.8fr', '0.8fr 1fr'];
  if (allowed.includes(ratio)) return ratio;
  const m = /^([\d.]+)fr\s+([\d.]+)fr$/.exec(String(ratio || '').trim());
  if (!m) return '1fr 1fr';
  const k = parseFloat(m[1]) / parseFloat(m[2]);
  if (k > 1.05) return '1.2fr 1fr';
  if (k < 0.95) return '1fr 1.2fr';
  return '1fr 1fr';
}

const boolish = (v) => !!v && v !== '' && v !== '0';

/* ── mappe per tipo: (s) → { settings parziali per il target, styleBg? } ── */

const MAPS = {
  imagehero(s) {
    return {
      settings: {
        eyebrow_text: s.eyebrow_text || '', eyebrow_dot: !!s.eyebrow_dot,
        title: joinTitle(
          [{ text: s.headline_text }, { text: s.accent_text, em: true }, { text: s.headline_tail }],
          s.stack_lines ? '<br>' : ' '
        ),
        subtitle: escHtml(s.subhead || ''),
        cta_text: s.cta1_text || '', cta_url: s.cta1_url || '#',
        cta2_text: s.cta2_text || '', cta2_url: s.cta2_url || '#',
        meta_text: s.meta_text || '', scroll_hint: s.scroll_hint || '',
        media_bg: sceneMedia(s.media_bg, s.bg_image, s.bg_color, s.bg_image_object_position),
        overlay_color: s.overlay_color || '', overlay_top: s.overlay_top ?? 0.2,
        overlay_bottom: s.overlay_bottom ?? 0.75, overlay_sides: !!s.overlay_sides,
        accent: s.accent || '',
        title_color: s.text_color || '', subtitle_color: s.sub_color || '',
        title_font_family: s.heading_font || 'serif', title_font_weight: '400',
        ...posToAlign(s.text_position),
        text_align: s.text_align || 'left',
        content_max_width: String(s.content_width || 600),
        min_height: vhpx(s.min_height, '520px'),
        cta_radius: s.cta_radius || { tl: 2, tr: 2, br: 2, bl: 2 },
      },
    };
  },

  maskedvideohero(s) {
    return {
      settings: {
        eyebrow_text: s.tag_text || '', eyebrow_dot: true,
        title: joinTitle([{ text: s.headline_text }, { text: s.accent_text, em: true }]),
        title_text_transform: s.uppercase === false ? 'none' : 'uppercase',
        title_font_weight: '800',
        subtitle: escHtml(s.subhead || ''),
        cta_text: s.cta1_text || '', cta_url: s.cta1_url || '#',
        cta2_text: s.cta2_text || '', cta2_url: s.cta2_url || '#',
        media_bg: sceneMedia(s.media_bg, s.bg_image, s.bg_color, s.bg_image_object_position),
        overlay_color: s.overlay_color || '',
        overlay_top: s.overlay_strength ?? 0.55, overlay_bottom: s.overlay_strength ?? 0.55,
        overlay_sides: false,
        arch: s.arch !== false,
        watermark_text: s.watermark_text || '', watermark_color: s.watermark_color || '',
        accent: s.accent || '',
        title_color: s.text_color || '', subtitle_color: s.sub_color || '',
        vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        min_height: vhpx(s.min_height, '84vh'),
      },
    };
  },

  mediacta(s) {
    return {
      settings: {
        eyebrow_text: s.eyebrow || '',
        title: joinTitle([{ text: s.headline }, { text: s.accent_text, em: true }]),
        title_text_transform: s.uppercase === false ? 'none' : 'uppercase',
        title_font_weight: '800',
        subtitle: escHtml(s.subhead || ''),
        cta_text: s.cta1_text || '', cta_url: s.cta1_url || '#',
        cta2_text: s.cta2_text || '', cta2_url: s.cta2_url || '#',
        cta_bg_color: s.btn_bg || '', cta_text_color: s.btn_color || '',
        cta_radius: s.btn_radius || { tl: 999, tr: 999, br: 999, bl: 999 },
        media_bg: sceneMedia(s.media_bg, s.bg_image, 'var(--olo-color-dark, #16263d)', s.bg_image_object_position),
        overlay_color: s.overlay_color || '', overlay_top: s.overlay_top ?? 0.78,
        overlay_bottom: s.overlay_bottom ?? 0.9, overlay_sides: false,
        accent: s.accent || '',
        title_color: s.headline_color || '', subtitle_color: s.subhead_color || '',
        vertical_align: 'center',
        horizontal_align: s.align || 'center', text_align: s.align || 'center',
        min_height: '600px',
      },
    };
  },

  photocover(s) {
    const meta = Array.isArray(s.meta_items)
      ? s.meta_items.map((m) => String(m && m.text || '').trim()).filter(Boolean).join(' · ')
      : '';
    return {
      settings: {
        eyebrow_text: s.kicker_text || '',
        title: escHtml(s.headline_text || ''),
        title_text_transform: s.uppercase === false ? 'none' : 'uppercase',
        title_font_weight: '700',
        cta_text: '', cta2_text: '',
        meta_text: meta,
        media_bg: sceneMedia(s.media_cover, s.bg_image, s.media_bg || '', s.bg_image_object_position),
        overlay_color: '', overlay_top: s.overlay_top ?? 0.3,
        overlay_bottom: s.overlay_bottom ?? 0.85, overlay_sides: false,
        frame_on: true, frame_inset: parseInt(s.frame_padding, 10) || 28,
        title_color: s.headline_color || '',
        vertical_align: 'bottom', horizontal_align: 'left', text_align: 'left',
        min_height: vhpx(s.min_height, '560px'),
      },
    };
  },

  glowhero(s) {
    const lines = Array.isArray(s.lines) ? s.lines : [];
    return {
      settings: {
        eyebrow_text: s.eyebrow || '',
        title: joinTitle(
          lines.map((l) => ({ text: l && l.text, em: !!(l && l.mode) })),
          '<br>'
        ),
        title_text_transform: s.uppercase === false ? 'none' : 'uppercase',
        title_font_weight: '800',
        subtitle: escHtml(s.subhead || ''),
        cta_text: s.cta1_text || '', cta_url: s.cta1_url || '#',
        cta2_text: s.cta2_text || '', cta2_url: s.cta2_url || '#',
        scroll_hint: s.show_scroll === false ? '' : (s.scroll_text || ''),
        media_bg: { type: 'solid', color: s.bg_color || 'var(--olo-color-dark, #16263d)' },
        glow_on: true,
        glow_color: s.glow_color || '', glow_w: s.glow_w ?? 760, glow_h: s.glow_h ?? 560,
        glow_blur: s.glow_blur ?? 100, glow_x: s.glow_x ?? 50, glow_y: s.glow_y ?? 20,
        accent: s.accent || '',
        title_color: s.text_color || '', subtitle_color: s.sub_color || '',
        vertical_align: 'center',
        horizontal_align: s.align || 'left', text_align: s.align || 'left',
        content_max_width: String(s.max_width || 1240),
        min_height: vhpx(s.min_height, '100vh'),
        cta_radius: s.btn_radius || { tl: 999, tr: 999, br: 999, bl: 999 },
      },
    };
  },

  glowgallery(s) {
    return {
      settings: {
        eyebrow_text: s.eyebrow || '',
        title: joinTitle([{ text: s.headline_text }, { text: s.accent_text, em: true }]),
        subtitle: escHtml(s.subhead || ''),
        cta_text: s.cta1_text || '', cta_url: s.cta1_url || '#',
        cta2_text: s.cta2_text || '', cta2_url: s.cta2_url || '#',
        media_bg: { type: 'solid', color: s.bg_color || 'var(--olo-color-dark, #16263d)' },
        glow_on: true,
        glow_color: s.glow_color || '', glow_w: s.glow_w ?? 760, glow_h: s.glow_h ?? 520,
        glow_blur: s.glow_blur ?? 120, glow_x: 50, glow_y: 0,
        module: 'strip',
        strip_items: Array.isArray(s.items) ? s.items.map((it) => ({
          image: (it && it.image) || '', caption: (it && it.caption) || '',
        })) : [],
        strip_offset: parseInt(s.strip_offset, 10) || 28,
        strip_radius: parseInt(s.strip_radius, 10) || 200,
        accent: s.accent || '',
        title_color: s.text_color || '', subtitle_color: s.sub_color || '',
        vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        content_max_width: String(s.max_width || 880),
        cta_radius: s.btn_radius || { tl: 999, tr: 999, br: 999, bl: 999 },
      },
    };
  },

  chathero(s) {
    return {
      settings: {
        eyebrow_text: s.pill_text || '', eyebrow_dot: !!s.pill_dot,
        title: joinTitle([{ text: s.headline_text }, { text: s.accent_text, em: true }]),
        subtitle: escHtml(s.subhead || ''),
        cta_text: s.cta1_text || '', cta_url: s.cta1_url || '#',
        cta2_text: s.cta2_text || '', cta2_url: s.cta2_url || '#',
        media_bg: { type: 'solid', color: s.bg_color || 'var(--olo-color-dark, #16263d)' },
        glow_on: true,
        glow_color: s.glow_color || '', glow_w: s.glow_w ?? 820, glow_h: s.glow_h ?? 560,
        glow_blur: s.glow_blur ?? 110, glow_x: s.glow_x ?? 50, glow_y: 0,
        module: s.chat_enabled === false ? '' : 'chat',
        chat_label: s.chat_label || '',
        chat_messages: Array.isArray(s.messages) ? s.messages.map((m) => ({
          side: (m && m.side) === 'you' ? 'you' : 'ai', text: (m && m.text) || '',
        })) : [],
        accent: s.accent || '',
        title_color: s.text_color || '', subtitle_color: s.sub_color || '',
        vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        content_max_width: String(s.max_width || 840),
      },
    };
  },

  searchhero(s) {
    return {
      settings: {
        eyebrow_text: s.eyebrow_text || '',
        title: joinTitle([
          { text: s.headline_text },
          { text: s.headline_line2 },
          { text: s.accent_text, em: true },
        ]),
        title_font_weight: '800',
        subtitle: escHtml(s.subhead || ''),
        cta_text: '', cta2_text: '',
        media_bg: { type: 'solid', color: s.bg_color || 'var(--olo-color-dark, #16263d)' },
        glow_on: true,
        glow_color: s.glow_color || '', glow_w: 720, glow_h: 480, glow_blur: 120,
        glow_x: 50, glow_y: 0,
        module: 'search',
        search_placeholder: s.search_placeholder || 'Cerca…',
        search_button: s.search_button || 'Cerca',
        search_url: s.search_url && s.search_url !== '#' ? s.search_url : '',
        search_chips: s.chips || '',
        accent: s.accent || '',
        title_color: s.text_color || '', subtitle_color: s.sub_color || '',
        vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        content_max_width: '760',
        min_height: vhpx(s.min_height, '500px'),
      },
    };
  },

  producthero(s) {
    const pill = [s.pill_pre, s.pill_text].map((x) => String(x || '').trim()).filter(Boolean).join(' · ');
    return {
      settings: {
        eyebrow_text: pill,
        title: joinTitle([{ text: s.headline_text }, { text: s.accent_text, em: true }], '<br>'),
        subtitle: escHtml(s.subhead || ''),
        cta_text: s.cta1_text || '', cta_url: s.cta1_url || '#',
        cta2_text: s.cta2_text || '', cta2_url: s.cta2_url || '#',
        media_bg: { type: 'solid', color: s.bg_color || 'var(--olo-color-dark, #16263d)' },
        glow_on: s.glow_on !== false,
        glow_color: s.glow_color || '', glow_w: 760, glow_h: 560, glow_blur: 90,
        glow_x: 50, glow_y: 0,
        module: 'mockup',
        mock_mode: s.mock_mode === 'media' ? 'media' : 'dashboard',
        mock_url: s.mock_url || '', mock_label: s.mock_label || '',
        mock_media: { type: 'none' },
        mock_kpis: Array.isArray(s.kpis) ? s.kpis.map((k) => ({
          label: (k && k.label) || '', value: (k && k.value) || '',
          delta: (k && k.delta) || '', down: boolish(k && k.down),
        })) : [],
        mock_chart_title: s.chart_title || '', mock_chart_meta: s.chart_meta || '',
        mock_bars: Array.isArray(s.bars) ? s.bars.map((b) => ({
          h: parseInt(b && b.h, 10) || 0, label: (b && b.label) || '', alt: boolish(b && b.alt),
        })) : [],
        accent: s.accent || '',
        title_color: s.text_color || '', subtitle_color: s.sub_color || '',
        vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        content_max_width: '820',
      },
    };
  },

  featuredstory(s) {
    const byline = [
      [s.byline_pre, s.byline_name].map((x) => String(x || '').trim()).filter(Boolean).join(' '),
      String(s.byline_meta || '').trim(),
    ].filter(Boolean).join(' · ');
    const sub = escHtml(s.standfirst || '') + (byline ? `<br><em>${escHtml(byline)}</em>` : '');
    return {
      settings: {
        eyebrow_text: s.kicker_text || '', eyebrow_color: s.kicker_color || '',
        headline_lines: [{ text: s.headline_text || '', color: s.headline_color || '', italic: false }],
        subhead: sub, subhead_italic: !!s.standfirst_italic,
        cta1_text: s.cta1_text || '', cta1_url: s.cta1_url || '#',
        cta2_text: s.cta2_text || '', cta2_url: s.cta2_url || '#',
        panel: 'media',
        panel_media: sceneMedia(s.media_cover, s.cover_image, s.media_bg || '', s.cover_image_object_position),
        panel_media_label: s.cover_label || '',
        panel_aspect: String(s.cover_aspect || '4/5').replace(/\s/g, ''),
        panel_badge_number: '', panel_badge_label: '',
        split_ratio: nearestSplitRatio(s.col_ratio),
        headline_font_family: 'serif',
      },
      styleBg: { bg: { type: 'solid', color: s.bg_color || 'var(--olo-color-light, #f8f9fa)' } },
    };
  },

  introsplit(s) {
    const lines = [
      { text: s.headline || '', color: s.headline_color || '', italic: false },
      { text: s.accent || '', color: s.accent_color || 'var(--olo-color-primary, #e1474f)', italic: !!s.accent_italic },
      { text: s.headline_tail || '', color: s.headline_color || '', italic: false },
    ].filter((l) => String(l.text).trim() !== '');
    return {
      settings: {
        eyebrow_text: s.eyebrow || '', eyebrow_color: s.eyebrow_color || '',
        headline_lines: lines,
        headline_font_weight: s.headline_weight || '900',
        subhead: escHtml(s.lead || ''), subhead_italic: false,
        stats: Array.isArray(s.stats) ? s.stats.map((st) => ({
          value: (st && st.number) || '', value_color: s.stat_number_color || '',
          label: (st && st.label) || '',
        })) : [],
        cta1_text: s.cta_text || '', cta1_url: s.cta_url || '#',
        cta2_text: s.cta2_text || '', cta2_url: s.cta2_url || '#',
        panel: 'media',
        panel_media: sceneMedia(s.media_bg, s.media_image, '', s.media_image_object_position),
        panel_media_label: s.media_label || '',
        panel_aspect: /^\d+\/\d+$/.test(String(s.media_aspect || '').replace(/\s/g, ''))
          ? String(s.media_aspect).replace(/\s/g, '') : '4/5',
        panel_badge_number: s.badge_number || '', panel_badge_label: s.badge_label || '',
        cta1_radius: s.cta_radius || { tl: 999, tr: 999, br: 999, bl: 999 },
      },
      styleBg: s.content_bg ? { bg: { type: 'solid', color: s.content_bg } } : null,
    };
  },

  audiohero(s) {
    return {
      settings: {
        eyebrow_text: s.tag_text || '',
        headline_lines: [{ text: s.headline_text || '', color: s.text_color || '', italic: false }],
        subhead: escHtml(s.subhead || ''), subhead_italic: false,
        subhead_color: s.sub_color || '',
        cta1_text: s.cta1_text || '', cta1_url: s.cta1_url || '#',
        cta2_text: s.cta2_text || '', cta2_url: s.cta2_url || '#',
        stats: [],
        panel: 'audio',
        panel_media: sceneMedia(s.media_bg, s.cover_image, '', s.object_position),
        panel_media_label: s.cover_label || '',
        panel_track_title: s.player_track || '', panel_track_meta: s.player_meta || '',
        split_ratio: nearestSplitRatio(s.split_ratio),
        eyebrow_color: s.accent || '',
      },
      styleBg: { bg: { type: 'solid', color: s.bg_color || 'var(--olo-color-dark, #16263d)' } },
    };
  },
};

/**
 * Converte una tile legacy nel canonico.
 * @param {string} type       tipo sorgente (una delle 12)
 * @param {object} settings   settings salvati della tile
 * @param {function} getDefaults  (type) => defaults — nel builder: getElementDefaults
 * @returns {{ type: string, settings: object, styleBg: object|null } | null}
 */
export function convertHeroTile(type, settings, getDefaults) {
  const target = heroConvertTarget(type);
  const map = MAPS[type];
  if (!target || !map) return null;
  const src = { ...(getDefaults(type) || {}), ...(settings || {}) };
  const out = map(src);
  return {
    type: target,
    settings: { ...(getDefaults(target) || {}), ...out.settings },
    styleBg: out.styleBg || null,
  };
}

/* ── «Disposizione» della Hero (2 nov 2026) ──────────────────────────────────
 * Nel builder c'è una sola Hero: a tutta larghezza (`hero`) o divisa, testo e pannello (`hero-split`). Restano due
 * renderer e due insiemi di chiavi (i template salvati non cambiano); il cambio porta i testi (occhiello, titolo,
 * sottotitolo, pulsanti) e il media (scena di sfondo ↔ pannello), e mette da parte il resto della disposizione
 * lasciata in `disposizione_prima`: tornando indietro si ritrova com'era. Nessun renderer legge quella chiave. */

const unHtml = (s) => String(s ?? '').replace(/<[^>]+>/g, '').replace(/&nbsp;/g, ' ').replace(/&lt;/g, '<')
  .replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#0?39;/g, "'").replace(/&amp;/g, '&').trim();
const conMedia = (bg) => !!(bg && typeof bg === 'object' && bg.type && bg.type !== 'none');

/** Il titolo della Hero (righe separate da <br>, una riga tutta in <em> = corsiva) → le righe della Hero divisa. */
function righeDaTitolo(html, vecchie) {
  const colori = (Array.isArray(vecchie) ? vecchie : []).map((r) => r && r.color).filter(Boolean);
  return String(html || '').split(/<br\s*\/?>/i).map((riga, i) => {
    const t = riga.trim();
    const tutta = /^<(em|i)>([\s\S]*)<\/\1>$/i.exec(t);
    const testo = unHtml(tutta ? tutta[2] : t);
    if (!testo) return null;
    const r = { text: testo, italic: !!tutta };
    const c = colori[i] || colori[colori.length - 1];
    if (c) r.color = c;
    return r;
  }).filter(Boolean);
}

/** Le righe della Hero divisa → il titolo della Hero (le corsive in <em>, colorate dall'accento). */
function titoloDaRighe(righe) {
  return (Array.isArray(righe) ? righe : [])
    .filter((r) => r && String(r.text ?? '').trim() !== '')
    .map((r) => (r.italic ? `<em>${escHtml(r.text)}</em>` : escHtml(r.text)))
    .join('<br>');
}

/**
 * @param {string} type       'hero' o 'hero-split'
 * @param {object} settings   settings della tile
 * @param {function} getDefaults  (type) => defaults — nel builder: getElementDefaults
 * @returns {{ type: string, settings: object } | null}
 */
export function cambiaDisposizione(type, settings, getDefaults) {
  const verso = type === 'hero' ? 'hero-split' : (type === 'hero-split' ? 'hero' : null);
  if (!verso) return null;
  const { disposizione_prima: prima, ...lasciata } = (settings && typeof settings === 'object') ? settings : {};
  const tornata = !!(prima && prima.type === verso && prima.settings && typeof prima.settings === 'object');
  const base = tornata ? { ...prima.settings } : { ...(getDefaults(verso) || {}) };
  const s = { ...base };
  const porta = (da, a) => { if (lasciata[da] !== undefined) s[a] = lasciata[da]; };

  if (verso === 'hero-split') {
    porta('eyebrow_text', 'eyebrow_text');
    if (lasciata.title !== undefined) {
      const righe = righeDaTitolo(lasciata.title, base.headline_lines);
      if (righe.length) s.headline_lines = righe;
    }
    porta('subtitle', 'subhead');
    porta('cta_text', 'cta1_text'); porta('cta_url', 'cta1_url'); porta('cta_target', 'cta1_target');
    porta('cta2_text', 'cta2_text'); porta('cta2_url', 'cta2_url'); porta('cta2_target', 'cta2_target');
    // La prima volta niente statistiche di esempio: la Hero non ne aveva.
    if (!tornata) s.stats = [];
    // La foto o il video della scena va nel pannello; se la disposizione divisa aveva già un pannello senza media
    // (la vetrina di card) resta quello.
    if (conMedia(lasciata.media_bg)) {
      if (!tornata || ['media', 'audio', 'compare'].includes(s.panel)) {
        if (!tornata) s.panel = 'media';
        s.panel_media = lasciata.media_bg;
      }
    }
  } else {
    porta('eyebrow_text', 'eyebrow_text');
    if (Array.isArray(lasciata.headline_lines)) {
      const titolo = titoloDaRighe(lasciata.headline_lines);
      if (titolo) s.title = titolo;
    }
    porta('subhead', 'subtitle');
    porta('cta1_text', 'cta_text'); porta('cta1_url', 'cta_url'); porta('cta1_target', 'cta_target');
    porta('cta2_text', 'cta2_text'); porta('cta2_url', 'cta2_url'); porta('cta2_target', 'cta2_target');
    if (['media', 'audio', 'compare'].includes(lasciata.panel) && conMedia(lasciata.panel_media)) {
      s.media_bg = lasciata.panel_media;
    }
  }
  s.disposizione_prima = { type, settings: lasciata };
  return { type: verso, settings: s };
}
