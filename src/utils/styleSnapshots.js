/**
 * Versioni dello stile del sito (istantanee, olobuild_design_preset_snapshots).
 *
 * Il server ne prende una prima di ogni import di tema, salvataggio degli stili,
 * ripristino dei predefiniti e import del sito (Olobuild_Style_System). Qui:
 * - il ripristino (POST design-presets/snapshots), usato dal builder e dalla
 *   Configurazione;
 * - il ponte fra l'import di un tema, che ricarica la pagina, e il toast
 *   «Ripristina», che deve comparire DOPO la ricarica: mostrato prima, la
 *   ricarica lo cancellava. Passa per sessionStorage (solo questa scheda del
 *   browser, e solo per qualche minuto); se è bloccato resta l'elenco in
 *   Configurazione › Palette › Versioni dello stile.
 */
import { t } from '@/i18n';
import { okOrThrow } from '@/components/Admin/cfgSave';
import { useToast } from '@/composables/useToast.js';

const CHIAVE = 'olo_stile_sostituito';
const VALIDITA_MS = 10 * 60 * 1000;

/** Rimette lo stile dell'istantanea `id`. Lancia se non riesce (messaggio con lo stato HTTP). */
export async function ripristinaIstantanea(id) {
  const o = (typeof window !== 'undefined' && window.oloData) || {};
  const res = await okOrThrow(fetch(`${o.restUrl}design-presets/snapshots`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': o.nonce },
    credentials: 'same-origin',
    body: JSON.stringify({ action: 'restore', id }),
  }));
  return res.json();
}

// Cosa il ripristino ha lasciato com'era perché nel frattempo è stato eliminato.
const NOMI_SALTATI = {
  olobuild_active_header: 'header',
  olobuild_active_footer: 'footer',
  olobuild_active_404: 'pagina 404',
  page_on_front: 'pagina iniziale',
  page_for_posts: 'pagina degli articoli',
};
// Le pagine arrivano come 'pagina:<id>' (eliminata) e 'template_pagina:<id>' (il suo
// template di allora è stato eliminato), col titolo di allora in esito.titoli[id];
// i font caricati i cui file sono stati cancellati come 'font:<id>', col nome in esito.font[id].
const NOMI_PAGINA = { pagina: 'pagina', template_pagina: 'template della pagina' };
export function descriviSaltati(esito) {
  const s = (esito && Array.isArray(esito.saltati)) ? esito.saltati : [];
  const titoli = (esito && esito.titoli && typeof esito.titoli === 'object') ? esito.titoli : {};
  const font = (esito && esito.font && typeof esito.font === 'object') ? esito.font : {};
  return s.map((k) => {
    const f = /^font:(.+)$/.exec(String(k));
    if (f) return t('font') + ' «' + (Object.prototype.hasOwnProperty.call(font, f[1]) && font[f[1]] ? String(font[f[1]]) : f[1]) + '»';
    const m = /^(pagina|template_pagina):(\d+)$/.exec(String(k));
    if (!m) return t(NOMI_SALTATI[k] || k);
    const titolo = titoli[m[2]] ? String(titoli[m[2]]) : '#' + m[2];
    return t(NOMI_PAGINA[m[1]]) + ' «' + titolo + '»';
  }).join(', ');
}

/** L'import di un tema, prima di ricaricare: ricorda l'istantanea da offrire dopo. */
export function ricordaStileSostituito(snapshot, tema) {
  if (!snapshot || !snapshot.id) return;
  try {
    sessionStorage.setItem(CHIAVE, JSON.stringify({
      id: String(snapshot.id),
      tema: String(tema || ''),
      puo: !!snapshot.can_restore,
      ora: Date.now(),
    }));
  } catch (e) { /* memoria di sessione bloccata: resta l'elenco in Configurazione */ }
}

function leggiETogli() {
  try {
    const raw = sessionStorage.getItem(CHIAVE);
    if (!raw) return null;
    sessionStorage.removeItem(CHIAVE);
    const d = JSON.parse(raw);
    if (!d || !d.id || typeof d.ora !== 'number' || Date.now() - d.ora > VALIDITA_MS) return null;
    return d;
  } catch (e) {
    return null;
  }
}

/** All'avvio del builder: se un import di tema ha appena sostituito lo stile, lo dice e offre «Ripristina». */
export function annunciaStileSostituito() {
  const d = leggiETogli();
  if (!d) return;
  const toast = useToast();
  const titolo = t('Stile del sito sostituito dal tema') + (d.tema ? ' «' + d.tema + '»' : '');
  if (!d.puo) {
    toast.info(titolo + '. ' + t('Un amministratore può ripristinarlo da Configurazione › Palette › Versioni dello stile.'), 10000);
    return;
  }
  const ripristina = async () => {
    try {
      const esito = await ripristinaIstantanea(d.id);
      const saltati = descriviSaltati(esito);
      if (saltati) {
        toast.warning(t('Stile ripristinato, tranne ciò che nel frattempo è stato eliminato (resta com\'è ora)') + ': ' + saltati, 6000);
        setTimeout(() => window.location.reload(), 5000);
      } else {
        toast.success(t('Stile ripristinato'), 3000);
        setTimeout(() => window.location.reload(), 800);
      }
    } catch (e) {
      toast.action(t('Ripristino non riuscito') + (e && e.message ? ' — ' + e.message : ''), t('Riprova'), ripristina, 10000, 'error');
    }
  };
  toast.action(titolo, t('Ripristina'), ripristina, 20000);
}
