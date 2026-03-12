/**
 * Olobuild — ProGallery custom lightbox with thumbnail strip.
 * Supports images, video files (MP4/WebM), and embed (YouTube/Vimeo).
 * Vanilla JS, IIFE, no dependencies.
 */
(function () {
  if (window._oloLightbox) return;
  window._oloLightbox = 1;

  /* ── helpers ─────────────────────────────────────────── */
  function clamp(v, min, max) { return Math.max(min, Math.min(max, v)); }

  function isEmbedUrl(url) {
    if (!url) return false;
    return url.indexOf('youtube.com/embed/') !== -1 || url.indexOf('player.vimeo.com/') !== -1;
  }

  /* ── build overlay (once) ───────────────────────────── */
  var overlay, mainImg, mainVideo, iframeWrap, mainIframe, strip, caption, btnPrev, btnNext, btnClose, mainEl;
  var items = [], current = 0, container = null;

  function buildDOM() {
    if (overlay) return;

    overlay = document.createElement('div');
    overlay.className = 'olo-lb-overlay';
    overlay.innerHTML =
      '<div class="olo-lb-wrap">' +
        '<div class="olo-lb-main">' +
          '<img class="olo-lb-img" />' +
          '<video class="olo-lb-video" controls playsinline style="display:none"></video>' +
          '<div class="olo-lb-iframe-wrap" style="display:none"><iframe class="olo-lb-iframe" allow="autoplay;fullscreen" allowfullscreen></iframe></div>' +
          '<div class="olo-lb-caption"></div>' +
          '<button class="olo-lb-arrow olo-lb-prev" aria-label="Precedente">&#10094;</button>' +
          '<button class="olo-lb-arrow olo-lb-next" aria-label="Successiva">&#10095;</button>' +
        '</div>' +
        '<div class="olo-lb-strip"></div>' +
      '</div>' +
      '<button class="olo-lb-fullscreen" aria-label="Schermo intero" title="Schermo intero">&#x26F6;</button>' +
      '<button class="olo-lb-close" aria-label="Chiudi">&times;</button>';
    document.body.appendChild(overlay);

    mainImg     = overlay.querySelector('.olo-lb-img');
    mainVideo   = overlay.querySelector('.olo-lb-video');
    iframeWrap  = overlay.querySelector('.olo-lb-iframe-wrap');
    mainIframe  = overlay.querySelector('.olo-lb-iframe');
    mainEl      = overlay.querySelector('.olo-lb-main');
    strip       = overlay.querySelector('.olo-lb-strip');
    caption     = overlay.querySelector('.olo-lb-caption');
    btnPrev     = overlay.querySelector('.olo-lb-prev');
    btnNext     = overlay.querySelector('.olo-lb-next');
    btnClose    = overlay.querySelector('.olo-lb-close');
    var btnFull = overlay.querySelector('.olo-lb-fullscreen');

    btnClose.addEventListener('click', close);
    btnFull.addEventListener('click', function () {
      if (!document.fullscreenElement) {
        overlay.requestFullscreen().catch(function () {});
      } else {
        document.exitFullscreen();
      }
    });
    btnPrev.addEventListener('click', function () { go(current - 1); });
    btnNext.addEventListener('click', function () { go(current + 1); });
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay || e.target.classList.contains('olo-lb-wrap')) close();
    });

    /* keyboard */
    document.addEventListener('keydown', function (e) {
      if (!overlay.classList.contains('olo-lb-open')) return;
      if (e.key === 'Escape' || e.key === 'Esc') close();
      else if (e.key === 'ArrowLeft') go(current - 1);
      else if (e.key === 'ArrowRight') go(current + 1);
      else if (e.key === 'f' || e.key === 'F') {
        if (!document.fullscreenElement) overlay.requestFullscreen().catch(function () {});
        else document.exitFullscreen();
      }
    });

    /* touch swipe — delegate on .olo-lb-main */
    var sx = 0, sy = 0;
    mainEl.addEventListener('touchstart', function (e) {
      sx = e.touches[0].clientX; sy = e.touches[0].clientY;
    }, { passive: true });
    mainEl.addEventListener('touchend', function (e) {
      var dx = e.changedTouches[0].clientX - sx;
      var dy = e.changedTouches[0].clientY - sy;
      if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) {
        go(dx < 0 ? current + 1 : current - 1);
      }
    }, { passive: true });
  }

  /* ── inject style (once) ────────────────────────────── */
  var styleInjected = false;
  function injectStyle() {
    if (styleInjected) return;
    styleInjected = true;
    var css =
      '.olo-lb-overlay{position:fixed;inset:0;z-index:100000;background:rgba(0,0,0,.92);display:none;align-items:center;justify-content:center}' +
      '.olo-lb-overlay.olo-lb-open{display:flex}' +
      '.olo-lb-wrap{display:flex;width:100%;height:100%;max-width:100vw;max-height:100vh;position:relative}' +
      '.olo-lb-main{flex:1;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;min-width:0}' +
      '.olo-lb-img{max-width:100%;max-height:100%;object-fit:contain;transition:opacity .25s ease;user-select:none;-webkit-user-drag:none}' +
      '.olo-lb-img.olo-lb-fade{opacity:0}' +
      /* video */
      '.olo-lb-video{max-width:100%;max-height:100%;object-fit:contain;outline:none}' +
      /* iframe */
      '.olo-lb-iframe-wrap{width:100%;max-width:900px;aspect-ratio:16/9;position:relative}' +
      '.olo-lb-iframe{position:absolute;inset:0;width:100%;height:100%;border:none}' +
      /* caption */
      '.olo-lb-caption{position:absolute;bottom:0;left:0;right:0;padding:10px 16px;color:#fff;font-size:14px;text-align:center;background:linear-gradient(transparent,rgba(0,0,0,.6));pointer-events:none;opacity:0;transition:opacity .3s}' +
      '.olo-lb-caption.olo-lb-cap-show{opacity:1}' +
      '.olo-lb-arrow{position:absolute;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);border:none;color:#fff;font-size:28px;width:44px;height:44px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;z-index:2;line-height:1}' +
      '.olo-lb-arrow:hover{background:rgba(255,255,255,.3)}' +
      '.olo-lb-prev{left:12px}' +
      '.olo-lb-next{right:12px}' +
      '.olo-lb-close{position:absolute;top:12px;right:12px;background:none;border:none;color:#fff;font-size:36px;cursor:pointer;z-index:2;line-height:1;width:44px;height:44px;display:flex;align-items:center;justify-content:center}' +
      '.olo-lb-fullscreen{position:absolute;top:12px;right:60px;background:none;border:none;color:#fff;font-size:22px;cursor:pointer;z-index:2;line-height:1;width:44px;height:44px;display:flex;align-items:center;justify-content:center;opacity:.7;transition:opacity .2s}' +
      '.olo-lb-fullscreen:hover{opacity:1}' +
      /* strip */
      '.olo-lb-strip{overflow:auto;display:flex;gap:6px;padding:6px;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.3) transparent;flex-shrink:0}' +
      '.olo-lb-strip::-webkit-scrollbar{width:4px;height:4px}' +
      '.olo-lb-strip::-webkit-scrollbar-thumb{background:rgba(255,255,255,.3);border-radius:4px}' +
      '.olo-lb-thumb{flex:0 0 auto;cursor:pointer;border:2px solid transparent;border-radius:4px;opacity:.5;transition:opacity .2s,border-color .2s,outline-color .35s ease,outline-offset .35s ease;overflow:hidden;object-fit:cover;outline:2px solid transparent;outline-offset:40px}' +
      '.olo-lb-thumb:hover{outline-color:rgba(255,255,255,.7);outline-offset:0}' +
      '.olo-lb-thumb.olo-lb-active{opacity:1;border-color:#fff}' +
      /* layout variants */
      '.olo-lb-wrap.olo-lb-bottom{flex-direction:column}' +
      '.olo-lb-wrap.olo-lb-bottom .olo-lb-strip{flex-direction:row;max-height:none;overflow-x:auto;overflow-y:hidden}' +
      '.olo-lb-wrap.olo-lb-right{flex-direction:row}' +
      '.olo-lb-wrap.olo-lb-right .olo-lb-strip{flex-direction:column;max-width:none;overflow-y:auto;overflow-x:hidden}' +
      '.olo-lb-wrap.olo-lb-left{flex-direction:row-reverse}' +
      '.olo-lb-wrap.olo-lb-left .olo-lb-strip{flex-direction:column;max-width:none;overflow-y:auto;overflow-x:hidden}' +
      /* 1 row — bottom: strip alta 15vh, thumb si adatta */
      '.olo-lb-wrap.olo-lb-rows1.olo-lb-bottom .olo-lb-strip{height:15vh;align-items:stretch}' +
      '.olo-lb-wrap.olo-lb-rows1.olo-lb-bottom .olo-lb-thumb{height:100%;width:auto;aspect-ratio:4/3}' +
      /* 1 col — right/left: strip larga 15vw, thumb si adatta */
      '.olo-lb-wrap.olo-lb-rows1.olo-lb-right .olo-lb-strip,.olo-lb-wrap.olo-lb-rows1.olo-lb-left .olo-lb-strip{width:15vw}' +
      '.olo-lb-wrap.olo-lb-rows1.olo-lb-right .olo-lb-thumb,.olo-lb-wrap.olo-lb-rows1.olo-lb-left .olo-lb-thumb{width:100%;height:auto;aspect-ratio:4/3}' +
      /* 2 rows — bottom: strip alta 15vh, 2 righe wrap */
      '.olo-lb-wrap.olo-lb-rows2.olo-lb-bottom .olo-lb-strip{flex-wrap:wrap;height:15vh;align-content:stretch}' +
      '.olo-lb-wrap.olo-lb-rows2.olo-lb-bottom .olo-lb-thumb{height:calc(50% - 3px);width:auto;aspect-ratio:4/3}' +
      /* 2 cols — right/left: strip larga 15vw, 2 colonne wrap */
      '.olo-lb-wrap.olo-lb-rows2.olo-lb-right .olo-lb-strip,.olo-lb-wrap.olo-lb-rows2.olo-lb-left .olo-lb-strip{flex-wrap:wrap;width:15vw;align-content:stretch}' +
      '.olo-lb-wrap.olo-lb-rows2.olo-lb-right .olo-lb-thumb,.olo-lb-wrap.olo-lb-rows2.olo-lb-left .olo-lb-thumb{width:calc(50% - 3px);height:auto;aspect-ratio:4/3}' +
      /* mobile: force bottom layout + responsive sizing */
      '@media(max-width:640px){' +
        '.olo-lb-wrap.olo-lb-right,.olo-lb-wrap.olo-lb-left{flex-direction:column}' +
        '.olo-lb-wrap.olo-lb-right .olo-lb-strip,.olo-lb-wrap.olo-lb-left .olo-lb-strip{flex-direction:row;overflow-x:auto;overflow-y:hidden;flex-wrap:nowrap;max-width:none;max-height:none}' +
        '.olo-lb-arrow{width:36px;height:36px;font-size:20px}' +
        '.olo-lb-prev{left:6px}' +
        '.olo-lb-next{right:6px}' +
        '.olo-lb-close{top:6px;right:6px;font-size:28px;width:36px;height:36px}' +
        '.olo-lb-fullscreen{top:6px;right:46px;font-size:18px;width:36px;height:36px}' +
        '.olo-lb-caption{font-size:12px;padding:6px 12px}' +
        '.olo-lb-wrap.olo-lb-rows1.olo-lb-bottom .olo-lb-strip{height:12vh}' +
      '}';
    var el = document.createElement('style');
    el.textContent = css;
    document.head.appendChild(el);
  }

  /* ── reset media ─────────────────────────────────────── */
  function resetMedia() {
    mainImg.style.display = 'none';
    mainImg.classList.remove('olo-lb-fade');
    mainVideo.style.display = 'none';
    mainVideo.pause();
    mainVideo.removeAttribute('src');
    mainVideo.innerHTML = '';
    iframeWrap.style.display = 'none';
    mainIframe.src = 'about:blank';
  }

  /* ── navigate ───────────────────────────────────────── */
  function go(idx) {
    idx = clamp(idx, 0, items.length - 1);
    current = idx;
    var item = items[idx];

    resetMedia();

    if (item.type === 'video') {
      if (isEmbedUrl(item.videoSrc)) {
        /* embed — show iframe */
        iframeWrap.style.display = '';
        mainIframe.src = item.videoSrc;
      } else {
        /* file video — show <video> */
        mainVideo.style.display = '';
        mainVideo.src = item.videoSrc;
        mainVideo.load();
      }
    } else {
      /* image */
      mainImg.style.display = '';
      mainImg.classList.add('olo-lb-fade');
      setTimeout(function () {
        mainImg.src = item.src;
        mainImg.alt = item.alt || '';
        mainImg.classList.remove('olo-lb-fade');
      }, 150);
    }

    /* caption */
    if (item.caption) {
      caption.textContent = item.caption;
      caption.classList.add('olo-lb-cap-show');
    } else {
      caption.classList.remove('olo-lb-cap-show');
    }

    /* active thumb */
    var thumbs = strip.querySelectorAll('.olo-lb-thumb');
    thumbs.forEach(function (t, i) {
      t.classList.toggle('olo-lb-active', i === idx);
    });

    /* auto-scroll thumb into view */
    if (thumbs[idx]) {
      thumbs[idx].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
    }
  }

  /* ── open / close ───────────────────────────────────── */
  function open(cfg, startIdx) {
    buildDOM();
    items   = cfg.items;
    current = startIdx || 0;

    var wrap = overlay.querySelector('.olo-lb-wrap');
    wrap.className = 'olo-lb-wrap olo-lb-' + cfg.position + ' olo-lb-rows' + cfg.rows;

    /* build thumbs */
    strip.innerHTML = '';
    items.forEach(function (item, i) {
      var img = document.createElement('img');
      img.className = 'olo-lb-thumb';
      /* for video items, use poster as thumbnail */
      img.src = item.thumb || item.poster || item.src;
      img.alt = item.alt || '';
      img.loading = 'lazy';
      img.addEventListener('click', function () { go(i); });
      strip.appendChild(img);
    });

    go(current);
    overlay.classList.add('olo-lb-open');
    document.body.style.overflow = 'hidden';
  }

  function close() {
    if (!overlay) return;
    /* stop any playing media */
    mainVideo.pause();
    mainIframe.src = 'about:blank';
    overlay.classList.remove('olo-lb-open');
    document.body.style.overflow = '';
  }

  /* ── init: intercept clicks on [data-olo-lb] containers ─ */
  function init() {
    injectStyle();
    buildDOM();

    document.addEventListener('click', function (e) {
      var link = e.target.closest('[data-olo-lb] a.olo-pg-item');
      if (!link) return;
      e.preventDefault();

      container = link.closest('[data-olo-lb]');
      if (!container) return;

      var cfg;
      try { cfg = JSON.parse(container.getAttribute('data-olo-lb')); }
      catch (ex) { return; }

      /* collect all items from DOM */
      var allLinks = container.querySelectorAll('a.olo-pg-item, a.olo-pg-hidden');
      var collected = [];
      var startIdx = 0;
      allLinks.forEach(function (a, i) {
        var src = a.getAttribute('href');
        if (!src) return;
        var dataType = a.getAttribute('data-type') || '';
        var videoSrc = a.getAttribute('data-video-src') || '';
        var posterUrl = a.getAttribute('data-poster') || '';

        if (dataType === 'video') {
          collected.push({
            src: posterUrl || src,
            videoSrc: videoSrc || src,
            thumb: a.getAttribute('data-thumb') || posterUrl || '',
            poster: posterUrl,
            alt: a.querySelector('img') ? a.querySelector('img').alt : '',
            caption: a.getAttribute('data-caption') || '',
            type: 'video'
          });
        } else {
          collected.push({
            src: src,
            thumb: a.getAttribute('data-thumb') || src,
            alt: a.querySelector('img') ? a.querySelector('img').alt : '',
            caption: a.getAttribute('data-caption') || '',
            type: 'image'
          });
        }
        if (a === link) startIdx = collected.length - 1;
      });

      open({
        items: collected,
        position: cfg.position || 'bottom',
        rows: cfg.rows || '1'
      }, startIdx);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
