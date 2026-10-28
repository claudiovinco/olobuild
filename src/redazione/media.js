/**
 * La libreria media di WordPress per la Redazione: scegliere foto e allegati, e inquadrare le foto per i social col
 * ritaglio di WordPress (ajax crop-image: il ritaglio diventa una foto nuova della libreria, l'originale resta intatto).
 * Stessa logica di assets/js/olo-tipi-contenuto.js, che fa lo stesso nella scheda di WordPress.
 */
import { t } from '@/i18n';

/** Un file scelto nella libreria, nella forma che usa la Redazione (come Olobuild_Redazione::media()). */
export function daAllegato(att) {
  const s = att.sizes || {};
  return {
    id: att.id,
    titolo: att.title || att.filename || '',
    url: att.url || '',
    mini: att.type === 'image' ? ((s.medium || s.thumbnail || s.full || {}).url || att.url || '') : '',
    est: String(att.filename || '').split('.').pop().toUpperCase(),
    peso: att.filesizeHumanReadable || '',
  };
}

/** Apre la libreria: tipo 'immagine' | 'galleria' | 'allegati'. Chiama fatto() con i file scelti. */
export function scegli(tipo, fatto) {
  const wp = window.wp;
  if (!wp || !wp.media) return;
  const multi = tipo !== 'immagine';
  const frame = wp.media({
    title: tipo === 'allegati' ? t('Scegli gli allegati') : (multi ? t('Scegli le foto') : t('Scegli la foto')),
    button: { text: t('Usa') },
    multiple: multi ? 'add' : false,
    library: tipo === 'allegati' ? {} : { type: 'image' },
  });
  frame.on('select', () => {
    fatto(frame.state().get('selection').map((m) => daAllegato(m.toJSON())));
  });
  frame.open();
}

/**
 * Inquadra la foto `sorgente` (id) nel formato { w, h, nome }: la libreria si apre con la foto già scelta, poi il
 * ritaglio a proporzione fissa con il centro della foto come proposta. fatto({ id, url }) col ritaglio salvato.
 */
export function inquadra(sorgente, formato, fatto) {
  const wp = window.wp;
  const _ = window._;
  if (!wp || !wp.media || !wp.media.controller || !wp.media.controller.Cropper) return;
  const Ritaglio = wp.media.controller.Cropper.extend({
    doCrop(attachment) {
      const d = _.extend({}, attachment.get('cropDetails'));
      const scala = Math.min(1, formato.w / Math.max(1, d.width));
      d.dst_width = Math.round(d.width * scala);
      d.dst_height = Math.round(d.height * scala);
      return wp.ajax.post('crop-image', {
        nonce: attachment.get('nonces').edit,
        id: attachment.get('id'),
        context: 'olo-foto-social',
        cropDetails: d,
      });
    },
  });
  const frame = wp.media({
    button: { text: t('Inquadra'), close: false },
    states: [
      new wp.media.controller.Library({
        title: formato.nome,
        library: wp.media.query({ type: 'image' }),
        multiple: false,
        date: false,
        priority: 20,
        suggestedWidth: formato.w,
        suggestedHeight: formato.h,
      }),
      new Ritaglio({
        canSkipCrop: false,
        imgSelectOptions(att) {
          const w = att.get('width');
          const h = att.get('height');
          let sw = w;
          let sh = Math.round((w * formato.h) / formato.w);
          if (sh > h) {
            sh = h;
            sw = Math.round((h * formato.w) / formato.h);
          }
          const x1 = Math.round((w - sw) / 2);
          const y1 = Math.round((h - sh) / 2);
          return {
            handles: true, keys: true, instance: true, persistent: true,
            imageWidth: w, imageHeight: h, aspectRatio: formato.w + ':' + formato.h,
            x1, y1, x2: x1 + sw, y2: y1 + sh,
          };
        },
      }),
    ],
  });
  frame.on('open', () => {
    const att = wp.media.attachment(sorgente);
    att.fetch();
    frame.state('library').get('selection').reset([att]);
  });
  frame.on('select', () => frame.setState('cropper'));
  frame.on('cropped', (img) => fatto({ id: img.id, url: img.url }));
  frame.on('content:error:crop', () => window.alert(t('Ritaglio non riuscito: riprova.')));
  frame.open();
}
