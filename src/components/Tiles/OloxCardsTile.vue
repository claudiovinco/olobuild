<template>
  <div class="oloxp" :style="{ '--c': accent }">
    <section class="dsec" :style="sectionStyle">
      <div class="wrap">
        <div v-if="s.kicker || s.title_html || s.lead" class="head"
          :style="s.head_center ? 'margin-left:auto; margin-right:auto; text-align:center; max-width:640px;' : ''">
          <div v-if="s.kicker" class="k" :style="s.head_center ? 'justify-content:center;' : ''">{{ s.kicker }}</div>
          <h2 v-if="s.title_html" class="d" v-html="s.title_html"></h2>
          <p v-if="s.lead" v-html="s.lead"></p>
        </div>

        <div v-if="s.variant === 'brick'" class="bricks">
          <div v-for="(it, i) in items" :key="i" class="brickcard">
            <span class="n">{{ it.label }}</span><h3>{{ it.title }}</h3><p v-html="it.text_html"></p>
          </div>
        </div>
        <div v-else-if="s.variant === 'ticket'" class="tickets">
          <div v-for="(it, i) in items" :key="i" class="ticket">
            <span class="tk">{{ it.label }}</span><h3>{{ it.title }}</h3><p v-html="it.text_html"></p>
            <div class="rip"></div><div class="code"><span>{{ it.extra }}</span><i></i></div>
          </div>
        </div>
        <div v-else-if="s.variant === 'red'" class="redgrid">
          <div v-for="(it, i) in items" :key="i" class="redcard in">
            <span class="kk">{{ it.label }}</span><h3 v-html="it.title"></h3><p v-html="it.text_html"></p>
          </div>
        </div>
        <div v-else-if="s.variant === 'room'" class="rooms">
          <template v-for="(it, i) in items" :key="i">
            <div class="room" :style="it.extra ? 'border-color:color-mix(in srgb, var(--c) 60%, transparent);' : ''">
              <span class="rn">{{ it.label }}</span><h3 v-html="it.title"></h3><p v-html="it.text_html"></p>
            </div>
            <div v-if="i < items.length - 1" class="corridor"></div>
          </template>
        </div>
        <div v-else-if="s.variant === 'hs'" class="hs-grid">
          <div v-for="(it, i) in items" :key="i" class="hs">
            <span class="dot"></span><h3 v-html="it.title"></h3><p v-html="it.text_html"></p>
          </div>
        </div>
        <div v-else class="dgrid3">
          <div v-for="(it, i) in items" :key="i" class="dcard">
            <span v-if="it.label" class="kk">{{ it.label }}</span><h3 v-html="it.title"></h3><p v-html="it.text_html"></p>
          </div>
        </div>

        <p v-if="s.foot_html" class="sub" style="margin-top:44px;" v-html="s.foot_html"></p>
        <a v-if="s.foot_cta" class="cta" @click.prevent>{{ s.foot_cta }}</a>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import def from '@/config/elements/oloxcards.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const accent = computed(() => oloxColor(s.value.accent));
const items = computed(() => (Array.isArray(s.value.items) ? s.value.items : []));
const sectionStyle = computed(() => (s.value.section_bg ? { background: s.value.section_bg } : {}));
</script>
