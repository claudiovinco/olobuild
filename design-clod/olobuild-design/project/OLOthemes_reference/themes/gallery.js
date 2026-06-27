/* OLOthemes gallery — Topic/Type/Zone filtering, live count + interactive-zone badges. */
(function(){
  function ready(fn){ if(document.readyState!=='loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }

  /* Which themes carry a live interactive zone (one of the 4 OLObuild zone tiles).
     Keyed by the theme name as it appears in the file href (lower-cased).
     Order = how labels read on the badge; multi-zone themes list both. */
  var ZONES = {
    // Finder
    'maison':['Finder'], 'terra':['Finder'], 'vela':['Finder'], 'pasaje':['Finder'],
    'linea':['Finder'], 'relay os':['Finder','Time zones'], 'vinea':['Finder'], 'atelier noir':['Finder'],
    'saffron':['Finder','Floor plan'], 'pulse':['Finder'], 'cadence':['Finder','Availability'], 'meridian':['Finder'],
    'vitalis':['Finder'], 'lumen':['Finder'], 'hearth':['Finder'], 'contour':['Finder'],
    // Builder
    'verde':['Builder'], 'field & co':['Builder'], 'brewline':['Builder'], 'honeycomb':['Builder',"Baker's %"],
    'mercato':['Builder','360°'], 'circuit':['Builder'], 'verdano':['Builder'], 'tavola':['Builder','Recipe'],
    // Mixer
    'loft':['Mixer'], 'canvas':['Mixer'], 'vélour':['Mixer'], 'kiln':['Mixer'], 'prisma':['Mixer','Palette'],
    // Projector
    'ledger':['Projector'], 'capital row':['Projector'], 'sterling':['Projector'],
    'nimbus':['Projector'], 'synapse':['Projector'],
    // Two zones on one page
    'carrello':['Builder','Finder'], 'fjordline':['Finder','Projector'],
    // Novel one-offs (rare interactive tiles)
    'mono':['Type tester'], 'voyage':['Route'],
    'soundwave':['Sequencer'], 'forge':['Contrast'], 'bloom':['Routine']
  };

  ready(function(){
    var cards = [].slice.call(document.querySelectorAll('.card'));
    var state = { topic:'all', type:'all', zone:'all' };
    var countEl = document.querySelector('[data-count-vis]');

    /* tag matching cards + drop a badge on the preview */
    cards.forEach(function(c){
      var a = c.querySelector('.tprev');
      if(!a) return;
      var href = a.getAttribute('href') || '';
      var m = href.match(/OLOtheme - (.+?) \(/);
      var name = m ? m[1].toLowerCase() : '';
      var z = ZONES[name];
      if(!z) return;
      c.setAttribute('data-zones', z.map(function(s){ return s.toLowerCase(); }).join(' '));
      var tag = document.createElement('span');
      tag.className = 'zone-tag';
      tag.innerHTML = '<span class="zi"></span>' + z.join(' · ');
      tag.title = 'Live interactive zone: ' + z.join(' + ');
      a.appendChild(tag);
    });

    function apply(){
      var vis = 0;
      cards.forEach(function(c){
        var okT = state.topic==='all' || c.getAttribute('data-topic')===state.topic;
        var okY = state.type==='all'  || c.getAttribute('data-type')===state.type;
        var zl = c.getAttribute('data-zones') || '';
        var okZ = state.zone==='all'
          || (state.zone==='interactive' ? !!zl : (' '+zl+' ').indexOf(' '+state.zone+' ')>=0);
        var show = okT && okY && okZ;
        c.classList.toggle('hide', !show);
        if(show){ vis++; c.style.animationDelay = (vis*22)+'ms'; }
      });
      if(countEl) countEl.textContent = vis;
      var empty = document.querySelector('.empty');
      if(empty) empty.classList.toggle('show', vis===0);
    }

    document.querySelectorAll('.fchip').forEach(function(chip){
      chip.addEventListener('click', function(){
        var row = chip.hasAttribute('data-type') ? 'type'
                : chip.hasAttribute('data-zone') ? 'zone'
                : 'topic';
        var key = row==='type' ? 'data-type' : row==='zone' ? 'data-zone' : 'data-topic';
        var val = chip.getAttribute(key);
        chip.parentNode.querySelectorAll('.fchip').forEach(function(c){ c.classList.remove('on'); });
        chip.classList.add('on');
        state[row] = val;
        apply();
      });
    });
    apply();
  });
})();
