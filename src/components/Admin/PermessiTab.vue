<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Permessi') }} <em>{{ t('& Ruoli') }}</em></h1>
      <p>{{ t('Chi può fare cosa nel builder. Si appoggia ai ruoli WordPress, ma li estende con permessi granulari specifici di OLObuild.') }}</p>
    </div>
    <div class="head-actions">
      <button class="cfg-btn cfg-btn-secondary" @click="createCustomRole">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        {{ t('Crea ruolo custom') }}
      </button>
    </div>
  </div>

  <!-- ─── Matrice permessi ─── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L2 19l3 3 7.3-7.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4 2.6-2.6z"/></svg>
      </div>
      <div>
        <h3>{{ t('Matrice permessi') }}</h3>
        <p>{{ t('Cliccare una cella per cambiare un permesso. I ruoli custom si possono creare e modificare.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body" style="padding: 0; overflow: auto;">
      <table class="perm-table">
        <thead>
          <tr>
            <th class="perm-col">{{ t('Permesso') }}</th>
            <th v-for="r in roles" :key="r.id">
              <div class="role-name">
                {{ r.label }}
                <span v-if="r.custom" class="cfg-pill new role-pill">CUSTOM</span>
              </div>
              <div class="role-count">{{ r.count }} {{ t('utenti') }}</div>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, ri) in matrix" :key="row.perm">
            <td class="perm-name">{{ t(row.perm) }}</td>
            <td v-for="r in roles" :key="r.id" class="perm-cell" @click="toggleCell(ri, r.id)">
              <span :class="row[r.id] ? 'check-on' : 'check-off'">
                <svg v-if="row[r.id]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ─── Opzioni avanzate ─── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="14" r="4"/><path d="m11 12 9-9 3 3-3 3-2-2-2 2-2-2-3 3"/></svg>
      </div>
      <div>
        <h3>{{ t('Opzioni avanzate') }}</h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Lock dei template Header/Footer') }}</label>
          <div class="hint">{{ t('Solo Admin può modificarli. Sicurezza per agenzie che consegnano siti ai clienti.') }}</div>
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': advanced.lock_header_footer }" @click="setAdv('lock_header_footer', !advanced.lock_header_footer)" role="switch"></button>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Lock degli Stili globali') }}</label>
          <div class="hint">{{ t('Una volta consegnato il sito, il cliente non può rovinare la palette/tipografia.') }}</div>
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': advanced.lock_styles }" @click="setAdv('lock_styles', !advanced.lock_styles)" role="switch"></button>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Sandbox per Contributors') }}</label>
          <div class="hint">{{ t('I contributor lavorano su una copia draft, niente live edit.') }}</div>
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': advanced.sandbox_contributors }" @click="setAdv('sandbox_contributors', !advanced.sandbox_contributors)" role="switch"></button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const roles = ref([
  { id: 'admin',   label: 'Admin',       count: 1, custom: false },
  { id: 'editor',  label: 'Editor',      count: 0, custom: false },
  { id: 'author',  label: 'Author',      count: 0, custom: false },
  { id: 'contrib', label: 'Contributor', count: 0, custom: false },
  { id: 'client',  label: 'Cliente',     count: 0, custom: true },
]);

const matrix = ref([
  { perm: 'Aprire l\'editor',                 admin: true, editor: true, author: true,  contrib: false, client: true  },
  { perm: 'Pubblicare pagine',                admin: true, editor: true, author: true,  contrib: false, client: false },
  { perm: 'Modificare Stili globali',         admin: true, editor: true, author: false, contrib: false, client: false },
  { perm: 'Modificare Header / Footer',       admin: true, editor: true, author: false, contrib: false, client: false },
  { perm: 'Modificare Configurazione',        admin: true, editor: false, author: false, contrib: false, client: false },
  { perm: 'Sfogliare la libreria template',   admin: true, editor: true, author: true,  contrib: true,  client: true  },
  { perm: 'Salvare template personalizzati',  admin: true, editor: true, author: false, contrib: false, client: false },
  { perm: 'Importare / esportare',            admin: true, editor: false, author: false, contrib: false, client: false },
  { perm: 'Vedere Analytics',                 admin: true, editor: true, author: false, contrib: false, client: true  },
]);

const advanced = ref({
  lock_header_footer: true,
  lock_styles: true,
  sandbox_contributors: false,
});

function toggleCell(rowIdx, roleId) {
  matrix.value[rowIdx][roleId] = !matrix.value[rowIdx][roleId];
  setDirty(true);
}
function setAdv(k, v) { advanced.value[k] = v; setDirty(true); }

function createCustomRole() {
  const name = prompt(t('Nome del ruolo custom (es. "SEO specialist"):'));
  if (!name) return;
  const id = name.toLowerCase().replace(/[^a-z0-9]+/g, '_').slice(0, 20);
  if (roles.value.find(r => r.id === id)) {
    showToast(t('Esiste già un ruolo con questo nome'), 'error');
    return;
  }
  roles.value.push({ id, label: name, count: 0, custom: true });
  matrix.value.forEach(row => { row[id] = false; });
  setDirty(true);
}

async function loadSettings() {
  try {
    const res = await fetch(`${window.oloData.restUrl}role-manager`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data?.roles)) roles.value = data.roles;
      if (Array.isArray(data?.matrix)) matrix.value = data.matrix;
      if (data?.advanced) Object.assign(advanced.value, data.advanced);
    }
  } catch (e) { /* defaults */ }
}

async function saveSettings() {
  try {
    await fetch(`${window.oloData.restUrl}role-manager`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ roles: roles.value, matrix: matrix.value, advanced: advanced.value }),
    });
  } catch (e) {
    showToast(t('Errore di salvataggio permessi'), 'error');
  }
}

const onSave = () => saveSettings();
const onDiscard = () => loadSettings();

onMounted(() => {
  loadSettings();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>

<style scoped>
.perm-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}
.perm-table thead tr { background: var(--c-bg); }
.perm-table th {
  padding: 12px 14px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: var(--c-text-faint);
  text-align: center;
  min-width: 100px;
}
.perm-table th.perm-col {
  text-align: left;
  padding: 12px 22px;
  min-width: 280px;
}
.role-name {
  display: flex;
  align-items: center;
  gap: 6px;
  justify-content: center;
  font-size: 12px;
  color: var(--c-navy);
  text-transform: none;
  letter-spacing: 0;
  font-weight: 600;
}
.role-pill {
  font-size: 8px;
  padding: 1px 5px;
}
.role-count {
  font-weight: 500;
  color: var(--c-text-faint);
  font-size: 10px;
  text-transform: none;
  letter-spacing: 0;
  margin-top: 2px;
}
.perm-table tbody tr { border-top: 1px solid var(--c-line-soft); }
.perm-table tbody tr:hover { background: var(--c-bg); }
.perm-name {
  padding: 10px 22px;
  font-weight: 500;
  color: var(--c-navy);
}
.perm-cell {
  text-align: center;
  padding: 10px 14px;
  cursor: pointer;
}
.check-on, .check-off {
  width: 22px; height: 22px;
  margin: 0 auto;
  border-radius: 5px;
  display: grid; place-items: center;
}
.check-on {
  background: var(--c-red-soft);
  color: var(--c-red);
}
.check-off {
  background: var(--c-bg);
  color: var(--c-text-faint);
  border: 1px solid var(--c-line);
}
.check-on svg, .check-off svg { width: 12px; height: 12px; }
.perm-cell:hover .check-off { background: #fff; border-color: var(--c-red-soft-2); color: var(--c-red); }
</style>
