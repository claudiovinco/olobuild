/**
 * Olobuild — Shared JS utilities.
 * Loaded before all olo-*.js frontend scripts.
 */
(function() {
  'use strict';
  window.oloUtils = window.oloUtils || {};

  oloUtils.escHtml = function(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  };

  /**
   * Mouse tilt 3D effect — elements with data-olo-tilt="intensity"
   */
  function initMouseTilt() {
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
  function initMouseTrack() {
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

  // Init on DOMContentLoaded
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      initMouseTilt();
      initMouseTrack();
    });
  } else {
    initMouseTilt();
    initMouseTrack();
  }
})();
