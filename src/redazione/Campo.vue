<template>
  <div class="orz-campo" :class="['orz-campo--' + campo.larghezza, 'orz-campo--' + campo.tipo]">
    <div v-if="campo.tipo !== 'sino'" class="orz-etichetta">
      <label :for="id">{{ campo.etichetta }}</label>
      <InfoTip v-if="campo.aiuto" :testo="campo.aiuto" :titolo="campo.etichetta" />
    </div>
    <slot />

    <textarea v-if="campo.tipo === 'testo_lungo' || campo.tipo === 'testo_social'" :id="id" rows="4" :value="modelValue" :disabled="spento" @input="manda($event.target.value)"></textarea>

    <EditorTesto
      v-else-if="campo.tipo === 'formattato'"
      :id="id"
      mini
      :model-value="modelValue"
      :spento="spento"
      @update:model-value="manda"
    />

    <select v-else-if="campo.tipo === 'scelta'" :id="id" :value="modelValue" :disabled="spento" @change="manda($event.target.value)">
      <option value="">—</option>
      <option v-for="o in opzioni" :key="o.valore" :value="o.valore">{{ o.etichetta }}</option>
    </select>

    <span v-else-if="campo.tipo === 'sino'" class="orz-sino">
      <label class="orz-spunta">
        <input :id="id" type="checkbox" :checked="modelValue === '1'" :disabled="spento" @change="manda($event.target.checked ? '1' : '')" />
        <span>{{ campo.etichetta }}</span>
      </label>
      <InfoTip v-if="campo.aiuto" :testo="campo.aiuto" :titolo="campo.etichetta" />
    </span>

    <CampoMedia
      v-else-if="['immagine', 'galleria', 'allegati'].includes(campo.tipo)"
      :model-value="modelValue"
      :tipo="campo.tipo"
      :media="media"
      :spento="spento"
      @update:model-value="manda"
      @media="(l) => emit('media', l)"
    />

    <CampoVideo
      v-else-if="campo.tipo === 'video'"
      :id="id"
      :model-value="modelValue"
      :media="media"
      :spento="spento"
      @update:model-value="manda"
      @media="(l) => emit('media', l)"
    />

    <CampoFotoSocial
      v-else-if="campo.tipo === 'foto_social'"
      :model-value="modelValue"
      :formati="campo.formati"
      :media="media"
      :spento="spento"
      @update:model-value="manda"
      @media="(l) => emit('media', l)"
    />

    <input
      v-else
      :id="id"
      :type="tipoInput"
      :step="campo.tipo === 'numero' ? 'any' : undefined"
      :min="campo.dopo && valori[campo.dopo] ? valori[campo.dopo] : undefined"
      :value="modelValue"
      :disabled="spento"
      @input="manda($event.target.value)"
    />
  </div>
</template>

<script setup>
// Un campo del tipo di contenuto (Impostazioni › Tipi di contenuto › Campi), come nella scheda di WordPress.
import { computed } from 'vue';
import InfoTip from '@/components/Builder/InfoTip.vue';
import EditorTesto from './EditorTesto.vue';
import CampoMedia from './CampoMedia.vue';
import CampoFotoSocial from './CampoFotoSocial.vue';
import CampoVideo from './CampoVideo.vue';

const props = defineProps({
  campo: { type: Object, required: true },
  modelValue: { type: String, default: '' },
  media: { type: Object, required: true },
  spento: { type: Boolean, default: false },
  // Tutti i valori della scheda: una data «dopo» un'altra non può venire prima di lei (min).
  valori: { type: Object, default: () => ({}) },
});
const emit = defineEmits(['update:modelValue', 'media']);

const id = 'orz-campo-' + props.campo.chiave;
// Una riga per opzione; «valore|Etichetta» salva il valore e mostra l'etichetta (Olobuild_Tipi_Contenuto::valori_scelta()).
const opzioni = computed(() => String(props.campo.opzioni || '').split('\n').filter(Boolean).map((riga) => {
  const p = riga.indexOf('|');
  return p < 0 ? { valore: riga, etichetta: riga } : { valore: riga.slice(0, p), etichetta: riga.slice(p + 1) };
}));
const tipoInput = computed(() => ({ numero: 'number', data: 'date', ora: 'time', link: 'url' })[props.campo.tipo] || 'text');

function manda(v) {
  emit('update:modelValue', v == null ? '' : String(v));
}
</script>
