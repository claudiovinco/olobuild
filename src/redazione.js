// La Redazione (Olobuild_Redazione): monta l'app nella pagina «Redazione» del menu di WordPress.
import { createApp } from 'vue';
import RedazioneApp from './redazione/RedazioneApp.vue';
import './redazione/redazione.css';

const el = document.getElementById('olo-redazione');
if (el) {
  createApp(RedazioneApp).mount(el);
}
