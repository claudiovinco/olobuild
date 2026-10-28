<template>
  <Teleport to="body">
    <div class="orz orz-modale" role="dialog" aria-modal="true" :aria-label="t('Anteprima')">
      <header class="orz-modale-testa">
        <h2 class="orz-modale-titolo"><Icona nome="occhio" :dim="18" /> {{ t('Anteprima') }}</h2>
        <div class="orz-segmenti" role="group" :aria-label="t('Larghezza')">
          <button
            v-for="d in DISPOSITIVI"
            :key="d.k"
            type="button"
            class="orz-segmento"
            :class="{ on: dispositivo === d.k }"
            :aria-pressed="dispositivo === d.k"
            @click="scegli(d.k)"
          ><Icona :nome="d.k" :dim="16" /> <span>{{ d.nome() }}</span></button>
        </div>
        <span v-if="nota" class="orz-nota">{{ nota }}</span>
        <span class="orz-spazio"></span>
        <button type="button" class="orz-btn orz-btn--quieto" :disabled="aggiornando" @click="emit('aggiorna')">
          <Icona nome="aggiorna" /> {{ aggiornando ? t('Preparo l\'anteprima…') : t('Ricarica') }}
        </button>
        <a class="orz-btn orz-btn--quieto" :href="url" target="_blank" rel="noopener"><Icona nome="esterno" /> {{ t('Apri in una nuova scheda') }}</a>
        <button ref="chiudiBtn" type="button" class="orz-btn-icona" :aria-label="t('Chiudi')" :title="t('Chiudi')" @click="emit('chiudi')">
          <Icona nome="x" :dim="18" />
        </button>
      </header>
      <div ref="palco" class="orz-palco">
        <div class="orz-cornice" :class="'orz-cornice--' + dispositivo" :style="cornice">
          <iframe :key="url" :src="url" :title="t('Anteprima della pagina')" :style="riquadro" @load="caricata = true"></iframe>
          <p v-if="!caricata" class="orz-palco-attesa">{{ t('Caricamento dell\'anteprima…') }}</p>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script>
// La larghezza scelta resta per le anteprime successive, finché la pagina è aperta.
let ultimo = 'computer';
</script>

<script setup>
/**
 * L'anteprima della Redazione: la pagina vera del sito (template Singolo compreso) con le modifiche non ancora salvate,
 * come la vede un visitatore al telefono, sul tablet o al computer. La pagina più larga del riquadro si rimpicciolisce
 * per starci intera: la larghezza resta quella vera, così il sito sceglie la sua disposizione per quel dispositivo.
 */
import { ref, reactive, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { t } from '@/i18n';
import Icona from './Icona.vue';

const props = defineProps({
  url: { type: String, required: true },
  nota: { type: String, default: '' },
  aggiornando: { type: Boolean, default: false },
});
const emit = defineEmits(['chiudi', 'aggiorna']);

const DISPOSITIVI = [
  { k: 'telefono', w: 390, nome: () => t('Telefono') },
  { k: 'tablet', w: 820, nome: () => t('Tablet') },
  { k: 'computer', w: 1280, nome: () => t('Computer') },
];

const dispositivo = ref(ultimo);
const caricata = ref(false);
const palco = ref(null);
const chiudiBtn = ref(null);
const misura = reactive({ w: 0, h: 0 });

function scegli(k) {
  dispositivo.value = k;
  ultimo = k;
}

const larghezza = computed(() => DISPOSITIVI.find((d) => d.k === dispositivo.value).w);
const scala = computed(() => (misura.w ? Math.min(1, Math.max(160, misura.w - 32) / larghezza.value) : 1));
const altezza = computed(() => Math.max(240, misura.h - 32));
const cornice = computed(() => ({ width: Math.round(larghezza.value * scala.value) + 'px', height: altezza.value + 'px' }));
const riquadro = computed(() => ({
  width: larghezza.value + 'px',
  height: Math.round(altezza.value / scala.value) + 'px',
  transform: scala.value < 1 ? `scale(${scala.value})` : 'none',
}));

watch(() => props.url, () => {
  caricata.value = false;
});

let osserva = null;
function prendiMisure() {
  if (!palco.value) return;
  misura.w = palco.value.clientWidth;
  misura.h = palco.value.clientHeight;
}
function tasti(e) {
  if (e.key === 'Escape') emit('chiudi');
}

onMounted(() => {
  prendiMisure();
  if (window.ResizeObserver) {
    osserva = new ResizeObserver(prendiMisure);
    osserva.observe(palco.value);
  } else {
    window.addEventListener('resize', prendiMisure);
  }
  window.addEventListener('keydown', tasti);
  document.documentElement.classList.add('orz-modale-aperta');
  nextTick(() => chiudiBtn.value && chiudiBtn.value.focus());
});
onBeforeUnmount(() => {
  if (osserva) osserva.disconnect();
  window.removeEventListener('resize', prendiMisure);
  window.removeEventListener('keydown', tasti);
  document.documentElement.classList.remove('orz-modale-aperta');
});
</script>
