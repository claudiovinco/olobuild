/**
 * Olobuild Bezier Parallax Engine v3
 * Scroll-linked animations with true 2D cubic-bezier motion paths.
 * ES module format.
 *
 * Editor coordinate system: Y-up positive (math convention).
 * CSS coordinate system: Y-down positive -> engine negates Y.
 *
 * Control points cpOutX/cpOutY and cpInX/cpInY are ABSOLUTE coordinates
 * in the same space as keyframe x/y values.
 */

// --- 2D Cubic Bezier path evaluation ---
// P0 = start, P1 = cpOut of start, P2 = cpIn of end, P3 = end
function bezier2D(t, p0x, p0y, p1x, p1y, p2x, p2y, p3x, p3y) {
  var mt = 1 - t;
  var mt2 = mt * mt;
  var mt3 = mt2 * mt;
  var t2 = t * t;
  var t3 = t2 * t;
  return {
    x: mt3 * p0x + 3 * mt2 * t * p1x + 3 * mt * t2 * p2x + t3 * p3x,
    y: mt3 * p0y + 3 * mt2 * t * p1y + 3 * mt * t2 * p2y + t3 * p3y
  };
}

// --- Scalar cubic bezier easing (for scale, rotate, opacity, blur) ---
function cubicBezierEasing(t, p1x, p1y, p2x, p2y) {
  var cx = 3 * p1x, bx = 3 * (p2x - p1x) - cx, ax = 1 - cx - bx;
  var cy = 3 * p1y, by = 3 * (p2y - p1y) - cy, ay = 1 - cy - by;
  function sX(t) { return ((ax * t + bx) * t + cx) * t; }
  function sY(t) { return ((ay * t + by) * t + cy) * t; }
  function dX(t) { return (3 * ax * t + 2 * bx) * t + cx; }
  var g = t;
  for (var i = 0; i < 8; i++) {
    var err = sX(g) - t;
    if (Math.abs(err) < 1e-6) break;
    var d = dX(g);
    if (Math.abs(d) < 1e-6) break;
    g -= err / d;
  }
  return sY(Math.max(0, Math.min(1, g)));
}

function lerp(a, b, t) { return a + (b - a) * t; }

function interpolateScalar(a, b, t, bezierCurve) {
  if (bezierCurve && bezierCurve.length === 4) {
    t = cubicBezierEasing(t, bezierCurve[0], bezierCurve[1], bezierCurve[2], bezierCurve[3]);
  }
  return lerp(a, b, t);
}

// --- Get element's ORIGINAL top offset (ignoring transforms) ---
function getOriginalTop(el) {
  var saved = el.style.transform;
  el.style.transform = 'none';
  var top = el.getBoundingClientRect().top + window.pageYOffset;
  el.style.transform = saved;
  return top;
}

// --- Main ---
var elements = [];
var ticking = false;

export function init() {
  var existing = new Map();
  elements.forEach(function (e) { existing.set(e.el, e); });
  elements = [];

  var nodes = document.querySelectorAll('[data-olo-bezier-parallax]');
  nodes.forEach(function (el) {
    try {
      var config = JSON.parse(el.getAttribute('data-olo-bezier-parallax'));
      var kf = config.keyframes || [];
      if (kf.length < 2) return;

      kf.sort(function (a, b) { return (a.pos || 0) - (b.pos || 0); });

      var prev = existing.get(el);
      var origTop = prev ? prev.origTop : getOriginalTop(el);

      // Apply z-index (default 10 so animated tile stays above siblings)
      var zi = config.zIndex != null ? parseInt(config.zIndex) : 10;
      if (zi !== 0) {
        el.style.zIndex = zi;
        // Ensure z-index takes effect (needs positioning context)
        var pos = window.getComputedStyle(el).position;
        if (pos === 'static') el.style.position = 'relative';
      }

      elements.push({
        el: el,
        keyframes: kf,
        origTop: origTop,
        height: el.offsetHeight || 100,
        nomobile: config.nomobile !== false,
        smooth: parseFloat(config.smooth) || 0,
        scrollRange: config.scrollRange || 'viewport',
        scrollStart: parseFloat(config.scrollStart) || 0,
        scrollEnd: parseFloat(config.scrollEnd) || 100,
        bgMode: !!config.bgMode,
        bgOrigPos: el.style.backgroundPosition || window.getComputedStyle(el).backgroundPosition || 'center center',
        // For JS-based smoothing
        curX: prev ? prev.curX : 0,
        curY: prev ? prev.curY : 0,
        curScale: prev ? prev.curScale : 1,
        curRotate: prev ? prev.curRotate : 0,
        curOpacity: prev ? prev.curOpacity : 1,
        curBlur: prev ? prev.curBlur : 0,
      });
    } catch (e) { /* skip */ }
  });
}

function getProgress(entry) {
  var scrollY = window.pageYOffset;
  var vh = window.innerHeight;
  var start, end;

  if (entry.scrollRange === 'page') {
    // Full page: 0% = top of page, 100% = bottom of page
    var docH = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);
    var maxScroll = docH - vh;
    start = 0;
    end = maxScroll > 0 ? maxScroll : 1;
    return Math.max(0, Math.min(1, scrollY / end));
  }

  if (entry.scrollRange === 'custom') {
    // Custom: scrollStart/scrollEnd are % of total page scroll
    var docH2 = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);
    var maxScroll2 = docH2 - vh;
    if (maxScroll2 <= 0) return 0;
    start = maxScroll2 * (entry.scrollStart / 100);
    end = maxScroll2 * (entry.scrollEnd / 100);
    var range2 = end - start;
    if (range2 <= 0) return 0;
    return Math.max(0, Math.min(1, (scrollY - start) / range2));
  }

  // Default: viewport-based (tile enters -> tile exits)
  start = entry.origTop - vh;
  end = entry.origTop + entry.height;
  var range = end - start;
  if (range <= 0) return 0;
  return Math.max(0, Math.min(1, (scrollY - start) / range));
}

// Responsive scale factor: X/Y values designed for 1440px, scale down proportionally
var REF_WIDTH = 1440;
function getResponsiveScale() {
  return Math.min(1, window.innerWidth / REF_WIDTH);
}

function applyKeyframes(entry, progress) {
  var kf = entry.keyframes;
  var pct = progress * 100;
  var rScale = getResponsiveScale();

  // Find surrounding keyframes
  var fromIdx = 0, toIdx = kf.length - 1;
  for (var i = 0; i < kf.length - 1; i++) {
    if (pct >= (kf[i].pos || 0) && pct <= (kf[i + 1].pos || 100)) {
      fromIdx = i;
      toIdx = i + 1;
      break;
    }
  }
  var from = kf[fromIdx];
  var to = kf[toIdx];

  // Local progress between from and to
  var range = (to.pos || 100) - (from.pos || 0);
  var localT = range > 0 ? (pct - (from.pos || 0)) / range : 0;
  localT = Math.max(0, Math.min(1, localT));

  // --- 2D Bezier path for X/Y ---
  var targetX, targetY;
  var hasCurve = (from.cpOutX != null || to.cpInX != null);

  if (hasCurve) {
    // Use 2D cubic bezier path (scaled for responsive)
    var p0x = (from.x || 0) * rScale;
    var p0y = (from.y || 0) * rScale;
    var p3x = (to.x || 0) * rScale;
    var p3y = (to.y || 0) * rScale;
    var p1x = (from.cpOutX != null ? from.cpOutX : lerp(from.x || 0, to.x || 0, 0.33)) * rScale;
    var p1y = (from.cpOutY != null ? from.cpOutY : lerp(from.y || 0, to.y || 0, 0.33)) * rScale;
    var p2x = (to.cpInX != null ? to.cpInX : lerp(from.x || 0, to.x || 0, 0.66)) * rScale;
    var p2y = (to.cpInY != null ? to.cpInY : lerp(from.y || 0, to.y || 0, 0.66)) * rScale;

    var point = bezier2D(localT, p0x, p0y, p1x, p1y, p2x, p2y, p3x, p3y);
    targetX = point.x;
    targetY = point.y;
  } else {
    // Linear interpolation (scaled for responsive)
    targetX = lerp((from.x || 0) * rScale, (to.x || 0) * rScale, localT);
    targetY = lerp((from.y || 0) * rScale, (to.y || 0) * rScale, localT);
  }

  // --- Scalar properties (linear or with per-property easing) ---
  var targetScale = interpolateScalar(
    from.scale != null ? from.scale : 1,
    to.scale != null ? to.scale : 1,
    localT, to.easeScale || to.ease
  );
  var targetRotate = interpolateScalar(from.rotate || 0, to.rotate || 0, localT, to.easeRotate || to.ease);
  var targetOpacity = interpolateScalar(
    from.opacity != null ? from.opacity : 1,
    to.opacity != null ? to.opacity : 1,
    localT, to.easeOpacity || to.ease
  );
  var targetBlur = interpolateScalar(from.blur || 0, to.blur || 0, localT, to.ease);

  // --- JS-based smoothing (lerp towards target) ---
  var sf = entry.smooth > 0 ? Math.max(0.05, 1 - entry.smooth / 100) : 1;
  entry.curX = lerp(entry.curX, targetX, sf);
  entry.curY = lerp(entry.curY, targetY, sf);
  entry.curScale = lerp(entry.curScale, targetScale, sf);
  entry.curRotate = lerp(entry.curRotate, targetRotate, sf);
  entry.curOpacity = lerp(entry.curOpacity, targetOpacity, sf);
  entry.curBlur = lerp(entry.curBlur, targetBlur, sf);

  var x = entry.curX;
  var y = -entry.curY;  // NEGATE Y: editor Y-up -> CSS Y-down
  var scale = entry.curScale;
  var rotate = entry.curRotate;
  var opacity = entry.curOpacity;
  var blur = entry.curBlur;

  // Apply
  if (entry.bgMode) {
    // Background mode: animate background-position instead of transform
    entry.el.style.backgroundPosition = 'calc(50% + ' + x.toFixed(1) + 'px) calc(50% + ' + y.toFixed(1) + 'px)';
    // Scale/rotate still apply as transform on the div
    var bgTransform = '';
    if (Math.abs(scale - 1) > 0.001) bgTransform += 'scale(' + scale.toFixed(3) + ')';
    if (Math.abs(rotate) > 0.1) bgTransform += ' rotate(' + rotate.toFixed(1) + 'deg)';
    entry.el.style.transform = bgTransform || '';
  } else {
    var transform = 'translate3d(' + x.toFixed(1) + 'px,' + y.toFixed(1) + 'px,0)';
    if (Math.abs(scale - 1) > 0.001) transform += ' scale(' + scale.toFixed(3) + ')';
    if (Math.abs(rotate) > 0.1) transform += ' rotate(' + rotate.toFixed(1) + 'deg)';
    entry.el.style.transform = transform;
  }
  entry.el.style.opacity = opacity < 0.999 ? opacity.toFixed(3) : '';
  entry.el.style.filter = blur > 0.1 ? 'blur(' + blur.toFixed(1) + 'px)' : '';
}

// Smooth animation loop
var smoothRunning = false;
function smoothLoop() {
  var needsMore = false;
  var isMobile = window.innerWidth < 960;
  elements.forEach(function (entry) {
    if (isMobile && entry.nomobile) return;
    if (entry.smooth <= 0) return;
    var progress = getProgress(entry);
    applyKeyframes(entry, progress);
    // crude convergence check
    var kf = entry.keyframes;
    var pct = progress * 100;
    var from = kf[0], to = kf[kf.length - 1];
    for (var i = 0; i < kf.length - 1; i++) {
      if (pct >= (kf[i].pos || 0) && pct <= (kf[i + 1].pos || 100)) {
        from = kf[i]; to = kf[i + 1]; break;
      }
    }
    if (Math.abs(entry.curX - (to.x || 0)) > 0.5 || Math.abs(entry.curY - (to.y || 0)) > 0.5) {
      needsMore = true;
    }
  });
  if (needsMore) requestAnimationFrame(smoothLoop);
  else smoothRunning = false;
}

function onScroll() {
  if (ticking) return;
  ticking = true;
  requestAnimationFrame(function () {
    var isMobile = window.innerWidth < 960;
    elements.forEach(function (entry) {
      if (isMobile && entry.nomobile) return;
      applyKeyframes(entry, getProgress(entry));
    });
    ticking = false;
  });
  var hasSmooth = elements.some(function (e) { return e.smooth > 0; });
  if (hasSmooth && !smoothRunning) {
    smoothRunning = true;
    requestAnimationFrame(smoothLoop);
  }
}

function startup() { init(); onScroll(); }
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', startup);
} else {
  startup();
}
window.addEventListener('scroll', onScroll, { passive: true });
window.addEventListener('resize', function () {
  elements.forEach(function (e) { e.origTop = getOriginalTop(e.el); });
  onScroll();
});

// Re-init on lazy-loaded content
var obs = new MutationObserver(function (muts) {
  var found = false;
  muts.forEach(function (m) {
    m.addedNodes.forEach(function (n) {
      if (n.nodeType === 1 && (
        (n.hasAttribute && n.hasAttribute('data-olo-bezier-parallax')) ||
        (n.querySelector && n.querySelector('[data-olo-bezier-parallax]'))
      )) found = true;
    });
  });
  if (found) { init(); onScroll(); }
});
obs.observe(document.body || document.documentElement, { childList: true, subtree: true });
