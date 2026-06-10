<template>
  <!--
    BackgroundControls — pannello "Sfondo" ridisegnato (design handoff
    "Redesign pannello Sfondo"). Il vecchio selettore stipava 10 tipi in una
    riga di chip testuali (etichette a capo, niente anteprime); ora è una
    GRIGLIA di swatch con mini-anteprima reale, raggruppata in
    Colore · Generativi · Media. Sotto, i controlli del tipo scelto seguono il
    sistema (slider arancio CHROME via --olo-ui-accent + valbox con unità,
    select con chevron) e la Sovrapposizione è una sotto-sezione con occhio.

    CONTRATTO DATI INVARIATO: cambia SOLO la presentazione. Tutte le chiavi
    salvate restano identiche (type, color, gradient*, image_*, video_*,
    gallery_*, mesh_*, pattern_*, glow_*, crt_*, overlay_*, parallax). La resa
    canvas (useBackgroundStyle.js) e frontend (class-frontend-renderer.php) NON
    sono toccate: i template esistenti continuano a funzionare.

    Accento = CHROME del builder (arancio fisso #e8622a via --olo-ui-accent),
    NON il primario tile. Il colore DEI contenuti (alone, base, pattern…) resta
    token-first (var(--olo-color-*)) tramite FieldColor.
  -->
  <div class="olo-bg2">

    <!-- ───────── TIPO DI SFONDO — griglia swatch raggruppata ───────── -->
    <div class="subhead first"><span class="t2">{{ t('Tipo di sfondo') }}</span></div>
    <div class="bgtypes">
      <template v-for="(group, gi) in typeGroups" :key="group.cat">
        <div class="cat" :class="{ first: gi === 0 }">{{ t(group.cat) }}</div>
        <button
          v-for="it in group.items"
          :key="it.value"
          type="button"
          class="bt"
          :class="{ on: bg.type === it.value }"
          :aria-pressed="bg.type === it.value"
          :title="t(it.label)"
          @click="updateField('type', it.value)"
        >
          <span v-if="bg.type === it.value" class="ck">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          </span>
          <span class="sw-prev" :class="it.prev">
            <span v-if="it.value === 'image'" class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="1.6"/><path d="m21 15-5-5L5 21"/></svg></span>
            <span v-else-if="it.value === 'video'" class="ico"><span class="play"></span></span>
            <span v-else-if="it.value === 'gallery'" class="ico"><span class="stack"><i></i><i></i><i></i></span></span>
          </span>
          <span class="bl">{{ t(it.label) }}</span>
        </button>
      </template>
    </div>

    <!-- ───────── IMPOSTAZIONI · per-tipo ───────── -->
    <template v-if="bg.type && bg.type !== 'none'">
      <div class="subhead"><span class="t2">{{ t('Impostazioni') }} · <span class="q">{{ t(currentTypeLabel) }}</span></span></div>

      <!-- Tinta unita -->
      <!-- v1.0.74 — niente slider "Opacità sfondo": ridondante con l'Alfa di
           FieldColor. color_opacity:100 resta nei dati come no-op legacy. -->
      <div v-if="bg.type === 'solid'" class="type-body">
        <div class="field">
          <span class="cl">{{ t('Colore') }}</span>
          <FieldColor :modelValue="bg.color || '#ffffff'" @update:modelValue="updateField('color', $event)" />
        </div>
        <div class="pv" :style="{ background: solidPreview }"></div>
      </div>

      <!-- Gradiente (FieldGradient ha già la sua preview interna) -->
      <div v-else-if="bg.type === 'gradient'" class="type-body">
        <FieldGradient :modelValue="gradientModel" @update:modelValue="onGradientUpdate" />
      </div>

      <!-- Immagine -->
      <div v-else-if="bg.type === 'image'" class="type-body">
        <div v-if="bg.image_url" class="thumb">
          <img :src="bg.image_url" :alt="t('Sfondo')" />
          <button type="button" class="thumb-x" :title="t('Rimuovi immagine')" @click="updateField('image_url', '')">&times;</button>
        </div>
        <button type="button" class="btn-soft" @click="pickBgImage">{{ bg.image_url ? t('Cambia immagine') : t('Seleziona immagine') }}</button>

        <div class="row">
          <span class="rowlab">{{ t('Dimensione') }}</span>
          <div class="selwrap">
            <FieldSelect ui="dropdown" :model-value="bg.image_size || 'cover'" :options="SIZE_OPTS" @update:model-value="updateField('image_size', $event)" />
          </div>
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Posizione') }}</span>
          <div class="selwrap">
            <FieldSelect ui="dropdown" :model-value="bg.image_position || 'center center'" :options="POSITION_OPTS" @update:model-value="updateField('image_position', $event)" />
          </div>
        </div>

        <div v-if="showParallax" class="field field--sep">
          <div class="tgl-row">
            <button type="button" class="tgl" :class="{ on: isParallaxEnabled }" :aria-pressed="isParallaxEnabled" @click="toggleParallax"><b></b></button>
            <span class="tl">{{ t('Parallasse') }}</span>
          </div>
          <div v-if="isParallaxEnabled" class="sub-editor">
            <ParallaxEditor :modelValue="bgParallaxData" :properties="bgParallaxProperties" @update:modelValue="updateParallaxData" />
          </div>
        </div>
      </div>

      <!-- Video -->
      <div v-else-if="bg.type === 'video'" class="type-body">
        <div v-if="bg.video_url" class="thumb">
          <div class="thumb-video">
            <img v-if="bg.video_poster" :src="bg.video_poster" :alt="t('Poster video')" />
            <span v-else class="thumb-vico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></span>
          </div>
          <button type="button" class="thumb-x" :title="t('Rimuovi video')" @click="removeVideo">&times;</button>
        </div>
        <button type="button" class="btn-soft" @click="pickBgVideo">{{ bg.video_url ? t('Cambia video') : t('Seleziona video') }}</button>
        <button type="button" class="btn-soft" @click="pickBgPoster">{{ bg.video_poster ? t('Cambia poster') : t('Seleziona poster') }}</button>

        <div class="row">
          <span class="rowlab">{{ t('Adattamento') }}</span>
          <div class="selwrap">
            <FieldSelect ui="dropdown" :model-value="bg.video_fit || 'cover'" :options="VIDEO_FIT_OPTS" @update:model-value="updateField('video_fit', $event)" />
          </div>
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Posizione') }}</span>
          <div class="selwrap">
            <FieldSelect ui="dropdown" :model-value="bg.image_position || 'center center'" :options="POSITION_OPTS" @update:model-value="updateField('image_position', $event)" />
          </div>
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Altezza') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.cover_height || 0, 0, 1200)" :value="bg.cover_height || 0" :aria-label="t('Altezza cover')" min="0" max="1200" step="10" @input="commitInt('cover_height', $event.target.value, 0)" />
          <div class="valbox"><input type="number" :value="bg.cover_height || 0" min="0" max="1200" step="10" @input="commitInt('cover_height', $event.target.value, 0)" @wheel="handleNumberWheel" /><span class="u">px</span></div>
        </div>
        <p class="hint">{{ t('0 = auto (altezza contenuto)') }}</p>
        <div class="row">
          <span class="rowlab">{{ t('Scala') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.video_scale || 100, 100, 300)" :value="bg.video_scale || 100" :aria-label="t('Scala video')" min="100" max="300" step="10" @input="commitInt('video_scale', $event.target.value, 100)" />
          <div class="valbox"><input type="number" :value="bg.video_scale || 100" min="100" max="300" step="10" @input="commitInt('video_scale', $event.target.value, 100)" @wheel="handleNumberWheel" /><span class="u">%</span></div>
        </div>

        <!-- Parallasse SOLO sfondo (video): scala/opacità/sfocatura allo scroll, contenuto fermo -->
        <div v-if="showParallax" class="field field--sep">
          <div class="tgl-row">
            <button type="button" class="tgl" :class="{ on: isParallaxEnabled }" :aria-pressed="isParallaxEnabled" @click="toggleParallax"><b></b></button>
            <span class="tl">{{ t('Parallasse') }}</span>
          </div>
          <div v-if="isParallaxEnabled" class="sub-editor">
            <ParallaxEditor :modelValue="bgParallaxData" :properties="videoParallaxProperties" @update:modelValue="updateParallaxData" />
          </div>
        </div>
      </div>

      <!-- Galleria -->
      <div v-else-if="bg.type === 'gallery'" class="type-body">
        <div class="gallery-head">
          <span class="gcount">{{ (bg.gallery_images || []).length }} {{ t('immagini selezionate') }}</span>
          <button v-if="(bg.gallery_images || []).length" type="button" class="gclear" :title="t('Svuota')" @click="updateField('gallery_images', [])">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
          </button>
        </div>
        <div v-if="(bg.gallery_images || []).length" class="gthumbs">
          <div v-for="(img, idx) in bg.gallery_images" :key="idx" class="gthumb">
            <img :src="img.url" :alt="img.alt" />
            <button type="button" class="gthumb-x" @click="removeGalleryImage(idx)">&times;</button>
          </div>
        </div>
        <button type="button" class="btn-soft" @click="pickGalleryImages">{{ (bg.gallery_images || []).length ? t('Cambia immagini') : t('Seleziona immagini') }}</button>

        <div class="tgl-row">
          <button type="button" class="tgl" :class="{ on: bg.gallery_loop !== false }" :aria-pressed="bg.gallery_loop !== false" @click="updateField('gallery_loop', !(bg.gallery_loop !== false))"><b></b></button>
          <span class="tl">{{ t('Ciclo infinito') }}</span>
        </div>

        <div class="row">
          <span class="rowlab">{{ t('Durata') }}</span>
          <span class="spacer"></span>
          <div class="valbox valbox--wide"><input type="number" :value="bg.gallery_duration || 5000" min="1000" max="30000" step="500" @input="commitInt('gallery_duration', $event.target.value, 5000)" @wheel="handleNumberWheel" /><span class="u">ms</span></div>
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Transizione') }}</span>
          <div class="selwrap">
            <FieldSelect ui="dropdown" :model-value="bg.gallery_transition || 'fade'" :options="GALLERY_TRANSITION_OPTS" @update:model-value="updateField('gallery_transition', $event)" />
          </div>
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Durata trans.') }}</span>
          <span class="spacer"></span>
          <div class="valbox valbox--wide"><input type="number" :value="bg.gallery_transition_ms || 500" min="100" max="3000" step="100" @input="commitInt('gallery_transition_ms', $event.target.value, 500)" @wheel="handleNumberWheel" /><span class="u">ms</span></div>
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Dimensione') }}</span>
          <div class="selwrap">
            <FieldSelect ui="dropdown" :model-value="bg.image_size || 'cover'" :options="SIZE_OPTS" @update:model-value="updateField('image_size', $event)" />
          </div>
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Posizione') }}</span>
          <div class="selwrap">
            <FieldSelect ui="dropdown" :model-value="bg.image_position || 'center center'" :options="POSITION_BASE_OPTS" @update:model-value="updateField('image_position', $event)" />
          </div>
        </div>
        <div class="tgl-row">
          <button type="button" class="tgl" :class="{ on: bg.gallery_lazyload !== false }" :aria-pressed="bg.gallery_lazyload !== false" @click="updateField('gallery_lazyload', !(bg.gallery_lazyload !== false))"><b></b></button>
          <span class="tl">{{ t('Lazyload') }}</span>
        </div>
        <div class="tgl-row">
          <button type="button" class="tgl" :class="{ on: !!bg.gallery_kenburns }" :aria-pressed="!!bg.gallery_kenburns" @click="updateField('gallery_kenburns', !bg.gallery_kenburns)"><b></b></button>
          <span class="tl">{{ t('Effetto Ken Burns') }}</span>
        </div>
        <div v-if="bg.gallery_kenburns" class="row">
          <span class="rowlab">{{ t('Direzione') }}</span>
          <div class="selwrap">
            <FieldSelect ui="dropdown" :model-value="bg.gallery_kenburns_dir || 'in'" :options="KENBURNS_DIR_OPTS" @update:model-value="updateField('gallery_kenburns_dir', $event)" />
          </div>
        </div>
      </div>

      <!-- Aurora (mesh) -->
      <div v-else-if="bg.type === 'mesh'" class="type-body">
        <p class="hint">{{ t('Aurora: blob sfumati su un colore base. Colori dai ruoli del tema.') }}</p>

        <!-- Disposizione -->
        <div class="row">
          <span class="rowlab">{{ t('Disposiz.') }}</span>
          <div class="selwrap">
            <FieldSelect ui="dropdown" :model-value="bg.mesh_preset || 'spread'" :options="meshPresets" @update:model-value="updateField('mesh_preset', $event)" />
          </div>
        </div>

        <!-- Colori (palette dinamica) -->
        <div class="field">
          <span class="cl">{{ t('Colori') }}</span>
          <div v-for="(c, idx) in meshColors" :key="idx" class="mesh-color-row">
            <div class="mesh-color-field"><FieldColor :modelValue="c" @update:modelValue="setMeshColor(idx, $event)" /></div>
            <button v-if="meshColors.length > 1" type="button" class="mini-x" :title="t('Rimuovi colore')" @click="removeMeshColor(idx)">&times;</button>
          </div>
          <button v-if="meshColors.length < 5" type="button" class="btn-soft btn-soft--sm" @click="addMeshColor">+ {{ t('Aggiungi colore') }}</button>
        </div>

        <!-- Colore base -->
        <div class="field">
          <span class="cl">{{ t('Colore base') }}</span>
          <FieldColor :modelValue="bg.mesh_base || 'var(--olo-color-background)'" @update:modelValue="updateField('mesh_base', $event)" />
        </div>

        <!-- Anteprima live -->
        <div class="pv" :style="meshPreviewStyle"><span class="pv-tag">{{ t('anteprima live') }}</span></div>

        <!-- N° luci -->
        <div class="row">
          <span class="rowlab">{{ t('N° luci') }}</span>
          <input type="range" class="uirange" :style="fillStyle(meshCount, 1, 6)" :value="meshCount" :aria-label="t('Numero luci')" min="1" max="6" step="1" @input="commitInt('mesh_count', $event.target.value, meshColors.length)" />
          <div class="valbox nounit"><input type="number" :value="meshCount" min="1" max="6" step="1" @input="commitInt('mesh_count', $event.target.value, meshColors.length)" @wheel="handleNumberWheel" /></div>
        </div>
        <!-- Morbidezza -->
        <div class="row">
          <span class="rowlab">{{ t('Morbidezza') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.mesh_softness ?? 70, 0, 100)" :value="bg.mesh_softness ?? 70" :aria-label="t('Morbidezza')" min="0" max="100" step="2" @input="commitInt('mesh_softness', $event.target.value, 70)" />
          <div class="valbox"><input type="number" :value="bg.mesh_softness ?? 70" min="0" max="100" step="2" @input="commitInt('mesh_softness', $event.target.value, 70)" @wheel="handleNumberWheel" /><span class="u">%</span></div>
        </div>
        <!-- Intensità -->
        <div class="row">
          <span class="rowlab">{{ t('Intensità') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.mesh_intensity ?? 100, 0, 100)" :value="bg.mesh_intensity ?? 100" :aria-label="t('Intensità')" min="0" max="100" step="2" @input="commitInt('mesh_intensity', $event.target.value, 100)" />
          <div class="valbox"><input type="number" :value="bg.mesh_intensity ?? 100" min="0" max="100" step="2" @input="commitInt('mesh_intensity', $event.target.value, 100)" @wheel="handleNumberWheel" /><span class="u">%</span></div>
        </div>
        <!-- Animazione + velocità -->
        <div class="row tgl-inline">
          <div class="tgl-row">
            <button type="button" class="tgl" :class="{ on: !!bg.mesh_animate }" :aria-pressed="!!bg.mesh_animate" @click="updateField('mesh_animate', !bg.mesh_animate)"><b></b></button>
            <span class="tl">{{ t('Animazione') }}</span>
          </div>
          <div v-if="bg.mesh_animate" class="valbox"><input type="number" :value="bg.mesh_speed || 18" min="4" max="60" step="1" @input="commitInt('mesh_speed', $event.target.value, 18)" @wheel="handleNumberWheel" /><span class="u">s</span></div>
        </div>
      </div>

      <!-- Pattern -->
      <div v-else-if="bg.type === 'pattern'" class="type-body">
        <div class="field">
          <span class="cl">{{ t('Pattern') }}</span>
          <div class="selwrap">
            <select class="sel" :value="bg.pattern_type || 'dots'" :aria-label="t('Pattern')" @change="updateField('pattern_type', $event.target.value)">
              <optgroup v-for="group in patternGroups" :key="group.label" :label="group.label">
                <option v-for="p in group.items" :key="p.value" :value="p.value">{{ p.label }}</option>
              </optgroup>
            </select>
            <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
          </div>
        </div>
        <div class="pv" :style="patternPreviewStyle"></div>
        <div class="grid2">
          <div class="cell"><span class="cl">{{ t('Colore pattern') }}</span><FieldColor :modelValue="bg.pattern_color || '#000000'" @update:modelValue="updateField('pattern_color', $event)" /></div>
          <div class="cell"><span class="cl">{{ t('Colore sfondo') }}</span><FieldColor :modelValue="bg.pattern_bg_color || '#ffffff'" @update:modelValue="updateField('pattern_bg_color', $event)" /></div>
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Dimensione') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.pattern_size || 20, 8, 100)" :value="bg.pattern_size || 20" :aria-label="t('Dimensione')" min="8" max="100" step="1" @input="commitInt('pattern_size', $event.target.value, 20)" />
          <div class="valbox"><input type="number" :value="bg.pattern_size || 20" min="8" max="100" step="1" @input="commitInt('pattern_size', $event.target.value, 20)" @wheel="handleNumberWheel" /><span class="u">px</span></div>
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Spessore') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.pattern_thickness ?? 1, 1, 12)" :value="bg.pattern_thickness ?? 1" :aria-label="t('Spessore')" min="1" max="12" step="1" @input="commitInt('pattern_thickness', $event.target.value, 1)" />
          <div class="valbox"><input type="number" :value="bg.pattern_thickness ?? 1" min="1" max="12" step="1" @input="commitInt('pattern_thickness', $event.target.value, 1)" @wheel="handleNumberWheel" /><span class="u">px</span></div>
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Rotazione') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.pattern_rotation ?? 0, 0, 180)" :value="bg.pattern_rotation ?? 0" :aria-label="t('Rotazione')" min="0" max="180" step="5" @input="commitInt('pattern_rotation', $event.target.value, 0)" />
          <div class="valbox"><input type="number" :value="bg.pattern_rotation ?? 0" min="0" max="180" step="5" @input="commitInt('pattern_rotation', $event.target.value, 0)" @wheel="handleNumberWheel" /><span class="u">&deg;</span></div>
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Opacità') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.pattern_opacity ?? 50, 5, 100)" :value="bg.pattern_opacity ?? 50" :aria-label="t('Opacità')" min="5" max="100" step="5" @input="commitInt('pattern_opacity', $event.target.value, 50)" />
          <div class="valbox"><input type="number" :value="bg.pattern_opacity ?? 50" min="5" max="100" step="5" @input="commitInt('pattern_opacity', $event.target.value, 50)" @wheel="handleNumberWheel" /><span class="u">%</span></div>
        </div>
      </div>

      <!-- Bagliori (glow) -->
      <div v-else-if="bg.type === 'glow'" class="type-body">
        <div class="row">
          <span class="rowlab">{{ t('Disposiz.') }}</span>
          <div class="selwrap">
            <FieldSelect ui="dropdown" :model-value="bg.glow_preset || 'spread'" :options="glowPresets" @update:model-value="updateField('glow_preset', $event)" />
          </div>
        </div>
        <div class="pv" :style="glowPreviewStyle"><span class="pv-tag">{{ t('anteprima live') }}</span></div>
        <div class="field">
          <span class="cl">{{ t('Colori alone') }}</span>
          <div v-for="(c, idx) in glowColors" :key="idx" class="mesh-color-row">
            <div class="mesh-color-field"><FieldColor :modelValue="c" @update:modelValue="setGlowColor(idx, $event)" /></div>
            <button v-if="glowColors.length > 1" type="button" class="mini-x" :title="t('Rimuovi colore')" @click="removeGlowColor(idx)">&times;</button>
          </div>
          <button v-if="glowColors.length < 5" type="button" class="btn-soft btn-soft--sm" @click="addGlowColor">+ {{ t('Aggiungi colore') }}</button>
        </div>
        <div class="field">
          <span class="cl">{{ t('Colore base') }}</span>
          <FieldColor :modelValue="bg.glow_base || '#0b0d12'" @update:modelValue="updateField('glow_base', $event)" />
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Intensità') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.glow_intensity ?? 62, 10, 100)" :value="bg.glow_intensity ?? 62" :aria-label="t('Intensità')" min="10" max="100" step="2" @input="commitInt('glow_intensity', $event.target.value, 62)" />
          <div class="valbox"><input type="number" :value="bg.glow_intensity ?? 62" min="10" max="100" step="2" @input="commitInt('glow_intensity', $event.target.value, 62)" @wheel="handleNumberWheel" /><span class="u">%</span></div>
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Ampiezza') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.glow_size ?? 78, 30, 120)" :value="bg.glow_size ?? 78" :aria-label="t('Ampiezza')" min="30" max="120" step="2" @input="commitInt('glow_size', $event.target.value, 78)" />
          <div class="valbox"><input type="number" :value="bg.glow_size ?? 78" min="30" max="120" step="2" @input="commitInt('glow_size', $event.target.value, 78)" @wheel="handleNumberWheel" /><span class="u">%</span></div>
        </div>
        <div class="tgl-row">
          <button type="button" class="tgl" :class="{ on: bg.glow_grain !== false }" :aria-pressed="bg.glow_grain !== false" @click="updateField('glow_grain', !(bg.glow_grain !== false))"><b></b></button>
          <span class="tl">{{ t('Grana film') }}</span>
        </div>

        <div class="field field--sep">
          <span class="cl">{{ t('Animazione bagliore') }}</span>
          <div class="selwrap">
            <FieldSelect ui="dropdown" :model-value="bg.glow_anim || 'none'" :options="GLOW_ANIM_OPTS" @update:model-value="updateField('glow_anim', $event)" />
          </div>
        </div>
        <div v-if="(bg.glow_anim || 'none') !== 'none' && bg.glow_anim !== 'scroll'" class="row">
          <span class="rowlab">{{ t('Velocità') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.glow_anim_speed ?? 6, 1, 10)" :value="bg.glow_anim_speed ?? 6" :aria-label="t('Velocità')" min="1" max="10" step="1" @input="commitInt('glow_anim_speed', $event.target.value, 6)" />
          <div class="valbox"><input type="number" :value="bg.glow_anim_speed ?? 6" min="1" max="10" step="1" @input="commitInt('glow_anim_speed', $event.target.value, 6)" @wheel="handleNumberWheel" /></div>
        </div>
        <div v-if="['pulse','vivo'].includes(bg.glow_anim)" class="row">
          <span class="rowlab">{{ t('Respiro') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.glow_anim_intensity ?? 46, 0, 100)" :value="bg.glow_anim_intensity ?? 46" :aria-label="t('Intensità respiro')" min="0" max="100" step="2" @input="commitInt('glow_anim_intensity', $event.target.value, 46)" />
          <div class="valbox"><input type="number" :value="bg.glow_anim_intensity ?? 46" min="0" max="100" step="2" @input="commitInt('glow_anim_intensity', $event.target.value, 46)" @wheel="handleNumberWheel" /><span class="u">%</span></div>
        </div>
      </div>

      <!-- CRT (scanline + vignetta + curvatura + flicker) -->
      <div v-else-if="bg.type === 'crt'" class="type-body">
        <!-- Modello -->
        <div class="row">
          <span class="rowlab">{{ t('Modello') }}</span>
          <div class="selwrap">
            <FieldSelect ui="dropdown" :model-value="bg.crt_model || 'classic'" :options="crtModels" @update:model-value="updateField('crt_model', $event)" />
          </div>
        </div>

        <!-- Anteprima live -->
        <div class="pv" :style="crtPreviewStyle"><span class="pv-tag">{{ t('anteprima live') }}</span></div>

        <!-- Colori -->
        <div class="grid2">
          <div class="cell"><span class="cl">{{ t('Colore linee') }}</span><FieldColor :modelValue="bg.crt_line_color || '#ffffff'" @update:modelValue="updateField('crt_line_color', $event)" /></div>
          <div class="cell"><span class="cl">{{ t('Colore base') }}</span><FieldColor :modelValue="bg.crt_base || 'var(--olo-color-background)'" @update:modelValue="updateField('crt_base', $event)" /></div>
        </div>

        <!-- Scanline -->
        <div class="row">
          <span class="rowlab">{{ t('Scanline') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.crt_scanline_opacity ?? 50, 0, 100)" :value="bg.crt_scanline_opacity ?? 50" :aria-label="t('Intensità scanline')" min="0" max="100" step="5" @input="commitInt('crt_scanline_opacity', $event.target.value, 50)" />
          <div class="valbox"><input type="number" :value="bg.crt_scanline_opacity ?? 50" min="0" max="100" step="5" @input="commitInt('crt_scanline_opacity', $event.target.value, 50)" @wheel="handleNumberWheel" /><span class="u">%</span></div>
        </div>
        <!-- Passo -->
        <div class="row">
          <span class="rowlab">{{ t('Passo') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.crt_scanline_gap ?? 3, 2, 12)" :value="bg.crt_scanline_gap ?? 3" :aria-label="t('Passo scanline')" min="2" max="12" step="1" @input="commitInt('crt_scanline_gap', $event.target.value, 3)" />
          <div class="valbox"><input type="number" :value="bg.crt_scanline_gap ?? 3" min="2" max="12" step="1" @input="commitInt('crt_scanline_gap', $event.target.value, 3)" @wheel="handleNumberWheel" /><span class="u">px</span></div>
        </div>
        <!-- Curvatura -->
        <div class="row">
          <span class="rowlab">{{ t('Curvatura') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.crt_curvature ?? 0, 0, 100)" :value="bg.crt_curvature ?? 0" :aria-label="t('Curvatura')" min="0" max="100" step="5" @input="commitInt('crt_curvature', $event.target.value, 0)" />
          <div class="valbox"><input type="number" :value="bg.crt_curvature ?? 0" min="0" max="100" step="5" @input="commitInt('crt_curvature', $event.target.value, 0)" @wheel="handleNumberWheel" /><span class="u">%</span></div>
        </div>
        <!-- Vignetta -->
        <div class="row">
          <span class="rowlab">{{ t('Vignetta') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.crt_vignette ?? 55, 0, 100)" :value="bg.crt_vignette ?? 55" :aria-label="t('Vignetta')" min="0" max="100" step="5" @input="commitInt('crt_vignette', $event.target.value, 55)" />
          <div class="valbox"><input type="number" :value="bg.crt_vignette ?? 55" min="0" max="100" step="5" @input="commitInt('crt_vignette', $event.target.value, 55)" @wheel="handleNumberWheel" /><span class="u">%</span></div>
        </div>
        <!-- Flicker + velocità -->
        <div class="row tgl-inline">
          <div class="tgl-row">
            <button type="button" class="tgl" :class="{ on: !!bg.crt_flicker }" :aria-pressed="!!bg.crt_flicker" @click="updateField('crt_flicker', !bg.crt_flicker)"><b></b></button>
            <span class="tl">{{ t('Flicker') }}</span>
          </div>
          <div v-if="bg.crt_flicker" class="valbox"><input type="number" :value="bg.crt_flicker_speed ?? 6" min="2" max="12" step="1" @input="commitInt('crt_flicker_speed', $event.target.value, 6)" @wheel="handleNumberWheel" /><span class="u">s</span></div>
        </div>
      </div>

      <!-- ───────── SOVRAPPOSIZIONE — sotto-sezione con occhio ───────── -->
      <div class="subhead">
        <span class="t2">{{ t('Sovrapposizione') }}</span>
        <button
          type="button"
          class="eyeb"
          :class="{ on: showOverlay }"
          :aria-pressed="showOverlay"
          :title="t('Mostra/nascondi sovrapposizione')"
          @click="showOverlay = !showOverlay"
        >
          <svg v-if="showOverlay" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
          <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 8 10 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/><path d="M6.61 6.61A18.5 18.5 0 0 0 2 12s3 8 10 8a9.12 9.12 0 0 0 5.39-1.61"/></svg>
        </button>
      </div>
      <div v-if="showOverlay" class="type-body">
        <div class="field">
          <span class="cl">{{ t('Colore') }}</span>
          <FieldColor :modelValue="bg.overlay_color || '#000000'" @update:modelValue="updateField('overlay_color', $event)" />
        </div>
        <div class="row">
          <span class="rowlab">{{ t('Opacità') }}</span>
          <input type="range" class="uirange" :style="fillStyle(bg.overlay_opacity || 0, 0, 100)" :value="bg.overlay_opacity || 0" :aria-label="t('Opacità sovrapposizione')" min="0" max="100" step="5" @input="commitInt('overlay_opacity', $event.target.value, 0)" />
          <div class="valbox"><input type="number" :value="bg.overlay_opacity || 0" min="0" max="100" step="5" @input="commitInt('overlay_opacity', $event.target.value, 0)" @wheel="handleNumberWheel" /><span class="u">%</span></div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { t } from '@/i18n';
import { useMediaPicker } from '@/composables/useMediaPicker';
import { patternList, getPatternCSS } from '@/utils/patternCSS';
import { getGlowCSS, getGlowColors, glowPresets } from '@/utils/glowCSS';
import { getMeshCSS, getMeshColors, meshPresets } from '@/utils/meshCSS';
import { getCrtCSS, crtModels } from '@/utils/crtCSS';
import { handleNumberWheel } from '@/utils/numberInputWheel';
import ParallaxEditor from './ParallaxEditor.vue';
import FieldGradient from './fields/FieldGradient.vue';
import FieldColor from './fields/FieldColor.vue';
import FieldSelect from './fields/FieldSelect.vue';

// Opzioni dei FieldSelect (label RAW: t() la applica FieldSelect internamente).
// meshPresets / glowPresets / crtModels sono già array { value, label } e si
// passano direttamente a :options.
const SIZE_OPTS = [
  { value: 'cover', label: 'Cover' },
  { value: 'contain', label: 'Contain' },
  { value: 'auto', label: 'Auto' },
];

const POSITION_OPTS = [
  { value: 'center center', label: 'Centro' },
  { value: 'top center', label: 'Alto' },
  { value: 'bottom center', label: 'Basso' },
  { value: 'left center', label: 'Sinistra' },
  { value: 'right center', label: 'Destra' },
  { value: 'top left', label: 'Alto sinistra' },
  { value: 'top right', label: 'Alto destra' },
  { value: 'bottom left', label: 'Basso sinistra' },
  { value: 'bottom right', label: 'Basso destra' },
];

// Galleria: solo le 5 posizioni base (come il select originale)
const POSITION_BASE_OPTS = POSITION_OPTS.slice(0, 5);

const VIDEO_FIT_OPTS = [
  { value: 'cover', label: 'Cover' },
  { value: 'contain', label: 'Contain' },
  { value: 'fill', label: 'Riempi' },
  { value: 'none', label: 'Nessuno (dimensione originale)' },
];

const GALLERY_TRANSITION_OPTS = [
  { value: 'fade', label: 'Fade' },
  { value: 'crossfade', label: 'Crossfade' },
  { value: 'slide', label: 'Slide' },
  { value: 'slide-up', label: 'Slide Up' },
  { value: 'zoom', label: 'Zoom' },
  { value: 'blur', label: 'Blur' },
  { value: 'flip', label: 'Flip' },
  { value: 'none', label: 'Nessuna' },
];

const KENBURNS_DIR_OPTS = [
  { value: 'in', label: 'Dentro (Zoom in)' },
  { value: 'out', label: 'Fuori (Zoom out)' },
  { value: 'alternate', label: 'Alternato' },
];

const GLOW_ANIM_OPTS = [
  { value: 'none', label: 'Nessuna (statico)' },
  { value: 'pulse', label: 'Pulsazione (respiro)' },
  { value: 'drift', label: 'Deriva (movimento lento)' },
  { value: 'wander', label: 'Vagare (respiro + deriva)' },
  { value: 'flicker', label: 'Sfarfallio (energia neon)' },
  { value: 'vivo', label: 'Vivo (respiro + orbita) ✦' },
  { value: 'tempesta', label: 'Tempesta (sfarfallio + ondeggio) ✦' },
  { value: 'scroll', label: 'Reattivo allo scroll' },
];

const bgParallaxProperties = [
  { key: 'bgx', label: 'Spostamento X', min: -800, max: 800, step: 10, unit: 'px' },
  { key: 'bgy', label: 'Spostamento Y', min: -800, max: 800, step: 10, unit: 'px' },
  { key: 'scale', label: 'Scala', min: 0.5, max: 2, step: 0.05, unit: '' },
  { key: 'opacity', label: 'Opacità', min: 0, max: 1, step: 0.05, unit: '' },
  { key: 'blur', label: 'Sfocatura', min: 0, max: 20, step: 1, unit: 'px' },
];
// Per lo sfondo VIDEO bgx/bgy (background-position) non hanno effetto su un <video>:
// lo spostamento si fa con la TRASLAZIONE (x/y), che il mapper uk-parallax applica come
// transform sul <video>. Esponiamo: Spostamento X/Y + Scala + Opacità + Sfocatura.
const videoParallaxProperties = [
  { key: 'x', label: 'Spostamento X', min: -800, max: 800, step: 10, unit: 'px' },
  { key: 'y', label: 'Spostamento Y', min: -800, max: 800, step: 10, unit: 'px' },
  ...bgParallaxProperties.filter(
    (p) => p.key === 'scale' || p.key === 'opacity' || p.key === 'blur'
  ),
];

const props = defineProps({
  modelValue: { type: Object, default: () => ({}) },
  showParallax: { type: Boolean, default: true },
});

const emit = defineEmits(['update:modelValue']);

const { openSingleImage, openGallery } = useMediaPicker();

const defaultBg = {
  type: 'none',
  color: '#ffffff',
  gradient_angle: 180,
  gradient_from: '#ffffff',
  gradient_to: '#000000',
  image_url: '',
  image_size: 'cover',
  image_position: 'center center',
  video_url: '',
  video_poster: '',
  video_fit: 'cover',
  parallax: false,
  parallax_speed: 0.3,
  parallax_bgy: -200,
  parallax_bgx: 0,
  parallax_opacity: false,
  parallax_opacity_start: 0.3,
  parallax_opacity_end: 1,
  parallax_scale: false,
  parallax_scale_start: 1,
  parallax_scale_end: 1.2,
  parallax_blur: false,
  parallax_blur_start: 5,
  parallax_blur_end: 0,
  parallax_nomobile: true,
  cover_height: 0,
  video_scale: 100,
  mesh_c1: 'var(--olo-color-primary)',
  mesh_c2: 'var(--olo-color-secondary)',
  mesh_c3: 'var(--olo-color-accent)',
  mesh_base: 'var(--olo-color-background)',
  mesh_animate: false,
  mesh_speed: 18,
  mesh_preset: 'spread',
  mesh_softness: 70,
  mesh_intensity: 100,
  glow_base: '#0b0d12',
  glow_color: 'var(--olo-color-primary)',
  glow_color2: '',
  glow_preset: 'spread',
  glow_intensity: 62,
  glow_size: 78,
  glow_grain: true,
  glow_anim: 'none',
  glow_anim_speed: 6,
  glow_anim_intensity: 46,
  overlay_color: '#000000',
  overlay_opacity: 0,
  color_opacity: 100,
  gallery_images: [],
  gallery_loop: true,
  gallery_duration: 5000,
  gallery_transition: 'fade',
  gallery_transition_ms: 500,
  gallery_lazyload: true,
  gallery_kenburns: true,
  gallery_kenburns_dir: 'in',
};

const bg = computed(() => ({ ...defaultBg, ...props.modelValue }));

// Tipi raggruppati per la griglia di swatch. `prev` = classe CSS della
// mini-anteprima; i tipi "media" hanno un'icona interna (image/video/gallery).
const typeGroups = [
  { cat: 'Colore', items: [
    { value: 'none',     label: 'Nessuno',      prev: 'p-none' },
    { value: 'solid',    label: 'Tinta unita',  prev: 'p-solid' },
    { value: 'gradient', label: 'Gradiente',    prev: 'p-grad' },
  ] },
  { cat: 'Generativi', items: [
    { value: 'mesh',    label: 'Aurora',        prev: 'p-aurora' },
    { value: 'glow',    label: 'Bagliori',      prev: 'p-glow' },
    { value: 'pattern', label: 'Pattern',       prev: 'p-pattern' },
    { value: 'crt',     label: 'CRT scanline',  prev: 'p-crt' },
  ] },
  { cat: 'Media', items: [
    { value: 'image',   label: 'Immagine',      prev: 'p-img' },
    { value: 'video',   label: 'Video',         prev: 'p-video' },
    { value: 'gallery', label: 'Galleria',      prev: 'p-gallery' },
  ] },
];
const allTypes = typeGroups.flatMap((g) => g.items);
const currentTypeLabel = computed(() => (allTypes.find((it) => it.value === bg.value.type) || {}).label || '');

// Pattern groups for select optgroup
const patternGroups = [
  { label: 'Linee', items: patternList.filter(p => ['horizontal-lines','vertical-lines','diagonal-lines','diagonal-lines-reverse','crosshatch','diagonal-crosshatch'].includes(p.value)) },
  { label: 'Punti', items: patternList.filter(p => ['dots','dots-large','dots-grid','polka-dots'].includes(p.value)) },
  { label: 'Geometrici', items: patternList.filter(p => ['checkerboard','triangles','diamonds','hexagons','zigzag','chevrons','herringbone'].includes(p.value)) },
  { label: 'Onde & Organici', items: patternList.filter(p => ['waves','wavy-lines','scales','circles','concentric-circles'].includes(p.value)) },
  { label: 'Texture', items: patternList.filter(p => ['carbon-fiber','graph-paper','lined-paper','blueprint','noise','brick','wood-grain'].includes(p.value)) },
  { label: 'Decorativi', items: patternList.filter(p => ['stars','crosses','plus-signs','hearts'].includes(p.value)) },
];

// Mesh/aurora preview: stesso util getMeshCSS della resa canvas/PHP (WYSIWYG).
const meshPreviewStyle = computed(() => bg.value.type === 'mesh' ? getMeshCSS(bg.value) : {});

// Palette colori effettiva (mesh_colors[] o legacy c1/c2/c3) e n° luci.
const meshColors = computed(() => getMeshColors(bg.value));
const meshCount = computed(() => {
  const n = parseInt(bg.value.mesh_count ?? meshColors.value.length);
  return Math.max(1, Math.min(6, Number.isNaN(n) ? meshColors.value.length : n));
});

// Persiste mesh_colors[] e rispecchia i primi 3 in mesh_c1/c2/c3 (retrocompat).
function writeMeshColors(arr) {
  emit('update:modelValue', {
    ...bg.value,
    mesh_colors: arr,
    mesh_c1: arr[0] || 'var(--olo-color-primary)',
    mesh_c2: arr[1] || 'var(--olo-color-secondary)',
    mesh_c3: arr[2] || 'var(--olo-color-accent)',
  });
}
function setMeshColor(idx, val) {
  const a = [...meshColors.value];
  a[idx] = val;
  writeMeshColors(a);
}
function addMeshColor() {
  const a = [...meshColors.value];
  if (a.length < 5) { a.push('var(--olo-color-primary)'); writeMeshColors(a); }
}
function removeMeshColor(idx) {
  const a = [...meshColors.value];
  a.splice(idx, 1);
  if (!a.length) a.push('var(--olo-color-primary)');
  writeMeshColors(a);
}

// Glow/Bagliori preview: usa lo stesso util getGlowCSS della resa canvas/PHP.
const glowPreviewStyle = computed(() => bg.value.type === 'glow' ? getGlowCSS(bg.value) : {});

// Palette aloni dinamica (stessa gestione dell'Aurora). Persiste glow_colors[] e
// rispecchia i primi 2 in glow_color/glow_color2 (retrocompat).
const glowColors = computed(() => getGlowColors(bg.value));
function writeGlowColors(arr) {
  emit('update:modelValue', {
    ...bg.value,
    glow_colors: arr,
    glow_color: arr[0] || 'var(--olo-color-primary)',
    glow_color2: arr[1] || '',
  });
}
function setGlowColor(idx, val) {
  const a = [...glowColors.value];
  a[idx] = val;
  writeGlowColors(a);
}
function addGlowColor() {
  const a = [...glowColors.value];
  if (a.length < 5) { a.push('var(--olo-color-primary)'); writeGlowColors(a); }
}
function removeGlowColor(idx) {
  const a = [...glowColors.value];
  a.splice(idx, 1);
  if (!a.length) a.push('var(--olo-color-primary)');
  writeGlowColors(a);
}

const patternPreviewStyle = computed(() => {
  const b = bg.value;
  if (b.type !== 'pattern') return {};
  return getPatternCSS(
    b.pattern_type || 'dots',
    b.pattern_color || '#000000',
    b.pattern_bg_color || '#ffffff',
    b.pattern_size || 20,
    (b.pattern_opacity ?? 50) / 100,
    b.pattern_thickness,
    b.pattern_rotation
  );
});

// CRT preview: stesso util getCrtCSS della resa canvas/PHP (WYSIWYG).
const crtPreviewStyle = computed(() => bg.value.type === 'crt' ? getCrtCSS(bg.value) : {});

// v1.0.77 — preview usa direttamente bg.color (può essere #hex, rgba(...) o var(--olo-color-*)),
// l'Alfa è già parte del valore emesso da FieldColor. color_opacity è legacy no-op.
const solidPreview = computed(() => bg.value.color || '#ffffff');

// Gradient multi-stop model (backward-compat with old gradient_from/gradient_to)
const gradientModel = computed(() => {
  const b = bg.value;
  // New format: bg.gradient object with { type, angle, stops }
  if (b.gradient && typeof b.gradient === 'object' && Array.isArray(b.gradient.stops)) {
    return b.gradient;
  }
  // Migrate from old 2-color format
  return {
    type: 'linear',
    angle: parseInt(b.gradient_angle) || 180,
    stops: [
      { color: b.gradient_from || '#ffffff', position: 0 },
      { color: b.gradient_to || '#000000', position: 100 },
    ],
  };
});

function onGradientUpdate(newGrad) {
  // Save as new gradient object + keep old keys for backward compat
  const stops = newGrad.stops || [];
  emit('update:modelValue', {
    ...bg.value,
    gradient: newGrad,
    gradient_angle: newGrad.angle,
    gradient_from: stops[0]?.color || '#ffffff',
    gradient_to: stops[stops.length - 1]?.color || '#000000',
  });
}

function updateField(key, value) {
  emit('update:modelValue', { ...bg.value, [key]: value });
}

// Commit numerico robusto (slider + valbox): scarta NaN tornando al default,
// così svuotare il campo non corrompe la chiave salvata.
function commitInt(key, raw, def = 0) {
  const n = parseInt(raw, 10);
  updateField(key, Number.isNaN(n) ? def : n);
}

// Stile di riempimento "arancio fino al valore" per gli slider nativi (WebKit).
// Firefox usa ::-moz-range-progress (vedi <style>). var(--ui)=accento chrome.
function fillStyle(value, min, max) {
  const lo = Number(min);
  const hi = Number(max);
  const val = Number(value);
  const pct = hi > lo ? Math.max(0, Math.min(100, ((val - lo) / (hi - lo)) * 100)) : 0;
  return { background: `linear-gradient(to right, var(--ui) ${pct}%, var(--track) ${pct}%)` };
}

// Sovrapposizione: disclosure locale (occhio). Aperta se esiste già un overlay.
const showOverlay = ref(((props.modelValue || {}).overlay_opacity ?? 0) > 0);

function pickBgImage() {
  openSingleImage(({ url }) => {
    updateField('image_url', url);
  });
}

function pickGalleryImages() {
  openGallery((images) => {
    updateField('gallery_images', images);
  });
}

function removeGalleryImage(idx) {
  const imgs = [...(bg.value.gallery_images || [])];
  imgs.splice(idx, 1);
  updateField('gallery_images', imgs);
}

function removeVideo() {
  emit('update:modelValue', { ...bg.value, video_url: '', video_poster: '' });
}

function pickBgVideo() {
  const frame = wp.media({ library: { type: 'video' }, multiple: false });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    updateField('video_url', url);
  });
  frame.open();
}

function pickBgPoster() {
  openSingleImage(({ url }) => {
    updateField('video_poster', url);
  });
}

// Whether parallax is enabled (supports both old boolean and new object format)
const isParallaxEnabled = computed(() => {
  const p = bg.value.parallax;
  return p === true || (typeof p === 'object' && p !== null);
});

// Migrate old flat parallax format to new multi-stop object
const bgParallaxData = computed(() => {
  const b = bg.value;
  // Already new format
  if (typeof b.parallax === 'object' && b.parallax !== null) {
    return b.parallax;
  }
  // Migrate from old flat keys
  const obj = { bgx: [], bgy: [], scale: [], opacity: [], blur: [], nomobile: true, easing: null, start: '', end: '' };
  if (b.parallax === true) {
    const bgy = b.parallax_bgy ?? -200;
    if (bgy !== 0) {
      obj.bgy = [{ value: 0, position: 0 }, { value: parseInt(bgy), position: 100 }];
    }
    const bgx = b.parallax_bgx ?? 0;
    if (bgx !== 0) {
      obj.bgx = [{ value: 0, position: 0 }, { value: parseInt(bgx), position: 100 }];
    }
    if (b.parallax_opacity) {
      obj.opacity = [
        { value: parseFloat(b.parallax_opacity_start ?? 0.3), position: 0 },
        { value: parseFloat(b.parallax_opacity_end ?? 1), position: 100 },
      ];
    }
    if (b.parallax_scale) {
      obj.scale = [
        { value: parseFloat(b.parallax_scale_start ?? 1), position: 0 },
        { value: parseFloat(b.parallax_scale_end ?? 1.2), position: 100 },
      ];
    }
    if (b.parallax_blur) {
      obj.blur = [
        { value: parseInt(b.parallax_blur_start ?? 5), position: 0 },
        { value: parseInt(b.parallax_blur_end ?? 0), position: 100 },
      ];
    }
    obj.nomobile = b.parallax_nomobile !== false;
  }
  return obj;
});

function toggleParallax() {
  if (isParallaxEnabled.value) {
    updateField('parallax', false);
  } else {
    // Enable with default bgy
    updateField('parallax', {
      bgx: [], bgy: [{ value: -200, position: 0 }, { value: 0, position: 100 }],
      scale: [], opacity: [], blur: [],
      nomobile: true, easing: null, start: '', end: '',
    });
  }
}

function updateParallaxData(newData) {
  updateField('parallax', newData);
}
</script>

<style scoped>
/* ════════════════════════════════════════════════════════════════════
   Pannello Sfondo — tema CHIARO coerente (handoff "Redesign pannello Sfondo").
   Card autosufficiente: leggibile sia nell'inspector chiaro (tile/sezione)
   sia nel PageSettingsPanel scuro. Accento = arancio CHROME (--olo-ui-accent),
   contenuti = token cliente (FieldColor). Vedi commento nel <template>.
   ════════════════════════════════════════════════════════════════════ */
.olo-bg2 {
  --ui: var(--olo-ui-accent, #e8622a);
  --ui-soft: #fdeee2;
  --navy: #16263d;
  --ink: #1f2937;
  --mute: #6b7280;
  --faint: #94a3b8;
  --line: #e5e7eb;
  --surface-alt: #f6f7f9;
  --track: #e5e7eb;
  --mono: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, monospace;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 12px;
  color: var(--ink);
}

/* sotto-intestazioni di sezione */
.subhead {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin: 0 0 11px;
  padding-top: 15px;
  border-top: 1px solid #f0f1f4;
}
.subhead.first { padding-top: 0; border-top: 0; }
.subhead .t2 {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  color: var(--navy);
}
.subhead .t2 .q { color: var(--ui); }

/* occhio (disclosure sovrapposizione) */
.eyeb {
  width: 28px;
  height: 26px;
  border: 1px solid var(--line);
  background: #fff;
  border-radius: 7px;
  color: var(--faint);
  display: grid;
  place-items: center;
  cursor: pointer;
  flex-shrink: 0;
  transition: color 0.12s, background 0.12s, border-color 0.12s;
}
.eyeb:hover { color: var(--mute); }
.eyeb.on { color: var(--navy); background: var(--surface-alt); }
.eyeb svg { width: 15px; height: 15px; }
.eyeb:focus-visible {
  outline: none;
  border-color: var(--ui);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--ui) 20%, transparent);
}

/* ───────── griglia tipi ───────── */
.bgtypes {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
}
.cat {
  grid-column: 1 / -1;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--faint);
  margin: 10px 0 1px;
  display: flex;
  align-items: center;
  gap: 9px;
}
.cat::after { content: ""; flex: 1; height: 1px; background: #f0f1f4; }
.cat.first { margin-top: 0; }

.bt {
  appearance: none;
  border: 1px solid var(--line);
  background: #fff;
  border-radius: 10px;
  padding: 5px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  gap: 5px;
  position: relative;
  transition: border-color 0.12s, box-shadow 0.12s;
}
.bt:hover { border-color: #d7dbe0; }
.bt.on { border-color: var(--ui); box-shadow: 0 0 0 2px var(--ui-soft); }
.bt:focus-visible {
  outline: none;
  border-color: var(--ui);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--ui) 22%, transparent);
}
.bt .sw-prev {
  height: 40px;
  border-radius: 6px;
  border: 1px solid rgba(15, 23, 42, 0.07);
  overflow: hidden;
  position: relative;
  display: grid;
  place-items: center;
}
.bt .bl {
  font-size: 10px;
  font-weight: 600;
  color: var(--mute);
  text-align: center;
  line-height: 1.05;
  letter-spacing: 0.01em;
}
.bt.on .bl { color: var(--navy); font-weight: 700; }
.bt .ck {
  position: absolute;
  top: -7px;
  right: -7px;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: var(--ui);
  color: #fff;
  display: grid;
  place-items: center;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.28);
  z-index: 2;
}
.bt .ck svg { width: 11px; height: 11px; }

/* mini-anteprime dei tipi */
.p-none { background: repeating-linear-gradient(45deg, #eef0f3 0 5px, #f9fafb 5px 10px); }
.p-none::after { content: ""; position: absolute; width: 140%; height: 1.5px; background: #cdd2d9; transform: rotate(-30deg); }
.p-solid { background: var(--olo-color-primary, #e1474f); }
.p-grad { background: linear-gradient(118deg, var(--olo-color-primary, #e1474f) 0%, var(--olo-color-accent, #f4a23b) 100%); }
.p-aurora {
  background:
    radial-gradient(circle at 18% 28%, color-mix(in srgb, var(--olo-color-primary, #e1474f) 85%, transparent), transparent 48%),
    radial-gradient(circle at 82% 22%, rgba(99, 102, 241, 0.8), transparent 46%),
    radial-gradient(circle at 62% 88%, color-mix(in srgb, var(--olo-color-accent, #f4a23b) 85%, transparent), transparent 50%),
    var(--navy);
}
.p-glow {
  background:
    radial-gradient(120% 150% at 50% 132%, var(--olo-color-primary, #e1474f) 0%, color-mix(in srgb, var(--olo-color-primary, #e1474f) 28%, transparent) 30%, transparent 60%),
    #0b0d12;
}
.p-pattern { background: radial-gradient(var(--olo-color-primary, #e1474f) 1.5px, transparent 1.7px) 0 0 / 9px 9px, #fff; }
.p-crt { background: repeating-linear-gradient(0deg, #06140f 0 2px, rgba(56, 209, 127, 0.32) 2px 3px), #06140f; }
.p-img { background: repeating-linear-gradient(45deg, #e7ebf0 0 8px, #dde3ea 8px 16px); }
.p-video { background: var(--navy); }
.p-gallery { background: #eef1f5; }

.ico { position: relative; z-index: 1; display: grid; place-items: center; }
.ico svg { width: 18px; height: 18px; color: #9aa6b4; }
.p-video .play {
  width: 0;
  height: 0;
  border-style: solid;
  border-width: 7px 0 7px 12px;
  border-color: transparent transparent transparent #fff;
  margin-left: 2px;
  opacity: 0.92;
}
.p-gallery .stack { position: relative; width: 30px; height: 24px; }
.p-gallery .stack i { position: absolute; width: 20px; height: 16px; border-radius: 3px; border: 1px solid #fff; }
.p-gallery .stack i:nth-child(1) { left: 0; top: 5px; background: var(--olo-color-accent, #f4a23b); transform: rotate(-8deg); }
.p-gallery .stack i:nth-child(2) { left: 5px; top: 2px; background: #6366f1; transform: rotate(4deg); }
.p-gallery .stack i:nth-child(3) { left: 9px; top: 6px; background: var(--olo-color-primary, #e1474f); transform: rotate(-2deg); }

/* ───────── corpo controlli per-tipo ───────── */
.type-body { display: flex; flex-direction: column; gap: 10px; }

.field { display: flex; flex-direction: column; gap: 6px; }
.field--sep { border-top: 1px solid #f0f1f4; padding-top: 10px; }
.sub-editor { margin-top: 4px; }
.cl {
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--faint);
}
.hint { font-size: 10px; color: var(--mute); line-height: 1.45; margin: 0; }

.row { display: flex; align-items: center; gap: 10px; }
.rowlab {
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--faint);
  width: 64px;
  flex-shrink: 0;
}
.spacer { flex: 1; }

/* select custom */
.selwrap { flex: 1; position: relative; min-width: 0; }
.sel {
  width: 100%;
  height: 34px;
  padding: 0 30px 0 10px;
  border: 1px solid var(--line);
  border-radius: 9px;
  background: #fff;
  font-size: 13px;
  font-weight: 500;
  color: var(--ink);
  outline: none;
  appearance: none;
  -webkit-appearance: none;
  cursor: pointer;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.sel:focus-visible {
  border-color: var(--ui);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--ui) 20%, transparent);
}
.chev {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  width: 14px;
  height: 14px;
  color: var(--faint);
  pointer-events: none;
}

/* slider — track con riempimento arancio (WebKit via :style; FF via progress) */
.uirange {
  flex: 1;
  min-width: 40px;
  -webkit-appearance: none;
  appearance: none;
  height: 6px;
  border-radius: 99px;
  background: var(--track);
  outline: none;
  cursor: pointer;
}
.uirange::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 15px;
  height: 15px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid var(--ui);
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.18);
  cursor: pointer;
}
.uirange::-moz-range-thumb {
  width: 15px;
  height: 15px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid var(--ui);
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.18);
  cursor: pointer;
}
.uirange::-moz-range-track { height: 6px; border-radius: 99px; background: var(--track); }
.uirange::-moz-range-progress { height: 6px; border-radius: 99px; background: var(--ui); }
.uirange:focus-visible { box-shadow: 0 0 0 3px color-mix(in srgb, var(--ui) 25%, transparent); }

/* valbox numerico con unità */
.valbox {
  display: flex;
  align-items: center;
  border: 1px solid var(--line);
  border-radius: 9px;
  overflow: hidden;
  background: #fff;
  height: 34px;
  width: 74px;
  flex-shrink: 0;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.valbox--wide { width: 96px; }
.valbox:focus-within {
  border-color: var(--ui);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--ui) 18%, transparent);
}
.valbox input {
  width: 100%;
  min-width: 0;
  border: 0;
  outline: none;
  text-align: center;
  font: 500 13px var(--mono);
  color: var(--ink);
  background: transparent;
  -moz-appearance: textfield;
}
.valbox input::-webkit-inner-spin-button,
.valbox input::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
.valbox .u {
  font-size: 11px;
  color: var(--faint);
  font-weight: 600;
  padding: 0 8px;
  border-left: 1px solid #eef0f3;
  align-self: stretch;
  display: flex;
  align-items: center;
  background: var(--surface-alt);
}

/* griglia 2 colonne (colori affiancati) */
.grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.cell { display: flex; flex-direction: column; gap: 6px; }

/* Aurora: palette dinamica + valbox senza unità (N° luci) */
.valbox.nounit { width: 54px; }
.row.tgl-inline { justify-content: space-between; }
.mesh-color-row { display: flex; align-items: flex-start; gap: 6px; }
.mesh-color-field { flex: 1; min-width: 0; }
.mini-x {
  flex: none;
  width: 26px;
  height: 30px;
  margin-top: 2px;
  border: 1px solid var(--line);
  border-radius: 8px;
  background: #fff;
  color: var(--faint);
  font-size: 15px;
  line-height: 1;
  cursor: pointer;
  display: grid;
  place-items: center;
  transition: color 0.12s, border-color 0.12s;
}
.mini-x:hover { color: #e11d48; border-color: #f0c2c8; }
.btn-soft--sm { height: 28px; font-size: 11px; }

/* toggle */
.tgl-row { display: flex; align-items: center; gap: 10px; }
.tgl {
  position: relative;
  width: 38px;
  height: 20px;
  border-radius: 99px;
  background: #d7dbe0;
  border: 0;
  cursor: pointer;
  flex: none;
  padding: 0;
  transition: background 0.15s;
}
.tgl.on { background: var(--ui); }
.tgl b {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
  transition: left 0.15s;
}
.tgl.on b { left: 20px; }
.tgl:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--ui) 25%, transparent); }
.tl { font-size: 12px; color: var(--ink); }

/* anteprima live (glow/aurora) */
.pv {
  height: 72px;
  border-radius: 10px;
  border: 1px solid #eef0f3;
  position: relative;
  overflow: hidden;
}
.pv-tag {
  position: absolute;
  left: 9px;
  bottom: 7px;
  font: 600 9px inherit;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.62);
  pointer-events: none;
}

/* pulsanti soft (picker media) */
.btn-soft {
  width: 100%;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  border: 1px solid var(--line);
  background: var(--surface-alt);
  border-radius: 9px;
  font-size: 12px;
  font-weight: 600;
  color: var(--ink);
  cursor: pointer;
  transition: background 0.12s, border-color 0.12s;
}
.btn-soft:hover { background: #eef0f3; border-color: #d7dbe0; }
.btn-soft:focus-visible {
  outline: none;
  border-color: var(--ui);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--ui) 20%, transparent);
}

/* thumbnail immagine / video */
.thumb { position: relative; border-radius: 9px; overflow: hidden; border: 1px solid var(--line); }
.thumb img { display: block; width: 100%; height: 80px; object-fit: cover; }
.thumb-video { width: 100%; height: 80px; background: var(--navy); display: flex; align-items: center; justify-content: center; }
.thumb-video img { height: 80px; }
.thumb-vico svg { width: 26px; height: 26px; color: rgba(255, 255, 255, 0.7); }
.thumb-x {
  position: absolute;
  top: 6px;
  right: 6px;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  border: 0;
  background: rgba(17, 24, 39, 0.72);
  color: #fff;
  font-size: 15px;
  line-height: 1;
  cursor: pointer;
  display: grid;
  place-items: center;
}
.thumb-x:hover { background: #e11d48; }

/* galleria */
.gallery-head { display: flex; align-items: center; justify-content: space-between; }
.gcount { font-size: 10px; color: var(--faint); font-weight: 600; }
.gclear { border: 0; background: transparent; color: var(--faint); cursor: pointer; display: grid; place-items: center; padding: 2px; }
.gclear:hover { color: #e11d48; }
.gclear svg { width: 15px; height: 15px; }
.gthumbs { display: flex; flex-wrap: wrap; gap: 5px; }
.gthumb { position: relative; }
.gthumb img { width: 54px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid var(--line); display: block; }
.gthumb-x {
  position: absolute;
  top: -5px;
  right: -5px;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 0;
  background: #e11d48;
  color: #fff;
  font-size: 11px;
  line-height: 1;
  cursor: pointer;
  display: grid;
  place-items: center;
}
</style>
