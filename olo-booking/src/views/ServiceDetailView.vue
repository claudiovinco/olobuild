<template>
  <div class="olom-detail-layout" v-if="service">
    <!-- Sidebar -->
    <aside class="olom-sidebar">
      <div class="olom-sidebar-head">
        <router-link to="/" class="olom-sidebar-back">
          <ChevronLeft :size="16" /> Dashboard
        </router-link>
        <h3 class="olom-sidebar-title">{{ service.title }}</h3>
        <a v-if="service.permalink" :href="service.permalink" target="_blank" class="olom-sidebar-link">
          <ExternalLink :size="13" /> Vedi sul sito
        </a>
      </div>
      <div class="olom-sidebar-nav">
        <span class="olom-sidebar-label">Informazioni</span>
        <button v-for="s in sidebarSections.info" :key="s.key" class="olom-sidebar-item"
                :class="{ 'olom-sidebar-active': activeTab === s.key }" @click="onTabChange(s.key)">
          <component :is="s.icon" :size="17" :stroke-width="1.75" /> {{ s.label }}
        </button>
        <span class="olom-sidebar-label">Gestione</span>
        <button v-for="s in sidebarSections.manage" :key="s.key" class="olom-sidebar-item"
                :class="{ 'olom-sidebar-active': activeTab === s.key }" @click="onTabChange(s.key)">
          <component :is="s.icon" :size="17" :stroke-width="1.75" /> {{ s.label }}
        </button>
      </div>
    </aside>

    <!-- Content -->
    <main class="olom-detail-main">

    <!-- Section: Descrizione -->
    <div v-if="activeTab === 'description'" class="olom-section-card">
      <div class="olom-section-head"><FileText :size="20" :stroke-width="1.75" /><h3>Descrizione</h3></div>
      <div class="olom-form-row"><label>Descrizione breve</label><RichTextEditor v-model="form.excerpt" :min-height="60" /></div>
      <div class="olom-form-row"><label>Descrizione completa</label><RichTextEditor v-model="form.description" :min-height="140" /></div>
      <div class="olom-section-foot">
        <button class="olom-btn olom-btn-success" @click="saveInfo" :disabled="saving">{{ saving ? 'Salvataggio...' : 'Salva' }}</button>
        <span v-if="savedMsg" class="olom-saved-msg">{{ savedMsg }}</span>
      </div>
    </div>

    <!-- Section: Struttura -->
    <div v-if="activeTab === 'structure'" class="olom-section-card">
      <div class="olom-section-head"><BedDouble :size="20" :stroke-width="1.75" /><h3>Struttura e capienza</h3></div>
      <div class="olom-form-grid">
        <div class="olom-form-row"><label>Prezzo base/notte (&euro;)</label><input type="text" v-model="form.price" /></div>
        <div class="olom-form-row"><label>Capienza (ospiti)</label><input type="number" v-model="form.capacity" min="1" /></div>
        <div class="olom-form-row"><label>Camere</label><input type="number" v-model="form.bedrooms" min="0" /></div>
        <div class="olom-form-row"><label>Posti letto</label><input type="number" v-model="form.beds" min="0" /></div>
        <div class="olom-form-row"><label>Bagni</label><input type="number" v-model="form.bathrooms" min="0" /></div>
        <div class="olom-form-row"><label>Superficie (m&sup2;)</label><input type="number" v-model="form.sqm" min="0" /></div>
        <div class="olom-form-row"><label>Altitudine (m s.l.m.)</label><input type="number" v-model="form.altitude" min="0" max="5000" placeholder="es. 1200" /></div>
        <div class="olom-form-row">
          <label>Classificazione funghi</label>
          <select v-model="form.mushrooms">
            <option :value="0">-- Non classificata --</option>
            <option v-for="n in 5" :key="n" :value="n">{{ n }} / 5</option>
          </select>
        </div>
        <div class="olom-form-row">
          <label>Apertura</label>
          <select v-model="form.opening">
            <option value="">-- Non specificato --</option>
            <option value="Apertura annuale">Apertura annuale</option>
            <option value="Apertura stagionale">Apertura stagionale</option>
          </select>
        </div>
      </div>
      <div class="olom-section-foot">
        <button class="olom-btn olom-btn-success" @click="saveInfo" :disabled="saving">{{ saving ? 'Salvataggio...' : 'Salva' }}</button>
        <span v-if="savedMsg" class="olom-saved-msg">{{ savedMsg }}</span>
      </div>
    </div>

    <!-- Section: Check-in -->
    <div v-if="activeTab === 'checkin'" class="olom-section-card">
      <div class="olom-section-head"><KeyRound :size="20" :stroke-width="1.75" /><h3>Check-in / Check-out</h3></div>
      <div class="olom-form-grid">
        <div class="olom-form-row"><label>Orario check-in</label><input type="time" v-model="form.checkin_time" /></div>
        <div class="olom-form-row"><label>Orario check-out</label><input type="time" v-model="form.checkout_time" /></div>
        <div class="olom-form-row">
          <label>Giorno check-in</label>
          <select v-model="form.checkin_day">
            <option value="">Qualsiasi</option>
            <option v-for="(l, k) in dayLabels" :key="k" :value="k">{{ l }}</option>
          </select>
        </div>
      </div>
      <div class="olom-section-foot">
        <button class="olom-btn olom-btn-success" @click="saveInfo" :disabled="saving">{{ saving ? 'Salvataggio...' : 'Salva' }}</button>
        <span v-if="savedMsg" class="olom-saved-msg">{{ savedMsg }}</span>
      </div>
    </div>

    <!-- Section: Posizione -->
    <div v-if="activeTab === 'position'" class="olom-section-card">
      <div class="olom-section-head"><MapPin :size="20" :stroke-width="1.75" /><h3>Posizione</h3></div>
      <div class="olom-form-grid">
        <div class="olom-form-row">
          <label>Valle</label>
          <select v-model="form.valley">
            <option value="">-- Seleziona valle --</option>
            <option v-for="v in valleyOptions" :key="v" :value="v">{{ v }}</option>
          </select>
        </div>
        <div class="olom-form-row"><label>Codice CIPAT</label><input type="text" v-model="form.cipat" /></div>
      </div>
      <div class="olom-form-row"><label>Indirizzo</label><input type="text" v-model="form.address" style="width:100%" /></div>
      <div class="olom-map-section">
        <div class="olom-form-grid olom-map-coords">
          <div class="olom-form-row"><label>Latitudine</label><input type="text" v-model="form.latitude" placeholder="46.0700" @change="updateMapFromInputs" /></div>
          <div class="olom-form-row"><label>Longitudine</label><input type="text" v-model="form.longitude" placeholder="11.1200" @change="updateMapFromInputs" /></div>
        </div>
        <p class="olom-hint">Clicca sulla mappa per posizionare la struttura.</p>
        <div ref="mapEl" class="olom-map-container"></div>
        <p v-if="mapError" class="olom-form-error" style="margin-top:6px">{{ mapError }}</p>
        <p v-if="mapLoading" class="olom-hint" style="margin-top:6px">Caricamento mappa...</p>
      </div>
      <div class="olom-form-row"><label>Come arrivare</label><RichTextEditor v-model="form.directions" :min-height="80" /></div>
      <div class="olom-form-row"><label>Regole</label><RichTextEditor v-model="form.rules" :min-height="80" /></div>
      <div class="olom-section-foot">
        <button class="olom-btn olom-btn-success" @click="saveInfo" :disabled="saving">{{ saving ? 'Salvataggio...' : 'Salva' }}</button>
        <span v-if="savedMsg" class="olom-saved-msg">{{ savedMsg }}</span>
      </div>
    </div>

    <!-- Section: Club di Prodotto -->
    <div v-if="activeTab === 'club'" class="olom-section-card">
      <div class="olom-section-head"><Award :size="20" :stroke-width="1.75" /><h3>Club di Prodotto</h3></div>
      <p class="olom-hint">Assegna la struttura a un gruppo e una categoria per le suddivisioni marketing.</p>
      <div class="olom-form-grid">
        <div class="olom-form-row"><label>Gruppo</label><input type="text" v-model="form.club_group" placeholder="es. Trentino Marketing" /></div>
        <div class="olom-form-row"><label>Categoria</label><input type="text" v-model="form.club_category" placeholder="es. Family, Romantica, Vacanze Attive" /></div>
      </div>
      <div class="olom-section-foot">
        <button class="olom-btn olom-btn-success" @click="saveInfo" :disabled="saving">{{ saving ? 'Salvataggio...' : 'Salva' }}</button>
        <span v-if="savedMsg" class="olom-saved-msg">{{ savedMsg }}</span>
      </div>
    </div>

    <!-- Tab: Servizi e Comfort -->
    <div v-if="activeTab === 'amenities'" class="olom-section-card">
      <!-- Configurazione categorie -->
      <div class="olom-form-section">
        <h4>Categorie attive</h4>
        <p class="olom-hint">Seleziona le categorie di caratteristiche disponibili per questa struttura.</p>
        <div class="olom-cat-toggles">
          <button v-for="cat in amenityCategories" :key="'t-'+cat.key"
                  class="olom-cat-toggle" :class="{ 'olom-cat-toggle-active': isCatEnabled(cat.key) }"
                  @click="toggleCat(cat.key)">
            {{ cat.label }}
          </button>
        </div>
        <div class="olom-form-row" style="max-width:320px;margin-top:14px">
          <label>Caratteristiche massime selezionabili</label>
          <select v-model.number="maxAmenities">
            <option :value="0">Nessun limite</option>
            <option v-for="n in [2,3,4,5]" :key="n" :value="n">Max {{ n }}</option>
          </select>
        </div>
        <p v-if="maxAmenities > 0" class="olom-hint">
          {{ amenities.length }} / {{ maxAmenities }} selezionate
          <span v-if="amenities.length >= maxAmenities" style="color:#d63638;font-weight:600"> — limite raggiunto</span>
        </p>
      </div>

      <!-- Griglia caratteristiche per categoria -->
      <div v-for="cat in amenityCategories" :key="cat.key" class="olom-form-section"
           :class="{ 'olom-cat-disabled': !isCatEnabled(cat.key) }">
        <h4>{{ cat.label }}</h4>
        <div class="olom-amenity-grid">
          <div v-for="a in cat.items" :key="a.key"
               class="olom-amenity-item"
               :class="{ 'olom-amenity-active': amenities.includes(a.key), 'olom-amenity-locked': !isCatEnabled(cat.key) || (maxAmenities > 0 && amenities.length >= maxAmenities && !amenities.includes(a.key)) }"
               @click="toggleAmenity(a.key, cat.key)">
            <span class="olom-amenity-icon" v-html="a.icon"></span>
            <span class="olom-amenity-label">{{ a.label }}</span>
          </div>
        </div>
      </div>
      <button class="olom-btn olom-btn-success" @click="saveAmenities" :disabled="saving">
        {{ saving ? 'Salvataggio...' : 'Salva servizi' }}
      </button>
      <span v-if="savedMsg" class="olom-saved-msg">{{ savedMsg }}</span>
    </div>

    <!-- Tab: Calendario -->
    <div v-if="activeTab === 'calendar'" class="olom-section-card">
      <div class="olom-cal-nav">
        <button class="olom-btn-icon olom-cal-arrow" @click="changeCalMonth(-1)">&lsaquo;</button>
        <h3 class="olom-cal-title">{{ calMonthLabel }}</h3>
        <button class="olom-btn-icon olom-cal-arrow" @click="changeCalMonth(1)">&rsaquo;</button>
        <button class="olom-btn olom-btn-ghost olom-cal-today-btn" @click="calGoToday">Oggi</button>
      </div>
      <div class="olom-cal">
        <div class="olom-cal-head">
          <span v-for="d in ['Lun','Mar','Mer','Gio','Ven','Sab','Dom']" :key="d">{{ d }}</span>
        </div>
        <div class="olom-cal-body">
          <div v-for="(week, wi) in calWeeks" :key="wi" class="olom-cal-week"
               :style="{ gridTemplateRows: '30px ' + 'minmax(20px, auto) '.repeat(week.maxLane || 1) }">
            <div v-for="(day, di) in week.days" :key="'d'+di"
                 class="olom-cal-cell"
                 :class="{
                   'olom-cal-out': !day.inMonth,
                   'olom-cal-today': day.isToday,
                   'olom-cal-we': di >= 5,
                   'olom-cal-drop-target': calDragOver === day.date
                 }"
                 :style="{ gridColumn: di + 1, gridRow: '1 / ' + (week.maxLane + 2) }"
                 @click="onCalCellClick(day)"
                 @dragover.prevent="calDragOver = day.date"
                 @dragleave="calDragOver = ''"
                 @drop.prevent="onCalDrop(day)">
              <span class="olom-cal-num">{{ day.num }}</span>
            </div>
            <a v-for="bar in week.bars" :key="bar.key"
                 class="olom-cal-bar"
                 :class="['olom-cal-s-' + bar.status, { 'olom-cal-bar-start': bar.isStart, 'olom-cal-bar-end': bar.isEnd, 'olom-cal-bar-dragging': calDragging?.id === bar.id }]"
                 :style="{ gridColumn: bar.col1 + ' / ' + (bar.col2 + 1), gridRow: bar.lane + 2, '--bc': bar.color }"
                 :title="bar.title"
                 draggable="true"
                 @dragstart="onCalBarDragStart($event, bar)"
                 @dragend="onCalBarDragEnd"
                 @click.prevent="$router.push('/prenotazione/' + bar.id)">
              <span v-if="bar.showLabel" class="olom-cal-bar-label">{{ bar.label }}</span>
            </a>
          </div>
        </div>
      </div>
      <div class="olom-cal-legend">
        <span class="olom-cal-legend-item"><i class="olom-cal-lswatch" style="background:var(--olom-primary)"></i> Confermata</span>
        <span class="olom-cal-legend-item"><i class="olom-cal-lswatch olom-cal-ls-pending"></i> In attesa</span>
        <span class="olom-cal-legend-item"><i class="olom-cal-lswatch" style="background:#10B981"></i> Completata</span>
        <span class="olom-cal-legend-item"><i class="olom-cal-lswatch" style="background:#D1D5DB"></i> Annullata</span>
        <span class="olom-cal-legend-item" style="margin-left:auto;font-style:italic;opacity:0.7">Clicca su una data per prenotare &bull; Trascina per spostare</span>
      </div>
      <div v-if="calLoading" class="olom-loading">Caricamento...</div>

      <!-- Quick Booking Modal -->
      <div v-if="showQuickBooking" class="olom-modal-backdrop" @click.self="showQuickBooking = false">
        <div class="olom-modal" style="max-width:440px">
          <div class="olom-modal-header">
            <h3>Nuova prenotazione</h3>
            <button class="olom-modal-close" @click="showQuickBooking = false">&times;</button>
          </div>
          <div class="olom-modal-body">
            <div class="olom-form-grid">
              <div class="olom-form-row">
                <label>Check-in</label>
                <input type="date" v-model="quickForm.checkin_date" />
              </div>
              <div class="olom-form-row">
                <label>Check-out</label>
                <input type="date" v-model="quickForm.checkout_date" />
              </div>
            </div>
            <div class="olom-form-row">
              <label>Nome ospite *</label>
              <input type="text" v-model="quickForm.guest_name" placeholder="Nome e cognome" />
            </div>
            <div class="olom-form-grid">
              <div class="olom-form-row">
                <label>Email</label>
                <input type="email" v-model="quickForm.guest_email" placeholder="email@esempio.it" />
              </div>
              <div class="olom-form-row">
                <label>Telefono</label>
                <input type="tel" v-model="quickForm.guest_phone" placeholder="+39 ..." />
              </div>
            </div>
            <div class="olom-form-grid">
              <div class="olom-form-row">
                <label>Ospiti</label>
                <input type="number" v-model.number="quickForm.guest_count" min="1" max="20" />
              </div>
              <div class="olom-form-row">
                <label>Stato</label>
                <select v-model="quickForm.status">
                  <option value="confirmed">Confermata</option>
                  <option value="pending">In attesa</option>
                </select>
              </div>
            </div>
            <div class="olom-form-row">
              <label>Note</label>
              <textarea v-model="quickForm.notes" rows="2" placeholder="Note opzionali"></textarea>
            </div>
            <div v-if="quickError" class="olom-form-error">{{ quickError }}</div>
          </div>
          <div class="olom-modal-footer">
            <button class="olom-btn olom-btn-ghost" @click="showQuickBooking = false">Annulla</button>
            <button class="olom-btn olom-btn-success" @click="submitQuickBooking" :disabled="quickSaving">
              {{ quickSaving ? 'Salvataggio...' : 'Crea prenotazione' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab: Stagioni -->
    <div v-if="activeTab === 'seasons'" class="olom-section-card">
      <div v-for="(season, idx) in seasons" :key="idx" class="olom-season-card">
        <div class="olom-season-header" @click="season._open = !season._open">
          <span class="olom-season-drag">&#9776;</span>
          <strong>{{ season.name || 'Nuova stagione' }}</strong>
          <span class="olom-season-dates" v-if="season.date_from">{{ season.date_from }} &rarr; {{ season.date_to }}</span>
          <button class="olom-btn-icon" @click.stop="seasons.splice(idx, 1)">&times;</button>
        </div>
        <div v-if="season._open !== false" class="olom-season-body">
          <div class="olom-form-grid">
            <div class="olom-form-row"><label>Nome</label><input v-model="season.name" placeholder="es. Alta stagione" /></div>
            <div class="olom-form-row"><label>Dal</label><input type="date" v-model="season.date_from" /></div>
            <div class="olom-form-row"><label>Al</label><input type="date" v-model="season.date_to" /></div>
          </div>
          <div class="olom-form-grid">
            <div class="olom-form-row"><label>&euro;/notte</label><input v-model="season.price_night" placeholder="Prezzo base se vuoto" /></div>
            <div class="olom-form-row"><label>Min notti</label><input type="number" v-model="season.min_nights" min="1" /></div>
            <div class="olom-form-row">
              <label>&nbsp;</label>
              <label class="olom-checkbox"><input type="checkbox" v-model="season.week_only" true-value="1" false-value="0" /> Solo settimane intere</label>
            </div>
          </div>
          <div class="olom-form-row"><label>Note</label><input v-model="season.note" style="width:100%" /></div>
        </div>
      </div>
      <button class="olom-btn olom-btn-ghost" @click="addSeason">+ Aggiungi stagione</button>
      <br/><br/>
      <button class="olom-btn olom-btn-success" @click="saveSeasons" :disabled="saving">Salva stagioni</button>
      <span v-if="savedMsg" class="olom-saved-msg">{{ savedMsg }}</span>
    </div>

    <!-- Tab: Chiusure -->
    <div v-if="activeTab === 'closures'" class="olom-section-card">
      <div v-for="(c, idx) in closures" :key="idx" class="olom-closure-row">
        <input type="date" v-model="c.date_from" />
        <span>&rarr;</span>
        <input type="date" v-model="c.date_to" />
        <input type="text" v-model="c.reason" placeholder="Motivo" style="flex:1" />
        <button class="olom-btn-icon olom-btn-danger" @click="closures.splice(idx, 1)">&times;</button>
      </div>
      <button class="olom-btn olom-btn-ghost" @click="closures.push({ date_from: '', date_to: '', reason: '' })">+ Aggiungi chiusura</button>
      <br/><br/>
      <button class="olom-btn olom-btn-success" @click="saveClosures" :disabled="saving">Salva chiusure</button>
      <span v-if="savedMsg" class="olom-saved-msg">{{ savedMsg }}</span>
    </div>

    <!-- Tab: Galleria -->
    <div v-if="activeTab === 'gallery'" class="olom-section-card">
      <p class="olom-hint" style="margin-bottom:10px">La prima immagine diventa la copertina. Trascina per riordinare.</p>
      <div class="olom-gallery-grid">
        <div v-for="(img, idx) in gallery" :key="img.id"
             class="olom-gallery-item"
             :class="{ 'olom-gallery-cover': idx === 0, 'olom-gallery-drag-over': galleryDragOver === idx }"
             draggable="true"
             @dragstart="onGalleryDragStart(idx)"
             @dragover.prevent="galleryDragOver = idx"
             @dragleave="galleryDragOver = -1"
             @drop.prevent="onGalleryDrop(idx)"
             @dragend="galleryDragFrom = -1; galleryDragOver = -1">
          <img :src="img.thumb" alt="" />
          <span v-if="idx === 0" class="olom-gallery-cover-badge">Copertina</span>
          <button class="olom-gallery-remove" @click="removeImage(idx)">&times;</button>
        </div>
        <!-- Upload dropzone -->
        <div class="olom-gallery-add"
             @click="$refs.fileInput.click()"
             @dragover.prevent="dragOver = true"
             @dragleave="dragOver = false"
             @drop.prevent="onDrop"
             :class="{ 'olom-gallery-dragover': dragOver }">
          <span v-if="!uploading">+</span>
          <span v-else class="olom-gallery-spinner"></span>
        </div>
      </div>
      <input ref="fileInput" type="file" accept="image/*" multiple style="display:none" @change="onFileSelect" />
      <div v-if="uploadError" class="olom-form-error" style="margin-top:8px">{{ uploadError }}</div>

      <div class="olom-gallery-actions">
        <p class="olom-hint">Trascina le immagini o clicca il + per caricare nuove foto.</p>
        <button class="olom-btn olom-btn-ghost" @click="openMediaPicker">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" style="vertical-align:-2px">
            <path d="M1 3h14v10H1V3zm1 1v6.3l3-2.5 2.2 1.8L11 6.5l3 2.5V4H2zm3 3.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
          </svg>
          Scegli dalla libreria media
        </button>
      </div>

      <button class="olom-btn olom-btn-success" @click="saveGallery" :disabled="saving">Salva galleria</button>
      <span v-if="savedMsg" class="olom-saved-msg">{{ savedMsg }}</span>

      <!-- Video Section -->
      <div class="olom-video-section">
        <h3 class="olom-section-title" style="margin-top:32px;margin-bottom:6px">Video</h3>
        <p class="olom-hint" style="margin-bottom:14px">Fino a 3 video: incolla un URL YouTube/Vimeo oppure scegli un file dalla libreria media.</p>
        <div v-for="n in 3" :key="'video-'+n" class="olom-video-slot">
          <label class="olom-label" style="font-size:13px;margin-bottom:4px">Video {{ n }}</label>
          <div style="display:flex;gap:8px;align-items:center">
            <input type="text" v-model="form['video_'+n]" placeholder="https://youtube.com/watch?v=... oppure https://vimeo.com/..."
                   style="flex:1" class="olom-input" />
            <button class="olom-btn olom-btn-ghost" @click="pickVideo(n)" type="button" style="white-space:nowrap">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" style="vertical-align:-2px">
                <path d="M6 3.5L12 8l-6 4.5V3.5z"/>
              </svg>
              Media
            </button>
            <button v-if="form['video_'+n]" class="olom-btn-icon olom-btn-danger" @click="form['video_'+n] = ''" type="button">&times;</button>
          </div>
          <div v-if="form['video_'+n] && getVideoPreviewType(form['video_'+n])" class="olom-video-preview">
            <span class="olom-video-badge">{{ getVideoPreviewType(form['video_'+n]) }}</span>
            <span class="olom-video-url">{{ form['video_'+n] }}</span>
          </div>
        </div>
        <button class="olom-btn olom-btn-success" @click="saveVideos" :disabled="saving" style="margin-top:14px">Salva video</button>
        <span v-if="savedMsg" class="olom-saved-msg">{{ savedMsg }}</span>
      </div>
    </div>

    <!-- Media Library Picker Modal -->
    <div v-if="showMediaPicker" class="olom-modal-backdrop" @click.self="showMediaPicker = false">
      <div class="olom-modal olom-media-modal">
        <div class="olom-modal-header">
          <h3>Libreria media</h3>
          <button class="olom-modal-close" @click="showMediaPicker = false">&times;</button>
        </div>
        <div class="olom-media-toolbar">
          <input type="text" v-model="mediaSearch" @input="debounceMediaSearch" placeholder="Cerca immagini..." class="olom-media-search" />
          <span class="olom-media-count" v-if="mediaItems.length">{{ mediaSelected.length }} selezionate</span>
        </div>
        <div class="olom-media-grid" @scroll="onMediaScroll">
          <div v-for="item in mediaItems" :key="item.id"
               class="olom-media-item" :class="{ 'olom-media-selected': mediaSelected.includes(item.id) }"
               @click="toggleMediaSelect(item)">
            <img :src="item.thumb" :alt="item.title" />
            <div class="olom-media-check" v-if="mediaSelected.includes(item.id)">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="#fff"><path d="M6.5 11.5L3 8l1-1 2.5 2.5 5-5 1 1z"/></svg>
            </div>
          </div>
          <div v-if="mediaLoading" class="olom-media-loading">Caricamento...</div>
          <div v-if="!mediaLoading && !mediaItems.length" class="olom-media-empty">Nessuna immagine trovata.</div>
        </div>
        <div class="olom-modal-footer">
          <button class="olom-btn olom-btn-ghost" @click="showMediaPicker = false">Annulla</button>
          <button class="olom-btn olom-btn-primary" @click="addFromMedia" :disabled="!mediaSelected.length">
            Aggiungi {{ mediaSelected.length || '' }} immagini
          </button>
        </div>
      </div>
    </div>

    <!-- Video Library Picker Modal -->
    <div v-if="showVideoPicker" class="olom-modal-backdrop" @click.self="showVideoPicker = false">
      <div class="olom-modal olom-media-modal">
        <div class="olom-modal-header">
          <h3>Libreria video</h3>
          <button class="olom-modal-close" @click="showVideoPicker = false">&times;</button>
        </div>
        <div class="olom-media-toolbar">
          <input type="text" v-model="videoMediaSearch" @input="debounceVideoSearch" placeholder="Cerca video..." class="olom-media-search" />
        </div>
        <div class="olom-media-grid" @scroll="onVideoMediaScroll">
          <div v-for="item in videoMediaItems" :key="item.id"
               class="olom-media-item" :class="{ 'olom-media-selected': videoMediaSelectedId === item.id }"
               @click="videoMediaSelectedId = (videoMediaSelectedId === item.id ? null : item.id)">
            <div class="olom-video-thumb">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="rgba(255,255,255,0.8)"><path d="M8 5v14l11-7z"/></svg>
            </div>
            <div class="olom-video-thumb-title">{{ item.title }}</div>
            <div class="olom-media-check" v-if="videoMediaSelectedId === item.id">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="#fff"><path d="M6.5 11.5L3 8l1-1 2.5 2.5 5-5 1 1z"/></svg>
            </div>
          </div>
          <div v-if="videoMediaLoading" class="olom-media-loading">Caricamento...</div>
          <div v-if="!videoMediaLoading && !videoMediaItems.length" class="olom-media-empty">Nessun video trovato.</div>
        </div>
        <div class="olom-modal-footer">
          <button class="olom-btn olom-btn-ghost" @click="showVideoPicker = false">Annulla</button>
          <button class="olom-btn olom-btn-primary" @click="confirmVideoPick" :disabled="!videoMediaSelectedId">
            Seleziona video
          </button>
        </div>
      </div>
    </div>
    </main>
  </div>
  <div v-else class="olom-loading">Caricamento...</div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, nextTick } from 'vue';
import { api } from '../stores/api.js';
import RichTextEditor from '../components/RichTextEditor.vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
// Fix Leaflet default icon paths when bundled with Vite
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2x,
  iconUrl: markerIcon,
  shadowUrl: markerShadow,
});
import {
  FileText, BedDouble, KeyRound, MapPin, Award, ListChecks,
  CalendarDays, Tag, CalendarOff, Images, Video,
  ChevronLeft, ExternalLink,
} from 'lucide-vue-next';

const props = defineProps({ id: [String, Number] });
const cfg = window.oloManagerConfig || {};
const perm = cfg.user?.permissions || {};

const sidebarSections = {
  info: [
    { key: 'description', label: 'Descrizione', icon: FileText },
    { key: 'structure', label: 'Struttura', icon: BedDouble },
    { key: 'checkin', label: 'Check-in', icon: KeyRound },
    { key: 'position', label: 'Posizione', icon: MapPin },
    { key: 'club', label: 'Club di Prodotto', icon: Award },
    { key: 'amenities', label: 'Servizi', icon: ListChecks },
  ],
  manage: [
    { key: 'calendar', label: 'Calendario', icon: CalendarDays },
    { key: 'seasons', label: 'Stagioni', icon: Tag },
    { key: 'closures', label: 'Chiusure', icon: CalendarOff },
    { key: 'gallery', label: 'Galleria', icon: Images },
  ],
};
const activeTab = ref('description');
const service = ref(null);
const saving = ref(false);
const savedMsg = ref('');
const uploading = ref(false);
const uploadError = ref('');
const dragOver = ref(false);
const fileInput = ref(null);

const dayLabels = { mon: 'Lunedi', tue: 'Martedi', wed: 'Mercoledi', thu: 'Giovedi', fri: 'Venerdi', sat: 'Sabato', sun: 'Domenica' };

const valleyOptions = [
  'Val di Sole', 'Val di Non', 'Val di Rabbi', 'Val Rendena', 'Val di Fiemme',
  'Val di Fassa', 'Valsugana', 'Val di Cembra', 'Valle dei Laghi', 'Vallagarina',
  'Giudicarie', 'Alto Garda', 'Altopiano della Paganella', 'Val di Ledro',
  'Valle dell\'Adige', 'Altopiano di Pine', 'Val dei Mocheni', 'Val di Gresta',
];

/* Amenity SVG icons (Lucide line-style, 24x24) */
const _i = (d) => `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">${d}</svg>`;
const amenityIcons = {
  wifi:       _i('<path d="M12 20h.01"/><path d="M2 8.82a15 15 0 0 1 20 0"/><path d="M5 12.86a10 10 0 0 1 14 0"/><path d="M8.5 16.9a5 5 0 0 1 7 0"/>'),
  heating:    _i('<path d="M12 12c-2-2.67 0-6 0-6s4 3.33 0 6"/><path d="M12 21a9 9 0 0 0 0-18 9 9 0 0 0 0 18z"/>'),
  aircon:     _i('<path d="M12 2v8"/><path d="m4.93 10.93 1.41 1.41"/><path d="M2 18h2"/><path d="M20 18h2"/><path d="m19.07 10.93-1.41 1.41"/><path d="M22 22H2"/><path d="M16 18a4 4 0 0 0-8 0"/>'),
  fireplace:  _i('<path d="M8 16c0-4 4-8 4-8s4 4 4 8a4 4 0 0 1-8 0"/><rect x="2" y="2" width="20" height="20" rx="2"/>'),
  tv:         _i('<rect x="2" y="7" width="20" height="15" rx="2" ry="2"/><polyline points="17 2 12 7 7 2"/>'),
  pets:       _i('<circle cx="11" cy="4" r="2"/><circle cx="18" cy="8" r="2"/><circle cx="4" cy="8" r="2"/><path d="M12 12c-2 4-6 6-6 6h12s-4-2-6-6"/>'),
  smoking:    _i('<path d="M18 12H2v4h16"/><path d="M22 12v4"/><path d="M7 12v-1a2 2 0 0 1 2-2h0a2 2 0 0 0 2-2V4"/>'),
  elevator:   _i('<rect x="3" y="2" width="18" height="20" rx="2"/><path d="m9 8 3-3 3 3"/><path d="m9 16 3 3 3-3"/>'),
  accessible: _i('<circle cx="16" cy="4" r="1"/><path d="m18 19-3-8H9l-2 8"/><circle cx="9.5" cy="21.5" r="1.5"/>'),
  kitchen:    _i('<path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2l-4 6 4 7"/><path d="M21 15v7"/>'),
  oven:       _i('<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 8h20"/><circle cx="8" cy="14" r="2"/><circle cx="16" cy="14" r="2"/>'),
  microwave:  _i('<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M6 8h8v8H6z"/><circle cx="18" cy="9" r=".5"/><circle cx="18" cy="12" r=".5"/>'),
  dishwasher: _i('<rect x="2" y="2" width="20" height="20" rx="2"/><path d="M2 10h20"/><circle cx="8" cy="6" r="1"/><circle cx="12" cy="16" r="3"/>'),
  fridge:     _i('<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M4 10h16"/><path d="M8 6v2"/><path d="M8 14v3"/>'),
  coffee:     _i('<path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/>'),
  kettle:     _i('<path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><path d="M6 2v2"/><path d="M10 2v2"/>'),
  washer:     _i('<rect x="2" y="2" width="20" height="20" rx="2"/><circle cx="12" cy="14" r="4"/><path d="M2 8h20"/><circle cx="7" cy="5" r="1"/>'),
  dryer:      _i('<path d="M4 14h2"/><path d="M18 14h2"/><path d="M12 2a10 10 0 0 1 0 20 10 10 0 0 1 0-20"/><path d="M12 8a4 4 0 0 0-4 4"/>'),
  iron:       _i('<path d="M2 18h12l6-6c1-1 2-3 2-5V4H10l-8 14z"/><path d="M14 4v4"/>'),
  hairdryer:  _i('<path d="M22 12a4 4 0 0 0-4-4h-2V6a4 4 0 0 0-8 0v8a4 4 0 0 0 4 4h2"/><circle cx="18" cy="12" r="1"/>'),
  bathtub:    _i('<path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1"/><path d="M6 12V5a2 2 0 0 1 2-2h.5"/>'),
  parking:    _i('<rect x="2" y="2" width="20" height="20" rx="4"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>'),
  garage:     _i('<path d="M2 20V8l10-6 10 6v12"/><path d="M6 20v-4h12v4"/><path d="M6 16h12"/>'),
  garden:     _i('<path d="M12 10a6 6 0 0 0-6-6 6 6 0 0 0 6 6"/><path d="M12 10a6 6 0 0 1 6-6 6 6 0 0 1-6 6"/><path d="M12 10v12"/>'),
  terrace:    _i('<path d="M2 22h20"/><path d="M6 18v4"/><path d="M18 18v4"/><rect x="4" y="14" width="16" height="4"/><path d="M12 2v6"/><path d="M8 6l4-4 4 4"/>'),
  bbq:        _i('<path d="M12 2a4 4 0 0 0-4 4c0 2 2 4 4 4s4-2 4-4a4 4 0 0 0-4-4"/><path d="M12 10v6"/><path d="M8 22l4-6 4 6"/>'),
  pool:       _i('<path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/>'),
  hottub:     _i('<path d="M7 2v2"/><path d="M12 2v2"/><path d="M17 2v2"/><path d="M2 8h20v4c0 4-4 8-10 8S2 16 2 12V8z"/>'),
  ski:        _i('<path d="m18 4-6 6"/><path d="m4 20 16-16"/><circle cx="18" cy="4" r="2"/>'),
  bikes:      _i('<circle cx="18.5" cy="17.5" r="3.5"/><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="15" cy="5" r="1"/><path d="M12 17.5V14l-3-3 4-3 2 3h2"/>'),
  playground: _i('<circle cx="12" cy="4" r="2"/><path d="M12 6v8"/><path d="m8 14 4 8 4-8"/>'),
  sauna:      _i('<path d="M12 2v4"/><path d="M8 4v2"/><path d="M16 4v2"/><rect x="4" y="10" width="16" height="12" rx="2"/>'),
  hiking:     _i('<path d="m3 22 2-8h4l2-4 3 6h4l3-12"/>'),
  linens:     _i('<path d="M2 4v16a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8.34a2 2 0 0 0-.59-1.42l-4.34-4.34A2 2 0 0 0 15.66 2H4a2 2 0 0 0-2 2"/><path d="M2 12h20"/>'),
  towels:     _i('<path d="M6 2v6a3 3 0 0 0 6 0V2"/><path d="M12 2v6a3 3 0 0 0 6 0V2"/><path d="M6 14h12v8H6z"/>'),
  cleaning:   _i('<path d="M12 2v8"/><path d="M4.93 10.93l2.83 2.83"/><path d="M2 18h2"/><path d="M20 18h2"/><path d="m19.07 10.93-2.83 2.83"/><path d="M12 22v-4"/>'),
  crib:       _i('<path d="M2 16h20"/><path d="M4 8h16v8H4z"/><path d="M4 16v4"/><path d="M20 16v4"/><path d="M8 8V5a2 2 0 0 1 4 0v3"/>'),
  highchair:  _i('<path d="M6 4h12v6H6z"/><path d="M9 10v12"/><path d="M15 10v12"/><path d="M6 16h12"/>'),
  safe:       _i('<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M12 9v-2"/>'),
};

/* Build amenityCategories from dynamic catalog (oloManagerConfig) with SVG icon fallback */
const _catalogData = (window.oloManagerConfig || {}).amenitiesCatalog;
const amenityCategories = (_catalogData && _catalogData.categories || []).map(cat => ({
  key: cat.key,
  label: cat.label,
  items: (cat.items || []).map(item => {
    let icon;
    if (item.icon && item.icon.startsWith('emoji:')) {
      // Custom emoji icon — render as text span
      icon = `<span style="font-size:18px;line-height:1">${item.icon.slice(6)}</span>`;
    } else {
      // Built-in SVG icon or fallback checkmark
      icon = amenityIcons[item.icon] || amenityIcons[item.key] || _i('<circle cx="12" cy="12" r="3"/>');
    }
    return { key: item.key, icon, label: item.label };
  }),
}));

const amenities = ref([]);
const enabledCats = ref([]);
const maxAmenities = ref(0);
const allCatKeys = amenityCategories.map(c => c.key);

function isCatEnabled(catKey) {
  // If no cats explicitly set, all are enabled by default
  return enabledCats.value.length === 0 || enabledCats.value.includes(catKey);
}

function toggleCat(catKey) {
  // If all enabled (empty array), initialize with all except the one being toggled
  if (enabledCats.value.length === 0) {
    enabledCats.value = allCatKeys.filter(k => k !== catKey);
  } else {
    const idx = enabledCats.value.indexOf(catKey);
    if (idx >= 0) {
      enabledCats.value.splice(idx, 1);
      // Remove amenities belonging to disabled category
      const catItems = amenityCategories.find(c => c.key === catKey)?.items || [];
      catItems.forEach(a => {
        const ai = amenities.value.indexOf(a.key);
        if (ai >= 0) amenities.value.splice(ai, 1);
      });
    } else {
      enabledCats.value.push(catKey);
    }
  }
}

const form = reactive({
  description: '', excerpt: '', price: '', capacity: '', bedrooms: '', beds: '', bathrooms: '', sqm: '',
  checkin_time: '15:00', checkout_time: '10:00', checkin_day: '',
  address: '', directions: '', rules: '', cipat: '',
  opening: '', altitude: '', valley: '', mushrooms: 0,
  club_group: '', club_category: '',
  latitude: '', longitude: '',
  video_1: '', video_2: '', video_3: '',
});
const seasons = ref([]);
const closures = ref([]);
const gallery = ref([]);


/* ── Map ── */
const mapEl = ref(null);
const mapError = ref('');
const mapLoading = ref(false);
let leafletMap = null;
let leafletMarker = null;

/* ── Gallery drag reorder ── */
const galleryDragFrom = ref(-1);
const galleryDragOver = ref(-1);

function onGalleryDragStart(idx) { galleryDragFrom.value = idx; }
function onGalleryDrop(toIdx) {
  const fromIdx = galleryDragFrom.value;
  galleryDragOver.value = -1;
  galleryDragFrom.value = -1;
  if (fromIdx < 0 || fromIdx === toIdx) return;
  const item = gallery.value.splice(fromIdx, 1)[0];
  gallery.value.splice(toIdx, 0, item);
}

/* ── Media Picker ── */
const showMediaPicker = ref(false);
const mediaItems = ref([]);
const mediaSelected = ref([]);
const mediaLoading = ref(false);
const mediaSearch = ref('');
const mediaPage = ref(1);
const mediaHasMore = ref(true);
let mediaSearchTimer = null;

// Video library picker
const showVideoPicker = ref(false);
const videoMediaItems = ref([]);
const videoMediaSelectedId = ref(null);
const videoMediaLoading = ref(false);
const videoMediaSearch = ref('');
const videoMediaPage = ref(1);
const videoMediaHasMore = ref(true);
let videoMediaSearchTimer = null;
let videoPickSlot = null;

/* ── Service Calendar ── */
const calYear = ref(new Date().getFullYear());
const calMonth = ref(new Date().getMonth());
const calBookings = ref([]);
const calLoading = ref(false);
const calMonthNames = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
const calMonthLabel = computed(() => `${calMonthNames[calMonth.value]} ${calYear.value}`);

onMounted(async () => {
  const data = await api.get('/manager/services/' + props.id);
  service.value = data;
  Object.assign(form, {
    description: data.description || '', excerpt: data.excerpt || '',
    price: data.price, capacity: data.capacity, bedrooms: data.bedrooms,
    beds: data.beds, bathrooms: data.bathrooms, sqm: data.sqm,
    checkin_time: data.checkin_time, checkout_time: data.checkout_time, checkin_day: data.checkin_day,
    address: data.address || '', directions: data.directions || '', rules: data.rules || '', cipat: data.cipat || '',
    opening: data.opening, altitude: data.altitude || '', valley: data.valley || '', mushrooms: data.mushrooms || 0,
    club_group: data.club_group || '', club_category: data.club_category || '',
    latitude: data.latitude || '', longitude: data.longitude || '',
    video_1: data.video_1 || '', video_2: data.video_2 || '', video_3: data.video_3 || '',
  });
  amenities.value = data.amenities || [];
  maxAmenities.value = data.max_amenities || 0;
  enabledCats.value = data.enabled_amenity_cats || [];
  seasons.value = (data.seasons || []).map(s => ({ ...s, _open: false }));
  closures.value = data.closures || [];
  gallery.value = data.gallery || [];
});

function onTabChange(key) {
  // Cleanup map when leaving position
  if (activeTab.value === 'position' && key !== 'position') {
    if (leafletMap) {
      try { leafletMap.off(); leafletMap.remove(); } catch(e) {}
      leafletMap = null;
      leafletMarker = null;
    }
  }
  activeTab.value = key;
  if (key === 'position') {
    leafletMap = null;
    leafletMarker = null;
    mapError.value = '';
    mapLoading.value = false;
    // Wait for v-if to render the DOM
    nextTick(() => {
      setTimeout(() => initMap(), 50);
    });
  }
  if (key === 'calendar') {
    loadCalBookings();
  }
}

function showSaved() {
  savedMsg.value = 'Salvato';
  setTimeout(() => savedMsg.value = '', 2000);
}

async function saveInfo() {
  saving.value = true;
  try {
    const result = await api.put('/manager/services/' + props.id, form);
    service.value = result;
    showSaved();
  } finally { saving.value = false; }
}

function addSeason() {
  seasons.value.push({ name: '', date_from: '', date_to: '', price_night: '', min_nights: '1', week_only: '0', note: '', _open: true });
}

async function saveSeasons() {
  saving.value = true;
  try {
    const data = seasons.value.map(({ _open, ...s }) => s);
    await api.put('/manager/services/' + props.id + '/seasons', { seasons: data });
    showSaved();
  } finally { saving.value = false; }
}

async function saveClosures() {
  saving.value = true;
  try {
    await api.put('/manager/services/' + props.id + '/closures', { closures: closures.value });
    showSaved();
  } finally { saving.value = false; }
}

async function saveGallery() {
  saving.value = true;
  try {
    const ids = gallery.value.map(i => i.id);
    await api.put('/manager/services/' + props.id + '/gallery', { gallery: ids });
    showSaved();
  } finally { saving.value = false; }
}

/* ── Video ── */
function getVideoPreviewType(url) {
  if (!url) return '';
  if (/youtube\.com|youtu\.be/i.test(url)) return 'YouTube';
  if (/vimeo\.com/i.test(url)) return 'Vimeo';
  if (/\.(mp4|webm|mov|avi)$/i.test(url)) return 'File video';
  return 'Video';
}

function pickVideo(slot) {
  videoPickSlot = slot;
  videoMediaItems.value = [];
  videoMediaSelectedId.value = null;
  videoMediaSearch.value = '';
  videoMediaPage.value = 1;
  videoMediaHasMore.value = true;
  showVideoPicker.value = true;
  loadVideoMedia();
}

async function loadVideoMedia(append = false) {
  videoMediaLoading.value = true;
  const wpMediaUrl = cfg.restUrl.replace(/\/olo-booking\/v2$/, '') + '/wp/v2/media';
  const params = new URLSearchParams({ media_type: 'video', per_page: '40', page: String(videoMediaPage.value), orderby: 'date', order: 'desc' });
  if (videoMediaSearch.value.trim()) params.set('search', videoMediaSearch.value.trim());
  try {
    const res = await fetch(wpMediaUrl + '?' + params.toString(), { headers: { 'X-WP-Nonce': cfg.nonce } });
    if (!res.ok) { videoMediaLoading.value = false; return; }
    const total = parseInt(res.headers.get('X-WP-TotalPages') || '1');
    videoMediaHasMore.value = videoMediaPage.value < total;
    const items = await res.json();
    const mapped = items.map(m => ({ id: m.id, title: m.title?.rendered || m.source_url?.split('/').pop() || 'Video', url: m.source_url }));
    if (append) videoMediaItems.value.push(...mapped); else videoMediaItems.value = mapped;
  } catch (e) {} finally { videoMediaLoading.value = false; }
}

function debounceVideoSearch() {
  clearTimeout(videoMediaSearchTimer);
  videoMediaSearchTimer = setTimeout(() => { videoMediaPage.value = 1; loadVideoMedia(false); }, 400);
}

function onVideoMediaScroll(e) {
  const el = e.target;
  if (el.scrollTop + el.clientHeight >= el.scrollHeight - 100 && !videoMediaLoading.value && videoMediaHasMore.value) {
    videoMediaPage.value++;
    loadVideoMedia(true);
  }
}

function confirmVideoPick() {
  const item = videoMediaItems.value.find(m => m.id === videoMediaSelectedId.value);
  if (item && videoPickSlot) {
    form['video_' + videoPickSlot] = item.url;
  }
  showVideoPicker.value = false;
}

async function saveVideos() {
  saving.value = true;
  try {
    await api.put('/manager/services/' + props.id, {
      video_1: form.video_1 || '',
      video_2: form.video_2 || '',
      video_3: form.video_3 || '',
    });
    showSaved();
  } finally { saving.value = false; }
}

/* ═══════════════════════════════════
   Amenities
   ═══════════════════════════════════ */

function toggleAmenity(key, catKey) {
  // Block if category disabled
  if (catKey && !isCatEnabled(catKey)) return;
  const idx = amenities.value.indexOf(key);
  if (idx >= 0) {
    amenities.value.splice(idx, 1);
  } else {
    // Block if max reached
    if (maxAmenities.value > 0 && amenities.value.length >= maxAmenities.value) return;
    amenities.value.push(key);
  }
}

async function saveAmenities() {
  saving.value = true;
  try {
    await api.put('/manager/services/' + props.id, {
      amenities: amenities.value,
      max_amenities: maxAmenities.value,
      enabled_amenity_cats: enabledCats.value,
    });
    showSaved();
  } finally { saving.value = false; }
}

/* ═══════════════════════════════════
   Map (Leaflet via npm)
   ═══════════════════════════════════ */

function initMap(retries) {
  if (leafletMap) return;
  if (!mapEl.value) {
    if ((retries || 0) < 8) {
      setTimeout(() => initMap((retries || 0) + 1), 200);
    } else {
      mapError.value = 'Elemento mappa non trovato.';
    }
    return;
  }

  mapError.value = '';
  mapLoading.value = true;

  try {
    const lat = parseFloat(form.latitude) || 46.07;
    const lng = parseFloat(form.longitude) || 11.12;
    const zoom = form.latitude ? 14 : 10;

    leafletMap = L.map(mapEl.value, { scrollWheelZoom: true }).setView([lat, lng], zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap',
      maxZoom: 19,
    }).addTo(leafletMap);

    if (form.latitude && form.longitude) {
      leafletMarker = L.marker([lat, lng]).addTo(leafletMap);
    }

    leafletMap.on('click', (e) => {
      const { lat: la, lng: ln } = e.latlng;
      form.latitude = la.toFixed(6);
      form.longitude = ln.toFixed(6);
      if (leafletMarker) {
        leafletMarker.setLatLng([la, ln]);
      } else {
        leafletMarker = L.marker([la, ln]).addTo(leafletMap);
      }
    });

    setTimeout(() => { if (leafletMap) leafletMap.invalidateSize(); }, 200);
    setTimeout(() => { if (leafletMap) leafletMap.invalidateSize(); }, 1000);
  } catch (err) {
    mapError.value = 'Impossibile caricare la mappa: ' + (err.message || err);
  } finally {
    mapLoading.value = false;
  }
}

function updateMapFromInputs() {
  if (!leafletMap) return;
  const lat = parseFloat(form.latitude);
  const lng = parseFloat(form.longitude);
  if (isNaN(lat) || isNaN(lng)) return;

  leafletMap.setView([lat, lng], 14);
  if (leafletMarker) {
    leafletMarker.setLatLng([lat, lng]);
  } else {
    leafletMarker = L.marker([lat, lng]).addTo(leafletMap);
  }
}

/* ═══════════════════════════════════
   Service Calendar
   ═══════════════════════════════════ */

function changeCalMonth(delta) {
  let m = calMonth.value + delta;
  let y = calYear.value;
  if (m < 0) { m = 11; y--; }
  if (m > 11) { m = 0; y++; }
  calMonth.value = m;
  calYear.value = y;
  loadCalBookings();
}

function calGoToday() {
  calYear.value = new Date().getFullYear();
  calMonth.value = new Date().getMonth();
  loadCalBookings();
}

async function loadCalBookings() {
  calLoading.value = true;
  try {
    const y = calYear.value;
    const m = calMonth.value;
    const from = new Date(y, m, -6);
    const to = new Date(y, m + 1, 7);
    const fmtD = (d) => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    const qs = new URLSearchParams({
      service_id: props.id,
      date_from: fmtD(from),
      date_to: fmtD(to),
    });
    calBookings.value = await api.get('/bookings?' + qs.toString());
  } catch(e) {
    calBookings.value = [];
  } finally {
    calLoading.value = false;
  }
}

/* Calendar grid computation */
function fmtDate(d) {
  return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}
function parseDate(s) { const [y,m,d]=s.split('-').map(Number); return new Date(y,m-1,d); }
function dayBefore(s) { const d=parseDate(s); d.setDate(d.getDate()-1); return fmtDate(d); }
function daysDiff(a,b) { return Math.round((parseDate(b)-parseDate(a))/86400000); }

const calWeeks = computed(() => {
  const year = calYear.value;
  const month = calMonth.value;
  const first = new Date(year, month, 1);
  let offset = first.getDay() - 1;
  if (offset < 0) offset = 6;
  const gridStart = new Date(first);
  gridStart.setDate(gridStart.getDate() - offset);
  const todayStr = fmtDate(new Date());
  const svcColor = service.value?.color || '#6366F1';
  const weeks = [];
  const cur = new Date(gridStart);

  while (true) {
    const days = [];
    const weekStartStr = fmtDate(cur);
    for (let i = 0; i < 7; i++) {
      days.push({ date: fmtDate(cur), num: cur.getDate(), inMonth: cur.getMonth() === month, isToday: fmtDate(cur) === todayStr });
      cur.setDate(cur.getDate() + 1);
    }
    const weekEndStr = days[6].date;

    const bars = [];
    for (const b of calBookings.value) {
      const lastNight = dayBefore(b.checkout_date);
      if (b.checkin_date > weekEndStr || lastNight < weekStartStr) continue;
      const barVisStart = b.checkin_date < weekStartStr ? weekStartStr : b.checkin_date;
      const barVisEnd = lastNight > weekEndStr ? weekEndStr : lastNight;
      const col1 = daysDiff(weekStartStr, barVisStart) + 1;
      const col2 = daysDiff(weekStartStr, barVisEnd) + 1;
      if (col1 > 7 || col2 < 1) continue;
      bars.push({
        id: b.id, key: b.id+'-w'+weeks.length,
        col1: Math.max(1,col1), col2: Math.min(7,col2), lane: 0,
        isStart: b.checkin_date >= weekStartStr, isEnd: lastNight <= weekEndStr,
        label: b.guest_name,
        showLabel: b.checkin_date >= weekStartStr || weeks.length === 0,
        title: `${b.guest_name}\n${b.checkin_date} → ${b.checkout_date} (${b.nights} notti)\n${b.status === 'confirmed' ? 'Confermata' : b.status === 'pending' ? 'In attesa' : b.status}`,
        color: svcColor, status: b.status,
      });
    }

    bars.sort((a,b) => a.col1 - b.col1 || (b.col2-b.col1) - (a.col2-a.col1));
    const laneFree = [];
    for (const bar of bars) {
      let assigned = false;
      for (let i = 0; i < laneFree.length; i++) {
        if (laneFree[i] < bar.col1) { bar.lane = i; laneFree[i] = bar.col2; assigned = true; break; }
      }
      if (!assigned) { bar.lane = laneFree.length; laneFree.push(bar.col2); }
    }
    weeks.push({ days, bars, maxLane: Math.max(1, laneFree.length) });
    if (cur.getMonth() !== month && weeks.length >= 5) break;
    if (weeks.length >= 6) break;
  }
  return weeks;
});

/* ── Calendar: Quick Booking ── */
const showQuickBooking = ref(false);
const quickSaving = ref(false);
const quickError = ref('');
const quickForm = reactive({
  checkin_date: '', checkout_date: '', guest_name: '', guest_email: '',
  guest_phone: '', guest_count: 1, status: 'confirmed', notes: '', source: 'manual',
});

function onCalCellClick(day) {
  if (!day.inMonth) return;
  // Pre-fill checkin = clicked date, checkout = next day
  const d = parseDate(day.date);
  const d2 = new Date(d); d2.setDate(d2.getDate() + 1);
  Object.assign(quickForm, {
    checkin_date: day.date,
    checkout_date: fmtDate(d2),
    guest_name: '', guest_email: '', guest_phone: '',
    guest_count: 1, status: 'confirmed', notes: '', source: 'manual',
  });
  quickError.value = '';
  showQuickBooking.value = true;
}

async function submitQuickBooking() {
  quickError.value = '';
  if (!quickForm.guest_name || !quickForm.checkin_date || !quickForm.checkout_date) {
    quickError.value = 'Compila nome ospite e date.';
    return;
  }
  quickSaving.value = true;
  try {
    await api.post('/bookings', { ...quickForm, service_id: Number(props.id) });
    showQuickBooking.value = false;
    await loadCalBookings();
  } catch (e) {
    quickError.value = e.message;
  } finally {
    quickSaving.value = false;
  }
}

/* ── Calendar: Drag & Drop Bookings ── */
const calDragging = ref(null);
const calDragOver = ref('');

function onCalBarDragStart(e, bar) {
  // Find the original booking data
  const booking = calBookings.value.find(b => b.id === bar.id);
  if (!booking || booking.status === 'cancelled') {
    e.preventDefault();
    return;
  }
  calDragging.value = { id: bar.id, checkin: booking.checkin_date, checkout: booking.checkout_date, nights: booking.nights };
  e.dataTransfer.effectAllowed = 'move';
  e.dataTransfer.setData('text/plain', bar.id);
}

function onCalBarDragEnd() {
  calDragging.value = null;
  calDragOver.value = '';
}

async function onCalDrop(day) {
  calDragOver.value = '';
  if (!calDragging.value || !day.inMonth) return;
  const drag = calDragging.value;
  calDragging.value = null;

  // Calculate new dates: new checkin = dropped date, keep same number of nights
  const newCheckin = day.date;
  if (newCheckin === drag.checkin) return; // same position

  const nights = drag.nights || daysDiff(drag.checkin, drag.checkout);
  const d2 = parseDate(newCheckin);
  d2.setDate(d2.getDate() + nights);
  const newCheckout = fmtDate(d2);

  try {
    await api.put('/bookings/' + drag.id, { checkin_date: newCheckin, checkout_date: newCheckout });
    await loadCalBookings();
  } catch (e) {
    alert('Impossibile spostare: ' + e.message);
  }
}

/* ═══════════════════════════════════
   Gallery upload
   ═══════════════════════════════════ */

function onFileSelect(e) {
  const files = e.target.files;
  if (files.length) uploadFiles(files);
  e.target.value = '';
}

function onDrop(e) {
  dragOver.value = false;
  const files = e.dataTransfer.files;
  if (files.length) uploadFiles(files);
}

function removeImage(idx) { gallery.value.splice(idx, 1); }

async function uploadFiles(fileList) {
  uploadError.value = '';
  uploading.value = true;
  const wpMediaUrl = cfg.restUrl.replace(/\/olo-booking\/v2$/, '') + '/wp/v2/media';
  try {
    for (const file of fileList) {
      if (!file.type.startsWith('image/')) { uploadError.value = 'Solo immagini sono supportate.'; continue; }
      if (file.size > 5*1024*1024) { uploadError.value = 'Immagine troppo grande (max 5 MB): '+file.name; continue; }
      const formData = new FormData();
      formData.append('file', file);
      formData.append('title', file.name.replace(/\.[^.]+$/, ''));
      const res = await fetch(wpMediaUrl, { method: 'POST', headers: { 'X-WP-Nonce': cfg.nonce }, body: formData });
      if (!res.ok) { const err = await res.json().catch(() => ({})); uploadError.value = err.message || 'Errore upload: '+file.name; continue; }
      const media = await res.json();
      gallery.value.push({
        id: media.id,
        thumb: media.media_details?.sizes?.thumbnail?.source_url || media.source_url,
        full: media.media_details?.sizes?.large?.source_url || media.source_url,
      });
    }
    if (gallery.value.length) await saveGallery();
  } catch (e) { uploadError.value = 'Errore: '+(e.message||''); }
  finally { uploading.value = false; }
}

/* ═══════════════════════════════════
   Media Library Picker
   ═══════════════════════════════════ */

function openMediaPicker() {
  mediaItems.value = []; mediaSelected.value = []; mediaSearch.value = '';
  mediaPage.value = 1; mediaHasMore.value = true;
  showMediaPicker.value = true; loadMedia();
}

async function loadMedia(append = false) {
  mediaLoading.value = true;
  const wpMediaUrl = cfg.restUrl.replace(/\/olo-booking\/v2$/, '') + '/wp/v2/media';
  const params = new URLSearchParams({ media_type: 'image', per_page: '40', page: String(mediaPage.value), orderby: 'date', order: 'desc' });
  if (mediaSearch.value.trim()) params.set('search', mediaSearch.value.trim());
  try {
    const res = await fetch(wpMediaUrl+'?'+params.toString(), { headers: { 'X-WP-Nonce': cfg.nonce } });
    if (!res.ok) { mediaLoading.value = false; return; }
    const total = parseInt(res.headers.get('X-WP-TotalPages') || '1');
    mediaHasMore.value = mediaPage.value < total;
    const items = await res.json();
    const mapped = items.map(m => ({ id: m.id, title: m.title?.rendered||'', thumb: m.media_details?.sizes?.thumbnail?.source_url||m.source_url, full: m.media_details?.sizes?.large?.source_url||m.source_url }));
    const existingIds = new Set(gallery.value.map(g => g.id));
    const filtered = mapped.filter(m => !existingIds.has(m.id));
    if (append) mediaItems.value.push(...filtered); else mediaItems.value = filtered;
  } catch(e) {} finally { mediaLoading.value = false; }
}

function debounceMediaSearch() { clearTimeout(mediaSearchTimer); mediaSearchTimer = setTimeout(() => { mediaPage.value = 1; loadMedia(false); }, 400); }
function onMediaScroll(e) { const el=e.target; if (el.scrollTop+el.clientHeight >= el.scrollHeight-100 && !mediaLoading.value && mediaHasMore.value) { mediaPage.value++; loadMedia(true); } }
function toggleMediaSelect(item) { const idx = mediaSelected.value.indexOf(item.id); if (idx>=0) mediaSelected.value.splice(idx,1); else mediaSelected.value.push(item.id); }

async function addFromMedia() {
  const selectedItems = mediaItems.value.filter(m => mediaSelected.value.includes(m.id));
  for (const item of selectedItems) gallery.value.push({ id: item.id, thumb: item.thumb, full: item.full });
  showMediaPicker.value = false;
  await saveGallery();
}
</script>
