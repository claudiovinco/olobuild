<template>
  <div class="mb-py-4 mb-px-4" :style="{ textAlign: s.alignment, display: 'flex', justifyContent: justifyMap[s.alignment] || 'center' }">
    <div :style="containerStyle">
      <div v-if="s.json_url" ref="lottieContainer" :style="{ width: '100%', height: '100%' }">
        <!-- Lottie renders here -->
        <div v-if="loadingState === 'loading'" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.05);border-radius:8px;">
          <span style="font-size:24px;animation:spin 1s linear infinite;">&#9696;</span>
        </div>
        <div v-if="loadingState === 'error'" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;background:linear-gradient(135deg,#1e293b,#0f172a);border-radius:8px;">
          <span style="font-size:28px;margin-bottom:6px;">&#9888;</span>
          <span style="font-size:11px;color:#f87171;">Errore caricamento Lottie</span>
        </div>
      </div>
      <div v-else :style="placeholderStyle">
        <div style="font-size:40px;margin-bottom:8px;">&#127916;</div>
        <div style="font-size:11px;color:#9ca3af;">Inserisci URL file .json Lottie</div>
        <div style="font-size:9px;color:#6b7280;margin-top:4px;">Supporta LottieFiles.com</div>
      </div>
      <!-- Trigger badge -->
      <div v-if="s.trigger !== 'autoplay'" style="position:absolute;bottom:4px;right:4px;background:rgba(0,0,0,0.6);color:#e5e7eb;font-size:9px;padding:1px 6px;border-radius:3px;">
        {{ triggerLabel }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  json_url: '',
  width: '300',
  height: '300',
  loop: true,
  autoplay: true,
  speed: '1',
  trigger: 'autoplay',
  hover_action: 'none',
  alignment: 'center',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const lottieContainer = ref(null);
let anim = null;
const loadingState = ref('idle');

const justifyMap = { left: 'flex-start', center: 'center', right: 'flex-end' };

const containerStyle = computed(() => ({
  width: (parseInt(s.value.width) || 300) + 'px',
  height: (parseInt(s.value.height) || 300) + 'px',
  position: 'relative',
  overflow: 'hidden',
}));

const placeholderStyle = computed(() => ({
  width: '100%',
  height: '100%',
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  justifyContent: 'center',
  background: 'linear-gradient(135deg, #1e293b, #0f172a)',
  borderRadius: '8px',
  border: '2px dashed #374151',
}));

const triggerLabel = computed(() => {
  const m = { viewport: 'viewport', hover: 'hover', click: 'click' };
  return m[s.value.trigger] || '';
});

async function loadLottie() {
  if (!s.value.json_url || !lottieContainer.value) return;
  if (anim) { anim.destroy(); anim = null; }
  loadingState.value = 'loading';

  try {
    if (!window.lottie) {
      const script = document.createElement('script');
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js';
      document.head.appendChild(script);
      await new Promise((resolve, reject) => { script.onload = resolve; script.onerror = reject; });
    }

    anim = window.lottie.loadAnimation({
      container: lottieContainer.value,
      renderer: 'svg',
      loop: s.value.loop !== false,
      autoplay: true,
      path: s.value.json_url,
    });
    anim.addEventListener('DOMLoaded', () => { loadingState.value = 'idle'; });
    anim.addEventListener('data_failed', () => { loadingState.value = 'error'; });
    const spd = parseFloat(s.value.speed) || 1;
    if (spd !== 1) anim.setSpeed(spd);
  } catch (e) {
    loadingState.value = 'error';
  }
}

watch(() => [s.value.json_url, s.value.loop, s.value.speed], () => loadLottie());
onMounted(() => loadLottie());
onUnmounted(() => { if (anim) anim.destroy(); });
</script>
