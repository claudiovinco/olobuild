/* OLOthemes — shared front-end behaviour for all theme demos.
   Reveal-on-scroll (rect-based, IO-free) · counters · marquee clone ·
   mobile menu · light parallax · word rotator. */
(function(){
  function ready(fn){ if(document.readyState!=='loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }

  ready(function(){
    document.documentElement.classList.add('reveal-on');
    var reveals = [].slice.call(document.querySelectorAll('[data-reveal]'));
    var counters = [].slice.call(document.querySelectorAll('[data-count]'));

    function inView(el, margin){
      var r = el.getBoundingClientRect();
      var vh = window.innerHeight || document.documentElement.clientHeight;
      return r.top < vh*(1-(margin||0.08)) && r.bottom > 0;
    }

    function fireReveal(el){
      if(el.__shown) return; el.__shown = true;
      var d = parseInt(el.getAttribute('data-reveal'),10) || 0;
      el.style.transitionDelay = d+'ms';
      el.classList.add('in');
    }
    function runCount(el){
      if(el.__counted) return; el.__counted = true;
      var target = parseFloat(el.getAttribute('data-count'));
      var dec = parseInt(el.getAttribute('data-dec')||'0',10);
      var t0=null, dur=1500;
      function step(ts){
        if(!t0) t0=ts;
        var p=Math.min((ts-t0)/dur,1), e=1-Math.pow(1-p,3), v=target*e;
        el.textContent = dec ? v.toFixed(dec) : Math.round(v).toLocaleString('en-US');
        if(p<1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    }

    function check(){
      reveals.forEach(function(el){ if(!el.__shown && inView(el)) fireReveal(el); });
      counters.forEach(function(el){ if(!el.__counted && inView(el)) runCount(el); });
    }

    check();
    window.addEventListener('scroll', check, {passive:true});
    window.addEventListener('resize', check);
    setTimeout(check, 250);
    /* failsafe: never leave content hidden if scroll events don't fire */
    setTimeout(function(){ reveals.forEach(fireReveal); counters.forEach(runCount); }, 1600);

    /* marquee seamless loop */
    document.querySelectorAll('[data-marquee]').forEach(function(t){ t.innerHTML += t.innerHTML; });

    /* mobile menu */
    var burger = document.querySelector('[data-burger]');
    var mob = document.querySelector('[data-mobile]');
    if(burger && mob){
      burger.addEventListener('click', function(){ mob.classList.add('open'); });
      mob.addEventListener('click', function(ev){
        if(ev.target.closest('a') || ev.target.hasAttribute('data-close')) mob.classList.remove('open');
      });
    }

    /* light parallax */
    var px = [].slice.call(document.querySelectorAll('[data-parallax]'));
    if(px.length){
      window.addEventListener('scroll', function(){
        var y = window.scrollY;
        px.forEach(function(el){
          var s = parseFloat(el.getAttribute('data-parallax'))||0;
          el.style.transform = 'translate3d(0,'+(y*s)+'px,0)';
        });
      }, {passive:true});
    }

    /* word rotator */
    document.querySelectorAll('[data-words]').forEach(function(el){
      var words = el.getAttribute('data-words').split(','); var i=0;
      setInterval(function(){
        i=(i+1)%words.length; el.style.opacity=0;
        setTimeout(function(){ el.textContent=words[i]; el.style.opacity=1; }, 220);
      }, 2200);
    });
  });
})();
