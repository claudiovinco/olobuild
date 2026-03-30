import { ref, onUnmounted } from 'vue';

export function useAutosave(builderStore, interval = 60000) {
  const lastSaved = ref(null);
  const autosaveEnabled = ref(true);
  let timer = null;

  function startAutosave() {
    if (timer) clearInterval(timer);
    timer = setInterval(() => {
      if (autosaveEnabled.value && builderStore.isDirty && !builderStore.isSaving) {
        builderStore.saveTemplate();
        lastSaved.value = new Date();
      }
    }, interval);
  }

  function stopAutosave() {
    if (timer) {
      clearInterval(timer);
      timer = null;
    }
  }

  function toggleAutosave() {
    autosaveEnabled.value = !autosaveEnabled.value;
    if (autosaveEnabled.value) startAutosave();
    else stopAutosave();
  }

  startAutosave();

  onUnmounted(() => stopAutosave());

  return { lastSaved, autosaveEnabled, toggleAutosave, startAutosave, stopAutosave };
}
