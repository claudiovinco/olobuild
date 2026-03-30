/**
 * Olobuild SVG Animator — stroke-draw animation engine
 * Zero dependencies, pure JS + CSS stroke-dasharray
 * ES module format
 */

// ── Easing functions (t: 0-1) ──
const easings = {
  linear: function(t) { return t; },
  ease: function(t) { return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t; },
  'ease-in': function(t) { return t * t * t; },
  'ease-out': function(t) { return 1 - Math.pow(1 - t, 3); },
  'ease-in-out': function(t) { return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2; }
};

function getEasing(name) {
  if (easings[name]) return easings[name];
  // cubic-bezier(a,b,c,d) — simplified approximation
  var m = name.match(/cubic-bezier\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)\s*\)/);
  if (m) {
    var x1 = parseFloat(m[1]), y1 = parseFloat(m[2]), x2 = parseFloat(m[3]), y2 = parseFloat(m[4]);
    return function(t) {
      // Simple cubic bezier approximation
      var ct = 1 - t;
      return 3 * ct * ct * t * y1 + 3 * ct * t * t * y2 + t * t * t;
    };
  }
  return easings.ease;
}

// ── Get drawable length for any SVG element ──
function getDrawLength(el) {
  try { return el.getTotalLength(); } catch(e) {}
  var tag = el.tagName.toLowerCase();
  if (tag === 'circle') return 2 * Math.PI * (parseFloat(el.getAttribute('r')) || 0);
  if (tag === 'rect') return 2 * ((parseFloat(el.getAttribute('width')) || 0) + (parseFloat(el.getAttribute('height')) || 0));
  if (tag === 'ellipse') {
    var rx = parseFloat(el.getAttribute('rx')) || 0;
    var ry = parseFloat(el.getAttribute('ry')) || 0;
    return Math.PI * (3 * (rx + ry) - Math.sqrt((3 * rx + ry) * (rx + 3 * ry)));
  }
  if (tag === 'line') {
    var x1 = parseFloat(el.getAttribute('x1')) || 0, y1 = parseFloat(el.getAttribute('y1')) || 0;
    var x2 = parseFloat(el.getAttribute('x2')) || 0, y2 = parseFloat(el.getAttribute('y2')) || 0;
    return Math.sqrt((x2 - x1) * (x2 - x1) + (y2 - y1) * (y2 - y1));
  }
  return 0;
}

// ── Animate a single value with rAF ──
function animateValue(from, to, duration, easeFn, onUpdate, onDone) {
  var start = performance.now();
  function step(now) {
    var t = Math.min((now - start) / duration, 1);
    var val = from + (to - from) * easeFn(t);
    onUpdate(val);
    if (t < 1) requestAnimationFrame(step);
    else if (onDone) onDone();
  }
  requestAnimationFrame(step);
}

// ── Shuffle array (Fisher-Yates) ──
function shuffle(arr) {
  var a = arr.slice();
  for (var i = a.length - 1; i > 0; i--) {
    var j = Math.floor(Math.random() * (i + 1));
    var tmp = a[i]; a[i] = a[j]; a[j] = tmp;
  }
  return a;
}

// ── Init a single SVG Animator container ──
function initContainer(wrap) {
  if (wrap.__oloSvgaInit) return;
  wrap.__oloSvgaInit = true;

  var config;
  try { config = JSON.parse(wrap.getAttribute('data-olo-svga')); } catch(e) { return; }

  var svg = wrap.querySelector('svg');
  if (!svg) return;

  var selectors = 'path, line, polyline, polygon, circle, ellipse, rect';
  var allEls = svg.querySelectorAll(selectors);
  if (!allEls.length) return;

  var duration = config.duration || 1500;
  var easeFn = getEasing(config.easing || 'ease');
  var reverse = config.reverse || false;
  var showFill = config.showFill !== false;
  var fillDelay = config.fillDelay || 300;
  var fillDuration = config.fillDuration || 500;
  var stagger = config.stagger || 100;
  var animType = config.type || 'draw';
  var sequence = config.sequence || 'delayed';

  // Prepare elements
  var items = [];
  allEls.forEach(function(el) {
    var len = getDrawLength(el);
    if (len <= 0) return;

    // Store original styles
    var origFill = el.getAttribute('fill') || getComputedStyle(el).fill || '';
    var origStroke = el.getAttribute('stroke') || getComputedStyle(el).stroke || '';
    var origOpacity = el.style.opacity;

    // Apply stroke overrides
    if (config.strokeWidth != null) el.style.strokeWidth = config.strokeWidth + 'px';
    if (config.strokeColor) el.style.stroke = config.strokeColor;
    if (config.strokeLinecap) el.style.strokeLinecap = config.strokeLinecap;
    if (config.strokeLinejoin) el.style.strokeLinejoin = config.strokeLinejoin;

    // Ensure stroke is visible for draw animation
    if (animType === 'draw' || animType === 'fill' || animType === 'loop-draw') {
      if (!el.getAttribute('stroke')) {
        if (!el.style.stroke || el.style.stroke === 'none') {
          el.style.stroke = config.strokeColor || '#333';
        }
      }
      if (!el.style.strokeWidth) {
        if (!el.getAttribute('stroke-width')) {
          el.style.strokeWidth = config.strokeWidth ? config.strokeWidth + 'px' : '2px';
        }
      }
    }

    items.push({ el: el, len: len, origFill: origFill, origStroke: origStroke, origOpacity: origOpacity });
  });

  if (!items.length) return;

  // Sequence order
  var ordered = items.slice();
  if (sequence === 'random') ordered = shuffle(ordered);

  function resetAll() {
    ordered.forEach(function(item) {
      var el = item.el;
      if (animType === 'fade') {
        el.style.opacity = '0';
      } else {
        el.style.strokeDasharray = item.len;
        el.style.strokeDashoffset = reverse ? -item.len : item.len;
      }
      if (showFill) {
        if (config.fillColor) el.style.fill = config.fillColor;
        el.style.fillOpacity = '0';
      } else {
        el.style.fillOpacity = '0';
      }
    });
  }

  function animateAll(onComplete) {
    var total = ordered.length;
    var done = 0;

    ordered.forEach(function(item, i) {
      var el = item.el;
      var elDelay = config.delay || 0;

      // Calculate per-element delay based on sequence
      if (sequence === 'sync') {
        // no additional delay
      } else if (sequence === 'one-by-one') {
        elDelay += i * (duration / total);
      } else {
        // delayed or random
        elDelay += i * stagger;
      }

      setTimeout(function() {
        if (animType === 'fade') {
          animateValue(0, 1, duration, easeFn, function(v) {
            el.style.opacity = v;
          }, function() {
            done++;
            if (done === total) { if (onComplete) onComplete(); }
          });
        } else {
          // Draw animation
          var from = reverse ? -item.len : item.len;
          animateValue(from, 0, duration, easeFn, function(v) {
            el.style.strokeDashoffset = v;
          }, function() {
            // Show fill after draw completes
            if (showFill) {
              setTimeout(function() {
                animateValue(0, 1, fillDuration, easeFn, function(v) {
                  el.style.fillOpacity = v;
                });
              }, fillDelay);
            }
            done++;
            if (done === total) { if (onComplete) onComplete(); }
          });
        }
      }, elDelay);
    });
  }

  function eraseAll(onComplete) {
    var total = ordered.length;
    var done = 0;
    ordered.forEach(function(item, i) {
      var el = item.el;
      el.style.fillOpacity = '0';
      var to = reverse ? -item.len : item.len;
      animateValue(0, to, duration * 0.7, easeFn, function(v) {
        el.style.strokeDashoffset = v;
      }, function() {
        done++;
        if (done === total) { if (onComplete) onComplete(); }
      });
    });
  }

  // Replay function — exposed on the DOM element
  wrap.__oloSvgaReplay = function() {
    resetAll();
    setTimeout(function() { animateAll(); }, 50);
  };

  // Initial reset
  resetAll();

  // ── Trigger setup ──
  var triggered = false;

  function doAnimate() {
    if (triggered) { if (!config.loop) return; }
    triggered = true;
    animateAll(function() {
      if (config.loop) {
        setTimeout(function() {
          resetAll();
          setTimeout(function() { doAnimate(); }, 50);
        }, config.loopPause || 500);
      }
    });
  }

  if (config.trigger === 'auto') {
    doAnimate();
  } else if (config.trigger === 'viewport') {
    var obs = new IntersectionObserver(function(entries) {
      entries.forEach(function(e) {
        if (e.isIntersecting) {
          doAnimate();
          if (!config.erase) { obs.unobserve(wrap); }
        } else if (config.erase) {
          if (triggered) { eraseAll(); triggered = false; }
        }
      });
    }, { threshold: 0.15 });
    obs.observe(wrap);
  } else if (config.trigger === 'hover') {
    wrap.addEventListener('mouseenter', function() { doAnimate(); });
    if (config.erase) {
      wrap.addEventListener('mouseleave', function() { eraseAll(); triggered = false; });
    }
  } else if (config.trigger === 'click') {
    wrap.style.cursor = 'pointer';
    wrap.addEventListener('click', function() {
      if (triggered) {
        eraseAll(); triggered = false;
      } else {
        doAnimate();
      }
    });
  }
}

// ── Init all containers ──
export function initAll() {
  document.querySelectorAll('[data-olo-svga]').forEach(initContainer);
}

// ── Re-init (single container or all) ──
export function reinit(container) {
  if (container) {
    container.__oloSvgaInit = false;
    initContainer(container);
  } else {
    document.querySelectorAll('[data-olo-svga]').forEach(function(el) {
      el.__oloSvgaInit = false;
      initContainer(el);
    });
  }
}

// Run on DOMContentLoaded or immediately
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAll);
} else {
  initAll();
}

// Expose for builder iframe reinit (backward compat)
window.__oloSvgaInit = reinit;
