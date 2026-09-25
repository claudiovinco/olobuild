import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import './assets/styles/main.scss';
import { guardStaleBundle } from './utils/staleBundleGuard';
import { ritornoPer } from './utils/ritornoZona';

// Header o footer aperto dal chip «Apri il template»: la voce del «Torna a «…»» si
// controlla qui, prima di ogni ricarica (bundle vecchio qui sotto, nuovo tentativo di
// App.vue se il template non si carica), perché dopo una ricarica il referrer è la
// pagina stessa. La barra riceve lo stesso esito (utils/ritornoZona.js). Solo nel
// builder di un template: l'elenco la voce non la tocca.
const idAperto = parseInt(window.oloData && window.oloData.templateId, 10) || 0;
if (idAperto > 0) ritornoPer(idAperto);

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
