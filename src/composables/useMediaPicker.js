/**
 * Composable for WordPress Media Library integration.
 * Requires wp_enqueue_media() in PHP.
 */
import { useToast } from './useToast.js';

export function useMediaPicker() {
  const toast = useToast();

  function checkWpMedia() {
    if (!window.wp || !window.wp.media) {
      toast.error(t('Libreria Media di WordPress non disponibile.'));
      return false;
    }
    return true;
  }

  function openSingleImage(callback) {
    if (!checkWpMedia()) return;

    const frame = wp.media({
      title: 'Seleziona immagine',
      button: { text: 'Usa questa immagine' },
      multiple: false,
      library: { type: 'image' },
    });

    frame.on('select', () => {
      const attachment = frame.state().get('selection').first().toJSON();
      callback({
        url: attachment.url,
        alt: attachment.alt || '',
        id: attachment.id,
      });
    });

    frame.open();
  }

  function openGallery(callback) {
    if (!checkWpMedia()) return;

    const frame = wp.media({
      title: 'Seleziona immagini',
      button: { text: 'Aggiungi alla galleria' },
      multiple: true,
      library: { type: 'image' },
    });

    frame.on('select', () => {
      const images = frame.state().get('selection').map((att) => {
        const json = att.toJSON();
        return {
          url: json.url,
          alt: json.alt || '',
          id: json.id,
        };
      });
      callback(images);
    });

    frame.open();
  }

  function openVideo(callback) {
    if (!checkWpMedia()) return;

    const frame = wp.media({
      title: 'Seleziona video',
      button: { text: 'Usa questo video' },
      multiple: false,
      library: { type: 'video' },
    });

    frame.on('select', () => {
      const att = frame.state().get('selection').first().toJSON();
      callback({
        url: att.url,
        alt: att.alt || '',
        id: att.id,
        type: 'video',
        poster: att.image?.src || '',
      });
    });

    frame.open();
  }

  function openPosterImage(callback) {
    if (!checkWpMedia()) return;

    const frame = wp.media({
      title: 'Seleziona poster',
      button: { text: 'Usa come poster' },
      multiple: false,
      library: { type: 'image' },
    });

    frame.on('select', () => {
      const att = frame.state().get('selection').first().toJSON();
      callback({
        url: att.url,
        id: att.id,
      });
    });

    frame.open();
  }

  return { openSingleImage, openGallery, openVideo, openPosterImage };
}
