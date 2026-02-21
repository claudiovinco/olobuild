<template>
  <div class="olom-users">
    <div class="olom-page-header">
      <h2 class="olom-page-title">{{ isAdmin ? 'Utenti' : 'Gestori' }}</h2>
      <button v-if="perm.create_users" class="olom-btn olom-btn-primary" @click="openCreate">+ Nuovo utente</button>
    </div>

    <!-- Users table -->
    <div class="olom-user-table" v-if="users.length">
      <div class="olom-ut-header">
        <span class="olom-ut-col olom-ut-name">Nome</span>
        <span class="olom-ut-col olom-ut-email">Email</span>
        <span v-if="isAdmin" class="olom-ut-col olom-ut-role">Ruolo</span>
        <span class="olom-ut-col olom-ut-services">Strutture assegnate</span>
        <span class="olom-ut-col olom-ut-actions">Azioni</span>
      </div>
      <div v-for="u in users" :key="u.id" class="olom-ut-row">
        <span class="olom-ut-col olom-ut-name">
          <span class="olom-ut-name-row">
            <img v-if="u.photo_url" :src="u.photo_url" class="olom-ut-avatar" />
            <span>
              <strong>{{ u.display_name }}</strong>
              <small class="olom-ut-username">@{{ u.username }}</small>
            </span>
          </span>
          <span v-if="u.languages && u.languages.length" class="olom-ut-langs">
            <span v-for="l in u.languages" :key="l" class="olom-ut-lang">{{ langFlag(l) }}</span>
          </span>
        </span>
        <span class="olom-ut-col olom-ut-email">{{ u.email }}</span>
        <span v-if="isAdmin" class="olom-ut-col olom-ut-role">
          <span class="olom-role-badge" :class="'olom-role-' + u.role">{{ u.role_label }}</span>
        </span>
        <span class="olom-ut-col olom-ut-services">
          <template v-if="u.role === 'administrator'">
            <span class="olom-ut-all">Tutte</span>
          </template>
          <template v-else-if="u.services.length">
            <span v-for="s in u.services" :key="s.id" class="olom-ut-svc-tag" :style="{ borderColor: s.color }">
              <span class="olom-svc-dot" :style="{ background: s.color }"></span>
              {{ s.title }}
            </span>
          </template>
          <template v-else>
            <span class="olom-ut-none">Nessuna</span>
          </template>
        </span>
        <span class="olom-ut-col olom-ut-actions">
          <button v-if="canEditUser(u)" class="olom-btn-icon" @click="openEdit(u)" :title="editBtnLabel">&#9998;</button>
          <button v-if="perm.delete_users && u.id !== currentUserId && canEditUser(u)" class="olom-btn-icon olom-btn-danger-text" @click="confirmDelete(u)" title="Elimina">&#10005;</button>
        </span>
      </div>
    </div>
    <div v-else-if="!loading" class="olom-empty">Nessun utente trovato.</div>
    <div v-if="loading" class="olom-loading">Caricamento...</div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="olom-modal-backdrop" @click.self="showModal = false">
      <div class="olom-modal">
        <div class="olom-modal-header">
          <h3>{{ modalTitle }}</h3>
          <button class="olom-modal-close" @click="showModal = false">&times;</button>
        </div>
        <div class="olom-modal-body">
          <div v-if="formError" class="olom-form-error">{{ formError }}</div>

          <!-- Profile fields: visible if creating OR if can edit profile -->
          <template v-if="!isEditing || perm.edit_user_profile">
            <div class="olom-form-grid">
              <div class="olom-form-row" v-if="!isEditing">
                <label>Username *</label>
                <input type="text" v-model="form.username" autocomplete="off" />
              </div>
              <div class="olom-form-row">
                <label>Nome visualizzato</label>
                <input type="text" v-model="form.display_name" />
              </div>
            </div>
            <div class="olom-form-grid">
              <div class="olom-form-row">
                <label>Email *</label>
                <input type="email" v-model="form.email" />
              </div>
              <div class="olom-form-row">
                <label>{{ isEditing ? 'Nuova password (vuoto = invariata)' : 'Password *' }}</label>
                <input type="password" v-model="form.password" autocomplete="new-password" />
              </div>
            </div>
          </template>

          <!-- Supervisor creating: show basic fields only -->
          <template v-else-if="!perm.edit_user_profile && isEditing">
            <div class="olom-form-row">
              <label>Gestore</label>
              <div class="olom-ut-readonly">{{ form.display_name }} <small>(@{{ form.username }})</small></div>
            </div>
          </template>

          <!-- Profilo esteso: visible if creating OR if can edit profile -->
          <template v-if="!isEditing || perm.edit_user_profile">
            <h4 class="olom-form-section-title">Profilo pubblico</h4>

            <!-- Foto profilo -->
            <div class="olom-form-row">
              <label>Foto profilo</label>
              <div class="olom-photo-upload">
                <div v-if="form.photo_url" class="olom-photo-preview">
                  <img :src="form.photo_url" alt="Foto" />
                  <button class="olom-photo-remove" @click="form.photo_id = 0; form.photo_url = ''">&times;</button>
                </div>
                <button v-else class="olom-btn olom-btn-ghost olom-btn-sm" @click="$refs.photoInput.click()">Carica foto</button>
                <button v-if="form.photo_url" class="olom-btn olom-btn-ghost olom-btn-sm" @click="$refs.photoInput.click()">Cambia</button>
                <input ref="photoInput" type="file" accept="image/*" style="display:none" @change="uploadPhoto" />
              </div>
            </div>

            <!-- Lingue parlate -->
            <div class="olom-form-row">
              <label>Lingue parlate</label>
              <div class="olom-lang-picker">
                <label v-for="lang in languageOptions" :key="lang.code" class="olom-lang-item"
                       :class="{ 'olom-lang-active': form.languages.includes(lang.code) }">
                  <input type="checkbox" :value="lang.code" v-model="form.languages" />
                  <span class="olom-lang-flag">{{ lang.flag }}</span>
                  <span>{{ lang.label }}</span>
                </label>
              </div>
            </div>

            <div class="olom-form-grid">
              <div class="olom-form-row">
                <label>Email pubblica (per il sito)</label>
                <input type="email" v-model="form.public_email" placeholder="visibile ai visitatori" />
              </div>
              <div class="olom-form-row">
                <label>Telefono pubblico (per il sito)</label>
                <input type="tel" v-model="form.public_phone" placeholder="+39..." />
              </div>
            </div>

            <div class="olom-form-row">
              <label>Note / Biografia</label>
              <textarea v-model="form.bio" rows="3" placeholder="Breve presentazione del gestore..."></textarea>
            </div>
          </template>

          <!-- Role select: admin only -->
          <div class="olom-form-row" v-if="isAdmin">
            <label>Ruolo</label>
            <select v-model="form.role">
              <option value="olo_manager">Gestore</option>
              <option value="olo_supervisor">Supervisore</option>
              <option value="administrator">Amministratore</option>
            </select>
          </div>

          <!-- Service picker -->
          <div class="olom-form-row" v-if="perm.assign_services && form.role !== 'administrator'">
            <label>Strutture assegnate</label>
            <div class="olom-svc-picker">
              <label v-for="svc in allServices" :key="svc.id" class="olom-svc-pick-item">
                <input type="checkbox" :value="svc.id" v-model="form.service_ids" />
                <span class="olom-svc-dot" :style="{ background: svc.color }"></span>
                {{ svc.title }}
              </label>
            </div>
          </div>
        </div>
        <div class="olom-modal-footer">
          <button class="olom-btn olom-btn-ghost" @click="showModal = false">Annulla</button>
          <button class="olom-btn olom-btn-success" @click="saveUser" :disabled="saving">
            {{ saving ? 'Salvataggio...' : (isEditing ? 'Salva' : 'Crea utente') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete confirmation modal -->
    <div v-if="showDeleteModal" class="olom-modal-backdrop" @click.self="showDeleteModal = false">
      <div class="olom-modal" style="max-width:420px">
        <div class="olom-modal-header">
          <h3>Conferma eliminazione</h3>
          <button class="olom-modal-close" @click="showDeleteModal = false">&times;</button>
        </div>
        <div class="olom-modal-body">
          <p>Vuoi davvero eliminare l'utente <strong>{{ deleteTarget?.display_name }}</strong> (@{{ deleteTarget?.username }})?</p>
          <p style="color:var(--olom-danger);font-size:13px;margin-top:8px">Questa azione non puo essere annullata.</p>
        </div>
        <div class="olom-modal-footer">
          <button class="olom-btn olom-btn-ghost" @click="showDeleteModal = false">Annulla</button>
          <button class="olom-btn olom-btn-danger" @click="doDelete" :disabled="saving">Elimina</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { api } from '../stores/api.js';
import { useManagerStore } from '../stores/manager.js';

const mgrStore = useManagerStore();
const cfg = window.oloManagerConfig || {};
const currentUserId = cfg.user?.id || 0;
const accessLevel = cfg.user?.access_level || 'none';
const isAdmin = accessLevel === 'admin';
const perm = cfg.user?.permissions || {};

const users = ref([]);
const allServices = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const deleteTarget = ref(null);
const formError = ref('');

const form = reactive({
  username: '',
  display_name: '',
  email: '',
  password: '',
  role: 'olo_manager',
  service_ids: [],
  // Profilo esteso
  photo_id: 0,
  photo_url: '',
  languages: [],
  bio: '',
  public_email: '',
  public_phone: '',
});

const photoInput = ref(null);

const languageOptions = [
  { code: 'it', flag: '🇮🇹', label: 'Italiano' },
  { code: 'de', flag: '🇩🇪', label: 'Tedesco' },
  { code: 'en', flag: '🇬🇧', label: 'Inglese' },
  { code: 'fr', flag: '🇫🇷', label: 'Francese' },
  { code: 'es', flag: '🇪🇸', label: 'Spagnolo' },
  { code: 'pt', flag: '🇵🇹', label: 'Portoghese' },
  { code: 'ru', flag: '🇷🇺', label: 'Russo' },
  { code: 'zh', flag: '🇨🇳', label: 'Cinese' },
];

const modalTitle = computed(() => {
  if (!isEditing.value) return 'Nuovo utente';
  if (perm.edit_user_profile) return 'Modifica utente';
  if (perm.assign_services) return 'Assegna strutture';
  return 'Modifica';
});

const editBtnLabel = computed(() => {
  if (perm.edit_user_profile) return 'Modifica';
  if (perm.assign_services) return 'Assegna strutture';
  return 'Modifica';
});

const langFlagMap = { it:'🇮🇹', de:'🇩🇪', en:'🇬🇧', fr:'🇫🇷', es:'🇪🇸', pt:'🇵🇹', ru:'🇷🇺', zh:'🇨🇳' };
function langFlag(code) { return langFlagMap[code] || code; }

function canEditUser(u) {
  if (isAdmin) return true;
  // Non-admin can only edit managers
  return u.role === 'olo_manager' && (perm.edit_user_profile || perm.assign_services);
}

onMounted(async () => {
  if (!mgrStore.services.length) await mgrStore.fetchServices();
  allServices.value = mgrStore.services;
  await loadUsers();
});

async function loadUsers() {
  loading.value = true;
  try {
    users.value = await api.get('/manager/users');
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

function openCreate() {
  isEditing.value = false;
  editingId.value = null;
  formError.value = '';
  form.username = '';
  form.display_name = '';
  form.email = '';
  form.password = '';
  form.role = 'olo_manager';
  form.service_ids = [];
  form.photo_id = 0;
  form.photo_url = '';
  form.languages = [];
  form.bio = '';
  form.public_email = '';
  form.public_phone = '';
  showModal.value = true;
}

function openEdit(u) {
  isEditing.value = true;
  editingId.value = u.id;
  formError.value = '';
  form.username = u.username;
  form.display_name = u.display_name;
  form.email = u.email;
  form.password = '';
  form.role = u.role;
  form.service_ids = u.services.map(s => s.id);
  form.photo_id = u.photo_id || 0;
  form.photo_url = u.photo_url || '';
  form.languages = u.languages || [];
  form.bio = u.bio || '';
  form.public_email = u.public_email || '';
  form.public_phone = u.public_phone || '';
  showModal.value = true;
}

function confirmDelete(u) {
  deleteTarget.value = u;
  showDeleteModal.value = true;
}

async function saveUser() {
  formError.value = '';
  saving.value = true;
  try {
    if (isEditing.value) {
      const payload = {};
      // Profile fields
      if (perm.edit_user_profile) {
        payload.display_name = form.display_name;
        payload.email = form.email;
        if (form.password) payload.password = form.password;
        // Extended profile
        payload.photo_id = form.photo_id;
        payload.languages = form.languages;
        payload.bio = form.bio;
        payload.public_email = form.public_email;
        payload.public_phone = form.public_phone;
      }
      // Role (admin only)
      if (isAdmin) {
        payload.role = form.role;
      }
      // Service assignment
      if (perm.assign_services) {
        payload.service_ids = form.role === 'administrator' ? [] : form.service_ids;
      }
      await api.put('/manager/users/' + editingId.value, payload);
    } else {
      if (!form.username || !form.email || !form.password) {
        formError.value = 'Username, email e password sono obbligatori.';
        saving.value = false;
        return;
      }
      const payload = {
        username: form.username,
        display_name: form.display_name || form.username,
        email: form.email,
        password: form.password,
        role: isAdmin ? form.role : 'olo_manager',
      };
      if (perm.assign_services) {
        payload.service_ids = form.role === 'administrator' ? [] : form.service_ids;
      }
      await api.post('/manager/users', payload);
    }
    showModal.value = false;
    await loadUsers();
  } catch (e) {
    formError.value = e.message || 'Errore nel salvataggio.';
  } finally {
    saving.value = false;
  }
}

async function uploadPhoto(e) {
  const file = e.target.files[0];
  if (!file) return;
  e.target.value = '';

  if (!file.type.startsWith('image/')) {
    formError.value = 'Solo immagini sono supportate.';
    return;
  }
  if (file.size > 5 * 1024 * 1024) {
    formError.value = 'Immagine troppo grande (max 5 MB).';
    return;
  }

  const wpMediaUrl = cfg.restUrl.replace(/\/olo-booking\/v2$/, '') + '/wp/v2/media';
  const formData = new FormData();
  formData.append('file', file);
  formData.append('title', 'profile-photo');

  try {
    const res = await fetch(wpMediaUrl, {
      method: 'POST',
      headers: { 'X-WP-Nonce': cfg.nonce },
      body: formData,
    });
    if (!res.ok) {
      const err = await res.json().catch(() => ({}));
      formError.value = err.message || 'Errore upload foto.';
      return;
    }
    const media = await res.json();
    form.photo_id = media.id;
    form.photo_url = media.media_details?.sizes?.thumbnail?.source_url || media.source_url;
  } catch (err) {
    formError.value = 'Errore durante il caricamento: ' + (err.message || '');
  }
}

async function doDelete() {
  saving.value = true;
  try {
    await api.del('/manager/users/' + deleteTarget.value.id);
    showDeleteModal.value = false;
    deleteTarget.value = null;
    await loadUsers();
  } catch (e) {
    formError.value = e.message || 'Errore nella cancellazione.';
  } finally {
    saving.value = false;
  }
}
</script>
