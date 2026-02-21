<template>
  <div class="olom-permissions">
    <div class="olom-page-header">
      <h2 class="olom-page-title">Permessi ruoli</h2>
    </div>
    <p class="olom-hint">Configura quali azioni possono compiere i Supervisori e i Gestori. L'Amministratore ha sempre tutti i permessi.</p>

    <div v-if="loading" class="olom-loading">Caricamento...</div>

    <div v-else class="olom-perm-table-wrap">
      <table class="olom-perm-table">
        <thead>
          <tr>
            <th class="olom-pt-label">Permesso</th>
            <th class="olom-pt-role olom-pt-admin">Amministratore</th>
            <th class="olom-pt-role">Supervisore</th>
            <th class="olom-pt-role">Gestore</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="(group, gname) in grouped" :key="gname">
            <tr class="olom-pt-group-row">
              <td colspan="4">{{ gname }}</td>
            </tr>
            <tr v-for="p in group" :key="p.key" class="olom-pt-row">
              <td class="olom-pt-label">{{ p.label }}</td>
              <td class="olom-pt-check olom-pt-admin">
                <input type="checkbox" checked disabled />
              </td>
              <td class="olom-pt-check">
                <input type="checkbox" v-model="perms.supervisor[p.key]" />
              </td>
              <td class="olom-pt-check">
                <input type="checkbox" v-model="perms.manager[p.key]" />
              </td>
            </tr>
          </template>
        </tbody>
      </table>

      <div class="olom-perm-actions">
        <button class="olom-btn olom-btn-success" @click="save" :disabled="saving">
          {{ saving ? 'Salvataggio...' : 'Salva permessi' }}
        </button>
        <span v-if="savedMsg" class="olom-saved-msg">{{ savedMsg }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { api } from '../stores/api.js';

const loading = ref(true);
const saving = ref(false);
const savedMsg = ref('');
const permList = ref([]);

const perms = reactive({
  supervisor: {},
  manager: {},
});

const grouped = computed(() => {
  const groups = {};
  for (const p of permList.value) {
    if (!groups[p.group]) groups[p.group] = [];
    groups[p.group].push(p);
  }
  return groups;
});

onMounted(async () => {
  try {
    const data = await api.get('/manager/permissions');
    permList.value = data;
    for (const p of data) {
      perms.supervisor[p.key] = p.supervisor;
      perms.manager[p.key] = p.manager;
    }
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
});

async function save() {
  saving.value = true;
  savedMsg.value = '';
  try {
    await api.put('/manager/permissions', {
      supervisor: { ...perms.supervisor },
      manager: { ...perms.manager },
    });
    savedMsg.value = 'Salvato!';
    setTimeout(() => { savedMsg.value = ''; }, 3000);
  } catch (e) {
    savedMsg.value = 'Errore: ' + (e.message || 'salvataggio fallito');
  } finally {
    saving.value = false;
  }
}
</script>
