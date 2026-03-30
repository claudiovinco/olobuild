'use strict';

/**
 * Olobuild — Shared JS utilities.
 * Loaded before all olo-*.js frontend scripts.
 *
 * ES module with backward-compatible window.oloUtils exposure.
 */

/**
 * Escape HTML entities in a string.
 */
export function escHtml(str) {
  if (!str) return '';
  var div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

/**
 * Mouse tilt 3D effect — elements with data-olo-tilt="intensity"
 */
export function initMouseTilt() {
  var els = document.querySelectorAll('[data-olo-tilt]');
  els.forEach(function(el) {
    var intensity = parseInt(el.getAttribute('data-olo-tilt')) || 15;
    el.style.transition = 'transform 0.1s ease-out';
    el.style.willChange = 'transform';
    el.addEventListener('mousemove', function(e) {
      var rect = el.getBoundingClientRect();
      var x = (e.clientX - rect.left) / rect.width - 0.5;
      var y = (e.clientY - rect.top) / rect.height - 0.5;
      var rotateX = -(y * intensity);
      var rotateY = x * intensity;
      el.style.transform = 'perspective(600px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg)';
    });
    el.addEventListener('mouseleave', function() {
      el.style.transform = 'perspective(600px) rotateX(0) rotateY(0)';
    });
  });
}

/**
 * Mouse tracking — elements with data-olo-track="speed"
 * Element follows cursor with slight delay
 */
export function initMouseTrack() {
  var els = document.querySelectorAll('[data-olo-track]');
  els.forEach(function(el) {
    var speed = parseInt(el.getAttribute('data-olo-track')) || 3;
    var factor = speed * 3;
    el.style.transition = 'transform 0.3s ease-out';
    el.style.willChange = 'transform';
    el.addEventListener('mousemove', function(e) {
      var rect = el.getBoundingClientRect();
      var x = (e.clientX - rect.left) / rect.width - 0.5;
      var y = (e.clientY - rect.top) / rect.height - 0.5;
      el.style.transform = 'translate3d(' + (x * factor) + 'px, ' + (y * factor) + 'px, 0)';
    });
    el.addEventListener('mouseleave', function() {
      el.style.transform = 'translate3d(0, 0, 0)';
    });
  });
}

// Backward compatibility: expose all public functions on window.oloUtils
window.oloUtils = window.oloUtils || {};
window.oloUtils.escHtml = escHtml;
window.oloUtils.initMouseTilt = initMouseTilt;
window.oloUtils.initMouseTrack = initMouseTrack;

// Auto-init on DOMContentLoaded (or immediately if DOM is already ready)
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', function() {
    initMouseTilt();
    initMouseTrack();
  });
} else {
  initMouseTilt();
  initMouseTrack();
}
