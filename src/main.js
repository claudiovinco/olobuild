import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import './assets/styles/main.scss';
import { guardStaleBundle } from './utils/staleBundleGuard';

// Bundle vecchio rimasto in una scheda aperta = funzioni nuove che "spariscono".
// Se il codice in esecuzione non è quello del server, si ricarica prima di montare.
if (guardStaleBundle()) {
  // Ricarica in corso: non montiamo nulla, la pagina sta per essere sostituita.
} else {

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);

// Make WordPress data available to stores
app.provide('oloData', window.oloData || {
  restUrl: '/wp-json/olobuild/v1',
  nonce: '',
  userId: 0,
  userName: 'Guest',
  version: '1.0.0',
});

app.mount('#olobuilder-app');

}
