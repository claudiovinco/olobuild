<template>
  <div class="login-settings">
    <h2>Aspetto Pagina Login</h2>
    <p class="ls-desc">Personalizza logo, colori e sfondo della pagina di accesso al pannello.</p>

    <div class="ls-layout">
      <!-- Settings Panel -->
      <div class="ls-panel">

        <!-- Slug -->
        <div class="ls-field">
          <label>Percorso Login</label>
          <div class="ls-slug-row">
            <span class="ls-slug-prefix">{{ siteUrl }}/</span>
            <input v-model="form.slug" type="text" placeholder="gestione" class="ls-input ls-slug-input" />
            <span class="ls-slug-suffix">/</span>
          </div>
          <small class="ls-hint">Cambiando il percorso, i vecchi link smetteranno di funzionare.</small>
        </div>

        <!-- Logo -->
        <div class="ls-field">
          <label>Logo</label>
          <div class="ls-img-picker">
            <img v-if="form.logo_url" :src="form.logo_url" class="ls-img-preview" :style="{ height: form.logo_height + 'px' }" />
            <div class="ls-img-actions">
              <button type="button" class="ls-btn ls-btn-sm" @click="pickImage('logo')">Scegli immagine</button>
              <button v-if="form.logo_url" type="button" class="ls-btn ls-btn-sm ls-btn-ghost" @click="form.logo_url = ''">Rimuovi</button>
            </div>
          </div>
          <div class="ls-size-row">
            <label>Altezza logo (px)</label>
            <input v-model.number="form.logo_height" type="range" min="20" max="120" class="ls-range" />
            <span class="ls-size-val">{{ form.logo_height }}px</span>
          </div>
        </div>

        <!-- Background Color -->
        <div class="ls-field">
          <label>Colore sfondo</label>
          <div class="ls-color-row">
            <input v-model="form.bg_color" type="color" class="ls-color-input" />
            <input v-model="form.bg_color" type="text" placeholder="#667eea" class="ls-input ls-color-text" />
            <button v-if="form.bg_color" type="button" class="ls-btn ls-btn-sm ls-btn-ghost" @click="form.bg_color = ''">Reset</button>
          </div>
        </div>

        <!-- Background Image -->
        <div class="ls-field">
          <label>Immagine sfondo <small>(opzionale, sovrappone il colore)</small></label>
          <div class="ls-img-picker">
            <img v-if="form.bg_image_url" :src="form.bg_image_url" class="ls-img-preview ls-img-bg" />
            <div class="ls-img-actions">
              <button type="button" class="ls-btn ls-btn-sm" @click="pickImage('bg')">Scegli immagine</button>
              <button v-if="form.bg_image_url" type="button" class="ls-btn ls-btn-sm ls-btn-ghost" @click="form.bg_image_url = ''">Rimuovi</button>
            </div>
          </div>
          <div v-if="bgSizes.length > 1" class="ls-size-row">
            <label>Dimensione</label>
            <select v-model="selectedBgSize" class="ls-select" @change="applyBgSize">
              <option v-for="s in bgSizes" :key="s.name" :value="s.name">
                {{ s.label }} ({{ s.width }}&times;{{ s.height }})
              </option>
            </select>
          </div>
        </div>

        <!-- Button Color -->
        <div class="ls-field">
          <label>Colore bottone</label>
          <div class="ls-color-row">
            <input v-model="form.btn_color" type="color" class="ls-color-input" />
            <input v-model="form.btn_color" type="text" placeholder="#6366F1" class="ls-input ls-color-text" />
            <button v-if="form.btn_color && form.btn_color !== '#6366F1'" type="button" class="ls-btn ls-btn-sm ls-btn-ghost" @click="form.btn_color = '#6366F1'">Reset</button>
          </div>
        </div>

        <!-- Save -->
        <div class="ls-actions">
          <button class="ls-btn ls-btn-primary" :disabled="saving" @click="save">
            {{ saving ? 'Salvataggio...' : 'Salva' }}
          </button>
          <span v-if="saved" class="ls-saved">Salvato!</span>
        </div>
      </div>

      <!-- Live Preview -->
      <div class="ls-preview-wrap">
        <label>Anteprima</label>
        <div class="ls-preview" :style="previewBgStyle">
          <div class="ls-preview-card">
            <div class="ls-preview-logo">
              <img v-if="previewLogo" :src="previewLogo" :style="{ height: (form.logo_height * 0.6) + 'px' }" />
              <strong v-else :style="{ color: previewBtnColor }">Olo Booking</strong>
            </div>
            <div class="ls-preview-subtitle">Pannello Gestione Strutture</div>
            <div class="ls-preview-field"></div>
            <div class="ls-preview-field"></div>
            <div class="ls-preview-btn" :style="{ background: previewBtnColor }">Accedi</div>
          </div>
          <div class="ls-preview-footer">@2026 Olo Booking by OloBuild</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const cfg = window.oloManagerConfig || {};
const api = cfg.restUrl;
const nonce = cfg.nonce;
const siteUrl = (cfg.homeUrl || '').replace(/\/+$/, '');

const form = ref({
  logo_url: '',
  logo_height: 36,
  bg_color: '',
  bg_image_url: '',
  btn_color: '#6366F1',
  slug: 'gestione',
});

const saving = ref(false);
const saved = ref(false);
const bgSizes = ref([]);
const selectedBgSize = ref('full');

onMounted(async () => {
  try {
    const res = await fetch(`${api}/manager/theme`, {
      headers: { 'X-WP-Nonce': nonce },
    });
    const data = await res.json();
    if (data.login) {
      form.value.logo_url = data.login.logo_url || '';
      form.value.logo_height = data.login.logo_height || 36;
      form.value.bg_color = data.login.bg_color || '';
      form.value.bg_image_url = data.login.bg_image_url || '';
      form.value.btn_color = data.login.btn_color || '#6366F1';
      form.value.slug = data.login.slug || 'gestione';
    }
  } catch (e) {
    console.error('Failed to load theme:', e);
  }
});

const sizeLabels = { thumbnail: 'Miniatura', medium: 'Media', medium_large: 'Media grande', large: 'Grande', full: 'Originale' };

function pickImage(target) {
  const frame = wp.media({
    title: target === 'logo' ? 'Scegli logo' : 'Scegli immagine sfondo',
    multiple: false,
    library: { type: 'image' },
  });
  frame.on('select', () => {
    const att = frame.state().get('selection').first().toJSON();
    if (target === 'logo') {
      form.value.logo_url = att.url;
    } else {
      // Collect available sizes for bg image
      const sizes = att.sizes || {};
      const list = [];
      for (const [name, info] of Object.entries(sizes)) {
        list.push({ name, label: sizeLabels[name] || name, url: info.url, width: info.width, height: info.height });
      }
      list.sort((a, b) => a.width - b.width);
      bgSizes.value = list;
      // Default to full
      const full = sizes.full || sizes.large;
      selectedBgSize.value = 'full';
      form.value.bg_image_url = full ? full.url : att.url;
    }
  });
  frame.open();
}

function applyBgSize() {
  const found = bgSizes.value.find(s => s.name === selectedBgSize.value);
  if (found) {
    form.value.bg_image_url = found.url;
  }
}

async function save() {
  saving.value = true;
  saved.value = false;
  try {
    const res = await fetch(`${api}/manager/theme`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': nonce,
      },
      body: JSON.stringify({ login: { ...form.value } }),
    });
    const data = await res.json();
    saved.value = true;
    setTimeout(() => saved.value = false, 3000);

    if (data.slug_changed) {
      // Redirect to new slug
      window.location.href = `${siteUrl}/${data.new_slug}/#/login-settings`;
    }
  } catch (e) {
    alert('Errore durante il salvataggio.');
  } finally {
    saving.value = false;
  }
}

const previewLogo = computed(() => form.value.logo_url);
const previewBtnColor = computed(() => form.value.btn_color || '#6366F1');
const previewBgStyle = computed(() => {
  const bg = form.value.bg_color;
  const img = form.value.bg_image_url;
  if (img) {
    return { background: `url(${img}) center/cover no-repeat` + (bg ? `, ${bg}` : '') };
  }
  if (bg) {
    return { background: bg };
  }
  return { background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' };
});
</script>

<style scoped>
.login-settings { max-width: 960px; margin: 0 auto; }
.login-settings h2 { margin-bottom: 4px; }
.ls-desc { color: #6B7280; font-size: 14px; margin-bottom: 24px; }

.ls-layout {
  display: grid;
  grid-template-columns: 1fr 280px;
  gap: 32px;
  align-items: start;
}
@media (max-width: 768px) {
  .ls-layout { grid-template-columns: 1fr; }
}

.ls-panel {
  background: #fff;
  border: 1px solid #E5E7EB;
  border-radius: 12px;
  padding: 24px;
}

.ls-field { margin-bottom: 20px; }
.ls-field > label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 6px;
}
.ls-field small { color: #9CA3AF; }
.ls-hint { display: block; margin-top: 4px; font-size: 12px; color: #9CA3AF; }

.ls-input {
  padding: 8px 12px;
  border: 1px solid #D1D5DB;
  border-radius: 6px;
  font-size: 14px;
  outline: none;
}
.ls-input:focus { border-color: #6366F1; }

.ls-slug-row {
  display: flex;
  align-items: center;
  gap: 0;
}
.ls-slug-prefix, .ls-slug-suffix {
  font-size: 14px;
  color: #6B7280;
  background: #F3F4F6;
  padding: 8px 10px;
  border: 1px solid #D1D5DB;
}
.ls-slug-prefix {
  border-radius: 6px 0 0 6px;
  border-right: none;
}
.ls-slug-suffix {
  border-radius: 0 6px 6px 0;
  border-left: none;
}
.ls-slug-input {
  border-radius: 0;
  flex: 1;
  min-width: 100px;
}

.ls-color-row {
  display: flex;
  align-items: center;
  gap: 8px;
}
.ls-color-input {
  width: 40px;
  height: 36px;
  border: 1px solid #D1D5DB;
  border-radius: 6px;
  padding: 2px;
  cursor: pointer;
}
.ls-color-text { width: 100px; }

.ls-img-picker {
  display: flex;
  align-items: center;
  gap: 12px;
}
.ls-img-preview {
  height: 40px;
  border-radius: 4px;
  border: 1px solid #E5E7EB;
}
.ls-img-bg {
  height: 60px;
  width: 100px;
  object-fit: cover;
}
.ls-img-actions {
  display: flex;
  gap: 6px;
}

.ls-size-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
}
.ls-size-row label {
  font-size: 12px;
  color: #6B7280;
  white-space: nowrap;
}
.ls-range {
  flex: 1;
  accent-color: #6366F1;
  max-width: 160px;
}
.ls-size-val {
  font-size: 12px;
  color: #374151;
  font-weight: 600;
  min-width: 40px;
}
.ls-select {
  padding: 5px 8px;
  border: 1px solid #D1D5DB;
  border-radius: 6px;
  font-size: 12px;
  outline: none;
}
.ls-select:focus { border-color: #6366F1; }

.ls-btn {
  padding: 8px 16px;
  border: 1px solid #D1D5DB;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  background: #fff;
  color: #374151;
  transition: all 0.15s;
}
.ls-btn:hover { background: #F9FAFB; }
.ls-btn-sm { padding: 5px 10px; font-size: 12px; }
.ls-btn-ghost { border: none; color: #EF4444; }
.ls-btn-ghost:hover { background: #FEF2F2; }
.ls-btn-primary {
  background: #6366F1;
  color: #fff;
  border-color: #6366F1;
}
.ls-btn-primary:hover { background: #4F46E5; }
.ls-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.ls-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 24px;
  padding-top: 16px;
  border-top: 1px solid #E5E7EB;
}
.ls-saved { color: #16A34A; font-size: 13px; font-weight: 500; }

/* Preview */
.ls-preview-wrap > label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}
.ls-preview {
  border-radius: 12px;
  padding: 24px 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
  border: 1px solid #E5E7EB;
  gap: 12px;
}
.ls-preview-card {
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  width: 100%;
  max-width: 200px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  text-align: center;
}
.ls-preview-logo { margin-bottom: 4px; }
.ls-preview-logo img { max-height: 28px; }
.ls-preview-logo strong { font-size: 14px; }
.ls-preview-subtitle {
  font-size: 8px;
  color: #9CA3AF;
  margin-bottom: 12px;
}
.ls-preview-field {
  height: 20px;
  background: #F3F4F6;
  border-radius: 4px;
  margin-bottom: 8px;
}
.ls-preview-btn {
  height: 24px;
  border-radius: 4px;
  color: #fff;
  font-size: 9px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 4px;
}
.ls-preview-footer {
  font-size: 8px;
  color: rgba(255,255,255,0.7);
  text-align: center;
}
</style>
