<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Manutenzione & Coming Soon') }}</h1>
      <p>{{ t('Mostra una pagina di manutenzione (HTTP 503) o di lancio "Coming Soon" ai visitatori. Gli amministratori (e i ruoli scelti) vedono il sito normalmente.') }}</p>
    </div>
    <div class="head-actions">
      <span class="cfg-pill" :class="form.mode === 'off' ? 'off' : 'warn'">
        <span class="dot"></span>
        {{ modeLabel }}
      </span>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L2 19l3 3 7.3-7.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4 2.6-2.6z"/></svg></div>
      <div>
        <h3>{{ t('Modalità') }}</h3>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Stato sito') }}</label></div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': form.mode === 'off' }"          @click="set('mode', 'off')">{{ t('Online') }}</button>
            <button :class="{ 'is-on': form.mode === 'coming_soon' }"  @click="set('mode', 'coming_soon')">{{ t('Coming Soon') }}</button>
            <button :class="{ 'is-on': form.mode === 'maintenance' }"  @click="set('mode', 'maintenance')">{{ t('Manutenzione') }}</button>
          </div>
        </div>
      </div>

      <div class="cfg-row" v-show="form.mode === 'coming_soon'">
        <div class="label-col"><label>{{ t('Template Coming Soon') }}</label><div class="hint">{{ t('Quale template Olobuild mostrare come "Coming Soon".') }}</div></div>
        <div class="control-col template-select-row">
          <CfgSelect :model-value="form.coming_soon_template_id" :options="templateOptions" @update:model-value="set('coming_soon_template_id', parseInt($event) || 0)" />
          <button class="cfg-btn cfg-btn-secondary" :disabled="generating === 'coming_soon'" @click="generateTemplate('coming_soon')" :title="t('Crea un template Coming Soon precaricato (headline + countdown +30gg + CTA) e selezionalo')">
            <svg v-if="generating !== 'coming_soon'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3z"/></svg>
            <svg v-else class="spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7l3 2.7"/></svg>
            {{ generating === 'coming_soon' ? t('Creazione…') : t('Genera template') }}
          </button>
        </div>
      </div>

      <div class="cfg-row" v-show="form.mode === 'maintenance'">
        <div class="label-col"><label>{{ t('Template Manutenzione') }}</label><div class="hint">{{ t('Quale template Olobuild mostrare durante la manutenzione (HTTP 503).') }}</div></div>
        <div class="control-col template-select-row">
          <CfgSelect :model-value="form.template_id" :options="templateOptions" @update:model-value="set('template_id', parseInt($event) || 0)" />
          <button class="cfg-btn cfg-btn-secondary" :disabled="generating === 'maintenance'" @click="generateTemplate('maintenance')" :title="t('Crea un template Manutenzione precaricato e selezionalo')">
            <svg v-if="generating !== 'maintenance'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3z"/></svg>
            <svg v-else class="spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7l3 2.7"/></svg>
            {{ generating === 'maintenance' ? t('Creazione…') : t('Genera template') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <div v-show="form.mode !== 'off'" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg></div>
      <div>
        <h3>{{ t('Chi può accedere comunque') }}</h3>
        <p>{{ t('Anche con il sito offline, alcuni ruoli e un URL segreto possono bypassare la pagina di manutenzione.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Ruoli che bypassano') }}</label><div class="hint">{{ t('Possono navigare il sito normalmente.') }}</div></div>
        <div class="control-col">
          <div class="cfg-roles-checkboxes">
            <label v-for="role in availableRoles" :key="role.slug" class="cfg-role-check">
              <input type="checkbox" :value="role.slug" :checked="form.bypass_roles.includes(role.slug)" @change="toggleRole(role.slug, $event.target.checked)" />
              <span>{{ role.label }}</span>
            </label>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('URL segreta di bypass') }}</label><div class="hint">{{ t('Aggiungi ?bypass=SECRET all\'URL del sito per accedere. Lascia vuoto per disabilitare.') }}</div></div>
        <div class="control-col"><div class="cfg-input mono cfg-w-md"><input type="text" :value="form.bypass_secret" @input="set('bypass_secret', $event.target.value)" placeholder="cambia-questo-secret" /></div></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgSelect from './controls/CfgSelect.vue';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const form = ref({
  mode: 'off',
  template_id: 0,
  coming_soon_template_id: 0,
  bypass_roles: ['administrator'],
  bypass_secret: '',
});

const templates = ref([]);
const generating = ref(null); // 'coming_soon' | 'maintenance' | null
const availableRoles = ref([
  { slug: 'administrator', label: 'Administrator' },
  { slug: 'editor',        label: 'Editor' },
  { slug: 'author',        label: 'Author' },
  { slug: 'contributor',   label: 'Contributor' },
  { slug: 'subscriber',    label: 'Subscriber' },
]);

const templateOptions = computed(() => [
  { value: 0, label: t('— Seleziona template —') },
  ...templates.value.map(tpl => ({ value: tpl.id, label: tpl.title })),
]);

const modeLabel = computed(() => {
  if (form.value.mode === 'off') return t('Sito online');
  if (form.value.mode === 'coming_soon') return t('Coming Soon attivo');
  if (form.value.mode === 'maintenance') return t('Manutenzione attiva');
  return '';
});

function set(k, v) { form.value[k] = v; setDirty(true); }
function toggleRole(slug, on) {
  const set_ = new Set(form.value.bypass_roles);
  if (on) set_.add(slug); else set_.delete(slug);
  form.value.bypass_roles = Array.from(set_);
  setDirty(true);
}

async function generateTemplate(kind) {
  if (generating.value) return;
  generating.value = kind;
  try {
    const res = await fetch(`${window.oloData.restUrl}maintenance/generate-template`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ kind }),
    });
    const data = await res.json();
    if (res.ok && data?.template_id) {
      await loadTemplates();
      if (kind === 'coming_soon') form.value.coming_soon_template_id = data.template_id;
      else                         form.value.template_id            = data.template_id;
      showToast(t('Template creato e selezionato'), 'success');
      // Apri il template appena creato nell'editor in una nuova tab.
      if (data.edit_url && confirm(t('Aprire il template appena creato nel builder per personalizzarlo?'))) {
        window.open(data.edit_url, '_blank', 'noopener');
      }
    } else {
      showToast(t('Errore creazione template'), 'error');
    }
  } catch (e) {
    showToast(t('Errore di rete'), 'error');
  } finally {
    generating.value = null;
  }
}

async function loadTemplates() {
  try {
    const res = await fetch(`${window.oloData.restUrl}templates?per_page=200`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      templates.value = (data?.templates || data || []).map(t => ({ id: t.id || t.ID, title: t.title || t.post_title || '(no title)' }));
    }
  } catch (e) { /* keep empty */ }
}

async function loadSettings() {
  try {
    const res = await fetch(`${window.oloData.restUrl}maintenance`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) Object.assign(form.value, await res.json());
  } catch (e) { /* defaults */ }
}

async function saveSettings() {
  try {
    await fetch(`${window.oloData.restUrl}maintenance`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(form.value),
    });
  } catch (e) {
    showToast(t('Errore di salvataggio manutenzione'), 'error');
  }
}

const onSave = () => saveSettings();
const onDiscard = () => loadSettings();

onMounted(() => {
  loadSettings();
  loadTemplates();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>

<style scoped>
.cfg-roles-checkboxes {
  display: flex; flex-wrap: wrap; gap: 8px;
}
.cfg-role-check {
  display: flex; align-items: center; gap: 6px;
  padding: 6px 12px;
  background: var(--c-bg);
  border: 1px solid var(--c-line);
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
}
.cfg-role-check input { accent-color: var(--c-red); }
.template-select-row {
  display: flex;
  gap: 8px;
  align-items: stretch;
}
.template-select-row .cfg-select { flex: 1; }
.spin { animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.cfg-role-check:has(input:checked) {
  background: var(--c-red-soft);
  border-color: var(--c-red-soft-2);
  color: var(--c-red-dark);
}
</style>
