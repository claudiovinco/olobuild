/** Le chiamate della Redazione alle rotte olobuild/v1/redazione (Olobuild_Redazione). */
import { t } from '@/i18n';

export class ErroreRedazione extends Error {
  constructor(messaggio, codice, stato) {
    super(messaggio);
    this.codice = codice || '';
    this.stato = stato || 0;
  }
}

export async function chiama(percorso, { metodo = 'GET', corpo = null, parametri = null } = {}) {
  const d = window.oloData || {};
  let url = (d.restUrl || '/wp-json/olobuild/v1/') + percorso;
  if (parametri) {
    const q = new URLSearchParams();
    Object.entries(parametri).forEach(([k, v]) => {
      if (v !== '' && v !== null && v !== undefined) q.set(k, v);
    });
    const s = q.toString();
    // Coi permalink semplici restUrl ha già ?rest_route=…
    if (s) url += (url.includes('?') ? '&' : '?') + s;
  }
  let res;
  try {
    res = await fetch(url, {
      method: metodo,
      credentials: 'same-origin',
      headers: { 'X-WP-Nonce': d.nonce || '', ...(corpo ? { 'Content-Type': 'application/json' } : {}) },
      body: corpo ? JSON.stringify(corpo) : undefined,
    });
  } catch (e) {
    throw new ErroreRedazione(t('Il sito non risponde: controlla la connessione e riprova.'), 'rete', 0);
  }
  let dati = null;
  try {
    dati = await res.json();
  } catch (e) {
    dati = null;
  }
  if (!res.ok) {
    throw new ErroreRedazione((dati && dati.message) || t('Qualcosa non è andato: riprova.'), dati && dati.code, res.status);
  }
  return dati;
}

// Le voci di ogni tassonomia (categorie, tag) si caricano una volta per pagina: le usano l'elenco e le schede.
const voci = {};

export function vociDi(tassonomia) {
  if (!voci[tassonomia]) {
    voci[tassonomia] = chiama('redazione/termini', { parametri: { tassonomia } }).catch(() => {
      delete voci[tassonomia];
      return [];
    });
  }
  return voci[tassonomia];
}

export function aggiungiVoce(tassonomia, voce) {
  voci[tassonomia] = vociDi(tassonomia).then((l) => (l.some((x) => x.id === voce.id) ? l : [...l, voce]));
  return voci[tassonomia];
}
