<template>
  <div class="mb-bg-gray-900 mb-border-l mb-border-gray-700 mb-overflow-y-auto mb-flex mb-flex-col mb-h-full">
    <template v-if="layer">
      <!-- Header -->
      <div class="mb-p-2 mb-border-b mb-border-gray-700 mb-text-xs mb-font-semibold mb-text-gray-300">
        {{ t('Livello') }} {{ nomeTipo }}
        <!-- Il nome del livello (1.4.1352): quello che compare nel pannello Livelli. Vuoto = il testo, il file o il tipo. -->
        <div class="mb-mt-1.5 mb-font-normal">
          <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Nome del livello') }}<InfoTip :testo="t('Serve solo a te, per riconoscere il livello nel pannello Livelli: sul sito non si vede. Vuoto, il livello prende il nome dal suo testo o dal file.')" :titolo="t('Nome del livello')" /></label>
          <input :value="layer.name || ''" @input="up('name', $event.target.value)" class="mps-input" :placeholder="nomeSuggerito" />
        </div>
        <!-- Il gruppo del pannello Livelli (1.4.1424): vuoto = quello che fa il livello (una scelta, i veli, i testi…) -->
        <div class="mb-mt-1.5 mb-font-normal">
          <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Gruppo') }}<InfoTip :testo="t('Il gruppo in cui il livello sta nel pannello Livelli. Vuoto, va da solo nel gruppo di quello che fa: una scelta, i veli, i testi e i pulsanti… Serve solo a te: sul sito non si vede.')" :titolo="t('Gruppo')" /></label>
          <input :value="layer.gruppo || ''" @input="up('gruppo', $event.target.value)" class="mps-input" :placeholder="gruppoAutomatico" list="mps-nomi-gruppi" />
          <datalist id="mps-nomi-gruppi">
            <option v-for="n in nomiGruppi" :key="n" :value="n" />
          </datalist>
        </div>
      </div>

      <!-- Tabs -->
      <div v-if="tabs.length > 1" class="mb-flex mb-bg-gray-800 mb-border-b mb-border-gray-700">
        <button
          v-for="tab in tabs"
          :key="tab"
          @click="activeTab = tab"
          :class="[
            'mb-flex-1 mb-py-1.5 mb-text-[10px] mb-font-medium mb-transition-colors',
            activeTab === tab ? 'mb-text-primary-400 mb-border-b-2 mb-border-primary-400' : 'mb-text-gray-500 hover:mb-text-gray-300'
          ]"
        >{{ NOMI_SCHEDE[tab] || tab }}</button>
      </div>

      <div class="mb-flex-1 mb-p-3 mb-space-y-3 mb-overflow-y-auto">

        <!-- ===== CONTENUTO ===== -->
        <template v-if="activeTab === 'Contenuto'">
          <!-- Text -->
          <template v-if="layer.type === 'text'">
            <div>
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Testo') }}<InfoTip :testo="t('Scegli le parole col mouse e premi un pulsante: grassetto, corsivo, colore, più piccole o più tenui. Invio va a capo.')" :titolo="t('Testo')" /></label>
              <FieldTestoLivello :key="layer.id" :modelValue="layer.content || ''" @update:modelValue="up('content', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Tag') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.tag" :options="OPTS_TAG" @update:model-value="up('tag', $event)" />
            </div>
          </template>
          <!-- Image -->
          <template v-if="layer.type === 'image'">
            <div>
              <label class="mps-label">{{ t('Indirizzo dell’immagine') }}</label>
              <div class="mb-flex mb-gap-1">
                <input :value="layer.imageSrc" @input="up('imageSrc', $event.target.value)" class="mps-input mb-flex-1" placeholder="https://..." />
                <button @click="pickImage" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">{{ t('Sfoglia') }}</button>
              </div>
              <!-- Slide dagli articoli (1.4.1457): la foto in evidenza di ogni articolo -->
              <button v-if="contenuti.attivi.value" type="button" class="mps-dato" :class="{ 'is-on': layer.imageSrc === FOTO_ARTICOLO }" :aria-pressed="layer.imageSrc === FOTO_ARTICOLO ? 'true' : 'false'" @click="up('imageSrc', FOTO_ARTICOLO)">{{ t('Foto dell’articolo') }}</button>
            </div>
            <div>
              <label class="mps-label">{{ t('Testo alternativo') }}</label>
              <input :value="layer.imageAlt" @input="up('imageAlt', $event.target.value)" class="mps-input" :placeholder="t('Cosa mostra l’immagine (per chi non la vede)')" />
              <button v-if="contenuti.attivi.value" type="button" class="mps-dato" :class="{ 'is-on': layer.imageAlt === '{post_title}' }" :aria-pressed="layer.imageAlt === '{post_title}' ? 'true' : 'false'" @click="up('imageAlt', '{post_title}')">{{ t('Titolo dell’articolo') }}</button>
            </div>
          </template>
          <!-- Button -->
          <template v-if="layer.type === 'button'">
            <div>
              <label class="mps-label">{{ t('Etichetta') }}</label>
              <input :value="layer.content" @input="up('content', $event.target.value)" class="mps-input" />
            </div>
            <div>
              <label class="mps-label">{{ t('Indirizzo') }}</label>
              <!-- (1.4.1445) cerca una pagina o un articolo del sito, o si incolla un indirizzo (prima solo testo libero) -->
              <FieldLink :modelValue="layer.buttonUrl || ''" @update:modelValue="up('buttonUrl', $event)" />
              <button v-if="contenuti.attivi.value" type="button" class="mps-dato" :class="{ 'is-on': layer.buttonUrl === LINK_ARTICOLO }" :aria-pressed="layer.buttonUrl === LINK_ARTICOLO ? 'true' : 'false'" @click="up('buttonUrl', LINK_ARTICOLO)">{{ t('Link dell’articolo') }}</button>
            </div>
            <div>
              <label class="mps-label">{{ t('Destinazione') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.buttonTarget" :options="OPTS_LINK_TARGET" @update:model-value="up('buttonTarget', $event)" />
            </div>
          </template>
          <!-- Icon -->
          <template v-if="layer.type === 'icon'">
            <div>
              <label class="mps-label">{{ t('Nome icona') }}</label>
              <div class="mb-flex mb-gap-1 mb-items-center">
                <span v-if="layer.iconName && svgIcona(layer.iconName, iconsSvg)" class="mps-icon-preview" v-html="svgIcona(layer.iconName, iconsSvg)"></span>
                <input :value="layer.iconName" @input="up('iconName', $event.target.value)" class="mps-input mb-flex-1" placeholder="star" />
                <button @click="showIconPicker = true" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">{{ t('Sfoglia') }}</button>
              </div>
            </div>
            <IconPicker
              v-if="showIconPicker"
              @select="up('iconName', $event); showIconPicker = false"
              @close="showIconPicker = false"
            />
          </template>
          <!-- Video -->
          <template v-if="layer.type === 'video'">
            <div>
              <label class="mps-label">{{ t('Indirizzo del video') }}</label>
              <div class="mb-flex mb-gap-1">
                <input :value="layer.videoSrc" @input="up('videoSrc', $event.target.value)" class="mps-input mb-flex-1" :placeholder="t('mp4 o indirizzo YouTube')" />
                <button @click="pickVideo" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">{{ t('Sfoglia') }}</button>
              </div>
            </div>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.videoAutoplay" @change="up('videoAutoplay', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Riproduzione automatica') }}</span>
            </label>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.videoMuted" @change="up('videoMuted', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Muto') }}</span>
            </label>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.videoLoop" @change="up('videoLoop', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Ripeti') }}</span>
            </label>
          </template>
          <!-- Audio -->
          <template v-if="layer.type === 'audio'">
            <div>
              <label class="mps-label">{{ t('Indirizzo dell’audio') }}</label>
              <div class="mb-flex mb-gap-1">
                <input :value="layer.audioSrc" @input="up('audioSrc', $event.target.value)" class="mps-input mb-flex-1" :placeholder="t('mp3 o indirizzo dell’audio')" />
                <button @click="pickAudio" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">{{ t('Sfoglia') }}</button>
              </div>
            </div>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.audioAutoplay" @change="up('audioAutoplay', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Riproduzione automatica') }}</span>
            </label>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.audioLoop" @change="up('audioLoop', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Ripeti') }}</span>
            </label>
          </template>

        </template>

        <!-- ===== INTERAZIONI (1.4.1426): azione al clic, visibilità negli stati, livello da pulire. Prima erano in
             fondo al Contenuto, mescolate ai testi; ora stanno nella modalità «Interazioni» dell'editor. ===== -->
        <template v-if="activeTab === 'Interazioni'">
          <!-- Stato iniziale (tutti i tipi) -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <div>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.initiallyHidden === true" @change="up('initiallyHidden', $event.target.checked)" class="mb-rounded" />
              <span class="mps-label mb-mb-0">{{ t("Nascosto all'avvio (lo mostra un altro livello)") }}</span>
            </label>
          </div>

          <!-- Layer Action (tutti i tipi) -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Azione al clic') }}</p>
          <div>
            <label class="mps-label">{{ t('Tipo azione') }}</label>
            <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.action?.type || 'none'" :options="OPTS_ACTION_TYPE" @update:model-value="setAction('type', $event)" />
          </div>
          <!-- Quando parte (1.4.1428, come «click / mouse enter» di Slider Revolution): al clic o al passaggio del mouse -->
          <div v-if="layer.action?.type && layer.action.type !== 'none'">
            <label class="mps-label">{{ t('Quando') }}</label>
            <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.action.trigger || 'click'" :options="OPTS_TRIGGER" @update:model-value="setAction('trigger', $event === 'click' ? '' : $event)" />
          </div>
          <label v-if="layer.action?.trigger === 'hover' && (layer.action.type === 'setState' || layer.action.type === 'cycleState')" class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
            <input type="checkbox" :checked="!!layer.action.ritorna" @change="setAction('ritorna', $event.target.checked)" class="mb-rounded" />
            <span class="mb-text-[11px] mb-text-gray-400">{{ t('Quando il mouse esce, torna com’era') }}</span>
          </label>
          <template v-if="layer.action?.type === 'goToSlide'">
            <!-- 1.4.1475: il numero parte da 1, come nelle frasi delle Interazioni («va alla slide 2»); si salva da 0 -->
            <div>
              <label class="mps-label">{{ t('Numero della slide') }}</label>
              <NumberScrubber theme="dark" :modelValue="(Number(layer.action.target) || 0) + 1" :min="1" :max="Math.max(1, (tutteLeSlide || []).length)" :step="1" :defaultValue="1" emitAs="number" :ariaLabel="t('Numero della slide')" @update:modelValue="setAction('target', Math.max(0, Math.round($event) - 1))" />
            </div>
          </template>
          <template v-if="layer.action?.type === 'openUrl'">
            <div>
              <label class="mps-label">{{ t('Indirizzo') }}</label>
              <FieldLink :modelValue="layer.action.url || ''" @update:modelValue="setAction('url', $event)" />
              <button v-if="contenuti.attivi.value" type="button" class="mps-dato" :class="{ 'is-on': layer.action.url === LINK_ARTICOLO }" :aria-pressed="layer.action.url === LINK_ARTICOLO ? 'true' : 'false'" @click="setAction('url', LINK_ARTICOLO)">{{ t('Link dell’articolo') }}</button>
            </div>
            <div>
              <label class="mps-label">{{ t('Destinazione') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.action.urlTarget || '_self'" :options="OPTS_LINK_TARGET" @update:model-value="setAction('urlTarget', $event)" />
            </div>
          </template>
          <template v-if="layer.action?.type === 'toggleLayer'">
            <div>
              <label class="mps-label">{{ t('Livello da mostrare o nascondere') }}</label>
              <!-- 1.4.1475: da tutte le slide e dai livelli su tutte le slide (il sito li trova ovunque nello slider), coi
                   nomi del pannello Livelli. Prima solo la slide, con emoji e segni HTML nel nome. -->
              <FieldSelect
                v-if="targetLayerOptions.length > 1"
                ui="dropdown" size="compact" theme="dark"
                :model-value="layer.action.target || ''"
                :options="targetLayerOptions"
                @update:model-value="setAction('target', $event)"
              />
              <p v-else class="mb-text-[10px] mb-text-gray-500 mb-italic">{{ t('Nessun altro livello nello slider') }}</p>
            </div>
            <div class="mb-grid mb-grid-cols-2 mb-gap-2">
              <div>
                <label class="mps-label">{{ t('Come compare') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.action.effetto || 'nessuno'" :options="OPTS_MOSTRA_EFFETTO" @update:model-value="setAction('effetto', $event === 'nessuno' ? undefined : $event)" />
              </div>
              <div v-if="layer.action.effetto === 'fade'">
                <label class="mps-label">{{ t('Durata') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.action.durata ?? 400" :min="50" :max="4000" :step="50" :defaultValue="400" emitAs="number" :sliderOnFocus="true" unit="ms" :ariaLabel="t('Durata')" @update:modelValue="setAction('durata', $event)" />
              </div>
            </div>
          </template>

          <template v-if="layer.action?.type === 'setState' || layer.action?.type === 'cycleState'">
            <div>
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Nome dello stato') }}<InfoTip :testo="t('Uno stato è un nome con un valore (luce = accesa). I livelli con «Visibile solo in uno stato» si mostrano quando lo stato ha uno dei loro valori. Il valore all’avvio si scrive nelle impostazioni dello slider, «Stati all’avvio».')" :titolo="t('Stati')" /></label>
              <input :value="layer.action.target || ''" @input="setAction('target', $event.target.value)" class="mps-input mb-w-full" placeholder="luce" list="mps-nomi-stati" />
            </div>
            <div v-if="layer.action.type === 'setState'">
              <label class="mps-label">{{ t('Valore') }}</label>
              <input :value="layer.action.value ?? ''" @input="setAction('value', $event.target.value)" class="mps-input mb-w-full" placeholder="accesa" />
            </div>
            <div v-if="layer.action.type === 'setState'">
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Colore dello stato') }}<InfoTip :testo="t('Facoltativo: i livelli con «Segue il colore di uno stato» (Stile, sotto il colore) prendono questo colore quando lo stato cambia.')" :titolo="t('Colore dello stato')" /></label>
              <FieldColor :modelValue="layer.action.color || ''" @update:modelValue="setAction('color', $event)" />
            </div>
            <div v-if="layer.action.type === 'cycleState'">
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Valori, in ordine') }}<InfoTip :testo="t('Separati da virgole: a ogni clic lo stato passa al valore dopo (1, 2, 3, 4). Due valori fanno un interruttore.')" :titolo="t('Stato successivo')" /></label>
              <input :value="layer.action.values || ''" @input="setAction('values', $event.target.value)" class="mps-input mb-w-full" placeholder="1,2,3,4" />
            </div>
            <label v-if="layer.action.type === 'cycleState'" class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.action.loop !== false" @change="setAction('loop', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Dall’ultimo valore ricomincia') }}</span>
            </label>
            <div>
              <label class="mps-label">{{ t('Nome per chi usa la tastiera o un lettore di schermo') }}</label>
              <input :value="layer.action.label || ''" @input="setAction('label', $event.target.value)" class="mps-input mb-w-full" :placeholder="t('Accendi la luce')" />
            </div>
          </template>
          <!-- Il contorno del livello scelto e di quello raggiunto con la tastiera (1.4.1421): prima solo da CSS su misura.
               Un pulsante che non imposta uno stato non ha contorno da colorare (ha il suo focus). -->
          <div v-if="layer.action?.type && layer.action.type !== 'none' && (layer.type !== 'button' || layer.action.type === 'setState')">
            <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Colore del contorno') }}<InfoTip :testo="t('Il contorno attorno al livello quando lo si raggiunge con la tastiera e, con «Imposta uno stato», quando è quello scelto. Vuoto = bianco; trasparente = nessun contorno.')" :titolo="t('Colore del contorno')" /></label>
            <FieldColor :modelValue="layer.action.segno || ''" @update:modelValue="setAction('segno', $event)" />
          </div>

          <!-- Visibile solo in uno stato -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
            <input type="checkbox" :checked="!!layer.stato" @change="up('stato', $event.target.checked ? { nome: '', vale: '', effetto: 'fade', durata: 600, ritardo: 0 } : null)" class="mb-rounded" />
            <span class="mps-label mb-mb-0 mb-flex mb-items-center mb-gap-1">{{ t('Visibile solo in uno stato') }}<InfoTip :testo="t('Il livello si mostra quando lo stato ha uno dei valori scritti (separati da virgole) e si nasconde negli altri casi. Con il punto esclamativo davanti vale il contrario: «!accesa» = finché non è accesa. Lo stato lo cambiano i livelli con l’azione «Imposta uno stato» o «Stato successivo».')" :titolo="t('Visibile solo in uno stato')" /></span>
          </label>
          <template v-if="layer.stato">
            <!-- Collegato a un livello cliccabile (1.4.1421): nome e valori si scrivono da soli, senza «!» né virgole. -->
            <div v-if="opzioniQuando.length > 1">
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Quando si vede') }}<InfoTip :testo="t('Scegli il livello cliccabile a cui è legato questo: il nome e i valori dello stato qui sotto si scrivono da soli. «Prima di ogni scelta» lo mostra finché non si è cliccato niente.')" :titolo="t('Quando si vede')" /></label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="quandoScelto" :options="opzioniQuando" @update:model-value="scegliQuando" />
            </div>
            <div class="mb-grid mb-grid-cols-2 mb-gap-2">
              <div>
                <label class="mps-label">{{ t('Stato') }}</label>
                <input :value="layer.stato.nome || ''" @input="upStato('nome', $event.target.value)" class="mps-input mb-w-full" placeholder="luce" list="mps-nomi-stati" />
              </div>
              <div>
                <label class="mps-label">{{ t('Valori') }}</label>
                <input :value="layer.stato.vale || ''" @input="upStato('vale', $event.target.value)" class="mps-input mb-w-full" placeholder="accesa" />
              </div>
            </div>
            <div>
              <label class="mps-label">{{ t('Come compare') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.stato.effetto || 'fade'" :options="OPTS_STATO_EFFETTO" @update:model-value="upStato('effetto', $event)" />
            </div>
            <div class="mb-grid mb-grid-cols-2 mb-gap-2">
              <div>
                <label class="mps-label">{{ t('Durata') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.stato.durata ?? 600" :min="0" :max="4000" :step="50" :defaultValue="600" emitAs="number" :sliderOnFocus="true" unit="ms" :ariaLabel="t('Durata')" @update:modelValue="upStato('durata', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Ritardo') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.stato.ritardo ?? 0" :min="0" :max="4000" :step="50" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="ms" :ariaLabel="t('Ritardo')" @update:modelValue="upStato('ritardo', $event)" />
              </div>
            </div>
          </template>

          <!-- Dove nasce e se prende i clic (1.4.1431): prima CSS su misura (la crepa di Scheggia, i veli di Sirena) -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <div v-if="opzioniSeguiClic.length > 1">
            <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Nasce dove si clicca') }}<InfoTip :testo="t('Il livello si sposta nel punto in cui si è cliccato per dare allo stato quel valore: la crepa compare dove si colpisce il vetro. Con la tastiera nasce al centro. Mettilo al centro dello slider: lo spostamento parte da lì.')" :titolo="t('Nasce dove si clicca')" /></label>
            <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.seguiClic || ''" :options="opzioniSeguiClic" @update:model-value="up('seguiClic', $event)" />
          </div>
          <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
            <input type="checkbox" :checked="!!layer.clicPassanti" @change="up('clicPassanti', $event.target.checked || '')" class="mb-rounded" />
            <span class="mps-label mb-mb-0 mb-flex mb-items-center mb-gap-1">{{ t('I clic passano attraverso') }}<InfoTip :testo="t('Sul sito il livello non prende il mouse: i clic e il passaggio arrivano ai livelli sotto. Serve per veli, luci e decori sopra i pulsanti. Nell’editor resta prendibile.')" :titolo="t('I clic passano attraverso')" /></span>
          </label>

          <!-- I nomi degli stati già usati nella slide, suggeriti mentre si scrive (1.4.1421) -->
          <datalist id="mps-nomi-stati">
            <option v-for="n in nomiStati" :key="n" :value="n" />
          </datalist>

          <!-- Da pulire col puntatore (solo immagini) -->
          <template v-if="layer.type === 'image'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.pulisci?.attivo" @change="up('pulisci', $event.target.checked ? { attivo: true, pennello: 70, soglia: 60, stato: '', colore: 'var(--olo-color-dark)' } : null)" class="mb-rounded" />
              <span class="mps-label mb-mb-0 mb-flex mb-items-center mb-gap-1">{{ t('Da pulire col puntatore') }}<InfoTip :testo="t('L’immagine copre quello che c’è sotto e si cancella passandoci sopra col mouse (col dito sul telefono). Pulita la parte scelta, il resto svanisce e lo stato scritto (nome=valore) cambia: così compaiono i livelli di «dopo».')" :titolo="t('Da pulire col puntatore')" /></span>
            </label>
            <template v-if="layer.pulisci?.attivo">
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">{{ t('Pennello') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.pulisci.pennello ?? 70" :min="10" :max="400" :step="5" :defaultValue="70" emitAs="number" :sliderOnFocus="true" unit="px" :ariaLabel="t('Pennello')" @update:modelValue="upPulisci('pennello', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Pulito a') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.pulisci.soglia ?? 60" :min="5" :max="100" :step="5" :defaultValue="60" emitAs="number" :sliderOnFocus="true" unit="%" :ariaLabel="t('Pulito a')" @update:modelValue="upPulisci('soglia', $event)" />
                </div>
              </div>
              <div>
                <label class="mps-label">{{ t('Colore sotto l’immagine') }}</label>
                <FieldColor :modelValue="layer.pulisci.colore || 'var(--olo-color-dark)'" @update:modelValue="upPulisci('colore', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Stato quando è pulito') }}</label>
                <input :value="layer.pulisci.stato || ''" @input="upPulisci('stato', $event.target.value)" class="mps-input mb-w-full" placeholder="camino=pulito" />
              </div>
            </template>
          </template>

        </template>

        <!-- ===== POSIZIONE ===== -->
        <template v-if="activeTab === 'Posizione'">
          <!-- Breakpoint indicator -->
          <div v-if="activeBreakpoint !== 'desktop'" class="mb-bg-yellow-900/30 mb-border mb-border-yellow-700/50 mb-rounded mb-p-2 mb-mb-2">
            <!-- 1.4.1442: la spiegazione sta nella (i) e «Azzera tutti» tiene sempre il suo posto. Prima compariva al
                 primo valore proprio dello schermo, il riquadro andava a capo e spostava tutti i campi sotto il mouse
                 mentre si scriveva (rifacendo a mano Elastan i valori finivano nel campo sbagliato). -->
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-flex mb-items-center mb-gap-1 mb-text-[10px] mb-text-yellow-400">{{ t('Valori per') }} {{ nomeSchermo }}
                <InfoTip :testo="t('Quelli non impostati qui ereditano dallo schermo più grande: il telefono dal tablet, il tablet dal notebook, il notebook dal computer.')" :titolo="t('Valori per') + ' ' + nomeSchermo" />
              </span>
              <button
                type="button"
                :disabled="!hasAnyOverride"
                :style="{ visibility: hasAnyOverride ? 'visible' : 'hidden' }"
                @click="clearAllOverrides"
                class="mb-text-[10px] mb-text-yellow-500 hover:mb-text-yellow-300 mb-whitespace-nowrap mb-ml-2"
              >{{ t('Azzera tutti') }}</button>
            </div>
          </div>
          <!-- Visibility toggle per breakpoint (anche sul Computer: un livello solo per il telefono si nasconde qui e si
               rimette «Visibile su Telefono»; gli schermi senza valore ereditano dal più grande, come sul sito) -->
          <div class="mb-mb-2">
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="rv('visible') !== false" @change="up('visible', $event.target.checked)" class="mb-rounded" />
              <span class="mps-label mb-mb-0">{{ t('Visibile su') }} {{ nomeSchermo }}</span>
            </label>
          </div>
          <div>
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('x') }">X (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <NumberScrubber class="mb-flex-1" theme="dark" :modelValue="rv('x')" :min="0" :max="100" :step="0.5" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('X')" @update:modelValue="up('x', Math.round($event * 1000) / 1000)" />
              <button v-if="isOverridden('x')" @click="clearOverride('x')" class="mb-text-yellow-500 mb-text-[10px]" :title="t('Azzera')">&times;</button>
            </div>
          </div>
          <div>
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('y') }">Y (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <NumberScrubber class="mb-flex-1" theme="dark" :modelValue="rv('y')" :min="0" :max="100" :step="0.5" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Y')" @update:modelValue="up('y', Math.round($event * 1000) / 1000)" />
              <button v-if="isOverridden('y')" @click="clearOverride('y')" class="mb-text-yellow-500 mb-text-[10px]" :title="t('Azzera')">&times;</button>
            </div>
          </div>
          <div>
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('width') }">{{ t('Larghezza (%)') }}</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <label class="mb-flex mb-items-center mb-gap-1 mb-text-[10px] mb-text-gray-400">
                <input type="checkbox" :checked="rv('width') === 'auto'" @change="up('width', $event.target.checked ? 'auto' : 50)" class="mb-rounded" /> Auto
              </label>
              <template v-if="rv('width') !== 'auto'">
                <NumberScrubber class="mb-flex-1" theme="dark" :modelValue="rv('width')" :min="0" :max="400" :step="0.5" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Larghezza (%)')" @update:modelValue="up('width', Math.round($event * 1000) / 1000)" />
              </template>
              <button v-if="isOverridden('width')" @click="clearOverride('width')" class="mb-text-yellow-500 mb-text-[10px]" :title="t('Azzera')">&times;</button>
            </div>
          </div>
          <div>
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('height') }">{{ t('Altezza (%)') }}</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <label class="mb-flex mb-items-center mb-gap-1 mb-text-[10px] mb-text-gray-400">
                <input type="checkbox" :checked="rv('height') === 'auto'" @change="up('height', $event.target.checked ? 'auto' : 30)" class="mb-rounded" /> Auto
              </label>
              <template v-if="rv('height') !== 'auto'">
                <NumberScrubber class="mb-flex-1" theme="dark" :modelValue="rv('height')" :min="0" :max="400" :step="0.5" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Altezza (%)')" @update:modelValue="up('height', Math.round($event * 1000) / 1000)" />
              </template>
              <button v-if="isOverridden('height')" @click="clearOverride('height')" class="mb-text-yellow-500 mb-text-[10px]" :title="t('Azzera')">&times;</button>
            </div>
          </div>
          <div v-if="layer.type === 'text' || layer.type === 'button' || layer.type === 'icon'">
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('fontSize') }">{{ t('Dimensione testo') }}</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <NumberScrubber class="mb-flex-1" theme="dark" :modelValue="rv('fontSize')" :min="4" :max="400" :step="1" :scriviDecimali="1" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Dimensione testo')" @update:modelValue="up('fontSize', $event)" />
              <button v-if="isOverridden('fontSize')" @click="clearOverride('fontSize')" class="mb-text-yellow-500 mb-text-[10px]" :title="t('Azzera')">&times;</button>
            </div>
          </div>

          <!-- Rotazione, punto di rotazione, retro nascosto (1.4.1430): nei temi erano 300 righe di CSS su misura -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <div>
            <label class="mps-label">{{ t('Rotazione') }}</label>
            <NumberScrubber theme="dark" :modelValue="Number(layer.rotazione) || 0" :min="-180" :max="180" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="°" :ariaLabel="t('Rotazione')" @update:modelValue="up('rotazione', $event || '')" />
          </div>
          <div>
            <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Punto di rotazione') }}<InfoTip :testo="t('Il punto attorno a cui il livello gira e si ingrandisce, nelle animazioni e nella rotazione: il centro, un angolo, un lato. Le carte che si girano come una porta ruotano su un lato.')" :titolo="t('Punto di rotazione')" /></label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <div class="mps-origine" role="group" :aria-label="t('Punto di rotazione')">
                <button v-for="o in ORIGINI" :key="o.v" type="button" class="mps-origine-punto" :class="{ 'is-on': (layer.origine || '50% 50%') === o.v }"
                        :title="o.nome" :aria-label="o.nome" :aria-pressed="(layer.origine || '50% 50%') === o.v ? 'true' : 'false'" @click="up('origine', o.v === '50% 50%' ? '' : o.v)"></button>
              </div>
              <input :value="layer.origine || ''" @change="up('origine', $event.target.value.trim())" class="mps-input mb-flex-1" placeholder="50% 50%" :aria-label="t('Punto di rotazione, in percentuale')" />
            </div>
          </div>
          <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
            <input type="checkbox" :checked="!!layer.retroNascosto" @change="up('retroNascosto', $event.target.checked || '')" class="mb-rounded" />
            <span class="mps-label mb-mb-0 mb-flex mb-items-center mb-gap-1">{{ t('Retro nascosto') }}<InfoTip :testo="t('Quando il livello gira in 3D e mostra la schiena, sparisce: due livelli uno sull’altro, uno girato di 180°, fanno una carta con due facce.')" :titolo="t('Retro nascosto')" /></span>
          </label>
          <!-- livelli su tutte le slide (1.4.1435): il sito li sa mettere dietro le slide, l'editor non lo offriva -->
          <label v-if="layer.isGlobal" class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
            <input type="checkbox" :checked="layer.globalPosition === 'back'" @change="up('globalPosition', $event.target.checked ? 'back' : 'front')" class="mb-rounded" />
            <span class="mps-label mb-mb-0 mb-flex mb-items-center mb-gap-1">{{ t('Dietro le slide') }}<InfoTip :testo="t('Un livello su tutte le slide sta davanti al loro contenuto; spuntato, sta dietro, come uno sfondo comune.')" :titolo="t('Dietro le slide')" /></span>
          </label>
        </template>

        <!-- ===== STILE ===== -->
        <template v-if="activeTab === 'Stile'">
          <!-- Su una riga sola (testi) e immagine nella forma (forme): 1.4.1430, prima CSS su misura -->
          <label v-if="layer.type === 'text' || layer.type === 'button'" class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
            <input type="checkbox" :checked="!!layer.unaRiga" @change="up('unaRiga', $event.target.checked || '')" class="mb-rounded" />
            <span class="mps-label mb-mb-0">{{ t('Su una riga sola (non va a capo)') }}</span>
          </label>
          <template v-if="layer.type === 'shape'">
            <div>
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Immagine nella forma') }}<InfoTip :testo="t('Una foto o una trama che riempie la forma al posto del colore: un tessuto, una carta, una striscia di un disegno più grande (con Dimensione e Posizione si sceglie quale parte).')" :titolo="t('Immagine nella forma')" /></label>
              <div class="mb-flex mb-gap-1">
                <input :value="layer.shapeImage || ''" @change="up('shapeImage', $event.target.value.trim())" class="mps-input mb-flex-1" placeholder="https://…" :aria-label="t('Immagine nella forma')" />
                <button type="button" @click="pickShapeImage" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-[10px] mb-text-gray-200">{{ t('Sfoglia') }}</button>
                <button v-if="layer.shapeImage" type="button" @click="up('shapeImage', '')" class="mb-px-2 mb-text-gray-400 hover:mb-text-red-400" :title="t('Togli')" :aria-label="t('Togli')">&times;</button>
              </div>
            </div>
            <div v-if="layer.shapeImage" class="mb-grid mb-grid-cols-2 mb-gap-2">
              <div>
                <label class="mps-label">{{ t('Dimensione') }}</label>
                <input :value="layer.shapeImageSize || 'cover'" @change="up('shapeImageSize', $event.target.value.trim())" class="mps-input mb-w-full" list="mps-dim-forma" />
                <datalist id="mps-dim-forma"><option value="cover" /><option value="contain" /><option value="100% 100%" /><option value="auto" /></datalist>
              </div>
              <div>
                <label class="mps-label">{{ t('Posizione') }}</label>
                <input :value="layer.shapeImagePos || 'center'" @change="up('shapeImagePos', $event.target.value.trim())" class="mps-input mb-w-full" list="mps-pos-forma" />
                <datalist id="mps-pos-forma"><option value="center" /><option value="top" /><option value="bottom" /><option value="left" /><option value="right" /></datalist>
              </div>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer mb-col-span-2">
                <input type="checkbox" :checked="(layer.shapeImageRepeat || '') === 'repeat'" @change="up('shapeImageRepeat', $event.target.checked ? 'repeat' : 'no-repeat')" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">{{ t('Ripeti come una trama') }}</span>
              </label>
            </div>
          </template>

          <!-- ── Tipografia (text/button) ── -->
          <template v-if="layer.type === 'text' || layer.type === 'button'">
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Tipografia') }}</p>
            <div>
              <!-- lo stesso selettore di tutte le tile (1.4.1435): ruoli del tema, caratteri del sito, Google Fonts e
                   qualsiasi nome di Google. Prima un menu di 35 caratteri: i temi ne usano 109 -->
              <label class="mps-label">{{ t('Font') }}</label>
              <FieldFontFamily :modelValue="layer.fontFamily || ''" @update:modelValue="up('fontFamily', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Dimensione testo') }}</label>
              <NumberScrubber theme="dark" :modelValue="rv('fontSize')" :min="4" :max="400" :step="1" :scriviDecimali="1" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Dimensione testo (px)')" @update:modelValue="up('fontSize', $event)" />
            </div>
            <div>
              <!-- un numero (1.4.1435): i caratteri variabili hanno anche i pesi di mezzo (Elastan 880), che un menu
                   400/700/900 non sapeva dare -->
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Peso del carattere') }}<InfoTip :testo="t('400 normale, 700 grassetto, 900 nerissimo. Con i caratteri variabili valgono anche i pesi di mezzo, per esempio 880.')" :titolo="t('Peso del carattere')" /></label>
              <NumberScrubber theme="dark" :modelValue="Number(layer.fontWeight) || 400" :min="100" :max="1000" :step="10" :defaultValue="400" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Peso del carattere')" @update:modelValue="up('fontWeight', String($event))" />
            </div>
            <div>
              <label class="mps-label">{{ t('Stile del carattere') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.fontStyle || 'normal'" :options="OPTS_FONT_STYLE" @update:model-value="up('fontStyle', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Colore') }}</label>
              <FieldColor :modelValue="baseColore('color') || 'var(--olo-color-light)'" @update:modelValue="upColore('color', $event)" />
              <div v-if="statiConColore.length" class="mb-mt-1">
                <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Segue il colore di uno stato') }}<InfoTip :testo="t('Il colore cambia quando cambia lo stato scelto: prende il «Colore dello stato» del livello cliccato (Interazioni). Finché lo stato non ha un colore, vale quello qui sopra.')" :titolo="t('Segue il colore di uno stato')" /></label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="statoColore('color')" :options="opzStatiColore" @update:model-value="upStatoColore('color', $event)" />
              </div>
            </div>
            <div>
              <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('textAlign') }">{{ t('Allineamento testo') }}</label>
              <div class="mb-flex mb-items-center mb-gap-1">
                <!-- 1.4.1446: in italiano (prima «left / center / right», i valori interni) -->
                <button v-for="a in ALLINEAMENTI" :key="a.v" type="button"
                  @click="up('textAlign', a.v)" :aria-pressed="(rv('textAlign') || 'left') === a.v ? 'true' : 'false'"
                  :class="['mb-flex-1 mb-py-1 mb-rounded mb-text-xs', (rv('textAlign') || 'left') === a.v ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']"
                >{{ a.n }}</button>
                <button v-if="isOverridden('textAlign')" @click="clearOverride('textAlign')" class="mb-text-yellow-500 mb-text-[10px]" :title="t('Azzera')">&times;</button>
              </div>
            </div>
            <div v-if="layer.type === 'text' && layer.height && layer.height !== 'auto'">
              <label class="mps-label">{{ t('Allineamento verticale') }}</label>
              <div class="mb-flex mb-items-center mb-gap-1">
                <button v-for="a in [['top', 'In alto'], ['center', 'Al centro'], ['bottom', 'In basso']]" :key="a[0]"
                  @click="up('textValign', a[0])"
                  :class="['mb-flex-1 mb-py-1 mb-rounded mb-text-xs', (layer.textValign || 'top') === a[0] ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']"
                >{{ t(a[1]) }}</button>
              </div>
            </div>
            <div>
              <label class="mps-label">{{ t('Interlinea') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.lineHeight ?? 1.2" :min="0.5" :max="4" :step="0.1" :defaultValue="1.2" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Interlinea')" @update:modelValue="up('lineHeight', $event)" />
            </div>
            <div>
              <!-- per schermo (1.4.1433): sul telefono un nome spaziato si stringe -->
              <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('letterSpacing') }">{{ t('Spaziatura lettere') }}</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <NumberScrubber class="mb-flex-1" theme="dark" :modelValue="rv('letterSpacing') ?? 0" :min="-20" :max="40" :step="0.5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Spaziatura lettere')" @update:modelValue="up('letterSpacing', $event)" />
                <button v-if="isOverridden('letterSpacing')" @click="clearOverride('letterSpacing')" class="mb-text-yellow-500 mb-text-[10px]" :title="t('Azzera')">&times;</button>
              </div>
            </div>
            <!-- Larghezza del carattere (1.4.1431): prima CSS su misura (Elastan) -->
            <div>
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Larghezza del carattere') }}<InfoTip :testo="t('Stringe o allarga le lettere senza deformarle: sotto 100 più strette, sopra più larghe. Funziona con i caratteri variabili che hanno la larghezza, come Anybody, Roboto Flex, Open Sans, Archivo.')" :titolo="t('Larghezza del carattere')" /></label>
              <NumberScrubber theme="dark" :modelValue="layer.fontWdth ?? 100" :min="25" :max="200" :step="1" :defaultValue="100" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Larghezza del carattere')" @update:modelValue="up('fontWdth', $event === 100 ? undefined : $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Trasformazione') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.textTransform || 'none'" :options="OPTS_TEXT_TRANSFORM" @update:model-value="up('textTransform', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Decorazione') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.textDecoration || 'none'" :options="OPTS_TEXT_DECORATION" @update:model-value="up('textDecoration', $event)" />
            </div>
            <!-- Text Stroke -->
            <div>
              <label class="mps-label">{{ t('Contorno del testo') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.textStrokeWidth ?? 0" :min="0" :max="10" :step="0.5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Contorno del testo')" @update:modelValue="up('textStrokeWidth', $event)" />
            </div>
            <template v-if="(layer.textStrokeWidth ?? 0) > 0">
              <div>
                <label class="mps-label">{{ t('Colore del tratto') }}</label>
                <FieldColor :modelValue="layer.textStrokeColor || 'var(--olo-color-dark)'" @update:modelValue="up('textStrokeColor', $event)" />
              </div>
            </template>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.selectableText" @change="up('selectableText', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Testo selezionabile') }}</span>
            </label>
          </template>

          <!-- ── Dimensione font (icon) ── -->
          <template v-if="layer.type === 'icon'">
            <div>
              <label class="mps-label">{{ t('Dimensione icona') }}</label>
              <NumberScrubber theme="dark" :modelValue="rv('fontSize')" :min="4" :max="400" :step="1" :scriviDecimali="1" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Dimensione icona (px)')" @update:modelValue="up('fontSize', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Colore di riempimento') }}</label>
              <FieldColor :modelValue="baseColore('color') || 'var(--olo-color-light)'" @update:modelValue="upColore('color', $event)" />
              <div v-if="statiConColore.length" class="mb-mt-1">
                <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Segue il colore di uno stato') }}<InfoTip :testo="t('Il colore cambia quando cambia lo stato scelto: prende il «Colore dello stato» del livello cliccato (Interazioni). Finché lo stato non ha un colore, vale quello qui sopra.')" :titolo="t('Segue il colore di uno stato')" /></label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="statoColore('color')" :options="opzStatiColore" @update:model-value="upStatoColore('color', $event)" />
              </div>
            </div>
            <div>
              <label class="mps-label">{{ t('Colore di riempimento (al posto di quello del file)') }}</label>
              <FieldColor :modelValue="layer.iconFillColor || ''" @update:modelValue="up('iconFillColor', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Colore del tratto') }}</label>
              <FieldColor :modelValue="layer.iconStrokeColor || ''" @update:modelValue="up('iconStrokeColor', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Spessore del tratto') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.iconStrokeWidth ?? 0" :min="0" :max="10" :step="0.5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Spessore del tratto')" @update:modelValue="up('iconStrokeWidth', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Tratteggio') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.iconStrokeDash ?? 0" :min="0" :max="50" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Tratteggio')" @update:modelValue="up('iconStrokeDash', $event)" />
            </div>
          </template>

          <!-- ── Sfondo e raggio ── -->
          <template v-if="layer.type !== 'image' && layer.type !== 'video'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Sfondo') }}</p>
          </template>

          <!-- Shape gradient toggle (le Sfumature qui sotto, quando ci sono, ne prendono il posto) -->
          <template v-if="layer.type === 'shape' && !sfumatureCoprono">
            <label v-if="!layer.sfondoForma?.length" class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.shapeGradient" @change="toggleShapeGradient($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Gradiente') }}</span>
            </label>
            <template v-if="layer.shapeGradient && !layer.sfondoForma?.length">
              <div>
                <label class="mps-label">{{ t('Colore da') }}</label>
                <FieldColor :modelValue="layer.shapeGradient.from || 'var(--olo-color-primary)'" @update:modelValue="upShapeGrad('from', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Colore a') }}</label>
                <FieldColor :modelValue="layer.shapeGradient.to || 'var(--olo-color-accent)'" @update:modelValue="upShapeGrad('to', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Angolo') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.shapeGradient.angle ?? 180" :min="0" :max="360" :step="15" :defaultValue="180" emitAs="number" :sliderOnFocus="false" unit="°" :ariaLabel="t('Angolo')" @update:modelValue="upShapeGrad('angle', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Inizio sfumatura') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.shapeGradient.fromStop ?? 0" :min="0" :max="95" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Inizio sfumatura')" @update:modelValue="upShapeGrad('fromStop', $event)" />
              </div>
            </template>
            <template v-else>
              <div>
                <label class="mps-label">{{ t('Sfondo') }}</label>
                <FieldColor :modelValue="baseColore('bgColor') || 'var(--olo-color-primary)'" @update:modelValue="upColore('bgColor', $event)" />
                <div v-if="statiConColore.length" class="mb-mt-1">
                  <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Segue il colore di uno stato') }}<InfoTip :testo="t('Il colore cambia quando cambia lo stato scelto: prende il «Colore dello stato» del livello cliccato (Interazioni). Finché lo stato non ha un colore, vale quello qui sopra.')" :titolo="t('Segue il colore di uno stato')" /></label>
                  <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="statoColore('bgColor')" :options="opzStatiColore" @update:model-value="upStatoColore('bgColor', $event)" />
                </div>
              </div>
            </template>
          </template>
          <!-- Sfumature della forma (1.4.1431): prima CSS su misura nei temi (righe, pois, aloni, trame) -->
          <template v-if="layer.type === 'shape'">
            <div>
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Sfumature') }}<InfoTip :testo="t('Una o più sfumature una sopra l’altra: lineari o dal centro, anche ripetute per fare righe, quadretti, pois e trame. La prima della lista sta sopra. Ogni colore ha il punto dove comincia e, se vuoi, dove finisce (in percentuale o in px).')" :titolo="t('Sfumature')" /></label>
              <FieldSfumature :modelValue="layer.sfondoForma || []" @update:modelValue="up('sfondoForma', $event.length ? $event : '')" />
            </div>
            <template v-if="layer.sfondoForma?.length">
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="!!layer.sfondoFormaSulColore" @change="up('sfondoFormaSulColore', $event.target.checked || '')" class="mb-rounded" />
                <span class="mps-label mb-mb-0 mb-flex mb-items-center mb-gap-1">{{ t('Sopra il colore della forma') }}<InfoTip :testo="t('Le sfumature si appoggiano sul colore Sfondo della forma (che può cambiare con uno stato). Senza, sotto le sfumature c’è il colore scelto qui.')" :titolo="t('Sopra il colore della forma')" /></span>
              </label>
              <div v-if="!layer.sfondoFormaSulColore">
                <label class="mps-label">{{ t('Colore sotto le sfumature') }}</label>
                <FieldColor :modelValue="layer.sfondoFormaColore || ''" @update:modelValue="up('sfondoFormaColore', $event)" />
              </div>
            </template>
          </template>
          <template v-else-if="layer.type !== 'shape' && layer.type !== 'image' && layer.type !== 'video'">
            <div>
              <label class="mps-label">{{ t('Sfondo') }}</label>
              <FieldColor :modelValue="baseColore('bgColor') || ''" @update:modelValue="upColore('bgColor', $event)" />
              <div v-if="statiConColore.length" class="mb-mt-1">
                <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Segue il colore di uno stato') }}<InfoTip :testo="t('Il colore cambia quando cambia lo stato scelto: prende il «Colore dello stato» del livello cliccato (Interazioni). Finché lo stato non ha un colore, vale quello qui sopra.')" :titolo="t('Segue il colore di uno stato')" /></label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="statoColore('bgColor')" :options="opzStatiColore" @update:model-value="upStatoColore('bgColor', $event)" />
              </div>
            </div>
          </template>

          <div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <label class="mps-label mb-mb-0">{{ t('Raggio (px)') }}</label>
              <button type="button" @click="up('borderRadiusLinked', layer.borderRadiusLinked === false)" :class="['mps-lega', layer.borderRadiusLinked !== false ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']" :title="t('Lega/slega angoli')" :aria-label="t('Lega/slega angoli')" :aria-pressed="layer.borderRadiusLinked !== false ? 'true' : 'false'" v-html="layer.borderRadiusLinked !== false ? ICO_LEGATI : ICO_SLEGATI"></button>
            </div>
            <template v-if="layer.borderRadiusLinked !== false">
              <NumberScrubber theme="dark" :modelValue="layer.borderRadius" :min="0" :max="999" :step="1" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Raggio (px)')" @update:modelValue="up('borderRadius', $event)" />
            </template>
            <template v-else>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">↖</label>
                  <NumberScrubber theme="dark" :modelValue="layer.borderRadiusTL ?? 0" :min="0" :max="999" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" :ariaLabel="t('Raggio in alto a sinistra')" @update:modelValue="up('borderRadiusTL', $event)" />
                </div>
                <div>
                  <label class="mps-label">↗</label>
                  <NumberScrubber theme="dark" :modelValue="layer.borderRadiusTR ?? 0" :min="0" :max="999" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" :ariaLabel="t('Raggio in alto a destra')" @update:modelValue="up('borderRadiusTR', $event)" />
                </div>
                <div>
                  <label class="mps-label">↙</label>
                  <NumberScrubber theme="dark" :modelValue="layer.borderRadiusBL ?? 0" :min="0" :max="999" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" :ariaLabel="t('Raggio in basso a sinistra')" @update:modelValue="up('borderRadiusBL', $event)" />
                </div>
                <div>
                  <label class="mps-label">↘</label>
                  <NumberScrubber theme="dark" :modelValue="layer.borderRadiusBR ?? 0" :min="0" :max="999" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" :ariaLabel="t('Raggio in basso a destra')" @update:modelValue="up('borderRadiusBR', $event)" />
                </div>
              </div>
            </template>
          </div>

          <!-- ── Bordo ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Bordo') }}</p>
          <div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <label class="mps-label mb-mb-0">{{ t('Larghezza (px)') }}</label>
              <button type="button" @click="up('borderWidthLinked', layer.borderWidthLinked === false)" :class="['mps-lega', layer.borderWidthLinked !== false ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']" :title="t('Lega/slega lati')" :aria-label="t('Lega/slega lati')" :aria-pressed="layer.borderWidthLinked !== false ? 'true' : 'false'" v-html="layer.borderWidthLinked !== false ? ICO_LEGATI : ICO_SLEGATI"></button>
            </div>
            <template v-if="layer.borderWidthLinked !== false">
              <NumberScrubber theme="dark" :modelValue="layer.borderWidth ?? 0" :min="0" :max="20" :step="1" :scriviDecimali="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Larghezza (px)')" @update:modelValue="up('borderWidth', $event)" />
            </template>
            <template v-else>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div v-for="side in ['Top','Right','Bottom','Left']" :key="side">
                  <label class="mps-label">{{ t(NOME_LATO[side]) }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer['borderWidth' + side] ?? 0" :min="0" :max="20" :step="1" :scriviDecimali="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="px" :ariaLabel="t('Bordo') + ' · ' + t(NOME_LATO[side])" @update:modelValue="up('borderWidth' + side, $event)" />
                </div>
              </div>
            </template>
          </div>
          <template v-if="(layer.borderWidth ?? 0) > 0 || (layer.borderWidthLinked === false && ((layer.borderWidthTop ?? 0) > 0 || (layer.borderWidthRight ?? 0) > 0 || (layer.borderWidthBottom ?? 0) > 0 || (layer.borderWidthLeft ?? 0) > 0))">
            <div>
              <label class="mps-label">{{ t('Stile') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.borderStyle || 'solid'" :options="OPTS_BORDER_STYLE" @update:model-value="up('borderStyle', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Colore') }}</label>
              <FieldColor :modelValue="layer.borderColor || 'var(--olo-color-light)'" @update:modelValue="up('borderColor', $event)" />
            </div>
          </template>

          <!-- ── Spaziatura ── -->
          <template v-if="layer.type !== 'image' && layer.type !== 'video'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Spaziatura') }}</p>
            <div>
              <div class="mb-flex mb-items-center mb-justify-between">
                <label class="mps-label mb-mb-0">{{ t('Padding') }}</label>
                <button type="button" @click="up('paddingLinked', layer.paddingLinked === false)" :class="['mps-lega', layer.paddingLinked !== false ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']" :title="t('Lega/slega lati')" :aria-label="t('Lega/slega lati')" :aria-pressed="layer.paddingLinked !== false ? 'true' : 'false'" v-html="layer.paddingLinked !== false ? ICO_LEGATI : ICO_SLEGATI"></button>
              </div>
              <template v-if="layer.paddingLinked !== false">
                <NumberScrubber theme="dark" :modelValue="layer.padding" :min="0" :max="80" :step="1" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Padding')" @update:modelValue="up('padding', $event)" />
              </template>
              <template v-else>
                <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                  <div v-for="side in ['Top','Right','Bottom','Left']" :key="side">
                    <label class="mps-label">{{ t(NOME_LATO[side]) }}</label>
                    <NumberScrubber theme="dark" :modelValue="layer['padding' + side] ?? 0" :min="0" :max="80" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="px" :ariaLabel="t('Padding') + ' · ' + t(NOME_LATO[side])" @update:modelValue="up('padding' + side, $event)" />
                  </div>
                </div>
              </template>
            </div>
          </template>

          <!-- ── Immagine (solo image) ── -->
          <template v-if="layer.type === 'image'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Immagine') }}</p>
            <!-- Colori del disegno (1.4.1433): i disegni SVG coi colori della palette al posto di quelli del file -->
            <div v-if="eDisegno(layer.imageSrc)">
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Colori del disegno') }}<InfoTip :testo="t('I colori che il disegno ha nel file, dal più usato. Scegline uno della palette al posto di quello del file: il disegno segue i colori del sito. Vuoto = il colore del file.')" :titolo="t('Colori del disegno')" /></label>
              <FieldColoriDisegno :key="layer.imageSrc" :src="layer.imageSrc" :modelValue="layer.coloriDisegno || {}" @update:modelValue="up('coloriDisegno', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Adattamento') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.objectFit || 'cover'" :options="OPTS_OBJECT_FIT" @update:model-value="up('objectFit', $event)" />
            </div>
            <div>
              <!-- griglia + valore libero (1.4.1435): i temi inquadrano le foto anche a «center 30%», «55% 50%» -->
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Posizione') }}<InfoTip :testo="t('Quale parte della foto resta in vista quando non entra tutta: un punto della griglia, oppure in percentuale (50% 30% = al centro, un po\' in alto).')" :titolo="t('Posizione')" /></label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <div class="mps-origine" role="group" :aria-label="t('Posizione')">
                  <button v-for="o in ORIGINI" :key="o.v" type="button" class="mps-origine-punto" :class="{ 'is-on': posizioneFoto === o.v }"
                          :title="o.nome" :aria-label="o.nome" :aria-pressed="posizioneFoto === o.v ? 'true' : 'false'" @click="up('objectPosition', o.v === '50% 50%' ? 'center' : o.v)"></button>
                </div>
                <input :value="layer.objectPosition || 'center'" @change="up('objectPosition', $event.target.value.trim() || 'center')" class="mps-input mb-flex-1" placeholder="center" :aria-label="t('Posizione, in percentuale')" />
              </div>
            </div>
          </template>

          <!-- 1.4.1462: sfocatura delle forme (aloni, macchie di luce): nei temi c'era già, ma nessun comando la mostrava -->
          <template v-if="layer.type === 'shape'">
            <div>
              <label class="mps-label">{{ t('Sfocatura') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterBlur ?? 0" :min="0" :max="100" :step="0.5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Sfocatura (px)')" @update:modelValue="up('filterBlur', $event)" />
            </div>
          </template>
          <!-- ── Filtri CSS (image/video) ── -->
          <template v-if="layer.type === 'image' || layer.type === 'video'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Filtri') }}</p>
            <div>
              <label class="mps-label">{{ t('Luminosità') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterBrightness ?? 100" :min="0" :max="200" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Luminosità (%)')" @update:modelValue="up('filterBrightness', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Contrasto') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterContrast ?? 100" :min="0" :max="200" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Contrasto (%)')" @update:modelValue="up('filterContrast', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Saturazione') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterSaturate ?? 100" :min="0" :max="200" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Saturazione (%)')" @update:modelValue="up('filterSaturate', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Scala di grigi') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterGrayscale ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Scala di grigi (%)')" @update:modelValue="up('filterGrayscale', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Rotazione tinta') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterHueRotate ?? 0" :min="0" :max="360" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="°" :ariaLabel="t('Rotazione tinta')" @update:modelValue="up('filterHueRotate', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Sfocatura') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterBlur ?? 0" :min="0" :max="100" :step="0.5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Sfocatura (px)')" @update:modelValue="up('filterBlur', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Seppia') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterSepia ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Seppia (%)')" @update:modelValue="up('filterSepia', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Inverti') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterInvert ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Inverti (%)')" @update:modelValue="up('filterInvert', $event)" />
            </div>
          </template>

          <!-- ── Backdrop Filter (tutti i tipi) ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Filtro sfondo') }}</p>
          <div>
            <label class="mps-label">{{ t('Sfocatura dietro') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropBlur ?? 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Sfocatura dietro')" @update:modelValue="up('backdropBlur', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Luminosità') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropBrightness ?? 100" :min="0" :max="200" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Luminosità (%)')" @update:modelValue="up('backdropBrightness', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Scala di grigi') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropGrayscale ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Scala di grigi (%)')" @update:modelValue="up('backdropGrayscale', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Contrasto') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropContrast ?? 100" :min="0" :max="300" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Contrasto (%)')" @update:modelValue="up('backdropContrast', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Saturazione') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropSaturate ?? 100" :min="0" :max="300" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Saturazione (%)')" @update:modelValue="up('backdropSaturate', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Inverti') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropInvert ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Inverti (%)')" @update:modelValue="up('backdropInvert', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Rotazione tinta') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropHueRotate ?? 0" :min="0" :max="360" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="°" :ariaLabel="t('Rotazione tinta')" @update:modelValue="up('backdropHueRotate', $event)" />
          </div>

          <!-- ── Ombre ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Ombre') }}</p>

          <!-- Ombre del riquadro (1.4.1431): una lista sola, anche più d'una e anche interne. Un'ombra semplice resta
               salvata in «boxShadow» come prima; più ombre o un'ombra interna vanno in «ombre». -->
          <FieldOmbre :modelValue="ombreLista" @update:modelValue="setOmbre" />

          <!-- Ombra che segue la forma (drop-shadow) -->
          <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer mb-mt-2">
            <input type="checkbox" :checked="!!layer.ombraForma" @change="up('ombraForma', $event.target.checked ? { x: 0, y: 10, blur: 20, color: 'color-mix(in srgb, var(--olo-color-dark) 40%, transparent)' } : '')" class="mb-rounded" />
            <span class="mps-label mb-mb-0 mb-flex mb-items-center mb-gap-1">{{ t('Ombra che segue la forma') }}<InfoTip :testo="t('L’ombra prende il contorno di quello che si vede (una forma ritagliata, un’immagine con parti trasparenti, un’icona) e non quello del riquadro.')" :titolo="t('Ombra che segue la forma')" /></span>
          </label>
          <template v-if="layer.ombraForma">
            <div class="mb-grid mb-grid-cols-2 mb-gap-2">
              <div>
                <label class="mps-label">X</label>
                <NumberScrubber theme="dark" :modelValue="layer.ombraForma.x ?? 0" :min="-100" :max="100" :step="1" :defaultValue="0" emitAs="number" unit="px" :sliderOnFocus="true" :ariaLabel="t('X')" @update:modelValue="up('ombraForma', { ...layer.ombraForma, x: $event })" />
              </div>
              <div>
                <label class="mps-label">Y</label>
                <NumberScrubber theme="dark" :modelValue="layer.ombraForma.y ?? 0" :min="-100" :max="100" :step="1" :defaultValue="0" emitAs="number" unit="px" :sliderOnFocus="true" :ariaLabel="t('Y')" @update:modelValue="up('ombraForma', { ...layer.ombraForma, y: $event })" />
              </div>
              <div>
                <label class="mps-label">{{ t('Sfocatura') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.ombraForma.blur ?? 0" :min="0" :max="200" :step="1" :defaultValue="0" emitAs="number" unit="px" :sliderOnFocus="true" :ariaLabel="t('Sfocatura')" @update:modelValue="up('ombraForma', { ...layer.ombraForma, blur: $event })" />
              </div>
            </div>
            <FieldColor :modelValue="layer.ombraForma.color || 'color-mix(in srgb, var(--olo-color-dark) 40%, transparent)'" @update:modelValue="up('ombraForma', { ...layer.ombraForma, color: $event })" />
          </template>

          <!-- Text shadow (solo text/button) -->
          <template v-if="layer.type === 'text' || layer.type === 'button'">
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer mb-mt-2">
              <input type="checkbox" :checked="!!layer.textShadow" @change="toggleTextShadow($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Ombra del testo') }}</span>
            </label>
            <template v-if="layer.textShadow">
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">X</label>
                  <NumberScrubber theme="dark" :modelValue="layer.textShadow.x ?? 2" :min="-20" :max="20" :step="1" :defaultValue="2" emitAs="number" :sliderOnFocus="true" :ariaLabel="t('Ombra del testo, orizzontale')" @update:modelValue="upTextShadow('x', $event)" />
                </div>
                <div>
                  <label class="mps-label">Y</label>
                  <NumberScrubber theme="dark" :modelValue="layer.textShadow.y ?? 2" :min="-20" :max="20" :step="1" :defaultValue="2" emitAs="number" :sliderOnFocus="true" :ariaLabel="t('Ombra del testo, verticale')" @update:modelValue="upTextShadow('y', $event)" />
                </div>
              </div>
              <div>
                <label class="mps-label">{{ t('Sfocatura') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.textShadow.blur ?? 4" :min="0" :max="30" :step="1" :defaultValue="4" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Sfocatura dell’ombra del testo')" @update:modelValue="upTextShadow('blur', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Colore ombra') }}</label>
                <FieldColor :modelValue="layer.textShadow.color || 'var(--olo-color-dark)'" @update:modelValue="upTextShadow('color', $event)" />
              </div>
            </template>
          </template>

          <!-- ── Effetti ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Effetti') }}</p>
          <div>
            <label class="mps-label">{{ t('Opacità') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.opacity ?? 100" :min="0" :max="100" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Opacità (%)')" @update:modelValue="up('opacity', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Fusione') }}</label>
            <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.blendMode || 'normal'" :options="OPTS_BLEND_MODE" @update:model-value="up('blendMode', $event)" />
          </div>
          <!-- Bordi sfumati, taglio, ritaglio, colore che sfuma (1.4.1431): prima CSS su misura nei temi -->
          <div>
            <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Bordi sfumati') }}<InfoTip :testo="t('Il livello svanisce verso i bordi, come una foto con la vignetta al contrario: pieno al centro fino a questa percentuale, poi trasparente. 0 = bordi netti.')" :titolo="t('Bordi sfumati')" /></label>
            <NumberScrubber theme="dark" :modelValue="Number(layer.bordiSfumati) || 0" :min="0" :max="99" :step="1" :defaultValue="0" emitAs="number" unit="%" :sliderOnFocus="false" :ariaLabel="t('Bordi sfumati')" @update:modelValue="up('bordiSfumati', $event || '')" />
          </div>
          <div>
            <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Cambio di colore sfumato') }}<InfoTip :testo="t('Quando il colore di sfondo del livello cambia (per esempio con uno stato), il passaggio dura questo tempo invece di essere di colpo. 0 = di colpo.')" :titolo="t('Cambio di colore sfumato')" /></label>
            <NumberScrubber theme="dark" :modelValue="Number(layer.sfumaColori) || 0" :min="0" :max="5000" :step="50" :defaultValue="0" emitAs="number" unit="ms" :sliderOnFocus="false" :ariaLabel="t('Cambio di colore sfumato')" @update:modelValue="up('sfumaColori', $event || '')" />
          </div>
          <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
            <input type="checkbox" :checked="!!layer.ritaglia" @change="up('ritaglia', $event.target.checked || '')" class="mb-rounded" />
            <span class="mps-label mb-mb-0 mb-flex mb-items-center mb-gap-1">{{ t('Taglia quello che esce') }}<InfoTip :testo="t('Quello che sporge dal riquadro del livello (un testo lungo, un’immagine ingrandita) non si vede.')" :titolo="t('Taglia quello che esce')" /></span>
          </label>
          <div>
            <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Ritaglio') }}<InfoTip :testo="t('Il livello si vede solo dentro la figura fatta da questi punti, in percentuale del livello (0 0 = in alto a sinistra, 100% 100% = in basso a destra). Parti da una forma pronta e sposta i punti.')" :titolo="t('Ritaglio')" /></label>
            <FieldRitaglio :modelValue="layer.ritaglio || []" @update:modelValue="up('ritaglio', $event.length ? $event : '')" />
          </div>

          <!-- SFX Block Reveal (solo text/button/icon) -->
          <template v-if="layer.type === 'text' || layer.type === 'button' || layer.type === 'icon'">
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Rivelazione a blocco') }}</p>
          <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
            <input type="checkbox" :checked="!!layer.sfx" @change="toggleSfx($event.target.checked)" class="mb-rounded" />
            <span class="mb-text-[11px] mb-text-gray-400">{{ t('Attivo') }}</span>
          </label>
          <template v-if="layer.sfx">
            <div>
              <label class="mps-label">{{ t('Effetto') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.sfx.effect || 'blockRight'" :options="OPTS_SFX_EFFECT" @update:model-value="upSfx('effect', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Colore blocco') }}</label>
              <FieldColor :modelValue="layer.sfx.color || 'var(--olo-color-light)'" @update:modelValue="upSfx('color', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Durata') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.sfx.duration || 800" :min="200" :max="2000" :step="50" :defaultValue="800" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata (ms)')" @update:modelValue="upSfx('duration', $event)" />
            </div>
          </template>
          </template>

          <!-- ── Cursore ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Cursore') }}</p>
          <div>
            <label class="mps-label">{{ t('Cursore del mouse') }}</label>
            <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.cursor || 'auto'" :options="OPTS_CURSOR" @update:model-value="up('cursor', $event)" />
          </div>

          <!-- ── Attributi personalizzati ── -->
          <!-- 1.4.1475: ID e classi solo dove un livello salvato prima li ha (per leggerli e toglierli), come il CSS su
               misura: il Pro Slider si costruisce coi comandi, e un nome di classe senza un CSS che lo usi non fa niente -->
          <template v-if="layer.customId || layer.customClass || layer.customCSS">
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Attributi') }}</p>
          <div v-if="layer.customId">
            <label class="mps-label">{{ t('ID personalizzato') }}</label>
            <input :value="layer.customId || ''" @input="up('customId', $event.target.value)" class="mps-input" placeholder="es. hero-title" />
          </div>
          <div v-if="layer.customClass">
            <label class="mps-label">{{ t('Classi CSS') }}</label>
            <input :value="layer.customClass || ''" @input="up('customClass', $event.target.value)" class="mps-input" placeholder="es. my-class another-class" />
          </div>
          <!-- Il CSS su misura non si offre più (1.4.1431, utente: «comandi usabili da chiunque»): resta visibile solo dove
               un livello salvato prima lo usa ancora, per poterlo leggere e togliere. -->
          <div v-if="layer.customCSS">
            <label class="mps-label">{{ t('CSS personalizzato') }}</label>
            <textarea :value="layer.customCSS || ''" @input="up('customCSS', $event.target.value)" rows="3"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-[10px] mb-text-gray-900 mb-resize-y mb-font-mono" placeholder="color: red; transform: rotate(5deg);"></textarea>
          </div>
          </template>
        </template>

        <!-- ===== ANIMAZIONE ===== -->
        <template v-if="activeTab === 'Animazione'">
          <!-- Toggle Semplice / Timeline -->
          <div class="mb-flex mb-bg-gray-800 mb-rounded mb-p-0.5 mb-mb-3">
            <button
              @click="setAnimMode('simple')"
              :class="['mb-flex-1 mb-py-1 mb-rounded mb-text-[10px] mb-font-medium mb-transition-colors',
                !hasTimeline ? 'mb-bg-primary-600 mb-text-white' : 'mb-text-gray-400 hover:mb-text-gray-300']"
            >{{ t('Semplice') }}</button>
            <button
              @click="setAnimMode('timeline')"
              :class="['mb-flex-1 mb-py-1 mb-rounded mb-text-[10px] mb-font-medium mb-transition-colors',
                hasTimeline ? 'mb-bg-primary-600 mb-text-white' : 'mb-text-gray-400 hover:mb-text-gray-300']"
            >{{ t('Linea del tempo') }}</button>
          </div>
          <!-- 1.4.1472: tornare a «Semplice» cancellava la linea del tempo senza chiedere -->
          <div v-if="chiediSemplice" class="mps-conferma" role="alert">
            <span>{{ t('La linea del tempo di questo livello si perde.') }}</span>
            <div class="mps-conferma-fila">
              <button type="button" class="mps-conferma-si" @click="confermaSemplice">{{ t('Torna a Semplice') }}</button>
              <button type="button" class="mps-conferma-no" @click="chiediSemplice = false">{{ t('Annulla') }}</button>
            </div>
          </div>

          <!-- Modo SEMPLICE -->
          <template v-if="!hasTimeline">
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Entrata') }}</p>
            <div>
              <label class="mps-label">{{ t('Animazione entrata') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.animIn" :options="animationsInGroups" @update:model-value="up('animIn', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Durata') }}</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <NumberScrubber theme="dark" :modelValue="layer.animInDuration" :min="0" :max="5000" :step="50" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata (ms)')" @update:modelValue="up('animInDuration', $event)" />
              </div>
            </div>
            <div>
              <label class="mps-label">{{ t('Ritardo') }}</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <NumberScrubber theme="dark" :modelValue="layer.animInDelay" :min="0" :max="10000" :step="50" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Ritardo (ms)')" @update:modelValue="up('animInDelay', $event)" />
              </div>
            </div>

            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Uscita') }}</p>
            <div>
              <label class="mps-label">{{ t('Animazione uscita') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.animOut" :options="animationsOutGroups" @update:model-value="up('animOut', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Durata') }}</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <NumberScrubber theme="dark" :modelValue="layer.animOutDuration" :min="100" :max="3000" :step="50" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata (ms)')" @update:modelValue="up('animOutDuration', $event)" />
              </div>
            </div>
            <div>
              <label class="mps-label">{{ t('Ritardo') }}</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <NumberScrubber theme="dark" :modelValue="layer.animOutDelay" :min="0" :max="3000" :step="50" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Ritardo (ms)')" @update:modelValue="up('animOutDelay', $event)" />
              </div>
            </div>
            <!-- 1.4.1472: stava sotto «Uscita» ma vale anche per l'entrata -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <div>
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Andamento di entrata e uscita') }}<InfoTip :testo="t('Come il livello si muove mentre entra e mentre esce: costante, morbido, che frena alla fine, che rimbalza… Lo stesso andamento vale per tutte e due.')" :titolo="t('Andamento di entrata e uscita')" /></label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.animEasing" :options="OPTS_ANIM_EASING" @update:model-value="up('animEasing', $event)" />
            </div>

            <!-- Character Animation (solo per text) -->
            <template v-if="layer.type === 'text'">
              <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
              <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Animazione del testo') }}</p>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="!!layer.charAnim" @change="toggleCharAnim($event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">{{ t('Anima lettere o parole') }}</span>
              </label>
              <template v-if="layer.charAnim">
                <div>
                  <label class="mps-label">{{ t('Dividi per') }}</label>
                  <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.charAnim.split || 'chars'" :options="OPTS_CHAR_SPLIT" @update:model-value="upCharAnim('split', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Direzione') }}</label>
                  <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.charAnim.direction || 'forward'" :options="OPTS_CHAR_DIRECTION" @update:model-value="upCharAnim('direction', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Ritardo fra le lettere') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.stagger || 30" :min="5" :max="200" :step="5" :defaultValue="30" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Intervallo fra lettere')" @update:modelValue="upCharAnim('stagger', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Spostamento orizzontale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.offsetX || 0" :min="-100" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Spostamento orizzontale')" @update:modelValue="upCharAnim('offsetX', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Spostamento verticale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.offsetY || 0" :min="-100" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Spostamento verticale')" @update:modelValue="upCharAnim('offsetY', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Rotazione') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.rotation || 0" :min="-180" :max="180" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="°" :ariaLabel="t('Rotazione (deg)')" @update:modelValue="upCharAnim('rotation', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Scala iniziale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.scale ?? 1" :min="0" :max="3" :step="0.1" :defaultValue="1" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Scala iniziale')" @update:modelValue="upCharAnim('scale', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Opacità iniziale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.opacity ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Opacità iniziale (%)')" @update:modelValue="upCharAnim('opacity', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Sfocatura iniziale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.blur || 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Sfocatura iniziale (px)')" @update:modelValue="upCharAnim('blur', $event)" />
                </div>
                <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                  <input type="checkbox" :checked="layer.charAnim.yoyo" @change="upCharAnim('yoyo', $event.target.checked)" class="mb-rounded" />
                  <span class="mb-text-[11px] mb-text-gray-400">{{ t('Andata e ritorno') }}</span>
                </label>
              </template>
            </template>

            <!-- Loop Animation -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Movimento continuo') }}</p>
            <div>
              <label class="mps-label">{{ t('Animazione continua') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.animLoop || 'none'" :options="loopAnimGroups" @update:model-value="up('animLoop', $event)" />
            </div>
            <template v-if="layer.animLoop && layer.animLoop !== 'none'">
              <div>
                <label class="mps-label">{{ t('Durata del ciclo') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.animLoopDuration || 3000" :min="200" :max="20000" :step="100" :defaultValue="3000" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata loop (ms)')" @update:modelValue="up('animLoopDuration', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Andamento') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.animLoopEasing || 'ease-in-out'" :options="EASING_GROUPS" @update:model-value="up('animLoopEasing', $event)" />
              </div>
            </template>

          </template>

          <!-- Modo TIMELINE -->
          <template v-else>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase mb-flex mb-items-center mb-gap-1">{{ t('Fotogramma chiave') }}<InfoTip :testo="t('Usa la linea del tempo sotto la tela per aggiungere e spostare i fotogrammi chiave. Clicca un fotogramma: la tela va a quel momento, e trascinando il livello lo sposti in quel fotogramma.')" :titolo="t('Fotogramma chiave')" /></p>
            <KeyframeProperties
              :keyframe="selectedKeyframe"
              :timeline="layer.timeline"
              @update-keyframe="(id, updates) => $emit('update-keyframe', id, updates)"
            />

            <!-- L'Uscita vale anche con la linea del tempo (1.4.1472): prima era sempre una dissolvenza di 400 ms -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase mb-flex mb-items-center mb-gap-1">{{ t('Uscita') }}<InfoTip :testo="t('Come il livello esce quando la slide cambia (o alla fine della linea del tempo, con «Aspetta la fine» spento). Parte dal punto in cui la linea del tempo è arrivata. Finché non la cambi è una dissolvenza rapida.')" :titolo="t('Uscita')" /></p>
            <div>
              <label class="mps-label">{{ t('Animazione uscita') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="uscitaTl.effetto" :options="animationsOutGroups" @update:model-value="upUscitaTl('effetto', $event)" />
            </div>
            <!-- uno per riga come nell'animazione semplice (1.4.1479): affiancati non ci stavano e il pannello scorreva di lato -->
            <div v-if="uscitaTl.effetto !== 'none'" class="mb-grid mb-grid-cols-1 mb-gap-2">
              <div>
                <label class="mps-label">{{ t('Durata') }}</label>
                <NumberScrubber theme="dark" :modelValue="uscitaTl.durata" :min="0" :max="3000" :step="50" :defaultValue="400" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata dell’uscita')" @update:modelValue="upUscitaTl('durata', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Ritardo') }}</label>
                <NumberScrubber theme="dark" :modelValue="uscitaTl.ritardo" :min="0" :max="3000" :step="50" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Ritardo dell’uscita')" @update:modelValue="upUscitaTl('ritardo', $event)" />
              </div>
            </div>
            <div v-if="uscitaTl.effetto !== 'none'">
              <label class="mps-label">{{ t('Andamento dell’uscita') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="uscitaTl.andamento" :options="OPTS_ANIM_EASING" @update:model-value="upUscitaTl('andamento', $event)" />
            </div>
            <p class="mb-text-[10px] mb-text-gray-500 mb-flex mb-items-center mb-gap-1 mb-mt-1">{{ t('Cosa non vale con la linea del tempo') }}<InfoTip :testo="t('L’entrata, l’animazione del testo, il movimento continuo e la maschera qui non agiscono: li fanno i fotogrammi chiave (posizione, opacità, scala, rotazione, maschera, sfocatura). Restano salvati e valgono di nuovo tornando a «Semplice».')" :titolo="t('Cosa non vale con la linea del tempo')" /></p>
          </template>

            <!-- Hover Effects (valgono anche con la linea del tempo: 1.4.1472, prima la sezione spariva ma il sito la rendeva) -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase mb-flex mb-items-center mb-gap-1">{{ t('Al passaggio del mouse') }}<InfoTip v-if="hasTimeline && layer.hover" :testo="t('Con la linea del tempo cambiano colori, bordo, raggio, cursore, scala, rotazione e spostamento. Inclinazioni, rotazioni 3D, opacità e filtri li comanda la linea del tempo, e qui non ci sono.')" :titolo="t('Al passaggio del mouse')" /></p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.hover" @change="toggleHover($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Cambia al passaggio del mouse') }}</span>
            </label>
            <template v-if="layer.hover">
              <!-- Transform -->
              <p class="mb-text-[9px] mb-text-gray-500 mb-uppercase mb-mt-1">{{ t('Trasformazione') }}</p>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">{{ t('Scala') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.scale ?? 1" :min="0.5" :max="2" :step="0.05" :defaultValue="1" emitAs="number" :sliderOnFocus="true" :ariaLabel="t('Scala')" @update:modelValue="upHover('scale', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Rotazione Z') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.rotation ?? 0" :min="-45" :max="45" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="°" :ariaLabel="t('Rotazione Z')" @update:modelValue="upHover('rotation', $event)" />
                </div>
                <div v-if="!hasTimeline">
                  <label class="mps-label">{{ t('Rotazione X') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.rotateX ?? 0" :min="-90" :max="90" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="°" :ariaLabel="t('Rotazione X')" @update:modelValue="upHover('rotateX', $event)" />
                </div>
                <div v-if="!hasTimeline">
                  <label class="mps-label">{{ t('Rotazione Y') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.rotateY ?? 0" :min="-90" :max="90" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="°" :ariaLabel="t('Rotazione Y')" @update:modelValue="upHover('rotateY', $event)" />
                </div>
                <div v-if="!hasTimeline">
                  <label class="mps-label">{{ t('Inclinazione orizzontale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.skewX ?? 0" :min="-30" :max="30" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="°" :ariaLabel="t('Inclinazione orizzontale')" @update:modelValue="upHover('skewX', $event)" />
                </div>
                <div v-if="!hasTimeline">
                  <label class="mps-label">{{ t('Inclinazione verticale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.skewY ?? 0" :min="-30" :max="30" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="°" :ariaLabel="t('Inclinazione verticale')" @update:modelValue="upHover('skewY', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Spostamento orizzontale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.x ?? 0" :min="-20" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="%" :ariaLabel="t('Spostamento orizzontale al passaggio')" @update:modelValue="upHover('x', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Spostamento verticale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.y ?? 0" :min="-20" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="%" :ariaLabel="t('Spostamento verticale al passaggio')" @update:modelValue="upHover('y', $event)" />
                </div>
              </div>
              <!-- Aspetto -->
              <p class="mb-text-[9px] mb-text-gray-500 mb-uppercase mb-mt-2">{{ t('Aspetto') }}</p>
              <div v-if="!hasTimeline">
                <label class="mps-label">{{ t('Opacità') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.hover.opacity ?? 100" :min="0" :max="100" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Opacità (%)')" @update:modelValue="upHover('opacity', $event)" />
              </div>
              <template v-if="layer.type === 'text' || layer.type === 'button'">
                <div>
                  <label class="mps-label">{{ t('Colore del testo') }}</label>
                  <FieldColor :modelValue="layer.hover.color || ''" @update:modelValue="upHover('color', $event)" />
                </div>
              </template>
              <div>
                <label class="mps-label">{{ t('Sfondo') }}</label>
                <FieldColor :modelValue="layer.hover.bgColor || ''" @update:modelValue="upHover('bgColor', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Bordo') }}</label>
                <FieldColor :modelValue="layer.hover.borderColor || ''" @update:modelValue="upHover('borderColor', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Raggio') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.hover.borderRadius ?? ''" :min="0" :max="200" :step="1" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Raggio al passaggio')" @update:modelValue="upHover('borderRadius', $event)" />
              </div>
              <!-- Filtri hover -->
              <p v-if="!hasTimeline" class="mb-text-[9px] mb-text-gray-500 mb-uppercase mb-mt-2">{{ t('Filtri') }}</p>
              <div v-if="!hasTimeline" class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">{{ t('Sfocatura') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.blur ?? 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="px" :ariaLabel="t('Sfocatura al passaggio')" @update:modelValue="upHover('blur', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Luminosità') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.brightness ?? 100" :min="0" :max="200" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="true" unit="%" :ariaLabel="t('Luminosità (%)')" @update:modelValue="upHover('brightness', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Scala di grigi') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.grayscale ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="%" :ariaLabel="t('Scala di grigi (%)')" @update:modelValue="upHover('grayscale', $event)" />
                </div>
              </div>
              <!-- Transizione -->
              <p class="mb-text-[9px] mb-text-gray-500 mb-uppercase mb-mt-2">{{ t('Transizione') }}</p>
              <div>
                <label class="mps-label">{{ t('Durata') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.hover.duration ?? 300" :min="100" :max="1000" :step="50" :defaultValue="300" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata (ms)')" @update:modelValue="upHover('duration', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Andamento') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.hover.easing || 'ease'" :options="OPTS_EASING" @update:model-value="upHover('easing', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Cursore') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.hover.cursor || ''" :options="OPTS_HOVER_CURSOR" @update:model-value="upHover('cursor', $event)" />
              </div>
            </template>

            <!-- Mask / Clip-Path: solo senza linea del tempo (lì la maschera sta nei fotogrammi chiave) -->
            <template v-if="!hasTimeline">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Maschera') }}</p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.mask" @change="toggleMask($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Maschera attiva') }}</span>
            </label>
            <template v-if="layer.mask">
              <div>
                <label class="mps-label">{{ t('Modello') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.mask.preset || 'custom'" :options="OPTS_MASK_PRESET" @update:model-value="applyMaskPreset($event)" />
              </div>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">{{ t('Alto') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.mask.top ?? 0" :min="0" :max="100" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="%" :ariaLabel="t('Maschera dall’alto')" @update:modelValue="upMask('top', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Destra') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.mask.right ?? 0" :min="0" :max="100" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="%" :ariaLabel="t('Maschera da destra')" @update:modelValue="upMask('right', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Basso') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.mask.bottom ?? 0" :min="0" :max="100" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="%" :ariaLabel="t('Maschera dal basso')" @update:modelValue="upMask('bottom', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Sinistra') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.mask.left ?? 0" :min="0" :max="100" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="%" :ariaLabel="t('Maschera da sinistra')" @update:modelValue="upMask('left', $event)" />
                </div>
              </div>
            </template>
            </template>

            <!-- Ken Burns per Layer -->
            <template v-if="layer.type === 'image'">
              <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
              <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Zoom lento (Ken Burns)') }}</p>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="!!layer.kenBurns" @change="toggleKenBurns($event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">{{ t('Attivo') }}</span>
              </label>
              <template v-if="layer.kenBurns">
                <div>
                  <label class="mps-label">{{ t('Tipo') }}</label>
                  <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.kenBurns.type || 'zoomIn'" :options="OPTS_KENBURNS_TYPE" @update:model-value="upKenBurns('type', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Intensità') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.kenBurns.intensity ?? 20" :min="5" :max="50" :step="5" :defaultValue="20" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Intensità (%)')" @update:modelValue="upKenBurns('intensity', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Durata') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.kenBurns.duration ?? 5000" :min="1000" :max="20000" :step="500" :defaultValue="5000" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata (ms)')" @update:modelValue="upKenBurns('duration', $event)" />
                </div>
              </template>
            </template>

            <!-- Motion Path -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase mb-flex mb-items-center mb-gap-1">{{ t('Percorso') }}<InfoTip :testo="t('Il livello corre lungo un tracciato partendo da dove è posato. Sulla tela il tracciato si vede tratteggiato quando il livello è scelto: disegnalo coi clic, poi trascina i punti per correggerlo.')" :titolo="t('Percorso')" /></p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.motionPath" @change="toggleMotionPath($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Anima lungo percorso') }}</span>
            </label>
            <template v-if="layer.motionPath">
              <!-- 1.4.1473: il tracciato si disegna sulla tela e si prova lì (prima cinque forme fisse o un «d» SVG a mano) -->
              <div class="mps-percorso-azioni">
                <button type="button" class="mps-percorso-btn" :class="{ 'is-on': disegnoPercorso }" :aria-pressed="disegnoPercorso ? 'true' : 'false'" @click="$emit('disegna-percorso')"><span aria-hidden="true" v-html="ICO_MATITA"></span>{{ t('Disegna sulla tela') }}</button>
                <button type="button" class="mps-percorso-btn" :disabled="!layer.motionPath.path" @click="$emit('prova-percorso')"><span aria-hidden="true" v-html="ICO_PLAY"></span>{{ t('Prova il percorso') }}</button>
              </div>
              <div>
                <label class="mps-label">{{ t('Forma del percorso') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.motionPath.preset || 'circle'" :options="OPTS_MOTION_PRESET" @update:model-value="applyMotionPreset($event)" />
              </div>
              <label v-if="Array.isArray(layer.motionPath.punti) && layer.motionPath.punti.length > 2" class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="!!layer.motionPath.dritte" @change="upDritte($event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">{{ t('Linee dritte') }}</span>
              </label>
              <template v-if="layer.motionPath.preset === 'custom'">
                <div>
                  <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Tracciato SVG') }}<InfoTip :testo="t('Il tracciato scritto come l’attributo «d» di un percorso SVG, in pixel della tela del Computer (per esempio M0,0 C50,0 50,100 100,100). Più semplice disegnarlo sulla tela.')" :titolo="t('Tracciato SVG')" /></label>
                  <textarea :value="layer.motionPath.path" @input="upMotionPath('path', $event.target.value)" rows="2"
                    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-[10px] mb-text-gray-900 mb-resize-y mb-font-mono" placeholder="M0,0 C50,0 50,100 100,100"></textarea>
                </div>
              </template>
              <div>
                <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Dimensione del percorso') }}<InfoTip :testo="t('Ingrandisce o rimpicciolisce il tracciato: 100% è com’è disegnato, 200% il doppio.')" :titolo="t('Dimensione del percorso')" /></label>
                <NumberScrubber theme="dark" :modelValue="layer.motionPath.scala ?? 100" :min="10" :max="400" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Dimensione del percorso')" @update:modelValue="upMotionPath('scala', $event === 100 ? undefined : $event)" />
              </div>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="!!layer.motionPath.adatta" @change="upMotionPath('adatta', $event.target.checked || undefined)" class="mb-rounded" />
                <span class="mps-label mb-mb-0 mb-flex mb-items-center mb-gap-1">{{ t('Segue la larghezza dello slider') }}<InfoTip :testo="t('Il tracciato si allarga e si stringe con lo slider: sul telefono resta in proporzione a come lo vedi sul Computer. Spento, ha sempre gli stessi pixel della tela e sugli schermi stretti sembra più grande.')" :titolo="t('Segue la larghezza dello slider')" /></span>
              </label>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">{{ t('Durata') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.motionPath.duration ?? 3000" :min="500" :max="120000" :step="500" :defaultValue="3000" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata (ms)')" @update:modelValue="upMotionPath('duration', $event)" />
                </div>
                <div>
                  <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Ritardo') }}<InfoTip :testo="t('Quanto aspetta il percorso prima di partire, dopo l’ingresso del livello.')" :titolo="t('Ritardo del percorso')" /></label>
                  <NumberScrubber theme="dark" :modelValue="layer.motionPath.ritardo ?? 0" :min="0" :max="60000" :step="100" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Ritardo del percorso')" @update:modelValue="upMotionPath('ritardo', $event || undefined)" />
                </div>
              </div>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="layer.motionPath.autoRotate" @change="upMotionPath('autoRotate', $event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">{{ t('Ruota lungo il percorso') }}</span>
              </label>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="layer.motionPath.loop !== false" @change="upMotionPath('loop', $event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">{{ t('Ripeti') }}</span>
              </label>
              <div>
                <label class="mps-label">{{ t('Andamento') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.motionPath.easing || 'linear'" :options="EASING_GROUPS" @update:model-value="upMotionPath('easing', $event)" />
              </div>
            </template>

            <!-- Parallax Layer -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Parallasse') }}</p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.parallax" @change="toggleParallax($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Parallasse attiva') }}</span>
            </label>
            <!-- 1.4.1472: stava in cima alla scheda e non diceva che agisce solo con la Parallasse dello slider accesa -->
            <div v-if="!layer.parallax && (parallasseSlider || layer.parallaxDepth > 0)">
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Profondità con la parallasse dello slider') }}<InfoTip :testo="parallasseSlider ? t('Con la Parallasse dello slider accesa (Stile della tile) il livello si muove col tipo e l’intensità dello slider, di più quanto più è profondo. 0 = fermo. Con «Parallasse attiva» qui sopra vale invece la parallasse propria del livello.') : t('Non agisce: la Parallasse dello slider è spenta (Stile della tile, «Parallasse»). Accendila, o usa «Parallasse attiva» qui sopra, che agisce da sola.')" :titolo="t('Profondità con la parallasse dello slider')" /></label>
              <NumberScrubber theme="dark" :modelValue="layer.parallaxDepth || 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Profondità con la parallasse dello slider')" @update:modelValue="up('parallaxDepth', $event)" />
            </div>
            <template v-if="layer.parallax">
              <div>
                <label class="mps-label">{{ t('Tipo') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.parallax.type || 'mouse'" :options="OPTS_PARALLAX_TYPE" @update:model-value="upParallax('type', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Profondità') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.parallax.depth ?? 5" :min="1" :max="20" :step="1" :defaultValue="5" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Profondità')" @update:modelValue="upParallax('depth', $event)" />
              </div>
              <template v-if="layer.parallax.type === 'mouse' || layer.parallax.type === 'both'">
                <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                  <input type="checkbox" :checked="layer.parallax.tilt3d" @change="upParallax('tilt3d', $event.target.checked)" class="mb-rounded" />
                  <span class="mb-text-[11px] mb-text-gray-400">{{ t('Inclinazione 3D') }}</span>
                </label>
              </template>
              <template v-if="layer.parallax.type === 'scroll' || layer.parallax.type === 'both'">
                <div>
                  <label class="mps-label">{{ t('Velocità con lo scorrimento') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.parallax.scrollSpeed ?? 0.5" :min="0.1" :max="2" :step="0.1" :defaultValue="0.5" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Velocità con lo scorrimento')" @update:modelValue="upParallax('scrollSpeed', $event)" />
                </div>
              </template>
            </template>

            <!-- Movimento con lo scorrimento -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase mb-flex mb-items-center mb-gap-1">{{ t('Movimento con lo scorrimento') }}<InfoTip :testo="t('Con «Timeline a scorrimento» accesa conta il tratto in cui lo slider resta fermo in cima; se no, l’uscita dello slider dalla finestra. Spostamenti in px della tela del computer.')" :titolo="t('Movimento con lo scorrimento')" /></p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.scrollMove" @change="toggleScrollMove($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Si muove mentre la pagina scorre') }}</span>
            </label>
            <template v-if="layer.scrollMove">
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">{{ t('Inizio del tratto') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.scrollMove.inizio ?? 0" :min="0" :max="100" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="true" unit="%" :ariaLabel="t('Inizio del tratto')" @update:modelValue="upScrollMove('inizio', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Fine del tratto') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.scrollMove.fine ?? 100" :min="0" :max="100" :step="1" :defaultValue="100" emitAs="number" :sliderOnFocus="true" unit="%" :ariaLabel="t('Fine del tratto')" @update:modelValue="upScrollMove('fine', $event)" />
                </div>
              </div>
              <div>
                <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Andamento') }}<InfoTip :testo="t('Morbido: fra una tappa e l’altra il livello parte e arriva piano. Costante: va a velocità uniforme e la scala cresce in proporzione, così uno zoom che attraversa più tappe non rallenta a ogni tappa.')" :titolo="t('Andamento')" /></label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.scrollMove.andamento || 'morbido'" :options="OPTS_ANDAMENTO" @update:model-value="upScrollMove('andamento', $event === 'morbido' ? undefined : $event)" />
              </div>
              <!-- Le tappe del percorso (1.4.1352): tutte, col punto in cui stanno. Prima si vedevano solo la prima e
                   l'ultima («All'inizio», «Alla fine», 30 cursori): il momento in cui un livello si muove non si vedeva. -->
              <div class="mps-tappe">
                <div class="mb-flex mb-items-center mb-justify-between">
                  <label class="mps-label mb-mb-0 mb-flex mb-items-center mb-gap-1">{{ t('Tappe del percorso') }}<InfoTip :testo="t('Ogni tappa dice com’è il livello a quel punto del suo tratto (0% = inizio, 100% = fine); fra una tappa e l’altra il livello passa da un valore all’altro. Clicca una tappa per vederla sulla tela e cambiarla: la tela va a quel punto dello scorrimento.')" :titolo="t('Tappe del percorso')" /></label>
                  <span class="mb-text-[10px] mb-text-gray-500">{{ tappeSM.length }}</span>
                </div>
                <!-- il tratto: i punti delle tappe e, con l'anteprima accesa, dov'è lo scorrimento -->
                <div class="mps-tappe-barra" role="presentation">
                  <span v-for="(tp, i) in tappeSM" :key="'b' + i" :class="['mps-tappe-punto', { 'is-on': i === tappaScelta }]" :style="{ left: tp.p + '%' }" :title="fmtP(tp.p) + '%'" @click="scegliTappa(i)"></span>
                  <span v-if="scrollPreview != null" class="mps-tappe-ora" :style="{ left: delLivello(layer.scrollMove, scrollPreview) + '%' }"></span>
                </div>
                <div class="mb-space-y-0.5">
                  <button v-for="(tp, i) in tappeSM" :key="'r' + i" type="button" :class="['mps-tappa', { 'is-on': i === tappaScelta }]" @click="scegliTappa(i)">
                    <span class="mps-tappa-p">{{ fmtP(tp.p) }}%</span>
                    <span class="mps-tappa-v">{{ riassuntoTappa(tp) }}</span>
                  </button>
                </div>
                <div class="mb-flex mb-gap-1">
                  <button type="button" class="mps-tappe-btn" @click="aggiungiTappa">+ {{ t('Tappa') }}</button>
                  <button type="button" class="mps-tappe-btn" :disabled="tappeSM.length <= 2" @click="eliminaTappa">{{ t('Elimina tappa') }}</button>
                </div>
                <template v-if="tappaCorrente">
                  <div>
                    <label class="mps-label">{{ t('A che punto del tratto') }}</label>
                    <NumberScrubber theme="dark" :modelValue="tappaCorrente.p" :min="0" :max="100" :step="0.5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('A che punto del tratto')" @update:modelValue="upTappa('p', $event)" />
                  </div>
                  <div class="mb-grid mb-grid-cols-1 mb-gap-2">
                    <div v-for="c in campiTappa" :key="c.k">
                      <label class="mps-label mb-flex mb-items-center mb-justify-between">{{ t(c.l) }}<button type="button" class="mps-tappe-x" :title="t('Non muovere più questa proprietà')" :aria-label="t('Non muovere più questa proprietà')" @click="togliChiave(c.k)">&times;</button></label>
                      <!-- 1.4.1459: un valore scritto tiene fino a 3 decimali (rotazioni di 4,8°, maschere a 14,286%): il passo vale per frecce e trascinamento -->
                      <NumberScrubber theme="dark" :modelValue="tappaCorrente.v[c.k] ?? c.d" :min="c.min" :max="c.max" :step="c.step" :scriviDecimali="3" :defaultValue="c.d" emitAs="number" :sliderOnFocus="false" :unit="c.u" :ariaLabel="t(c.l)" @update:modelValue="upTappa(c.k, $event)" />
                    </div>
                  </div>
                  <FieldSelect v-if="campiAggiungibili.length" ui="dropdown" size="compact" theme="dark" :model-value="''" :options="opzioniAggiungi" @update:model-value="aggiungiChiave" />
                </template>
              </div>
            </template>
        </template>

      </div>
    </template>

    <!-- No selection -->
    <div v-else class="mb-p-4 mb-text-xs mb-text-gray-500 mb-text-center mb-mt-8">
      {{ t('Seleziona un livello per modificarne le proprietà') }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import IconPicker from './IconPicker.vue';
import iconsSvg from './uikitIconsSvg.js';
import { svgIcona } from './iconeMie.js';
import FieldColor from '@/components/Builder/fields/FieldColor.vue';
import FieldSelect from '../Builder/fields/FieldSelect.vue';
import NumberScrubber from '../Builder/fields/NumberScrubber.vue';
import KeyframeProperties from './KeyframeProperties.vue';
import InfoTip from '../Builder/InfoTip.vue';
import { useContenutiSlider, FOTO_ARTICOLO, LINK_ARTICOLO } from './datiArticolo.js';
import FieldSfumature from './FieldSfumature.vue';
import FieldOmbre from './FieldOmbre.vue';
import FieldRitaglio from './FieldRitaglio.vue';
import FieldTestoLivello from './FieldTestoLivello.vue';
import FieldColoriDisegno from './FieldColoriDisegno.vue';
import FieldFontFamily from '../Builder/fields/FieldFontFamily.vue';
import FieldLink from '../Builder/fields/FieldLink.vue';
import { eDisegno } from './disegni.js';
// Curve di ingresso, uscita, loop e percorso: le stesse della timeline (il PHP le traduce in cubic-bezier,
// easing_to_css). La curva dell'hover resta fra le 5 di base, che il PHP accetta per l'hover.
import { defaultTimeline, EASING_GROUPS } from './timelineUtils.js';
import { dDaPunti } from './percorso.js';
import lucideIconsLP from './lucideIconsSvg.js';
import { t } from '@/i18n';
import { nomeLivello, tappeDi, chiaviUsate, valoreSM, delLivello, dellaScena, SM_CHIAVI, statiDellaSlide, gruppiLivelli, nomeGruppo } from './livelloUtils.js';

// Slide dagli articoli (1.4.1457): i pulsanti dei dati dell'articolo si vedono solo quando sono accese
const contenuti = useContenutiSlider();
// 1.4.1459: i lati in italiano (erano Top, Right, Bottom, Left) e il pulsante lega/slega con l'icona del set (era un'emoji)
const NOME_LATO = { Top: 'Sopra', Right: 'Destra', Bottom: 'Sotto', Left: 'Sinistra' };
const ICO_LEGATI = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>';
const ICO_SLEGATI = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18.84 12.25 1.72-1.71h-.02a5 5 0 0 0-.12-7.07 5 5 0 0 0-6.95 0l-1.72 1.71"/><path d="m5.17 11.75-1.71 1.71a5 5 0 0 0 .12 7.07 5 5 0 0 0 6.95 0l1.71-1.71"/><path d="M8 2v3"/><path d="M2 8h3"/><path d="M16 19v3"/><path d="M19 16h3"/></svg>';
const props = defineProps({
  layer: { type: Object, default: null },
  selectedKeyframeId: { type: String, default: null },
  activeBreakpoint: { type: String, default: 'desktop' },
  slideLayers: { type: Array, default: () => [] },
  // Anteprima dello scorrimento dell'editor (0–100 o null): le tappe del movimento la leggono e la spostano.
  scrollPreview: { type: Number, default: null },
  // La modalità dell'editor (1.4.1426): disegno · animazione · interazioni, decide le schede.
  modo: { type: String, default: 'disegno' },
  // La Parallasse dello slider è accesa (1.4.1472): la profondità numerica del livello agisce solo con lei
  parallasseSlider: { type: Boolean, default: false },
  // Il percorso si sta disegnando sulla tela (1.4.1473)
  disegnoPercorso: { type: Boolean, default: false },
  // Tutte le slide, quella scelta e i livelli su tutte le slide (1.4.1475): bersagli di «Mostra o nasconde un livello»
  // e numero massimo di «Vai alla slide…»
  tutteLeSlide: { type: Array, default: () => [] },
  indiceSlide: { type: Number, default: 0 },
  livelliGlobali: { type: Array, default: () => [] },
});

// Il nome che il livello avrebbe senza «Nome del livello» (testo, file, id parlante, tipo): segnaposto del campo.
const nomeSuggerito = computed(() => (props.layer ? nomeLivello({ ...props.layer, name: '' }) : ''));

// Il gruppo che il livello avrebbe da solo (segnaposto del campo «Gruppo») e i nomi dei gruppi della slide (1.4.1424)
const gruppoAutomatico = computed(() => {
  const id = props.layer?.id;
  if (!id) return '';
  const g = gruppiLivelli((props.slideLayers || []).map((l) => (l.id === id ? { ...l, gruppo: '' } : l))).find((x) => x.livelli.some((l) => l.id === id));
  return g ? nomeGruppo(g) : '';
});
const nomiGruppi = computed(() => [...new Set(gruppiLivelli(props.slideLayers || []).map((g) => nomeGruppo(g)))]);

// «Mostra o nasconde un livello» (1.4.1475): i livelli di questa slide, poi delle altre, poi quelli su tutte le slide
const targetLayerOptions = computed(() => {
  const cur = props.layer?.id;
  const voce = (l) => ({ value: l.id, label: targetLayerLabel(l) });
  const gruppi = [];
  const slide = props.tutteLeSlide && props.tutteLeSlide.length ? props.tutteLeSlide : [{ layers: props.slideLayers || [] }];
  const ordine = slide.map((s, i) => i).sort((a, b) => (a === props.indiceSlide ? -1 : b === props.indiceSlide ? 1 : a - b));
  for (const i of ordine) {
    const opts = (slide[i].layers || []).filter((l) => l?.id && l.id !== cur).map(voce);
    if (opts.length) gruppi.push({ label: i === props.indiceSlide ? t('Questa slide (%d)').replace('%d', i + 1) : t('Slide %d').replace('%d', i + 1), options: opts });
  }
  const glob = (props.livelliGlobali || []).filter((l) => l?.id && l.id !== cur).map(voce);
  if (glob.length) gruppi.push({ label: t('Su tutte le slide'), options: glob });
  // un bersaglio che non c'è più resta leggibile, per cambiarlo
  const t0 = props.layer?.action?.target;
  const presente = !t0 || gruppi.some((g) => g.options.some((o) => o.value === t0));
  return [{ value: '', label: t('— Scegli un livello —') }, ...gruppi, ...(presente ? [] : [{ value: t0, label: t('Livello che non c’è più') }])];
});

function fmt(v) {
  if (v === 'auto' || v === undefined || v === null) return v ?? '';
  const n = Number(v);
  if (!Number.isFinite(n)) return v;
  return (Math.round(n * 10) / 10).toString();
}

// il nome del pannello Livelli (testo senza segni HTML, nome del file, nome dato), col tipo fra parentesi
function targetLayerLabel(l) {
  const nome = nomeLivello(l);
  const corto = nome.length > 40 ? nome.slice(0, 40) + '…' : nome;
  const tipo = NOMI_TIPO[l.type] || '';
  return tipo && tipo !== corto ? `${corto} (${tipo.toLowerCase()})` : corto;
}

const emit = defineEmits(['update', 'update-keyframe', 'set-scroll-preview', 'disegna-percorso', 'prova-percorso']);

const hasTimeline = computed(() => {
  return !!(props.layer?.timeline?.keyframes?.length);
});

const selectedKeyframe = computed(() => {
  if (!hasTimeline.value || !props.selectedKeyframeId) return null;
  return props.layer.timeline.keyframes.find(kf => kf.id === props.selectedKeyframeId) || null;
});

function setAnimMode(mode) {
  chiediSemplice.value = false;
  if (mode === 'timeline' && !hasTimeline.value) {
    // Attiva timeline con valori default basati sulla posizione corrente del layer
    up('timeline', defaultTimeline(props.layer));
  } else if (mode === 'simple' && hasTimeline.value) {
    // 1.4.1472: prima la toglieva senza chiedere, coi suoi fotogrammi chiave
    chiediSemplice.value = true;
  }
}
const chiediSemplice = ref(false);
function confermaSemplice() {
  chiediSemplice.value = false;
  up('timeline', null);
}
watch(() => props.layer?.id, () => { chiediSemplice.value = false; });

// L'Uscita con la linea del tempo (1.4.1472): finché non si cambia è la dissolvenza rapida di sempre (400 ms); al primo
// cambio si accende «timeline.uscita» e si scrivono effetto, durata, ritardo e andamento del livello (le chiavi di sempre
// dell'Uscita), partendo da quello che si vedeva.
const uscitaTl = computed(() => {
  const l = props.layer;
  if (l?.timeline?.uscita) {
    return { effetto: l.animOut || 'fadeOutDown', durata: l.animOutDuration ?? 600, ritardo: l.animOutDelay ?? 0, andamento: l.animEasing || 'ease' };
  }
  return { effetto: 'fadeOut', durata: 400, ritardo: 0, andamento: 'ease' };
});
function upUscitaTl(k, v) {
  const l = props.layer;
  if (!l?.timeline) return;
  const u = { ...uscitaTl.value, [k]: v };
  if (!l.timeline.uscita) up('timeline', { ...l.timeline, uscita: true });
  up('animOut', u.effetto);
  up('animOutDuration', u.durata);
  up('animOutDelay', u.ritardo);
  up('animEasing', u.andamento);
}

const showIconPicker = ref(false);

const activeTab = ref('Contenuto');

// Il nome del livello e dello schermo come li legge chi usa l'editor (17 nov 2026: «Livello Button», «Visibile su mobile»)
const NOMI_TIPO = { text: t('Testo'), button: t('Pulsante'), image: t('Immagine'), video: t('Video'), icon: t('Icona'),
  shape: t('Forma'), audio: t('Audio') };
const nomeTipo = computed(() => NOMI_TIPO[props.layer?.type] || (props.layer?.type || ''));
// 1.4.1475: i nomi della barra degli schermi (prima «Visibile su Desktop / Mobile»)
const NOMI_SCHERMO = { desktop: t('Computer'), notebook: t('Notebook'), tablet: t('Tablet'), mobile: t('Telefono') };
const nomeSchermo = computed(() => NOMI_SCHERMO[props.activeBreakpoint] || props.activeBreakpoint);
// Le schede secondo la modalità dell'editor (1.4.1426, come Disegno/Animazione/Azioni di Slider Revolution 7):
// Disegno = Contenuto, Posizione, Stile; Animazione e Interazioni = un pannello solo. Prima le quattro schede insieme,
// con le interazioni in fondo al Contenuto.
const SCHEDE = { disegno: ['Contenuto', 'Posizione', 'Stile'], animazione: ['Animazione'], interazioni: ['Interazioni'] };
const tabs = computed(() => SCHEDE[props.modo] || SCHEDE.disegno);
const NOMI_SCHEDE = { Contenuto: t('Contenuto'), Posizione: t('Posizione'), Stile: t('Stile'), Animazione: t('Animazione'), Interazioni: t('Interazioni') };
watch(() => props.modo, () => { if (!tabs.value.includes(activeTab.value)) activeTab.value = tabs.value[0]; }, { immediate: true });

// Responsive: risolve il valore effettivo per il breakpoint attivo
function rv(key) {
  if (!props.layer) return undefined;
  const bp = props.activeBreakpoint;
  if (bp !== 'desktop') {
    const chain = ['notebook', 'tablet', 'mobile'];
    const idx = chain.indexOf(bp);
    for (let i = idx; i >= 0; i--) {
      const ov = props.layer.responsive?.[chain[i]];
      if (ov && ov[key] !== undefined && ov[key] !== null) return ov[key];
    }
  }
  return props.layer[key];
}

function isOverridden(key) {
  const bp = props.activeBreakpoint;
  if (bp === 'desktop') return false;
  return props.layer?.responsive?.[bp]?.[key] !== undefined && props.layer?.responsive?.[bp]?.[key] !== null;
}

function clearOverride(key) {
  const bp = props.activeBreakpoint;
  if (bp === 'desktop' || !props.layer?.responsive?.[bp]) return;
  delete props.layer.responsive[bp][key];
}

const hasAnyOverride = computed(() => {
  const bp = props.activeBreakpoint;
  if (bp === 'desktop') return false;
  const ov = props.layer?.responsive?.[bp];
  return ov && Object.keys(ov).length > 0;
});

function clearAllOverrides() {
  const bp = props.activeBreakpoint;
  if (bp === 'desktop' || !props.layer?.responsive) return;
  props.layer.responsive[bp] = null;
}

const animationsInGroups = [
  { label: t('Base'), options: [
    { value: 'none', label: t('Nessuna') },
    { value: 'fadeIn', label: t('Dissolvenza') },
  ]},
  { label: t('Dissolvenza'), options: [
    { value: 'fadeInUp', label: t('Dissolvenza dal basso') },
    { value: 'fadeInDown', label: t('Dissolvenza dall’alto') },
    { value: 'fadeInLeft', label: t('Dissolvenza da sinistra') },
    { value: 'fadeInRight', label: t('Dissolvenza da destra') },
  ]},
  { label: t('Scivola'), options: [
    { value: 'slideInLeft', label: t('Scivola da sinistra') },
    { value: 'slideInRight', label: t('Scivola da destra') },
    { value: 'slideInUp', label: t('Scivola dal basso') },
    { value: 'slideInDown', label: t('Scivola dall’alto') },
    { value: 'slideShortFromTop', label: t('Breve dall’alto') },
    { value: 'slideShortFromBottom', label: t('Breve dal basso') },
    { value: 'slideShortFromLeft', label: t('Breve da sinistra') },
    { value: 'slideShortFromRight', label: t('Breve da destra') },
    { value: 'smoothSlideFromBottom', label: t('Morbida dal basso') },
    { value: 'smoothSlideFromTop', label: t('Morbida dall’alto') },
    { value: 'smoothSlideFromLeft', label: t('Morbida da sinistra') },
    { value: 'smoothSlideFromRight', label: t('Morbida da destra') },
  ]},
  { label: t('Inclinata'), options: [
    { value: 'skewFromLeft', label: t('Inclinata da sinistra') },
    { value: 'skewFromRight', label: t('Inclinata da destra') },
    { value: 'skewShortFromLeft', label: t('Inclinata breve da sinistra') },
    { value: 'skewShortFromRight', label: t('Inclinata breve da destra') },
  ]},
  { label: t('Ribaltata 3D'), options: [
    { value: 'flipFromTop', label: t('Ribaltata dall’alto') },
    { value: 'flipFromBottom', label: t('Ribaltata dal basso') },
    { value: 'flipFromLeft', label: t('Ribaltata da sinistra') },
    { value: 'flipFromRight', label: t('Ribaltata da destra') },
  ]},
  { label: t('Rotazione'), options: [
    { value: 'rotateIn', label: t('Ruota entrando') },
    { value: 'rotateInFromBottom', label: t('Ruota dal basso') },
    { value: 'rotate3D', label: t('Ruota in 3D') },
    { value: 'rotateInFromLeft', label: t('Ruota da sinistra') },
    { value: 'rotateInFromRight', label: t('Ruota da destra') },
  ]},
  { label: t('Zoom e rimbalzo'), options: [
    { value: 'zoomIn', label: t('Si ingrandisce') },
    { value: 'bounceIn', label: t('Rimbalzo') },
    { value: 'popUpSmooth', label: t('Spunta morbida') },
    { value: 'popUpBack', label: t('Spunta e torna indietro') },
    { value: 'bounceInUp', label: t('Rimbalzo dal basso') },
    { value: 'bounceInDown', label: t('Rimbalzo dall’alto') },
  ]},
  { label: t('Maschera'), options: [
    { value: 'maskFromLeft', label: t('Si scopre da sinistra') },
    { value: 'maskFromRight', label: t('Si scopre da destra') },
    { value: 'maskFromTop', label: t('Si scopre dall’alto') },
    { value: 'maskFromBottom', label: t('Si scopre dal basso') },
    { value: 'maskZoomOut', label: t('Si scopre dal centro con zoom') },
    { value: 'maskCenter', label: t('Si scopre dal centro') },
  ]},
];

const animationsOutGroups = [
  { label: t('Base'), options: [
    { value: 'none', label: t('Nessuna') },
    { value: 'fadeOut', label: t('Dissolvenza') },
  ]},
  { label: t('Dissolvenza'), options: [
    { value: 'fadeOutUp', label: t('Dissolvenza verso l’alto') },
    { value: 'fadeOutDown', label: t('Dissolvenza verso il basso') },
    { value: 'fadeOutLeft', label: t('Dissolvenza verso sinistra') },
    { value: 'fadeOutRight', label: t('Dissolvenza verso destra') },
  ]},
  { label: t('Scivola'), options: [
    { value: 'slideOutLeft', label: t('Scivola verso sinistra') },
    { value: 'slideOutRight', label: t('Scivola verso destra') },
    { value: 'slideOutUp', label: t('Scivola verso l’alto') },
    { value: 'slideOutDown', label: t('Scivola verso il basso') },
    { value: 'slideShortOutTop', label: t('Breve verso l’alto') },
    { value: 'slideShortOutBottom', label: t('Breve verso il basso') },
    { value: 'slideShortOutLeft', label: t('Breve verso sinistra') },
    { value: 'slideShortOutRight', label: t('Breve verso destra') },
    { value: 'smoothSlideOutBottom', label: t('Morbida verso il basso') },
    { value: 'smoothSlideOutTop', label: t('Morbida verso l’alto') },
    { value: 'smoothSlideOutLeft', label: t('Morbida verso sinistra') },
    { value: 'smoothSlideOutRight', label: t('Morbida verso destra') },
  ]},
  { label: t('Inclinata'), options: [
    { value: 'skewOutLeft', label: t('Inclinata verso sinistra') },
    { value: 'skewOutRight', label: t('Inclinata verso destra') },
    { value: 'skewShortOutLeft', label: t('Inclinata breve verso sinistra') },
    { value: 'skewShortOutRight', label: t('Inclinata breve verso destra') },
  ]},
  { label: t('Ribaltata 3D'), options: [
    { value: 'flipOutTop', label: t('Ribaltata verso l’alto') },
    { value: 'flipOutBottom', label: t('Ribaltata verso il basso') },
    { value: 'flipOutLeft', label: t('Ribaltata verso sinistra') },
    { value: 'flipOutRight', label: t('Ribaltata verso destra') },
  ]},
  { label: t('Rotazione'), options: [
    { value: 'rotateOut', label: t('Ruota uscendo') },
    { value: 'rotateOutToBottom', label: t('Ruota verso il basso') },
    { value: 'rotateOut3D', label: t('Ruota in 3D') },
    { value: 'rotateOutToLeft', label: t('Ruota verso sinistra') },
    { value: 'rotateOutToRight', label: t('Ruota verso destra') },
  ]},
  { label: t('Zoom e rimbalzo'), options: [
    { value: 'zoomOut', label: t('Si rimpicciolisce') },
    { value: 'bounceOut', label: t('Rimbalzo') },
    { value: 'popOutSmooth', label: t('Sparisce morbida') },
    { value: 'popOutBack', label: t('Si gonfia e sparisce') },
    { value: 'bounceOutUp', label: t('Rimbalzo verso l’alto') },
    { value: 'bounceOutDown', label: t('Rimbalzo verso il basso') },
  ]},
  { label: t('Maschera'), options: [
    { value: 'maskOutLeft', label: t('Si copre verso sinistra') },
    { value: 'maskOutRight', label: t('Si copre verso destra') },
    { value: 'maskOutTop', label: t('Si copre verso l’alto') },
    { value: 'maskOutBottom', label: t('Si copre verso il basso') },
    { value: 'maskZoomIn', label: t('Si chiude al centro con zoom') },
    { value: 'maskOutCenter', label: t('Si chiude al centro') },
  ]},
];

const loopAnimGroups = [
  { label: t('Nessuno'), options: [{ value: 'none', label: t('Nessuno') }] },
  { label: t('Pendolo'), options: [
    { value: 'pendulum', label: t('Pendolo') },
    { value: 'pendulumBelow', label: t('Pendolo basso') },
    { value: 'pendulumAbove', label: t('Pendolo alto') },
    { value: 'pendulumLeft', label: t('Pendolo a sinistra') },
    { value: 'pendulumRight', label: t('Pendolo a destra') },
  ]},
  { label: t('Onda'), options: [
    { value: 'waveSmallLeft', label: t('Onda piccola a sinistra') },
    { value: 'waveSmallRight', label: t('Onda piccola a destra') },
    { value: 'waveBigLeft', label: t('Onda grande a sinistra') },
    { value: 'waveBigRight', label: t('Onda grande a destra') },
  ]},
  { label: t('Vibrazione'), options: [
    { value: 'wiggleY', label: t('Vibrazione verticale') },
    { value: 'wiggleX', label: t('Vibrazione orizzontale') },
    { value: 'wiggle3D', label: t('Vibrazione 3D') },
    { value: 'crazyWiggle', label: t('Vibrazione scatenata') },
  ]},
  { label: t('Rotazione'), options: [
    { value: 'spinCW', label: t('Gira in senso orario') },
    { value: 'spinCCW', label: t('Gira in senso antiorario') },
  ]},
  { label: t('Effetto'), options: [
    { value: 'blinkLoop', label: t('Lampeggio') },
    { value: 'floatLoop', label: t('Galleggiamento') },
    { value: 'pulseLoop', label: t('Pulsazione') },
    { value: 'breathLoop', label: t('Respiro') },
    { value: 'slideHLoop', label: t('Scivola avanti e indietro') },
    { value: 'windLoop', label: t('Vento (piega dal basso)') },
    { value: 'hoverLoop', label: t('Fluttua piano') },
    // 1.4.1435: nei temi c'erano (scritti «flicker», «wander», «drift») ma il motore non li conosceva
    { value: 'flickerLoop', label: t('Sfarfallio (come una fiamma)') },
    { value: 'wanderLoop', label: t('Vaga piano') },
    { value: 'driftLoop', label: t('Deriva lenta') },
  ]},
];

function up(key, val) {
  emit('update', key, val);
}

// ── «Segue il colore di uno stato» (1.4.1435) ──
// Il colore dello stato (Interazioni › «Colore dello stato») va nella variabile --mps-st-<nome>: un livello che lo segue
// salva var(--mps-st-<nome>, <colore di partenza>). Prima si poteva solo scrivere a mano quella variabile (Due mani).
const RE_STATO_COL = /^var\(\s*--mps-st-([a-z0-9_-]+)\s*,\s*(.+)\)$/i;
const statiConColore = computed(() => [...new Set((props.slideLayers || []).filter((l) => l?.action?.type === 'setState' && l.action.color && l.action.target).map((l) => String(l.action.target).toLowerCase().replace(/[^a-z0-9_-]/g, '')))].filter(Boolean));
const opzStatiColore = computed(() => [{ value: '', label: t('No, resta fisso') }, ...statiConColore.value.map((n) => ({ value: n, label: t('Lo stato «%s»').replace('%s', n) }))]);
function baseColore(k) { const v = String(props.layer?.[k] || ''); const m = RE_STATO_COL.exec(v); return m ? m[2].trim() : v; }
function statoColore(k) { const m = RE_STATO_COL.exec(String(props.layer?.[k] || '')); return m ? m[1].toLowerCase() : ''; }
function upColore(k, base) { const s = statoColore(k); up(k, s ? `var(--mps-st-${s}, ${base || 'transparent'})` : base); }
function upStatoColore(k, nome) { const base = baseColore(k); up(k, nome ? `var(--mps-st-${nome}, ${base || 'transparent'})` : base); }

// ── Comandi che nei temi erano CSS su misura (1.4.1431) ──
// Le sfumature senza «Sopra il colore della forma» coprono il colore e il gradiente semplice: quei due si nascondono.
const sfumatureCoprono = computed(() => !!(props.layer?.sfondoForma?.length && !props.layer.sfondoFormaSulColore));
// Una lista sola di ombre: un'ombra semplice resta in «boxShadow» (il formato di sempre), più ombre o una interna in «ombre»
const ombreLista = computed(() => {
  const l = props.layer;
  if (Array.isArray(l?.ombre) && l.ombre.length) return l.ombre;
  return l?.boxShadow ? [{ x: 0, y: 4, blur: 10, spread: 0, color: 'color-mix(in srgb, var(--olo-color-dark) 30%, transparent)', ...l.boxShadow, inset: false }] : [];
});
function setOmbre(lista) {
  if (!lista.length) { up('ombre', ''); up('boxShadow', null); return; }
  if (lista.length === 1 && !lista[0].inset) {
    const { inset, ...o } = lista[0]; // eslint-disable-line no-unused-vars
    up('ombre', ''); up('boxShadow', o); return;
  }
  up('boxShadow', null); up('ombre', lista);
}
// «Nasce dove si clicca»: i valori che i livelli cliccabili della slide danno agli stati
const pulisciValore = (v) => String(v || '').toLowerCase().replace(/[^a-z0-9_-]/g, '');
const opzioniSeguiClic = computed(() => {
  const out = [{ value: '', label: t('No, resta al suo posto') }];
  const visti = new Set();
  for (const l of props.slideLayers || []) {
    const a = l?.action;
    if (!a || (a.type !== 'setState' && a.type !== 'cycleState')) continue;
    const valori = a.type === 'setState' ? [a.value] : String(a.values || '').split(',');
    for (const v of valori) {
      const k = pulisciValore(v);
      if (!k || visti.has(k)) continue;
      visti.add(k);
      out.push({ value: k, label: t('Quando %s diventa «%v»').replace('%s', a.target || t('lo stato')).replace('%v', String(v).trim()) });
    }
  }
  const cur = pulisciValore(props.layer?.seguiClic);
  if (cur && !visti.has(cur)) out.push({ value: cur, label: '«' + cur + '»' });
  return out;
});

function setAction(field, value) {
  const current = props.layer?.action || { type: 'none' };
  if (field === 'type') {
    if (value === 'none') {
      up('action', null);
    } else {
      up('action', { ...current, type: value });
    }
  } else {
    up('action', { ...current, [field]: value });
  }
}

function upStato(key, val) {
  up('stato', { ...(props.layer?.stato || {}), [key]: val });
}

// «Quando si vede» (1.4.1421, utente: «che cliccando su una spia cambi il testo collegato… ho seri dubbi che si possa
// fare senza scomodare una programmazione»). Si sceglie il livello cliccabile e il pannello scrive nome e valori dello
// stato: «Quando si sceglie X» = il valore di X; «Finché non si sceglie X» = «!valore» (la spia che si spegne);
// «Prima di ogni scelta» = valore vuoto, quello di partenza di uno stato non scritto in «Stati all'avvio».
const nomeStato = (n) => String(n || '').trim().toLowerCase().replace(/[^a-z0-9_-]/g, '');
const pulsantiStato = computed(() => (props.slideLayers || []).filter((l) => l?.id && l.action?.type === 'setState' && nomeStato(l.action.target)));
const nomiStati = computed(() => Object.keys(statiDellaSlide(props.slideLayers || [])));
const opzioniQuando = computed(() => {
  const out = [{ value: '', label: t('Come scritto qui sotto') }];
  const nomi = [];
  for (const l of pulsantiStato.value) {
    const chi = l.id === props.layer?.id ? t('questo livello') : '«' + nomeLivello(l) + '»';
    out.push({ value: 'clic:' + l.id, label: t('Quando si sceglie %s').replace('%s', chi) });
    out.push({ value: 'fino:' + l.id, label: t('Finché non si sceglie %s').replace('%s', chi) });
    const n = nomeStato(l.action.target);
    if (!nomi.includes(n)) nomi.push(n);
  }
  for (const n of nomi) out.push({ value: 'prima:' + n, label: t('Prima di ogni scelta (%s)').replace('%s', n) });
  return out;
});
const quandoScelto = computed(() => {
  const st = props.layer?.stato;
  if (!st) return '';
  const n = nomeStato(st.nome);
  const v = String(st.vale ?? '').trim();
  const stessi = pulsantiStato.value.filter((l) => nomeStato(l.action.target) === n);
  for (const l of stessi) {
    const val = String(l.action.value ?? '').trim();
    if (v === val) return 'clic:' + l.id;
    if (v === '!' + val) return 'fino:' + l.id;
  }
  return v === '' && stessi.length ? 'prima:' + n : '';
});
function scegliQuando(scelta) {
  const i = String(scelta || '').indexOf(':');
  if (i < 0) return;
  const tipo = scelta.slice(0, i), chi = scelta.slice(i + 1);
  if (tipo === 'prima') {
    up('stato', { ...(props.layer?.stato || {}), nome: chi, vale: '' });
    return;
  }
  const l = pulsantiStato.value.find((x) => x.id === chi);
  if (!l) return;
  const val = String(l.action.value ?? '').trim();
  up('stato', { ...(props.layer?.stato || {}), nome: nomeStato(l.action.target), vale: (tipo === 'fino' ? '!' : '') + val });
}

function upPulisci(key, val) {
  up('pulisci', { ...(props.layer?.pulisci || { attivo: true }), [key]: val });
}

function toggleCharAnim(enabled) {
  if (enabled) {
    up('charAnim', { split: 'chars', direction: 'forward', stagger: 30 });
  } else {
    up('charAnim', null);
  }
}

function upCharAnim(key, val) {
  if (!props.layer?.charAnim) return;
  up('charAnim', { ...props.layer.charAnim, [key]: val });
}

function toggleHover(enabled) {
  if (enabled) {
    up('hover', { scale: 1, rotation: 0, rotateX: 0, rotateY: 0, skewX: 0, skewY: 0, opacity: 100, x: 0, y: 0, duration: 300, easing: 'ease', color: '', bgColor: '', borderColor: '', borderRadius: '', blur: 0, brightness: 100, grayscale: 0, cursor: '' });
  } else {
    up('hover', null);
  }
}

function upHover(key, val) {
  if (!props.layer?.hover) return;
  up('hover', { ...props.layer.hover, [key]: val });
}

// L'immagine dentro una forma (1.4.1430)
function pickShapeImage() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: t('Immagine nella forma'), multiple: false, library: { type: 'image' } });
  frame.on('select', () => up('shapeImage', frame.state().get('selection').first().toJSON().url));
  frame.open();
}
// La posizione della foto letta come punto della griglia (center top → 50% 0%), per accendere il punto giusto
const PAROLE_POS = { left: '0%', center: '50%', right: '100%', top: '0%', bottom: '100%' };
const posizioneFoto = computed(() => {
  const p = String(props.layer?.objectPosition || 'center').trim().toLowerCase().split(/\s+/);
  if (p.length === 1) p.push('center');
  let [a, b] = p;
  if (a === 'top' || a === 'bottom') [a, b] = [b, a];
  return (PAROLE_POS[a] || a) + ' ' + (PAROLE_POS[b] || b);
});
const ORIGINI = [
  { v: '0% 0%', nome: t('In alto a sinistra') }, { v: '50% 0%', nome: t('In alto al centro') }, { v: '100% 0%', nome: t('In alto a destra') },
  { v: '0% 50%', nome: t('A sinistra') }, { v: '50% 50%', nome: t('Al centro') }, { v: '100% 50%', nome: t('A destra') },
  { v: '0% 100%', nome: t('In basso a sinistra') }, { v: '50% 100%', nome: t('In basso al centro') }, { v: '100% 100%', nome: t('In basso a destra') },
];

function pickImage() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona immagine', multiple: false, library: { type: 'image' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    up('imageSrc', url);
  });
  frame.open();
}

function pickVideo() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona video', multiple: false, library: { type: 'video' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    up('videoSrc', url);
  });
  frame.open();
}

function pickAudio() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona audio', multiple: false, library: { type: 'audio' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    up('audioSrc', url);
  });
  frame.open();
}

const blendModes = ['normal','multiply','screen','overlay','darken','lighten','color-dodge','color-burn','hard-light','soft-light','difference','exclusion','hue','saturation','color','luminosity'];

// Option arrays per FieldSelect (stessi value dei vecchi <option>: dati salvati invariati)
const OPTS_TAG = ['h1','h2','h3','h4','h5','h6','p','span','div'].map(v => ({ value: v, label: v }));
const ALLINEAMENTI = [
  { v: 'left', n: t('A sinistra') },
  { v: 'center', n: t('Al centro') },
  { v: 'right', n: t('A destra') },
];
const OPTS_LINK_TARGET = [
  { value: '_self', label: t('Stessa scheda') },
  { value: '_blank', label: t('Nuova scheda') },
];
const OPTS_TRIGGER = [
  { value: 'click', label: t('Al clic') },
  { value: 'hover', label: t('Al passaggio del mouse (sul telefono al tocco)') },
];
const OPTS_ACTION_TYPE = [
  { value: 'none', label: t('Nessuna') },
  { value: 'nextSlide', label: t('Slide successiva') },
  { value: 'prevSlide', label: t('Slide precedente') },
  { value: 'goToSlide', label: t('Vai alla slide…') },
  { value: 'openUrl', label: t('Apri un indirizzo') },
  { value: 'scrollBelow', label: t('Scorri sotto lo slider') },
  { value: 'toggleLayer', label: t('Mostra o nasconde un livello') },
  { value: 'setState', label: t('Imposta uno stato') },
  { value: 'cycleState', label: t('Stato successivo (a giro)') },
];
// Come compare (e sparisce) il livello di «Mostra o nasconde un livello» (1.4.1475: prima sempre di colpo)
const OPTS_MOSTRA_EFFETTO = [
  { value: 'nessuno', label: t('Subito, senza effetto') },
  { value: 'fade', label: t('Dissolvenza') },
];
const OPTS_STATO_EFFETTO = [
  { value: 'fade', label: t('Dissolvenza') },
  { value: 'su', label: t('Sale dal basso') },
  { value: 'giu', label: t('Scende dall’alto') },
  { value: 'sinistra', label: t('Da sinistra') },
  { value: 'destra', label: t('Da destra') },
  { value: 'zoom', label: t('Si ingrandisce') },
  { value: 'nessuno', label: t('Subito, senza effetto') },
];
const OPTS_FONT_WEIGHT = ['300','400','500','600','700','800','900'].map(v => ({ value: v, label: v }));
const OPTS_FONT_STYLE = [
  { value: 'normal', label: t('Normale') },
  { value: 'italic', label: t('Corsivo') },
];
const OPTS_TEXT_TRANSFORM = [
  { value: 'none', label: t('Nessuna') },
  { value: 'uppercase', label: t('MAIUSCOLO') },
  { value: 'lowercase', label: t('minuscolo') },
  { value: 'capitalize', label: t('Iniziali maiuscole') },
];
const OPTS_TEXT_DECORATION = [
  { value: 'none', label: t('Nessuna') },
  { value: 'underline', label: t('Sottolineato') },
  { value: 'line-through', label: t('Barrato') },
  { value: 'overline', label: t('Sopralineato') },
];
const OPTS_BORDER_STYLE = [
  { value: 'solid', label: t('Solido') },
  { value: 'dashed', label: t('Tratteggiato') },
  { value: 'dotted', label: t('Puntinato') },
  { value: 'double', label: t('Doppio') },
];
const OPTS_OBJECT_FIT = [
  { value: 'cover', label: t('Copri (taglia)') },
  { value: 'contain', label: t('Contieni (intera)') },
  { value: 'fill', label: t('Riempi (deforma)') },
  { value: 'none', label: t('Nessuno') },
];
const OPTS_OBJECT_POSITION = [
  { value: 'center', label: t('Centro') },
  { value: 'top', label: t('Alto') },
  { value: 'bottom', label: t('Basso') },
  { value: 'left', label: t('Sinistra') },
  { value: 'right', label: t('Destra') },
  { value: 'top left', label: t('In alto a sinistra') },
  { value: 'top right', label: t('In alto a destra') },
  { value: 'bottom left', label: t('In basso a sinistra') },
  { value: 'bottom right', label: t('In basso a destra') },
];
// 1.4.1446: nomi in italiano (prima si leggeva il valore CSS: «normal», «multiply»…), come nelle altre tile
const NOMI_FUSIONE = {
  normal: t('Normale'), multiply: t('Moltiplica'), screen: t('Schermo'), overlay: t('Sovrapposizione'),
  darken: t('Scurisci'), lighten: t('Schiarisci'), 'color-dodge': t('Colore schiarito'), 'color-burn': t('Colore brunito'),
  'hard-light': t('Luce intensa'), 'soft-light': t('Luce soffusa'), difference: t('Differenza'), exclusion: t('Esclusione'),
  hue: t('Tonalità'), saturation: t('Saturazione'), color: t('Colore'), luminosity: t('Luminosità'),
};
const OPTS_BLEND_MODE = blendModes.map(v => ({ value: v, label: NOMI_FUSIONE[v] || v }));
const OPTS_SFX_EFFECT = [
  { value: 'blockRight', label: t('Blocco da destra') },
  { value: 'blockLeft', label: t('Blocco da sinistra') },
  { value: 'blockDown', label: t('Blocco dall’alto') },
  { value: 'blockUp', label: t('Blocco dal basso') },
];
const OPTS_CURSOR = [
  { value: 'auto', label: t('Automatico') },
  { value: 'pointer', label: t('Manina') },
  { value: 'default', label: t('Freccia') },
  { value: 'move', label: t('Sposta') },
  { value: 'crosshair', label: t('Mirino') },
  { value: 'text', label: t('Testo') },
  { value: 'grab', label: t('Mano aperta') },
  { value: 'none', label: t('Nascosto') },
];
// L'andamento delle entrate e uscite: i livelli nuovi e quelli dei temi hanno cubic-bezier(.22,1,.36,1), che il menu
// degli andamenti non elencava e mostrava vuoto («—»).
const OPTS_ANIM_EASING = [{ value: 'cubic-bezier(.22,1,.36,1)', label: t('Morbido, frena alla fine (predefinito)') }, ...EASING_GROUPS];
// 1.4.1446: gli stessi nomi dell'Andamento delle animazioni (prima «ease», «ease-in»…)
const OPTS_EASING = [
  { value: 'ease', label: t('Morbido') }, { value: 'ease-in', label: t('Accelera') }, { value: 'ease-out', label: t('Rallenta') },
  { value: 'ease-in-out', label: t('Accelera e rallenta') }, { value: 'linear', label: t('Costante') },
];
const OPTS_CHAR_SPLIT = [
  { value: 'chars', label: t('Caratteri') },
  { value: 'words', label: t('Parole') },
  { value: 'lines', label: t('Righe') },
];
const OPTS_CHAR_DIRECTION = [
  { value: 'forward', label: t('Avanti') },
  { value: 'backward', label: t('Indietro') },
  { value: 'random', label: t('Casuale') },
  { value: 'middletoedge', label: t('Centro → Bordi') },
  { value: 'edgetomiddle', label: t('Bordi → Centro') },
];
const OPTS_HOVER_CURSOR = [
  { value: '', label: t('Nessun cambio') },
  { value: 'pointer', label: t('Manina') },
  { value: 'default', label: t('Freccia') },
  { value: 'grab', label: t('Mano aperta') },
  { value: 'crosshair', label: t('Mirino') },
];
const OPTS_MASK_PRESET = [
  { value: 'custom', label: t('Personalizzato') },
  // 1.4.1477: nomi giusti (erano rovesciati): coperto a destra (right 100) = si scopre da sinistra, come l'entrata
  // «Si scopre da sinistra» (mpsMaskFromLeft); i valori dei modelli non cambiano
  { value: 'revealRight', label: t('Si scopre da sinistra') },
  { value: 'revealLeft', label: t('Si scopre da destra') },
  { value: 'revealDown', label: t('Si scopre dal basso') },
  { value: 'revealUp', label: t('Si scopre dall’alto') },
  { value: 'curtainH', label: t('Sipario orizzontale') },
  { value: 'curtainV', label: t('Sipario verticale') },
];
const OPTS_KENBURNS_TYPE = [
  { value: 'zoomIn', label: t('Si avvicina') },
  { value: 'zoomOut', label: t('Si allontana') },
  { value: 'panLeft', label: t('Scorre a sinistra') },
  { value: 'panRight', label: t('Scorre a destra') },
  { value: 'panUp', label: t('Scorre in alto') },
  { value: 'panDown', label: t('Scorre in basso') },
];
const OPTS_MOTION_PRESET = [
  { value: 'circle', label: t('Cerchio') },
  { value: 'figure8', label: t('Otto') },
  { value: 'wave', label: t('Onda') },
  { value: 'zigzag', label: t('Zigzag') },
  { value: 'arc', label: t('Arco') },
  { value: 'disegnato', label: t('Disegnato sulla tela') },
  { value: 'custom', label: t('Tracciato SVG scritto a mano') },
];
const ICO_MATITA = lucideIconsLP.pencil || '';
const ICO_PLAY = lucideIconsLP.play || '';
const OPTS_PARALLAX_TYPE = [
  { value: 'mouse', label: t('Mouse') },
  { value: 'scroll', label: t('Scorrimento') },
  { value: 'both', label: t('Mouse e scorrimento') },
];

// Font families
const fontsSans = ['Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins', 'Nunito', 'Raleway', 'Source Sans 3', 'Work Sans', 'DM Sans', 'Manrope'];
const fontsSerif = ['Playfair Display', 'Merriweather', 'Lora', 'PT Serif', 'Libre Baskerville', 'Cormorant Garamond', 'Crimson Text', 'Noto Serif'];
const fontsMono = ['JetBrains Mono', 'Fira Code', 'Source Code Pro', 'IBM Plex Mono', 'Space Mono'];
const fontsDisplay = ['Bebas Neue', 'Oswald', 'Anton', 'Permanent Marker', 'Abril Fatface', 'Righteous', 'Pacifico', 'Dancing Script', 'Lobster', 'Satisfy'];
const fontGroupOpts = [
  { value: '', label: t('Predefinito') },
  { group: 'Sans-serif', options: fontsSans.map(f => ({ value: f, label: f })) },
  { group: 'Serif', options: fontsSerif.map(f => ({ value: f, label: f })) },
  { group: 'Mono', options: fontsMono.map(f => ({ value: f, label: f })) },
  { group: 'Display', options: fontsDisplay.map(f => ({ value: f, label: f })) },
];

function toggleSfx(enabled) {
  if (enabled) {
    up('sfx', { effect: 'blockRight', color: 'var(--olo-color-light)', duration: 800 });
  } else {
    up('sfx', null);
  }
}

function upSfx(key, val) {
  if (!props.layer?.sfx) return;
  up('sfx', { ...props.layer.sfx, [key]: val });
}

// Box shadow helpers
function toggleBoxShadow(enabled) {
  up('boxShadow', enabled ? { x: 0, y: 4, blur: 10, spread: 0, color: 'color-mix(in srgb, var(--olo-color-dark) 30%, transparent)' } : null);
}
function upBoxShadow(key, val) {
  if (!props.layer?.boxShadow) return;
  up('boxShadow', { ...props.layer.boxShadow, [key]: val });
}

// Text shadow helpers
function toggleTextShadow(enabled) {
  up('textShadow', enabled ? { x: 2, y: 2, blur: 4, color: 'var(--olo-color-dark)' } : null);
}
function upTextShadow(key, val) {
  if (!props.layer?.textShadow) return;
  up('textShadow', { ...props.layer.textShadow, [key]: val });
}

// Shape gradient helpers
function toggleShapeGradient(enabled) {
  up('shapeGradient', enabled ? { from: 'var(--olo-color-primary)', to: 'var(--olo-color-accent)', angle: 180 } : null);
}
function upShapeGrad(key, val) {
  if (!props.layer?.shapeGradient) return;
  up('shapeGradient', { ...props.layer.shapeGradient, [key]: val });
}

// Mask (clip-path) helpers
function toggleMask(enabled) {
  up('mask', enabled ? { preset: 'custom', top: 0, right: 0, bottom: 0, left: 0 } : null);
}
function upMask(key, val) {
  if (!props.layer?.mask) return;
  up('mask', { ...props.layer.mask, [key]: val, preset: 'custom' });
}
function applyMaskPreset(preset) {
  const presets = {
    custom:     { top: 0, right: 0, bottom: 0, left: 0 },
    revealRight:{ top: 0, right: 100, bottom: 0, left: 0 },
    revealLeft: { top: 0, right: 0, bottom: 0, left: 100 },
    revealDown: { top: 100, right: 0, bottom: 0, left: 0 },
    revealUp:   { top: 0, right: 0, bottom: 100, left: 0 },
    curtainH:   { top: 0, right: 50, bottom: 0, left: 50 },
    curtainV:   { top: 50, right: 0, bottom: 50, left: 0 },
  };
  up('mask', { preset, ...(presets[preset] || presets.custom) });
}

// Ken Burns per layer helpers
function toggleKenBurns(enabled) {
  up('kenBurns', enabled ? { type: 'zoomIn', intensity: 20, duration: 5000 } : null);
}
function upKenBurns(key, val) {
  if (!props.layer?.kenBurns) return;
  up('kenBurns', { ...props.layer.kenBurns, [key]: val });
}

// Motion Path helpers
const MOTION_PRESETS = {
  circle:  'M50,0 A50,50 0 1,1 50,100 A50,50 0 1,1 50,0 Z',
  figure8: 'M50,50 C75,0 100,0 100,50 C100,100 75,100 50,50 C25,0 0,0 0,50 C0,100 25,100 50,50 Z',
  wave:    'M0,50 Q25,0 50,50 Q75,100 100,50',
  zigzag:  'M0,50 L25,10 L50,90 L75,10 L100,50',
  arc:     'M0,100 Q50,-20 100,100',
};
function toggleMotionPath(enabled) {
  up('motionPath', enabled ? { preset: 'circle', path: MOTION_PRESETS.circle, duration: 3000, autoRotate: false, loop: true, easing: 'linear' } : null);
}
function applyMotionPreset(preset) {
  if (!props.layer?.motionPath) return;
  const mp = props.layer.motionPath;
  // «Disegnato sulla tela»: si torna ai punti disegnati (se ci sono), se no si comincia a disegnare
  if (preset === 'disegnato') {
    if (Array.isArray(mp.punti) && mp.punti.length > 1) up('motionPath', { ...mp, preset, path: dDaPunti(mp.punti, mp.dritte) });
    else emit('disegna-percorso');
    return;
  }
  const path = MOTION_PRESETS[preset] || mp.path || '';
  up('motionPath', { ...mp, preset, path });
}
// «Linee dritte»: lo stesso disegno coi segmenti dritti invece della curva morbida
function upDritte(si) {
  const mp = props.layer?.motionPath;
  if (!mp) return;
  const n = { ...mp, dritte: si || undefined };
  if (Array.isArray(mp.punti) && mp.punti.length > 1 && (mp.preset === 'disegnato' || !mp.preset)) n.path = dDaPunti(mp.punti, si);
  up('motionPath', n);
}
function upMotionPath(key, val) {
  if (!props.layer?.motionPath) return;
  up('motionPath', { ...props.layer.motionPath, [key]: val });
}

// Movimento con lo scorrimento (scrollMove): due stati, o la prima e l'ultima delle tappe se ci sono
// Andamento del movimento con lo scorrimento (scrollMove.andamento): morbido = di serie (nessuna chiave salvata)
const OPTS_ANDAMENTO = [
  { value: 'morbido', label: t('Morbido fra le tappe') },
  { value: 'costante', label: t('Costante (zoom uniforme)') },
];
const SCROLL_MOVE_CAMPI = [
  { k: 'x', l: 'Sposta in orizzontale', d: 0, min: -2000, max: 2000, step: 10, u: 'px' },
  { k: 'y', l: 'Sposta in verticale', d: 0, min: -2000, max: 2000, step: 10, u: 'px' },
  { k: 's', l: 'Scala', d: 1, min: 0, max: 6, step: 0.05, u: '' },
  { k: 'sx', l: 'Allunga in orizzontale', d: 1, min: 0, max: 6, step: 0.05, u: '' },
  { k: 'sy', l: 'Allunga in verticale', d: 1, min: 0, max: 6, step: 0.05, u: '' },
  { k: 'r', l: 'Rotazione', d: 0, min: -720, max: 720, step: 1, u: '°' },
  { k: 'rx', l: 'Rotazione 3D (X)', d: 0, min: -180, max: 180, step: 1, u: '°' },
  { k: 'ry', l: 'Rotazione 3D (Y)', d: 0, min: -180, max: 180, step: 1, u: '°' },
  { k: 'o', l: 'Opacità', d: 100, min: 0, max: 100, step: 1, u: '%' },
  // la maschera: quanto del livello resta coperto da ogni lato (il livello si scopre o si copre scorrendo)
  { k: 'mt', l: 'Maschera dall’alto', d: 0, min: 0, max: 100, step: 1, u: '%' },
  { k: 'mr', l: 'Maschera da destra', d: 0, min: 0, max: 100, step: 1, u: '%' },
  { k: 'mb', l: 'Maschera dal basso', d: 0, min: 0, max: 100, step: 1, u: '%' },
  { k: 'ml', l: 'Maschera da sinistra', d: 0, min: 0, max: 100, step: 1, u: '%' },
  // gli assi del carattere variabile (livelli di testo): larghezza e peso veri del disegno, non la scritta deformata
  { k: 'wdth', testo: true, l: 'Larghezza del carattere', d: 100, min: 25, max: 200, step: 1, u: '' },
  { k: 'wght', testo: true, l: 'Peso del carattere', d: 400, min: 100, max: 1000, step: 10, u: '' },
];
function toggleScrollMove(enabled) {
  up('scrollMove', enabled ? { inizio: 0, fine: 100, da: {}, a: { y: -120, o: 0 } } : null);
}
function upScrollMove(key, val) {
  if (!props.layer?.scrollMove) return;
  up('scrollMove', { ...props.layer.scrollMove, [key]: val });
}
function smVal(stato, k, d) {
  const sm = props.layer?.scrollMove || {};
  const tappe = sm.tappe || [];
  const v = tappe.length > 1 ? (stato === 'da' ? tappe[0] : tappe[tappe.length - 1]) : (sm[stato] || {});
  return v[k] ?? d;
}
function upSmVal(stato, k, val) {
  const sm = props.layer?.scrollMove;
  if (!sm) return;
  const tappe = sm.tappe || [];
  if (tappe.length > 1) {
    const i = stato === 'da' ? 0 : tappe.length - 1;
    const nuove = tappe.map((t, j) => (j === i ? { ...t, [k]: val } : t));
    up('scrollMove', { ...sm, tappe: nuove });
  } else {
    up('scrollMove', { ...sm, [stato]: { ...(sm[stato] || {}), [k]: val } });
  }
}

// ── Le tappe del percorso (1.4.1352) ──
// Si leggono sempre come tappe ([{ p, v }]); si scrivono in scrollMove.tappe (il PHP le preferisce a da/a, che restano
// valide per chi non le tocca): i dati salvati dei temi non cambiano finché non si modifica il livello.
const tappaScelta = ref(0);
const tappeSM = computed(() => tappeDi(props.layer?.scrollMove));
const tappaCorrente = computed(() => tappeSM.value[Math.min(tappaScelta.value, tappeSM.value.length - 1)] || null);
const perTesto = (c) => !c.testo || props.layer?.type === 'text' || props.layer?.type === 'button';
const campiTappa = computed(() => {
  const usate = chiaviUsate(props.layer?.scrollMove);
  return SCROLL_MOVE_CAMPI.filter((c) => usate.includes(c.k) && perTesto(c));
});
const campiAggiungibili = computed(() => {
  const usate = chiaviUsate(props.layer?.scrollMove);
  return SCROLL_MOVE_CAMPI.filter((c) => !usate.includes(c.k) && perTesto(c));
});
const opzioniAggiungi = computed(() => [
  { value: '', label: t('+ Muovi anche…') },
  ...campiAggiungibili.value.map((c) => ({ value: c.k, label: t(c.l) })),
]);
watch(() => props.layer?.id, () => { tappaScelta.value = 0; });

function fmtP(p) {
  return Math.round(p * 10) / 10;
}
const BREVI = {
  x: ['oriz.', 'px'], y: ['vert.', 'px'], s: ['scala', ''], sx: ['largh.', '×'], sy: ['alt.', '×'], r: ['rot.', '°'], rx: ['rot. X', '°'], ry: ['rot. Y', '°'],
  o: ['opac.', '%'], mt: ['masch. ↓', '%'], mr: ['masch. ←', '%'], mb: ['masch. ↑', '%'], ml: ['masch. →', '%'], wdth: ['largh. car.', ''], wght: ['peso', ''],
};
function riassuntoTappa(tp) {
  const parti = SM_CHIAVI.filter((k) => tp.v[k] !== undefined).map((k) => t(BREVI[k][0]) + ' ' + (Math.round(tp.v[k] * 100) / 100) + BREVI[k][1]);
  return parti.length ? parti.join(' · ') : t('com’è sulla tela');
}
function scriviTappe(nuove) {
  const sm = props.layer?.scrollMove;
  if (!sm) return;
  const pulite = [...nuove].sort((a, b) => a.p - b.p).map((x) => ({ p: Math.round(x.p * 100) / 100, ...x.v }));
  const out = { ...sm, tappe: pulite };
  delete out.da;
  delete out.a;
  up('scrollMove', out);
}
function scegliTappa(i) {
  tappaScelta.value = i;
  const tp = tappeSM.value[i];
  // la tela va a quel punto dello scorrimento: si vede com'è il livello in quella tappa
  if (tp) emit('set-scroll-preview', Math.round(dellaScena(props.layer.scrollMove, tp.p) * 10) / 10);
}
function upTappa(k, val) {
  const i = Math.min(tappaScelta.value, tappeSM.value.length - 1);
  const nuove = tappeSM.value.map((x, j) => (j === i ? (k === 'p' ? { p: Math.max(0, Math.min(100, +val || 0)), v: x.v } : { p: x.p, v: { ...x.v, [k]: val } }) : x));
  if (k === 'p') {
    // dopo lo spostamento la tappa resta scelta anche se cambia posto nell'ordine
    const presa = nuove[i];
    scriviTappe(nuove);
    const ord = [...nuove].sort((a, b) => a.p - b.p);
    tappaScelta.value = ord.indexOf(presa);
  } else {
    scriviTappe(nuove);
  }
}
function aggiungiTappa() {
  const sm = props.layer?.scrollMove;
  const t2 = tappeSM.value;
  if (!sm || !t2.length) return;
  // al punto dell'anteprima, se è accesa; se no a metà fra la tappa scelta e la successiva (o la precedente)
  // (1.4.1443) Scegliere una tappa porta l'anteprima al suo punto: se lì c'è già una tappa si va a metà, se no la
  // nuova nasceva sopra quella scelta (rifacendo il titolo di Elastan, sette tappe di fila al 100%).
  let p = props.scrollPreview != null ? delLivello(sm, props.scrollPreview) : null;
  if (p == null || t2.some((x) => Math.abs(x.p - p) < 0.5)) {
    const i = Math.min(tappaScelta.value, t2.length - 1);
    const b = t2[i + 1] || t2[i - 1] || { p: 100 };
    p = (t2[i].p + b.p) / 2;
  }
  p = Math.round(p * 10) / 10;
  const c = sm.andamento === 'costante';
  const v = {};
  for (const k of chiaviUsate(sm)) v[k] = Math.round(valoreSM(t2, p, k, c) * 100) / 100;
  const nuova = { p, v };
  const nuove = [...t2, nuova];
  scriviTappe(nuove);
  tappaScelta.value = [...nuove].sort((a, b) => a.p - b.p).indexOf(nuova);
}
function eliminaTappa() {
  const t2 = tappeSM.value;
  if (t2.length <= 2) return;
  const i = Math.min(tappaScelta.value, t2.length - 1);
  scriviTappe(t2.filter((_, j) => j !== i));
  tappaScelta.value = Math.max(0, i - 1);
}
function aggiungiChiave(k) {
  if (!k) return;
  const c = SCROLL_MOVE_CAMPI.find((x) => x.k === k);
  // su tutte le tappe il valore che il livello ha già: non cambia niente finché non lo si modifica in una tappa.
  // (1.4.1443) Larghezza e peso del carattere partono da quelli del livello (Stile), non da 100 e 400: col titolo
  // di Elastan (62 e 880) aggiungerli faceva «saltare» le lettere.
  let d = c ? c.d : 0;
  if (k === 'wdth' && Number(props.layer?.fontWdth) > 0) d = Number(props.layer.fontWdth);
  if (k === 'wght' && Number(props.layer?.fontWeight) > 0) d = Number(props.layer.fontWeight);
  scriviTappe(tappeSM.value.map((x) => ({ p: x.p, v: { ...x.v, [k]: d } })));
}
function togliChiave(k) {
  scriviTappe(tappeSM.value.map((x) => {
    const v = { ...x.v };
    delete v[k];
    return { p: x.p, v };
  }));
}

// Parallax layer helpers
function toggleParallax(enabled) {
  up('parallax', enabled ? { type: 'mouse', depth: 5, tilt3d: false, scrollSpeed: 0.5 } : null);
}
function upParallax(key, val) {
  if (!props.layer?.parallax) return;
  up('parallax', { ...props.layer.parallax, [key]: val });
}
</script>

<style scoped>
/* Slide dagli articoli: un dato dell'articolo al posto di quello scritto (1.4.1457) */
.mps-lega { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 18px; border-radius: 4px; cursor: pointer; }
.mps-lega:focus-visible { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: 1px; }
.mps-dato { margin-top: 4px; font: inherit; font-size: 10px; height: 22px; padding: 0 8px; border: 1px solid #374151; border-radius: 4px; background: #1f2937; color: #d1d5db; cursor: pointer; }
.mps-dato:hover { background: #374151; color: #fff; }
.mps-dato.is-on { border-color: var(--olo-color-primary, #e1474f); color: #fff; }
.mps-dato:focus-visible { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: 1px; }
/* Percorso: disegna e prova (1.4.1473) */
.mps-percorso-azioni { display: flex; flex-wrap: wrap; gap: 4px; }
.mps-percorso-btn { font: inherit; display: inline-flex; align-items: center; gap: 4px; font-size: 10.5px; padding: 3px 8px; border-radius: 4px; border: 1px solid #4b5563; background: #374151; color: #e5e7eb; cursor: pointer; }
.mps-percorso-btn :deep(svg) { width: 12px; height: 12px; }
.mps-percorso-btn:hover:not(:disabled) { background: #4b5563; }
.mps-percorso-btn:disabled { opacity: .4; cursor: default; }
.mps-percorso-btn.is-on { border-color: rgb(var(--olo-primary-rgb, 129 140 248)); color: #fff; }
.mps-percorso-btn:focus-visible { outline: 2px solid #93c5fd; outline-offset: 1px; }
/* Conferma in linea (1.4.1472): tornare a «Semplice» con la linea del tempo, togliere un percorso disegnato */
.mps-conferma { display: flex; flex-direction: column; gap: 6px; padding: 8px; margin-bottom: 8px; border: 1px solid #92400e; border-radius: 6px; background: #451a03; font-size: 11px; color: #fde68a; }
.mps-conferma-fila { display: flex; gap: 6px; }
.mps-conferma-si, .mps-conferma-no { font: inherit; font-size: 11px; padding: 3px 10px; border-radius: 4px; cursor: pointer; border: 1px solid #4b5563; }
.mps-conferma-si { background: #b91c1c; border-color: #b91c1c; color: #fff; font-weight: 600; }
.mps-conferma-no { background: #374151; color: #e5e7eb; }
.mps-conferma-si:focus-visible, .mps-conferma-no:focus-visible { outline: 2px solid #fff; outline-offset: 1px; }
/* Le tappe del percorso (1.4.1352) */
.mps-tappe { display: flex; flex-direction: column; gap: 6px; padding: 8px; border: 1px solid #374151; border-radius: 6px; background: #111827; }
.mps-tappe-barra { position: relative; height: 18px; margin: 2px 6px; }
.mps-tappe-barra::before { content: ''; position: absolute; left: 0; right: 0; top: 8px; height: 2px; background: #374151; border-radius: 1px; }
.mps-tappe-punto { position: absolute; top: 3px; width: 12px; height: 12px; margin-left: -6px; border-radius: 50%; background: #60a5fa; border: 2px solid #111827; cursor: pointer; }
.mps-tappe-punto.is-on { background: #fbbf24; box-shadow: 0 0 0 2px #fbbf24; }
.mps-tappe-ora { position: absolute; top: 0; width: 2px; height: 18px; margin-left: -1px; background: #f87171; pointer-events: none; }
.mps-tappa { display: flex; align-items: baseline; gap: 8px; width: 100%; text-align: left; padding: 3px 6px; border-radius: 4px; background: transparent; border: 1px solid transparent; color: #d1d5db; cursor: pointer; font-size: 10px; }
.mps-tappa:hover { background: #1f2937; }
.mps-tappa.is-on { background: #1e293b; border-color: #fbbf24; }
.mps-tappa:focus-visible, .mps-tappe-btn:focus-visible, .mps-tappe-x:focus-visible { outline: 2px solid #93c5fd; outline-offset: 1px; }
.mps-tappa-p { font-family: ui-monospace, monospace; font-weight: 700; color: #93c5fd; min-width: 40px; }
.mps-tappa-v { color: #9ca3af; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.mps-tappe-btn { font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 4px; background: #374151; color: #e5e7eb; border: 1px solid #4b5563; cursor: pointer; }
.mps-tappe-btn:hover:not(:disabled) { background: #4b5563; }
.mps-tappe-btn:disabled { opacity: .4; cursor: default; }
.mps-tappe-x { background: none; border: 0; color: #6b7280; cursor: pointer; font-size: 12px; line-height: 1; padding: 0 2px; }
.mps-tappe-x:hover { color: #f87171; }
.mps-label {
  display: block;
  font-size: 10px;
  color: #9ca3af;
  margin-bottom: 2px;
}
.mps-val {
  font-size: 11px;
  color: #9ca3af;
  width: 40px;
  text-align: right;
  flex-shrink: 0;
}
.mps-input {
  width: 100%;
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  padding: 4px 8px;
  font-size: 0.75rem;
  color: #111827;
}
.mps-icon-preview {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}
.mps-icon-preview :deep(svg) {
  width: 18px;
  height: 18px;
  color: #d1d5db;
  stroke: currentColor;
}
.mps-icon-preview :deep(svg:not([fill="none"])) {
  fill: currentColor;
}
.mps-icon-preview :deep(svg [fill="none"]) {
  fill: none;
}

/* Il punto di rotazione (1.4.1430): una griglia di 3 × 3 punti */
.mps-origine { display: grid; grid-template-columns: repeat(3, 12px); gap: 3px; padding: 3px; border: 1px solid #374151; border-radius: 4px; flex: none; }
.mps-origine-punto { width: 12px; height: 12px; padding: 0; border-radius: 50%; border: 1px solid #4b5563; background: transparent; cursor: pointer; }
.mps-origine-punto.is-on { background: var(--olo-color-primary, #e1474f); border-color: var(--olo-color-primary, #e1474f); }
.mps-origine-punto:focus-visible { outline: 2px solid #fff; outline-offset: 1px; }
</style>
