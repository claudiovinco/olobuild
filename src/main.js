import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import './assets/styles/main.scss';

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
