<template>
  <div class="oloxp" :style="{ '--c': accent }">
    <div v-if="s.variant === 'day'" class="ox-dayrail">
      <div class="ox-dayview">
        <div class="wrap day-grid">
          <div>
            <div class="k">{{ s.kicker }}</div>
            <div class="daytime">14:<em>30</em></div>
            <div class="dayocc">{{ s.day_label }} <b class="ox-dayocc">50%</b> · {{ s.day_hint }}</div>
          </div>
          <div class="slots">
            <div v-for="(sl, i) in daySlots" :key="i" class="slot" :class="{ b: i < Math.ceil(daySlots.length / 2) }">
              <span class="hh">{{ sl.hh }}</span><span class="what">{{ sl.what }}</span>
              <span class="who">{{ sl.who }}</span><span class="stamp">{{ s.day_stamp }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="ox-asmrail">
      <div class="ox-asmview">
        <div class="wrap asm-grid">
          <div class="asm-copy">
            <div class="k">{{ s.kicker }}</div>
            <div class="step">fase <b class="ox-stepno">{{ asmBlocks.length }}</b> / {{ asmBlocks.length }}</div>
            <div class="stepname" v-html="lastStep"></div>
            <div class="asm-hint">{{ s.asm_hint }}</div>
          </div>
          <div class="browser">
            <div class="bar"><i></i><i></i><i></i><span class="url">{{ s.browser_url }}</span></div>
            <div class="stage">
              <div v-for="(b, i) in asmBlocks" :key="i" class="blk set" :data-b="i"><b>tile</b> {{ b.text }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import def from '@/config/elements/oloxsticky.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const accent = computed(() => oloxColor(s.value.accent));
const asmBlocks = computed(() => (Array.isArray(s.value.asm_blocks) ? s.value.asm_blocks : []));
const daySlots = computed(() => (Array.isArray(s.value.day_slots) ? s.value.day_slots : []));
const lastStep = computed(() => {
  const steps = Array.isArray(s.value.asm_steps) ? s.value.asm_steps : [];
  return steps.length ? steps[steps.length - 1].text : '';
});
</script>
