/**
 * Background style condiviso — elimina duplicazione tra GridCell.vue e OlobuilderGrid.vue.
 *
 * Supporta: solid (con color_opacity), gradient, image, video (poster).
 *
 * Due modalità d'uso:
 *   1) Reactive (computed) — per GridCell dove il bg è un singolo tile reattivo
 *      const { effectiveBg, bgImageStyle, overlayStyle, bgInlineStyle } = useBackgroundStyle(bgGetter);
 *
 *   2) Imperative (funzioni) — per OlobuilderGrid dove si chiamano su nodi diversi
 *      import { resolveNodeBg, buildBgStyle, buildOverlayStyle } from '@/composables/useBackgroundStyle';
 */
import { computed } from 'vue';

// ────────────────────────────────────────────
// Funzioni imperative (usate anche internamente dai computed)
// ────────────────────────────────────────────

/**
 * Risolve l'oggetto bg effettivo di un nodo (tile o section/row).
 * Gestisce il fallback da bg_color legacy.
 */
export function resolveNodeBg(nodeOrStyle) {
  const s = nodeOrStyle?.style ?? nodeOrStyle ?? {};
  if (s.bg && s.bg.type && s.bg.type !== 'none') return s.bg;
  if (s.bg_color) return { type: 'solid', color: s.bg_color };
  return { type: 'none' };
}

/**
 * Costruisce l'oggetto style CSS per un background (solid / gradient / image / video).
 * Per "solid" include gestione color_opacity (rgba).
 */
export function buildBgStyle(bg) {
  if (!bg || bg.type === 'none') return {};

  if (bg.type === 'solid') {
    const color = bg.color || '';
    const opacity = bg.color_opacity ?? 100;
    if (color && opacity < 100) {
      const h = color.replace('#', '');
      const rp = parseInt(h.substring(0, 2), 16); const r = !isNaN(rp) ? rp : 0;
      const gp = parseInt(h.substring(2, 4), 16); const g = !isNaN(gp) ? gp : 0;
      const bp = parseInt(h.substring(4, 6), 16); const b = !isNaN(bp) ? bp : 0;
      return { backgroundColor: `rgba(${r}, ${g}, ${b}, ${opacity / 100})` };
    }
    return { backgroundColor: color };
  }

  if (bg.type === 'gradient') {
    // Multi-stop gradient (new format)
    if (bg.gradient && typeof bg.gradient === 'object' && Array.isArray(bg.gradient.stops) && bg.gradient.stops.length) {
      const stops = bg.gradient.stops.map(s => `${s.color} ${s.position}%`).join(', ');
      if (bg.gradient.type === 'radial') {
        return { background: `radial-gradient(circle, ${stops})` };
      }
      return { background: `linear-gradient(${bg.gradient.angle || 180}deg, ${stops})` };
    }
    // Legacy 2-color format
    return {
      background: `linear-gradient(${bg.gradient_angle || 180}deg, ${bg.gradient_from || '#ffffff'}, ${bg.gradient_to || '#000000'})`,
    };
  }

  if (bg.type === 'image' && bg.image_url) {
    return {
      backgroundImage: `url(${bg.image_url})`,
      backgroundSize: bg.image_size || 'cover',
      backgroundPosition: bg.image_position || 'center center',
      backgroundRepeat: 'no-repeat',
    };
  }

  if (bg.type === 'video' && bg.video_poster) {
    return {
      backgroundImage: `url(${bg.video_poster})`,
      backgroundSize: 'cover',
      backgroundPosition: bg.image_position || 'center center',
      backgroundRepeat: 'no-repeat',
    };
  }

  if (bg.type === 'video') {
    return { backgroundColor: '#1a1a2e' };
  }

  return {};
}

/**
 * Costruisce lo style dell'overlay (colore + opacità).
 * Ritorna null se overlay non attivo.
 */
export function buildOverlayStyle(bg) {
  if (!bg || bg.type === 'none') return null;
  if (!bg.overlay_opacity || parseInt(bg.overlay_opacity) <= 0) return null;
  return {
    backgroundColor: bg.overlay_color || '#000000',
    opacity: parseInt(bg.overlay_opacity) / 100,
  };
}

// ────────────────────────────────────────────
// Composable reattivo (per GridCell e simili)
// ────────────────────────────────────────────

/**
 * @param {Function|import('vue').Ref} bgGetter — funzione o ref che ritorna il nodo (tile)
 *   da cui estrarre lo style. Viene letto `.style` internamente.
 */
export function useBackgroundStyle(bgGetter) {
  const effectiveBg = computed(() => {
    const node = typeof bgGetter === 'function' ? bgGetter() : bgGetter.value;
    return resolveNodeBg(node);
  });

  const hasBgImage = computed(() => {
    const bg = effectiveBg.value;
    return bg.type === 'image' && !!bg.image_url;
  });

  const hasOverlay = computed(() => {
    const bg = effectiveBg.value;
    return bg.type !== 'none' && bg.overlay_opacity && bg.overlay_opacity > 0;
  });

  const bgImageStyle = computed(() => {
    const bg = effectiveBg.value;
    if (bg.type !== 'image' || !bg.image_url) return {};
    return {
      position: 'absolute',
      inset: '0',
      zIndex: 0,
      backgroundImage: `url(${bg.image_url})`,
      backgroundSize: bg.image_size || 'cover',
      backgroundPosition: bg.image_position || 'center center',
      backgroundRepeat: 'no-repeat',
    };
  });

  const overlayStyle = computed(() => {
    const bg = effectiveBg.value;
    if (bg.type === 'none' || !bg.overlay_opacity || bg.overlay_opacity <= 0) return {};
    return {
      position: 'absolute',
      inset: '0',
      zIndex: 1,
      pointerEvents: 'none',
      backgroundColor: bg.overlay_color || '#000000',
      opacity: (bg.overlay_opacity || 0) / 100,
    };
  });

  /** Style inline da applicare al div principale (solo solid e gradient; image va su layer separato) */
  const bgInlineStyle = computed(() => {
    const bg = effectiveBg.value;
    if (bg.type === 'solid') return buildBgStyle(bg);
    if (bg.type === 'gradient') return buildBgStyle(bg);
    return {};
  });

  return { effectiveBg, hasBgImage, hasOverlay, bgImageStyle, overlayStyle, bgInlineStyle };
}
