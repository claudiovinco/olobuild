<template>
  <!-- anteprima canvas condivisa delle scene-minigioco (usata da OloxSceneTile e OloxPanelTile) -->
  <div v-if="scene === 'madlib'" class="madwrap">
    <div class="madcard">
      <div class="madhead"><span>{{ s.mad_doc }}</span><span class="blinkdot"></span><span>{{ s.mad_line }}</span></div>
      <div class="madlib">
        {{ s.mad_intro }}
        <input type="text" :placeholder="s.mad_nome_ph" size="14" readonly />
        {{ s.mad_mid }}
        <span class="pick">
          <button v-for="(pk, i) in madPicks" :key="i" type="button" :style="{ '--c': oloxColor(pk.color) }">{{ pk.label }}</button>
        </span>.
        {{ s.mad_pre_mail }}
        <input type="email" :placeholder="s.mad_mail_ph" size="16" readonly />
        {{ s.mad_end }}
      </div>
      <div class="madfoot">
        <button class="cta" type="button">{{ s.mad_btn }}</button>
        <span class="madnote">{{ s.mad_note }}</span>
      </div>
    </div>
  </div>
  <div v-else class="scene go">
    <div v-if="scene === 'wall'" style="position:relative; width:min(100%,560px);">
      <div class="crane"></div>
      <div class="wall"><i v-for="n in 84" :key="n" :class="n % 29 === 0 ? 'p1' : (n % 17 === 0 ? 'p2' : '')"></i></div>
      <div class="wfoot">
        <div class="wstat"><b>Costruisci il sito perfetto</b> · posiziona le tile · 4 in fila vince</div>
        <button class="wreset" type="button">↺ nuova partita</button>
      </div>
    </div>
    <div v-else-if="scene === 'cal'" style="position:relative; width:min(100%,520px);">
      <div class="cal">
        <div class="head"><span>TURNO DI PROVA · <b>imprevisti in arrivo</b></span><span>02:00</span></div>
        <div class="arena"><div class="final"><span>turno di prova · 2 minuti</span><a class="cta" style="--c:var(--booking); margin-top:6px;">Cattura gli imprevisti</a></div></div>
        <div class="foot"><span>gestiti dal motore <b class="win">0</b></span><span>sfuggiti <b>0</b></span></div>
      </div>
      <div class="stub">Prenotato<b>Tavolo 12 · h 20:30</b></div>
    </div>
    <div v-else-if="scene === 'lang'" class="langbox">
      <div class="lcode">che lingua è? · punti <b>0</b> · <b>01:00</b></div>
      <div class="hello">«<span class="cur">Benvenuto</span>»</div>
      <div class="langpicks"><button type="button">Tedesco</button><button type="button">Italiano</button><button type="button">Francese</button></div>
    </div>
    <div v-else-if="scene === 'radar'" class="radarwrap">
      <div class="radar">
        <div class="shieldring"></div><div class="cross"></div><div class="sweep"></div>
        <div class="radhud">02:00 · intercettati <b>0</b> · violazioni <b>0</b></div>
      </div>
    </div>
    <div v-else-if="scene === 'pano'" style="position:relative;">
      <div class="porthole">
        <div class="vista sky"></div><div class="vista far"></div><div class="vista near"></div>
        <span class="spot" style="top:38%; left:30%;"></span>
        <span class="spot" style="top:58%; left:66%;"></span>
      </div>
      <div class="compass">N ─ E ─ S ─ O</div>
    </div>
    <div v-else-if="scene === 'course'" class="course" style="position:relative;">
      <div class="badge">livello<b>01</b>studente</div>
      <div class="xphead"><span>corso · conosci OLOtheme</span><b><span>0</span> XP</b></div>
      <div class="xpbar"><i style="width:40%"></i></div>
      <div class="tquiz">
        <div class="tq-q">Per tradurre il sito in 28 lingue uso <span class="tq-slot">trascina qui</span></div>
        <div class="tq-chips"><span class="tq-chip">OLOlang</span><span class="tq-chip">OLObuild</span><span class="tq-chip">OLOtour</span></div>
        <div class="tq-stat">trascina la risposta giusta nello spazio · +60 xp</div>
      </div>
    </div>
    <!-- Scene showcase hero-* (gemelle statiche del PHP: fonte unica oloxhero) -->
    <div v-else-if="scene === 'hero-wall'" data-olox="hwall" style="width:min(100%,520px);">
      <div class="ox-hwall" data-cells="84">
        <div class="count"><b>{{ s.wall_count || 187 }}</b>{{ s.wall_label }}</div>
      </div>
    </div>
    <div v-else-if="scene === 'hero-clock'" class="clockface">
      <div class="hand h2"></div>
      <div class="hand hm"></div>
      <div class="pin"></div>
      <div class="clocklbl"><b>08:00</b>{{ s.clock_label }}</div>
    </div>
    <div v-else-if="scene === 'hero-console'" class="console">
      <div class="cbar"><b>{{ s.console_title }}</b><span>{{ s.console_sub }}</span></div>
      <div class="rows">
        <div v-for="(r, i) in consoleRows" :key="i" class="crow">
          <span class="lc">{{ r.lc }}</span>
          <span class="bar"><i :style="{ '--w': (r.w || 0) + '%', width: (r.w || 0) + '%' }"></i></span>
          <span class="pc">{{ r.pc || (r.w + '%') }}</span>
        </div>
      </div>
    </div>
    <div v-else-if="scene === 'hero-term'" class="term">
      <div class="tbar"><b>{{ s.term_title }}</b><span>{{ s.term_sub }}</span></div>
      <pre>{{ termPreview }}</pre>
    </div>
    <div v-else-if="scene === 'hero-porthole'" class="ox-porthole">
      <div class="pano"></div>
      <span class="spot" style="top:38%; left:30%;"></span>
      <span class="spot" style="top:58%; left:66%;"></span>
    </div>
    <div v-else-if="scene === 'hero-medal'" class="medal">
      <div class="orbit"><i></i></div>
      <div class="inner">{{ s.medal_top }}<b>{{ s.medal_big }}</b>{{ s.medal_bot }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';

const props = defineProps({
  scene: { type: String, default: 'wall' },
  s: { type: Object, default: () => ({}) },
});
const madPicks = computed(() => (Array.isArray(props.s.mad_picks) ? props.s.mad_picks : []));
const consoleRows = computed(() => (Array.isArray(props.s.console_rows) ? props.s.console_rows : []));
const termPreview = computed(() =>
  (Array.isArray(props.s.term_lines) ? props.s.term_lines : []).map((l) => l.text).join('\n'));
</script>
