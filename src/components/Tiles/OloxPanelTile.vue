<template>
  <div class="oloxp oloxp-home">
    <!-- fermata intro -->
    <section v-if="s.variant === 'intro'" class="panel intro" style="--c:var(--olo)">
      <div class="inner">
        <div>
          <div class="k">{{ s.intro_kicker }} <button class="olw" type="button">{{ s.olw_text }}</button></div>
          <h1 v-html="s.intro_title"></h1>
          <p class="sub" style="max-width:52ch;" v-html="s.intro_sub"></p>
          <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <a class="cta" @click.prevent>{{ s.intro_cta1 }}</a>
            <a class="cta ghost" @click.prevent>{{ s.intro_cta2 }}</a>
          </div>
        </div>
      </div>
      <div class="marq"><span class="in">{{ marqueeLine }}{{ marqueeLine }}</span></div>
    </section>

    <!-- fermata finale -->
    <section v-else-if="s.variant === 'outro'" class="panel outro" style="--c:var(--olo)">
      <div class="inner outro-grid">
        <div>
          <div class="k">{{ s.outro_kicker }}</div>
          <h2 style="font-size:clamp(36px,4.4vw,72px); max-width:14ch;" v-html="s.outro_title"></h2>
          <p class="sub" style="max-width:44ch;" v-html="s.outro_sub"></p>
          <div class="outro-links" style="justify-content:flex-start;">
            <a v-for="(l, i) in outroLinks" :key="i" :style="{ '--c': oloxColor(l.color) }" @click.prevent>{{ l.label }}</a>
          </div>
          <div class="fine" style="margin-top:40px;">{{ s.outro_fine }}</div>
        </div>
        <div class="madwrap">
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
      </div>
    </section>

    <!-- fermata prodotto -->
    <section v-else class="panel" :style="{ '--c': oloxColor(s.color) }">
      <span class="idx">·· / ··</span>
      <div class="inner">
        <div>
          <img v-if="s.logo" class="plogo" :src="s.logo" :alt="s.label" />
          <div class="k">{{ s.kicker }}</div>
          <h2 v-html="s.title_html"></h2>
          <p class="sub" v-html="s.sub_html"></p>
          <div class="tags">
            <span v-for="(tg, ti) in tags" :key="ti" :class="{ hot: ti === 0 }">{{ tg }}</span>
          </div>
          <a v-if="s.cta_text" class="cta" @click.prevent>{{ s.cta_text }}</a>
        </div>
        <div class="scene">
          <div v-if="s.scene === 'wall'" style="position:relative; width:min(100%,560px);">
            <div class="crane"></div>
            <div class="wall"><i v-for="n in 84" :key="n" :class="n % 29 === 0 ? 'p1' : (n % 17 === 0 ? 'p2' : '')"></i></div>
            <div class="wfoot">
              <div class="wstat"><b>Costruisci il sito perfetto</b> · posiziona le tile · 4 in fila vince</div>
              <button class="wreset" type="button">↺ nuova partita</button>
            </div>
          </div>
          <div v-else-if="s.scene === 'cal'" style="position:relative; width:min(100%,520px);">
            <div class="cal">
              <div class="head"><span>TURNO DI PROVA · <b>imprevisti in arrivo</b></span><span>02:00</span></div>
              <div class="arena"><div class="final"><span>turno di prova · 2 minuti</span><a class="cta" style="--c:var(--booking); margin-top:6px;">Cattura gli imprevisti</a></div></div>
              <div class="foot"><span>gestiti dal motore <b class="win">0</b></span><span>sfuggiti <b>0</b></span></div>
            </div>
            <div class="stub">Prenotato<b>Tavolo 12 · h 20:30</b></div>
          </div>
          <div v-else-if="s.scene === 'lang'" class="langbox">
            <div class="lcode">che lingua è? · punti <b>0</b> · <b>01:00</b></div>
            <div class="hello">«<span class="cur">Benvenuto</span>»</div>
            <div class="langpicks"><button type="button">Tedesco</button><button type="button">Italiano</button><button type="button">Francese</button></div>
          </div>
          <div v-else-if="s.scene === 'radar'" class="radarwrap">
            <div class="radar">
              <div class="shieldring"></div><div class="cross"></div><div class="sweep"></div>
              <div class="radhud">02:00 · intercettati <b>0</b> · violazioni <b>0</b></div>
            </div>
          </div>
          <div v-else-if="s.scene === 'pano'" style="position:relative;">
            <div class="porthole">
              <div class="vista sky"></div><div class="vista far"></div><div class="vista near"></div>
              <span class="spot" style="top:38%; left:30%;"></span>
              <span class="spot" style="top:58%; left:66%;"></span>
            </div>
            <div class="compass">N ─ E ─ S ─ O</div>
          </div>
          <div v-else-if="s.scene === 'course'" class="course" style="position:relative;">
            <div class="badge">livello<b>01</b>studente</div>
            <div class="xphead"><span>corso · conosci OLOtheme</span><b><span>0</span> XP</b></div>
            <div class="xpbar"><i style="width:40%"></i></div>
            <div class="tquiz">
              <div class="tq-q">Per tradurre il sito in 28 lingue uso <span class="tq-slot">trascina qui</span></div>
              <div class="tq-chips"><span class="tq-chip">OLOlang</span><span class="tq-chip">OLObuild</span><span class="tq-chip">OLOtour</span></div>
              <div class="tq-stat">trascina la risposta giusta nello spazio · +60 xp</div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import def from '@/config/elements/oloxpanel.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const tags = computed(() => String(s.value.tags || '').split('|').map(x => x.trim()).filter(Boolean));
const outroLinks = computed(() => (Array.isArray(s.value.outro_links) ? s.value.outro_links : []));
const madPicks = computed(() => (Array.isArray(s.value.mad_picks) ? s.value.mad_picks : []));
const marqueeLine = computed(() => {
  const items = Array.isArray(s.value.marquee_items) ? s.value.marquee_items : [];
  return items.map(i => i.text).filter(Boolean).join(' ● ') + ' ● ';
});
</script>
