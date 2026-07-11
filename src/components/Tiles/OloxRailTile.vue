<template>
  <div class="oloxp oloxp-home">
    <!-- anteprima canvas: chrome del binario; le fermate sono le sezioni successive -->
    <div class="chrome" style="position:relative; background:rgba(12,14,19,.88); border-bottom:1px solid var(--panelline);">
      <a class="logo" @click.prevent><img v-if="s.logo" :src="s.logo" alt="OLOtheme" /></a>
      <div class="langsw"><div class="lsw-list">
        <a v-for="(l, i) in langs" :key="i" :class="{ on: l.active }" @click.prevent>{{ l.code }}</a>
      </div></div>
      <div class="jump">
        <button v-for="n in 8" :key="n" :style="{ '--jc': 'var(--olo)' }" type="button"><span></span></button>
      </div>
    </div>
    <div style="padding:14px 26px; font-family:'JetBrains Mono',monospace; font-size:10px; letter-spacing:.18em;
      text-transform:uppercase; color:var(--faint); border-bottom:1px dashed var(--line); display:flex; gap:14px; align-items:center;">
      <span style="color:var(--olo);">◈ binario experience</span>
      <span>sul sito le sezioni-fermata sotto scorrono in orizzontale · qui nel builder restano separate per l'editing</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import def from '@/config/elements/oloxrail.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const langs = computed(() => (Array.isArray(s.value.langs) && s.value.langs.length ? s.value.langs : def.defaults.langs));
</script>
