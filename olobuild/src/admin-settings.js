import { createApp } from 'vue';
import { createPinia } from 'pinia';
import SettingsApp from './components/Admin/SettingsApp.vue';

const el = document.getElementById('olo-admin-settings');
if (el) {
  const app = createApp(SettingsApp);
  app.use(createPinia());
  app.mount(el);
}
