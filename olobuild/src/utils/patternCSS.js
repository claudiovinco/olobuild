/**
 * CSS Pattern Library — 33 pattern CSS-only (SVG data URI + CSS gradients).
 *
 * Usato sia in BackgroundControls.vue (preview) sia in useBackgroundStyle.js (canvas).
 * Il PHP ha una copia equivalente in class-frontend-renderer.php → build_pattern_css().
 */

export const patternList = [
  // Lines
  { value: 'horizontal-lines', label: 'Linee orizzontali' },
  { value: 'vertical-lines', label: 'Linee verticali' },
  { value: 'diagonal-lines', label: 'Linee diagonali' },
  { value: 'diagonal-lines-reverse', label: 'Linee diagonali inverse' },
  { value: 'crosshatch', label: 'Griglia a croce' },
  { value: 'diagonal-crosshatch', label: 'Griglia diagonale' },
  // Dots
  { value: 'dots', label: 'Puntini' },
  { value: 'dots-large', label: 'Puntini grandi' },
  { value: 'dots-grid', label: 'Puntini griglia' },
  // Geometric
  { value: 'checkerboard', label: 'Scacchiera' },
  { value: 'triangles', label: 'Triangoli' },
  { value: 'diamonds', label: 'Rombi' },
  { value: 'hexagons', label: 'Esagoni' },
  { value: 'zigzag', label: 'Zigzag' },
  { value: 'chevrons', label: 'Chevron' },
  { value: 'herringbone', label: 'Spina di pesce' },
  // Waves & Organic
  { value: 'waves', label: 'Onde' },
  { value: 'wavy-lines', label: 'Linee ondulate' },
  { value: 'scales', label: 'Squame' },
  { value: 'circles', label: 'Cerchi' },
  { value: 'concentric-circles', label: 'Cerchi concentrici' },
  // Textures
  { value: 'carbon-fiber', label: 'Fibra di carbonio' },
  { value: 'graph-paper', label: 'Carta millimetrata' },
  { value: 'lined-paper', label: 'Carta rigata' },
  { value: 'blueprint', label: 'Blueprint' },
  { value: 'noise', label: 'Rumore' },
  { value: 'brick', label: 'Mattoni' },
  { value: 'wood-grain', label: 'Venatura legno' },
  // Decorative
  { value: 'polka-dots', label: 'Polka dots' },
  { value: 'stars', label: 'Stelle' },
  { value: 'crosses', label: 'Croci' },
  { value: 'plus-signs', label: 'Segno +' },
  { value: 'hearts', label: 'Cuori' },
];

/**
 * Converte hex (#rrggbb) + opacity (0-1) in rgba().
 */
function colorToRgba(input, opacity) {
  const s = (input || '#000000').trim();
  // Already rgba/rgb — extract components and apply opacity
  const rgbaMatch = s.match(/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+))?\s*\)/);
  if (rgbaMatch) {
    const r = parseInt(rgbaMatch[1]) || 0;
    const g = parseInt(rgbaMatch[2]) || 0;
    const b = parseInt(rgbaMatch[3]) || 0;
    const a = rgbaMatch[4] != null ? parseFloat(rgbaMatch[4]) : 1;
    return `rgba(${r}, ${g}, ${b}, ${a * opacity})`;
  }
  // Hex
  const h = s.replace('#', '');
  let r, g, b;
  if (h.length === 3) {
    r = parseInt(h[0] + h[0], 16); g = parseInt(h[1] + h[1], 16); b = parseInt(h[2] + h[2], 16);
  } else {
    r = parseInt(h.substring(0, 2), 16); g = parseInt(h.substring(2, 4), 16); b = parseInt(h.substring(4, 6), 16);
  }
  return `rgba(${isNaN(r) ? 0 : r}, ${isNaN(g) ? 0 : g}, ${isNaN(b) ? 0 : b}, ${opacity})`;
}

/**
 * Genera un SVG data URI.
 */
function svgDataUri(svgContent, width, height) {
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}">${svgContent}</svg>`;
  return `url("data:image/svg+xml,${encodeURIComponent(svg)}")`;
}

/**
 * Ritorna un oggetto { backgroundImage, backgroundColor, backgroundSize } per il pattern richiesto.
 *
 * @param {string} type - chiave pattern (es. 'dots', 'hexagons')
 * @param {string} color - colore pattern hex (#rrggbb)
 * @param {string} bgColor - colore sfondo hex (#rrggbb)
 * @param {number} size - dimensione pattern in px (10-100)
 * @param {number} opacity - opacita pattern 0-1
 * @returns {{ backgroundImage: string, backgroundColor: string, backgroundSize: string }}
 */
export function getPatternCSS(type, color, bgColor, size, opacity) {
  const c = colorToRgba(color || '#000000', opacity);
  const sz = size || 20;
  const bg = bgColor || '#ffffff';

  let backgroundImage = '';
  let backgroundSize = `${sz}px ${sz}px`;

  switch (type) {
    // ── Lines ──
    case 'horizontal-lines':
      backgroundImage = `repeating-linear-gradient(0deg, ${c} 0px, ${c} 1px, transparent 1px, transparent ${sz}px)`;
      break;

    case 'vertical-lines':
      backgroundImage = `repeating-linear-gradient(90deg, ${c} 0px, ${c} 1px, transparent 1px, transparent ${sz}px)`;
      break;

    case 'diagonal-lines':
      backgroundImage = `repeating-linear-gradient(45deg, ${c} 0px, ${c} 1px, transparent 1px, transparent ${sz}px)`;
      break;

    case 'diagonal-lines-reverse':
      backgroundImage = `repeating-linear-gradient(-45deg, ${c} 0px, ${c} 1px, transparent 1px, transparent ${sz}px)`;
      break;

    case 'crosshatch':
      backgroundImage = `repeating-linear-gradient(0deg, ${c} 0px, ${c} 1px, transparent 1px, transparent ${sz}px), repeating-linear-gradient(90deg, ${c} 0px, ${c} 1px, transparent 1px, transparent ${sz}px)`;
      break;

    case 'diagonal-crosshatch':
      backgroundImage = `repeating-linear-gradient(45deg, ${c} 0px, ${c} 1px, transparent 1px, transparent ${sz}px), repeating-linear-gradient(-45deg, ${c} 0px, ${c} 1px, transparent 1px, transparent ${sz}px)`;
      break;

    // ── Dots ──
    case 'dots':
      backgroundImage = `radial-gradient(circle, ${c} ${Math.max(1, sz * 0.05)}px, transparent ${Math.max(1, sz * 0.05)}px)`;
      backgroundSize = `${sz}px ${sz}px`;
      break;

    case 'dots-large':
      backgroundImage = `radial-gradient(circle, ${c} ${Math.max(2, sz * 0.15)}px, transparent ${Math.max(2, sz * 0.15)}px)`;
      backgroundSize = `${sz}px ${sz}px`;
      break;

    case 'dots-grid': {
      const dr = Math.max(1, sz * 0.08);
      backgroundImage = `radial-gradient(circle, ${c} ${dr}px, transparent ${dr}px)`;
      backgroundSize = `${sz}px ${sz}px`;
      break;
    }

    // ── Geometric ──
    case 'checkerboard': {
      const half = sz / 2;
      backgroundImage = `linear-gradient(45deg, ${c} 25%, transparent 25%, transparent 75%, ${c} 75%), linear-gradient(45deg, ${c} 25%, transparent 25%, transparent 75%, ${c} 75%)`;
      backgroundSize = `${sz}px ${sz}px`;
      backgroundImage = `linear-gradient(45deg, ${c} 25%, transparent 25%, transparent 75%, ${c} 75%, ${c}), linear-gradient(45deg, ${c} 25%, transparent 25%, transparent 75%, ${c} 75%, ${c})`;
      backgroundSize = `${sz}px ${sz}px`;
      // Checkerboard needs background-position offset
      return {
        backgroundColor: bg,
        backgroundImage,
        backgroundSize,
        backgroundPosition: `0 0, ${half}px ${half}px`,
      };
    }

    case 'triangles': {
      const svgInner = `<polygon points="${sz / 2},${sz * 0.1} ${sz * 0.1},${sz * 0.9} ${sz * 0.9},${sz * 0.9}" fill="${color}" fill-opacity="${opacity}" />`;
      backgroundImage = svgDataUri(svgInner, sz, sz);
      break;
    }

    case 'diamonds': {
      const cx = sz / 2;
      const cy = sz / 2;
      const dx = sz * 0.35;
      const dy = sz * 0.45;
      const svgInner = `<polygon points="${cx},${cy - dy} ${cx + dx},${cy} ${cx},${cy + dy} ${cx - dx},${cy}" fill="${color}" fill-opacity="${opacity}" />`;
      backgroundImage = svgDataUri(svgInner, sz, sz);
      break;
    }

    case 'hexagons': {
      const w = sz;
      const h = Math.round(sz * 0.866);
      const points = [
        [w * 0.25, 0], [w * 0.75, 0], [w, h * 0.5],
        [w * 0.75, h], [w * 0.25, h], [0, h * 0.5],
      ].map(p => p.join(',')).join(' ');
      const svgInner = `<polygon points="${points}" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" />`;
      backgroundImage = svgDataUri(svgInner, w, h);
      backgroundSize = `${w}px ${h}px`;
      break;
    }

    case 'zigzag': {
      const h = sz;
      const w = sz;
      const svgInner = `<polyline points="0,${h} ${w / 4},0 ${w / 2},${h} ${w * 3 / 4},0 ${w},${h}" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1.5" />`;
      backgroundImage = svgDataUri(svgInner, w, h);
      backgroundSize = `${w}px ${h}px`;
      break;
    }

    case 'chevrons': {
      const w = sz;
      const h = sz;
      const svgInner = `<polyline points="0,${h * 0.75} ${w / 2},${h * 0.25} ${w},${h * 0.75}" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1.5" />`;
      backgroundImage = svgDataUri(svgInner, w, h);
      backgroundSize = `${w}px ${h}px`;
      break;
    }

    case 'herringbone': {
      const w = sz;
      const h = sz;
      const svgInner = `<path d="M0,${h / 2} L${w / 2},0 L${w},${h / 2} M0,${h} L${w / 2},${h / 2} L${w},${h}" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" />`;
      backgroundImage = svgDataUri(svgInner, w, h);
      backgroundSize = `${w}px ${h}px`;
      break;
    }

    // ── Waves & Organic ──
    case 'waves': {
      const w = sz * 2;
      const h = sz;
      const svgInner = `<path d="M0,${h / 2} Q${w / 4},0 ${w / 2},${h / 2} Q${w * 3 / 4},${h} ${w},${h / 2}" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1.5" />`;
      backgroundImage = svgDataUri(svgInner, w, h);
      backgroundSize = `${w}px ${h}px`;
      break;
    }

    case 'wavy-lines': {
      const w = sz * 2;
      const h = sz;
      const svgInner = `<path d="M0,${h * 0.3} Q${w / 4},0 ${w / 2},${h * 0.3} Q${w * 3 / 4},${h * 0.6} ${w},${h * 0.3}" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" /><path d="M0,${h * 0.7} Q${w / 4},${h * 0.4} ${w / 2},${h * 0.7} Q${w * 3 / 4},${h} ${w},${h * 0.7}" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" />`;
      backgroundImage = svgDataUri(svgInner, w, h);
      backgroundSize = `${w}px ${h}px`;
      break;
    }

    case 'scales': {
      const w = sz;
      const h = sz;
      const svgInner = `<path d="M0,${h} A${w / 2},${h / 2} 0 0,1 ${w / 2},${h / 2} A${w / 2},${h / 2} 0 0,1 ${w},${h}" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" /><path d="M${-w / 2},${h / 2} A${w / 2},${h / 2} 0 0,1 0,0" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" /><path d="M${w},0 A${w / 2},${h / 2} 0 0,1 ${w * 1.5},${h / 2}" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" />`;
      backgroundImage = svgDataUri(svgInner, w, h);
      backgroundSize = `${w}px ${h}px`;
      break;
    }

    case 'circles': {
      const r = sz * 0.35;
      const svgInner = `<circle cx="${sz / 2}" cy="${sz / 2}" r="${r}" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" />`;
      backgroundImage = svgDataUri(svgInner, sz, sz);
      backgroundSize = `${sz}px ${sz}px`;
      break;
    }

    case 'concentric-circles': {
      const svgInner = `<circle cx="${sz / 2}" cy="${sz / 2}" r="${sz * 0.4}" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" /><circle cx="${sz / 2}" cy="${sz / 2}" r="${sz * 0.2}" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" />`;
      backgroundImage = svgDataUri(svgInner, sz, sz);
      backgroundSize = `${sz}px ${sz}px`;
      break;
    }

    // ── Textures ──
    case 'carbon-fiber': {
      const half = sz / 2;
      backgroundImage = `radial-gradient(circle, ${c} 1px, transparent 1px), radial-gradient(circle, ${c} 1px, transparent 1px)`;
      backgroundSize = `${sz}px ${sz}px`;
      return {
        backgroundColor: bg,
        backgroundImage,
        backgroundSize,
        backgroundPosition: `0 0, ${half}px ${half}px`,
      };
    }

    case 'graph-paper': {
      backgroundImage = `linear-gradient(${c} 1px, transparent 1px), linear-gradient(90deg, ${c} 1px, transparent 1px)`;
      backgroundSize = `${sz}px ${sz}px`;
      break;
    }

    case 'lined-paper': {
      backgroundImage = `repeating-linear-gradient(0deg, ${c} 0px, ${c} 1px, transparent 1px, transparent ${sz}px)`;
      break;
    }

    case 'blueprint': {
      const cThin = colorToRgba(color || '#000000', opacity * 0.5);
      const subSz = sz / 5;
      backgroundImage = `linear-gradient(${c} 1px, transparent 1px), linear-gradient(90deg, ${c} 1px, transparent 1px), linear-gradient(${cThin} 1px, transparent 1px), linear-gradient(90deg, ${cThin} 1px, transparent 1px)`;
      backgroundSize = `${sz}px ${sz}px, ${sz}px ${sz}px, ${subSz}px ${subSz}px, ${subSz}px ${subSz}px`;
      break;
    }

    case 'noise': {
      // Noise via tiny random SVG rects
      let rects = '';
      const step = Math.max(2, Math.round(sz / 10));
      for (let x = 0; x < sz; x += step) {
        for (let y = 0; y < sz; y += step) {
          const o = (Math.sin(x * 12.9898 + y * 78.233) * 43758.5453) % 1;
          const a = Math.abs(o) * opacity;
          rects += `<rect x="${x}" y="${y}" width="${step}" height="${step}" fill="${color}" fill-opacity="${a.toFixed(2)}" />`;
        }
      }
      backgroundImage = svgDataUri(rects, sz, sz);
      backgroundSize = `${sz}px ${sz}px`;
      break;
    }

    case 'brick': {
      const w = sz * 2;
      const h = sz;
      const halfW = w / 2;
      const halfH = h / 2;
      const svgInner = `<rect x="0" y="0" width="${w}" height="${h}" fill="none" /><line x1="0" y1="${halfH}" x2="${w}" y2="${halfH}" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" /><line x1="0" y1="0" x2="0" y2="${h}" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" /><line x1="${halfW}" y1="${halfH}" x2="${halfW}" y2="${h}" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" /><line x1="${w}" y1="0" x2="${w}" y2="${halfH}" stroke="${color}" stroke-opacity="${opacity}" stroke-width="1" />`;
      backgroundImage = svgDataUri(svgInner, w, h);
      backgroundSize = `${w}px ${h}px`;
      break;
    }

    case 'wood-grain': {
      const w = sz * 3;
      const h = sz;
      const svgInner = `<path d="M0,${h * 0.2} Q${w * 0.25},${h * 0.15} ${w * 0.5},${h * 0.22} Q${w * 0.75},${h * 0.28} ${w},${h * 0.2}" fill="none" stroke="${color}" stroke-opacity="${opacity * 0.6}" stroke-width="0.5" /><path d="M0,${h * 0.45} Q${w * 0.3},${h * 0.38} ${w * 0.5},${h * 0.46} Q${w * 0.7},${h * 0.52} ${w},${h * 0.44}" fill="none" stroke="${color}" stroke-opacity="${opacity * 0.8}" stroke-width="0.7" /><path d="M0,${h * 0.7} Q${w * 0.2},${h * 0.65} ${w * 0.5},${h * 0.72} Q${w * 0.8},${h * 0.78} ${w},${h * 0.7}" fill="none" stroke="${color}" stroke-opacity="${opacity}" stroke-width="0.5" /><path d="M0,${h * 0.9} Q${w * 0.35},${h * 0.86} ${w * 0.5},${h * 0.91} Q${w * 0.65},${h * 0.96} ${w},${h * 0.9}" fill="none" stroke="${color}" stroke-opacity="${opacity * 0.5}" stroke-width="0.4" />`;
      backgroundImage = svgDataUri(svgInner, w, h);
      backgroundSize = `${w}px ${h}px`;
      break;
    }

    // ── Decorative ──
    case 'polka-dots': {
      const r = Math.max(2, sz * 0.2);
      const half = sz / 2;
      backgroundImage = `radial-gradient(circle ${r}px, ${c} 100%, transparent 100%), radial-gradient(circle ${r}px, ${c} 100%, transparent 100%)`;
      backgroundSize = `${sz}px ${sz}px`;
      return {
        backgroundColor: bg,
        backgroundImage,
        backgroundSize,
        backgroundPosition: `0 0, ${half}px ${half}px`,
      };
    }

    case 'stars': {
      const cx = sz / 2;
      const cy = sz / 2;
      const outerR = sz * 0.4;
      const innerR = sz * 0.16;
      let points = '';
      for (let i = 0; i < 5; i++) {
        const outerAngle = (i * 72 - 90) * Math.PI / 180;
        const innerAngle = ((i * 72) + 36 - 90) * Math.PI / 180;
        points += `${cx + outerR * Math.cos(outerAngle)},${cy + outerR * Math.sin(outerAngle)} `;
        points += `${cx + innerR * Math.cos(innerAngle)},${cy + innerR * Math.sin(innerAngle)} `;
      }
      const svgInner = `<polygon points="${points.trim()}" fill="${color}" fill-opacity="${opacity}" />`;
      backgroundImage = svgDataUri(svgInner, sz, sz);
      backgroundSize = `${sz}px ${sz}px`;
      break;
    }

    case 'crosses': {
      const w = sz;
      const h = sz;
      const arm = sz * 0.3;
      const mid = sz / 2;
      const t = Math.max(1, sz * 0.08);
      const svgInner = `<line x1="${mid}" y1="${mid - arm}" x2="${mid}" y2="${mid + arm}" stroke="${color}" stroke-opacity="${opacity}" stroke-width="${t}" /><line x1="${mid - arm}" y1="${mid}" x2="${mid + arm}" y2="${mid}" stroke="${color}" stroke-opacity="${opacity}" stroke-width="${t}" />`;
      backgroundImage = svgDataUri(svgInner, w, h);
      backgroundSize = `${w}px ${h}px`;
      break;
    }

    case 'plus-signs': {
      const w = sz;
      const h = sz;
      const arm = sz * 0.25;
      const mid = sz / 2;
      const t = Math.max(2, sz * 0.12);
      const svgInner = `<rect x="${mid - t / 2}" y="${mid - arm}" width="${t}" height="${arm * 2}" rx="0.5" fill="${color}" fill-opacity="${opacity}" /><rect x="${mid - arm}" y="${mid - t / 2}" width="${arm * 2}" height="${t}" rx="0.5" fill="${color}" fill-opacity="${opacity}" />`;
      backgroundImage = svgDataUri(svgInner, w, h);
      backgroundSize = `${w}px ${h}px`;
      break;
    }

    case 'hearts': {
      const w = sz;
      const h = sz;
      const scale = sz / 24;
      const svgInner = `<g transform="translate(${w / 2 - 12 * scale}, ${h / 2 - 10 * scale}) scale(${scale})"><path d="M12,21.35 L10.55,20.03 C5.4,15.36 2,12.28 2,8.5 C2,5.42 4.42,3 7.5,3 C9.24,3 10.91,3.81 12,5.09 C13.09,3.81 14.76,3 16.5,3 C19.58,3 22,5.42 22,8.5 C22,12.28 18.6,15.36 13.45,20.04 L12,21.35Z" fill="${color}" fill-opacity="${opacity}" /></g>`;
      backgroundImage = svgDataUri(svgInner, w, h);
      backgroundSize = `${w}px ${h}px`;
      break;
    }

    default:
      // Fallback dots
      backgroundImage = `radial-gradient(circle, ${c} 1px, transparent 1px)`;
      backgroundSize = `${sz}px ${sz}px`;
      break;
  }

  return {
    backgroundColor: bg,
    backgroundImage,
    backgroundSize,
  };
}
