<template>
  <div class="mb-space-y-2">
    <!-- v1.4.387 — riordino via useListSort (motore DnD custom) al posto di vuedraggable. -->
    <div v-if="Array.isArray(modelValue) && modelValue.length" class="mb-space-y-2">
      <template v-for="(img, idx) in modelValue" :key="itemKey(img)">
        <div
          class="mb-relative mb-group mb-flex mb-gap-2 mb-items-start"
          v-olo-draggable="itemDraggable(idx)"
          v-olo-drop-target="itemDrop(idx)"
        >
          <!-- Grip handle -->
          <div class="fg-grip mb-flex mb-items-center mb-self-stretch mb-cursor-grab mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity mb-shrink-0 mb-text-gray-500 hover:mb-text-gray-300">
            <svg width="8" height="14" viewBox="0 0 8 14" fill="currentColor">
              <circle cx="2" cy="2" r="1.2"/><circle cx="6" cy="2" r="1.2"/>
              <circle cx="2" cy="7" r="1.2"/><circle cx="6" cy="7" r="1.2"/>
              <circle cx="2" cy="12" r="1.2"/><circle cx="6" cy="12" r="1.2"/>
            </svg>
          </div>
          <!--
            L'ANTEPRIMA E' IL PULSANTE PER CAMBIARE IL MEDIA (v1.4.425).

            Prima, una foto gia' messa si poteva solo togliere e rimettere in
            coda: si perdevano la posizione, la didascalia e, sulla Pro
            Gallery, il sottotitolo e il testo. Chi cercava «sostituisci» non
            trovava niente e concludeva che la funzione fosse sparita, mentre
            non c'era mai stata. Cliccare qui cambia IL SOLO media e lascia
            intatto tutto il resto della riga.
          -->
          <button
            type="button"
            @click="sostituisci(idx)"
            :title="t('Cambia questo media')"
            class="mb-relative mb-block mb-w-16 mb-h-16 mb-shrink-0 mb-rounded mb-border mb-border-gray-600 mb-overflow-hidden mb-bg-gray-800 mb-p-0"
          >
            <template v-if="isVideo(img)">
              <img
                v-if="img.poster"
                :src="img.poster"
                :alt="img.alt || ''"
                class="mb-w-full mb-h-full mb-object-cover"
              />
              <div v-else class="mb-w-full mb-h-full mb-flex mb-items-center mb-justify-center mb-text-gray-500 mb-text-[9px]">{{ t('Video') }}</div>
              <!-- Play icon overlay -->
              <div class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center mb-pointer-events-none">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="white" opacity="0.85"><polygon points="8,5 19,12 8,19"/></svg>
              </div>
              <!-- Badge YT/VM -->
              <span v-if="embedBadge(img)" class="mb-absolute mb-top-0.5 mb-right-0.5 mb-bg-black/70 mb-text-white mb-text-[8px] mb-px-1 mb-rounded mb-leading-tight">{{ embedBadge(img) }}</span>
            </template>
            <img
              v-else
              :src="img.url"
              :alt="img.alt || ''"
              class="mb-w-full mb-h-full mb-object-cover"
            />
            <!-- La matita si accende passandoci sopra; il comando scritto sta sotto, sempre visibile. -->
            <span class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center mb-bg-black/50 mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity mb-pointer-events-none">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
            </span>
          </button>

          <div class="mb-flex-1 mb-min-w-0 mb-space-y-1">
            <input
              type="text"
              :value="img.caption || ''"
              @input="updateCaption(idx, $event.target.value)"
              :placeholder="righeExtra ? t('Titolo...') : t('Didascalia...')"
              class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200 mb-placeholder-gray-500"
            />
            <!--
              LE DUE RIGHE IN PIU' COMPAIONO SOLO DOVE IL TILE LE CHIEDE.

              Il campo galleria lo montano anche Galleria, Marquee e 360: la',
              di righe, ne serve una. Accese sempre, quelle tre schermate si
              troverebbero due caselle in piu' per ogni immagine senza che
              nessuno le abbia chieste.

              I nomi sono `subtitle` e `text` perche' sono gli stessi degli
              item dello ScrollScrub: lo stesso contenuto si sposta da un tile
              all'altro senza riscriverlo.
            -->
            <template v-if="righeExtra">
              <input
                type="text"
                :value="img.subtitle || ''"
                @input="updateRiga(idx, 'subtitle', $event.target.value)"
                :placeholder="t('Sottotitolo...')"
                class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200 mb-placeholder-gray-500"
              />
              <input
                type="text"
                :value="img.text || ''"
                @input="updateRiga(idx, 'text', $event.target.value)"
                :placeholder="t('Testo...')"
                class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200 mb-placeholder-gray-500"
              />
            </template>
            <!--
              Il comando primario non sta dentro un hover: chi non sa che
              l'anteprima si clicca non ci passa sopra il mouse per scoprirlo.
            -->
            <div class="fg-cmds">
              <button
                type="button"
                @click="sostituisci(idx)"
                class="fg-cmd"
              >{{ t('Cambia') }}</button>
              <!--
                L'INQUADRATURA DI QUESTA FOTO, non di tutte.
                Il punto focale della galleria vale per ogni foto insieme, e in una
                galleria vera i soggetti stanno in punti diversi: quella che
                rimetti a posto ne taglia un'altra. Qui si apre il pad con DENTRO
                questa foto, quindi si vede subito cosa resta nel riquadro.
              -->
              <button
                v-if="puntoFocale && !isVideo(img)"
                type="button"
                @click="apriFocale(idx)"
                class="fg-cmd"
                :class="{ 'is-on': !!img.focal, 'is-open': focaleAperta === idx }"
                :title="img.focal
                  ? t('Inquadratura scelta per questa foto') + ': ' + img.focal
                  : t('Segue la posizione della galleria')"
              >{{ t('Inquadratura') }}</button>
              <!-- Bottone poster per video -->
              <button
                v-if="isVideo(img)"
                type="button"
                @click="pickPoster(idx)"
                class="fg-cmd"
              >{{ t('Poster') }}</button>
            </div>
          </div>
          <button
            @click="removeImage(idx)"
            class="mb-bg-red-600 mb-text-white mb-rounded-full mb-w-5 mb-h-5 mb-text-[10px] mb-flex mb-items-center mb-justify-center mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity mb-shrink-0"
            :title="t('Rimuovi')"
          >{{ t('&times;') }}</button>
        </div>

        <!--
          Il pad sta FUORI dalla riga e non dentro: la riga e' una striscia bassa
          che si trascina per riordinare, e infilarci un riquadro alto 270 px la
          renderebbe una cosa che non si riesce piu' ad afferrare. Una sola foto
          per volta, o l'elenco diventa illeggibile.
        -->
        <div v-if="focaleAperta === idx" class="mb-ml-4 mb-mb-2 mb-p-2 mb-bg-gray-800 mb-border mb-border-gray-700 mb-rounded-md">
          <FieldObjectPosition
            :modelValue="img.focal || posizioneGalleria"
            :image-src="img.url"
            :object-fit="fitGalleria"
            @update:modelValue="impostaFocale(idx, $event)"
          />
          <div class="fg-cmds fg-cmds-pad">
            <button
              type="button"
              @click="focaleAperta = null"
              class="fg-cmd"
            >{{ t('Chiudi') }}</button>
            <button
              v-if="img.focal"
              type="button"
              @click="azzeraFocale(idx)"
              class="fg-cmd"
              :title="t('Torna a seguire il punto focale della galleria')"
            >{{ t('Come la galleria') }}</button>
          </div>
        </div>
      </template>
    </div>

    <!-- Bottoni -->
    <div class="mb-flex mb-gap-1.5 mb-flex-wrap">
      <button
        @click="addImages"
        class="mb-flex-1 mb-py-1.5 mb-px-2 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
      >
        Media
      </button>
      <button
        @click="addVideo"
        class="mb-flex-1 mb-py-1.5 mb-px-2 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
      >
        Video
      </button>
      <button
        @click="apriEmbed()"
        class="mb-flex-1 mb-py-1.5 mb-px-2 mb-rounded-md mb-text-xs mb-transition-colors mb-border"
        :class="showEmbedInput
          ? 'mb-bg-gray-600 mb-border-gray-500 mb-text-white'
          : 'mb-bg-gray-700 mb-border-gray-600 mb-text-gray-300 hover:mb-bg-gray-600'"
      >
        YT / Vimeo
      </button>
      <button
        v-for="svc in services"
        :key="svc.key"
        @click="togglePanel(svc.key)"
        class="mb-flex-1 mb-py-1.5 mb-px-2 mb-rounded-md mb-text-xs mb-transition-colors mb-border"
        :class="activePanel === svc.key
          ? 'mb-bg-gray-600 mb-border-gray-500 mb-text-white'
          : 'mb-bg-gray-700 mb-border-gray-600 mb-text-gray-300 hover:mb-bg-gray-600'"
      >
        {{ svc.label }}
      </button>
    </div>

    <!-- Pannello embed YouTube/Vimeo -->
    <div v-if="showEmbedInput" class="mb-flex mb-gap-1 mb-items-center">
      <input
        v-model="embedUrl"
        type="text"
        :placeholder="t('URL YouTube o Vimeo...')"
        class="mb-flex-1 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-200 mb-placeholder-gray-500"
        @keydown.enter="addEmbedVideo"
      />
      <button
        @click="addEmbedVideo"
        :disabled="!embedUrl.trim()"
        class="mb-px-3 mb-py-1.5 mb-rounded-md mb-text-xs mb-text-white mb-transition-colors"
        style="background: var(--olo-ui-accent, #e8622a);"
      >
        {{ sostituendo === null ? t('Aggiungi') : t('Sostituisci') }}
      </button>
    </div>

    <!-- Pannello stock photos (Unsplash / Pexels) -->
    <div v-if="activePanel" class="mb-border mb-border-gray-600 mb-rounded-lg mb-overflow-hidden">
      <!-- Barra ricerca -->
      <div class="mb-p-2 mb-bg-gray-800 mb-space-y-1.5">
        <div class="mb-flex mb-gap-1">
          <input
            v-model="stockQuery"
            @keydown.enter="searchStock(1)"
            type="text"
            :placeholder="`${t('Cerca su')} ${activeSvc.label}...`"
            class="mb-flex-1 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200 mb-placeholder-gray-500"
          />
          <button
            @click="searchStock(1)"
            :disabled="stockLoading || !stockQuery.trim()"
            class="mb-px-3 mb-py-1 mb-rounded-md mb-text-xs mb-text-white mb-transition-colors"
            style="background: var(--olo-ui-accent, #e8622a);"
          >
            {{ t('Cerca') }}
          </button>
        </div>
        <!-- Toggle foto / video -->
        <div v-if="activeSvc.hasVideo" class="mb-flex mb-gap-0.5 mb-bg-gray-700 mb-rounded-md mb-p-0.5">
          <button
            @click="toggleMediaType('photo')"
            class="mb-flex-1 mb-py-0.5 mb-rounded mb-text-[10px] mb-transition-colors"
            :class="stockMediaType === 'photo' ? 'mb-bg-gray-500 mb-text-white' : 'mb-text-gray-400 hover:mb-text-gray-200'"
          >{{ t('Foto') }}</button>
          <button
            @click="toggleMediaType('video')"
            class="mb-flex-1 mb-py-0.5 mb-rounded mb-text-[10px] mb-transition-colors"
            :class="stockMediaType === 'video' ? 'mb-bg-gray-500 mb-text-white' : 'mb-text-gray-400 hover:mb-text-gray-200'"
          >Video</button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="stockLoading" class="mb-flex mb-items-center mb-justify-center mb-py-6">
        <div class="mb-w-5 mb-h-5 mb-border-2 mb-border-gray-500 mb-border-t-white mb-rounded-full mb-animate-spin"></div>
      </div>

      <!-- Risultati -->
      <div v-else-if="stockResults.length" class="mb-p-2">
        <div class="mb-grid mb-grid-cols-3 mb-gap-1.5">
          <div
            v-for="photo in stockResults"
            :key="photo.id"
            class="mb-relative mb-cursor-pointer mb-group mb-rounded mb-overflow-hidden"
            style="aspect-ratio: 1;"
            @click="handleStockClick(photo)"
          >
            <!-- Thumbnail: video con <video> per Pixabay, immagine per il resto -->
            <video
              v-if="photo.is_video_thumb && photo.thumb"
              :src="photo.thumb + '#t=0.5'"
              preload="metadata"
              muted
              class="mb-w-full mb-h-full mb-object-cover"
            ></video>
            <img
              v-else
              :src="photo.thumb"
              :alt="photo.alt"
              class="mb-w-full mb-h-full mb-object-cover"
              loading="lazy"
            />
            <!-- Video overlay: play icon + durata -->
            <template v-if="photo.duration != null">
              <div class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center mb-pointer-events-none">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white" opacity="0.85"><polygon points="8,5 19,12 8,19"/></svg>
              </div>
              <span class="mb-absolute mb-bottom-1 mb-right-1 mb-bg-black/70 mb-text-white mb-text-[9px] mb-px-1 mb-rounded mb-leading-tight">{{ formatDuration(photo.duration) }}</span>
            </template>
            <div class="mb-absolute mb-inset-x-0 mb-bottom-0 mb-bg-gradient-to-t mb-from-black/70 mb-to-transparent mb-px-1 mb-pb-1 mb-pt-3 mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity">
              <span class="mb-text-[9px] mb-text-white mb-leading-tight mb-block mb-truncate">{{ photo.photographer }}</span>
            </div>
            <div
              v-if="downloadingIds.has(photo.id)"
              class="mb-absolute mb-inset-0 mb-bg-black/60 mb-flex mb-items-center mb-justify-center"
            >
              <div class="mb-w-5 mb-h-5 mb-border-2 mb-border-gray-400 mb-border-t-white mb-rounded-full mb-animate-spin"></div>
            </div>
          </div>
        </div>

        <div v-if="stockPage < stockTotalPages" class="mb-mt-2">
          <button
            @click="searchStock(stockPage + 1)"
            class="mb-w-full mb-py-1.5 mb-text-xs mb-text-gray-400 hover:mb-text-gray-200 mb-transition-colors"
          >
            {{ t('Carica altre...') }}
          </button>
        </div>

        <div class="mb-text-[9px] mb-text-gray-500 mb-mt-1 mb-text-center">
          {{ stockTotal }} {{ t('risultati su') }} {{ activeSvc.label }}
        </div>
      </div>

      <!-- Nessun risultato -->
      <div v-else-if="stockSearched" class="mb-py-4 mb-text-center mb-text-xs mb-text-gray-500">
        {{ t('Nessun risultato') }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, reactive, computed } from 'vue';
import { vOloDraggable, vOloDropTarget } from '@/composables/useDnD';
import { useListSort } from '@/composables/useListSort';
import { useMediaPicker } from '@/composables/useMediaPicker';
import { useToast } from '@/composables/useToast';
import FieldObjectPosition from './FieldObjectPosition.vue';

const toast = useToast();

const YOUTUBE_RE = /(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/;
const VIMEO_RE = /vimeo\.com\/(\d+)/;

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  /** Accende sottotitolo e testo su ogni immagine. Lo chiede il tile, non il campo. */
  righeExtra: { type: Boolean, default: false },
  /*
   * Accende l'inquadratura per singola foto. Lo chiede il tile perche' deve
   * essere il suo RENDER a saperla disegnare: un comando che salva un valore
   * che poi nessuno guarda e' peggio di un comando che non c'e'.
   */
  puntoFocale: { type: Boolean, default: false },
  /** Il ritaglio della galleria, per disegnare il pad com'e' davvero. */
  fitGalleria: { type: String, default: 'cover' },
  /** Da dove parte una foto che non ha ancora un'inquadratura sua. */
  posizioneGalleria: { type: String, default: 'center center' },
});
const emit = defineEmits(['update:modelValue']);

const { openSingleImage, openGallery, openVideo, openPosterImage } = useMediaPicker();

/* Quale riga sta mostrando il pad, o null. Una sola per volta. */
const focaleAperta = ref(null);

// Riordino immagini col motore DnD custom (v1.4.387, ex vuedraggable).
const { itemDraggable, itemDrop } = useListSort({
  handleSelector: '.fg-grip',
  ghostLabel: (index) => {
    const it = (props.modelValue || [])[index];
    return (it && (it.caption || it.alt)) || t('Immagine') + ' ' + (index + 1);
  },
  onMove: (from, to) => {
    // Il pad e' agganciato all'INDICE: dopo uno spostamento mostrerebbe la foto
    // sbagliata, e chi trascina non se ne accorgerebbe.
    focaleAperta.value = null;
    const arr = [...(props.modelValue || [])];
    const [moved] = arr.splice(from, 1);
    arr.splice(to, 0, moved);
    emit('update:modelValue', arr);
  },
});

function itemKey(item) {
  return `${item.id || 0}-${item.url || ''}-${item.embed || ''}`;
}

function isVideo(img) {
  return img && img.type === 'video';
}

function embedBadge(img) {
  if (!img || !img.embed) return '';
  if (YOUTUBE_RE.test(img.embed)) return 'YT';
  if (VIMEO_RE.test(img.embed)) return 'VM';
  return '';
}

// ── WP Media Library (immagini) ──
function addImages() {
  openGallery((newImages) => {
    const current = props.modelValue || [];
    const merged = [...current, ...newImages.map(img => ({ url: img.url, alt: img.alt, id: img.id, caption: '' }))];
    emit('update:modelValue', merged);
  });
}

// ── WP Media Library (video) ──
function addVideo() {
  openVideo((vid) => {
    const current = props.modelValue || [];
    emit('update:modelValue', [
      ...current,
      { url: vid.url, alt: vid.alt, id: vid.id, caption: '', type: 'video', poster: vid.poster || '' },
    ]);
  });
}

// ── Embed YouTube/Vimeo ──
const showEmbedInput = ref(false);
const embedUrl = ref('');
/** L'indice della riga che il pannello sta sostituendo, o null se aggiunge. */
const sostituendo = ref(null);

function apriEmbed() {
  sostituendo.value = null;
  embedUrl.value = '';
  showEmbedInput.value = !showEmbedInput.value;
}

function addEmbedVideo() {
  const raw = embedUrl.value.trim();
  if (!raw) return;

  let poster = '';
  const ytMatch = raw.match(YOUTUBE_RE);
  if (ytMatch) {
    poster = 'https://img.youtube.com/vi/' + ytMatch[1] + '/hqdefault.jpg';
  }

  const current = props.modelValue || [];
  if (sostituendo.value === null) {
    emit('update:modelValue', [
      ...current,
      { url: '', alt: '', id: 0, caption: '', type: 'video', embed: raw, poster },
    ]);
  } else {
    // Il video caricato che c'era prima se ne va con url e id: una riga con
    // un embed E un file resterebbe ambigua per chi la disegna.
    const i0 = sostituendo.value;
    emit('update:modelValue', current.map((it, i) => (
      i === i0 ? { ...it, type: 'video', embed: raw, poster: poster || it.poster || '', url: '', id: 0 } : it
    )));
  }

  sostituendo.value = null;
  embedUrl.value = '';
  showEmbedInput.value = false;
}

/**
 * CAMBIA IL MEDIA DI UNA RIGA E BASTA.
 *
 * Didascalia, sottotitolo, testo, poster scelto a mano e posizione nella
 * galleria restano dov'erano: e' tutto il senso del comando, perche' «togli e
 * rimetti in coda» li perdeva tutti. Un'immagine resta un'immagine e un video
 * resta un video, cosi' chi clicca sa gia' cosa gli si aprira'.
 */
function sostituisci(idx) {
  const riga = (props.modelValue || [])[idx];
  if (!riga) return;

  // Un embed non sta nella libreria di WordPress: si cambia il suo indirizzo.
  if (riga.embed) {
    embedUrl.value = riga.embed;
    sostituendo.value = idx;
    showEmbedInput.value = true;
    return;
  }

  const apri = isVideo(riga) ? openVideo : openSingleImage;
  apri((m) => {
    const scelto = { url: m.url, alt: m.alt || '', id: m.id };
    // Il poster scelto a mano non si perde per un cambio di video.
    if (isVideo(riga)) scelto.poster = riga.poster || m.poster || '';
    emit('update:modelValue', (props.modelValue || []).map((it, i) => (
      i === idx ? { ...it, ...scelto } : it
    )));
  });
}

// ── Poster picker ──
function pickPoster(idx) {
  openPosterImage((result) => {
    const updated = (props.modelValue || []).map((img, i) =>
      i === idx ? { ...img, poster: result.url, poster_id: result.id } : img
    );
    emit('update:modelValue', updated);
  });
}

function apriFocale(idx) {
  focaleAperta.value = focaleAperta.value === idx ? null : idx;
}

/**
 * L'inquadratura di UNA foto. Sta in `focal` sull'item, che e' una chiave nuova:
 * una galleria salvata prima non ce l'ha, e chi non ce l'ha continua a seguire il
 * punto focale della galleria, quindi nessun template esistente si sposta.
 */
function impostaFocale(index, valore) {
  const updated = (props.modelValue || []).map((img, i) => (
    i === index ? { ...img, focal: String(valore || '') } : img
  ));
  emit('update:modelValue', updated);
}

/* Torna a seguire la galleria: si TOGLIE la chiave, non si scrive il valore
   della galleria dentro la foto, o cambiando quello globale questa resterebbe
   indietro senza che si capisca perche'. */
function azzeraFocale(index) {
  const updated = (props.modelValue || []).map((img, i) => {
    if (i !== index) return img;
    const copia = { ...img };
    delete copia.focal;
    return copia;
  });
  emit('update:modelValue', updated);
}

function removeImage(index) {
  const updated = (props.modelValue || []).filter((_, i) => i !== index);
  emit('update:modelValue', updated);
}

function updateCaption(index, value) {
  const updated = (props.modelValue || []).map((img, i) =>
    i === index ? { ...img, caption: value } : img
  );
  emit('update:modelValue', updated);
}

/** Il sottotitolo e il testo, che esistono solo con `righeExtra` acceso. */
function updateRiga(index, chiave, value) {
  const updated = (props.modelValue || []).map((img, i) =>
    i === index ? { ...img, [chiave]: value } : img
  );
  emit('update:modelValue', updated);
}

// ── Stock photo services ──
const services = [
  { key: 'unsplash', label: 'Unsplash', searchPath: '/unsplash/search', downloadPath: '/unsplash/download', hasTracking: true },
  { key: 'pexels', label: 'Pexels', searchPath: '/pexels/search', downloadPath: '/pexels/download', hasTracking: false, hasVideo: true, videoSearchPath: '/pexels/videos', videoDownloadPath: '/pexels/video-download' },
  { key: 'pixabay', label: 'Pixabay', searchPath: '/pixabay/search', downloadPath: '/pixabay/download', hasTracking: false, hasVideo: true, videoSearchPath: '/pixabay/videos', videoDownloadPath: '/pixabay/video-download' },
  { key: 'openverse', label: 'Openverse', searchPath: '/openverse/search', downloadPath: '/openverse/download', hasTracking: false },
];

const preferredProvider = window.oloData?.stockmedia?.preferred || 'unsplash';
const activePanel = ref(services.find(s => s.key === preferredProvider) ? preferredProvider : null);
const activeSvc = computed(() => services.find(s => s.key === activePanel.value) || services[0]);

// Per-service state
const stateMap = reactive({});
function getState(key) {
  if (!stateMap[key]) {
    stateMap[key] = { query: '', results: [], loading: false, searched: false, page: 1, total: 0, totalPages: 0, mediaType: 'photo' };
  }
  return stateMap[key];
}

const stockMediaType = computed({
  get: () => getState(activePanel.value).mediaType,
  set: (v) => { getState(activePanel.value).mediaType = v; },
});

const stockQuery = computed({
  get: () => getState(activePanel.value).query,
  set: (v) => { getState(activePanel.value).query = v; },
});
const stockResults = computed(() => getState(activePanel.value).results);
const stockLoading = computed(() => getState(activePanel.value).loading);
const stockSearched = computed(() => getState(activePanel.value).searched);
const stockPage = computed(() => getState(activePanel.value).page);
const stockTotal = computed(() => getState(activePanel.value).total);
const stockTotalPages = computed(() => getState(activePanel.value).totalPages);

const downloadingIds = reactive(new Set());

function togglePanel(key) {
  activePanel.value = activePanel.value === key ? null : key;
}

function toggleMediaType(type) {
  const st = getState(activePanel.value);
  if (st.mediaType === type) return;
  st.mediaType = type;
  st.results = [];
  st.searched = false;
  st.page = 1;
  st.total = 0;
  st.totalPages = 0;
  if (st.query.trim()) searchStock(1);
}

async function searchStock(page = 1) {
  const svc = activeSvc.value;
  const st = getState(svc.key);
  const q = st.query.trim();
  if (!q) return;

  const isVideo = st.mediaType === 'video' && svc.hasVideo;
  const searchPath = isVideo ? svc.videoSearchPath : svc.searchPath;
  const perPage = isVideo ? 15 : 30;

  st.loading = true;
  st.searched = true;

  try {
    const params = new URLSearchParams({ query: q, page, per_page: perPage });
    const resp = await fetch(`${window.oloData.restUrl}${searchPath}?${params}`, {
      headers: { 'X-WP-Nonce': window.oloData.nonce },
    });

    if (!resp.ok) {
      const errText = await resp.text().catch(() => '');
      console.error(`${svc.label} search HTTP ${resp.status}:`, errText);
      toast.error(t(`Errore ${svc.label}: ${resp.status} — ${resp.statusText}`));
      return;
    }

    const data = await resp.json();

    if (page === 1) {
      st.results = data.results || [];
    } else {
      st.results = [...st.results, ...(data.results || [])];
    }
    st.page = page;
    st.total = data.total || 0;
    st.totalPages = data.total_pages || 0;
  } catch (err) {
    console.error(`${svc.label} search error:`, err);
    toast.error(t('Errore nella ricerca immagini. Riprova.'));
  } finally {
    st.loading = false;
  }
}

function handleStockClick(photo) {
  const st = getState(activePanel.value);
  const svc = activeSvc.value;
  if (st.mediaType === 'video' && svc.hasVideo) {
    downloadStockVideo(photo);
  } else {
    downloadStockPhoto(photo);
  }
}

async function downloadStockPhoto(photo) {
  if (downloadingIds.has(photo.id)) return;
  downloadingIds.add(photo.id);

  const svc = activeSvc.value;

  try {
    const body = {
      photo_id: String(photo.id),
      regular_url: photo.regular,
      alt: photo.alt,
      photographer: photo.photographer,
    };
    if (svc.hasTracking && photo.download_location) {
      body.download_location = photo.download_location;
    }

    const resp = await fetch(`${window.oloData.restUrl}${svc.downloadPath}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': window.oloData.nonce,
      },
      body: JSON.stringify(body),
    });

    if (!resp.ok) {
      console.error(`${svc.label} download HTTP ${resp.status}`);
      toast.error(t(`Errore download da ${svc.label}: ${resp.status} — ${resp.statusText}`));
      return;
    }

    const data = await resp.json();

    if (data.id && data.url) {
      const current = props.modelValue || [];
      emit('update:modelValue', [
        ...current,
        { url: data.url, alt: data.alt || '', id: data.id, caption: data.caption || '' },
      ]);
    }
  } catch (err) {
    console.error(`${svc.label} download error:`, err);
    toast.error(t('Errore nel download immagine. Riprova.'));
  } finally {
    downloadingIds.delete(photo.id);
  }
}

async function downloadStockVideo(video) {
  if (downloadingIds.has(video.id)) return;
  downloadingIds.add(video.id);

  const svc = activeSvc.value;

  try {
    const body = {
      photo_id: String(video.id),
      regular_url: video.regular,
      alt: video.alt,
      photographer: video.photographer,
      thumb_url: video.thumb || '',
    };

    const resp = await fetch(`${window.oloData.restUrl}${svc.videoDownloadPath}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': window.oloData.nonce,
      },
      body: JSON.stringify(body),
    });

    if (!resp.ok) {
      console.error(`${svc.label} video download HTTP ${resp.status}`);
      toast.error(t(`Errore download video da ${svc.label}: ${resp.status} — ${resp.statusText}`));
      return;
    }

    const data = await resp.json();

    if (data.id && data.url) {
      const current = props.modelValue || [];
      emit('update:modelValue', [
        ...current,
        { url: data.url, alt: data.alt || '', id: data.id, caption: data.caption || '', type: 'video', poster: data.poster || '' },
      ]);
    }
  } catch (err) {
    console.error(`${svc.label} video download error:`, err);
    toast.error(t('Errore nel download video. Riprova.'));
  } finally {
    downloadingIds.delete(video.id);
  }
}

function formatDuration(sec) {
  if (!sec) return '';
  const m = Math.floor(sec / 60);
  const s = sec % 60;
  return m + ':' + String(s).padStart(2, '0');
}
</script>

<style scoped>
.fg-grip:active {
  cursor: grabbing;
}

/*
 * I COMANDI DELLA RIGA, con uno stile loro e non con le utility.
 *
 * Erano scritti con le classi del tema scuro (text-gray-400 a 10 px): nel
 * pannello chiaro quel grigio diventa #888 su bianco e si legge a fatica, e i
 * due comandi finivano appiccicati, «CambiaInquadratura», perche' la spaziatura
 * arrivava da una utility che li' non arrivava mai. Un comando che non si legge
 * e' un comando che non c'e', ed e' gia' successo una volta con questa riga.
 *
 * Qui il colore viene dal testo del pannello (currentColor smorzato), quindi
 * funziona sul chiaro e sullo scuro senza due serie di classi da tenere
 * allineate, e la spaziatura e' scritta accanto a chi la usa.
 */
.fg-cmds {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
  margin-top: 2px;
}
.fg-cmds-pad {
  margin-top: 6px;
}
.fg-cmd {
  appearance: none;
  border: 1px solid transparent;
  background: transparent;
  border-radius: 4px;
  padding: 2px 6px;
  font-size: 11px;
  line-height: 1.4;
  color: currentColor;
  opacity: 0.7;
  cursor: pointer;
  /* 0.7 e non meno: a 0.62 il contrasto sul pannello chiaro scende a 4.88, cioe'
     appena sopra la soglia leggibile. Misurato, non stimato. */
  transition: opacity 120ms, background-color 120ms, border-color 120ms;
}
.fg-cmd:hover {
  opacity: 1;
  background: rgba(127, 127, 127, 0.14);
  border-color: rgba(127, 127, 127, 0.28);
}
.fg-cmd:focus-visible {
  outline: 2px solid var(--olo-color-primary, #e1474f);
  outline-offset: 1px;
  opacity: 1;
}
/* Questa foto ha un'inquadratura sua: si vede scorrendo l'elenco, senza aprire niente. */
.fg-cmd.is-on {
  opacity: 1;
  font-weight: 600;
  border-color: rgba(127, 127, 127, 0.35);
}
.fg-cmd.is-open {
  opacity: 1;
  background: rgba(127, 127, 127, 0.16);
  border-color: rgba(127, 127, 127, 0.4);
}
</style>
