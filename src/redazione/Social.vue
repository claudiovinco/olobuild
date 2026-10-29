<template>
  <div class="orz-blocco orz-social">
    <div class="orz-etichetta orz-etichetta--fila">
      <span class="orz-social-titolo">
        <h2 class="orz-sezione">{{ t('Social') }}</h2>
        <InfoTip :titolo="t('Social')" :testo="AIUTO" />
      </span>
      <a v-if="info.collega" class="orz-link orz-link--piccolo" :href="info.collega" target="_blank" rel="noopener">
        <Icona nome="esterno" :dim="13" /> {{ t('Account dei social') }}
      </a>
    </div>

    <!-- Già condiviso: com'è andata, rete per rete -->
    <div v-if="info.condiviso" class="orz-social-esito">
      <p class="orz-riga-info">
        <span class="orz-stato" :class="'orz-stato--' + classe(info.condiviso.stato)">{{ info.condiviso.statoNome }}</span>
      </p>
      <ul class="orz-social-righe">
        <li v-for="x in info.condiviso.righe" :key="x.target" class="orz-social-riga">
          <span class="orz-rete" :class="'orz-rete--' + x.rete" aria-hidden="true">{{ iniziale(x.nomeRete) }}</span>
          <span class="orz-social-nome">
            <strong>{{ x.nomeRete }}</strong>
            <small>{{ x.nome }}</small>
            <small v-if="x.errore" class="orz-social-errore">{{ x.errore }}</small>
          </span>
          <span class="orz-stato" :class="'orz-stato--' + classe(x.stato)">{{ x.statoNome }}</span>
          <a v-if="x.url" class="orz-btn orz-btn--piccolo" :href="x.url" target="_blank" rel="noopener"><Icona nome="esterno" :dim="13" /> {{ t('Apri il post') }}</a>
          <button v-if="x.stato === 'failed'" type="button" class="orz-btn orz-btn--piccolo" :disabled="occupato || spento" @click="agisci('riprova', x.target)">
            <Icona nome="aggiorna" :dim="13" /> {{ t('Riprova') }}
          </button>
        </li>
      </ul>
      <div class="orz-riga-pulsanti">
        <button v-if="info.condiviso.stato === 'draft'" type="button" class="orz-btn orz-btn--primario orz-btn--piccolo" :disabled="occupato || spento" @click="agisci('pubblica')">
          {{ t('Pubblica adesso sui social') }}
        </button>
        <a class="orz-link" :href="info.condiviso.apri" target="_blank" rel="noopener"><Icona nome="esterno" :dim="13" /> {{ t('Apri in OLOsocial') }}</a>
      </div>
      <p v-if="errore" class="orz-avviso orz-avviso--errore" role="alert">{{ errore }}</p>
    </div>

    <!-- Nessun social collegato -->
    <div v-else-if="!info.accountDisponibili.length" class="orz-social-vuoto">
      <p class="orz-nota">{{ t('Nessun social collegato a OLOsocial.') }}</p>
      <a class="orz-btn orz-btn--piccolo" :href="info.collega" target="_blank" rel="noopener"><Icona nome="esterno" :dim="13" /> {{ t('Collega un social') }}</a>
    </div>

    <!-- Le scelte: quando, dove, con quale testo -->
    <template v-else>
      <div class="orz-social-quando" role="radiogroup" :aria-label="t('Quando esce sui social')">
        <label v-for="m in MODI" :key="m.v" class="orz-scelta" :class="{ on: modelValue.modo === m.v }">
          <input v-model="modelValue.modo" type="radio" :value="m.v" :disabled="spento" />
          <span><strong>{{ m.titolo }}</strong><small>{{ m.nota }}</small></span>
        </label>
      </div>

      <div v-if="$slots.foto && modelValue.modo !== 'no'" class="orz-social-foto-campi">
        <slot name="foto" />
      </div>

      <div v-if="modelValue.modo !== 'no'" class="orz-social-reti">
        <div v-for="a in info.accountDisponibili" :key="a.id" class="orz-social-carta" :class="{ on: scelto(a) }">
          <label class="orz-social-testa">
            <input type="checkbox" :checked="scelto(a)" :disabled="spento" @change="alterna(a, $event.target.checked)" />
            <span class="orz-rete" :class="'orz-rete--' + a.rete" aria-hidden="true">{{ iniziale(a.nomeRete) }}</span>
            <span class="orz-social-nome"><strong>{{ a.nomeRete }}</strong><small>{{ a.nome }}</small></span>
            <img v-if="a.foto" class="orz-social-foto" :src="a.foto" alt="" />
          </label>
          <div v-if="scelto(a)" class="orz-social-corpo">
            <textarea
              :value="testoDi(a)"
              rows="4"
              :placeholder="a.modello || t('Il testo del modello di OLOsocial')"
              :aria-label="t('Testo per %s').replace('%s', a.nomeRete)"
              :disabled="spento"
              @input="scrivi(a, $event.target.value)"
            ></textarea>
            <div class="orz-social-piede">
              <span class="orz-nota">{{ notaTesto(a) }}</span>
              <span v-if="a.max" class="orz-conto" :class="{ 'is-troppo': lunghezza(a) > a.max }">{{ lunghezza(a) }} / {{ a.max }}</span>
            </div>
          </div>
        </div>
      </div>
      <p v-if="modelValue.modo !== 'no' && !modelValue.account.length" class="orz-nota">{{ t('Spunta almeno un social: altrimenti non esce niente.') }}</p>

      <label v-if="uscito && modelValue.modo !== 'no'" class="orz-spunta orz-social-adesso">
        <input v-model="modelValue.adesso" type="checkbox" :disabled="spento || !modelValue.account.length" />
        <span>{{ t('Condividi adesso, salvando: il contenuto è già pubblicato') }}</span>
      </label>
    </template>
  </div>
</template>

<script setup>
/**
 * I social di un contenuto, dalla Redazione: quando esce (insieme al contenuto, come bozza, niente), su quali account,
 * con quale testo per ognuno; dopo l'uscita com'è andata, con «Apri il post», «Riprova» e la bozza da pubblicare. Le
 * scelte sono quelle del riquadro Social di OLOsocial (Olo_Social_Riquadro::per_la_scheda / salva_scelte): farle qui o
 * nella scheda di WordPress è la stessa cosa.
 */
import { ref } from 'vue';
import { t } from '@/i18n';
import InfoTip from '@/components/Builder/InfoTip.vue';
import Icona from './Icona.vue';
import { chiama } from './api.js';

const props = defineProps({
  // Le scelte nella scheda: { modo, account: [id], testi: { id: testo }, adesso }
  modelValue: { type: Object, required: true },
  // I campi del contenuto (f.campi) e quelli «Testo per i social» del tipo: il testo di una rete sta lì, se c'è.
  campi: { type: Object, default: () => ({}) },
  campiTesto: { type: Array, default: () => [] },
  // Da OLOsocial: account disponibili, esito se già condiviso, indirizzo degli account.
  info: { type: Object, required: true },
  idContenuto: { type: Number, default: 0 },
  spento: { type: Boolean, default: false },
  uscito: { type: Boolean, default: false },
});
const emit = defineEmits(['aggiornato']);

const occupato = ref(false);
const errore = ref('');

const MODI = [
  { v: 'pubblica', titolo: t('Insieme al contenuto'), nota: t('esce sui social quando lo pubblichi') },
  { v: 'bozza', titolo: t('Come bozza da controllare'), nota: t('OLOsocial la prepara nel suo calendario') },
  { v: 'no', titolo: t('Niente social'), nota: t('per questo contenuto') },
];

const AIUTO = t('Scegli su quali social esce questo contenuto e con quale testo.\n\nInsieme al contenuto: il post parte quando pubblichi, o all\'ora programmata.\n\nCome bozza: OLOsocial prepara il post nel suo calendario; lo pubblichi da qui con «Pubblica adesso sui social».\n\nTesto: se lo lasci vuoto, OLOsocial usa il suo modello (o i campi «Testo per i social» del contenuto). Il link al contenuto lo aggiunge OLOsocial.\n\nFoto: l\'immagine in evidenza, o la «Foto per i social» del contenuto, inquadrata per ogni rete.');

function iniziale(nome) {
  return String(nome || '?').charAt(0);
}

function scelto(a) {
  return props.modelValue.account.includes(a.id);
}

function alterna(a, acceso) {
  const lista = props.modelValue.account.filter((id) => id !== a.id);
  if (acceso) lista.push(a.id);
  props.modelValue.account = lista;
}

/**
 * Dove sta il testo di un account, cioè quello che uscirà: il testo scritto apposta per l'account (vince sempre), se no
 * il campo «Testo per i social» del tipo per la sua rete (Instagram, oppure le altre reti, oppure tutte), se no il testo
 * dell'account (vuoto = il modello di OLOsocial).
 */
function campoDi(a) {
  if ((props.modelValue.testi[a.id] || '') !== '') return null;
  const gruppo = a.rete === 'instagram' ? 'instagram' : 'altre';
  return props.campiTesto.find((c) => c.reti === gruppo) || props.campiTesto.find((c) => c.reti === 'tutte') || null;
}

function testoDi(a) {
  const c = campoDi(a);
  return c ? props.campi[c.chiave] || '' : props.modelValue.testi[a.id] || '';
}

function scrivi(a, testo) {
  const c = campoDi(a);
  if (c) props.campi[c.chiave] = testo;
  else props.modelValue.testi = { ...props.modelValue.testi, [a.id]: testo };
}

function notaTesto(a) {
  const c = campoDi(a);
  if (c) return c.etichetta;
  return props.modelValue.testi[a.id] ? t('Testo scritto per questo account') : t('Testo del modello');
}

function lunghezza(a) {
  return [...(testoDi(a) || a.modello || '')].length;
}

// Lo stato di OLOsocial come colore della Redazione.
function classe(stato) {
  if (stato === 'published') return 'publish';
  if (stato === 'failed' || stato === 'partial') return 'errore';
  if (stato === 'draft') return 'draft';
  if (stato === 'cancelled') return 'private';
  return 'future';
}

async function agisci(azione, target = 0) {
  occupato.value = true;
  errore.value = '';
  try {
    const r = await chiama('redazione/' + props.idContenuto + '/social', { metodo: 'POST', corpo: { azione, target } });
    emit('aggiornato', r);
  } catch (e) {
    errore.value = e.message;
  } finally {
    occupato.value = false;
  }
}
</script>
