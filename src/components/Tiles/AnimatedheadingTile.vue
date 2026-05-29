<template>
  <div class="mb-py-6 mb-px-4" :style="{ textAlign: s.alignment }">
    <component :is="s.tag || 'h2'" :style="headingStyle" class="mb-m-0 mb-leading-tight">
      <span v-if="s.before_text" data-olo-editable="before_text">{{ s.before_text }} </span>
      <span :style="animatedStyle" :class="animClass" ref="wordEl">{{ currentWord }}</span>
      <span class="olo-ah-cursor" v-if="s.animation === 'typing'">|</span>
      <span v-if="s.after_text" data-olo-editable="after_text"> {{ s.after_text }}</span>
    </component>
    <div style="margin-top:8px;display:flex;justify-content:center;gap:6px;flex-wrap:wrap;">
      <span v-for="(word, i) in words" :key="i" style="font-size:10px;padding:1px 6px;border-radius:3px;background:color-mix(in srgb, var(--olo-color-primary, #e1474f) 15%, transparent);color:var(--olo-color-primary, #e1474f);">{{ word }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount, watch } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  before_text: 'Noi siamo',
  animated_words: 'creativi\ninnovativi\nappassionati',
  after_text: '',
  animation: 'typing',
  tag: 'h2',
  alignment: 'center',
  text_color: 'var(--olo-color-text, #374151)',
  animated_color: 'var(--olo-color-primary, #e1474f)',
  font_size: '36',
  font_weight: '700',
  typing_speed: '100',
  pause_time: '2000',
  highlight_style: 'underline',
  highlight_color: 'var(--olo-color-primary, #e1474f)',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const words = computed(() => (s.value.animated_words || '').split('\n').map(w => w.trim()).filter(Boolean));
const currentWord = ref('');
const wordIdx = ref(0);
const wordEl = ref(null);

let timer = null;
let typingTimer = null;

function startAnimation() {
  stopAnimation();
  if (!words.value.length) return;

  currentWord.value = words.value[0];
  wordIdx.value = 0;

  const anim = s.value.animation;
  const pause = parseInt(s.value.pause_time) || 2000;
  const speed = parseInt(s.value.typing_speed) || 100;

  if (anim === 'typing') {
    let charIdx = words.value[0].length;
    let isDeleting = false;
    let curText = words.value[0];

    function typeStep() {
      if (isDeleting) {
        charIdx--;
        currentWord.value = curText.substring(0, charIdx);
        if (charIdx === 0) {
          isDeleting = false;
          wordIdx.value = (wordIdx.value + 1) % words.value.length;
          curText = words.value[wordIdx.value];
          typingTimer = setTimeout(typeStep, speed);
          return;
        }
        typingTimer = setTimeout(typeStep, speed / 2);
      } else {
        charIdx++;
        currentWord.value = curText.substring(0, charIdx);
        if (charIdx === curText.length) {
          isDeleting = true;
          typingTimer = setTimeout(typeStep, pause);
          return;
        }
        typingTimer = setTimeout(typeStep, speed);
      }
    }
    typingTimer = setTimeout(() => { isDeleting = true; typeStep(); }, pause);
  } else {
    timer = setInterval(() => {
      wordIdx.value = (wordIdx.value + 1) % words.value.length;
      currentWord.value = words.value[wordIdx.value];
    }, pause);
  }
}

function stopAnimation() {
  if (timer) { clearInterval(timer); timer = null; }
  if (typingTimer) { clearTimeout(typingTimer); typingTimer = null; }
}

onMounted(startAnimation);
onBeforeUnmount(stopAnimation);

watch(() => [s.value.animation, s.value.animated_words, s.value.typing_speed, s.value.pause_time], () => {
  startAnimation();
});

const headingStyle = computed(() => ({
  color: s.value.text_color || 'var(--olo-color-text, #374151)',
  fontSize: (parseInt(s.value.font_size) || 36) + 'px',
  fontWeight: s.value.font_weight || '700',
}));

const animatedStyle = computed(() => {
  const st = { color: s.value.animated_color || 'var(--olo-color-primary, #e1474f)' };
  const anim = s.value.animation;
  if (anim === 'highlight') {
    const hclr = s.value.highlight_color || 'var(--olo-color-primary, #e1474f)';
    const hstyle = s.value.highlight_style || 'underline';
    if (hstyle === 'underline') st.borderBottom = '3px solid ' + hclr;
    else if (hstyle === 'background') { st.background = hclr + '30'; st.padding = '0 6px'; st.borderRadius = '4px'; }
    else if (hstyle === 'strikethrough') st.textDecoration = 'line-through ' + hclr;
    st.transition = 'opacity 0.4s ease';
  } else if (anim === 'fade') {
    st.transition = 'opacity 0.5s ease';
  } else if (anim === 'rotating') {
    st.display = 'inline-block';
    st.overflow = 'hidden';
    st.verticalAlign = 'bottom';
    st.transition = 'transform 0.5s cubic-bezier(.4,0,.2,1)';
  } else if (anim === 'slide') {
    st.display = 'inline-block';
    st.transition = 'transform 0.5s ease, opacity 0.5s ease';
  } else if (anim === 'clip') {
    st.display = 'inline-block';
    st.overflow = 'hidden';
    st.borderRight = '2px solid currentColor';
    st.whiteSpace = 'nowrap';
    st.transition = 'width 0.8s cubic-bezier(.4,0,.2,1)';
  }
  return st;
});

const animClass = computed(() => 'olo-ah-' + (s.value.animation || 'typing'));
</script>

<style scoped>
.olo-ah-cursor { animation: olo-blink 0.7s infinite; color: currentColor; }
@keyframes olo-blink { 0%, 50% { opacity: 1; } 51%, 100% { opacity: 0; } }
</style>
